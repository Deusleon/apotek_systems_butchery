<?php


namespace App;


use App\Notifications\StockNotification;
use Illuminate\Support\Facades\DB;

class CommonFunctions
{
    public function generateNumber()
    {
        $unique = strtoupper(substr(md5(microtime()), rand(0, 26), 8));
        return $unique;
    }

    public function sumByKey($key_name, array $new_array, $string_key)
    {
        $result = -1;
        for ($i = 0; $i < sizeof($new_array); $i++) {
            if ($new_array[$i][$string_key] == $key_name) {
                $result = $i;
                break;
            }
        }

        return $result;
    }

    public function search($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->search($subarray, $key, $value));
            }
        }

        return $results;
    }

    public function stockNotificationSchedule($id)
    {
        $key = 'data'; //for expired and out of stock

        /*all notification query*/
        $notifications = DB::table('notifications')
            ->where('read_at', null)
            ->get();
        $save_flag = 0;
        foreach ($notifications as $notification) {
            $decode_data = json_decode($notification->data);
            foreach ($decode_data as $index => $item) {
                if ($key === $index) {
                    /*donot save*/
                    $save_flag = 1;
                } else {
                    /*save*/
                    $save_flag = 0;
                }
            }
        }

        if ($save_flag === 0) {
            User::find($id)->notify(new StockNotification);
        }

    }

}
