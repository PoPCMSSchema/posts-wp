<?php

declare(strict_types=1);

namespace PoP\PostsWP;

use PoP\Root\Component\AbstractComponent;
use PoP\Root\Component\YAMLServicesTrait;
use PoP\PostsWP\Config\ServiceConfiguration;

/**
 * Initialize component
 */
class Component extends AbstractComponent
{
    use YAMLServicesTrait;
    // const VERSION = '0.1.0';

    public static function getDependedComponentClasses(): array
    {
        return [
            \PoP\Posts\Component::class,
            \PoP\ContentWP\Component::class,
            \PoP\QueriedObjectWP\Component::class,
        ];
    }

    public static function getDependedMigrationPlugins(): array
    {
        return [
            'migrate-posts-wp',
        ];
    }

    /**
     * Initialize services
     */
    protected static function doInitialize(array $configuration = [], bool $skipSchema = false): void
    {
        parent::doInitialize($configuration, $skipSchema);
        self::initYAMLServices(dirname(__DIR__));
        ServiceConfiguration::initialize();
    }
}
