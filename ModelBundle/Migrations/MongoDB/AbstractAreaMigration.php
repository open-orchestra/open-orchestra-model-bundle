<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;

/**
 * Class AbstractAreaMigration
 */
abstract class AbstractAreaMigration extends AbstractMigration
{
    /**
     * @return string
     */
    protected function getUpdateAreaFunction()
    {
        return '
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
        ';
    }

    /**
     * @return string
     */
    protected function getCleanRowAreasFunction()
    {
        return '
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
        ';
    }
}
