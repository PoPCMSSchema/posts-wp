<?php
namespace PoP\PostsWP\TypeAPIs;

use function get_post;
use WP_Post;
use PoP\Posts\TypeAPIs\PostTypeAPIInterface;
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

    /**
     * Get the post with provided ID or, if it doesn't exist, null
     *
     * @param [type] $id
     * @return void
     */
    public function getPost($id)
    {
        $post = get_post($id);
        if (!$post || $post->post_type != 'post') {
            return null;
        }
        return $post;
    }

    /**
     * Indicate if an post with provided ID exists
     *
     * @param [type] $id
     * @return void
     */
    public function postExists($id): bool
    {
        return $this->getPost($id) != null;
    }
}
