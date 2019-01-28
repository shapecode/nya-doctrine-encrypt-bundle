<?php

namespace Shapecode\NYADoctrineEncryptBundle\Encryption;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\Mapping\Embedded;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class EncryptionHandler
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Encryption
 * @author  Nikita Loges
 */
class EncryptionHandler implements EncryptionHandlerInterface
{

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
            $realClass = $this->getRealClass($entity);
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
     * @inheritdoc
     */
    public function processField($entity, \ReflectionProperty $refProperty, $isEncryptOperation = true)
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

        $length = strlen(self::ENCRYPTION_MARKER) * -1;
        $substr = substr($value, $length);

        if ($isEncryptOperation) {
            if ($substr === self::ENCRYPTION_MARKER) {
                return;
            }

            $value = $this->encryptor->encrypt($value) . self::ENCRYPTION_MARKER;
        } else {
            if ($substr !== self::ENCRYPTION_MARKER) {
                return;
            }

            $value = substr($value, 0, $length);
            $value = $this->encryptor->decrypt($value);
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

    /**
     * @param $class
     *
     * @return bool|string
     */
    protected function getRealClass($class)
    {
        if (false === $pos = strrpos($class, '\\' . Proxy::MARKER . '\\')) {
            return $class;
        }

        return substr($class, $pos + Proxy::MARKER_LENGTH + 2);
    }
}
