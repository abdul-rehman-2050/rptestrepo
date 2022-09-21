<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\Searchable;


class PaymentMethod extends Model {
    use Searchable;
	
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $table = 'payment_methods';
}
