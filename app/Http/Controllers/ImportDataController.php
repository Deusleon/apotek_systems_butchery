<?php

namespace App\Http\Controllers;


use App\Category;
use App\CurrentStock;
use App\GoodsReceiving;
use App\PriceCategory;
use App\PriceList;
use App\Product;
use App\StockTracking;
use App\Store;
use App\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDateObject;

class ImportDataController extends Controller
{

    public function index()
    {
        $categories = Category::all();
        $price_categories = PriceCategory::all();
        $suppliers = Supplier::orderby('name', 'ASC')->get();
        $stores = Store::all();
        return view('import.index', compact('categories', 'price_categories', 'stores', 'suppliers'));
    }

    public function recordImport(Request $request)
    {
        $pending_to_save = array();
        if ($request->file('file')) {
            $excel_raw_data = Excel::toArray(null, request()->file('file'));

            $loop_count = 1;
            foreach ($excel_raw_data as $raw_data) {
                unset($raw_data[0]);
                foreach ($raw_data as $data) {
                    $loop_count++;
                    $data[2] = preg_replace('/[^\d.]/', '', $data[2]);
                    $data[3] = preg_replace('/[^\d.]/', '', $data[3]);
                    $data[4] = preg_replace('/[^\d.]/', '', $data[4]);

                    try {
                        if ($data[5] != null) {
                            $excelDate = ExcelDateObject::excelToDateTimeObject($data[5]);
                            $excel_date = $excelDate->format('Y-m-d');
                        } else {
                            $excel_date = null;
                        }
                        if (is_numeric($data[2]) && is_numeric($data[3]) && is_numeric($data[4])) {

                            /*check category name if exists*/
                            $category_id = Category::where('name', $data[7])->value('id');

                            array_push($pending_to_save, array(
                                'name' => $data[1],
//                                'category_id' => $request->category_id,
                                'price_category_id' => $request->price_category_id,
                                'store_id' => $request->store_id,
                                'unit_price' => $data[2],
                                'sell_price' => $data[3],
                                'quantity' => $data[4],
                                'date' => $excel_date,
                                'barcode' => $data[6],
                                'category_id' => $category_id
                            ));
                        } else {
                            if ($data[1] != null) {
                                /*end of data*/
                                $pending_to_save = [];
                                session()->flash("alert-danger", "Item row " . $loop_count . " has wrong entry!");
                                return back();
                                break;
                            }
                        }

                    } catch (Exception $e) {
                        $pending_to_save = [];
                        session()->flash("alert-danger", "Date format error!");
                        return back();
                    }

                }
            }

            /*save the data*/
            DB::statement('ALTER TABLE inv_products AUTO_INCREMENT = 100000;');
            foreach ($pending_to_save as $data) {
                //save in product
                $product = new Product;
                $product->name = $data['name'];
                $product->category_id = $data['category_id'];
                $product->barcode = $data['barcode'];

                try {
                    $product->save();
                } catch (Exception $e) {
                    /*truncate all the data*/
                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    GoodsReceiving::truncate();
                    PriceList::truncate();
                    StockTracking::truncate();
                    CurrentStock::truncate();
                    Product::truncate();
                    DB::statement('ALTER TABLE inv_products AUTO_INCREMENT = 100000;');
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    $pending_to_save = [];
                    session()->flash("alert-danger", $data['name'] . " has duplicate record!");
                    return back();
                }

                //save incoming
                $incoming = new GoodsReceiving;
                $incoming->product_id = $product->id;
                $incoming->quantity = $data['quantity'];
                $incoming->supplier_id = $request->supplier_id;
                $incoming->expire_date = $data['date'];
                $incoming->unit_cost = $data['unit_price'];
                $incoming->total_cost = $data['quantity'] * $data['unit_price'];
                $incoming->total_sell = $data['quantity'] * $data['sell_price'];
                $incoming->sell_price = $data['sell_price'];
                $incoming->created_by = Auth::user()->id;
                $incoming->item_profit = $data['quantity'] * ($data['sell_price'] - $data['unit_price']);
                $incoming->save();

                /*current_stock*/
                $current_stock = new CurrentStock;
                $current_stock->product_id = $product->id;
                $current_stock->quantity = $data['quantity'];
                $current_stock->unit_cost = $data['unit_price'];
                $current_stock->expiry_date = $data['date'];
                $current_stock->store_id = $request->store_id;
                $current_stock->created_by = Auth::user()->id;
                $current_stock->save();

                /*sales price*/
                $sales_price = new PriceList;
                $sales_price->stock_id = $current_stock->id;
                $sales_price->price = $data['sell_price'];
                $sales_price->price_category_id = $request->price_category_id;
                $sales_price->status = 1;
                $sales_price->created_at = date('Y-m-d H:m:s');
                $sales_price->save();

                /*stock tracking*/
                $tracking = new StockTracking;
                $tracking->stock_id = $current_stock->id;
                $tracking->product_id = $product->id;
                $tracking->quantity = $data['quantity'];
                $tracking->out_mode = 'Stock Taking';
                $tracking->store_id = $request->store_id;
                $tracking->updated_by = Auth::user()->id;
                $tracking->updated_at = date('Y-m-d');
                $tracking->movement = 'IN';
                $tracking->save();

            }

        }

        session()->flash("alert-success", "Data Imported Successfully!");
        return back();
    }

    public function getImportTemplate()
    {
        $file = public_path() . "/fileStore/import_template/import_data_template.xlsx";
        $headers = array(
            'Content-Type: application/csv',
        );
        return Response::download($file, 'import_data_template.csv', $headers);
    }


}
