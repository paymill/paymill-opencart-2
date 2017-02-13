<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/paymill/lib/Services/Paymill/LoggingInterface.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/paymill/lib/Services/Paymill/Preauthorizations.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/paymill/lib/Services/Paymill/Transactions.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/paymill/lib/Services/Paymill/Refunds.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/paymill/lib/Services/Paymill/PaymentProcessor.php';
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/paymill/metadata.php';

class ControllerCustomPaymillOrder extends Controller implements Services_Paymill_LoggingInterface
{

    /**
     * @var Services_Paymill_PaymentProcessor
     */
    private $paymillProcessor;

    /**
     * @var Services_Paymill_Preauthorizations
     */
    private $paymillPreauth;

    /**
     * @var Services_Paymill_Transactions
     */
    private $paymillTransaction;

    /**
     * @var Services_Paymill_Refunds
     */
    private $paymillRefund;


    /**
     * @var string
     */
    private $apiEndpoint = 'https://api.paymill.com/v2/';

    private $logId;

    protected function _getBreadcrumbs()
    {
        $orderId = $this->getPost('orderId');
        $orderIdUrl = is_null($orderId)?'':'&order_id='.$orderId;
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'],true),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $breadcrumbs[] = array(
            'href' => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'].$orderIdUrl, true),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );
        return $breadcrumbs;
    }

    public function init(){
        $this->logId = time();
        $key = $this->config->get('paymillcreditcard_privatekey');
        $this->paymillProcessor = new Services_Paymill_PaymentProcessor($key, $this->apiEndpoint);
        $this->paymillPreauth = new Services_Paymill_Preauthorizations($key, $this->apiEndpoint);
        $this->paymillTransaction = new Services_Paymill_Transactions($key, $this->apiEndpoint);
        $this->paymillRefund = new Services_Paymill_Refunds($key, $this->apiEndpoint);

        $metadata = new metadata();
        $source = $metadata->getVersion() . "_opencart_" . VERSION;
        $this->paymillProcessor->setSource($source);
        $this->paymillProcessor->setLogger($this);
    }

    private function getPost($name, $default = null)
    {
        $value = $default;
        if (isset($this->request->request[$name])) {
            $value = $this->request->request[$name];
        }
        return $value;
    }

    public function index()
    {
        $this->load->model('sale/order');
        $this->load->model('localisation/order_status');
        $this->load->language('sale/order');
        $this->load->language('custom/paymillOrder');
   

        $orderId = $this->getPost('orderId', 0);

        $order_info = $this->model_sale_order->getOrder($orderId);
        $data['breadcrumbs'] = $this->_getBreadcrumbs();
        $data['data_orderId'] = '-';
        $data['data_storename'] = '-';
        $data['data_customer_firstname'] = '-';
        $data['data_customer_lastname'] = '-';
        $data['data_customer_email'] = '-';
        $data['data_order_total'] = '-';
        $data['data_order_date_added'] = '-';
        $data['data_order_payment_method'] = '-';
        $data['data_order_status'] = '-';

        $data['text_order_id'] = $this->language->get('text_order_id');
        $data['text_store_name'] = $this->language->get('entry_store');
        $data['text_firstname'] = $this->language->get('entry_firstname');
        $data['text_lastname'] = $this->language->get('entry_lastname');
        $data['text_email'] = $this->language->get('text_email');
        $data['column_total'] = $this->language->get('column_total');
        $data['text_date_added'] = $this->language->get('text_date_added');
        $data['text_payment_method'] = $this->language->get('text_payment_method');
        $data['text_order_status'] = $this->language->get('entry_order_status');
        $data['text_capture_success'] = $this->language->get('capture_success');
        $data['text_capture_failure'] = $this->language->get('capture_failure');
        $data['text_refund_success'] = $this->language->get('refund_success');
        $data['text_refund_failure'] = $this->language->get('refund_failure');

        if($order_info){
            $data['data_orderId'] = $orderId;
            $data['data_storename'] = $order_info['store_name'];
            $data['data_customer_firstname'] = $order_info['firstname'];
            $data['data_customer_lastname'] = $order_info['lastname'];
            $data['data_customer_email'] = $order_info['email'];
            $data['data_order_total'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);
            $data['data_order_date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
            $data['data_order_payment_method'] = $order_info['payment_method'];
            $order_status_info = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);
            $data['data_order_status'] = $order_status_info ? $order_status_info['name']:'';
        }

        $url_capture = $this->url->link('custom/paymillOrder/capture', '', 'SSL');
        $data['url_capture'] = $url_capture .'&token=' . $this->session->data['token'] . '&orderId='.$orderId;

        $url_refund = $this->url->link('custom/paymillOrder/refund', '', 'SSL');
        $data['url_refund'] = $url_refund .'&token=' . $this->session->data['token'] . '&orderId='.$orderId;


        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $data['column_left'] = $this->load->controller('common/column_left');

      return   $this->response->setOutput($this->load->view('custom/paymillOrder', $data));


    }

    public function capture(){
        $result = false;
        $orderId = $this->getPost('orderId', 0);
        $details = $this->getOrderDetails($orderId);
        if(!is_null($details) && array_key_exists('preauth_id', $details) && empty($details['transaction_id'])){
            $result = $this->proceedCapture($details['preauth_id']);
        }
        echo $result ? 'OK' : 'NOK';
    }

