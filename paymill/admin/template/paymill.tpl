  <?php echo $header; ?><?php echo $column_left; ?>
  <div id="content">

   <div class="page-header">
      <div class="container-fluid">
        <div class="pull-right">
          <button type="submit"  form="form-creditcard"  data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
          <a onclick="location = '<?php echo $logging; ?>';" class="btn btn-default"><span><?php echo $button_logging; ?></span>  </a>
          <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
          </div>

        <h1><?php echo $heading_title; ?></h1>
          <div class="breadcrumb" align="left">
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
            <?php } ?>
         </div>
      </div>
  </div>


   <div class="container-fluid">
      <div style="text-align:center; margin:5px;color:red;font-size: large;">
                    <?php echo isset($error_warning)?$error_warning:''; ?>
        </div>
           <div class="panel panel-default">
                <div class="panel-heading">
                  <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
                </div>


               <div class="panel-body">
                  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-creditcard" class="form-horizontal">   
                                          
                      <div class="form-group">
                       <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                          <select name="paymill_status" id="input-status" class="form-control">
                          <option value="1" <?php if ($paymill_status) { echo 'selected="selected"';}?>> <?php echo $text_enabled; ?></option>
                         <option value="0" <?php if (!$paymill_status) { echo 'selected="selected"';}?>> <?php echo $text_disabled; ?></option>
                          </select>
                        </div>
                      </div>
                                      

                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-privatekey"><?php echo $entry_privatekey; ?></label>
                            <div class="col-sm-10">
                              <input type="text" name="paymill_privatekey" value="<?php echo $paymill_privatekey; ?>"  id="input-privatekey" class="form-control" />
                               <?php if ($error_privatekey) { ?>
                              <div class="text-danger"><?php echo $error_privatekey; ?></div>
                              <?php } ?>
                            </div>
                         </div>
                                            
                         <div class="form-group required">
                            <label class="col-sm-2 control-label" for="input-publickey"><?php echo $entry_publickey; ?></label>
                            <div class="col-sm-10">
                              <input type="text" name="paymill_publickey" value="<?php echo $paymill_publickey; ?>"  id="input-publickey" class="form-control" />
                                <?php if ($error_publickey) { ?>
                              <div class="text-danger"><?php echo $error_publickey; ?></div>
                              <?php } ?>
                            </div>
                         </div>          

                         <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-pci"><?php echo $entry_pci; ?></label>
                        <div class="col-sm-10">
                          <select name="paymill_pci" id="input-pci" class="form-control">
                          <option value="1" <?php if ($paymill_pci) { echo 'selected="selected"';}?>> <?php echo $text_pci_saq_a_ep; ?></option>
                         <option value="0" <?php if (!$paymill_pci) { echo 'selected="selected"';}?>> <?php echo $text_pci_saq_a; ?></option>
                          </select>
                        </div>
                      </div>


                     <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="paymill_sort_order" value="<?php echo $paymill_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                      </div>


                      <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-fast_checkout"><?php echo $entry_fast_checkout; ?></label>
                        <div class="col-sm-10">
                          <select name="paymill_fast_checkout" id="input-fast_checkout" class="form-control">
                          <option value="1" <?php if ($paymill_fast_checkout) { echo 'selected="selected"';}?>> <?php echo $text_enabled; ?></option>
                         <option value="0" <?php if (!$paymill_fast_checkout) { echo 'selected="selected"';}?>> <?php echo $text_disabled; ?></option>
                          </select>
                        </div>
                      </div>

                     <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-logging"><?php echo $entry_logging; ?></label>
                        <div class="col-sm-10">
                          <select name="paymill_logging" id="input-logging" class="form-control">
                          <option value="1" <?php if ($paymill_logging) { echo 'selected="selected"';}?>> <?php echo $text_enabled; ?></option>
                         <option value="0" <?php if (!$paymill_logging) { echo 'selected="selected"';}?>> <?php echo $text_disabled; ?></option>
                          </select>
                        </div>
                      </div>

                     <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-debugging"><?php echo $entry_debugging; ?></label>
                        <div class="col-sm-10">
                          <select name="paymill_debugging" id="input-debugging" class="form-control">
                          <option value="1" <?php if ($paymill_debugging) { echo 'selected="selected"';}?>> <?php echo $text_enabled; ?></option>
                         <option value="0" <?php if (!$paymill_debugging) { echo 'selected="selected"';}?>> <?php echo $text_disabled; ?></option>
                          </select>
                        </div>
                      </div>

                       <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-buttonSolution"><?php echo $entry_buttonSolution; ?></label>
                        <div class="col-sm-10">
                          <select name="paymill_buttonSolution" id="input-buttonSolution" class="form-control">
                          <option value="1" <?php if ($paymill_buttonSolution) { echo 'selected="selected"';}?>> <?php echo $text_enabled; ?></option>
                         <option value="0" <?php if (!$paymill_buttonSolution) { echo 'selected="selected"';}?>> <?php echo $text_disabled; ?></option>
                          </select>
                        </div>
                      </div>


                     <?php if($paymill_payment === 'paymilldirectdebit'){ ?>

                     <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sepa-date"><?php echo $entry_sepa_date; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="paymill_sepa_date" value="<?php echo $paymill_sepa_date; ?>" id="input-sepa-date" class="form-control" />
                        </div>
                      </div>

                            <input type="hidden" value="0" name="icon_visa">
                            <input type="hidden" value="0" name="icon_master">
                            <input type="hidden" value="0" name="icon_amex">
                            <input type="hidden" value="0" name="icon_jcb">
                            <input type="hidden" value="0" name="icon_maestro">
                            <input type="hidden" value="0" name="icon_diners_club">
                            <input type="hidden" value="0" name="icon_discover">
                            <input type="hidden" value="0" name="icon_china_unionpay">
                            <input type="hidden" value="0" name="icon_dankort">
                            <input type="hidden" value="0" name="icon_carta_si">
                            <input type="hidden" value="0" name="icon_carte_bleue">
                            <input type="hidden" value="0" name="paymill_preauth">
                          <?php } else { ?>

                       <div class="form-group">
                          <label class="col-sm-2 control-label" for="input-preauth"><?php echo $entry_preauth; ?></label>
                        <div class="col-sm-10">
                          <select name="paymill_preauth" id="input-preauth" for="paymill_preauth_amount" class="form-control">
                          <option value="1" <?php if ($paymill_preauth) { echo 'selected="selected"';}?>> <?php echo $text_enabled; ?></option>
                         <option value="0" <?php if (!$paymill_preauth) { echo 'selected="selected"';}?>> <?php echo $text_disabled; ?></option>
                          </select>
                        </div>
                      </div>
                       <div class="form-group" name="paymill_preauth_div" id="paymill_preauth_div" style="display=none;" >
                          <label class="col-sm-2 control-label" for="input-preauth-amount"><?php echo $entry_preauth_amount; ?></label>
                        <div class="col-sm-10">
                        <input type="text" name="paymill_preauth_amount" value="<?php echo $paymill_preauth_amount; ?>" id="input-preauth-amount" class="form-control" />
                        </div>
                      </div>
                  

                          <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-preauth"><?php echo $entry_specific_creditcard; ?></label>
                              <div class="col-sm-10">
                              <input type="checkbox" value="1" name="icon_visa" <?php if($paymill_icon_visa){ echo 'checked';}?>><img src="<?php echo $base . $paymill_image_folder; ?>/32x20_visa.png">
                              <input type="checkbox" value="1" name="icon_master" <?php if($paymill_icon_master){ echo 'checked';}?>><img src="<?php echo $base . $paymill_image_folder; ?>/32x20_mastercard.png"><br>
                              <input type="checkbox" value="1" name="icon_amex" <?php if($paymill_icon_amex){ echo 'checked';}?>><img src="<?php echo $base . $paymill_image_folder; ?>/32x20_amex.png">
                              <input type="checkbox" value="1" name="icon_jcb" <?php if($paymill_icon_jcb){ echo 'checked';}?>><img src="<?php echo $base . $paymill_image_folder; ?>/32x20_jcb.png"><br>
                              <input type="checkbox" value="1" name="icon_maestro" <?php if($paymill_icon_maestro){ echo 'checked';}?>><img src="<?php echo $base . $paymill_image_folder; ?>/32x20_maestro.png">
                              <input type="checkbox" value="1" name="icon_diners_club" <?php if($paymill_icon_diners_club){ echo 'checked';}?>><img src="<?php echo $base . $paymill_image_folder; ?>/32x20_dinersclub.png"><br>
                              <input type="checkbox" value="1" name="icon_discover" <?php if($paymill_icon_discover){ echo 'checked';}?>><img src="<?php echo $base . $paymill_image_folder; ?>/32x20_discover.png">
                              <input type="checkbox" value="1" name="icon_china_unionpay" <?php if($paymill_icon_china_unionpay){ echo 'checked';}?>><img src="<?php echo $base . $paymill_image_folder; ?>/32x20_unionpay.png"><br>
                              <input type="checkbox" value="1" name="icon_dankort" <?php if($paymill_icon_dankort){ echo 'checked';}?>><img src="<?php echo $base . $paymill_image_folder; ?>/32x20_dankort.png">
                              <input type="checkbox" value="1" name="icon_carta_si" <?php if($paymill_icon_carta_si){ echo 'checked';}?>><img src="<?php echo $base . $paymill_image_folder; ?>/32x20_carta-si.png"><br>
                              <input type="checkbox" value="1" name="icon_carte_bleue" <?php if($paymill_icon_carte_bleue){ echo 'checked';}?>><img src="<?php echo $base . $paymill_image_folder; ?>/32x20_carte-bleue.png">
                              </div>
                            </div>
                              <input type="hidden" value="0" name="paymill_sepa_date">
                               <?php } ?>
                       
                      </form>
              </div>
          </div>
      </div>
  </div>
  <script>
  $('#input-preauth').on('change',function(){
	var selection = $(this).val();
    switch(selection){
		case '1' : $('#paymill_preauth_div').show();
			break;
		default : $('#paymill_preauth_div').hide();
    }
  });
  </script>
  <?php echo $footer; ?>