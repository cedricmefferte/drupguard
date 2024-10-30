<?php

namespace App\Entity\Plugin\Type\Analyse;

use App\Entity\Plugin\Type\PathTypeAbstract;
use App\Repository\Plugin\Type\Analyse\Drupal7 as Drupal7Repository;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'analyse_drupal7')]
#[ORM\Entity(repositoryClass: Drupal7Repository::class)]
#[AppAssert\Plugin\Path(checkPathFileSystem: false)]
class Drupal7 extends PathTypeAbstract
{
    public function __toString()
    {
        return 'Drupal 7'.parent::__toString();
    }
}
