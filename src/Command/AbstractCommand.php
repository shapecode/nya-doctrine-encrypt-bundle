<?php

namespace Shapecode\NYADoctrineEncryptBundle\Command;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Shapecode\NYADoctrineEncryptBundle\Configuration\Encrypted;
use Shapecode\NYADoctrineEncryptBundle\Encryption\EncryptionHandlerInterface;
use Shapecode\NYADoctrineEncryptBundle\Encryption\EncryptionManagerInterface;
use Shapecode\NYADoctrineEncryptBundle\EventListener\DoctrineEncryptSubscriber;
use Symfony\Component\Console\Command\Command;

/**
 * Class AbstractCommand
 *
 * @package Shapecode\NYADoctrineEncryptBundle\Command
 * @author  Nikita Loges
 * @company tenolo GbR
 */
abstract class AbstractCommand extends Command
{
    /** @var ManagerRegistry */
    protected $registry;

    /** @var EncryptionHandlerInterface */
    protected $encryptHandler;

    /** @var EncryptionManagerInterface */
    protected $encryptManager;

    /** @var DoctrineEncryptSubscriber */
    protected $subscriber;

    /** @var Reader */
    protected $annotationReader;

    /**
     * @param ManagerRegistry            $registry
     * @param EncryptionHandlerInterface $encryptHandler
     * @param EncryptionManagerInterface $encryptManager
     * @param DoctrineEncryptSubscriber  $subscriber
     * @param Reader                     $annotationReader
     */
    public function __construct(
        ManagerRegistry $registry,
        EncryptionHandlerInterface $encryptHandler,
        EncryptionManagerInterface $encryptManager,
        DoctrineEncryptSubscriber $subscriber,
        Reader $annotationReader
    )
    {
        $this->registry = $registry;
        $this->encryptHandler = $encryptHandler;
        $this->encryptManager = $encryptManager;
        $this->subscriber = $subscriber;
        $this->annotationReader = $annotationReader;

        parent::__construct();
    }

    /**
     * Get an result iterator over the whole table of an entity.
     *
     * @param string $entityName
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    protected function getEntityIterator($entityName)
    {
        $query = $this->registry->getManager()->createQuery(sprintf('SELECT o FROM %s o', $entityName));

        return $query->iterate();
    }

    /**
     * Get the number of rows in an entity-table
     *
     * @param string $entityName
     *
     * @return int
     */
    protected function getTableCount($entityName)
    {
        $query = $this->registry->getManager()->createQuery(sprintf('SELECT COUNT(o) FROM %s o', $entityName));

        return (int)$query->getSingleScalarResult();
    }

    /**
     * @return ClassMetadata[]
     */
    protected function getEncryptionableEntityMetaData()
    {
        $validMetaData = [];
        $metaDataArray = $this->registry->getManager()->getMetadataFactory()->getAllMetadata();

        foreach ($metaDataArray as $entityMetaData) {
            if ($entityMetaData->isMappedSuperclass) {
                continue;
            }

            $properties = $this->getEncryptionableProperties($entityMetaData);
            if (count($properties) === 0) {
                continue;
            }

            $validMetaData[] = $entityMetaData;
        }

        return $validMetaData;
    }

    /**
     * @param ClassMetadata $entityMetaData
     *
     * @return array|\ReflectionProperty[]
     */
    protected function getEncryptionableProperties(ClassMetadata $entityMetaData)
    {
        //Create reflectionClass for each meta data object
        $reflectionClass = new \ReflectionClass($entityMetaData->getName());
        $propertyArray = $reflectionClass->getProperties();
        $properties = [];

        foreach ($propertyArray as $property) {
            if ($this->annotationReader->getPropertyAnnotation($property, Encrypted::class)) {
                $properties[] = $property;
            }
        }

        return $properties;
    }
}
