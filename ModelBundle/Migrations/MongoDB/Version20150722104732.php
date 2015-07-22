<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150722104732 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "create stored procedure to increase node version";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $this->executeScript($db, 'create_duplicate_node_20150722104732.js');

    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $this->executeScript($db, 'delete_duplicate_node_20150722104732.js');

    }
}
