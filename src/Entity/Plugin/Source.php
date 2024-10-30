<?php

namespace App\Entity\Plugin;

use App\Entity\Plugin\Type\Source\Git;
use App\Entity\Plugin\Type\Source\Local;
use App\Repository\Plugin\Source as SourceRepository;
use App\Validator as AppAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SourceRepository::class)]
#[ORM\AssociationOverrides([
    new ORM\AssociationOverride(name: 'project', inversedBy: 'sourcePlugins'),
])]
#[AppAssert\Plugin\Plugin()]
class Source extends PluginAbstract
{
    #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
    // #[Assert\Valid()]
    protected ?Local $local = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], orphanRemoval: true)]
    // #[Assert\Valid()]
    protected ?Git $git = null;

    public function getLocal(): ?Local
    {
        return $this->local;
    }

    public function setLocal(?Local $local): static
    {
        $this->local = $local;

        return $this;
    }

    public function getGit(): ?Git
    {
        return $this->git;
    }

    public function setGit(?Git $git): static
    {
        $this->git = $git;

        return $this;
    }
}
