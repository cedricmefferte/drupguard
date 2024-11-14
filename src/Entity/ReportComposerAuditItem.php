<?php

namespace App\Entity;

use App\AnalyseLevelState;
use App\Repository\ReportComposerAuditItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportComposerAuditItemRepository::class)]
class ReportComposerAuditItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $currentVersion = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $detail = null;

    #[ORM\Column(enumType: AnalyseLevelState::class)]
    private ?AnalyseLevelState $state = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ReportComposerAudit $reportComposerAudit = null;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCurrentVersion(): ?string
    {
        return $this->currentVersion;
    }

    public function setCurrentVersion(string $currentVersion): static
    {
        $this->currentVersion = $currentVersion;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    public function getState(): ?AnalyseLevelState
    {
        return $this->state;
    }

    public function setState(AnalyseLevelState $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getReportComposerAudit(): ?ReportComposerAudit
    {
        return $this->reportComposerAudit;
    }

    public function setReportComposerAudit(?ReportComposerAudit $reportComposerAudit): static
    {
        $this->reportComposerAudit = $reportComposerAudit;

        return $this;
    }
}
