<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "authors".
 *
 * @property int $id
 * @property string $name
 * @property int $birth_year
 * @property string $country
 */
class Author extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'authors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'birth_year', 'country'], 'required'],
            [['birth_year'], 'default', 'value' => null],
            [['birth_year'], 'integer'],
            [['name', 'country'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'birth_year' => 'Birth Year',
            'country' => 'Country',
        ];
    }
}
