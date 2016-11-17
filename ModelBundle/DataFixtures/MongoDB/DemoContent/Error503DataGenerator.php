<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB\DemoContent;

use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\DisplayBundle\DisplayBlock\Strategies\TinyMCEWysiwygStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelBundle\Document\Area;

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
        $nodeError503Block = new Block();
        $nodeError503Block->setLabel('Wysiwyg');
        $nodeError503Block->setComponent(TinyMCEWysiwygStrategy::NAME);
        $nodeError503Block->setAttributes(array(
            "htmlContent" => $htmlContent
        ));

        $nodeError503Block = $this->generateBlock($nodeError503Block);

        $main = new Area();
        $main->addBlock($nodeError503Block);

        $header = $this->createHeader();

        $footer = $this->createFooter();

        $nodeError503 = $this->createBaseNode();
        $nodeError503->setArea('main', $main);
        $nodeError503->setArea('footer', $footer);
        $nodeError503->setArea('header', $header);

        $nodeError503->setNodeType(NodeInterface::TYPE_ERROR);
        $nodeError503->setLanguage($language);
        $nodeError503->setNodeId(NodeInterface::ERROR_503_NODE_ID);
        $nodeError503->setName('Error 503');
        $nodeError503->setBoLabel('Error 503');
        $nodeError503->setCreatedBy('fake_admin');
        $nodeError503->setParentId(NodeInterface::ROOT_PARENT_ID);
        $nodeError503->setRoutePattern($routePattern);
        $nodeError503->setInFooter(false);
        $nodeError503->setInMenu(false);
        $nodeError503->setOrder(-1);

        return $nodeError503;
    }
}
