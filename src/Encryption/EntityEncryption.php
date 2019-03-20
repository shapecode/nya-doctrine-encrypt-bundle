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
class EntityEncryption implements EntityEncryptionInterface
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
     */
    public function encrypt(object $entity): void
    {
        $properties = $this->process($entity);

        foreach ($properties as $refProperty) {
            $embedded = $this->annReader->getPropertyAnnotation($refProperty, Embedded::class);

            if ($embedded !== null) {
                $this->encryptFieldEmbedded($entity, $refProperty);
            } else {
                $this->encryptField($entity, $refProperty);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function decrypt(object $entity): void
    {
        $properties = $this->process($entity);

        foreach ($properties as $refProperty) {
            $embedded = $this->annReader->getPropertyAnnotation($refProperty, Embedded::class);

            if ($embedded !== null) {
                $this->decryptFieldEmbedded($entity, $refProperty);
            } else {
                $this->decryptField($entity, $refProperty);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function encryptField(object $entity, \ReflectionProperty $refProperty): void
    {
        $propertyName = $refProperty->getName();

        $pac = PropertyAccess::createPropertyAccessor();
        $value = $pac->getValue($entity, $propertyName);

        $hasMarker = $this->hasMarker($value);

        if ($hasMarker) {
            return;
        }

        $default = $this->encryptor->getDefaultName();

        $value = $this->encryptor->encrypt($value, $default).self::ENCRYPTION_MARKER.$default;

        $pac->setValue($entity, $propertyName, $value);
    }

    /**
     * @inheritdoc
     */
    public function decryptField(object $entity, \ReflectionProperty $refProperty): void
    {
        $propertyName = $refProperty->getName();

        $pac = PropertyAccess::createPropertyAccessor();
        $value = $pac->getValue($entity, $propertyName);

        $hasMarker = $this->hasMarker($value);

        if (!$hasMarker) {
            return;
        }

        $data = explode(self::ENCRYPTION_MARKER, $value);

        $secret = $data[0];
        $encryptor = null;

        if (isset($data[1]) && !empty($data[1])) {
            $encryptor = $data[1];
        }

        $value = $this->encryptor->decrypt($secret, $encryptor);

        $pac->setValue($entity, $propertyName, $value);
    }

    /**
     * @inheritdoc
     */
    public function encryptFieldEmbedded(object $entity, \ReflectionProperty $refProperty): void
    {
        $embeddedEntity = $this->getValue($entity, $refProperty);

        if ($embeddedEntity) {
            $this->encrypt($embeddedEntity);
        }
    }

    /**
     * @inheritdoc
     */
    public function decryptFieldEmbedded(object $entity, \ReflectionProperty $refProperty): void
    {
        $embeddedEntity = $this->getValue($entity, $refProperty);

        if ($embeddedEntity) {
            $this->decrypt($embeddedEntity);
        }
    }

    /**
     * @param object $entity
     *
     * @return \ReflectionProperty[]
     * @throws \ReflectionException
     */
    protected function process(object $entity): array
    {
        // Get the real class, we don't want to use the proxy classes
        if (false !== strpos(get_class($entity), 'Proxies')) {
            $realClass = $this->getRealClass($entity);
        } else {
            $realClass = get_class($entity);
        }

        $marked = [];
        $properties = $this->getClassProperties($realClass);

        foreach ($properties as $property) {
            $annotation = $this->annReader->getPropertyAnnotation($property, self::ENCRYPTED_ANN_NAME);

            if ($annotation !== null) {
                $pac = PropertyAccess::createPropertyAccessor();
                $value = $pac->getValue($entity, $property->getName());

                if (!empty($value)) {
                    $marked[] = $property;
                }
            }
        }

        return $marked;

    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function hasMarker(string $value): bool
    {
        $substr = strpos($value, self::ENCRYPTION_MARKER);

        return $substr !== false;
    }

    /**
     * @param object              $entity
     * @param \ReflectionProperty $refProperty
     *
     * @return mixed
     */
    protected function getValue(object $entity, \ReflectionProperty $refProperty)
    {
        $propName = $refProperty->getName();

        $pac = PropertyAccess::createPropertyAccessor();

        return $pac->getValue($entity, $propName);
    }

    /**
     * @param $className
     *
     * @return \ReflectionProperty[]
     * @throws \ReflectionException
     */
    protected function getClassProperties($className): array
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
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (false === $pos = strrpos($class, '\\'.Proxy::MARKER.'\\')) {
            return $class;
        }

        return substr($class, $pos + Proxy::MARKER_LENGTH + 2);
    }
}
