<?php

namespace App\Entity\Plugin\Type\Analyse;

use App\Entity\Plugin\Type\PathTypeAbstract;
use App\Repository\Plugin\Type\Analyse\Drupal8 as Drupal8Repository;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'analyse_drupal8')]
#[ORM\Entity(repositoryClass: Drupal8Repository::class)]
#[AppAssert\Plugin\Path(checkPathFileSystem: false)]
class Drupal8 extends PathTypeAbstract
{
    public function __toString()
    {
        return 'Drupal 8+'.parent::__toString();
    }
}
