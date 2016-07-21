<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Class Version20160720095529
 */
class Version20160720095529 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Update template with new structure";
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
            db.template.find().forEach(function(item) {
                 var rootArea = {}
                 rootArea.label = \'Root\';
                 rootArea.areaType = \'root\';
                 rootArea.areaId = \'root\';
                 rootArea.subAreas = [];

                 var areas = item.areas;
                 for (var i in areas) {
                    var subAreas = areas[i];
                    rowArea = updateArea(subAreas);
                    area.subAreas.push(rowArea);
                 }

                 item.rootArea = area;
                 delete item.areas;
                 db.template.update({ _id: item._id }, item);

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

            db.template.find().forEach(function(item) {
                 var rowAreas = item.rootArea.subAreas;
                 areas = []
                 areas = cleanRowAreas(rowAreas);
                 item.areas = areas;
                 delete item.rootArea;
                 db.template.update({ _id: item._id }, item);
            });
        ');
    }
}
