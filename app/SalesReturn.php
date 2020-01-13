<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SalesDetail;

class SalesReturn extends Model
{
     protected $table = 'sales_returns';
     public $timestamps = false;

     public function item_returned(){
        return $this->hasOne(SalesDetail::class,'id','sale_detail_id')
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->join('sales', 'sales.id', '=', 'sales_details.sale_id')
                ->join('sales_returns', 'sales_returns.sale_detail_id', '=', 'sales_details.id')
                ->select('name','sales_details.quantity as bought_qty','sales_returns.quantity as rtn_qty','sales.date as b_date','sales_details.amount','sales_details.discount','sales_details.id as item_detail_id','sales_details.status as status','stock_id','sales.id as sale_id')
                ->groupBy('sales_details.id');
   }

}
