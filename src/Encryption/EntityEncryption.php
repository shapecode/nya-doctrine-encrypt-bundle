<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\Encryption;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\Mapping\Embedded;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\PropertyAccess\PropertyAccess;
use function array_merge;
use function count;
use function explode;
use function get_class;
use function is_object;
use function strpos;
use function strrpos;
use function substr;

class EntityEncryption implements EntityEncryptionInterface
{
    /** @var EncryptionManagerInterface */
    protected $encryptor;

    /** @var Reader */
    protected $annReader;

    public function __construct(Reader $annReader, EncryptionManagerInterface $encryptor)
    {
        $this->annReader = $annReader;
        $this->encryptor = $encryptor;
    }

    public function encrypt(object $entity) : void
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

    public function decrypt(object $entity) : void
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

    public function encryptField(object $entity, ReflectionProperty $refProperty) : void
    {
        $propertyName = $refProperty->getName();

        $pac   = PropertyAccess::createPropertyAccessor();
        $value = $pac->getValue($entity, $propertyName);

        $hasMarker = $this->hasMarker($value);

        if ($hasMarker) {
            return;
        }

        $default = $this->encryptor->getDefaultName();

        $value = $this->encryptor->encrypt($value, $default) . self::ENCRYPTION_MARKER . $default;

        $pac->setValue($entity, $propertyName, $value);
    }

    public function decryptField(object $entity, ReflectionProperty $refProperty) : void
    {
        $propertyName = $refProperty->getName();

        $pac   = PropertyAccess::createPropertyAccessor();
        $value = $pac->getValue($entity, $propertyName);

        $hasMarker = $this->hasMarker($value);

        if (! $hasMarker) {
            return;
        }

        $data = explode(self::ENCRYPTION_MARKER, $value);

        $secret    = $data[0];
        $encryptor = $data[1] ?? null;

        $value = $this->encryptor->decrypt($secret, $encryptor);

        $pac->setValue($entity, $propertyName, $value);
    }

    public function encryptFieldEmbedded(object $entity, ReflectionProperty $refProperty) : void
    {
        $embeddedEntity = $this->getValue($entity, $refProperty);

        if ($embeddedEntity === null) {
            return;
        }

        $this->encrypt($embeddedEntity);
    }

    public function decryptFieldEmbedded(object $entity, ReflectionProperty $refProperty) : void
    {
        $embeddedEntity = $this->getValue($entity, $refProperty);

        if ($embeddedEntity === null) {
            return;
        }

        $this->decrypt($embeddedEntity);
    }

    /**
     * @return ReflectionProperty[]
     */
    protected function process(object $entity) : array
    {
        // Get the real class, we don't want to use the proxy classes
        if (strpos(get_class($entity), 'Proxies') !== false) {
            $realClass = $this->getRealClass($entity);
        } else {
            $realClass = get_class($entity);
        }

        $marked     = [];
        $properties = $this->getClassProperties($realClass);

        foreach ($properties as $property) {
            $annotation = $this->annReader->getPropertyAnnotation($property, self::ENCRYPTED_ANN_NAME);

            if ($annotation === null) {
                continue;
            }

            $pac   = PropertyAccess::createPropertyAccessor();
            $value = $pac->getValue($entity, $property->getName());

            if ($value !== null) {
                continue;
            }

            $marked[] = $property;
        }

        return $marked;
    }

    protected function hasMarker(?string $value) : bool
    {
        $substr = false;

        if ($value !== null) {
            $substr = strpos($value, self::ENCRYPTION_MARKER);
        }

        return $substr !== false;
    }

    /**
     * @return mixed
     */
    protected function getValue(object $entity, ReflectionProperty $refProperty)
    {
        $propName = $refProperty->getName();

        $pac = PropertyAccess::createPropertyAccessor();

        return $pac->getValue($entity, $propName);
    }

    /**
     * @return ReflectionProperty[]
     */
    protected function getClassProperties(string $className) : array
    {
        $reflectionClass = new ReflectionClass($className);
        $properties      = $reflectionClass->getProperties();
        $propertiesArray = [];

        foreach ($properties as $property) {
            $propertyName                   = $property->getName();
            $propertiesArray[$propertyName] = $property;
        }

        $parentClass = $reflectionClass->getParentClass();
        if ($parentClass !== false) {
            $parentPropertiesArray = $this->getClassProperties($parentClass->getName());
            if (count($parentPropertiesArray) > 0) {
                $propertiesArray = array_merge($parentPropertiesArray, $propertiesArray);
            }
        }

        return $propertiesArray;
    }

    /**
     * @param mixed $class
     */
    protected function getRealClass($class) : string
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $pos = strrpos($class, '\\' . Proxy::MARKER . '\\');
        if ($pos === false) {
            return $class;
        }

        return substr($class, $pos + Proxy::MARKER_LENGTH + 2);
    }
}
