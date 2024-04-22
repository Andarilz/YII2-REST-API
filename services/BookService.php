<?php


namespace app\services;

use app\models\Book;
use app\resources\BookResource;
use yii\base\BaseObject;

/**
 * BookService have methods for prepared book resources to add author field
 * @package app\services
 */
class BookService
{
    /**
     * Get array of books resources with prepared author fields
     * @param array $books
     * @return array
     */
    public static function getPreparedBooksResources(array $books): array
    {
        $resources = [];
        foreach ($books as $book) {
            $resources[] = self::getPreparedBookResource($book);
        }
        return $resources;
    }

    /**
     * Get book resource with prepared author field
     * @param Book $book
     * @return BookResource
     */
    public static function getPreparedBookResource(Book $book): BookResource
    {
        $resource = new BookResource();
        $resource->id = $book->id;
        $resource->title = $book->title;
        $resource->description = $book->description;
        $resource->pages = $book->pages;
        $resource->language = $book->language;
        $resource->genre = $book->genre;
        $resource->setAuthor($book->author);
        return $resource;
    }

}
