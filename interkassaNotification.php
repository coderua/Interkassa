<?php
/**
 * PHP class to emulate «Interkassa.com» HTTP notifications
 * @author Slam <coder.ua@gmail.com>
 * @version 17.09.2013 16:20
 * @license MIT
 * 
 * Usage:
 *
 * include 'interkassaNotification.php';
 * $domain     = 'example.com';      // Your site domain
 * $protocol   = 'https';            // Protocol: http|https
 * $secret_key = 'lY6iGS6OzRzW8GYt'; // Shop secret key
 * 
 * //
 * // 1. Send payments STATUS message
 * //
 *
 * // 1.1. Direct properties initialisation
 * $notification = new interkassaNotification();
 * // Set notification properties as you need
 * // By default some properties setup in constructor
 * $notification->ik_shop_id           = '64C18529-4B94-0B5D-7405-F2752F2B716C'; // Shop ID
 * $notification->ik_payment_amount    = 25;                     // Payment sum
 * $notification->ik_payment_id        = 1261;                   // Shop internal payment id
 * $notification->ik_payment_desc      = 'Payment description';  // Payment description
 * $notification->ik_paysystem_alias   = 'yandexdengir';         // Payment system short name/alias
 * $notification->ik_baggage_fields    = 'Some user data';       // Some user data, returned back from «Interkassa»
 * $notification->ik_payment_timestamp = 1379346671;             // Date and time of payment
 * $notification->ik_payment_state     = 'success';              // Payment status
 * $notification->ik_trans_id          = 'IK_15363502';          // «Interkassa» payment id
 * $notification->ik_currency_exch     = 0.029411;               // Exchange rate for dollar
 * $notification->ik_fees_payer        = 0;                      // Fees on payer
 * $notification->status($protocol .'://'. $domain .'/payments/interkassa/status', 'lY6iGS6OzRzW8GYt');
 *
 * // 1.2. Properties initialization by constructor
 * $notification = new interkassaNotification(
 *     array(
 *      'ik_shop_id'           => '64C18529-4B94-0B5D-7405-F2752F2B716C', // Shop ID
 *      'ik_payment_amount'    => 25,                     // Payment sum
 *      'ik_payment_id'        => 1261,                   // Shop internal payment id
 *      'ik_payment_desc'      => 'Payment description',  // Payment description
 *      'ik_paysystem_alias'   => 'yandexdengir',         // Payment system short name/alias
 *      'ik_baggage_fields'    => 'Some user data',       // Some user data, returned back from «Interkassa»
 *      'ik_payment_timestamp' => 1379346671,             // Date and time of payment
 *      'ik_payment_state'     => 'success',              // Payment status
 *      'ik_trans_id'          => 'IK_15363502',          // «Interkassa» payment id
 *      'ik_currency_exch'     => 0.029411,               // Exchange rate for dollar
 *      'ik_fees_payer'        => 0                       // Fees on payer
 *     )
 * );
 * $notification->status($protocol .'://'. $domain .'/payments/interkassa/status', $secret_key);
 *
 * //
 * // 2. Send payments SUCCESS/FAIL message
 * //
 * $notification->ik_shop_id           = '64C18529-4B94-0B5D-7405-F2752F2B716C';
 * $notification->ik_payment_id        = 1261;                   // Shop internal payment id
 * $notification->ik_paysystem_alias   = 'yandexdengir';         // Payment system short name/alias
 * $notification->ik_baggage_fields    = 'Some user data';       // Some user data, returned back from «Interkassa»
 * $notification->ik_payment_timestamp = 1379346671;             // Date and time of payment
 * $notification->ik_trans_id          = 'IK_15363502';          // «Interkassa» payment id
 * // 2.1. Send success message
 * $notification->success($protocol .'://'. $domain .'/payments/interkassa/success');
 *
 * // 2.2. or Send fail message
 * $notification->fail($protocol .'://'. $domain .'/payments/interkassa/fail');
 */

class interkassaNotification
{
    /**
     * Shop indentificator (Идентификатор магазина)
     * Example: 64C18529-4B94-0B5D-7405-F2752F2B716C
     * @var string
     */
    public $ik_shop_id;

    /**
     * Payment amount (Сумма операции)
     * @var float|string
     */
	public $ik_payment_amount;

    /**
     * Payment identificator (Идентификатор платежа — в соответствии с системой учета продавца)
     * Example: 1234
     * @var int|string
     */
    public $ik_payment_id;

    /**
     * Payment or product description (Описание товара или услуги. Формируется продавцом. Строка добавляется в назначение платежа)
     * Example: iPod 80Gb
     * @var string
     */
    public $ik_payment_desc;

    /**
     * Payment system short name/alias (Способ оплаты с помощью которого была произведена оплата покупателем)
     * List of available values:
     *   rupay, egold, webmoneyz, webmoneyu, webmoneyr, webmoneye, ukrmoneyu, ukrmoneyz, ukrmoneyr, ukrmoneye, liberty, pecunix
     * @var string
     */
    public $ik_paysystem_alias = '';

    /**
     * User field (Пользовательское поле — это поле, переданное с веб-сайта продавца в «Форме запроса платежа».
     * Example: email: mail@mail.com, tel: +380441234567
     * @var string
     */
    public $ik_baggage_fields = '';

    /**
     * Date and time of payment in Unix Timestamp (Дата и время выполнения платежа Unix Timestamp формате)
     * @var int
     */
    public $ik_payment_timestamp;

    /**
     * Payment statement (Состояние (статус) платежа проведенного в системе «INTERKASSA»)
     * List of available values: success, fail
     * Example: success
     * @var string
     */
    public $ik_payment_state;

