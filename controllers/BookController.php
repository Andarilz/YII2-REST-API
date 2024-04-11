<?php

namespace app\controllers;

use app\models\Book;
use yii\data\ActiveDataProvider;
use yii\data\DataFilter;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

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
     */
    public function actionIndex($search = null)
    {
        $authors = \Yii::$app->request->getQueryParam('author');
        $query = Book::find();
        if ($search !== null) {
            $query->andFilterWhere(['or',
                ['like', 'title', $search],
                ['like', 'description', $search]
            ]);
        }
        if ($authors !== null) {
            if (is_array($authors)) {
                    $query->andFilterWhere(['in', 'author_id', $authors]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    /**
     * Display information about the specific book
     * @param $id
     * @return array|\yii\console\Response|\yii\db\ActiveRecord|\yii\web\Response
     */
    public function actionView($id)
    {
        return $this->findBook($id);
    }

    /**
     * Create new book
     * @return Book|array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $book = new Book();

        $book->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if ($book->save()) {
            return $book;
        } else {
            return ['errors' => $book->errors];
        }
    }

    /**
     * Update on existing book
     * @param $id
     * @return array|\yii\console\Response|\yii\db\ActiveRecord|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate($id)
    {
        $book = $this->findBook($id);
        $book->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if ($book->save()) {
            return $book;
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
     * Find a Book model based on the primary key value
     * If the model is not found, a 404 HTTP exception will be thrown
     * @param $id
     * @return array|\yii\console\Response|\yii\db\ActiveRecord|\yii\web\Response
     */
    protected function findBook($id)
    {
        $book = Book::find()->where(['id' => $id])->one();

        if($book !== null){
            return $book;
        } else {
           return \Yii::$app->getResponse()->setStatusCode(404);
        }
    }

}
