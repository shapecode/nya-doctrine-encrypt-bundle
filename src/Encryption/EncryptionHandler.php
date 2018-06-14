<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Mapping\Embedded;
use Shapecode\NYADoctrineEncryptBundle\Configuration\Encrypted;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class EncryptionHandler
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class EncryptionHandler implements EncryptionHandlerInterface
{

    const ENCRYPTION_MARKER = '<ENC>';
    const ENCRYPTED_ANN_NAME = Encrypted::class;

    /** @var EncryptionManagerInterface */
    protected $encryptor;

    /** @var Reader */
    protected $annReader;

    /**
     * @param Reader                     $annReader
     * @param EncryptionManagerInterface $encryptor
     */
    public function __construct(Reader $annReader, EncryptionManagerInterface $encryptor)
    {
        $this->annReader = $annReader;
        $this->encryptor = $encryptor;
    }

    /**
     * @inheritdoc
     * @throws \ReflectionException
     */
    public function processFields($entity, $isEncryptOperation = true)
    {
        // Get the real class, we don't want to use the proxy classes
        if (false !== strpos(get_class($entity), 'Proxies')) {
            $realClass = ClassUtils::getClass($entity);
        } else {
            $realClass = get_class($entity);
        }

        $properties = $this->getClassProperties($realClass);

        foreach ($properties as $refProperty) {
            $embedded = $this->annReader->getPropertyAnnotation($refProperty, Embedded::class);

            if ($embedded !== null) {
                $this->processEmbeddedField($entity, $refProperty, $isEncryptOperation);
            } else {
                $this->processField($entity, $refProperty, $isEncryptOperation);
            }

        }

        return $entity;
    }

    /**
     * @param                     $entity
     * @param \ReflectionProperty $refProperty
     * @param bool                $isEncryptOperation
     */
    protected function processField($entity, \ReflectionProperty $refProperty, $isEncryptOperation = true)
    {
        $annotation = $this->annReader->getPropertyAnnotation($refProperty, self::ENCRYPTED_ANN_NAME);
        $propertyName = $refProperty->getName();

        if ($annotation === null) {
            return;
        }

        $pac = PropertyAccess::createPropertyAccessor();
        $value = $pac->getValue($entity, $propertyName);

        if (empty($value)) {
            return;
        }

        if (substr($value, -strlen(self::ENCRYPTION_MARKER)) === self::ENCRYPTION_MARKER) {
            return;
        }

        if ($isEncryptOperation) {
            $value = $this->encryptor->encrypt($value) . self::ENCRYPTION_MARKER;
        } else {
            $value = $this->encryptor->decrypt(substr($value, 0, -5));
        }

        $pac->setValue($entity, $propertyName, $value);
    }

    /**
     * @param                     $entity
     * @param \ReflectionProperty $embeddedProperty
     * @param bool                $isEncryptOperation
     *
     * @throws \ReflectionException
     */
    protected function processEmbeddedField($entity, \ReflectionProperty $embeddedProperty, $isEncryptOperation = true)
    {
        $propName = $embeddedProperty->getName();

        $pac = PropertyAccess::createPropertyAccessor();

        $embeddedEntity = $pac->getValue($entity, $propName);

        if ($embeddedEntity) {
            $this->processFields($embeddedEntity, $isEncryptOperation);
        }
    }

    /**
     * @param $className
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function getClassProperties($className)
    {
        $reflectionClass = new \ReflectionClass($className);
        $properties = $reflectionClass->getProperties();
        $propertiesArray = [];

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertiesArray[$propertyName] = $property;
        }

        if ($parentClass = $reflectionClass->getParentClass()) {
            $parentPropertiesArray = $this->getClassProperties($parentClass->getName());
            if (count($parentPropertiesArray) > 0) {
                $propertiesArray = array_merge($parentPropertiesArray, $propertiesArray);
            }
        }

        return $propertiesArray;
    }
}
