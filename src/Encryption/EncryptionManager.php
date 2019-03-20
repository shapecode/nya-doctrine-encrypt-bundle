<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption;

use Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor\EncryptorInterface;

/**
 * Class EncryptionManager
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption
 * @author  Nikita Loges
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
     * @inheritDoc
     */
    public function getDefaultName(): string
    {
        return $this->default;
    }

    /**
     * @param EncryptorInterface $encryptor
     */
    public function addEncryptor(EncryptorInterface $encryptor): void
    {
        $this->encryptors[$encryptor->getName()] = $encryptor;
    }

    /**
     * @param null|string $name
     *
     * @return EncryptorInterface
     */
    protected function getEncryptor($name = null): EncryptorInterface
    {
        if ($name === null) {
            $name = $this->getDefaultName();
        }

        return $this->encryptors[$name];
    }

    /**
     * @inheritDoc
     */
    public function encrypt(string $data, ?string $name = null): string
    {
        $encryptor = $this->getEncryptor($name);

        return $encryptor->encrypt($data);
    }

    /**
     * @inheritDoc
     */
    public function decrypt(string $data, ?string $name = null): string
    {
        $encryptor = $this->getEncryptor($name);

        return $encryptor->decrypt($data);
    }

}
