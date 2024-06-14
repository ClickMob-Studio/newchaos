<?php

class Captcha
{
    /**
     * Validate a RECAPTCHA token against the server.
     *
     * @param $token
     *
     * @throws Exception
     *
     * @return bool
     */
    public static function validate($token)
    {
        $fields = [
            'secret' => getenv('RECAPTCHA_SECRET'),
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'],
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);
        $json = json_decode($result, true);
        if (isset($json['success'])) {
            return (bool) $json['success'];
        }

        return false;
    }
}
