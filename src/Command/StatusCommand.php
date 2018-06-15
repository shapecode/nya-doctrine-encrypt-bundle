<?php

namespace Shapecode\NYADoctrineEncryptBundle\Command;

use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class StatusCommand
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Command
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class StatusCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('doctrine:encrypt:status');
        $this->setDescription('Get status of doctrine encrypt bundle and the database');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
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
                implode(', ', $props)
            ];
        }

        $io->title('Show entities with encrypted properies');
        $io->block(sprintf('Found %d entities.', count($rows)), null, 'fg=white;bg=blue', ' ', true);
        $io->table([
            'Class Name',
            'Properties',
        ], $rows);
    }
}
