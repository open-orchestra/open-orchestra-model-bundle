<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150807083326 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "create stored procedure to select in mongoDB through sub-attributs";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $this->executeScript($db, 'create_select_enumeration_20150807083326.js');
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $this->executeScript($db, 'delete_select_enumeration_20150807083326.js');
    }
}
