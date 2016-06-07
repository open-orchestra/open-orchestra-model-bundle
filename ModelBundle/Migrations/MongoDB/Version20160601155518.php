<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\MongoDB\Database;
use Symfony\Component\Yaml\Parser as YamlParser;
use OpenOrchestra\ModelInterface\Repository\RepositoryTrait\KeywordableTraitInterface;

class Version20160601155518 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;
    protected $collections;
    protected $configuration;

    /**
     * Set the container
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return "change stored keyword to dbref";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $databaseName = $db->getName();
        $this->getKeywordableCollection();
        $this->loadConfiguration();

        foreach ($this->collections as $collection) {
            $db->execute('
                db.' . $collection . '.find().forEach(function(item) {
                    if (typeof item.keywords != \'undefined\') {
                        keywords = item.keywords;
                        item.keywords = [];
                        for (var i in keywords) {
                            keyword = {};
                            keyword.$ref = "keyword";
                            keyword.$id = keywords[i]._id
                            keyword.$db = "' . $databaseName . '"
                            item.keywords.push(keyword);
                        }
                        db.' . $collection . '.update({ _id: item._id }, item);
                    }
                });
            ');
        }

        $this->parseNode('up');
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $this->getKeywordableCollection();
        $this->loadConfiguration();

        foreach ($this->collections as $collection) {
            $db->execute('
                db.' . $collection . '.find().forEach(function(item) {
                    if (typeof item.keywords != \'undefined\') {
                        keywords = item.keywords;
                        item.keywords = [];
                        for (var i in keywords) {
                            keyword = {};
                            keyword._id = keywords[i].$id
                            db.keyword.find({"_id" : keywords[i].$id}).forEach(function(record) {
                                for (property in record) {
                                    keyword[property] = record[property];
                                }
                            });
                            item.keywords.push(keyword);
                        }
                        db.' . $collection . '.update({ _id: item._id }, item);
                    }
                });
            ');
        }

        $this->parseNode('down');
    }

    /**
     * Load keywordable collection
     */
    protected function getKeywordableCollection() {
        $this->collections = array();
        $metadataFactory = $this->container->get('object_manager')->getMetadataFactory();
        $allMetadata = $metadataFactory->getAllMetadata();

        foreach ($allMetadata as $metadata) {
            try {
                $document = $metadata->newInstance();
                if (array_key_exists('OpenOrchestra\MongoTrait\Keywordable', class_uses($document))) {
                    $this->collections[] = $metadata->getCollection();
                }
            } catch (\Exception $e) {}
        }
    }

    /**
     * Load the configuration file
     */
    protected function loadConfiguration()
    {
        $yamlParser = new YamlParser();

        $configurationFilePath =
            $this->container->getParameter('kernel.root_dir') . '/config/keyword_migration.yml';

        if (is_file($configurationFilePath)) {
            $this->configuration = $yamlParser->parse(file_get_contents($configurationFilePath));
        } else {
            $migrationDir = $this->container->getParameter('mongo_db_migrations.dir_name');
            $this->configuration = $yamlParser->parse(file_get_contents($migrationDir . '/config/keyword_migration.yml'));
        }
        if (!array_key_exists('condition_csv', $this->configuration)) {
            $this->configuration['condition_csv'] = array();
        }
        if (!array_key_exists('condition_boolean', $this->configuration)) {
            $this->configuration['condition_boolean'] = array();
        }
    }

    protected function parseNode($direction) {
        $step = 10;
        $documentManager = $this->container->get('object_manager');
        $countNodes = $documentManager->createQueryBuilder('OpenOrchestra\ModelBundle\Document\Node')
            ->getQuery()
            ->execute()
            ->count();
        for ($i = 0; $i < intval($countNodes / $step) + 1; $i++) {
            $nodes = $documentManager->createQueryBuilder('OpenOrchestra\ModelBundle\Document\Node')
                ->limit($step)
                ->skip($i * $step)
                ->sort('_id', 'asc')
                ->getQuery()
                ->execute();
            foreach ($nodes as $node) {
                $blocks = $node->getBlocks();
                $isChanged = false;
                foreach ($blocks as $key => $block) {
                    $component = $block->getComponent();
                    foreach (array_keys($this->configuration) as $type) {
                        if(array_key_exists($component, $this->configuration[$type])) {
                            foreach ($this->configuration[$type][$component] as $attributePath) {
                                $attributePath = explode('.', $attributePath);
                                $attribute = $attributePath[0];
                                array_shift($attributePath);
                                if (null !== $block->getAttribute($attribute)) {
                                    $block->addAttribute($attribute, $this->transform($block->getAttribute($attribute), $direction, $type, $attributePath));
                                    $node->setBlock($key, $block);
                                    $isChanged = true;
                                }
                            }
                        }
                    }
                }
                if ($isChanged) {
                    $documentManager->flush($node);
                }
            }
        }
    }

    /**
     * Transform csv condition
     *
     * @param mixed  $attribute
     * @param string $type
     * @param array  $path
     *
     * return mixed
     */
    protected function transform($attribute, $direction, $type, array $path)
    {
        if (count($path) > 0) {
            $nextPath = array_shift($path);
            if (array_key_exists($nextPath, $attribute)) {
                $attribute[$nextPath] = $this->transform($attribute[$nextPath], $direction, $type, $path);
            }
        } else {
            if("down" == $direction) {
                if ('condition_csv' == $type) {
                    $attribute = $this->transformCsv($attribute);
                } else if ('condition_boolean' == $type) {
                    $attribute = $this->transformBoolean($attribute);
                }
            } else if ('up' == $direction) {
                if ('condition_csv' == $type) {
                    $attribute = $this->reverseTransformCsv($attribute);
                } else if ('condition_boolean' == $type) {
                    $attribute = $this->reverseTransformBoolean($attribute);
                }
            }
        }
        return $attribute;
    }

    /**
     * Transform csv condition
     *
     * @param string $condition
     */
    protected function transformCsv($condition)
    {
        $keywordWithoutOperator = preg_replace(explode('|', KeywordableTraitInterface::OPERATOR_SPLIT), ' ', $condition);
        $keywordArray = explode(' ', $keywordWithoutOperator);
        foreach ($keywordArray as &$keyword) {
            if ($keyword != '') {
                $keywordDocument = $this->container->get('open_orchestra_model.repository.keyword')->find($keyword);
                if (!is_null($keywordDocument)) {
                    $keyword = $keywordDocument->getLabel();
                } else {
                    unset($keyword);
                }
            }
        }

        return implode(',', $keywordArray);
    }

    /**
     * Transform boolean condition
     *
     * @param string $condition
     */
    protected function transformBoolean($condition)
    {
        $transformer = $this->container->get('open_orchestra_backoffice.transformer.condition_to_reference_keyword');
        $condition = $transformer->transform($condition);

        $transformer = $this->container->get('open_orchestra.transformer.boolean_to_bdd');
        $condition = $transformer->reverseTransform($condition);

        return $condition;
    }

    /**
     * Reverse Transform csv condition
     *
     * @param string $condition
     */
    protected function reverseTransformCsv($condition)
    {
        $keywordArray = explode(',', $condition);
        $condition = implode(' OR ', $keywordArray);

        $transformer = $this->container->get('open_orchestra_backoffice.transformer.condition_to_reference_keyword');
        $condition = $transformer->reverseTransform($condition);

        return $condition;
    }

    /**
     * Reverse Transform boolean condition
     *
     * @param string $condition
     */
    protected function reverseTransformBoolean($condition)
    {
        $transformer = $this->container->get('open_orchestra.transformer.boolean_to_bdd');
        $condition = $transformer->transform($condition);

        $transformer = $this->container->get('open_orchestra_backoffice.transformer.condition_to_reference_keyword');
        $condition = $transformer->reverseTransform($condition);

        return $condition;
    }
}
