<?php

namespace Shapecode\NYADoctrineEncryptBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class DecryptCommand
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Command
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class DecryptCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('doctrine:decrypt:database');
        $this->setDescription('Decrypt whole database on tables which are encrypted');
        $this->addOption('force', 'f', InputOption::VALUE_NONE);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $force = $input->getOption('force');

        $io = new SymfonyStyle($input, $output);

        // Get entity manager metadata
        $metaDataArray = $this->getEncryptionableEntityMetaData();

        // Set counter and loop through entity manager meta data
        $propertyCount = 0;
        foreach ($metaDataArray as $metaData) {
            if ($metaData->isMappedSuperclass) {
                continue;
            }

            $countProperties = count($this->getEncryptionableProperties($metaData));
            $propertyCount += $countProperties;
        }

        if (!$force) {
            // Start decrypting database
            $io->note('Decrypting all fields. This can take up to several minutes depending on the database size.');
            $io->note(count($metaDataArray) . ' entities found which are containing ' . $propertyCount . ' properties with the encryption tag. ' .
                'Wrong settings can mess up your data and it will be unrecoverable. ' .
                'I advise you to make a backup.');

            $confirmationQuestion = new ConfirmationQuestion('Continue with this action?', false);

            if (!$io->askQuestion($confirmationQuestion)) {
                return;
            }
        }

        $io->section('Decrypting');

        $batchSize = 20;
        $valueCounter = 0;
        $pac = PropertyAccess::createPropertyAccessor();
        $em = $this->registry->getManager();

        $this->subscriber->setEnable(false);

        // Loop through entity manager meta data
        foreach ($metaDataArray as $metaData) {
            $i = 0;
            $entityName = $metaData->getName();

            $iterator = $this->getEntityIterator($entityName);
            $totalCount = $this->getTableCount($entityName);

            $io->text(sprintf('Processing %s', $entityName));

            if ($totalCount > 0) {
                $properties = $this->getEncryptionableProperties($metaData);
                $progressBar = $io->createProgressBar($totalCount * count($properties));

                foreach ($iterator as $row) {
                    $i++;
                    $entity = $row[0];

                    //Loop through the property's in the entity
                    foreach ($properties as $property) {
                        $propertyName = $property->getName();

                        if ($pac->isReadable($entity, $propertyName) && $pac->isWritable($entity, $propertyName)) {
                            $this->encryptHandler->processField($entity, $property, false);
                            $valueCounter++;
                        }

                        $progressBar->advance(1);
                    }

                    $em->persist($entity);

                    if (($i % $batchSize) === 0) {
                        $em->flush();
                        $em->clear();
                    }

                }

                $progressBar->finish();
            } else {
                $io->block('no entities found.', 'INFO', 'fg=white;bg=blue', ' ', false);
            }

            $io->newLine(2);

            $em->flush();
            $em->clear();
        }

        $this->subscriber->setEnable(true);

        $io->section('Finished');
        $io->text('Decryption finished values found: <info>' . $valueCounter . '</info>');
        $io->text('All values are now decrypted.');
    }
}
