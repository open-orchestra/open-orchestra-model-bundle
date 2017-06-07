<?php

namespace OpenOrchestra\ModelBundle\Command;

use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\Backoffice\Reference\ReferenceManager;
use OpenOrchestra\ModelBundle\Document\Block;
use OpenOrchestra\ModelBundle\Document\Content;
use OpenOrchestra\ModelBundle\Document\Node;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class OrchestraGenerateReferenceCommand
 */
class OrchestraGenerateReferenceCommand extends ContainerAwareCommand
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('orchestra:references:generate')
            ->setDescription('generate references')
            ->addArgument('document', null, 'Class of Document.');
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
        $document = $input->getArgument('document');

        $dm = $this->getContainer()->get('doctrine.odm.mongodb.document_manager');
        $referenceManager = $this->getContainer()->get('open_orchestra_backoffice.reference.manager');
        $this->updateUseReferenceEntity($document, $dm, $referenceManager, $output);
    }

    /**
     * @param String           $entityClass
     * @param DocumentManager  $dm
     * @param ReferenceManager $referenceManager
     * @param OutputInterface $output
     */
    protected function updateUseReferenceEntity($entityClass, DocumentManager $dm, ReferenceManager $referenceManager, OutputInterface $output)
    {
        $limit = 20;
        $countEntities = $dm->createQueryBuilder($entityClass)->getQuery()->count();
        for ($skip = 0; $skip < $countEntities; $skip += $limit) {
            $output->writeln('  - Update references from '.$skip.' to '.($skip+$limit));
            $entities = $dm->createQueryBuilder($entityClass)
                ->skip($skip)
                ->limit($limit)
                ->getQuery()->execute();
            foreach ($entities as $entity) {
                $referenceManager->updateReferencesToEntity($entity);
            }
            $dm->clear();
        }
    }
}
