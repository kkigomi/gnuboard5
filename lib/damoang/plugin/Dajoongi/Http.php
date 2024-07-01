<?php

namespace Damoang\Plugin\Dajoongi;

class Http
{
    public static function get($url, $headers = [])
    {
        $ch = curl_init();
        $responseHeaders = [];
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$responseHeaders) {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) // ignore invalid headers
                return $len;
            $responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);
            return $len;
        });
        $response = curl_exec($ch);
        curl_close($ch);
        $contentType = @$responseHeaders['content-type'][0] ?? '';
        return $contentType == 'application/json' ? json_decode($response) : $response;
    }

    public static function post($url, $data, $headers = [], $json = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        if ($json) {
            $data = json_encode($data);
            $headers[] = 'Content-Type: application/json';
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
