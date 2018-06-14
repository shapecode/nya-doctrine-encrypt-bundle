<?php

namespace Shapecode\NYADoctrineEncryptBundle\Command;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Shapecode\NYADoctrineEncryptBundle\Subscribers\DoctrineEncryptSubscriber;
use Symfony\Component\Console\Command\Command;

/**
 * Base command containing usefull base methods.
 *
 * @author Michael Feinbier <michael@feinbier.net>
 **/
abstract class AbstractCommand extends Command
{
    /** @var ManagerRegistry */
    protected $entityManager;

    /** @var DoctrineEncryptSubscriber */
    protected $subscriber;

    /** @var AnnotationReader */
    protected $annotationReader;

    /**
     * @param ManagerRegistry           $registry
     * @param DoctrineEncryptSubscriber $subscriber
     * @param AnnotationReader          $annotationReader
     */
    public function __construct(ManagerRegistry $registry, DoctrineEncryptSubscriber $subscriber, AnnotationReader $annotationReader)
    {
        $this->entityManager = $registry;
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
        $query = $this->entityManager->createQuery(sprintf('SELECT o FROM %s o', $entityName));

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
        $query = $this->entityManager->createQuery(sprintf('SELECT COUNT(o) FROM %s o', $entityName));

        return (int)$query->getSingleScalarResult();
    }

    /**
     * Return an array of entity-metadata for all entities
     * that have at least one encrypted property.
     *
     * @return array
     */
    protected function getEncryptionableEntityMetaData()
    {
        $validMetaData = [];
        $metaDataArray = $this->entityManager->getMetadataFactory()->getAllMetadata();

        foreach ($metaDataArray as $entityMetaData) {
            if ($entityMetaData->isMappedSuperclass) {
                continue;
            }

            $properties = $this->getEncryptionableProperties($entityMetaData);
            if (count($properties) == 0) {
                continue;
            }

            $validMetaData[] = $entityMetaData;
        }

        return $validMetaData;
    }

    /**
     * @param $entityMetaData
     *
     * @return array
     */
    protected function getEncryptionableProperties($entityMetaData)
    {
        //Create reflectionClass for each meta data object
        $reflectionClass = new \ReflectionClass($entityMetaData->name);
        $propertyArray = $reflectionClass->getProperties();
        $properties = [];

        foreach ($propertyArray as $property) {
            if ($this->annotationReader->getPropertyAnnotation($property, 'Shapecode\NYADoctrineEncryptBundle\Configuration\Encrypted')) {
                $properties[] = $property;
            }
        }

        return $properties;
    }
}
