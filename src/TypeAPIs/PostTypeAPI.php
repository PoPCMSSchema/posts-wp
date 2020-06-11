<?php

declare(strict_types=1);

namespace PoP\PostsWP\TypeAPIs;

use PoP\Posts\TypeAPIs\PostTypeAPIInterface;

use WP_Post;
use function get_post;
use function apply_filters;
use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\CustomPosts\Types\Status;

/**
 * Methods to interact with the Type, to be implemented by the underlying CMS
 */
class PostTypeAPI extends \PoP\CustomPostsWP\TypeAPIs\PostTypeAPI implements PostTypeAPIInterface
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

    public function getPostType($post)
    {
        return \get_post_type($post);
    }

    public function getSlug($postObjectOrID): ?string
    {
        if ($this->getStatus($postObjectOrID) == Status::PUBLISHED) {
            $post = $this->getPost($postObjectOrID);
            return $post->post_name;
        }

        // Function get_sample_permalink comes from the file below, so it must be included
        // Code below copied from `function get_sample_permalink_html`
        include_once ABSPATH . 'wp-admin/includes/post.php';
        list($permalink, $post_name) = \get_sample_permalink($postObjectOrID, null, null);
        return $post_name;
    }

    public function getBasicPostContent($post_id)
    {
        $post = $this->getPost($post_id);

        // Basic content: remove embeds, shortcodes, and tags
        // Remove the embed functionality, and then add again
        $wp_embed = $GLOBALS['wp_embed'];
        HooksAPIFacade::getInstance()->removeFilter('the_content', array( $wp_embed, 'autoembed' ), 8);

        // Do not allow HTML tags or shortcodes
        $ret = \strip_shortcodes($post->post_content);
        $ret = HooksAPIFacade::getInstance()->applyFilters('the_content', $ret);
        HooksAPIFacade::getInstance()->addFilter('the_content', array( $wp_embed, 'autoembed' ), 8);

        return strip_tags($ret);
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
        ) = $this->getPostObjectAndID($postObjectOrID);
        return $post->post_date;
    }
    public function getAuthorID($postObjectOrID)
    {
        list(
            $post,
            $postID,
        ) = $this->getPostObjectAndID($postObjectOrID);
        return $post->post_author;
    }
}
