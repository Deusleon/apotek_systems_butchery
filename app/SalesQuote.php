<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SalesQuoteDetail;
use DB;
class SalesQuote extends Model
{
    protected $table = 'sales_quotes';
    public $timestamps = false;


   public function details(){
        return $this->hasMany(SalesQuoteDetail::class,'quote_id','id')
                ->join('inv_products', 'inv_products.id', '=', 'sales_quote_details.product_id')
                ->select('sales_quote_details.id as id','name','sales_quote_details.quantity as quantity','price','vat','discount','amount','sales_quote_details.status')
                ->groupBy('sales_quote_details.id');
   }

   public function cost(){
        return $this->hasOne('App\SalesQuoteDetail','quote_id')
                ->join('sales_quotes', 'sales_quotes.id', '=', 'sales_quote_details.quote_id')
                ->join('price_categories', 'price_categories.id', '=', 'sales_quotes.price_category_id')
                ->select('name',DB::raw('COALESCE(sum(discount),0.00) as discount'),
                	     DB::raw('COALESCE(sum(price),0.00) as sub_total'),
                	     DB::raw('COALESCE(sum(vat),0.00) as vat'),
                	     DB::raw('COALESCE(sum(amount),0.00) as amount')
                	 )
                ->groupBy('quote_id');
   }

}
