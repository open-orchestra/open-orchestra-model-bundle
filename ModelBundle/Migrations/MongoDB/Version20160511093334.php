<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Class Version20160511093334
 */
class Version20160511093334 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "update attributes of block configurable content";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $db->execute('
            db.node.find().forEach(function(item) {
                for (key in item.blocks) {
                    block = item.blocks[key];
                    contentSearch = {}
                    if (block.component == "configurable_content" &&
                        typeof block.attributes["contentTypeId"] !== "undefined" &&
                        typeof block.attributes["contentId"] !== "undefined" &&
                        typeof block.attributes["contentSearch"] == "undefined"
                    ) {
                        contentSearch.contentType = block.attributes["contentTypeId"];
                        contentSearch.choiceType = "";
                        contentSearch.keywords = "";
                        contentSearch.contentId = block.attributes["contentId"];
                        block.attributes["contentSearch"] = contentSearch;
                        delete block.attributes["contentTypeId"];
                        delete block.attributes["contentId"];
                        db.node.update({ _id: item._id }, item);
                    }
                }
            });
        ');
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->execute('
            db.node.find().forEach(function(item) {
                for (key in item.blocks) {
                    var block = item.blocks[key];
                    if (block.component == "configurable_content" &&
                        typeof block.attributes["contentTypeId"] == "undefined" &&
                        typeof block.attributes["contentId"] == "undefined" &&
                        typeof block.attributes["contentSearch"] !== "undefined"
                    ) {
                        block.attributes["contentTypeId"] = block.attributes["contentSearch"].contentType;
                        block.attributes["contentId"] = block.attributes["contentSearch"].contentId;
                        delete block.attributes["contentSearch"];
                        db.node.update({ _id: item._id }, item);
                    }
                }
            });
        ');
    }
}
