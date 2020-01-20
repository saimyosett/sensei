<?php

namespace App\Uploads;

use App\Exceptions\HttpFetchException;

class HttpFetcher
{

    /**
     * Fetch content from an external URI.
     * @param string $uri
     * @return bool|string
     * @throws HttpFetchException
     */
    public function fetch(string $uri)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 5
        ]);

        $data = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new HttpFetchException($err);
        }

        return $data;
    }
}
