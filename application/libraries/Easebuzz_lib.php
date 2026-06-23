<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Easebuzz_lib
 * CodeIgniter library wrapper for Easebuzz Payment Gateway.
 */
class Easebuzz_lib {

    protected $CI;
    protected $merchant_key;
    protected $salt;
    protected $env;
    protected $base_url;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->config('easebuzz');

        $env = $this->CI->config->item('easebuzz_env');
        $this->env = $env;

        if ($env === 'prod') {
            $this->merchant_key = $this->CI->config->item('easebuzz_live_key');
            $this->salt         = $this->CI->config->item('easebuzz_live_salt');
            $this->base_url     = 'https://pay.easebuzz.in/';
        } else {
            $this->merchant_key = $this->CI->config->item('easebuzz_test_key');
            $this->salt         = $this->CI->config->item('easebuzz_test_salt');
            $this->base_url     = 'https://testpay.easebuzz.in/';
        }

        if (empty($this->merchant_key) || empty($this->salt)) {
            log_message('error', 'Easebuzz_lib: merchant key or salt is not configured.');
        }
    }

    public function get_env()  { return $this->env; }
    public function get_key()  { return $this->merchant_key; }

    public function generate_txnid()
    {
        $prefix = $this->CI->config->item('easebuzz_txnid_prefix');
        $rand   = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        return $prefix . date('YmdHis') . $rand;
    }

    public function initiate_payment(array $params)
    {
        $this->_load_sdk();

        if (isset($params['amount'])) {
            $params['amount'] = number_format((float)$params['amount'], 2, '.', '');
        }

        $defaults = [
            'udf1' => '', 'udf2' => '', 'udf3' => '', 'udf4' => '', 'udf5' => '',
            'address1' => '', 'address2' => '', 'city' => '', 'state' => '',
            'country' => 'India', 'zipcode' => ''
        ];
        $params = array_merge($defaults, $params);

        try {
            $easebuzzObj = new Easebuzz($this->merchant_key, $this->salt, $this->env);
            $raw = $easebuzzObj->initiatePaymentAPI($params, false);
            $result = is_string($raw) ? json_decode($raw, true) : $raw;

            if (!is_array($result) || !isset($result['status'])) {
                log_message('error', 'Easebuzz initiatePaymentAPI unexpected response: ' . print_r($raw, true));
                return ['status' => 0, 'data' => 'Unexpected SDK response.'];
            }

            if ((int)$result['status'] === 1 && !empty($result['access_key'])) {
                return ['status' => 1, 'data' => $result['access_key']];
            }

            $err = isset($result['data']) ? $result['data'] : (isset($result['msg']) ? $result['msg'] : 'Payment initiation failed.');
            return ['status' => 0, 'data' => $err];
        } catch (Exception $e) {
            log_message('error', 'Easebuzz initiate_payment exception: ' . $e->getMessage());
            return ['status' => 0, 'data' => 'Gateway error: ' . $e->getMessage()];
        }
    }

    public function build_checkout_url($access_key)
    {
        return $this->base_url . 'pay/' . $access_key;
    }

    public function verify_response_hash(array $post)
    {
        $required = ['key','txnid','amount','productinfo','firstname','email','status','hash'];
        foreach ($required as $f) {
            if (!isset($post[$f])) {
                log_message('error', "Easebuzz verify_response_hash: missing field '{$f}'");
                return false;
            }
        }

        $hash_str = $this->salt . '|'
                  . $post['status'] . '|'
                  . (isset($post['udf10']) ? $post['udf10'] : '') . '|'
                  . (isset($post['udf9'])  ? $post['udf9']  : '') . '|'
                  . (isset($post['udf8'])  ? $post['udf8']  : '') . '|'
                  . (isset($post['udf7'])  ? $post['udf7']  : '') . '|'
                  . (isset($post['udf6'])  ? $post['udf6']  : '') . '|'
                  . (isset($post['udf5'])  ? $post['udf5']  : '') . '|'
                  . (isset($post['udf4'])  ? $post['udf4']  : '') . '|'
                  . (isset($post['udf3'])  ? $post['udf3']  : '') . '|'
                  . (isset($post['udf2'])  ? $post['udf2']  : '') . '|'
                  . (isset($post['udf1'])  ? $post['udf1']  : '') . '|'
                  . $post['email'] . '|'
                  . $post['firstname'] . '|'
                  . $post['productinfo'] . '|'
                  . $post['amount'] . '|'
                  . $post['txnid'] . '|'
                  . $post['key'];

        $expected_hash = strtolower(hash('sha512', $hash_str));
        $received_hash = strtolower($post['hash']);

        $match = hash_equals($expected_hash, $received_hash);
        if (!$match) {
            log_message('error', 'Easebuzz hash mismatch. txnid=' . $post['txnid']);
        }
        return $match;
    }

    public function verify_via_api($txnid, $amount, $email, $phone)
    {
        $this->_load_sdk();
        try {
            $easebuzzObj = new Easebuzz($this->merchant_key, $this->salt, $this->env);
            $postData = [
                'txnid'  => $txnid,
                'amount' => number_format((float)$amount, 2, '.', ''),
                'email'  => $email,
                'phone'  => $phone
            ];
            $result = $easebuzzObj->transactionAPI($postData);
            return is_string($result) ? json_decode($result, true) : $result;
        } catch (Exception $e) {
            log_message('error', 'Easebuzz verify_via_api exception: ' . $e->getMessage());
            return ['status' => 0, 'data' => $e->getMessage()];
        }
    }

    protected function _load_sdk()
    {
        if (!class_exists('Easebuzz', false)) {
            $sdk_path = APPPATH . 'third_party/easebuzz-lib/easebuzz_payment_gateway.php';
            if (!file_exists($sdk_path)) {
                show_error('Easebuzz SDK not found at: ' . $sdk_path);
            }
            require_once $sdk_path;
        }
    }
}
