<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SalesQuote extends Model
{
    protected $table = 'sales_quotes';
    public $timestamps = false;

    public function details()
    {
       return $this->hasMany(SalesQuoteDetail::class,'quote_id','id')
           ->join('inv_products','inv_products.id','=','sales_quote_details.product_id');
    }

   //Changed price to amount on subtotal, also changed total to amount + vat - discount
   public function cost(){
        return $this->hasOne('App\SalesQuoteDetail','quote_id')
                ->join('sales_quotes', 'sales_quotes.id', '=', 'sales_quote_details.quote_id')
                ->join('price_categories', 'price_categories.id', '=', 'sales_quotes.price_category_id')
                ->select('sales_quotes.id', 'quote_id', 'name',DB::raw('COALESCE(sum(discount),0.00) as discount'),
                	     DB::raw('COALESCE(sum(amount),0.00) as sub_total'),
                	     DB::raw('COALESCE(sum(vat),0.00) as vat'),
                	     DB::raw('COALESCE(sum(amount-discount),0.00) as amount')
                	 )
                ->groupBy('quote_id');
   }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
