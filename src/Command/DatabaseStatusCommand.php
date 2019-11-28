<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\Command;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function count;
use function implode;
use function sprintf;

final class DatabaseStatusCommand extends AbstractCommand
{
    public function __construct(
        ManagerRegistry $registry,
        Reader $reader
    ) {
        $this->registry         = $registry;
        $this->annotationReader = $reader;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this->setName('encryption:doctrine:status');
        $this->setDescription('Get status of doctrine encrypt bundle and the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $em = $this->registry->getManager();

        /** @var ClassMetadata[] $metaDataArray */
        $metaDataArray = $em->getMetadataFactory()->getAllMetadata();

        $io = new SymfonyStyle($input, $output);

        $rows = [];
        foreach ($metaDataArray as $metaData) {
            if ($metaData->isMappedSuperclass) {
                continue;
            }

            $properties = $this->getEncryptionableProperties($metaData);

            if (count($properties) === 0) {
                continue;
            }

            $props = [];
            foreach ($properties as $p) {
                $props[] = $p->getName();
            }

            $rows[] = [
                $metaData->getName(),
                implode(', ', $props),
            ];
        }

        $io->title('Show entities with encrypted properies');
        $io->block(sprintf('Found %d entities.', count($rows)), null, 'fg=white;bg=blue', ' ', true);
        $io->table([
            'Class Name',
            'Properties',
        ], $rows);

        return 0;
    }
}
