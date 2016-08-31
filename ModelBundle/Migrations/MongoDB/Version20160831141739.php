<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Auto-generated Migration Please modify to your needs!
 */
class Version20160831141739 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Update storage translation";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $this->upStorageTranslatedValue($db, 'content_type', 'names');
        $this->upStorageTranslatedValue($db, 'users_group', 'labels');
        $this->upStorageTranslatedValue($db, 'status', 'labels');
        $this->upStorageTranslatedValue($db, 'node', 'status.labels');
        $this->upStorageTranslatedValue($db, 'content', 'status.labels');
        $this->upStorageTranslatedValue($db, 'role', 'descriptions');

        $db->execute('
            db.content_type.find({"fields":{$exists:1}}).forEach(function(item) {
                for (var i in item.fields) {
                    var field = item.fields[i];
                    var property = field.labels
                    var newProperty = {};
                    for (var i in property) {
                       var element = property[i];
                       var language = element.language;
                       var value = element.value;
                       newProperty[language] = value;
                    }
                    field.labels = newProperty;
                    item.fields[i] = field;
                    printjson(item.fields[i]);
                }

                db.content_type.update({_id: item._id}, item);
            });
        ');
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $this->downStorageTranslatedValue($db, 'content_type', 'names');
        $this->downStorageTranslatedValue($db, 'users_group', 'labels');
        $this->downStorageTranslatedValue($db, 'status', 'labels');
        $this->downStorageTranslatedValue($db, 'node', 'status.labels');
        $this->downStorageTranslatedValue($db, 'content', 'status.labels');
        $this->downStorageTranslatedValue($db, 'role', 'descriptions');
        $db->execute('
            db.content_type.find({"fields":{$exists:1}}).forEach(function(item) {
                for (var i in item.fields) {
                    var field = item.fields[i];
                    var property = field.labels
                    var newProperty = {};
                    for (var language in property) {
                       var value = property[language];

                       var element = {};
                       element.language = language;
                       element.value = value;
                       newProperty[language] = element;
                    }
                    field.labels = newProperty;
                    item.fields[i] = field;
                    printjson(item.fields[i]);
                }

                db.content_type.update({_id: item._id}, item);
            });
        ');

    }

    /**
     * @param Database $db
     * @param string $collection
     * @param string $property
     */
    protected function upStorageTranslatedValue(Database $db, $collection, $property)
    {
        $db->execute('
            db.'.$collection.'.find({"'.$property.'":{$exists:1}}).forEach(function(item) {
                 var property = item.'.$property.';
                 var newProperty = {};
                 for (var i in property) {
                    var element = property[i];
                    var language = element.language;
                    var value = element.value;
                    newProperty[language] = value;
                 }
                 item.'.$property.' = newProperty;

                 db.'.$collection.'.update({_id: item._id}, item);
            });
        ');
    }

    /**
     * @param Database $db
     * @param string $collection
     * @param string $property
     */
    protected function downStorageTranslatedValue(Database $db, $collection, $property)
    {
        $db->execute('
            db.'.$collection.'.find({"'.$property.'":{$exists:1}}).forEach(function(item) {
                 var property = item.'.$property.';
                 var newProperty = {};
                 for (var language in property) {
                    var value = property[language];

                    var element = {};
                    element.language = language;
                    element.value = value;
                    newProperty[language] = element;
                 }
                 item.'.$property.' = newProperty;

                 db.'.$collection.'.update({_id: item._id}, item);
            });
        ');
    }
}
