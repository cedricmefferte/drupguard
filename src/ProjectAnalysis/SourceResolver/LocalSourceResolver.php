<?php

namespace App\ProjectAnalysis\SourceResolver;

use App\Entity\Plugin\Type\Source\Local as LocalEntity;
use App\Entity\Project;
use App\Form\Plugin\Type\Source\Local as LocalForm;
use App\Plugin\Annotation\TypeInfo;
use App\Plugin\Exception\Source as SourceException;
use App\ProjectAnalysis\SourceResolver\AbstractSourceResolver;
use App\Repository\Plugin\Type\Source\Local as LocalRepository;
use Symfony\Component\Filesystem\Filesystem;
use function Symfony\Component\Translation\t;


class LocalSourceResolver extends AbstractSourceResolver
{
    public function resolveSource(Project $project, mixed $source): string
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