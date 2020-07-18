<?php

declare(strict_types=1);

namespace PoP\PostsWP\TypeAPIs;

use WP_Post;
use function get_post;

use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\Posts\ComponentConfiguration;
use PoP\Posts\TypeAPIs\PostTypeAPIInterface;
use PoP\CustomPostsWP\TypeAPIs\CustomPostTypeAPI;

/**
 * Methods to interact with the Type, to be implemented by the underlying CMS
 */
class PostTypeAPI extends CustomPostTypeAPI implements PostTypeAPIInterface
{
    /**
     * Add an extra hook just to modify posts
     *
     * @param [type] $query
     * @param array $options
     * @return array
     */
    protected function convertCustomPostsQuery(array $query, array $options = []): array
    {
        $query = parent::convertCustomPostsQuery($query, $options);
        return HooksAPIFacade::getInstance()->applyFilters(
            'CMSAPI:posts:query',
            $query,
            $options
        );
    }

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
    public function getPost($id): ?object
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

    /**
     * Limit of how many custom posts can be retrieved in the query.
     * Override this value for specific custom post types
     *
     * @return integer
     */
    protected function getCustomPostListMaxLimit(): int
    {
        return ComponentConfiguration::getPostListMaxLimit();
    }

    public function getPosts(array $query, array $options = []): array
    {
        $query['custom-post-types'] = ['post'];
        return $this->getCustomPosts($query, $options);
    }
    public function getPostCount(array $query = [], array $options = []): int
    {
        $query['custom-post-types'] = ['post'];
        return $this->getCustomPostCount($query, $options);
    }
    public function getPostCustomPostType(): string
    {
        return 'post';
    }
}
