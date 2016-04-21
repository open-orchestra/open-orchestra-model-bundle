<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160421105828 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Add attribute boLabel in node";
    }

    public function up(Database $db)
    {
        $db->execute('
            db.node.find({\'boLabel\':{$exists:0}}).forEach(function(item) {
                 var nodeBoLabel = db.node.findOne({\'boLabel\':{$exists:1}, \'nodeId\': item.nodeId, \'siteId\': item.siteId});
                 var boLabel = \'\';
                 if (nodeBoLabel) {
                    boLabel = nodeBoLabel.boLabel;
                 } else {
                    boLabel = item.name;
                 }
                 print (tojson(boLabel));
                 item.boLabel = boLabel;
                db.node.update({_id: item._id}, item);
            });
        ');
    }

    public function down(Database $db)
    {
        $db->execute('db.node.update({}, {$unset : {\'boLabel\':\'\'}}, false, true);');
    }
}
