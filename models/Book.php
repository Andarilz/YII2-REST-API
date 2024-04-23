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
     * Search method filters the books query according to title\description or author_id
     * @param string|null $search Title or Description to search for
     * @param array|null $authors Array of authors ID's to filtering
     * @return \yii\db\ActiveQuery
     */
    public static function search($search, $authors, $languages, $genre, $minPages, $maxPages)
    {
        $query = static::find()->joinWith('author')->orderBy(['id' => SORT_ASC]);

        if ($search  !== null) {
            $query->andFilterWhere(['or',
                ['like', 'title', strtolower($search)],
                ['like', 'description', $search],
                ['like', 'authors.name', $search]
            ]);
        }
        if ($authors !== null) {
            if (is_array($authors)) {
                $query->andFilterWhere(['in', 'author_id', $authors]);
            }
        }
        if ($languages !== null) {
            if (is_array($languages)) {
                $query->andFilterWhere(['in', 'language', $languages]);
            }
        }
        if ($genre !== null) {
                $query->andFilterWhere(['in', 'genre', $genre]);
        }
        if ($minPages !== null) {
            $query->andWhere(['>=', 'pages', $minPages]);
        }
        if ($maxPages !== null) {
            $query->andWhere(['<=', 'pages', $maxPages]);
        }

        return $query;
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
