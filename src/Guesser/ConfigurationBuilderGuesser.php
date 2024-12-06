<?php

namespace App\Guesser;

use App\DependencyInjection\Compiler\ConfigurationBuilderChain;
use App\Guesser\Exception\GuessException;

/**
 *
 * TODO: decide if projectAnalysis builder chain is necessary
 *
 * Class ConfigurationBuilderGuesser.
 */
class ConfigurationBuilderGuesser
{
    /** @var ConfigurationBuilderChain */
    protected $configurationBuilderChain;

    /**
     * @param ConfigurationBuilderChain $builder
     */
    public function __construct(
        ConfigurationBuilderChain $ConfigurationBuilderChain
    ) {
        $this->configurationBuilderChain = $ConfigurationBuilderChain;
    }

    /**
     * @param $type
     * @param $normalizeType
     *
     * @return mixed
     *
     * @throws GuessException
     */
    public function guessBuilder($type = '')
    {
        $builder = $this->configurationBuilderChain->getBuilder(
            $type
        );
        if (\is_null($builder)) {
            throw new GuessException(
                'Type de plugin non géré: '.$type
            );
        }

        return $builder;
    }
}
