<?php


namespace app\controllers;

use app\models\Author;
use yii\rest\Controller;
use yii\web\HttpException;

/**
 * AuthorController manages CRU-methods for the Author model
 * @package app\controllers
 */
class AuthorController extends Controller
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
     * Display list of authors
     * @return array|\yii\db\ActiveRecord[]
     */

    public function actionIndex()
    {
        $authors = Author::find()->all();

        return $authors;
    }

    /**
     * Display information about specific author
     * @param $id
     * @return array|\yii\console\Response|\yii\db\ActiveRecord|\yii\web\Response
     */
    public function actionView($id)
    {
        return $this->findAuthor($id);
    }

    /**
     * Create new author
     * @return Author|array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $author = new Author();

        $author->load(\Yii::$app->getRequest()->getBodyParams(), '');

        $author->name = strtolower($author->name);

        if ($author->save()) {
            return $author;
        } else {
            return ['errors' => $author->errors];
        }
    }

    /**
     * Update an exited author
     * @param $id
     * @return array|\yii\console\Response|\yii\db\ActiveRecord|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */

    public function actionUpdate($id)
    {
        $author = $this->findAuthor($id);
        $author->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if ($author->save()) {
            return $author;
        } else {
            return ['errors' => $author->errors];
        }
    }

    /**
     * Find an Author based on the primary key value with related Book model
     * If the model will not found, a 404 HTTP exception will be thrown
     * @param $id
     * @return array|\yii\console\Response|\yii\db\ActiveRecord|\yii\web\Response
     */

    protected function findAuthor($id)
    {
        $author = Author::find()->where(['id' => $id])->with('books')->one();

        if($author !== null){
            return $author;
        } else {
            throw new HttpException(404);
        }
    }

}