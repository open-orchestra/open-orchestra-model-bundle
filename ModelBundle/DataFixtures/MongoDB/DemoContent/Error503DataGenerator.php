<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;

/**
 * Class Error503DataGenerator
 */
class Error503DataGenerator extends AbstractDataGenerator
{
    /**
     * @return Node
     */
    protected function generateNodeFr()
    {
        $htmlContent = <<<EOF
<div class='content2'>
    <h1>Site en maintenance</h1>
    <p>Le site est actuellement en maintenance. Vous pourrez à nouveau le consulter très prochainement.</p>
</div>
EOF;
        $routePattern = "errorPage503";
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
    <h1>Website under maintenance</h1>
    <p>The site is currently under maintenance. You will be able to visit it again in the very near future.</p>
</div>
EOF;
        $routePattern = "errorPage503";
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
    <h1>Webseite unter Wartung</h1>
    <p>Die Seite befindet sich derzeit im Wartungsmodus. Sie können es wieder in sehr naher Zukunft zu besuchen.</p>
</div>
EOF;
        $routePattern = "errorPage503";
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
        $error503Block0 = new Block();
        $error503Block0->setLabel('Wysiwyg');
        $error503Block0->setComponent(TinyMCEWysiwygStrategy::NAME);
        $error503Block0->setAttributes(array(
            "htmlContent" => $htmlContent
        ));
        $error503Block0->addArea(array('nodeId' => 0, 'areaId' => 'mainContentArea1'));

        $error503Area0 = $this->createHeader();
        $error503Area4 = $this->createColumnArea('Main content area 1', 'mainContentArea1', 'main-content-area1');
        $error503Area4->addBlock(array('nodeId' => 0, 'blockId' => 1));
        $error503Area5 = $this->createModuleArea();
        $error503Area3 = $this->createMain(array($error503Area4, $error503Area5));
        $error503Area6 = $this->createFooter();

        $error503 = $this->createBaseNode();
        $error503->setNodeType(NodeInterface::TYPE_ERROR);
        $error503->setLanguage($language);
        $error503->setNodeId(NodeInterface::ERROR_503_NODE_ID);
        $error503->setName('Error 503');
        $error503->setBoLabel('Error 503');
        $error503->setCreatedBy('fake_admin');
        $error503->setParentId(NodeInterface::ROOT_NODE_ID);
        $error503->setRoutePattern($routePattern);
        $error503->setInFooter(false);
        $error503->setInMenu(false);

        $rootArea = $error503->getRootArea();
        $rootArea->addArea($error503Area0);
        $rootArea->addArea($error503Area3);
        $rootArea->addArea($error503Area6);
        $error503->addBlock($error503Block0);

        return $error503;
    }
}
