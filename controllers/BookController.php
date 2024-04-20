<?php

namespace app\controllers;

use app\models\Book;
use app\resources\BookResource;
use app\services\BookService;
use yii\data\ActiveDataProvider;
use yii\rest\Controller;
use yii\web\HttpException;

/**
* BookController manages CRUD-operations for the Book model
**/
class BookController extends Controller
{
    /**
     * Overwrite list of default html-actions for REST API
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    /**
     * Display a list of books
     * @param null $search
     * @return array
     */
    public function actionIndex($search = null)
    {
        $query = \Yii::$app->db->createCommand(
            'SELECT * FROM books LEFT JOIN authors ON books.author_id = authors.id 
                WHERE LOWER(authors.name) LIKE :author
                OR title like :title
                OR description like :description'
        )->bindValues(['author' => '%' . strtolower($search) . '%', 'title' => '%' . strtolower($search) . '%', 'description' => '%' . $search . '%'])->queryAll();

        $books = BookService::prepareAttributes($query);

        return BookService::getPreparedBooksResources($books);
    }

    /**
     * Display information about the specific book
     * @param $id
     * @return BookResource
     */
//    public function actionView($id)
//    {
//        $book = $this->findBook($id);
//
//        if($book instanceof Book) {
//            $resource = BookService::getPreparedBookResource($book);
//        } else {
//            throw new HttpException(500);
//        }
//
//        return $resource;
//    }

    /**
     * Create new book
     * @return BookResource|array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $bodyParams = \Yii::$app->getRequest()->getBodyParams(); //получаем параметры ввода

        $command = \Yii::$app->db->createCommand("
        INSERT INTO books (author_id, title, pages, language, genre, description)
        VALUES (:author_id, :title, :pages, :language, :genre, :description)
        ")->bindValues([
            ':author_id' => $bodyParams['author_id'],
            ':title' => $bodyParams['title'],
            ':pages' => $bodyParams['pages'],
            ':language' => $bodyParams['language'],
            ':genre' => $bodyParams['genre'],
            ':description' => $bodyParams['description']
        ])->execute(); //вызываем SQL-запрос на создание записи

        if ($command) { //если запись создана успешно
            $id = \Yii::$app->db->createCommand("SELECT id FROM books ORDER BY id DESC LIMIT 1")->execute(); //получаем id последней записи

            $query = \Yii::$app->db->createCommand("SELECT * FROM books WHERE id = :id")->bindValue(':id', $id)->queryOne(); //получаем созданную книгу

            $book = BookService::prepareAttribute($query);

            return BookService::getPreparedBookResource($book);

        } else {
            return ['error' => 'Failed to create a book'];
        }
    }

    /**
     * Update on existing book
     * @param $id
     * @return array|BookResource
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($id)
    {
        $book = $this->findBook($id);
        $book->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if ($book->save()) {
            return BookService::getPreparedBookResource($book);
        } else {
            return ['errors' => $book->errors];
        }
    }

    /**
     * Delete an existing book
     * @param $id
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $book = $this->findBook($id);
        $book->delete();
        \Yii::$app->getResponse()->setStatusCode(204, "Book was deleted successfully!");
    }

    /**
     * Find a Book model based on the primary key value with related Author model
     * If the model is not found, a 404 HTTP exception will be thrown
     * @param $id
     * @return array|\yii\console\Response|\yii\db\ActiveRecord|\yii\web\Response
     */
    protected function findBook($id)
    {
//        $book = Book::find()->where(['id' => $id])->with('author')->one();

        $book = \Yii::$app->db->createCommand(
            "SELECT * FROM books 
                LEFT JOIN authors ON books.author_id = authors.id 
                WHERE books.id = :id")->bindValue('id', $id)->queryOne();

        if($book !== null){
            return $book;
        } else {
           throw new HttpException(404);
        }
    }

}
