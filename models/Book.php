<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property int $author_id
 * @property string $title
 * @property int $pages
 * @property string|null $language
 * @property string $genre
 * @property string|null $description
 */
class Book extends \yii\db\ActiveRecord
{
    /**
     * Returns a query for retrieving author related to the current book.
     *
     *  This method establishes a 'hasOne' relationship between the Book and Author models,
     * linking them via the 'author_id' attribute of the Book model and the 'id' attribute of the Author model.
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'title', 'pages', 'genre'], 'required'],
            [['author_id', 'pages'], 'default', 'value' => null],
            [['author_id', 'pages'], 'integer'],
            [['title', 'language', 'genre', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Author ID',
            'title' => 'Title',
            'pages' => 'Pages',
            'language' => 'Language',
            'genre' => 'Genre',
            'description' => 'Description',
        ];
    }
}
