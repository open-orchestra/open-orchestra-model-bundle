<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Class Version20160720134051
 */
class Version20160720134051 extends AbstractMigration
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
        $db->execute('
            var updateArea = function(area) {
                area.areaType = \'column\';
                area.width = 1;
                var subAreas = []
                for (var i in area.subAreas) {
                   var subArea = area.subAreas[i];
                   subAreas.push(updateArea(subArea));
                }
                area.subAreas = subAreas;

                var rowArea = {}
                rowArea.areaType = \'row\';
                rowArea.areaId = \'row_\'+area.areaId;
                rowArea.subAreas = [];
                rowArea.subAreas.push(area);

                return rowArea;
            }
            db.node.find().forEach(function(item) {
                 var area = {}
                 area.label = \'Root\';
                 area.areaType = \'root\';
                 area.areaId = \'root\';
                 area.subAreas = [];

                 var areas = item.areas;
                 for (var i in areas) {
                    var subAreas = areas[i];
                    rowArea = updateArea(subAreas);
                    area.subAreas.push(rowArea);
                 }

                 item.area = area;
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
        $db->execute('
            var cleanRowAreas = function(rowAreas) {
                var subareas = []
                for (var i in rowAreas) {
                    var rowArea = rowAreas[i];
                    var columnArea = rowArea.subAreas[0];
                    delete columnArea.areaType;
                    delete columnArea.width;
                    columnArea.subAreas = cleanRowAreas(columnArea.subAreas);
                    subareas.push(columnArea)
                }

                return subareas;
            }

            db.node.find().forEach(function(item) {
                 var rowAreas = item.area.subAreas;
                 areas = []
                 areas = cleanRowAreas(rowAreas);
                 item.areas = areas;
                 delete item.area;
                 db.node.update({ _id: item._id }, item);
            });
        ');
    }
}
