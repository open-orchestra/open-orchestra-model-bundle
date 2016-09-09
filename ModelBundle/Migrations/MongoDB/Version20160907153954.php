<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160907153954 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Add out of workflow on status";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $db->execute('
            db.status.find().forEach(function(item) {
                item.outOfWorkflow = false;
                if (item.name == "outOfWorkflow") {
                    item.outOfWorkflow = true;
                }
                db.status.update({ _id: item._id }, item);
            });
            db.content_type.find().forEach(function(item) {
                item.definingNonStatusable = false;
                db.content_type.update({ _id: item._id }, item);
            });
        ');
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->execute('
            db.status.update({}, {$unset: {"outOfWorkflow":1}} , {"multi": true});;
            db.content_type.update({}, {$unset: {"definingNonStatusable":1}} , {"multi": true});;
        ');
    }
}
