<?php

namespace App\Lib;

class ResponseGenerator
{

    public static function generate(int $code, $msg, $data)
    {
        return response()->json([
            "ok" => (((200 <= $code) && ($code <= 299)) ? true : false),
            "code" => $code,
            "status" => $msg,
            "message" => $msg,
            "exec_time" => (hrtime(true) - START_TIME) / 1e+6,
            "exec_time_readable" => floor((hrtime(true) - START_TIME) / 1e+6) . " ms",
            "data" => $data
        ])->setStatusCode($code);
    }

    public static function make200($data)
    {
        return static::generate(200, "OK", $data);
    }

    public static function make201($data)
    {
        return static::generate(201, "Created", $data);
    }
}
