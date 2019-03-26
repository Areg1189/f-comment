<?php

namespace Quick;

class Logger
{
    public static function log($data)
    {
        $_token = "347561403:AAEY8vWDRbuw2iZeU8p2K9kLzL3Y6sy6LlM";
        $chat = "89952480";
        $url = "https://api.telegram.org/bot$_token/sendMessage?chat_id=$chat";
        $content = json_encode(['text' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-type: application/json"]);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        curl_exec($curl);
    }
}
