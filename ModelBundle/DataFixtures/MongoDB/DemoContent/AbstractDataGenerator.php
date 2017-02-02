<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Area;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\Model\BlockInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

/**
 * Class AbstractDataGenerator
 */
abstract class AbstractDataGenerator
{
    protected $fixture;
    protected $container;
    protected $manager;
    protected $version;
    protected $status;
    protected $blockParameters;

    /**
     * Constructor
     *
     * @param AbstractFixture    $fixture
     * @param ContainerInterface $container
     * @param ObjectManager      $manager
     * @param int                $version
     * @param string             $status
     */
    public function __construct(AbstractFixture $fixture, ContainerInterface $container, ObjectManager $manager, $version = 1, $status = 'status-published')
    {
        $this->fixture = $fixture;
        $this->container = $container;
        $this->manager = $manager;
        $this->version = $version;
        $this->status = $status;
        $this->blockParameters = array(
            'tiny_mce_wysiwyg' => array()
        );
    }

    /**
     * @param string $language
     *
     * @return Node
     */
    public function generateNode($language)
    {
        if ($language == "fr") {
            return $this->generateNodeFr();
        } else if ($language == "en") {
            return $this->generateNodeEn();
        } else {
            return $this->generateNodeDe();
        }
    }

    /**
     * @param BlockInterface $block
     *
     * @return BlockInterface
     */
    protected function generateBlock(BlockInterface $block)
    {
        $block->setSiteId('2');
        $block->setPrivate($this->container->get('open_orchestra_display.display_block_manager')->isPublic($block));
        $block->setParameter($this->blockParameters[$block->getComponent()]);

        $this->manager->persist($block);
        $this->manager->flush();

        return $block;
    }

    /**
     * @return Node
     */
    abstract protected function generateNodeFr();

    /**
     * @return Node
     */
    abstract protected function generateNodeEn();

    /**
     * @return Node
     */
    abstract protected function generateNodeDe();

    /**
     * @param string $language
     *
     * @return Area
     */
    protected function createHeader($language)
    {
        $header = new Area();

        $header->addBlock($this->fixture->getReference('Wysiwyg logo'.'-'.$language));
        $header->addBlock($this->fixture->getReference('Menu'.'-'.$language));

        return $header;
    }

    /**
     * @param string $language
     *
     * @return Area
     */
    protected function createFooter($language)
    {
        $footer = new Area();

        $footer->addBlock($this->fixture->getReference('footer menu'.'-'.$language));
        $footer->addBlock($this->fixture->getReference('Wysiwyg footer'.'-'.$language));

        return $footer;
    }

    /**
     * @return Node
     */
    protected function createBaseNode()
    {
        $node = new Node();
        $node->setMaxAge(1000);
        $node->setNodeType(NodeInterface::TYPE_DEFAULT);
        $node->setSiteId('2');
        $node->setPath('-');
        $node->setVersion($this->version);
        $node->setStatus($this->fixture->getReference($this->status));
        $node->setDeleted(false);
        $node->setTemplate('default');
        $node->setTheme('themePresentation');
        $node->setDefaultSiteTheme(true);

        return $node;
    }

    /**
     * @param NodeInterface $node
     *
     * @return string
     */
    protected function getVersionName(NodeInterface $node)
    {
        $date = new \DateTime("now");
        return $node->getName().'_'. $node->getVersion(). '_'. $date->format("Y-m-d_H:i:s");
    }
}
