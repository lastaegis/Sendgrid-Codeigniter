<?php
/**
 * Created by PhpStorm.
 * User: ianno
 * Date: 26/02/17
 * Time: 16:27
 */

/**
 * Class Sendgrid
 */
class Sendgrid
{
    private static $endPoint;
    private static $token;
    private static $senderEmail;
    private static $senderName;
    private static $reciverEmail;
    private static $emailSubject;
    private static $ccEmail;
    private static $enableDebug;
    private static $locationForDebug;
    private static $bulkEmail;

    /**
     * Used for initial configurations
     * @param $param
     */
    public function config($param)
    {
        Sendgrid::$endPoint     = 'https://api.sendgrid.com/v3/mail/send';
        Sendgrid::$token        = $param['token'];
//        Sendgrid::$senderEmail  = $param['sender_email'];
        Sendgrid::$senderEmail  = $param['sender_email'];
        Sendgrid::$senderName   = $param['sender_name'];
        Sendgrid::$enableDebug  = $param['debug'];
    }

    /**
     * Digunakan untuk menambahkan Email Subject
     * @param $emailSubject
     */
    public function subject($emailSubject)
    {
        Sendgrid::$emailSubject = $emailSubject;
    }

    /**
     * Digunakan untuk menambahkan penerima email
     * @param $reciverEmail
     */
    public function to($reciverEmail)
    {
        if(array($reciverEmail))
        {

        }
        else
        {
            Sendgrid::$reciverEmail = $reciverEmail;
        }
    }

    /**
     * Digunakan untuk menambahkan CC Email
     * @param $ccEmail
     */
    public function cc($ccEmail)
    {
        if(array($ccEmail))
        {

        }
        else
        {
            Sendgrid::$ccEmail;
        }
    }

    /**
     * Digunakan untuk melakukan pengiriman Email
     */
    public function sendEmail()
    {
        $ch = curl_init();

        $curl_options = array(
            CURLOPT_URL             => Sendgrid::$endPoint,
            CURLOPT_HTTPHEADER      => array(
                'Authorization: Bearer '.Sendgrid::$endPoint,
                'Content-Type: application/json'
            ),
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS      => Sendgrid::_parseJSON(),
            CURLOPT_HTTPAUTH        => 1
        );

        curl_setopt_array($ch, $curl_options);

        $result = curl_exec($ch);
        if(Sendgrid::$enableDebug == "true")
        {
            return $result;
        }
    }

    /**
     * Digunakan untuk melakukan JSON Parse terhadap seluruh data yang diterima
     */
    public function parseJSON()
    {
        $dataJson = array(
            'personalizations' => array(
                array(
                    'to' => array(

                    ),
                    'subject'   => 'Test Email'
                )
            ),
            'from'  => array(
                'email' => Sendgrid::$senderEmail
            ),
            'content'   => array(
                array(
                    'type'  => 'Text/plain',
                    'value' => 'Hello World!'
                )
            )
        );
        return json_encode($dataJson);
    }

    /**
     * Digunakan untuk melakukan pengecekan apakah tujuan email hanya satu atau banyak
     * Dan melakukan validasi apakah format email tujuan sudah sesuai atau belum
     */
    private function _checkEmailDestination()
    {
        if(array(Sendgrid::$reciverEmail))
        {

        }
        else
        {
            if(filter_var(Sendgrid::$reciverEmail, FILTER_VALIDATE_EMAIL))
            {
                return array(
                    'email' => Sendgrid::$reciverEmail
                );
            }
            else
            {
                return json_encode(
                    array(
                        'error_code'    => 301,
                        'error_message' => 'reciver Email not well formated'
                    )
                );
            }
        }
    }

    /**
     * Digunakan untuk melakukan pengecekan apakah CC Email hanya satu atau banyak
     */
    private function _checkCCEmail()
    {

    }
}
