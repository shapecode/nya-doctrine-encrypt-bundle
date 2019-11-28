<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\Encryption;

use Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor\EncryptorInterface;

class EncryptionManager implements EncryptionManagerInterface
{
    /** @var array|EncryptorInterface[] */
    protected $encryptors = [];

    /** @var string */
    protected $default;

    public function __construct(string $defaultEncryptor)
    {
        $this->default = $defaultEncryptor;
    }

    public function getDefaultName() : string
    {
        return $this->default;
    }

    public function addEncryptor(EncryptorInterface $encryptor) : void
    {
        $this->encryptors[$encryptor->getName()] = $encryptor;
    }

    protected function getEncryptor(?string $name = null) : EncryptorInterface
    {
        if ($name === null) {
            $name = $this->getDefaultName();
        }

        return $this->encryptors[$name];
    }

    public function encrypt(string $data, ?string $name = null) : string
    {
        $encryptor = $this->getEncryptor($name);

        return $encryptor->encrypt($data);
    }

    public function decrypt(string $data, ?string $name = null) : string
    {
        $encryptor = $this->getEncryptor($name);

        return $encryptor->decrypt($data);
    }
}
