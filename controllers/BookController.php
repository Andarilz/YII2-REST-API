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
        $authors = \Yii::$app->request->getQueryParam('author');

        $dataProvider = new ActiveDataProvider([
            'query' => Book::search($search, $authors),
        ]);

        $prepared_books = $dataProvider->getModels();

        $resources = BookService::getPreparedBooksResources($prepared_books);

        return $resources;
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
        \Yii::$app->getResponse()->setStatusCode(204);
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

}
