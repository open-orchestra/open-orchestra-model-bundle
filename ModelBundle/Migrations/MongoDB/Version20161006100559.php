<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161006100559 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Add site id on trash items";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $db->execute('
            db.trash_item.find().forEach(function(item) {
                var reference = db.getCollection(item.type).findOne(item.entity.$id);
                item.siteId = reference.siteId
                db.trash_item.update({ _id: item._id }, item);
            });
        ');
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->execute('
            db.trash_item.update({}, {$unset: {"siteId":1}} , {"multi": true});;
        ');
    }
}
