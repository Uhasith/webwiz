<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $token
 * @property string $created_at
 * @property string $updated_at
 */
class Firebase extends Model
{

    protected $table = 'firebase';
    /**
     * @var array
     */
    protected $fillable = ['token', 'created_at', 'updated_at'];

  
   
}
