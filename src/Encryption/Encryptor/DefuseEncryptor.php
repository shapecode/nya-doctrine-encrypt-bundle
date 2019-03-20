<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor;

use Defuse\Crypto\Crypto;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DefuseEncryptor
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor
 * @author  Nikita Loges
 */
class DefuseEncryptor implements EncryptorInterface
{

    /** @var Filesystem */
    protected $fs;

    /** @var string */
    protected $encryptionKey;

    /** @var string */
    protected $keyFile;

    /**
     * @inheritdoc
     */
    public function __construct($keyDirectory)
    {
        $this->keyFile = $keyDirectory.'/defuse.key';
        $dir = dirname($this->keyFile);

        $this->fs = new Filesystem();
        if (!$this->fs->exists($dir)) {
            $this->fs->mkdir($dir);
        }
    }

    /**
     * @inheritdoc
     */
    public function encrypt(string $data): string
    {
        return Crypto::encryptWithPassword($data, $this->getKey());
    }

    /**
     * @inheritdoc
     */
    public function decrypt(string $data): string
    {
        return Crypto::decryptWithPassword($data, $this->getKey());
    }

    /**
     * @return string
     */
    protected function getKey(): string
    {
        if ($this->encryptionKey === null) {
            if ($this->fs->exists($this->keyFile)) {
                $this->encryptionKey = trim(file_get_contents($this->keyFile));
            } else {
                $string = random_bytes(255);
                $this->encryptionKey = bin2hex($string);
                $this->fs->dumpFile($this->keyFile, $this->encryptionKey);
            }
        }

        return $this->encryptionKey;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'defuse';
    }
}
