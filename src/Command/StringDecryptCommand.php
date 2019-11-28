<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\Command;

use Shapecode\NYADoctrineEncryptBundle\Encryption\EncryptionManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class StringDecryptCommand extends Command
{
    /** @var EncryptionManagerInterface */
    protected $encryptionManager;

    public function __construct(EncryptionManagerInterface $encryptionManager)
    {
        $this->encryptionManager = $encryptionManager;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this->setName('encryption:decrypt');
        $this->setDescription('Encrypt an argument');
        $this->addArgument('text', InputArgument::REQUIRED);
        $this->addArgument('encryptor', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string $text */
        $text = $input->getArgument('text');

        /** @var string|null $encryptor */
        $encryptor = $input->getArgument('encryptor');

        $io = new SymfonyStyle($input, $output);

        $io->section('Encrypting');

        $secret = $this->encryptionManager->decrypt($text, $encryptor);

        $io->writeln($secret);

        return 0;
    }
}