    private function proceedCapture($preauth_id){
        $result = false;
        $this->init();
        $this->load->model('sale/order');
        $orderId = $this->getPost('orderId', 0);
        $preauth = $this->paymillPreauth->getOne($preauth_id);
            error_log("\n   hyra  1 " . print_r($preauth, 1) , 3, "/var/tmp/my-errors.log");
        if(is_array($preauth)){
            $this->paymillProcessor->setAmount($preauth['amount']);
            $this->paymillProcessor->setCurrency($preauth['currency']);
            $this->paymillProcessor->setPreauthId($preauth_id);
            $this->paymillProcessor->setDescription('Capture '. $preauth_id);
            try{
                $result = $this->paymillProcessor->capture();
                $this->log('Capture resulted in', var_export($result,true));
                $this->log('Capture successfully', $this->paymillProcessor->getTransactionId());
                $this->saveTransactionId($orderId, $this->paymillProcessor->getTransactionId());
                $orderStatusId = $this->db->query('SELECT `order_status_id` FROM `' . DB_PREFIX . 'order_status` WHERE `name`= "Complete"')->row['order_status_id'];
                $this->addOrderHistory($orderId, array(
                    'order_status_id' => $orderStatusId,
                    'notify' => false,
                    'comment' => ''
                ));
            } catch (Exception $ex) {
                $result = false;
            }
        }
        return $result;
    }

    public function refund(){
 
        $result = false;
        $orderId = $this->getPost('orderId', 0);
        $details = $this->getOrderDetails($orderId);
        if(!is_null($details) && array_key_exists('transaction_id', $details)){
            $result = $this->proceedRefund($details['transaction_id']);
        }
        echo $result ? 'OK' : 'NOK';
    }

    private function proceedRefund($transactionId){
        $result = false;
        $this->init();
        $orderId = $this->getPost('orderId', 0);
        $transaction = $this->paymillTransaction->getOne($transactionId);
        $this->log('Transaction used for Refund', var_export($transaction, true));
        if(is_array($transaction)){
            try{
                $result = $this->paymillRefund->create(array(
                    'transactionId' => $transactionId,
                    'params' => array(
                        'amount' => $transaction['origin_amount']
                    )
                ));
                $this->log('Refund resulted in', var_export($result,true));
                $this->log('Refund successfully', $transaction['id']);
                $orderStatusId = $this->db->query('SELECT `order_status_id` FROM `' . DB_PREFIX . 'order_status` WHERE `name`= "Refunded"')->row['order_status_id'];
                 $this->addOrderHistory($orderId, array(
                    'order_status_id' => $orderStatusId,
                    'notify' => true,
                    'comment' => ''
                ));
            } catch (Exception $ex) {
                $result = false;
                         
            }

        }
        return $result;
        
    }

    private function addOrderHistory($order_id, $data) {
    
      $this->load->model('sale/order');
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$data['order_status_id'] . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$data['order_status_id'] . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");

        $order_info = $this->model_sale_order->getOrder($order_id);

        // Send out any gift voucher mails
        if ($this->config->get('config_complete_status_id') == $data['order_status_id']) {
            $this->load->model('sale/voucher');

            $results = $this->getOrderVouchers($order_id);
            
            foreach ($results as $result) {
                $this->model_sale_voucher->sendVoucher($result['voucher_id']);
            }
        }

        if ($data['notify']) {
              
            $language = new Language();
            $language->load('mail/order');

            $subject = sprintf($language->get('text_subject'), $order_info['store_name'], $order_id);

            $message  = $language->get('text_order') . ' ' . $order_id . "\n";
            $message .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";
            
            $order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$data['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
                
            if ($order_status_query->num_rows) {
                $message .= $language->get('text_order_status') . "\n";
                $message .= $order_status_query->row['name'] . "\n\n";
            }
            
            if ($order_info['customer_id']) {
                $message .= $language->get('text_link') . "\n";
                $message .= html_entity_decode($order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id, ENT_QUOTES, 'UTF-8') . "\n\n";
            }
            
            if ($data['comment']) {
                $message .= $language->get('text_comment') . "\n\n";
                $message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
            }

            $message .= $language->get('text_footer');

            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->hostname = $this->config->get('config_smtp_host');
            $mail->username = $this->config->get('config_smtp_username');
            $mail->password = $this->config->get('config_smtp_password');
            $mail->port = $this->config->get('config_smtp_port');
            $mail->timeout = $this->config->get('config_smtp_timeout');
            $mail->setTo($order_info['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($order_info['store_name']);
            $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
            $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
            $mail->send();
        }
    }
   

    private function getOrderDetails($orderId){
        $where = 'WHERE order_id ='. $this->db->escape($orderId);
        $result = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'pigmbh_paymill_orders` ' . $where);
        if($result->num_rows === 1){
            return $result->row;
        }
    }

    private function saveTransactionId($orderId, $id){
        $where = 'WHERE `order_id` = '. $this->db->escape($orderId);
        $this->db->query('UPDATE `'.DB_PREFIX.'pigmbh_paymill_orders` SET `transaction_id` = "'.$this->db->escape($id).'" ' . $where);
    }

    /**
     * Logger for events
     * @return void
     */
    public function log($message, $debuginfo)
    {
        if ($this->config->get('paymillcreditcard_logging')) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "pigmbh_paymill_logging` (`identifier`,`debug`,`message`) VALUES ('" . $this->logId . "', '" . $this->db->escape($debuginfo) . "', '" . $this->db->escape($message) . "')");
        }
    }

}
