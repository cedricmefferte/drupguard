<?php

namespace App\Entity\Plugin;

use App\Entity\Plugin\Type\Build\Composer;
use App\Repository\Plugin\Build as BuildRepository;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildRepository::class)]
#[ORM\AssociationOverrides([
    new ORM\AssociationOverride(name: 'project', inversedBy: 'buildPlugins'),
])]
#[AppAssert\Plugin\Plugin()]
class Build extends PluginAbstract
{
    #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
    // #[Assert\Valid()]
    protected ?Composer $composer = null;

    public function getComposer(): ?Composer
    {
        return $this->composer;
    }

    public function setComposer(?Composer $composer): static
    {
        $this->composer = $composer;

        return $this;
    }
}
