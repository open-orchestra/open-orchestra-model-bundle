<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Class Version20160422151315
 */
class Version20160422151315 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "rename role role_access_move_node by role_access_move_tree";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $db->execute('
            db.users_group.find({roles: \'ROLE_ACCESS_MOVE_NODE\'}).forEach(function(item) {
                db.users_group.update({ _id: item._id}, { $pull: { roles : \'ROLE_ACCESS_MOVE_NODE\' }});
                db.users_group.update({ _id: item._id}, { $push: { roles : \'ROLE_ACCESS_MOVE_TREE\' }});
            });
            db.users_group.update({}, { $pull: { modelRoles : { role : \'ROLE_ACCESS_MOVE_NODE\'} }}, { multi: true });
        ');
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->execute('
            db.users_group.find({roles: \'ROLE_ACCESS_MOVE_TREE\'}).forEach(function(item) {
                db.users_group.update({ _id: item._id}, { $pull: { roles : \'ROLE_ACCESS_MOVE_TREE\' }});
                db.users_group.update({ _id: item._id}, { $push: { roles : \'ROLE_ACCESS_MOVE_NODE\' }});
            });
            db.users_group.find({\'modelRoles\':{$exists:1}}).forEach(function(item) {
                    var currentNodeId = [];
                    var length = item.modelRoles.length;
                    for (i = 0; i != length; ++i) {
                       if (currentNodeId.indexOf(item.modelRoles[i].id) == -1 && item.modelRoles[i].type == \'node\') {
                          currentNodeId.push(item.modelRoles[i].id);
                          var nodeRole = {};
                          nodeRole[\'id\'] = item.modelRoles[i].id;
                          nodeRole[\'accessType\'] = \'denied\';
                          nodeRole[\'granted\'] = 0;
                          nodeRole[\'type\'] = \'node\';
                          nodeRole[\'role\'] = \'ROLE_ACCESS_MOVE_NODE\';
                          item.modelRoles.push(nodeRole);
                       }
                    }
                    db.users_group.update({_id: item._id}, item);
                }
            );
        ');
    }
}
