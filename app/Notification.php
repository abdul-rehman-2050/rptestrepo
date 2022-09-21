<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;

class Notification extends Model
{
    use Searchable;
    protected $searchables = ['message'];
    

    protected $protected = [
    ];
    protected $primaryKey = 'id';
    protected $table = 'notifications';

}
