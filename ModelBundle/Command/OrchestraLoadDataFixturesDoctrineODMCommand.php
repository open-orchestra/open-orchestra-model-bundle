<?php

namespace OpenOrchestra\ModelBundle\Command;

use Doctrine\Bundle\MongoDBBundle\Command\LoadDataFixturesDoctrineODMCommand;
use Doctrine\Common\DataFixtures\Executor\MongoDBExecutor;
use Doctrine\Common\DataFixtures\Purger\MongoDBPurger;
use OpenOrchestra\ModelBundle\DataFixtures\Loader\OrchestraContainerAwareLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OrchestraLoadDataFixturesDoctrineODMCommand
 */
class OrchestraLoadDataFixturesDoctrineODMCommand extends LoadDataFixturesDoctrineODMCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:mongodb:fixtures:load')
            ->setDescription('Load data fixtures to your database.')
            ->addOption('fixtures', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The directory or file to load data fixtures from.')
            ->addOption('append', null, InputOption::VALUE_NONE, 'Append the data fixtures instead of flushing the database first.')
            ->addOption('dm', null, InputOption::VALUE_REQUIRED, 'The document manager to use for this command.')
            ->addOption('type', null, InputOption::VALUE_NONE, 'Choose type of loaded fixtures.')
            ->setHelp(<<<EOT
The <info>orchestra:mongodb:fixtures:load</info> command loads data fixtures from your bundles:

  <info>./app/console doctrine:mongodb:fixtures:load</info>

You can also optionally specify the path to fixtures with the <info>--fixtures</info> option:

  <info>./app/console doctrine:mongodb:fixtures:load --fixtures=/path/to/fixtures1 --fixtures=/path/to/fixtures2</info>

If you want to append the fixtures instead of flushing the database first you can use the <info>--append</info> option:

  <info>./app/console doctrine:mongodb:fixtures:load --append</info>

If you want to choose the type of loaded fixtures you can use the <info>--type</info> option:

  <info>./app/console doctrine:mongodb:fixtures:load --type</info>
EOT
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = 'functional';
        if ($input->getOption('type')) {
            $type = $this->getHelperSet()->get('dialog')->ask(
                $output,
                'Choose type in (' . implode(', ', $this->getContainer()->getParameter('open_orchestra_model.fixtures.command')) . ') : ',
                'production'
            );
        }

        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager($input->getOption('dm'));
        $dirOrFile = $input->getOption('fixtures');
        if ($dirOrFile) {
            $paths = is_array($dirOrFile) ? $dirOrFile : array($dirOrFile);
        } else {
            $paths = $this->getContainer()->getParameter('doctrine_mongodb.odm.fixtures_dirs');
            $paths = is_array($paths) ? $paths : array($paths);
            foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle) {
                $paths[] = $bundle->getPath().'/DataFixtures/MongoDB';
            }
        }

        $loader = new OrchestraContainerAwareLoader($this->getContainer(), $type);
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $loader->loadFromDirectory($path);
            }
        }

        $fixtures = $loader->getFixtures();
        if (!$fixtures) {
            throw new \InvalidArgumentException(
                sprintf('Could not find any fixtures to load in: %s', "\n\n- ".implode("\n- ", $paths))
            );
        }

        $purger = new MongoDBPurger($dm);
        $executor = new MongoDBExecutor($dm, $purger);
        $executor->setLogger(function($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });
        $executor->execute($fixtures, $input->getOption('append'));
    }
}
