<?php

declare(strict_types=1);

namespace PoP\PostsWP\TypeAPIs;

use PoP\Posts\TypeAPIs\PostTypeAPIInterface;

use WP_Post;
use function get_post;
use function apply_filters;
use PoP\CustomPosts\Types\Status;

/**
 * Methods to interact with the Type, to be implemented by the underlying CMS
 */
class PostTypeAPI extends \PoP\CustomPostsWP\TypeAPIs\CustomPostTypeAPI implements PostTypeAPIInterface
{
    /**
     * Indicates if the passed object is of type Post
     *
     * @param object $object
     * @return boolean
     */
    public function isInstanceOfPostType($object): bool
    {
        return ($object instanceof WP_Post) && $object->post_type == 'post';
    }

    /**
     * Get the post with provided ID or, if it doesn't exist, null
     *
     * @param int $id
     * @return void
     */
    protected function getPost($id): ?object
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
     * @param int $id
     * @return void
     */
    public function postExists($id): bool
    {
        return $this->getPost($id) != null;
    }

    public function getExcerptMore()
    {

        return apply_filters('excerpt_more', ' ' . '[&hellip;]');
    }

    public function getExcerptLength()
    {
        return apply_filters('excerpt_length', 55);
    }
    public function getPublishedDate($postObjectOrID): ?string
    {
        list(
            $post,
            $postID,
        ) = $this->getCustomPostObjectAndID($postObjectOrID);
        return $post->post_date;
    }

    public function getPosts($query, array $options = []): array
    {
        $query['post-types'] = ['post'];
        return $this->getCustomPosts($query, $options);
    }
    public function getPostCount(array $query = [], array $options = []): int
    {
        $query['post-types'] = ['post'];
        return $this->getCustomPostCount($query, $options);
    }
}
