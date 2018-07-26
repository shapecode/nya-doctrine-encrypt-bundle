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
     * @param string      $data
     * @param string|null $name
     *
     * @return string
     */
    public function encrypt($data, $name = null);

    /**
     * @param string      $data
     * @param string|null $name
     *
     * @return string
     */
    public function decrypt($data, $name = null);
}
