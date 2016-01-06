<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\FooterStrategy;
use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class TransverseDataGenerator
 */
class TransverseDataGenerator extends AbstractDataGenerator
{
    /**
     * @return Node
     */
    protected function generateNodeFr()
    {
        return $this->generateNodeGlobal("fr");
    }

    /**
     * @return Node
     */
    protected function generateNodeEn()
    {
        return $this->generateNodeGlobal("en");
    }

    /**
     * @return Node
     */
    protected function generateNodeDe()
    {
        return $this->generateNodeGlobal("de");
    }

    /**
     * @param string $language
     *
     * @return Node
     */
    protected function generateNodeGlobal($language)
    {
        $siteBlockLogo = new Block();
        $siteBlockLogo->setLabel('Wysiwyg logo');
        $siteBlockLogo->setClass('logo');
        $siteBlockLogo->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $orchestraTitle = "Open Orchestra";
        if (isset($this->references['logo-orchestra'])) {
            $orchestraTitle = '[media=original]' . $this->references['logo-orchestra']->getId() . '[/media]';
        }
        $siteBlockLogo->setAttributes(
            array(
                "htmlContent" =>
                    '<a href="/" id="myLogo">' . $orchestraTitle . '</a>',
            )
        );
        $siteBlockLogo->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $siteBlockMainMenu = new Block();
        $siteBlockMainMenu->setLabel('Menu');
        $siteBlockMainMenu->setComponent('menu');
        $siteBlockMainMenu->setId('myMainMenu');
        $siteBlockMainMenu->setClass('my-main-menu');
        $siteBlockMainMenu->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $siteBlockFooter = new Block();
        $siteBlockFooter->setLabel('Wysiwyg footer');
        $siteBlockFooter->setComponent(TinyMCEWysiwygStrategy::TINYMCEWYSIWYG);
        $siteBlockFooter->setAttributes(array(
            "htmlContent" => <<<EOF
<div class='footer-networks'>
    <h4>Networks</h4>
    <ul>
        <li><a href="http://www.businessdecision.fr/">http://www.businessdecision.fr/</a></li>
        <li><a href="http://www.interakting.com/">http://www.interakting.com/</a></li>
    </ul>
</div>
<div class="footer-contact">
    <h4>Contact</h4>
    <ul>
        <li>Interakting</li>
        <li>153 Rue de Courcelles</li>
        <li>75017 Paris France</li>
        <li>01 56 21 21 21</li>
        <li><a href='/node/fixture_page_contact'>contact@interakting.com</a></li>
    </ul>
</div>
EOF
        ));
        $siteBlockFooter->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $siteBlockFooterMenu = new Block;
        $siteBlockFooterMenu->setLabel('footer menu');
        $siteBlockFooterMenu->setClass("footer-legal");
        $siteBlockFooterMenu->setComponent(FooterStrategy::FOOTER);

        $siteBlockContact = new Block();
        $siteBlockContact->setLabel('Contact');
        $siteBlockContact->setComponent('contact');
        $siteBlockContact->setId('myFormContact');
        $siteBlockContact->setClass('my-form-contact');
        $siteBlockContact->addArea(array('nodeId' => 0, 'areaId' => 'main'));

        $mainArea = $this->createArea('main','main','main');
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 0));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 1));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 2));
        $mainArea->addBlock(array('nodeId' => 0, 'blockId' => 3));

        $nodeTransverse = new Node();
        $nodeTransverse->setNodeId(NodeInterface::TRANSVERSE_NODE_ID);
        $nodeTransverse->setMaxAge(1000);
        $nodeTransverse->setNodeType(NodeInterface::TYPE_TRANSVERSE);
        $nodeTransverse->setName(NodeInterface::TRANSVERSE_NODE_ID);
        $nodeTransverse->setSiteId('2');
        $nodeTransverse->setParentId('-');
        $nodeTransverse->setPath('-');
        $nodeTransverse->setVersion(1);
        $nodeTransverse->setOrder(1);
        $nodeTransverse->setLanguage($language);
        $nodeTransverse->setStatus($this->references['status-draft']);
        $nodeTransverse->setDeleted(false);
        $nodeTransverse->setTemplateId('');
        $nodeTransverse->setTheme('');
        $nodeTransverse->setInFooter(false);
        $nodeTransverse->setInMenu(false);
        $nodeTransverse->addArea($mainArea);
        $nodeTransverse->addBlock($siteBlockLogo);
        $nodeTransverse->addBlock($siteBlockMainMenu);
        $nodeTransverse->addBlock($siteBlockFooter);
        $nodeTransverse->addBlock($siteBlockFooterMenu);
        $nodeTransverse->addBlock($siteBlockContact);

        return $nodeTransverse;
    }
}
