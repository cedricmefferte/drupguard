<?php

namespace App\ProjectAnalysis\Analyzer;

use App\Entity\Plugin\Analyse as AnalyseEntity;
use App\Entity\Project;
use App\Form\Plugin\Analyse as AnalyseForm;
use App\Plugin\Annotation\PluginInfo;
use App\Repository\Plugin\Analyse as AnalyseRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractAnalyzer
{
    abstract public function analyse(Project $project, mixed $analyse, string $path): mixed;
}