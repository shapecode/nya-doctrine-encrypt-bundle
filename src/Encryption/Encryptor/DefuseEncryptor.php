<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor;

use Defuse\Crypto\Crypto;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DefuseEncryptor
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor
 * @author  Nikita Loges
 * @company tenolo GbR
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
    public function __construct($keyFile)
    {
        $this->keyFile = $keyFile;
        $this->fs = new Filesystem();
    }

    /**
     * @inheritdoc
     */
    public function encrypt($data)
    {
        return Crypto::encryptWithPassword($data, $this->getKey());
    }

    /**
     * @inheritdoc
     */
    public function decrypt($data)
    {
        return Crypto::decryptWithPassword($data, $this->getKey());
    }

    /**
     * @return string
     */
    private function getKey()
    {
        if ($this->encryptionKey === null) {
            if ($this->fs->exists($this->keyFile)) {
                $this->encryptionKey = file_get_contents($this->keyFile);
            } else {
                $string = random_bytes(255);
                $this->encryptionKey = bin2hex($string);
                $this->fs->dumpFile($this->keyFile, $this->encryptionKey);
            }
        }

        return $this->encryptionKey;
    }
}
