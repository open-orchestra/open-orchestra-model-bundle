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
        return "";
    }

    public function up(Database $db)
    {
        // this up() migration is auto-generated, please modify it to your needs

    }

    public function down(Database $db)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
    public function postUp(Database $db)
    {
        $result = $this->executeScript($db, 'duplicate_node.js');
    }
}
