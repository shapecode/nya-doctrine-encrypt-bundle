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

    /** @var bool */
    protected $enable = true;

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
     * @return bool
     */
    public function isEnable()
    {
        return $this->enable;
    }

    /**
     * @param bool $enable
     */
    public function setEnable($enable)
    {
        $this->enable = (bool)$enable;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        if ($this->isEnable() === false) {
            return;
        }

        $entity = $args->getEntity();
        $this->handler->processFields($entity, false);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if ($this->isEnable() === false) {
            return;
        }

        $entity = $args->getEntity();
        $this->handler->processFields($entity);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        if ($this->isEnable() === false) {
            return;
        }

        $entity = $args->getEntity();
        $this->handler->processFields($entity, false);
    }

    /**
     * @param PreFlushEventArgs $preFlushEventArgs
     */
    public function preFlush(PreFlushEventArgs $preFlushEventArgs)
    {
        if ($this->isEnable() === false) {
            return;
        }

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
        if ($this->isEnable() === false) {
            return;
        }

        $unitOfWork = $postFlushEventArgs->getEntityManager()->getUnitOfWork();

        foreach ($unitOfWork->getIdentityMap() as $entityMap) {
            foreach ($entityMap as $entity) {
                $this->handler->processFields($entity, false);
            }
        }
    }
}
