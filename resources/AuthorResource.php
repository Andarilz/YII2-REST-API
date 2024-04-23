<?php

namespace app\resources;

class AuthorResource extends \yii\base\Model
{
    public $id;
    public $name;
    public $birth_year;
    public $country;
    public $books;

    /**
     * Set resource fields of Book
     * @return string[]
     */
    public function fields()
    {
        return [
            'id',
            'name',
            'birth_year',
            'country',
            'books'
        ];
    }

}
