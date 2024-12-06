<?php

namespace App\ProjectAnalysis\Builder;

use App\Entity\Plugin\Build as BuildEntity;
use App\Entity\Plugin\Type\Build\Composer as ComposerEntity;
use App\Entity\Project;
use App\Form\Plugin\Type\Build\Composer as ComposerForm;
use App\Plugin\Annotation\TypeInfo;
use App\Plugin\Exception\Build as BuildException;
use App\Repository\Plugin\Type\Build\Composer as ComposerRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use function Symfony\Component\Translation\t;
use App\ProjectAnalysis\Builder\AbstractBuilder;

class ComposerBuilder extends AbstractBuilder
{
    public function build(Project $project, mixed $build, string $path) {
        $fileSystem = new Filesystem();

        /**
         * @var \App\Entity\Plugin\Type\Build\Composer $build
         */
        if (!empty($build->getPath())) {
            $path .= $build->getPath();
        }

        if (!$fileSystem->exists($path . '/composer.lock')) {
            throw new BuildException($this->translator->trans('Composer files not found.'));
        }

        $composerBinary = 'composer';
        switch ($build->getVersion()) {
            case 'v1' :
                $composerBinary = 'composer';
                break;
            case 'v2' :
            default :
                break;
        }

        $composerCmd = explode(
            ' ',
            $composerBinary .' install --ignore-platform-reqs --no-scripts --no-plugins --no-cache --no-autoloader --quiet --no-interaction'
        );
        $composerInstall = new Process($composerCmd, $path);
        $composerInstall->setTimeout(60*60);
        if ($composerInstall->run() !== 0) {
            $e = new \Exception($composerInstall->getErrorOutput());
            throw new BuildException($this->translator->trans('Composer install failed.'), $e);
        }

    }
}