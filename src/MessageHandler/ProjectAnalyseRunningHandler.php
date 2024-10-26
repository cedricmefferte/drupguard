<?php

namespace App\MessageHandler;

use App\Entity\Project;
use App\Message\ProjectAnalyseRunning;
use App\ProjectState;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class ProjectAnalyseRunningHandler extends ProjectAnalyseHandlerAbstract
{
    protected LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $bus, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $bus);
        $this->logger = $logger;
    }

    public function __invoke(ProjectAnalyseRunning $message)
    {
        if (empty($message->getProjectId())) {
            return;
        }

        /**
         * @var Project $project
         */
        $project = $this->repository->find($message->getProjectId());
        if (!$project || $project->getState() !== ProjectState::PENDING) {
            return;
        }

        $this->logger->debug('Analyse start ' . $project->getId());
        $project->setState(ProjectState::SOURCING);
        //Do sourcing
        $this->logger->debug('Sourcing ' . $project->getId());
        $project->setState(ProjectState::BUILDING);
        //Do building
        $this->logger->debug('Building ' . $project->getId());
        $project->setState(ProjectState::ANALYSING);
        //Do analysing
        $this->logger->debug('Analysing ' . $project->getId());
        $project->setState(ProjectState::IDLE);
        $this->logger->debug('Analyse end ' . $project->getId());
        //Do analysing

        $this->entityManager->persist($project);
    }
}