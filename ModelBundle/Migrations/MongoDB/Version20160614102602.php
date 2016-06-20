<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Version20160614102602
 */
class Version20160614102602 extends AbstractMigration implements ContainerAwareInterface
{
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return "Update site meta keywords and meta description";
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $languages = $this->container->getParameter('open_orchestra_backoffice.orchestra_choice.front_language');
        $languages = array_keys($languages);

        $updateRequest = '
            db.site.find().forEach(function(item) {
                var itemHasChanged = false;
                if ("string" == typeof item.metaKeywords) {
                    var metaKeywords = {};
        ';

        foreach ($languages as $language) {
            $updateRequest .= '
                    metaKeywords.' . $language . ' = item.metaKeywords;
            ';
        }

        $updateRequest .= '
                    item.metaKeywords = metaKeywords;
                    itemHasChanged = true;
                }

                if ("string" == typeof item.metaDescription) {
                    var metaDescriptions = {};
        ';

        foreach ($languages as $language) {
            $updateRequest .= '
                    metaDescriptions.' . $language . ' = item.metaDescription;
                ';
        }

        $updateRequest .= '
                    item.metaDescriptions = metaDescriptions;
                    delete item.metaDescription;
                    itemHasChanged = true;
                }

                if (itemHasChanged) {
                    db.site.update({_id: item._id}, item);
                }
            });
        ';

        $db->execute($updateRequest);
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $languages = $this->container->getParameter('open_orchestra_backoffice.orchestra_choice.front_language');
        $languages = array_keys($languages);
        $language = $languages[0];

        $revertRequest = '
            db.site.find().forEach(function(item) {
                var itemHasChanged = false;
                if ("object" == typeof item.metaKeywords) {
                    item.metaKeywords = item.metaKeywords.' . $language . ';
                    itemHasChanged = true;
                }
                if ("object" == typeof item.metaDescriptions) {
                    item.metaDescription = item.metaDescriptions.' . $language . ';
                    delete item.metaDescriptions;
                    itemHasChanged = true;
                }
                if (itemHasChanged) {
                    db.site.update({_id: item._id}, item);
                }
            });
        ';

        $db->execute($revertRequest);
    }
}
