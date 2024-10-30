<?php

namespace App\Entity\Plugin\Type\Analyse;

use App\Entity\Plugin\Type\PathTypeAbstract;
use App\Repository\Plugin\Type\Analyse\ComposerAudit as ComposerAuditRepository;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'analyse_composer_audit')]
#[ORM\Entity(repositoryClass: ComposerAuditRepository::class)]
#[AppAssert\Plugin\Path(checkPathFileSystem: false)]
class ComposerAudit extends PathTypeAbstract
{
    public function __toString()
    {
        return 'Composer audit'.parent::__toString();
    }
}
