<?php

namespace Shapecode\NYADoctrineEncryptBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Shapecode\NYADoctrineEncryptBundle\Encryption\EncryptionHandlerInterface;

/**
 * Class DoctrineEncryptSubscriber
 *
 * @package Shapecode\NYADoctrineEncryptBundle\EventListener
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class DoctrineEncryptSubscriber implements EventSubscriber
{

    /** @var EncryptionHandlerInterface */
    protected $handler;

    /**
     * @param EncryptionHandlerInterface $handler
     */
    public function __construct(EncryptionHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @inheritdoc
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
        $this->handler->processFields($entity, false);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->handler->processFields($entity);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->handler->processFields($entity, false);
    }

    /**
     * @param PreFlushEventArgs $preFlushEventArgs
     */
    public function preFlush(PreFlushEventArgs $preFlushEventArgs)
    {
        $unitOfWork = $preFlushEventArgs->getEntityManager()->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            $this->handler->processFields($entity);
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
                $this->handler->processFields($entity, false);
            }
        }
    }
}
