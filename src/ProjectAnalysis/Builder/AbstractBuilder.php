<?php

namespace App\ProjectAnalysis\Builder;

use App\Entity\Plugin\Build as BuildEntity;
use App\Entity\Project;
use App\Form\Plugin\Build as BuildForm;
use App\Plugin\Annotation\PluginInfo;
use App\Repository\Plugin\Build as BuildRepository;

abstract class AbstractBuilder
{
    abstract public function build(Project $project, mixed $build, string $path);
}