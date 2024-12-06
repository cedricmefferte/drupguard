<?php

namespace  App\ProjectAnalysis\Analyzer;

use App\Entity\Plugin\Analyse as AnalyseEntity;
use App\Entity\Plugin\Type\Analyse\Drupal8 as Drupal8Entity;
use App\Entity\Project;
use App\Form\Plugin\Type\Analyse\Drupal8 as Drupal8Form;
use App\Plugin\Annotation\TypeInfo;
//use App\Plugin\Service\Analyse;
use App\Repository\Plugin\Type\Analyse\Drupal8 as Drupal8Repository;
use Symfony\Component\Translation\TranslatableMessage;


class Drupal8Analyzer /*extends Analyse*/
{
    public function analyse(Project $project, mixed $analyse, string $path): mixed {
        return null;
    }
}