<?php
if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists('send_notification')) {
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function send_notification($option)
    {
        $fcmEndpoint = 'https://fcm.googleapis.com/fcm/send';

        $option['notification']['sound'] = $option['notification']['sound'] ?? 'default';

        $payloads = [
            'content_available' => true,
            'priority' => $option['priority'] ?? 'high',
            'data' => $option['data'] ?? null,
            'notification' => $option['notification'] ?? null,
            'time_to_live' => $option['time_to_live'] ?? null,
        ];
        $payloads['registration_ids'] = $option['device_token'] ?? null;

        $serverKey = env('FCM_SERVER_KEY');
        if (is_null($serverKey)) {
            return false;
        }

        $headers = [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payloads));
        $result = json_decode(curl_exec($ch), true);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        curl_close($ch);

        return $result;
    }
}
