<?php

class ControllerPaymentConcordpay extends Controller
{
    public $codesCurrency = [
        980 => 'UAH',
    ];

    public function index()
    {
        $con = new Concordpay();
        $key = $this->config->get('concordpay_secretkey');
        $con->setSecretKey($key);

        $order_id = $this->session->data['order_id'];

        $this->load->model('checkout/order');
        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $callback_url = $this->url->link('payment/concordpay/callback', '', true);
        $approve_url = $this->url->link('payment/concordpay/approve', '', true);
        $cancel_url = $this->url->link('payment/concordpay/decline', '', true);
        $decline_url = $this->url->link('payment/concordpay/decline', '', true);

        $currency = isset($this->codesCurrency[$order['currency_code']]) ? $this->codesCurrency[$order['currency_code']] : $order['currency_code'];
        $amount = round(($order['total'] * $order['currency_value']), 2);

        $fields = array(
            'operation' => 'Purchase',
            'merchant_id' => $this->config->get('concordpay_merchant'),
            'amount' => $amount,
            'order_id' => $order_id,
            'currency_iso' => $currency,
            'description' => 'Покупка в : '. $_SERVER['HTTP_HOST'],
            'add_params' => 'AddParams',
            'approve_url' => $approve_url,
            'decline_url' => $decline_url,
            'cancel_url' => $cancel_url,
            'callback_url' => $callback_url,
        );

        $fields['signature'] = $con->getRequestSignature($fields);

        $data['fields'] = $fields;
        $data['action'] = Concordpay::URL;
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['text_loading'] = 'loading';
        $data['continue'] = $this->url->link('checkout/success');

        return $this->load->view('payment/concordpay.tpl', $data);
    }

    public function confirm()
    {
        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        if (!$order_info) return;

        $order_id = $this->session->data['order_id'];

        if ($order_info['order_status_id'] == 0) {
            $this->model_checkout_order->confirm($order_id, $this->config->get('concordpay_order_status_progress_id'), 'Concordpay');
            return;
        }

        if ($order_info['order_status_id'] != $this->config->get('concordpay_order_status_progress_id')) {
            $this->model_checkout_order->update($order_id, $this->config->get('concordpay_order_status_progress_id'), 'Concordpay', true);
        }
    }

    public function approve()
    {
        $this->response->redirect($this->url->link('checkout/success'));
    }

    public function response()
    {
        $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
    }

    public function decline()
    {
        $this->load->language('payment/concordpay');

        $this->session->data['error'] = $this->language->get('decline_error');

        $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
    }

    public function callback()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $con = new Concordpay();
        $key = $this->config->get('concordpay_secretkey');
        $con->setSecretKey($key);

        $paymentInfo = $con->isPaymentValid($data);

        if ($paymentInfo === true) {
            $order_id = $data['orderReference'];

            $this->load->model('checkout/order');

            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('concordpay_order_status_id'));
        }
         else {
            echo $paymentInfo;
        }
        exit();
    }
}

class Concordpay
{
    const ORDER_NEW = 'New';
    const ORDER_DECLINED = 'Declined';
    const ORDER_REFUND_IN_PROCESSING = 'RefundInProcessing';
    const ORDER_REFUNDED = 'Refunded';
    const ORDER_EXPIRED = 'Expired';
    const ORDER_PENDING = 'Pending';
    const ORDER_APPROVED = 'Approved';
    const ORDER_WAITING_AUTH_COMPLETE = 'WaitingAuthComplete';
    const ORDER_IN_PROCESSING = 'InProcessing';
    const ORDER_SEPARATOR = '#';

    const SIGNATURE_SEPARATOR = ';';

    const URL = "https://pay.concord.ua/api/";

    protected $secret_key = '';

    protected $keysForResponseSignature = array(
        'merchantAccount',
        'orderReference',
        'amount',
        'currency'
    );

    /** @var array */
    protected $keysForSignature = array(
        'merchant_id',
        'order_id',
        'amount',
        'currency_iso',
        'description'
    );

    /**
     * @param $option
     * @param $keys
     * @return string
     */
    public function getSignature($option, $keys)
    {
        $hash = array();
        foreach ($keys as $dataKey) {
            if (!isset($option[$dataKey])) {
                continue;
            }
            if (is_array($option[$dataKey])) {
                foreach ($option[$dataKey] as $v) {
                    $hash[] = $v;
                }
            } else {
                $hash [] = $option[$dataKey];
            }
        }

        $hash = implode(self::SIGNATURE_SEPARATOR, $hash);
        return hash_hmac('md5', $hash, $this->getSecretKey());
    }

    /**
     * @param $options
     * @return string
     */
    public function getRequestSignature($options)
    {
        return $this->getSignature($options, $this->keysForSignature);
    }

    /**
     * @param $options
     * @return string
     */
    public function getResponseSignature($options)
    {
        return $this->getSignature($options, $this->keysForResponseSignature);
    }

    /**
     * @param $response
     * @return bool|string
     */
    public function isPaymentValid($response)
    {
        $sign = $this->getResponseSignature($response);
        if ($sign != $response['merchantSignature']) {
            return 'An error has occurred during payment';
        }

        if ($response['transactionStatus'] == self::ORDER_APPROVED) {
            return true;
        }

        return false;
    }

    public function setSecretKey($key)
    {
        $this->secret_key = $key;
    }

    public function getSecretKey()
    {
        return $this->secret_key;
    }
}
