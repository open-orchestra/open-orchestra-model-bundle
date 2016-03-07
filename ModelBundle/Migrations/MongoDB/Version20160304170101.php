<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Class Version20160304170101
 */
class Version20160304170101 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Update users group collection";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $db->execute('db.users_group.find({\'nodeRoles\':{$exists:1}}).snapshot().forEach(function(item){
                        for(i = 0; i != item.nodeRoles.length; ++i) {
                            item.nodeRoles[i].type = \'node\'
                            item.nodeRoles[i].id = item.nodeRoles[i].nodeId;
                            delete item.nodeRoles[i].nodeId;
                        }
                        db.users_group.update({_id: item._id}, item);
                     });'
        );
        $db->execute('db.users_group.update({}, {$rename : {\'nodeRoles\':\'modelRoles\'}});');

    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->execute('db.users_group.find({\'modelRoles\':{$exists:1}}).snapshot().forEach(function(item){
                        for(i = 0; i != item.modelRoles.length; ++i) {
                            if(item.modelRoles[i].type == \'node\') {
                                delete item.modelRoles[i].type;
                                item.modelRoles[i].nodeId = item.modelRoles[i].id;
                                delete item.modelRoles[i].id;
                            }
                        }
                        db.users_group.update({_id: item._id}, item);
                     });'
        );
        $db->execute('db.users_group.update({}, {$rename : {\'modelRoles\':\'nodeRoles\'}});');
    }
}
