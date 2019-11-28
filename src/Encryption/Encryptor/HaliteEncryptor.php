<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor;

use ParagonIE\Halite\Alerts\CannotPerformOperation;
use ParagonIE\Halite\HiddenString;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use Symfony\Component\Filesystem\Filesystem;
use function dirname;

final class HaliteEncryptor implements EncryptorInterface
{
    /** @var EncryptionKey */
    protected $encryptionKey;

    /** @var string */
    protected $keyFile;

    public function __construct(string $keyDirectory)
    {
        $this->keyFile = $keyDirectory . '/halite.key';
        $dir           = dirname($this->keyFile);

        $fs = new Filesystem();
        if ($fs->exists($dir)) {
            return;
        }

        $fs->mkdir($dir);
    }

    public function encrypt(string $data) : string
    {
        return Crypto::encrypt(new HiddenString($data), $this->getKey());
    }

    public function decrypt(string $data) : string
    {
        return Crypto::decrypt($data, $this->getKey())->getString();
    }

    private function getKey() : EncryptionKey
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

    public function getName() : string
    {
        return 'halite';
    }
}
