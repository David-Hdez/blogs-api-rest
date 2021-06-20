<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * Get the post for the category.
     */
    public function posts()
    {
        return $this->hasMany('App\Post');
    }
}
