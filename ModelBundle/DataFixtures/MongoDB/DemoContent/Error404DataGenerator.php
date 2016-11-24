<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelBundle\Document\Area;

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
        $nodeError404Block = new Block();
        $nodeError404Block->setLabel('Wysiwyg');
        $nodeError404Block->setComponent(TinyMCEWysiwygStrategy::NAME);
        $nodeError404Block->setAttributes(array(
            "htmlContent" => $htmlContent
        ));

        $nodeError404Block = $this->generateBlock($nodeError404Block);

        $main = new Area();
        $main->addBlock($nodeError404Block);

        $header = $this->createHeader();

        $footer = $this->createFooter();

        $nodeError404 = $this->createBaseNode();
        $nodeError404->setArea('main', $main);
        $nodeError404->setArea('footer', $footer);
        $nodeError404->setArea('header', $header);

        $nodeError404->setNodeType(NodeInterface::TYPE_ERROR);
        $nodeError404->setLanguage($language);
        $nodeError404->setNodeId(NodeInterface::ERROR_404_NODE_ID);
        $nodeError404->setName('Error 404');
        $nodeError404->setCreatedBy('fake_admin');
        $nodeError404->setParentId(NodeInterface::ROOT_PARENT_ID);
        $nodeError404->setRoutePattern($routePattern);
        $nodeError404->setInFooter(false);
        $nodeError404->setInMenu(false);
        $nodeError404->setOrder(-1);

        return $nodeError404;
    }
}
