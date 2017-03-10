<?php
require_once dirname(dirname(dirname(__FILE__))) . '/Metadata.php';
require_once dirname(dirname(dirname(__FILE__))) .
         '/lib/Services/Paymill/Webhooks.php';

/**
 *
 * @copyright Copyright (c) 2015 PAYMILL GmbH (http://www.paymill.com)
 */
abstract class ControllerPaymentPaymill extends Controller
{
    abstract protected function getPaymentName ();

    private $error = array();

    public function getVersion ()
    {
        $metadata = new Metadata();
        return $metadata->getVersion();
    }

    public function index ()
    {
        global $config;
        $this->language->load('extension/payment/' . $this->getPaymentName());
        $this->document->setTitle(
                $this->language->get('heading_title') . " (" .
                         $this->getVersion() . ")");
        if (isset($this->request->server['HTTPS']) &&
                 (($this->request->server['HTTPS'] == 'on') ||
                 ($this->request->server['HTTPS'] == '1'))) {
            $data['base'] = $this->config->get('config_ssl');
        } elseif (! is_null($this->config->get('config_url'))) {
            $data['base'] = $this->config->get('config_url');
        } else {
            $data['base'] = preg_replace("/admin\/index\.php/", "", 
                    $this->request->server['SCRIPT_NAME']); // shoproot
        }
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') &&
                 $this->validate()) {
            $this->load->model('setting/setting');
            
            $newConfig[$this->getPaymentName() . '_status'] = $this->getPostValue(
                    'paymill_status', 0);
            $newConfig[$this->getPaymentName() . '_publickey'] = trim(
                    $this->getPostValue('paymill_publickey', ''));
            $newConfig[$this->getPaymentName() . '_privatekey'] = trim(
                    $this->getPostValue('paymill_privatekey', ''));
            if ($this->getPaymentName() === 'paymillcreditcard') {
                $newConfig[$this->getPaymentName() . '_pci'] = trim(
                        $this->getPostValue('paymill_pci', ''));
            }
            $newConfig[$this->getPaymentName() . '_sort_order'] = $this->getPostValue('paymill_sort_order', 0);
            $newConfig[$this->getPaymentName() . '_preauth'] = $this->getPostValue('paymill_preauth', false);
            $newConfig[$this->getPaymentName() . '_preauth_amount'] = $this->getPostValue('paymill_preauth_amount', false);
            $newConfig[$this->getPaymentName() . '_fast_checkout'] = $this->getPostValue('paymill_fast_checkout', false);
            $newConfig[$this->getPaymentName() . '_logging'] = $this->getPostValue('paymill_logging', false);
            $newConfig[$this->getPaymentName() . '_debugging'] = $this->getPostValue('paymill_debugging', false);
            $newConfig[$this->getPaymentName() . '_buttonSolution'] = $this->getPostValue('paymill_buttonSolution', false);
            $newConfig[$this->getPaymentName() . '_sepa_date'] = $this->getPostValue('paymill_sepa_date');
            $newConfig[$this->getPaymentName() . '_icon_visa'] = $this->getPostValue('icon_visa');
            $newConfig[$this->getPaymentName() . '_icon_master'] = $this->getPostValue('icon_master');
            $newConfig[$this->getPaymentName() . '_icon_amex'] = $this->getPostValue('icon_amex');
            $newConfig[$this->getPaymentName() . '_icon_jcb'] = $this->getPostValue('icon_jcb');
            $newConfig[$this->getPaymentName() . '_icon_maestro'] = $this->getPostValue('icon_maestro');
            $newConfig[$this->getPaymentName() . '_icon_diners_club'] = $this->getPostValue('icon_diners_club');
            $newConfig[$this->getPaymentName() . '_icon_discover'] = $this->getPostValue('icon_discover');
            $newConfig[$this->getPaymentName() . '_icon_china_unionpay'] = $this->getPostValue('icon_china_unionpay');
            $newConfig[$this->getPaymentName() . '_icon_dankort'] = $this->getPostValue('icon_dankort');
            $newConfig[$this->getPaymentName() . '_icon_carta_si'] = $this->getPostValue('icon_carta_si');
            $newConfig[$this->getPaymentName() . '_icon_carte_bleue'] = $this->getPostValue('icon_carte_bleue');

            $this->model_setting_setting->editSetting($this->getPaymentName(), $newConfig);
            $this->addPaymillWebhook($newConfig[$this->getPaymentName() . '_privatekey']);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
        }
        $data['text_edit'] = $this->language->get(
                'text_edit_' . $this->getPaymentName());
        $data['breadcrumbs'] = $this->getBreadcrumbs();
        $data['heading_title'] = $this->language->get('heading_title') . " (" .
                 $this->getVersion() . ")";
        $data['paymill_image_folder'] = '/catalog/view/theme/default/image/extension/payment';
        
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_payment'] = $this->language->get('text_payment');
        $data['text_success'] = $this->language->get('text_success');
        $data['text_paymill'] = $this->language->get('text_paymill');
        $data['text_sale'] = $this->language->get('text_sale');
        $data['text_sale'] = $this->language->get('text_sale');
        $data['text_pci_saq_a'] = $this->language->get('text_pci_saq_a');
        $data['text_pci_saq_a_ep'] = $this->language->get('text_pci_saq_a_ep');
        
