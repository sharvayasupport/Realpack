<?php
/*
 * Plugin Name: IppoPay for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/ippopay-for-woocommerce/
 * Description: IppoPay Payment Gateway Integration for WooCommerce
 * Version: 1.0.4
 * Stable tag: 1.0.0
 * Author: IppoPay Team
 * Author URI: https://www.ippopay.com/
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('plugins_loaded', 'woocommerce_ippopay_init', 21);

function woocommerce_ippopay_init()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    class WC_Ippopay extends WC_Payment_Gateway
    {
        // This one stores the WooCommerce Order Id
        const SESSION_KEY                    = 'ippopay_wc_order_id';
        const ippopay_PAYMENT_ID             = 'ippopay_payment_id';
        const ippopay_ORDER_ID               = 'ippopay_order_id';
        const REFERENCE_ID                   = 'ippopay_reference_id';
        const ippopay_WOO_ORDER_ID           = 'ippopay_woo_order_id';

        const INR                            = 'INR';
        const CAPTURE                        = 'capture';
        const AUTHORIZE                      = 'authorize';
        const WC_ORDER_ID                    = 'woocommerce_order_id';

        const DEFAULT_LABEL                  = 'Credit Card/Debit Card Payment by Ippopay';
        const DEFAULT_DESCRIPTION            = 'Pay securely by Credit or Debit card or Internet Banking or UPI through Ippopay.';

        protected $visibleSettings = array(
            'enabled',
            'title',
            'description',
            'key_public',
            'key_secret',
            'webhook_url'

        );

        public $form_fields = array();

        public $supports = array(
            'products',
            'refunds'
        );

        /**
         * Can be set to true if you want payment fields
         * to show on the checkout (if doing a direct integration).
         * @var boolean
         */
        public $has_fields = false;

        /**
         * Unique ID for the gateway
         * @var string
         */
        public $id = 'ippopay';

        /**
         * Title of the payment method shown on the admin page.
         * @var string
         */
        public $method_title = 'ippopay';

        /**
         * Icon URL, set in constructor
         * @var string
         */
        public $icon;

        /**
         * TODO: Remove usage of $this->msg
         */
        protected $msg = array(
            'message'   =>  '',
            'class'     =>  '',
        );

        /**
         * Return Wordpress plugin settings
         * @param  string $key setting key
         * @return mixed setting value
         */
        public function getSetting($key)
        {
            return $this->settings[$key];
        }

        /**
         * @param boolean $hooks Whether or not to
         *                       setup the hooks on
         *                       calling the constructor
         */
        public function __construct($hooks = true)
        {
            //$this->icon =  plugins_url('images/logo.png' , _FILE_);

            $this->init_form_fields();
            $this->init_settings();
            // TODO: This is hacky, find a better way to do this
            // See mergeSettingsWithParentPlugin() in subscriptions for more details.
            if ($hooks) {
                $this->initHooks();
            }

            $this->title = $this->getSetting('title');
        }
        protected function initHooks()
        {
            add_action('init', array(&$this, 'check_ippopay_response'));

            add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));

            add_action('woocommerce_api_' . $this->id, array($this, 'check_ippopay_response'));

            $cb = array($this, 'process_admin_options');

            if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
                add_action("woocommerce_update_options_payment_gateways_{$this->id}", $cb);
            } else {
                add_action('woocommerce_update_options_payment_gateways', $cb);
            }
        }

        public function init_form_fields()
        {

            $defaultFormFields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', $this->id),
                    'type' => 'checkbox',
                    'label' => __('Enable this module?', $this->id),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __('Title', $this->id),
                    'type' => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', $this->id),
                    'default' => __(static::DEFAULT_LABEL, $this->id)
                ),
                'description' => array(
                    'title' => __('Description', $this->id),
                    'type' => 'textarea',
                    'description' => __('This controls the description which the user sees during checkout.', $this->id),
                    'default' => __(static::DEFAULT_DESCRIPTION, $this->id)
                ),
                'key_public' => array(
                    'title' => __('Public Key', $this->id),
                    'type' => 'text',
                    'description' => __('The Public Key can be generated from "API Settings" section of Ippopay Dashboard. Use test or live for test or live mode.', $this->id)
                ),
                'key_secret' => array(
                    'title' => __('Secret Key', $this->id),
                    'type' => 'text',
                    'description' => __('The Secret key  can be generated from "API Settings" section of Ippopay Dashboard. Use test or live for test or live mode.', $this->id)
                ),
                'webhook_url' => array(
                    'title' => __('Webhook URL', $this->id),
                    'type' => 'text',
                    'description' => __('Enter your webhook url', $this->id)
                ),

            );

            foreach ($defaultFormFields as $key => $value) {
                if (in_array($key, $this->visibleSettings, true)) {
                    $this->form_fields[$key] = $value;
                }
            }
        }

        /**
         * Receipt Page
         * @param string $orderId WC Order Id
         **/
        function receipt_page($orderId)
        {
            echo $this->generate_ippopay_form($orderId);
        }

        public function getReferenceToken($order_id){
            global $woocommerce;
            $order = wc_get_order($order_id);
            $customer_info = $this->getCustomerInfo($order);
            $args = array(
                'headers' => array(
                    'Content-Type' => 'application/json'
                ),
                'body' => json_encode(array(
                    'amount' => $order->get_total(),
                    'currency' => $this->getOrderCurrency($order),
                    'payment_modes' => "cc,dc,nb,upi",
                    'customer'=>array(
                        'name'=>$customer_info['name'], 
                        'email'=>$customer_info['email'], 
                        'phone'=>array(
                            "national_number" => $customer_info['contact']
                        )
                    ),
                    'notify_url'=>$this->getSetting('webhook_url')
                ))
            );
            /*
            * Your API interaction could be built with wp_remote_post()
            */
            //var_dump($args);
            $response = wp_remote_post('https://'.$this->getSetting('key_public').':'.$this->getSetting('key_secret').'@api.ippopay.com/v1/order/create', $args);
            //var_dump($response);
            return $response;
        }

        /**
         * Generate ippopay button link
         * @param string $orderId WC Order Id
         **/
        public function generate_ippopay_form($order_id)
        {
            global $woocommerce;
            $refTokenForm = $woocommerce->session->get(self::REFERENCE_ID);
            $order = wc_get_order($order_id);
            $this->getReferenceToken($order_id);
            $html = '<p>' . __('Thank you for your order, please click the button below to pay with ippopay.', $this->id) . '</p>';

            if(!empty($refTokenForm)){               
                $html .= $this->generateOrderForm($woocommerce->session->get(self::REFERENCE_ID), $order_id);
                return $html;
            }
            else{
                $response = $this->getReferenceToken($order_id);
                $body = json_decode($response['body'], true);
                if (array_key_exists("data", $body) && array_key_exists("order_id", $body['data']['order'])) {
                    $orderToken = $body['data']['order']['order_id'];
                    $woocommerce->session->set(self::SESSION_KEY, $order_id);
                    $woocommerce->session->set(self::REFERENCE_ID, $orderToken);
                    $html .= $this->generateOrderForm($woocommerce->session->get(self::REFERENCE_ID), $order_id);
                    return $html;
                }
            }
        }
        /**
         * Returns redirect URL post payment processing
         * @return string redirect URL
         */
        private function getRedirectUrl()
        {
            return get_site_url() . '/wc-api/' . $this->id;
        }
        /**
         * Generates the order form
         **/
        function generateOrderForm($orderToken, $order_id)
        {
            $redirectUrl = $this->getRedirectUrl();
            $this->enqueueCheckoutScripts($orderToken);

            return <<<EOT
<form name='ippopayform' action="$redirectUrl" method="POST">
    <input type="hidden" name="ippopay_payment_id" id="ippopay_payment_id">
    <input type="hidden" name="ippopay_signature"  id="ippopay_signature" >
    <input type="hidden" value="$order_id" name="ippopay_woo_order_id" id="ippopay_woo_order_id">
    <input type="hidden" value="$orderToken" name="ippopay_order_id" id="ippopay_order_id">
    <!-- This distinguishes all our various wordpress plugins -->
    <input type="hidden" name="ippopay_wc_form_submit" value="1">
</form>
<p id="msg-ippopay-success" class="woocommerce-info woocommerce-message" style="display:none">
Please wait while we are processing your payment.
</p>
<p>
    <button class="ippopayApi" id="btn-ippopay">Pay Now</button>

    <button id="btn-ippopay-submit" onclick="document.ippopayform.submit()" style="display: none;">Submit</button>
</p>
EOT;
        }
        /**
         * Check for valid ippopay server callback
         **/
        function check_ippopay_response()
        {
            global $woocommerce;

            $orderId = $woocommerce->session->get(self::SESSION_KEY);
            if($orderId === NULL){
                $orderId = $_POST[self::ippopay_WOO_ORDER_ID];
            }
            
            $order = new WC_Order($orderId);
           
            //
            // If the order has already been paid for
            // redirect user to success page
            //

            if ($order->needs_payment() === false) {
                $this->redirectUser($order);
            }
            $ippopaymentId = null;  
            $ippopay_order_id = null;          
            if ($orderId  and !empty($_POST[self::ippopay_PAYMENT_ID])) {
                $error = "";
                $success = false;
                try {
                    $success = false;
                    $ippopaymentId = sanitize_text_field($_POST[self::ippopay_PAYMENT_ID]);
                    $ippopay_order_id = sanitize_text_field($_POST[self::ippopay_ORDER_ID]);
                    $response = wp_remote_get('https://api.ippopay.com/api/v1/pg/open/order/preview/'.$ippopay_order_id.'?public_key='.$this->getSetting('key_public'));
                    
                    if (!is_wp_error($response)) {
                        $body = json_decode($response['body'], true);                        
                        if (array_key_exists("data", $body) && array_key_exists("status", $body['data']['order'])) {
                            $status = $body['data']['order']['status'];
                            $order_status = $body['data']['order']['status'];
                            $getorderid = $body['data']['order']['order_id'];  
                            if ($order_status == 'paid' && $getorderid == $ippopay_order_id)
                                $success = true;
                        }
                    }
                } catch (Errors\SignatureVerificationError $e) {
                    $error = 'WOOCOMMERCE_ERROR: Payment to Ippopay Failed. ' . $e->getMessage();
                }
            } else {
                $success = false;
                $error = 'Customer cancelled the payment';
                $this->handleErrorCase($order);
            }

            $this->updateOrder($order, $success, $error, $ippopaymentId, $ippopay_order_id);

            $this->redirectUser($order);
        }
        public function getCustomerInfo($order)
        {
            if (version_compare(WOOCOMMERCE_VERSION, '2.7.0', '>=')) {
                $args = array(
                    'name'    => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                    'email'   => $order->get_billing_email(),
                    'contact' => $order->get_billing_phone(),
                    'address'=> $order->get_billing_address_1(),
                    'city'=> $order->get_billing_city(),
                    'state'=> $order->get_billing_state(),
                    'postal_code'=> $order->get_billing_postcode(),
                    'api_description' => $order->get_customer_note()
                );
            } else {
                $args = array(
                    'name'    => $order->billing_first_name . ' ' . $order->billing_last_name,
                    'email'   => $order->billing_email,
                    'contact' => $order->billing_phone,
                    'address'=> $order->billing_address_1,
                    'city'=> $order->billing_city,
                    'state'=> $order->billing_state,
                    'postal_code'=> $order->billing_postcode,
                    'api_description'=> $order->customer_note
                    
                );
            }    
            return $args;
        }

        protected function handleErrorCase(&$order)
        {
            $orderId = $this->getOrderId($order);

            $this->msg['class'] = 'error';
            $this->msg['message'] = $this->getErrorMessage($orderId);
        }

        protected function redirectUser($order)
        {
            $redirectUrl = $this->get_return_url($order);

            wp_redirect($redirectUrl);
            exit;
        }

        protected function getErrorMessage($orderId)
        {
            // We don't have a proper order id
            if ($orderId !== null)
            {
                $message = 'An error occured while processing this payment';
            }
            if (isset($_POST['error']) === true)
            {
                $error = $_POST['error'];

                $description = htmlentities($error['description']);
                $code = htmlentities($error['code']);

                $message = 'An error occured. Description : ' . $description . '. Code : ' . $code;

                if (isset($error['field']) === true)
                {
                    $fieldError = htmlentities($error['field']);
                    $message .= 'Field : ' . $fieldError;
                }
            }
            else
            {
                $message = 'An error occured. Please contact administrator for assistance';
            }

            return $message;
        }
        
        /**
         * Modifies existing order and handles success case
         *
         * @param $success, & $order
         */
        public function updateOrder(&$order, $success, $errorMessage, $ippopaymentId, $ippopay_order_id, $webhook = false)
        {
            global $woocommerce;

            $orderId = $this->getOrderId($order);

            if (($success === true) and ($order->needs_payment() === true)){
                $this->msg['message'] = "Thank you for shopping with us. Your account has been charged and your transaction is successful. We will be processing your order soon. Order Id: $orderId";
                $this->msg['class'] = 'success';

                $order->payment_complete($ippopaymentId);
                $order->add_order_note("ippopay payment successful <br/>Ippopay Id: $ippopaymentId");
                $order->add_order_note($this->msg['message']);

                if (isset($woocommerce->cart) === true) {
                    $woocommerce->cart->empty_cart();
                }
            } else {
                $this->msg['class'] = 'error';
                $this->msg['message'] = $errorMessage;
                if ($ippopaymentId) {
                    $order->add_order_note("Payment Failed. Please check Ippopay Dashboard. <br/> Ippopay Id: $ippopaymentId");
                }
                $order->add_order_note("Transaction Failed: $errorMessage<br/>");
                $order->update_status('failed');
            }
            if ($webhook === false) {
                $this->add_notice($this->msg['message'], $this->msg['class']);
            }
        }
        /**
         * Add a woocommerce notification message
         *
         * @param string $message Notification message
         * @param string $type Notification type, default = notice
         */
        protected function add_notice($message, $type = 'notice')
        {
            global $woocommerce;
            $type = in_array($type, array('notice', 'error', 'success'), true) ? $type : 'notice';
            // Check for existence of new notification api. Else use previous add_error
            if (function_exists('wc_add_notice')) {
                wc_add_notice($message, $type);
            } else {
                // Retrocompatibility WooCommerce < 2.1
                switch ($type) {
                    case "error":
                        $woocommerce->add_error($message);
                        break;
                    default:
                        $woocommerce->add_message($message);
                        break;
                }
            }
        }

        private function enqueueCheckoutScripts($orderToken)
        {
            wp_register_script(
                'ippopay_checkout',
                'https://js.ippopay.com/scripts/ippopay.v1.js',
                null,
                null
            );

            $params = array(
                'order_id'          => $orderToken,
                'public_key'         => $this->getSetting('key_public'),
                'secret_key' => $this->getSetting('key_secret')
            );

            wp_register_script('ippopay_wc_script', plugin_dir_url(__FILE__)  . 'script.js', array('ippopay_checkout'));
            wp_localize_script('ippopay_wc_script', 'ippopay_params', $params);
            wp_enqueue_script('ippopay_wc_script');
        }

        protected function getOrderId($order)
        {
            if (version_compare(WOOCOMMERCE_VERSION, '2.7.0', '>=')) {
                return $order->get_id();
            }

            return $order->id;
        }
        /**
         * Gets the Order Key from the Order
         * for all WC versions that we suport
         */
        protected function getOrderKey($order)
        {
            $orderKey = null;

            if (version_compare(WOOCOMMERCE_VERSION, '3.0.0', '>=')) {
                return $order->get_order_key();
            }

            return $order->order_key;
        }
        /**
         * @param  WC_Order $order
         * @return string currency
         */
        private function getOrderCurrency($order)
        {
            if (version_compare(WOOCOMMERCE_VERSION, '2.7.0', '>=')) {
                return $order->get_currency();
            }

            return $order->get_order_currency();
        }
        public function process_payment($order_id)
        {
            global $woocommerce;
            $order = wc_get_order($order_id);            
            //$orderKey = $this->getOrderKey($order);
            $response = $this->getReferenceToken($order_id);            
            if (!is_wp_error($response)) {             
                $body = json_decode($response['body'], true);           
                if (array_key_exists("data", $body) && array_key_exists("status", $body['data']['order'])) {
                    $refToken = $body['data']['reference_token'];
                    $woocommerce->session->set(self::SESSION_KEY, $order_id);
                    $woocommerce->session->set(self::REFERENCE_ID, $refToken);
                    $orderKey = $this->getOrderKey($order);             
                    if (version_compare(WOOCOMMERCE_VERSION, '2.1', '>=')) {
                        return array(
                            'result' => 'success',
                            'redirect' => add_query_arg('key', $orderKey, $order->get_checkout_payment_url(true))
                        );
                    } else if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) {
                        return array(
                            'result' => 'success',
                            'redirect' => add_query_arg(
                                'order',
                                $order->get_id(),
                                add_query_arg('key', $orderKey, $order->get_checkout_payment_url(true))
                            )
                        );
                    } else {
                        return array(
                            'result' => 'success',
                            'redirect' => add_query_arg(
                                'order',
                                $order->get_id(),
                                add_query_arg('key', $orderKey, get_permalink(get_option('woocommerce_pay_page_id')))
                            )
                        );
                    }
                }
            }
        }
    }

    /**
     * Add the Gateway to WooCommerce
     **/
    function woocommerce_add_ippopay_gateway($methods)
    {
        $methods[] = 'WC_Ippopay';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'woocommerce_add_ippopay_gateway');
}