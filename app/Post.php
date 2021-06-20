<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post';

    /**
     * Get the user that owns the post.
     */
    public function user()
    {
        return $this->belongsTo('App\user', 'user_id');
    }

    /**
     * Get the category that owns the post.
     */
    public function category()
    {
        return $this->belongsTo('App\user', 'category_id');
    }
}
