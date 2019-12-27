<?php
namespace PoP\PostsWP\TypeResolverPickers;

use PoP\Content\TypeResolvers\ContentEntityUnionTypeResolver;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;
use PoP\ComponentModel\TypeResolvers\UnionTypeResolverInterface;
use PoP\PostsWP\TypeResolverPickers\ContentEntityTypeResolverPickerInterface;

/**
 * In the context of WordPress, "Content Entities" are all posts (eg: posts, pages, attachments, events, etc)
 * Hence, this class can simply inherit from the Post dataloader, and add the post-types for all required types
 */
class ContentEntityUnionTypeHelpers
{
    /**
     * Obtain the post types from all member typeResolvers
     *
     * @return void
     */
    public static function getTargetTypeResolverPostTypes(string $unionTypeResolverClass)
    {
        $postTypes = [];
        $instanceManager = InstanceManagerFacade::getInstance();
        $unionTypeResolver = $instanceManager->getInstance($unionTypeResolverClass);
        $typeResolverPickers = $unionTypeResolver->getTypeResolverPickers();
        foreach ($typeResolverPickers as $typeResolverPicker) {
            // The picker should implement interface ContentEntityTypeResolverPickerInterface
            if ($typeResolverPicker instanceof ContentEntityTypeResolverPickerInterface) {
                $postTypes[] = $typeResolverPicker->getPostType();
            }
        }
        return $postTypes;
    }
}
