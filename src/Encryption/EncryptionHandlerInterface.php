<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption;

use Shapecode\NYADoctrineEncryptBundle\Configuration\Encrypted;

/**
 * Interface EncryptionHandlerInterface
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption
 * @author  Nikita Loges
 * @company tenolo GbR
 */
interface EncryptionHandlerInterface
{

    const ENCRYPTION_MARKER = '<ENC>';
    const ENCRYPTED_ANN_NAME = Encrypted::class;

    /**
     * @param      $entity
     * @param bool $isEncryptOperation
     *
     * @return mixed
     */
    public function processFields($entity, $isEncryptOperation = true);

    /**
     * @param                     $entity
     * @param \ReflectionProperty $refProperty
     * @param bool                $isEncryptOperation
     */
    public function processField($entity, \ReflectionProperty $refProperty, $isEncryptOperation = true);
}
