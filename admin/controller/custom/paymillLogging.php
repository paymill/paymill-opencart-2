<?php

/**
 * @copyright  Copyright (c) 2015 PAYMILL GmbH (http://www.paymill.com)
 */
class ControllerCustomPaymillLogging extends Controller
{

    private function getPost($name, $default = null)
    {
         
        $value = $default;
        if (isset($this->request->post[$name])) {
            $value = $this->request->post[$name];
        }
        return $value;
    }

    public function index()
    {     
        $this->load->model('custom/paymillLogging');
        $this->language->load('custom/paymillLogging');
        //Get Post Vars
        $connectedSearch = $this->getPost("connectedSearch", "off");
        $searchValue = $this->getPost("searchValue", "");
        $actualPage = (int) $this->getPost("page", 0);
        $selectedIds = $this->getPost("selected");

        if($actualPage <= 0){
            $actualPage = 1;
          }

        if ($this->getPost("button", "search") === "delete" && is_array($selectedIds)) {
            $this->model_custom_paymillLogging->deleteEntries($selectedIds);
            }


        $this->model_custom_paymillLogging->setSearchValue($searchValue);
        $this->model_custom_paymillLogging->setConnectedSearch($connectedSearch);
        $data['paymillEntries'] = $this->model_custom_paymillLogging->getEntries($actualPage);
        $data['paymillInputSearch'] = $searchValue;
        $data['paymillCheckboxConnectedSearch'] = $connectedSearch;

        $maxPages = (int)floor($this->model_custom_paymillLogging->getTotal() / $this->model_custom_paymillLogging->getPageSize());

        $data['paymillPage'] = $actualPage;
        $data['paymillPageMax'] = $maxPages;

        $this->baseUrl = preg_replace("/\/index\.php/", "", $this->request->server['SCRIPT_NAME']);
        $data['breadcrumbs'] = $this->_getBreadcrumbs();
        $data['paymillCSS'] = $this->baseUrl . '/../catalog/view/theme/default/stylesheet/paymill_styles.css';
        $data['paymillJS'] = $this->baseUrl . '/../catalog/view/javascript/paymill/loggingOverview.js';
        $data['paymillAction'] = $this->url->link('custom/paymillLogging', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token']. '&type=payment', true);
 
        $this->document->setTitle($this->language->get('headingTitle'));
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_search'] = $this->language->get('button_search');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['headingTitle'] = $this->language->get('headingTitle');
        $data['paymillTableHeadDate'] = $this->language->get('paymillTableHeadDate');
        $data['paymillTableHeadID'] = $this->language->get('paymillTableHeadID');
        $data['paymillTableHeadMessage'] = $this->language->get('paymillTableHeadMessage');
        $data['paymillTableHeadDebug'] = $this->language->get('paymillTableHeadDebug');
        $data['paymillTableHeadDetail'] = $this->language->get('paymillTableHeadDetail');
        $data['paymillTableShowDetails'] = $this->language->get('paymillTableShowDetails');
        $data['paymillCheckboxConnectedSearch'] = $this->language->get('paymillCheckboxConnectedSearch');

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $data['column_left'] = $this->load->controller('common/column_left');

      return   $this->response->setOutput($this->load->view('custom/paymillLogging', $data));
    }

    protected function _getBreadcrumbs()
    {
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], true),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $breadcrumbs[] = array(
            'href' => $this->url->link('custom/paymillLogging', 'token=' . $this->session->data['token'], true),
            'text' => $this->language->get('headingTitle'),
            'separator' => ' :: '
        );
        return $breadcrumbs;
    }


}
