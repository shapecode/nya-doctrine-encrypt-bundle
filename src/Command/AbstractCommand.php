<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\Command;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use ReflectionClass;
use ReflectionProperty;
use Shapecode\NYADoctrineEncryptBundle\Configuration\Encrypted;
use Symfony\Component\Console\Command\Command;
use function count;
use function sprintf;

abstract class AbstractCommand extends Command
{
    /** @var ManagerRegistry */
    protected $registry;

    /** @var Reader */
    protected $annotationReader;

    protected function getEntityIterator(string $entityName) : IterableResult
    {
        /** @var EntityManagerInterface $manager */
        $manager = $this->registry->getManager();

        $query = $manager->createQuery(sprintf('SELECT o FROM %s o', $entityName));

        return $query->iterate();
    }

    protected function getTableCount(string $entityName) : int
    {
        /** @var EntityManagerInterface $manager */
        $manager = $this->registry->getManager();

        $query = $manager->createQuery(sprintf('SELECT COUNT(o) FROM %s o', $entityName));

        return (int) $query->getSingleScalarResult();
    }

    /**
     * @return ClassMetadataInfo[]|array
     */
    protected function getEncryptionableEntityMetaData() : array
    {
        $validMetaData = [];

        /** @var ClassMetadataInfo[] $metaDataArray */
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
     * @return array|ReflectionProperty[]
     */
    protected function getEncryptionableProperties(ClassMetadata $entityMetaData) : array
    {
        //Create reflectionClass for each meta data object
        $reflectionClass = new ReflectionClass($entityMetaData->getName());
        $propertyArray   = $reflectionClass->getProperties();
        $properties      = [];

        foreach ($propertyArray as $property) {
            if ($this->annotationReader->getPropertyAnnotation($property, Encrypted::class) !== null) {
                continue;
            }

            $properties[] = $property;
        }

        return $properties;
    }
}
