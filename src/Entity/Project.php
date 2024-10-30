<?php

namespace App\Entity;

use App\Entity\Plugin\Analyse;
use App\Entity\Plugin\Build;
use App\Entity\Plugin\Source;
use App\AnalyseLevelState;
use App\ProjectState;
use App\Repository\ProjectRepository;
use App\Security\ProjectRoles;
use App\Validator as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_MACHINENAME', fields: ['machine_name'])]
#[UniqueEntity(fields: ['machine_name'], message: 'There is already a project with this machine name')]
#[AppAssert\Plugin\ProjectDependencies()]
#[AppAssert\ProjectOwner()]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(pattern: '/^[a-z0-9_]+$/i')]
    private ?string $machine_name = null;

    #[ORM\Column]
    private ?bool $isPublic = null;

    /**
     * @var Collection<int, ProjectMember>
     */
    #[ORM\OneToMany(targetEntity: ProjectMember::class, mappedBy: 'project', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Valid()]
    private Collection $projectMembers;

    /**
     * @var Collection<int, Source>
     */
    #[ORM\OneToMany(targetEntity: Source::class, mappedBy: 'project', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Count(max: 1)]
    #[Assert\Valid()]
    private Collection $sourcePlugins;

    /**
     * @var Collection<int, Build>
     */
    #[ORM\OneToMany(targetEntity: Build::class, mappedBy: 'project', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Valid()]
    private Collection $buildPlugins;

    /**
     * @var Collection<int, Analyse>
     */
    #[ORM\OneToMany(targetEntity: Analyse::class, mappedBy: 'project', cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Count(min: 1)]
    #[Assert\Valid()]
    private Collection $analysePlugins;

    #[ORM\Column(type: 'integer', enumType: ProjectState::class)]
    private ProjectState $state = ProjectState::IDLE;

    #[ORM\Column(length: 255, nullable: true)]
    #[AppAssert\CronExpression()]
    private ?string $periodicity = null;

    #[ORM\Column(type: 'integer', enumType: AnalyseLevelState::class)]
    private AnalyseLevelState $emailLevel = AnalyseLevelState::NONE;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $emailExtra = null;

    #[ORM\OneToOne()]
    private ?Report $lastReport = null;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'project', orphanRemoval: true)]
    private Collection $reports;

    public function __construct()
    {
        $this->projectMembers = new ArrayCollection();
        $this->sourcePlugins = new ArrayCollection();
        $this->buildPlugins = new ArrayCollection();
        $this->analysePlugins = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMachineName(): ?string
    {
        return $this->machine_name;
    }

    public function setMachineName(string $machineName): static
    {
        $this->machine_name = $machineName;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * @return Collection<int, ProjectMember>
     */
    public function getProjectMembers(): Collection
    {
        return $this->projectMembers;
    }

    public function addProjectMember(ProjectMember $projectMember): static
    {
        if (!$this->projectMembers->contains($projectMember)) {
            $this->projectMembers->add($projectMember);
            $projectMember->setProject($this);
        }

        return $this;
    }

    public function removeProjectMember(ProjectMember $projectMember): static
    {
        if ($this->projectMembers->removeElement($projectMember)) {
            // set the owning side to null (unless already changed)
            if ($projectMember->getProject() === $this) {
                $projectMember->setProject(null);
            }
        }

        return $this;
    }

    public function hasOwner(?ProjectMember $excludedProjectMember = null): bool
    {
        if (null === $this->getProjectMembers()) {
            return false;
        }

        $owner = false;
        foreach ($this->getProjectMembers() as $projectMember) {
            if (
                ProjectRoles::OWNER === $projectMember->getRole()
                && (!$excludedProjectMember || ($projectMember->getId() !== $excludedProjectMember->getId()))
            ) {
                $owner = true;
                break;
            }
        }

        return $owner;
    }

    /**
     * @return Collection<int, Source>
     */
    public function getSourcePlugins(): Collection
    {
        return $this->sourcePlugins;
    }

    public function addSourcePlugin(Source $sourcePlugin): static
    {
        if (!$this->sourcePlugins->contains($sourcePlugin)) {
            $this->sourcePlugins->add($sourcePlugin);
            $sourcePlugin->setProject($this);
        }

        return $this;
    }

    public function removeSourcePlugin(Source $sourcePlugin): static
    {
        if ($this->sourcePlugins->removeElement($sourcePlugin)) {
            // set the owning side to null (unless already changed)
            if ($sourcePlugin->getProject() === $this) {
                $sourcePlugin->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Build>
     */
    public function getBuildPlugins(): Collection
    {
        return $this->buildPlugins;
    }

    public function addBuildPlugin(Build $buildPlugins): static
    {
        if (!$this->buildPlugins->contains($buildPlugins)) {
            $this->buildPlugins->add($buildPlugins);
            $buildPlugins->setProject($this);
        }

        return $this;
    }

    public function removeBuildPlugin(Build $buildPlugins): static
    {
        if ($this->buildPlugins->removeElement($buildPlugins)) {
            // set the owning side to null (unless already changed)
            if ($buildPlugins->getProject() === $this) {
                $buildPlugins->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Analyse>
     */
    public function getAnalysePlugins(): Collection
    {
        return $this->analysePlugins;
    }

    public function addAnalysePlugin(Analyse $analysePlugin): static
    {
        if (!$this->analysePlugins->contains($analysePlugin)) {
            $this->analysePlugins->add($analysePlugin);
            $analysePlugin->setProject($this);
        }

        return $this;
    }

    public function removeAnalysePlugin(Analyse $analysePlugin): static
    {
        if ($this->analysePlugins->removeElement($analysePlugin)) {
            // set the owning side to null (unless already changed)
            if ($analysePlugin->getProject() === $this) {
                $analysePlugin->setProject(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getState(): ProjectState
    {
        return $this->state;
    }

    public function setState(ProjectState $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function isRunning(): bool
    {
        return $this->state !== ProjectState::IDLE;
    }

    public function getPeriodicity(): ?string
    {
        return $this->periodicity;
    }

    public function setPeriodicity(?string $periodicity): static
    {
        $this->periodicity = $periodicity;

        return $this;
    }

    public function getEmailLevel(): ?AnalyseLevelState
    {
        return $this->emailLevel;
    }

    public function setEmailLevel(AnalyseLevelState $emailLevel): static
    {
        $this->emailLevel = $emailLevel;

        return $this;
    }

    public function getEmailExtra(): ?string
    {
        return $this->emailExtra;
    }

    public function setEmailExtra(?string $emailExtra): static
    {
        $this->emailExtra = $emailExtra;

        return $this;
    }

    public function getLastReport(): ?Report
    {
        return $this->lastReport;
    }

    public function setLastReport(?Report $lastReport): static
    {
        $this->lastReport = $lastReport;

        return $this;
    }

    /**
     * @return Collection<int, Report>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setProject($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getProject() === $this) {
                $report->setProject(null);
            }
        }

        return $this;
    }
}
