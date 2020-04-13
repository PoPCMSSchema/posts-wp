<?php

declare(strict_types=1);

namespace PoP\PostsWP\TypeResolverPickers\Overrides;

use PoP\PostsWP\TypeResolvers\Overrides\ContentEntityUnionTypeResolver;
use PoP\PostsWP\TypeResolverPickers\ContentEntityTypeResolverPickerInterface;
use PoP\PostsWP\TypeResolverPickers\NoCastContentEntityTypeResolverPickerTrait;

class PostContentEntityTypeResolverPicker extends \PoP\Posts\TypeResolverPickers\Optional\PostContentEntityTypeResolverPicker implements ContentEntityTypeResolverPickerInterface
{
    use NoCastContentEntityTypeResolverPickerTrait;

    public static function getClassesToAttachTo(): array
    {
        return [
            ContentEntityUnionTypeResolver::class,
        ];
    }

    public function getPostType(): string
    {
        return 'post';
    }
}
