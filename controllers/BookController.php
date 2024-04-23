<?php

namespace app\controllers;

use app\models\Book;
use app\resources\BookResource;
use app\services\BookService;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\Response;

/**
* BookController manages CRUD-operations for the Book model
**/
class BookController extends Controller
{
    /**
     * Overwrite list of default html-actions for REST API
     * @return array
     */
//    public function actions()
//    {
//        $actions = parent::actions();
//        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
//        return $actions;
//    }

    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                // restrict access to
                'Origin' => ['http://vue.home'],
                // Allow only POST and PUT methods
                'Access-Control-Request-Methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                // Allow only headers 'X-Wsse'
                'Access-Control-Request-Headers' => ['Content-Type'],
                // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
                'Access-Control-Allow-Credentials' => true,
                // Allow OPTIONS caching
                'Access-Control-Max-Age' => 3600,
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
            ],
        ];

        return $behaviors;
    }

    public function actionOptions()
    {
        \Yii::$app->response->statusCode = 200;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['status' => 'success'];
    }

    /**
     * Display a list of books
     * @param null $search
     * @return array
     */
    public function actionIndex($search = null)
    {
        $books = \Yii::$app->db->createCommand(
            'SELECT books.id, authors.name AS author_name, authors.id AS author_id, books.title, books.pages, books.language, books.genre, books.description
            FROM books
            JOIN authors ON books.author_id = authors.id
            WHERE LOWER(authors.name) LIKE :author
                OR title like :title
                OR description like :description'
        )->bindValues(['author' => '%' . strtolower($search) . '%', 'title' => '%' . strtolower($search) . '%', 'description' => '%' . $search . '%'])->queryAll();

        return $books;

    }

    /**
     * Display information about the specific book
     * @param $id
     * @return array|\yii\db\DataReader
     * @throws HttpException
     * @throws \yii\db\Exception
     */
    public function actionView($id)
    {
        $book = \Yii::$app->db->createCommand('
        SELECT books.id, authors.name AS author_name, authors.id AS author_id, books.title, books.pages, books.language, books.genre, books.description
        FROM books
        JOIN authors ON books.author_id = authors.id
        WHERE books.id = :id')->bindValue(':id', $id)->queryOne();

        if($book) {
            return $book;
        } else {
            throw new HttpException(404);
        }
    }

    /**
     * Create new book
     * @return BookResource|array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $book = new Book();
        $book->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if ($book->save()) {
            return BookService::getPreparedBookResource($book);
        } else {
            return ['errors' => $book->errors];
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
        $book = Book::find()->where(['id' => $id])->with('author')->one();

        if($book !== null){
            return $book;
        } else {
           throw new HttpException(404);
        }
    }

    public function actionLanguage()
    {
        return \Yii::$app->db->createCommand('SELECT DISTINCT language FROM books')->queryAll();
    }

}
