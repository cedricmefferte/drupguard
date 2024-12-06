<?php

namespace App\MessageHandler;

use App\Message\ProjectAnalysePending;
use App\Message\ProjectAnalyseRunning;
use App\ProjectState;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[AsMessageHandler]
class ProjectAnalysePendingHandler extends ProjectAnalyseHandlerAbstract {

    public function __invoke(ProjectAnalysePending $message)
    {
        if (empty($message->getProjectId())) {
            return;
        }

        $project = $this->repository->find($message->getProjectId());

        // TODO: forward responsability to ProjectStateManager => $projectStateManager->stateIs(ProjectState::IDLE)
        if (!$project || $project->getState() !== ProjectState::IDLE) {
            return;
        }

        // TODO: forward responsability to ProjectStateManager
        $project->setState(ProjectState::PENDING);

        $this->entityManager->persist($project);
        $this->entityManager->flush();

        // TODO: keep only  this process here
        $event = new ProjectAnalyseRunning($project->getId());
        $this->bus->dispatch(
            // Be sure sql transaction end before sending new message
            (new Envelope($event))->with(new DispatchAfterCurrentBusStamp())
        );
    }
}