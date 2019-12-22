<?php
namespace PoP\PostsWP\TypeResolverPickers;

trait NoCastContentEntityTypeResolverPickerTrait
{
    /**
     * Do not cast the object of type `WP_Post` returned by function `get_posts`, since it already satisfies this Type too (eg: locationPost)
     *
     * @param [type] $post
     * @return void
     */
    public function maybeCast($post)
    {
        return $post;
    }
}
