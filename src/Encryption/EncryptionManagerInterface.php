<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\Encryption;

interface EncryptionManagerInterface
{
    public function getDefaultName() : string;

    public function encrypt(string $data, ?string $name = null) : string;

    public function decrypt(string $data, ?string $name = null) : string;
}
