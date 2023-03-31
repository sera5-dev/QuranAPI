<?php

namespace App\Lib;

class YatesShuffleEngine
{
    public static function do()
    {

    }

    public static function get_top_shuffle(array $arr, int $n)
	{
		$shuffled_array = static::yates_shuffle($arr);

		return array_slice($shuffled_array, 0, $n);
	}

    public static function yates_shuffle_explain(array $arr, $verbose = false)
    {
        $steps = [];

        $n = count($arr);
        // Start from the last element
        // and swap one by one. We
        // don't need to run for the
        // first element that's why i > 0
        for ($i = $n - 1; $i >= 0; $i--) {
            $before = $arr;

            // Pick a random index
            // from 0 to i
            $j = rand(0, $i);

            $el_k = $arr[$j];
            $el_N = $arr[$i];

            // Swap arr[i] with the
            // element at random index
            $tmp = $arr[$i];
            $arr[$i] = $arr[$j];
            $arr[$j] = $tmp;

            $steps[] = [
                "k" => $el_k,
                "N" => $el_N,
                "before" => $before,
                "after" => implode(",", $arr),
            ];
        }
        return [$arr, $steps];
    }

    public static function yates_shuffle(array $arr)
    {
        $n = count($arr);
        // Start from the last element
        // and swap one by one. We
        // don't need to run for the
        // first element that's why i > 0
        for ($i = $n - 1; $i >= 0; $i--) {
            // Pick a random index
            // from 0 to i
            $j = rand(0, $i);

            // Swap arr[i] with the
            // element at random index
            $tmp = $arr[$i];
            $arr[$i] = $arr[$j];
            $arr[$j] = $tmp;
        }
        return $arr;
    }
}
