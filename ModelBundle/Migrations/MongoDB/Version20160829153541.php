<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160829153541 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $configuration;

    /**
     * Load the configuration file
     */
    protected function loadConfiguration()
    {
        $yamlParser = new YamlParser();
        $configurationFilePath =
            $this->container->getParameter('kernel.root_dir') . '/config/private_block_migration.yml';

        if (is_file($configurationFilePath)) {
            $this->configuration =
                $yamlParser->parse(file_get_contents($configurationFilePath));
        } else {
            $migrationDir = $this->container->getParameter('mongo_db_migrations.dir_name');
            $this->configuration = $yamlParser->parse(file_get_contents($migrationDir . '/config/private_block_migration.yml'));
        }
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return "Add parameter blockPrivate in block of areas";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $this->loadConfiguration();
        $db->execute(
            '
            var privateBlock = '.json_encode($this->configuration['private_block']).';
            var updateBlockArea = function(area, node) {
                if (typeof area.blocks != \'undefined\') {
                    var blocks = area.blocks;
                    for (var i in blocks) {
                        var block = blocks[i];
                        if (block.nodeId != 0 || block.nodeId != "0") {
                            node = db.node.findOne({ nodeId: block.nodeId, siteId:node.siteId, language:node.language });
                        }
                        if (typeof node != \'undefined\') {
                            var defBlock = node.blocks["" + block.blockId];
                            if (privateBlock.indexOf(defBlock.component) > -1) {
                                block.blockPrivate = true
                            } else {
                                block.blockPrivate = false
                            }
                        }
                    }
                }
                if (typeof area.subAreas != \'undefined\') {
                    var areas = area.subAreas;
                    for (var i in areas) {
                        var subAreas = areas[i];
                        updateBlockArea(subAreas, node);
                    }
                }
            }

            db.node.find().forEach(function(item) {
                 var rootArea = item.rootArea;
                 if (typeof rootArea != \'undefined\') {
                     updateBlockArea(rootArea, item);
                 }

                 db.node.update({ _id: item._id }, item);
            });
        ');
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->execute(
            '
            var deleteBlockPrivateInArea = function(area, node) {
                if (typeof area.blocks != \'undefined\') {
                    var blocks = area.blocks;
                    for (var i in blocks) {
                        var block = blocks[i];
                        delete block.blockPrivate;
                    }
                }
                if (typeof area.subAreas != \'undefined\') {
                    var areas = area.subAreas;
                    for (var i in areas) {
                        var subAreas = areas[i];
                        deleteBlockPrivateInArea(subAreas, node);
                    }
                }
            }

            db.node.find().forEach(function(item) {
                 var rootArea = item.rootArea;
                 if (typeof rootArea != \'undefined\') {
                     deleteBlockPrivateInArea(rootArea, item);
                 }

                 db.node.update({ _id: item._id }, item);
            });
        ');
    }
}
