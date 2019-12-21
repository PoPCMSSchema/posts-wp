<?php
namespace PoP\PostsWP\Config;

use PoP\Root\Component\PHPServiceConfigurationTrait;
use PoP\ComponentModel\Container\ContainerBuilderUtils;

class ServiceConfiguration
{
    use PHPServiceConfigurationTrait;

    protected static function configure()
    {
        ContainerBuilderUtils::injectValuesIntoService(
            'instance_manager',
            'overrideClass',
            \PoP\Content\TypeDataLoaders\ContentEntityUnionTypeDataLoader::class,
            \PoP\PostsWP\TypeDataLoaders\Overrides\ContentEntityUnionTypeDataLoader::class
        );
    }
}
