<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor;

/**
 * Interface EncryptorInterface
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor
 * @author  Nikita Loges
 * @company tenolo GbR
 */
interface EncryptorInterface
{

    /**
     * @param string $data
     *
     * @return string
     */
    public function encrypt($data);

    /**
     * @param string $data
     *
     * @return string
     */
    public function decrypt($data);
}
