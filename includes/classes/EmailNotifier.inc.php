<?php
/**
 * This is relatively new class is intended to be used as a central class for
 * sending email notifications
 * all email notifications should be registered here.
 *
 * Singleton
 */
final class EmailNotifier extends PHPMailer
{
    const PATH = 'views/mail_templates';
    const EXT = '.tpl';
    public static $_instance;

    public $From = 'noreply@generalforces.com';
    public $FromName = 'Automatic Warning';
    //public $Host = 'localhost';

    public $Host = 'mail.ktnet.kg';
    public $Mailer = 'smtp';

    /**
     * TODO: Fill with data.
     */
    private $types = [
        'pmail_for_harassment' => [
            'Subject' => 'Harassing',
        ],
        'pmail_for_harassment_2' => [
            'Subject' => 'Harassing',
        ],
    ];

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new EmailNotifier();
        }

        return self::$_instance;
    }

    /**
     *  Send notif email from template (defined by path/$type.tpl).
     */
    public function Send($type, $email, $variables = false)
    {
        if (!array_key_exists($type, $this->types)) {
            throw new Exception('Uknown type of email notification');
        }
        $tpl = dirname(__FILE__) . '/../../';
        $tpl .= self::PATH . '/' . $type . self::EXT;

        if (!is_file($tpl)) {
            throw new Exception('Template file doesn\'t exist');
        }
        if (is_array($email)) {
            foreach ($email as $e) {
                $this->AddAddress($e);
            }
        } else {
            $this->AddAddress($email);
        }

        if ($variables) {
            extract($variables, EXTR_OVERWRITE);
        }

        ob_start();
        include $tpl;
        $this->Body = ob_get_clean();

        $this->Subject = $this->types[$type]['Subject'];

        if (!parent::Send()) {
            throw new SoftException('There has been a mail error sending to ' . $email);
        }

        return true;
    }
}
