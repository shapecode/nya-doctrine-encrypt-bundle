<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor;

interface EncryptorInterface
{
    public function encrypt(string $data) : string;

    public function decrypt(string $data) : string;

    public function getName() : string;
}
