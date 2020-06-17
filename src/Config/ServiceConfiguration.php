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
            \PoP\Posts\TypeResolverPickers\Optional\PostCustomPostTypeResolverPicker::class,
            \PoP\PostsWP\TypeResolverPickers\Overrides\PostCustomPostTypeResolverPicker::class
        );
    }
}
