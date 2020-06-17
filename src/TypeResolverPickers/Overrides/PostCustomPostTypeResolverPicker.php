<?php

declare(strict_types=1);

namespace PoP\PostsWP\TypeResolverPickers\Overrides;

use PoP\Posts\Facades\PostTypeAPIFacade;
use PoP\CustomPostsWP\TypeResolvers\Overrides\CustomPostUnionTypeResolver;
use PoP\CustomPostsWP\TypeResolverPickers\CustomPostTypeResolverPickerInterface;
use PoP\CustomPostsWP\TypeResolverPickers\NoCastCustomPostTypeResolverPickerTrait;

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
        $postTypeAPI = PostTypeAPIFacade::getInstance();
        return $postTypeAPI->getPostCustomPostType();
    }
}
