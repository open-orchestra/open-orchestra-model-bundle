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
        $description = "Add currentlyPublished flag to nodes: ".PHP_EOL;
        $description .= " - In Collection of nodes, the currentlyPublished flag is added to those with published status and bigger version rank".PHP_EOL;

        return $description;
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {db.node.find({'status.published':true}).snapshot().forEach(function(item){

    });
        $db->execute('var currentNodeId = \'\';
                      db.node.find({\'status.published\':1}).sort({\'version\':-1}).snapshot().forEach(function(item){
                        if (item.nodeId != currentNodeId) {
                            item.currentlyPublished = true;
                            currentNodeId = item.nodeId;
                        }
                        db.node.update({_id: item._id}, item);
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
