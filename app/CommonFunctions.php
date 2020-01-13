<?php


namespace App;


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

}
