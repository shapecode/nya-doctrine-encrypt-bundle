<?php

namespace Shapecode\NYADoctrineEncryptBundle\Subscribers;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Shapecode\NYADoctrineEncryptBundle\Configuration\Encrypted;
use Shapecode\NYADoctrineEncryptBundle\Encryption\EncryptionManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Doctrine event subscriber which encrypt/decrypt entities
 */
class DoctrineEncryptSubscriber implements EventSubscriber
{

    const ENCRYPTION_MARKER = '<ENC>';
    const ENCRYPTED_ANN_NAME = Encrypted::class;

    /** @var EncryptionManagerInterface */
    private $encryptor;

    /** @var Reader */
    private $annReader;

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
     * Realization of EventSubscriber interface method.
     *
     * @return array Return all events which this subscriber is listening
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postUpdate,
            Events::preUpdate,
            Events::postLoad,
            Events::preFlush,
            Events::postFlush,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->processFields($entity, false);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->processFields($entity);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->processFields($entity, false);
    }

    /**
     * @param PreFlushEventArgs $preFlushEventArgs
     */
    public function preFlush(PreFlushEventArgs $preFlushEventArgs)
    {
        $unitOfWork = $preFlushEventArgs->getEntityManager()->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            $this->processFields($entity);
        }
    }

    /**
     * @param PostFlushEventArgs $postFlushEventArgs
     */
    public function postFlush(PostFlushEventArgs $postFlushEventArgs)
    {
        $unitOfWork = $postFlushEventArgs->getEntityManager()->getUnitOfWork();

        foreach ($unitOfWork->getIdentityMap() as $entityMap) {
            foreach ($entityMap as $entity) {
                $this->processFields($entity, false);
            }
        }
    }

    /**
     * Process (encrypt/decrypt) entities fields
     *
     * @param Object  $entity             doctrine entity
     * @param Boolean $isEncryptOperation If true - encrypt, false - decrypt entity
     *
     * @throws \RuntimeException
     *
     * @return object|null
     */
    public function processFields($entity, $isEncryptOperation = true)
    {

        if (!empty($this->encryptor)) {
            // Check which operation to be used
            $encryptorMethod = $isEncryptOperation ? 'encrypt' : 'decrypt';

            // Get the real class, we don't want to use the proxy classes
            if (strstr(get_class($entity), 'Proxies')) {
                $realClass = ClassUtils::getClass($entity);
            } else {
                $realClass = get_class($entity);
            }

            // Get ReflectionClass of our entity
            $properties = $this->getClassProperties($realClass);

            // Foreach property in the reflection class
            foreach ($properties as $refProperty) {
                if ($this->annReader->getPropertyAnnotation($refProperty, 'Doctrine\ORM\Mapping\Embedded')) {
                    $this->handleEmbeddedAnnotation($entity, $refProperty, $isEncryptOperation);
                    continue;
                }

                /**
                 * If property is an normal value and contains the Encrypt tag, lets encrypt/decrypt that property
                 */
                if ($this->annReader->getPropertyAnnotation($refProperty, self::ENCRYPTED_ANN_NAME)) {
                    $pac = PropertyAccess::createPropertyAccessor();
                    $value = $pac->getValue($entity, $refProperty->getName());
                    if ($encryptorMethod == 'decrypt') {
                        if (!is_null($value) and !empty($value)) {
                            if (substr($value, -strlen(self::ENCRYPTION_MARKER)) == self::ENCRYPTION_MARKER) {
                                $this->decryptCounter++;
                                $currentPropValue = $this->encryptor->decrypt(substr($value, 0, -5));
                                $pac->setValue($entity, $refProperty->getName(), $currentPropValue);
                            }
                        }
                    } else {
                        if (!is_null($value) and !empty($value)) {
                            if (substr($value, -strlen(self::ENCRYPTION_MARKER)) != self::ENCRYPTION_MARKER) {
                                $this->encryptCounter++;
                                $currentPropValue = $this->encryptor->encrypt($value) . self::ENCRYPTION_MARKER;
                                $pac->setValue($entity, $refProperty->getName(), $currentPropValue);
                            }
                        }
                    }
                }
            }

            return $entity;
        }

        return $entity;
    }

    private function handleEmbeddedAnnotation($entity, $embeddedProperty, $isEncryptOperation = true)
    {
        $reflectionClass = new \ReflectionClass($entity);
        $propName = $embeddedProperty->getName();

        $pac = PropertyAccess::createPropertyAccessor();

        $embeddedEntity = $pac->getValue($entity, $propName);

        if ($embeddedEntity) {
            $this->processFields($embeddedEntity, $isEncryptOperation);
        }
    }

    /**
     * Recursive function to get an associative array of class properties
     * including inherited ones from extended classes
     *
     * @param string $className Class name
     *
     * @return array
     */
    private function getClassProperties($className)
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
