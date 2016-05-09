<?php

namespace OpenOrchestra\ModelBundle\Migrations\MongoDB;

use AntiMattr\MongoDB\Migrations\AbstractMigration;
use Doctrine\MongoDB\Database;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\SiteEvents;
use OpenOrchestra\ModelInterface\Event\SiteEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160317140327 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $description = "Update site alias indexation in site collection: ".PHP_EOL;
        $description .= " - In Collection of sites, the previous numeric indexation of site aliases is replaced by random string indexation".PHP_EOL;

        return $description;
    }

    /**
     * @param Database $db
     */
    public function up(Database $db)
    {
        $sitesUpdated = $db->execute('
            var sitesUpdated = [];
            db.site.find().forEach(function(item) {
                keyCharacters = "Zabcdefghijklmnopqrstuvwxyz0123456789";
                aliases = item.aliases;
                item.aliases = {};
                for (i in aliases) {
                    if (i.indexOf("'.SiteInterface::PREFIX_SITE_ALIAS.'") == -1) {
                        key = "";
                        for (j = 0; j < 13; j++) {
                            key += keyCharacters.charAt(Math.floor(Math.random() * keyCharacters.length));
                        }
                        item.aliases["'.SiteInterface::PREFIX_SITE_ALIAS.'" + key] = aliases[i];
                        if(sitesUpdated.indexOf(item._id) === -1) {
                            sitesUpdated.push(item._id);
                        }
                    } else {
                        item.aliases[i] = aliases[i];
                    }
                }
                db.site.update({_id: item._id}, item);
            });
            return sitesUpdated;
        ');
        if (is_array($sitesUpdated) && array_key_exists('retval', $sitesUpdated) && is_array($sitesUpdated['retval'])) {
            foreach ($sitesUpdated['retval'] as $site) {
                $site = $this->container->get('open_orchestra_model.repository.site')->find($site->{'$id'});
                $this->container->get('event_dispatcher')->dispatch(SiteEvents::SITE_UPDATE, new SiteEvent($site));
            }
        }
    }

    /**
     * @param Database $db
     */
    public function down(Database $db)
    {
        $db->execute('
            db.site.find().forEach(function(item) {
                aliases = item.aliases;
                item.aliases = [];
                for (i in aliases) {
                    item.aliases.push(aliases[i]);
                }
                db.site.update({_id: item._id}, item);
            });
        ');
    }
}
