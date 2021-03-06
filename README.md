PAYMILL-OpenCart 2 Extension for credit card and direct debit payments
====================

PAYMILL extension is compatible with: 2.3.0.0 , 2.3.0.1, 2.3.0.2 (tested for 2.3.0.2). This extension installs two payment methods: Credit card and direct debit.

## Your Advantages
* PCI DSS compatibility
* Payment means: Credit Card (Visa, Visa Electron, Mastercard, Maestro, Diners, Discover, JCB, AMEX), Direct Debit (ELV)
* Optional fast checkout configuration allowing your customers not to enter their payment detail over and over during checkout
* Improved payment form with visual feedback for your customers
* Supported Languages: German, English, Portuguese, Italian, French, Spanish
* Backend Log with custom View accessible from your shop backend

## PayFrame
 We've introduced a "payment form" option for easier compliance with PCI requirements.
 In addition to having a payment form directly integrated in your checkout page, you can
 use our embedded PayFrame solution to ensure that payment data never touches your
 website.

 PayFrame is enabled by default, but you can choose between both options in the plugin
 settings. Later this year, we're bringing you the ability to customise the appearance and
 text content of the PayFrame version.

 To learn more about the benefits of PayFrame, please visit our [FAQ](https://www.paymill.com/en/faq/how-does-paymills-payframe-solution-work "FAQ").

## Installation from this git repository

Download the complete module by using the link below:

[Latest Version](https://github.com/Paymill/paymill-opencart-2/archive/master.zip)

######Please note that Github will add an additional folder.
To install the extension merge the content of the folder `paymill-opencart-master` with your Opencart installation.

## Configuration

Go to Extentions > Payments and `install` your favorite payment method.
Afterwards you can click `edit` to enter your configuration.

Be sure to set the permission for the following pages. 
To do so please navigate in your Shop backened to "System" -> "Users" -> "User Groups" and edit the Administrator rights. You can search for these with ctrl+f (cmd+f on mac): 
* `custom/paymillLogging` -> shows you all log entries
* `custom/paymillOrder` -> offers additional payment actions ( capture & refund )
* `payment/paymillcreditcard` -> configuration page for creditcard
* `payment/paymilldirectdebit` -> configuration page for directdebit

## Notes about the payment process

The payment is processed when an order is placed in the shop frontend.
An invoice is being generated automatically.

There are several options altering this process:

Fast Checkout: Fast checkout can be enabled by selecting the option in the PAYMILL Settings. If any customer completes a purchase while the option is active this customer will not be asked for data again. Instead a reference to the customer data will be saved allowing comfort during checkout.

## In case of errors

In case of any errors turn on the debug mode and logging in the PAYMILL Settings. Open the javascript console in your browser and check what's being logged during the checkout process. To access the logged information not printed in the console please click the `Logging`-button within your configuration.

## Adding capture / refund

Open the file `admin/view/template/sale/order_info.tpl`.
Search for the following code
```php
              <tr>
                <td><?php echo $text_affiliate; ?>
                  <?php if ($affiliate) { ?>
                  (<a href="<?php echo $affiliate; ?>"><?php echo $affiliate_firstname; ?> <?php echo $affiliate_lastname; ?></a>)
                  <?php } ?></td>
                <td class="text-right"><?php echo $commission; ?></td>
                <td class="text-center"><?php if ($affiliate) { ?>
                  <?php if (!$commission_total) { ?>
                  <button id="button-commission-add" data-loading-text="<?php echo $text_loading; ?>" data-toggle="tooltip" title="<?php echo $button_commission_add; ?>" class="btn btn-success btn-xs"><i class="fa fa-plus-circle"></i></button>
                  <?php } else { ?>
                  <button id="button-commission-remove" data-loading-text="<?php echo $text_loading; ?>" data-toggle="tooltip" title="<?php echo $button_commission_remove; ?>" class="btn btn-danger btn-xs"><i class="fa fa-minus-circle"></i></button>
                  <?php } ?>
                  <?php } else { ?>
                  <button disabled="disabled" class="btn btn-success btn-xs"><i class="fa fa-plus-circle"></i></button>
                  <?php } ?></td>
              </tr>
```

Add the folowing code right atfer the previous code
```php
<?php
    require_once(DIR_SYSTEM."../paymill/admin/template/paymentActions.tpl");
?>
```

Open the file `admin/controller/sale/order.php`
Search for the following code
```php
public function info() {
    $this->load->model('sale/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$order_info = $this->model_sale_order->getOrder($order_id);

		if ($order_info) {
```
and add the following code below it
```php
    $data['paymillshow'] = preg_match('/^paymill.*$/', $order_info['payment_code']);
    $data['paymillURL'] = $this->url->link('custom/paymillOrder', '&token=' . $this->session->data['token'] .'&orderId='.$order_id);
```
