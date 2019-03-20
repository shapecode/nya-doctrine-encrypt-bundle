<?php

namespace Shapecode\NYADoctrineEncryptBundle\Command;

use Shapecode\NYADoctrineEncryptBundle\Encryption\EncryptionManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class StringDecryptCommand
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Command
 * @author  Nikita Loges
 */
class StringDecryptCommand extends Command
{

    /** @var $encryptionManager */
    protected $encryptionManager;

    /**
     * @param EncryptionManagerInterface $encryptionManager
     */
    public function __construct(EncryptionManagerInterface $encryptionManager)
    {
        $this->encryptionManager = $encryptionManager;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName('encryption:decrypt');
        $this->setDescription('Encrypt an argument');
        $this->addArgument('text', InputArgument::REQUIRED);
        $this->addArgument('encryptor', InputArgument::OPTIONAL);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = $input->getArgument('text');
        $encryptor = $input->getArgument('encryptor');

        $io = new SymfonyStyle($input, $output);

        $io->section('Encrypting');

        $secret = $this->encryptionManager->decrypt($text, $encryptor);

        $io->writeln($secret);
    }

}
