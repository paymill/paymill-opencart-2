<?php
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) .
         '/paymill/catalog/controller/paymill.php';

class ControllerExtensionPaymentPaymilldirectdebit extends ControllerPaymentPaymill
{

    protected function getPaymentName ()
    {
        return 'paymilldirectdebit';
    }

    protected function getDatabaseName ()
    {
        return 'paymill_dd_userdata';
    }
}
