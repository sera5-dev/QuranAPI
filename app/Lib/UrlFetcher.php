<?php

namespace App\Lib;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UrlFetcher
{
    protected $url = "";
    protected $getQuery = [];

    public function __construct($url)
    {
        $this->url = $url;

        return $this;
    }

    public function buildGetQuery($params)
    {
        $this->getQuery = $params;

        return $this;
    }

    public function process()
    {
        $ch = curl_init();

        $data = http_build_query($this->getQuery);

        $getUrl = $this->url . "?" . $data;

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            curl_close($ch);

            throw new HttpException("Cannot crawl");
        } else {
            curl_close($ch);

            return $response;
        }

    }
}
