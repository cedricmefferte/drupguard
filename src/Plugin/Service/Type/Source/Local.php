<?php

namespace App\Plugin\Service\Type\Source;

use App\Entity\Plugin\Type\Source\Local as LocalEntity;
use App\Entity\Project;
use App\Form\Plugin\Type\Source\Local as LocalForm;
use App\Plugin\Annotation\TypeInfo;
use App\Plugin\Exception\Source as SourceException;
use App\Plugin\Service\Source;
use App\Repository\Plugin\Type\Source\Local as LocalRepository;
use Symfony\Component\Filesystem\Filesystem;
use function Symfony\Component\Translation\t;

#[TypeInfo(
    id: 'local',
    name: 'Local',
    type: 'source',
    entityClass: LocalEntity::class,
    repositoryClass: LocalRepository::class,
    formClass: LocalForm::class
)]
class Local extends Source
{
    public function source(Project $project, mixed $source): string
    {
        /**
         * @var \App\Entity\Plugin\Type\Source\Local $source
         */
        $fileSystem = new Filesystem();
        $path = $source->getPath();
        if (!empty($path) && !$fileSystem->exists($path)) {
            throw new SourceException($this->translator->trans('Path "%path%" not found.', ['path' => $path]));
        }
        return $this->getPath($project, $source);
    }
}