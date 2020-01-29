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

}