        if (isset($this->error['error_missing_publickey'])) {
            $data['error_publickey'] = $this->error['error_missing_publickey'];
        } else {
            $data['error_publickey'] = '';
        }
        
        if (isset($this->error['error_missing_privatekey'])) {
            $data['error_privatekey'] = $this->error['error_missing_privatekey'];
        } else {
            $data['error_privatekey'] = '';
        }
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_publickey'] = $this->language->get('entry_publickey');
        $data['entry_privatekey'] = $this->language->get('entry_privatekey');
        $data['entry_pci'] = $this->language->get('entry_pci');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_fast_checkout'] = $this->language->get(
                'entry_fast_checkout');
        $data['entry_preauth'] = $this->language->get('entry_preauth');
        $data['entry_preauth_amount'] = $this->language->get('entry_preauth_amount');
        $data['entry_logging'] = $this->language->get('entry_logging');
        $data['entry_debugging'] = $this->language->get('entry_debugging');
        $data['entry_buttonSolution'] = $this->language->get(
                'entry_buttonSolution');
        $data['entry_sepa_date'] = $this->language->get('entry_sepa_date');
        $data['entry_specific_creditcard'] = $this->language->get(
                'entry_specific_creditcard');
        
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_logging'] = $this->language->get('button_logging');
        $data['action'] = $this->url->link(
                'extension/payment/' . $this->getPaymentName(), 
                'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 
                'token=' . $this->session->data['token'] . '&type=payment', true);
        $data['logging'] = $this->url->link('custom/paymillLogging', 
                'token=' . $this->session->data['token'], true);
        
        $data['paymill_status'] = $this->getConfigValue(
                $this->getPaymentName() . '_status');
        $data['paymill_publickey'] = $this->getConfigValue(
                $this->getPaymentName() . '_publickey');
        $data['paymill_privatekey'] = $this->getConfigValue(
                $this->getPaymentName() . '_privatekey');
        
        if ($this->getPaymentName() === 'paymillcreditcard') {
            $data['paymill_pci'] = $this->getConfigValue(
                    $this->getPaymentName() . '_pci');
        }

        $data['paymill_sort_order'] = $this->getConfigValue($this->getPaymentName() . '_sort_order');
        $data['paymill_fast_checkout'] = $this->getConfigValue($this->getPaymentName() . '_fast_checkout');
        $data['paymill_preauth'] = $this->getConfigValue($this->getPaymentName() . '_preauth');
        $data['paymill_preauth_amount'] = $this->getConfigValue($this->getPaymentName() . '_preauth_amount');
        $data['paymill_logging'] = $this->getConfigValue($this->getPaymentName() . '_logging');
        $data['paymill_debugging'] = $this->getConfigValue($this->getPaymentName() . '_debugging');
        $data['paymill_buttonSolution'] = $this->getConfigValue($this->getPaymentName() . '_buttonSolution');
        $data['paymill_sepa_date'] = $this->getConfigValue($this->getPaymentName() . '_sepa_date');
        $data['paymill_creditcardicons'] = $this->getConfigValue($this->getPaymentName() . '_creditcardicons');
        $data['paymill_payment'] = $this->getPaymentName();
        $data['paymill_icon_visa'] = $this->getConfigValue(
                $this->getPaymentName() . '_icon_visa');
        $data['paymill_icon_master'] = $this->getConfigValue(
                $this->getPaymentName() . '_icon_master');
        $data['paymill_icon_amex'] = $this->getConfigValue(
                $this->getPaymentName() . '_icon_amex');
        $data['paymill_icon_jcb'] = $this->getConfigValue(
                $this->getPaymentName() . '_icon_jcb');
        $data['paymill_icon_maestro'] = $this->getConfigValue(
                $this->getPaymentName() . '_icon_maestro');
        $data['paymill_icon_diners_club'] = $this->getConfigValue(
                $this->getPaymentName() . '_icon_diners_club');
        $data['paymill_icon_discover'] = $this->getConfigValue(
                $this->getPaymentName() . '_icon_discover');
        $data['paymill_icon_china_unionpay'] = $this->getConfigValue(
                $this->getPaymentName() . '_icon_china_unionpay');
        $data['paymill_icon_dankort'] = $this->getConfigValue(
                $this->getPaymentName() . '_icon_dankort');
        $data['paymill_icon_carta_si'] = $this->getConfigValue(
                $this->getPaymentName() . '_icon_carta_si');
        $data['paymill_icon_carte_bleue'] = $this->getConfigValue(
                $this->getPaymentName() . '_icon_carte_bleue');
        
        $data['config_compression'] = $this->config->get('config_compression');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput(
                $this->load->view('extension/payment/' . $this->getPaymentName(), 
                        $data));
    }

    protected function getBreadcrumbs ()
    {
        $breadcrumbs = array();
        $breadcrumbs[] = array(
                'href' => $this->url->link('common/home', 
                        'token=' . $this->session->data['token'], true),
                'text' => $this->language->get('text_home'),
                'separator' => FALSE
        );
        
        $breadcrumbs[] = array(
                'href' => $this->url->link('extension/extension', 
                        'token=' . $this->session->data['token'] .
                                 '&type=payment', true),
                'text' => $this->language->get('text_payment'),
                'separator' => ' :: '
        );
        
        $breadcrumbs[] = array(
                'href' => $this->url->link(
                        'extension/payment/' . $this->getPaymentName(), 
                        'token=' . $this->session->data['token'], true),
                'text' => $this->language->get('heading_title'),
                'separator' => ' :: '
        );
        return $breadcrumbs;
    }

    protected function getConfigValue ($configField)
    {
        if (isset($this->request->post[$configField])) {
            return $this->request->post[$configField];
        } else {
            return $this->config->get($configField);
        }
    }

    protected function getPostValue ($configField)
    {
        $result = $this->getConfigValue($configField);
        if (isset($this->request->post[$configField])) {
            $result = $this->request->post[$configField];
        }
        return $result;
    }

    protected function validate ()
    {
        $validation = true;
        $publickey = $this->request->post['paymill_publickey'];
        $privatekey = $this->request->post['paymill_privatekey'];
        
        if (! $this->user->hasPermission('modify', 
                'extension/payment/' . $this->getPaymentName())) {
            $data['error_warning'] = $this->language->get('error_permission');
            $validation = false;
        }
        
        if (isset($this->request->post['paymill_differnet_amount'])) {
            if (! is_numeric($this->request->post['paymill_differnet_amount'])) {
                $data['error_warning'] = $this->language->get(
                        'error_different_amount');
                $validation = false;
            }
        }
        
        if (empty($publickey)) {
            $this->error['error_missing_publickey'] = $this->language->get(
                    'error_missing_publickey');
            $validation = false;
        }
        
        if (empty($privatekey)) {
            $this->error['error_missing_privatekey'] = $this->language->get(
                    'error_missing_privatekey');
            $validation = false;
        }
        return $validation;
    }

    public function install ()
    {
        $config[$this->getPaymentName() . '_status'] = '0';
        $config[$this->getPaymentName() . '_publickey'] = '';
        $config[$this->getPaymentName() . '_privatekey'] = '';
        
        if ($this->getPaymentName() === 'paymillcreditcard') {
            $config[$this->getPaymentName() . '_pci'] = '0';
        }
        
        $config[$this->getPaymentName() . '_sort_order'] = '1';
        $config[$this->getPaymentName() . '_fast_checkout'] = '0';
        $config[$this->getPaymentName() . '_preauth'] = '0';
        $config[$this->getPaymentName() . '_preauth_amount'] = '0';
        $config[$this->getPaymentName() . '_different_amount'] = '0.00';
        $config[$this->getPaymentName() . '_logging'] = '1';
        $config[$this->getPaymentName() . '_debugging'] = '1';
        $config[$this->getPaymentName() . '_buttonSolution'] = '0';
        $config[$this->getPaymentName() . '_sepa_date'] = '7';
        $config[$this->getPaymentName() . '_icon_visa'] = '1';
        $config[$this->getPaymentName() . '_icon_master'] = '1';
        $config[$this->getPaymentName() . '_icon_amex'] = '1';
        $config[$this->getPaymentName() . '_icon_jcb'] = '1';
        $config[$this->getPaymentName() . '_icon_maestro'] = '1';
        $config[$this->getPaymentName() . '_icon_diners_club'] = '1';
        $config[$this->getPaymentName() . '_icon_discover'] = '1';
        $config[$this->getPaymentName() . '_icon_china_unionpay'] = '1';
        $config[$this->getPaymentName() . '_icon_dankort'] = '1';
        $config[$this->getPaymentName() . '_icon_carta_si'] = '1';
        $config[$this->getPaymentName() . '_icon_carte_bleue'] = '1';
        
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting($this->getPaymentName(), 
                $config);
        
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX .
                         "pigmbh_paymill_logging` (" .
                         "`id` int(11) NOT NULL AUTO_INCREMENT," .
                         "`identifier` text NOT NULL," . "`debug` text NOT NULL," .
                         "`message` text NOT NULL," .
                         "`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP," .
                         "PRIMARY KEY (`id`)" . ") AUTO_INCREMENT=1");
        
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX .
                         "pigmbh_paymill_orders` (" .
                         "`order_id` int(11) NOT NULL," .
                         "`preauth_id` varchar(100) NOT NULL," .
                         "`transaction_id` varchar(100) NOT NULL," .
                         "`refund_amount` DECIMAL(2) NOT NULL DEFAULT 0," .
                         "PRIMARY KEY (`order_id`)" . ")");
    }

    protected function addPaymillWebhook ($privateKey)
    {
        $webhookObject = new Services_Paymill_Webhooks($privateKey, Metadata::PAYMILL_API);
        $url = $this->url->link(
                'extension/payment/' . $this->getPaymentName() .
                         '/webHookEndpoint');
        $webhookUrl = str_replace('/admin', '', $url);
        $webhookObject->create(
                array(
                        "url" => $webhookUrl,
                        "event_types" => array(
                                'refund.succeeded'
                        )
                ));
    }

    public function uninstall ()
    {}
}