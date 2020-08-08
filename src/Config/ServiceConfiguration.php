<?php

declare(strict_types=1);

namespace PoPSchema\PostsWP\Config;

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
            \PoPSchema\Posts\TypeResolverPickers\Optional\PostCustomPostTypeResolverPicker::class,
            \PoPSchema\PostsWP\TypeResolverPickers\Overrides\PostCustomPostTypeResolverPicker::class
        );
    }
}
