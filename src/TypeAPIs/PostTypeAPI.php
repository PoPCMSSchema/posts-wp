<?php
namespace PoP\PostsWP\TypeAPIs;

use WP_Post;
use WP_Query;
use function get_post;
use function get_posts;
use function get_post_status;
use function apply_filters;
use PoP\Hooks\Facades\HooksAPIFacade;
use PoP\PostsWP\TypeAPIs\PostTypeAPIUtils;
use PoP\Posts\TypeAPIs\PostTypeAPIInterface;
use PoP\ComponentModel\TypeDataResolvers\APITypeDataResolverTrait;

/**
 * Methods to interact with the Type, to be implemented by the underlying CMS
 */
class PostTypeAPI implements PostTypeAPIInterface
{
    use APITypeDataResolverTrait;

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

    public function getStatus($postObjectOrID): ?string
    {
        $status = get_post_status($postObjectOrID);
        return PostTypeAPIUtils::convertPostStatusFromCMSToPoP($status);
    }
    public function getPosts($query, array $options = [])
    {
        // Convert the parameters
        $query = $this->convertPostsQuery($query, $options);
        return get_posts($query);
    }
    public function getPostCount($query)
    {
        // All results
        if (!isset($query['limit'])) {
            $query['limit'] = -1;
        }

        // Convert parameters
        $query = $this->convertPostsQuery($query, ['return-type' => POP_RETURNTYPE_IDS]);

        // Taken from https://stackoverflow.com/questions/2504311/wordpress-get-post-count
        $wp_query = new WP_Query();
        $wp_query->query($query);
        return $wp_query->found_posts;
    }
    protected function convertPostsQuery($query, array $options = [])
    {
        if ($return_type = $options['return-type']) {
            if ($return_type == POP_RETURNTYPE_IDS) {
                $query['fields'] = 'ids';
            }
        }

        // Accept field atts to filter the API fields
        $this->maybeFilterDataloadQueryArgs($query, $options);

        // Convert the parameters
        if (isset($query['post-status'])) {
            if (is_array($query['post-status'])) {
                // doing get_posts can accept an array of values
                $query['post_status'] = array_map([PostTypeAPIUtils::class, 'convertPostStatusFromPoPToCMS'], $query['post-status']);
            } else {
                // doing wp_insert/update_post accepts a single value
                $query['post_status'] = PostTypeAPIUtils::convertPostStatusFromPoPToCMS($query['post-status']);
            }
            unset($query['post-status']);
        }
        if ($query['include']) {
            // Transform from array to string
            $query['include'] = implode(',', $query['include']);

            // Make sure the post can also be draft or pending
            if (!isset($query['post_status'])) {
                $query['post_status'] = PostTypeAPIUtils::getCMSPostStatuses();
            }
        }
        if (isset($query['post-types'])) {

            $query['post_type'] = $query['post-types'];
            unset($query['post-types']);
        }
        if (isset($query['offset'])) {
            // Same param name, so do nothing
        }
        if (isset($query['limit'])) {

            $query['posts_per_page'] = $query['limit'];
            unset($query['limit']);
        }
        if (isset($query['order'])) {
            // Same param name, so do nothing
        }
        if (isset($query['orderby'])) {
            // Same param name, so do nothing
            // This param can either be a string or an array. Eg:
            // $query['orderby'] => array('date' => 'DESC', 'title' => 'ASC');
        }
        if (isset($query['post-not-in'])) {

            $query['post__not_in'] = $query['post-not-in'];
            unset($query['post-not-in']);
        }
        if (isset($query['search'])) {

            $query['is_search'] = true;
            $query['s'] = $query['search'];
            unset($query['search']);
        }
        // Filtering by date: Instead of operating on the query, it does it through filter 'posts_where'
        if (isset($query['date-from'])) {

            $query['date_query'][] = [
                'after' => $query['date-from'],
                'inclusive' => false,
            ];
            unset($query['date-from']);
        }
        if (isset($query['date-from-inclusive'])) {

            $query['date_query'][] = [
                'after' => $query['date-from-inclusive'],
                'inclusive' => true,
            ];
            unset($query['date-from-inclusive']);
        }
        if (isset($query['date-to'])) {

            $query['date_query'][] = [
                'before' => $query['date-to'],
                'inclusive' => false,
            ];
            unset($query['date-to']);
        }
        if (isset($query['date-to-inclusive'])) {

            $query['date_query'][] = [
                'before' => $query['date-to-inclusive'],
                'inclusive' => true,
            ];
            unset($query['date-to-inclusive']);
        }

        $query = HooksAPIFacade::getInstance()->applyFilters(
            'CMSAPI:posts:query',
            $query,
            $options
        );
        return $query;
    }
    public function getPostTypes($query = array()): array
    {
        // Convert the parameters
        if (isset($query['exclude-from-search'])) {

            $query['exclude_from_search'] = $query['exclude-from-search'];
            unset($query['exclude-from-search']);
        }
        return \get_post_types($query);
    }

    public function getPostType($post)
    {
        return \get_post_type($post);
    }
    public function getPermalink($postObjectOrID): ?string
    {
        list(
            $post,
            $postID,
        ) = $this->getPostObjectAndID($postObjectOrID);
        if ($this->getStatus($postObjectOrID) == POP_POSTSTATUS_PUBLISHED) {
            return \get_permalink($postID);
        }

        // Function get_sample_permalink comes from the file below, so it must be included
        // Code below copied from `function get_sample_permalink_html`
        include_once ABSPATH.'wp-admin/includes/post.php';
        list($permalink, $post_name) = \get_sample_permalink($postID, null, null);
        return str_replace(['%pagename%', '%postname%'], $post_name, $permalink);
    }
    public function getExcerpt($postObjectOrID): ?string
    {
        return \get_the_excerpt($postObjectOrID);
    }
    protected function getPostObjectAndID($postObjectOrID): array
    {
        if (is_object($postObjectOrID)) {
            $post = $postObjectOrID;
            $postID = $post->ID;
        }  else {
            $postID = $postObjectOrID;
            $post = get_post($postID);
        }
        return [
            $post,
            $postID,
        ];
    }

    public function getTitle($postObjectOrID): ?string
    {
        list(
            $post,
            $postID,
        ) = $this->getPostObjectAndID($postObjectOrID);
        return apply_filters('the_title', $post->post_title, $postID);
    }

    public function getContent($postObjectOrID): ?string
    {
        list(
            $post,
            $postID,
        ) = $this->getPostObjectAndID($postObjectOrID);
        return apply_filters('the_content', $post->post_content);
    }
    // public function getSinglePostTitle($post)
    // {
    //     // Copied from `single_post_title` in wp-includes/general-template.php
    //     return HooksAPIFacade::getInstance()->applyFilters('single_post_title', $post->post_title, $post);
    // }
    public function getSlug($postObjectOrID): ?string
    {
        if ($this->getStatus($postObjectOrID) == POP_POSTSTATUS_PUBLISHED) {
            $post = $this->getPost($postObjectOrID);
            return $post->post_name;
        }

        // Function get_sample_permalink comes from the file below, so it must be included
        // Code below copied from `function get_sample_permalink_html`
        include_once ABSPATH.'wp-admin/includes/post.php';
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


    public function getExcerptMore() {

        return apply_filters('excerpt_more', ' '.'[&hellip;]');
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
    public function getModifiedDate($postObjectOrID): ?string
    {
        list(
            $post,
            $postID,
        ) = $this->getPostObjectAndID($postObjectOrID);
        return $post->post_modified;
    }

}
