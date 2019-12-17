<?php
namespace PoP\PostsWP\TypeAPIs;

use WP_Post;
/**
 * Methods to interact with the Type, to be implemented by the underlying CMS
 */
class PostTypeAPI implements PostTypeAPIInterface
{
    /**
     * Indicates if the passed object is of type Post
     *
     * @param [type] $object
     * @return boolean
     */
    public function isInstanceOfPostType($object): bool
    {
        return ($object instanceof WP_Post) && $object->post_type == 'post';
    }
}
