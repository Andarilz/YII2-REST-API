<?php


namespace app\services;

use app\models\Author;
use app\resources\AuthorResource;
use yii\base\BaseObject;

/**
 * BookService have methods for prepared book resources to add author field
 * @package app\services
 */
class AuthorService
{
    /**
     * Get array of authors resources with prepared books fields
     * @param array $authors
     * @return array
     */
    public static function getPreparedAuthorsResources(array $authors): array
    {
        $resources = [];
        foreach ($authors as $author) {
            $resources[] = self::getPreparedAuthorResource($author);
        }
        return $resources;
    }

    /**
     * Get book resource with prepared author field
     * @param Author $author
     * @return AuthorResource
     */
    public static function getPreparedAuthorResource(Author $author): AuthorResource
    {
        $resource = new AuthorResource();
        $resource->id = $author->id;
        $resource->name = $author->name;
        $resource->birth_year = $author->birth_year;
        $resource->country = $author->country;
        $resource->books = $author->books;
        return $resource;
    }

}
