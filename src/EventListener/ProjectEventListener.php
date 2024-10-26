<?php

namespace App\EventListener;

use App\Entity\Project;
use App\Service\SchedulerUpdateService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\Cache\Adapter\PdoAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Contracts\Service\Attribute\Required;

#[AsEventListener(event: AfterEntityDeletedEvent::class, method: 'onEntityDeletePersistedEvent')]
#[AsEventListener(event: AfterEntityPersistedEvent::class, method: 'onEntityDeletePersistedEvent')]
#[AsEventListener(event: BeforeEntityUpdatedEvent::class, method: 'onEntityUpdatedEvent')]
final class ProjectEventListener
{
    private SchedulerUpdateService $schedulerUpdateService;

    public function __construct(SchedulerUpdateService $schedulerUpdateService) {
        $this->schedulerUpdateService = $schedulerUpdateService;
    }

    public function onEntityDeletePersistedEvent(AfterEntityDeletedEvent $event) {
        $entity = $event->getEntityInstance();
        if (!$entity instanceof Project) {
            return;
        }
        $this->schedulerUpdateService->append($entity);
    }

    public function onEntityUpdatedEvent(BeforeEntityUpdatedEvent $event) {
        $entity = $event->getEntityInstance();
        if (!$entity instanceof Project) {
            return;
        }
        $this->schedulerUpdateService->append($entity, true);
    }
}