    /**
     * Interkassa unique payment identificator (Внутренний номер платежа в системе «INTERKASSA»)
     * Example: IK_6878
     * @var string
     */
    public $ik_trans_id;

    /**
     * Currency exchange rate (Курс валюты установленный в «Настройках магазина» в момент создания платежа)
     * @var float|string
     */
    public $ik_currency_exch;

    /**
     * Fees payer
     * (Плательщик комиссии установленный в «Настройках магазина» в момент создания платежа.0 – за счет продавца, 1– за счет покупателя, 2 – 50/50)
     * List of available values: 0, 1, 2
     * @var int
     */
    public $ik_fees_payer;

    /**
     * Sign hash for validating payment message data
     * (Электронная подпись оповещения о выполнении платежа, которая используется для проверки целостности полученной
     * информации и однозначной идентификации отправителя)
     * Example: ED890BA468446635B22779B826425CD2
     * @var string
     */
    public $ik_sign_hash;

    /**
     * Constructor (Конструктор)
     * @param null|array $fields
     */
    public function __construct($fields = NULL)
    {
        // Default values for all type of messages (status|success|fail)
        $this->ik_shop_id           = '64C18529-4B94-0B5D-7405-F2752F2B716C';
        $this->ik_payment_state     = 'success';
        $this->ik_payment_id        = rand(1, 2147483647);
        $this->ik_payment_timestamp = time();
        $this->ik_trans_id          = 'IK_'. rand(1, 2147483647);

        if ( !empty($fields) && is_array($fields) ) {
            if ( array_key_exists('ik_payment_amount', $fields) ) {
                // It's a status fields, setting default random values
                $this->ik_payment_amount    = rand(1, 1000) + rand(1, 99) / 100;
                $this->ik_currency_exch     = 0.029411;
                $this->ik_fees_payer        = 0;
                $this->ik_payment_desc      = 'Product #' . rand(1, 100000);
            }
            $this->init($fields);
        }
    }

    /**
     * Метод инициализации объекта (заполнение полей)
     * @access protected
     * @param array $fields
     * @return bool
     */
    protected function init(array $fields)
    {
        if ( !empty($fields) ) {
            $vars = get_object_vars($this);
            foreach ($fields as $k => $v) {
                if ( array_key_exists($k, $vars) )
                    $this->{$k} = $v;
            }
        }
        return $this;
    }

    /**
     * Generating sign hash
     * (Генерация контрольной подпись оповещения о выполнении платежа)
     * @access private
     * @param string $notification_secret
     * @return string
     */
    private function hash($notification_secret)
    {
        $sing_hash_str = $this->ik_shop_id
            .':'. $this->ik_payment_amount
            .':'. $this->ik_payment_id
            .':'. $this->ik_paysystem_alias
            .':'. $this->ik_baggage_fields
            .':'. $this->ik_payment_state
            .':'. $this->ik_trans_id
            .':'. $this->ik_currency_exch
            .':'. $this->ik_fees_payer
            .':'. $notification_secret;

    	return strtoupper(md5($sing_hash_str));
    }

    /**
     * Send status form (Отправка формы оповещения о платеже)
     * @param string $url
     * @param string $notification_secret
     */
    public function status($url, $notification_secret)
    {
        $fields = array(
            'ik_shop_id',
            'ik_payment_amount',
            'ik_payment_id',
            'ik_payment_desc',
            'ik_paysystem_alias',
            'ik_baggage_fields',
            'ik_payment_timestamp',
            'ik_payment_state',
            'ik_trans_id',
            'ik_currency_exch',
            'ik_fees_payer',
            'ik_sign_hash'
        );

        $this->ik_sign_hash = $this->hash($notification_secret);
        $this->send($url, $fields);
    }


    /**
     * Sending success payment form (Отправка формы выполненного платежа)
     * @param string $url
     */
    public function success($url)
    {
        $this->ik_payment_state = 'success';
        $fields = array(
            'ik_shop_id',
            'ik_payment_id',
            'ik_paysystem_alias',
            'ik_baggage_fields',
            'ik_payment_timestamp',
            'ik_payment_state',
            'ik_trans_id'
        );
        $this->send($url, $fields);
    }

    /**
     * Sending fail payment form (Отправка формы невыполненного платежа)
     * @param string $url
     */
    public function fail($url)
    {
        $this->ik_payment_state = 'fail';
        $fields = array(
            'ik_shop_id',
            'ik_payment_id',
            'ik_paysystem_alias',
            'ik_baggage_fields',
            'ik_payment_timestamp',
            'ik_payment_state',
            'ik_trans_id'
        );
        $this->send($url, $fields);
    }

    /**
     * Signs and sends a notification, outputs all response headers and actual response
     * @access private
     * @param string $url
     * @param array $fields
     */
    private function send($url, array $fields)
    {
        $ch = curl_init();
        $data = array();
        if ( !empty($fields) ) {
            foreach ($fields as $var) {
                if ( isset($this->{$var}) ) $data[$var] = $this->{$var};
            }
        }

    	curl_setopt_array(
            $ch,
            array(
                CURLOPT_URL            => $url,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => false,
                CURLOPT_VERBOSE        => false,
                CURLOPT_POSTFIELDS     => http_build_query($data),
                CURLOPT_CONNECTTIMEOUT => 60,
                CURLOPT_FAILONERROR    => false,
                CURLOPT_HEADER         => true,
            )
        );
    
        echo curl_exec($ch);

        if ( curl_error($ch) ) {
            var_dump(curl_error($ch), curl_errno($ch));	
        }
        curl_close($ch);
    }
}
