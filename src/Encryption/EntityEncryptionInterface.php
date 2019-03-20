<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption;

use Shapecode\NYADoctrineEncryptBundle\Configuration\Encrypted;

/**
 * Interface EntityEncryptionInterface
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption
 * @author  Nikita Loges
 */
interface EntityEncryptionInterface
{

    public const ENCRYPTION_MARKER = '<ENC>';
    public const ENCRYPTED_ANN_NAME = Encrypted::class;

    /**
     * @param object $entity
     */
    public function encrypt(object $entity): void;

    /**
     * @param object $entity
     */
    public function decrypt(object $entity): void;

    /**
     * @param object              $entity
     * @param \ReflectionProperty $refProperty
     */
    public function encryptField(object $entity, \ReflectionProperty $refProperty): void;

    /**
     * @param object              $entity
     * @param \ReflectionProperty $refProperty
     */
    public function decryptField(object $entity, \ReflectionProperty $refProperty): void;

    /**
     * @param object              $entity
     * @param \ReflectionProperty $refProperty
     */
    public function encryptFieldEmbedded(object $entity, \ReflectionProperty $refProperty): void;

    /**
     * @param object              $entity
     * @param \ReflectionProperty $refProperty
     */
    public function decryptFieldEmbedded(object $entity, \ReflectionProperty $refProperty): void;
}
