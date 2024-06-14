<?php

class Logger
{
    /**
     * Log a message into the log file.
     *
     * @param string|array|\Exception $message
     */
    public static function log($message)
    {
        error_log(self::handleMessage($message), 3, $_SERVER['DOCUMENT_ROOT'] . DS . 'logs' . DS . 'log.txt');
    }

    /**
     * Log an error message into the log file.
     *
     * @param string|array|\Exception $message
     */
    public static function error($message)
    {
        error_log(self::handleMessage($message), 3, $_SERVER['DOCUMENT_ROOT'] . DS . 'logs' . DS . 'error.txt');
    }

    /**
     * Handle the message accordingly to be written into the log file.
     *
     * @param $message
     *
     * @return string|true
     */
    private static function handleMessage($message)
    {
        $response = '';
        if (is_string($message)) {
            $response = $message;
        }

        if (is_array($message)) {
            $response = print_r($message, true);
        }

        if ($message instanceof \Exception) {
            $response = $message->getMessage() . "\n" . $message->getTraceAsString();
        }

        // If a user is logged in, attach their ID
        if (isset($_SESSION['id'])) {
            $response = '[User: ' . $_SESSION['id'] . '] ' . $response;
        }

        $response = date(DATE_RFC2822) . ' [IP: ' . $_SERVER['REMOTE_ADDR'] . '] ' . $response;

        return $response;
    }
}
