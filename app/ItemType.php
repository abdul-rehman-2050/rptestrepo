<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;

class ItemType extends Model
{
    use Searchable;
    protected $searchables = ['models.name'];

    protected $fillable = [
        'name',
        'parent_id',
        'description'
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'created_by' => 'integer',
    ];


    protected $primaryKey = 'id';
    protected $table = 'models';

    public function scopeOnlyCategories($query) {
        return $query->whereNull('models.parent_id')->orWhere('models.parent_id', 0);
    }
    public function defects() {
        return $this->hasMany(\App\ItemType::class, 'parent_id', 'id');
    }
}
