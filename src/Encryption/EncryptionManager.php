<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption;

use Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor\EncryptorInterface;

/**
 * Class EncryptionManager
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class EncryptionManager implements EncryptionManagerInterface
{

    /** @var array|EncryptorInterface[] */
    protected $encryptors = [];

    /** @var string */
    protected $default;

    /**
     * @param string $default
     */
    public function __construct($default)
    {
        $this->default = $default;
    }

    /**
     * @param EncryptorInterface $encryptor
     */
    public function addEncryptor(EncryptorInterface $encryptor)
    {
        $this->encryptors[$encryptor->getName()] = $encryptor;
    }

    /**
     * @param null|string $name
     *
     * @return EncryptorInterface
     */
    protected function getEncryptor($name = null)
    {
        if ($name === null) {
            $name = $this->default;
        }

        return $this->encryptors[$name];
    }

    /**
     * @inheritDoc
     */
    public function encrypt($data, $name = null)
    {
        $encryptor = $this->getEncryptor($name);

        return $encryptor->encrypt($data);
    }

    /**
     * @inheritDoc
     */
    public function decrypt($data, $name = null)
    {
        $encryptor = $this->getEncryptor($name);

        return $encryptor->decrypt($data);
    }

}
