<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption;

/**
 * Interface EncryptionManagerInterface
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption
 * @author  Nikita Loges
 */
interface EncryptionManagerInterface
{

    /**
     * @return string
     */
    public function getDefaultName(): string;

    /**
     * @param string      $data
     * @param string|null $name
     *
     * @return string
     */
    public function encrypt(string $data, ?string $name = null): string;

    /**
     * @param string      $data
     * @param string|null $name
     *
     * @return string
     */
    public function decrypt(string $data, ?string $name = null): string;
}
