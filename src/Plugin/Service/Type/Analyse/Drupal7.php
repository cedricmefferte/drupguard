<?php

namespace App\Plugin\Service\Type\Analyse;

use App\Entity\Plugin\Analyse as AnalyseEntity;
use App\Entity\Plugin\Type\Analyse\Drupal7 as Drupal7Entity;
use App\Entity\Project;
use App\Form\Plugin\Type\Analyse\Drupal7 as Drupal7Form;
use App\Plugin\Annotation\TypeInfo;
use App\Plugin\Service\Analyse;
use App\Repository\Plugin\Type\Analyse\Drupal7 as Drupal7Repository;
use Symfony\Component\Translation\TranslatableMessage;

#[TypeInfo(
    id: 'drupal7',
    name: 'Drupal 7',
    type: 'analyse',
    entityClass: Drupal7Entity::class,
    repositoryClass: Drupal7Repository::class,
    formClass: Drupal7Form::class,
    help: new TranslatableMessage('Drupal security releases happen between 16:00 UTC and 22:00 UTC every Wednesday, so the value <em>0 5 * * 4</em> is suggested.'),
    dependencies: [
        'source' => '*',
    ]
)]
class Drupal7 extends Analyse
{
    public function analyse(Project $project, mixed $analyse, string $path): mixed {

    }
}