<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor;

/**
 * Interface EncryptorInterface
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption\Encryptor
 * @author  Nikita Loges
 */
interface EncryptorInterface
{

    /**
     * @param string $data
     *
     * @return string
     */
    public function encrypt(string $data): string;

    /**
     * @param string $data
     *
     * @return string
     */
    public function decrypt(string $data): string;

    /**
     * @return string
     */
    public function getName(): string;
}
