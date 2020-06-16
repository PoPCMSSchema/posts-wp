<?php

declare(strict_types=1);

namespace PoP\PostsWP\TypeResolverPickers\Overrides;

use PoP\PostsWP\TypeResolvers\Overrides\CustomPostUnionTypeResolver;
use PoP\CustomPostsWP\TypeResolverPickers\CustomPostTypeResolverPickerInterface;
use PoP\PostsWP\TypeResolverPickers\NoCastCustomPostTypeResolverPickerTrait;

class PostCustomPostTypeResolverPicker extends \PoP\Posts\TypeResolverPickers\Optional\PostCustomPostTypeResolverPicker implements CustomPostTypeResolverPickerInterface
{
    use NoCastCustomPostTypeResolverPickerTrait;

    public static function getClassesToAttachTo(): array
    {
        return [
            CustomPostUnionTypeResolver::class,
        ];
    }

    public function getCustomPostType(): string
    {
        return 'post';
    }
}
