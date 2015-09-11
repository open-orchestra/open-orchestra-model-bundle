<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class ContactDataGenerator
 */
class ContactDataGenerator extends AbstractDataGenerator
{
    /**
     * @return Node
     */
    protected function generateNodeEn()
    {
        $htmlContent = <<<EOF
<div class="contact-information">
    <h3>Contact Us</h3>
    <div class="info-interakting" >
        <h4>Interakting</h4>
        <p>
            Groupe Business & Decision
            <br>153 Rue de Courcelles
            <br>75017 PARIS FRANCE
            <br><span class="fontOrange">Tel:</span> +33 1 56 21 21 21
            <br><span class="fontOrange">Fax:</span> +33 1 56 21 21 22
        </p>
    </div>
    <div class="access-interakting">
        <h4>Access:</h4>
        <p>
            <span class="fontOrange">Underground service 3</span> Stop Pereire
            <br><span class="fontOrange">RER service C</span> Stop Pereire-Levallois
        </p>
    </div>
    <div class="google-maps-interakting"">
        <iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
        src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=fr&amp;geocode=&amp;q=153+Rue+de+Courcelles+75817+Paris&amp;aq=&amp;sll=48.834414,2.499298&amp;sspn=0.523838,0.909805&amp;ie=UTF8&amp;hq=&amp;hnear=153+Rue+de+Courcelles,+75817+Paris&amp;ll=48.883747,2.298345&amp;spn=0.004088,0.007108&amp;t=m&amp;z=14&amp;output=embed&amp;hl=en"></iframe>
    </div>
</div>
EOF;
        $name = "Contact";
        $language = "en";
        $routePattern = 'page-contact';

        return $this->generateNodeGlobal($htmlContent, $name, $language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeFr()
    {
        $htmlContent = <<<EOF
<div class="contact-information">
    <h3>Contactez-nous</h3>
    <div class="info-interakting" >
        <h4>Interakting</h4>
        <p>
            Groupe Business & Decision
            <br>153 Rue de Courcelles
            <br>75017 PARIS FRANCE
            <br><span class="fontOrange">Tél:</span> +33 1 56 21 21 21
            <br><span class="fontOrange">Fax:</span> +33 1 56 21 21 22
        </p>
    </div>
    <div class="access-interakting">
        <h4>Accès:</h4>
        <p>
            <span class="fontOrange">Metro ligne 3</span> arrêt Pereire
            <br><span class="fontOrange">RER ligne C</span> arrêt Pereire-Levallois
        </p>
    </div>
    <div class="google-maps-interakting"">
        <iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
        src="https://maps.google.fr/maps?f=q&amp;source=s_q&amp;hl=fr&amp;geocode=&amp;q=153+Rue+de+Courcelles+75817+Paris&amp;aq=&amp;sll=48.834414,2.499298&amp;sspn=0.523838,0.909805&amp;ie=UTF8&amp;hq=&amp;hnear=153+Rue+de+Courcelles,+75817+Paris&amp;ll=48.883747,2.298345&amp;spn=0.004088,0.007108&amp;t=m&amp;z=14&amp;output=embed"></iframe>
    </div>
</div>
EOF;
        $name = "Contact";
        $language = "fr";
        $routePattern = "page-contact";

        return $this->generateNodeGlobal($htmlContent, $name, $language, $routePattern);
    }

    /**
     * @param string $htmlContent
     * @param string $name
     * @param string $language
     * @param string $routePattern
     *
     * @return Node
     */
    protected function generateNodeGlobal($htmlContent, $name, $language, $routePattern)
    {
        $siteContactBlock0 = new Block();
        $siteContactBlock0->setLabel('Wysiwyg 1');
        $siteContactBlock0->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteContactBlock0->setAttributes(array("htmlContent" => $htmlContent));
        $siteContactBlock0->addArea(array('nodeId' => 0, 'areaId' => 'moduleArea'));

        $siteContactArea0 = $this->createHeader();
        $siteContactArea4 = $this->createArea('Main content area 1', 'mainContentArea1', 'main-content-contact');
        $siteContactArea4->addBlock(array('nodeId' => NodeInterface::TRANSVERSE_NODE_ID, 'blockId' => 4));
        $siteContactArea5 = $this->createModuleArea(false, "module-area-contact");
        $siteContactArea5->addBlock(array('nodeId' => 0, 'blockId' => 1));
        $siteContactArea3 = $this->createMain(array($siteContactArea4, $siteContactArea5));
        $siteContactArea6 = $this->createFooter();

        $siteContact = $this->createBaseNode();
        $siteContact->setNodeId('fixture_page_contact');
        $siteContact->setName($name);
        $siteContact->setLanguage($language);
        $siteContact->setParentId(NodeInterface::ROOT_NODE_ID);
        $siteContact->setOrder(9);
        $siteContact->setRoutePattern($routePattern);
        $siteContact->setInFooter(false);
        $siteContact->setInMenu(true);
        $siteContact->addArea($siteContactArea0);
        $siteContact->addArea($siteContactArea3);
        $siteContact->addArea($siteContactArea6);
        $siteContact->addBlock($siteContactBlock0);

        return $siteContact;
    }
}
