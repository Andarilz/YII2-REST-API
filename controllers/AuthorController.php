<?php


namespace app\controllers;

use app\models\Author;
use app\services\AuthorService;
use yii\rest\Controller;
use yii\web\HttpException;

/**
 * AuthorController manages CRU-methods for the Author model
 * @package app\controllers
 */
class AuthorController extends Controller
{

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

    /**
     * Display list of authors
     * @return array|\yii\db\ActiveRecord[]
     */

    public function actionIndex()
    {
        $authors = Author::find()->all();

        return AuthorService::getPreparedAuthorsResources($authors);
    }

    /**
     * Display information about specific author
     * @param $id
     * @return \app\resources\AuthorResource
     * @throws HttpException
     */
    public function actionView($id)
    {
        $author = $this->findAuthor($id);
        if($author instanceof Author){
            return AuthorService::getPreparedAuthorResource($author);
        }
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

        $author->name = strtolower($author->name);

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