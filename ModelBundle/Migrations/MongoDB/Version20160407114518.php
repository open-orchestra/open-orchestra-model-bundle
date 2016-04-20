<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160407114518 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        $description = "Add currentlyPublished flag to nodes and contents: ".PHP_EOL;
        $description .= " - In Collection of nodes and contents, the currentlyPublished flag is added to those with published status and bigger version rank".PHP_EOL;

        return $description;
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $db->execute('
            var currentNodeId = \'\';
            var currentContentId = \'\';
            var currentLanguage = \'\';
            db.node.find({\'status.published\':true}).sort({\'language\':1, \'version\':-1}).forEach(function(item) {
                if (item.nodeId != currentNodeId || item.language != currentLanguage) {
                    item.currentlyPublished = true;
                    currentNodeId = item.nodeId;
                    currentLanguage = item.language;
                }
                db.node.update({_id: item._id}, item);
            });
            db.content.find({\'status.published\':true}).sort({\'language\':1, \'version\':-1}).forEach(function(item) {
                if (item.contentId != currentCotentId || item.language != currentLanguage) {
                    item.currentlyPublished = true;
                    currentContentId = item.contentId;
                    currentLanguage = item.language;
                }
                db.content.update({_id: item._id}, item);
            });
        ');
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->execute('
            db.node.find().forEach(function(item) {
                delete item.currentlyPublished;
                db.node.update({_id: item._id}, item);
            });
            db.content.find().forEach(function(item) {
                delete item.currentlyPublished;
                db.content.update({_id: item._id}, item);
            });
        ');
    }
}
