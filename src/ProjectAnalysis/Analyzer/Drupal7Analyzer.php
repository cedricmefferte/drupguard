<?php

namespace App\ProjectAnalysis\Analyzer;

use App\Entity\Plugin\Analyse as AnalyseEntity;
use App\Entity\Plugin\Type\Analyse\Drupal7 as Drupal7Entity;
use App\Entity\Project;
use App\Form\Plugin\Type\Analyse\Drupal7 as Drupal7Form;
use App\Plugin\Annotation\TypeInfo;
use App\ProjectAnalysis\Analyzer\AbstractAnalyzer;
//use App\Repository\Plugin\Type\Analyse\Drupal7 as Drupal7Repository;
use Symfony\Component\Translation\TranslatableMessage;


class Drupal7Analyzer extends AbstractAnalyzer
{
    public function analyse(Project $project, mixed $analyse, string $path): mixed {
        return null;
    }
}