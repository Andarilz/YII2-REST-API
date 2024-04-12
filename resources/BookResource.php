<?php

namespace app\resources;

class BookResource extends \yii\base\Model
{
    public $id;
    public $title;
    public $pages;
    public $language;
    public $genre;
    public $description;
    public $author;

    /**
     * Set resource fields of Book
     * @return string[]
     */
    public function fields()
    {
        return [
            'id',
            'title',
            'author',
            'pages',
            'language',
            'genre',
            'description'
        ];
    }

    /**
     * Set author field to book resource
     * @param $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }
}
