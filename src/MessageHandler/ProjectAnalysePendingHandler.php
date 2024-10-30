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
        if (!$project || $project->getState() !== ProjectState::IDLE) {
            return;
        }

        $project->setState(ProjectState::PENDING);
        $this->entityManager->persist($project);
        $this->entityManager->flush();
        $event = new ProjectAnalyseRunning($project->getId());
        $this->bus->dispatch(
            // Be sure sql transaction end before sending new message
            (new Envelope($event))->with(new DispatchAfterCurrentBusStamp())
        );
    }
}