<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor;

use ParagonIE\Halite\Alerts\CannotPerformOperation;
use ParagonIE\Halite\Alerts\InvalidKey;
use ParagonIE\Halite\HiddenString;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Symmetric\EncryptionKey;

/**
 * Class HaliteEncryptor
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class HaliteEncryptor implements EncryptorInterface
{

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
    }

    /**
     * @inheritdoc
     */
    public function encrypt($data)
    {
        return Crypto::encrypt(new HiddenString($data), $this->getKey());
    }

    /**
     * @inheritdoc
     */
    public function decrypt($data)
    {
        return Crypto::decrypt($data, $this->getKey());
    }

    /**
     * @return EncryptionKey
     * @throws CannotPerformOperation
     * @throws InvalidKey
     */
    private function getKey()
    {
        if ($this->encryptionKey === null) {
            try {
                $this->encryptionKey = KeyFactory::loadEncryptionKey($this->keyFile);
            } catch (CannotPerformOperation $e) {
                $this->encryptionKey = KeyFactory::generateEncryptionKey();

                KeyFactory::save($this->encryptionKey, $this->keyFile);
            }
        }

        return $this->encryptionKey;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'halite';
    }
}
