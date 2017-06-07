<?php

namespace OpenOrchestra\ModelBundle\Tests\Command;

use OpenOrchestra\BaseBundle\Tests\AbstractTest\AbstractBaseTestCase;
use Phake;
use Symfony\Component\Console\Application;
use OpenOrchestra\ModelBundle\Command\OrchestraGenerateReferenceCommand;

/**
 * Class OrchestraGenerateReferenceCommandTest
 */
class OrchestraGenerateReferenceCommandTest extends AbstractBaseTestCase
{
    /**
     * @var OrchestraGenerateReferenceCommand
     */
    protected $command;

    protected $container;
    protected $application;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');


    /*use OpenOrchestra\ModelBundle\Document\Block;
    use OpenOrchestra\ModelBundle\Document\Content;
    use OpenOrchestra\ModelBundle\Document\Node;*/


        $this->command = new OrchestraGenerateReferenceCommand();
        $this->command->setContainer($this->container);

        $this->application = new Application();
        $this->application->add($this->command);
    }

    /**
     * Test presence and name
     */
    public function testPresenceAndName()
    {
        $command = $this->application->find('orchestra:references:generate');

        $this->assertInstanceOf('Symfony\Component\Console\Command\Command', $command);
    }

    /**
     * Test the definition
     */
    public function testDefinition()
    {
        $definition = $this->command->getDefinition();

        $this->assertTrue($definition->hasArgument('document'));
    }
}
