<?php

namespace Shapecode\NYADoctrineEncryptBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class EncryptCommand
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Command
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class EncryptCommand extends AbstractCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('doctrine:encrypt:database');
        $this->setDescription('Encrypt whole database on tables which are not encrypted yet');
        $this->addArgument('encryptor', InputArgument::OPTIONAL, 'The encryptor you want to decrypt the database with');
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

        if (!$force) {
            $io->note('Encrypting all fields can take up to several minutes depending on the database size.');
            $io->note(count($metaDataArray) . ' entities found which are containing properties with the encryption tag. ' .
                'Wrong settings can mess up your data and it will be unrecoverable. ' .
                'I advise you to make a backup.');

            $confirmationQuestion = new ConfirmationQuestion('Continue with this action?', false);

            if (!$io->askQuestion($confirmationQuestion)) {
                return;
            }
        }

        $batchSize = 20;
        $em = $this->registry->getManager();

        $io->section('Encrypting');

        $this->subscriber->setEnable(false);

        // Loop through entity manager meta data
        foreach ($metaDataArray as $metaData) {
            $i = 0;
            $entityName = $metaData->getName();

            $iterator = $this->getEntityIterator($entityName);
            $totalCount = $this->getTableCount($entityName);

            $io->text(sprintf('Processing %s', $entityName));
            $progressBar = $io->createProgressBar($totalCount);

            foreach ($iterator as $row) {
                $i++;

                $this->encryptHandler->processFields($row[0]);
                $progressBar->advance(1);

                if (($i % $batchSize) === 0) {
                    $em->flush();
                    $em->clear();
                }
            }

            $progressBar->finish();
            $io->newLine(2);

            $em->flush();
        }

        $this->subscriber->setEnable(true);

        $io->section('Finished');
        $io->text('Encryption finished.');
        $io->text('All values are now encrypted');
    }

}
