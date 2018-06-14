<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption;

/**
 * Interface EncryptionHandlerInterface
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption
 * @author  Nikita Loges
 * @company tenolo GbR
 */
interface EncryptionHandlerInterface
{

    /**
     * @param      $entity
     * @param bool $isEncryptOperation
     *
     * @return mixed
     */
    public function processFields($entity, $isEncryptOperation = true);
}
