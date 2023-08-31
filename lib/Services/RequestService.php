<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot\Services;

use IVDCode\TelegramBot\Settings\Setting;

/**
 * Class RequestService
 */
class RequestService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = Setting::getBaseUrl();
    }

    /**
     * @param $method
     * @param array $params
     * @return mixed
     */
    public function send_request($method, array $params = [])
    {
        $url =  $this->baseUrl . $method;

        if (count($params) > 0) {
            $url .= '?' . http_build_query($params);
        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        $data = curl_exec($curl);
        curl_close($curl);

        return json_decode($data, true);
    }
}