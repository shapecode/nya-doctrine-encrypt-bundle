<?php

declare(strict_types=1);

namespace Shapecode\NYADoctrineEncryptBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Shapecode\NYADoctrineEncryptBundle\Encryption\EntityEncryptionInterface;

final class DoctrineEncryptSubscriber implements EventSubscriber
{
    /** @var EntityEncryptionInterface */
    protected $handler;

    /** @var bool */
    protected $enable = true;

    public function __construct(EntityEncryptionInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @inheritdoc
     */
    public function getSubscribedEvents() : array
    {
        return [
            Events::postUpdate,
            Events::preUpdate,
            Events::postLoad,
            Events::preFlush,
            Events::postFlush,
        ];
    }

    public function isEnable() : bool
    {
        return $this->enable;
    }

    public function setEnable(bool $enable) : void
    {
        $this->enable = $enable;
    }

    public function postUpdate(LifecycleEventArgs $args) : void
    {
        if ($this->isEnable() === false) {
            return;
        }

        $entity = $args->getEntity();
        $this->handler->decrypt($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args) : void
    {
        if ($this->isEnable() === false) {
            return;
        }

        $entity = $args->getEntity();
        $this->handler->encrypt($entity);
    }

    public function postLoad(LifecycleEventArgs $args) : void
    {
        if ($this->isEnable() === false) {
            return;
        }

        $entity = $args->getEntity();
        $this->handler->decrypt($entity);
    }

    public function preFlush(PreFlushEventArgs $preFlushEventArgs) : void
    {
        if ($this->isEnable() === false) {
            return;
        }

        $unitOfWork = $preFlushEventArgs->getEntityManager()->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            $this->handler->encrypt($entity);
        }
    }

    public function postFlush(PostFlushEventArgs $postFlushEventArgs) : void
    {
        if ($this->isEnable() === false) {
            return;
        }

        $unitOfWork = $postFlushEventArgs->getEntityManager()->getUnitOfWork();

        foreach ($unitOfWork->getIdentityMap() as $entityMap) {
            foreach ($entityMap as $entity) {
                $this->handler->decrypt($entity);
            }
        }
    }
}
