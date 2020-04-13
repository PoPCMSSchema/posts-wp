<?php
namespace PoP\PostsWP\TypeResolvers\Overrides;

use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;
use PoP\PostsWP\TypeDataLoaders\Overrides\ContentEntityUnionTypeDataLoader;

class ContentEntityUnionTypeResolver extends \PoP\Content\TypeResolvers\ContentEntityUnionTypeResolver
{
    public function getTypeDataLoaderClass(): string
    {
        return ContentEntityUnionTypeDataLoader::class;
    }

    /**
     * Overriding function to provide optimization:
     * instead of calling ->isIDOfType on each object (as in parent function), in which case we must make a DB call for each result,
     * we obtain all the types from executing a single query against the DB
     *
     * @param array $ids
     * @return array
     */
    public function getResultItemIDTargetTypeResolvers(array $ids): array
    {
        $resultItemIDTargetTypeResolvers = [];
        $instanceManager = InstanceManagerFacade::getInstance();
        $postUnionTypeDataLoader = $instanceManager->getInstance($this->getTypeDataLoaderClass());
        if ($posts = $postUnionTypeDataLoader->getObjects($ids)) {
            foreach ($posts as $post) {
                $targetTypeResolver = $this->getTargetTypeResolver($post);
                if (!is_null($targetTypeResolver)) {
                    $resultItemIDTargetTypeResolvers[$targetTypeResolver->getID($post)] = $targetTypeResolver;
                }
            }
        }
        return $resultItemIDTargetTypeResolvers;
    }
}
