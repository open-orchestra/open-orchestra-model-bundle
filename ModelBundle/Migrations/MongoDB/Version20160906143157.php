<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160906143157 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Add blocked edition on status";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $db->execute('
            db.status.find().forEach(function(item) {
                item.blockedEdition = false;
                if (item.published) {
                    item.blockedEdition = true;
                }
                db.status.update({ _id: item._id }, item);
            });
            db.status.insert({
              "name": "outOfWorkflow",
              "labels": {
                "en": "Out of validation workflow",
                "fr": "Non soumis au workflow de validation"
              },
              "published": true,
              "blockedEdition": false,
              "initial": false,
              "displayColor": "grayDark"
            });
        ');
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->execute('
            db.status.remove({"name": "outOfWorkflow"});
            db.status.update({}, {$unset: {"blockedEdition":1}} , {"multi": true});;
        ');
    }
}
