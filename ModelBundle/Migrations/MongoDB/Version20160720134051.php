<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use Doctrine\MongoDB\Database;

/**
 * Class Version20160720134051
 */
class Version20160720134051 extends AbstractAreaMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Update node with new structure";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $db->execute(
            $this->getUpdateAreaFunction().'

            db.node.find().forEach(function(item) {
                 var rootArea = {}
                 rootArea.label = \'Root\';
                 rootArea.areaType = \'root\';
                 rootArea.areaId = \'root\';
                 rootArea.subAreas = [];

                 var areas = item.areas;
                 for (var i in areas) {
                    var subAreas = areas[i];
                    rowArea = updateArea(subAreas);
                    rootArea.subAreas.push(rowArea);
                 }

                 item.rootArea = rootArea;
                 delete item.areas;

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
            $this->getCleanRowAreasFunction() .'

            db.node.find().forEach(function(item) {
                 if (typeof item.rootArea != \'undefined\') {
                     var rowAreas = item.rootArea.subAreas;
                     areas = []
                     areas = cleanRowAreas(rowAreas);
                     item.areas = areas;
                     delete item.rootArea;
                     db.node.update({ _id: item._id }, item);
                 }
            });
        ');
    }
}
