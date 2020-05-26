<?php

declare(strict_types=1);

namespace PoP\PostsWP\Config;

use PoP\Root\Component\PHPServiceConfigurationTrait;
use PoP\ComponentModel\Container\ContainerBuilderUtils;

class ServiceConfiguration
{
    use PHPServiceConfigurationTrait;

    protected static function configure(): void
    {
        ContainerBuilderUtils::injectValuesIntoService(
            'instance_manager',
            'overrideClass',
            \PoP\Content\TypeDataLoaders\ContentEntityUnionTypeDataLoader::class,
            \PoP\PostsWP\TypeDataLoaders\Overrides\ContentEntityUnionTypeDataLoader::class
        );

        ContainerBuilderUtils::injectValuesIntoService(
            'instance_manager',
            'overrideClass',
            \PoP\Content\TypeResolvers\ContentEntityUnionTypeResolver::class,
            \PoP\PostsWP\TypeResolvers\Overrides\ContentEntityUnionTypeResolver::class
        );

        ContainerBuilderUtils::injectValuesIntoService(
            'instance_manager',
            'overrideClass',
            \PoP\Posts\TypeResolverPickers\Optional\PostContentEntityTypeResolverPicker::class,
            \PoP\PostsWP\TypeResolverPickers\Overrides\PostContentEntityTypeResolverPicker::class
        );
    }
}
