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
     * @param $entity
     */
    public function encrypt($entity): void;

    /**
     * @param $entity
     */
    public function decrypt($entity): void;

    /**
     * @param object              $entity
     * @param \ReflectionProperty $refProperty
     */
    public function encryptField($entity, \ReflectionProperty $refProperty): void;

    /**
     * @param object              $entity
     * @param \ReflectionProperty $refProperty
     */
    public function decryptField($entity, \ReflectionProperty $refProperty): void;

    /**
     * @param object              $entity
     * @param \ReflectionProperty $refProperty
     */
    public function encryptFieldEmbedded($entity, \ReflectionProperty $refProperty): void;

    /**
     * @param object              $entity
     * @param \ReflectionProperty $refProperty
     */
    public function decryptFieldEmbedded($entity, \ReflectionProperty $refProperty): void;
}
