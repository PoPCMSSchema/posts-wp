<?php

declare(strict_types=1);

namespace PoP\PostsWP\TypeDataLoaders\Overrides;

use PoP\Posts\TypeDataLoaders\PostTypeDataLoader;
use PoP\Content\TypeResolvers\CustomPostUnionTypeResolver;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;
use PoP\ContentWP\TypeResolverPickers\CustomPostUnionTypeHelpers;
use PoP\ContentWP\TypeResolverPickers\CustomPostTypeResolverPickerInterface;

/**
 * In the context of WordPress, "Content Entities" are all posts (eg: posts, pages, attachments, events, etc)
 * Hence, this class can simply inherit from the Post dataloader, and add the post-types for all required types
 */
class CustomPostUnionTypeDataLoader extends PostTypeDataLoader
{
    public function getObjectQuery(array $ids): array
    {
        $query = parent::getObjectQuery($ids);

        // From all post types from the member typeResolvers
        $query['post-types'] = CustomPostUnionTypeHelpers::getTargetTypeResolverPostTypes(CustomPostUnionTypeResolver::class);

        return $query;
    }

    public function getDataFromIdsQuery(array $ids): array
    {
        $query = parent::getDataFromIdsQuery($ids);

        // From all post types from the member typeResolvers
        $query['post-types'] = CustomPostUnionTypeHelpers::getTargetTypeResolverPostTypes(CustomPostUnionTypeResolver::class);

        return $query;
    }

    public function getObjects(array $ids): array
    {
        $posts = parent::getObjects($ids);

        // After executing `get_posts` it returns a list of posts, without converting the object to its own post type
        // Cast the posts to their own classes (eg: event)
        $instanceManager = InstanceManagerFacade::getInstance();
        $postUnionTypeResolver =  $instanceManager->getInstance(CustomPostUnionTypeResolver::class);
        $posts = array_map(
            function ($post) use ($postUnionTypeResolver) {
                $targetTypeResolverPicker = $postUnionTypeResolver->getTargetTypeResolverPicker($post);
                if (is_null($targetTypeResolverPicker)) {
                    return $post;
                }
                if ($targetTypeResolverPicker instanceof CustomPostTypeResolverPickerInterface) {
                    // Cast object, eg: from post to event
                    return $targetTypeResolverPicker->maybeCast($post);
                }
                return $post;
            },
            $posts
        );
        return $posts;
    }
}
