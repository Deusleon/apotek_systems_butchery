<?php

namespace App\Http\Controllers;


use App\Category;
use App\CommonFunctions;
use App\CurrentStock;
use App\Customer;
use App\PriceCategory;
use App\Product;
use App\Sale;
use App\SalesDetail;
use App\Store;
use App\Supplier;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDateObject;


ini_set('max_execution_time', 500);
set_time_limit(500);
ini_set('memory_limit', '512M');


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
        $all_products = array();

        $products = Product::all();
        $commonFunction = new CommonFunctions();

        foreach ($products as $product) {
            array_push($all_products, array(
                'id' => $product->id,
                'name' => $product->name
            ));
        }

        if ($request->file('file')) {
            $excel_raw_data = Excel::toArray(null, request()->file('file'));


            $loop_count = 1;
            foreach ($excel_raw_data as $raw_data) {
                unset($raw_data[0]);

                foreach ($raw_data as $data) {
                    $loop_count++;
                    $data[1] = preg_replace('/[^\d.]/', '', $data[1]);
                    $data[2] = preg_replace('/[^\d.]/', '', $data[2]);
                    $data[3] = preg_replace('/[^\d.]/', '', $data[3]);

                    try {
                        if ($data[7] != null) {
                            $excelDate = ExcelDateObject::excelToDateTimeObject($data[7]);
                            $excel_date = $excelDate->format('Y-m-d');
                        } else {
                            $excel_date = null;
                        }
                        if (is_numeric($data[1]) && is_numeric($data[2]) && is_numeric($data[3])) {

                            /*stock_id*/
//                            $product_id = Product::where('name',$data[0])->value('id');

                            $returned_ = $commonFunction->search($all_products, 'name', $data[0]);

                            if ($returned_ != []) {
                                $product_id = $returned_[0]['id'];
                            }


//                            $product_id = DB::table('inv_products')
//                                ->whereRaw('CAST(name AS UNSIGNED) = "'.$test.'"')
//                                ->value('id');
//

                            $stock_id = CurrentStock::where('product_id', $product_id)->value('id');

                            /*customer id*/
                            $customer_id = Customer::whereRaw('name like "' . $data[5] . '"')->value('id');

                            /*user_id*/
                            $user_id = User::whereRaw('name like "' . $data[6] . '"')->value('id');

                            array_push($pending_to_save, array(
                                'price_category_id' => 1,
                                'receipt_number' => $data[4],
                                'date' => $excel_date,
                                'created_by' => $user_id,
                                'customer_id' => $customer_id,
                                'stock_id' => $stock_id,
                                'quantity' => $data[1],
                                'sell_price' => $data[2],
                                'amount' => $data[1] * $data[2],
                                'vat' => 0,
                                'discount' => 0
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
                        dd($e);
                        $pending_to_save = [];
                        session()->flash("alert-danger", "Date format error!");
                        return back();
                    }

                }
            }


            /*group by receipt no*/
            $grouped_by_receipt_no = array();
            foreach ($pending_to_save as $val) {
                if (array_key_exists('receipt_number', $val)) {
                    $grouped_by_receipt_no[$val['receipt_number']][] = $val;
                }
            }


            /*save the data*/
            foreach ($grouped_by_receipt_no as $key => $data) {
                /*sale save*/
                $sale = new Sale;
                $sale->receipt_number = $key;
                $sale->customer_id = $data[0]['customer_id'];
                $sale->date = $data[0]['date'];
                $sale->created_by = $data[0]['created_by'];
                $sale->price_category_id = $data[0]['price_category_id'];
                $sale->save();

                /*save sale detail*/
                foreach ($data as $datum) {
                    $sale_detail = new SalesDetail;
                    $sale_detail->sale_id = $sale->id;
                    $sale_detail->stock_id = $datum['stock_id'];
                    $sale_detail->quantity = $datum['quantity'];
                    $sale_detail->price = $datum['sell_price'];
                    $sale_detail->vat = $datum['vat'];
                    $sale_detail->amount = $datum['amount'];
                    $sale_detail->discount = $datum['discount'];
                    $sale_detail->save();
                }


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
