<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class Error404DataGenerator
 */
class Error404DataGenerator extends AbstractDataGenerator
{
    /**
     * @return Node
     */
    protected function generateNodeFr()
    {
        $htmlContent = <<<EOF
<div class='content2'>
    <h1>Page non trouvée</h1>
    <p>La page que vous tentez d'afficher n'existe pas. Vérifiez s'il vous plait que l'url est correcte.</p>
</div>
EOF;
        $routePattern = "errorPage404";
        $language = "fr";

        return $this->generateNodeGlobal($htmlContent, $language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeEn()
    {
        $htmlContent = <<<EOF
<div class='content2'>
    <h1>Page not found</h1>
    <p>The page you are trying to display does not exist. Please verify the url is correct.</p>
</div>
EOF;
        $routePattern = "errorPage404";
        $language = "en";

        return $this->generateNodeGlobal($htmlContent, $language, $routePattern);
    }

    /**
     * @return Node
     */
    protected function generateNodeDe()
    {
        $htmlContent = <<<EOF
<div class='content2'>
    <h1>Seite nicht gefunden</h1>
    <p>Die Seite, die Sie versuchen, existiert nicht angezeigt werden soll. Bitte überprüfen Sie die URL korrekt ist.</p>
</div>
EOF;
        $routePattern = "errorPage404";
        $language = "de";

        return $this->generateNodeGlobal($htmlContent, $language, $routePattern);
    }

    /**
     * @param string $htmlContent
     * @param string $language
     * @param string $routePattern
     *
     * @return Node
     */
    protected function generateNodeGlobal($htmlContent, $language, $routePattern)
    {
        $error404Block0 = new Block();
        $error404Block0->setLabel('Wysiwyg');
        $error404Block0->setComponent(TinyMCEWysiwygStrategy::NAME);
        $error404Block0->setAttributes(array(
            "htmlContent" => $htmlContent
        ));
        $error404Block0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $error404Area0 = $this->createHeader();
        $error404Area4 = $this->createColumnArea('Main content area 1', 'mainContentArea1', 'main-content-area1');
        $error404Area4->addBlock(array('nodeId' => 0, 'blockId' => 1, 'blockPrivate' => false));
        $error404Area5 = $this->createModuleArea();
        $error404Area3 = $this->createMain(array($error404Area4, $error404Area5));
        $error404Area6 = $this->createFooter();

        $error404 = $this->createBaseNode();
        $error404->setNodeType(NodeInterface::TYPE_ERROR);
        $error404->setLanguage($language);
        $error404->setNodeId(NodeInterface::ERROR_404_NODE_ID);
        $error404->setName('Error 404');
        $error404->setBoLabel('Error 404');
        $error404->setCreatedBy('fake_admin');
        $error404->setParentId(NodeInterface::ROOT_NODE_ID);
        $error404->setRoutePattern($routePattern);
        $error404->setInFooter(false);
        $error404->setInMenu(false);

        $rootArea = $error404->getRootArea();
        $rootArea->addArea($error404Area0);
        $rootArea->addArea($error404Area3);
        $rootArea->addArea($error404Area6);
        $error404->addBlock($error404Block0);

        return $error404;
    }
}
