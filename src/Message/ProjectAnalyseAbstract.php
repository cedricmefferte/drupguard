<?php

namespace App\Message;

// TODO: rename Analyse to Analyze
abstract class ProjectAnalyseAbstract implements \Stringable
{
    private int $projectId;

    public function __construct(
        int $projectId,
    ) {
        $this->projectId = $projectId;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function __toString(): string
    {
        return sprintf('with project %s', $this->projectId);
    }
}