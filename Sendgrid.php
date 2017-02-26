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
    private static $bodyEmail;
    private static $bodyTypeEmail;

    /**
     * Used for initial configurations
     * @param $param
     */
    public function config($param)
    {
        Sendgrid::$endPoint     = 'https://api.sendgrid.com/v3/mail/send';
        Sendgrid::$token        = $param['token'];
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
     * @param String $reciverEmail
     */
    public function to($reciverEmail)
    {
        Sendgrid::$reciverEmail = $reciverEmail;
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
     * Digunakan untuk memberikan isi pada email body
     * @param string $body
     * @param string $bodyType
     */
    public function body($body, $bodyType = 'text/html')
    {
        Sendgrid::$bodyEmail        = $body;
        Sendgrid::$bodyTypeEmail    = $bodyType;
    }

    /**
     * Digunakan untuk melakukan pengiriman Email
     */
    public function send()
    {
        $ch = curl_init();

        $curl_options = array(
            CURLOPT_URL             => Sendgrid::$endPoint,
            CURLOPT_HTTPHEADER      => array(
                'Authorization: Bearer '.Sendgrid::$token,
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
    private function _parseJSON()
    {
        $dataJson = array(
            'personalizations' => array(
                array(
                    'to' => array(
                        Sendgrid::_checkEmailDestination()
                    ),
                    'subject'   => Sendgrid::$emailSubject
                )
            ),
            'from'  => array(
                'email' => Sendgrid::$senderEmail
            ),
            'content'   => array(
                array(
                    'type'  => Sendgrid::$bodyTypeEmail,
                    'value' => Sendgrid::$bodyEmail
                )
            )
        );
        return json_encode($dataJson);
    }

    /**
     * Digunakan untuk melakukan pengecekan apakah tujuan email hanya satu atau banyak
     * Dan melakukan validasi apakah format email tujuan sudah sesuai atau belum
     *
     * ToDo:
     * 1. Email Bulk
     */
    private function _checkEmailDestination()
    {
        $return = "";
        if(is_array(Sendgrid::$reciverEmail))
        {
            foreach (Sendgrid::$reciverEmail as $reciver)
            {
                if(!is_array($return))
                {
                    $return[] = array(
                        'email' => $reciver
                    );
                }
                else
                {
                    $nextEmail = array(
                        'email' => $reciver
                    );
                    array_push($return, $nextEmail);
                }
            }
        }
        else
        {
            if(filter_var(Sendgrid::$reciverEmail, FILTER_VALIDATE_EMAIL))
            {
                $return = array(
                    'email' => Sendgrid::$reciverEmail
                );
            }
            else
            {
                $return = json_encode(
                    array(
                        'error_code'    => 301,
                        'error_message' => 'Reciver Email not well formated'
                    )
                );
            }
        }

        return $return;
    }

    /**
     * Digunakan untuk melakukan pengecekan apakah CC Email hanya satu atau banyak
     */
    private function _checkCCEmail()
    {

    }
}
