<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\Encryption;

use ReflectionProperty;
use Shapecode\NYADoctrineEncryptBundle\Configuration\Encrypted;

interface EntityEncryptionInterface
{
    public const ENCRYPTION_MARKER  = '<ENC>';
    public const ENCRYPTED_ANN_NAME = Encrypted::class;

    public function encrypt(object $entity) : void;

    public function decrypt(object $entity) : void;

    public function encryptField(object $entity, ReflectionProperty $refProperty) : void;

    public function decryptField(object $entity, ReflectionProperty $refProperty) : void;

    public function encryptFieldEmbedded(object $entity, ReflectionProperty $refProperty) : void;

    public function decryptFieldEmbedded(object $entity, ReflectionProperty $refProperty) : void;
}
