<?php echo $header; ?>
<script src="view/javascript/jquery/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<link src="view/javascript/jquery/jquery-ui/jquery-ui.min.css" type="text/javascript"></link>
<link type="text/css" href="view/javascript/jquery/jquery-ui/jquery-ui.min.css" rel="stylesheet">
<script type="text/javascript" >
    $('document').ready(function(){
        $('#paymill_capture').click(function(){
            $.ajax({
                url: "<?php echo $url_capture;?>",
                success: function(result){
                    $( "#dialog-message" ).html("<?php echo $text_capture_failure; ?>");
                    if(result === 'OK'){
                        $( "#dialog-message" ).html("<?php echo $text_capture_success; ?>");
                    }
                    $( "#dialog-message" ).dialog({
                        title:'Capture',
                        modal: true,
                        buttons: {
                          Ok: function() {
                            $( this ).dialog( "close" );
                          }
                        }
                    });
                }
            });
        });
        $('#paymill_refund').click(function(){
            $.ajax({
                url: "<?php echo $url_refund;?>",
                success: function(result){
                    $( "#dialog-message" ).html("<?php echo $text_refund_failure; ?>");
                    if(result === 'OK'){
                        $( "#dialog-message" ).html("<?php echo $text_refund_success; ?>");
                    }
                    $( "#dialog-message" ).dialog({
                        title:'Refund',
                        modal: true,
                        buttons: {
                          Ok: function() {
                            $( this ).dialog( "close" );
                          }
                        }
                    });
                }
            });
        });
    });
</script>
<?php echo $column_left; ?>
<div id="content">

              <div class="page-header">
                  <div class="container-fluid">
                        <h1>Paymill Order Action</h1>
             </div>
             </div>

   <div class="panel panel-default">
              <div class="panel-heading">
              <div class="buttons">
                <div class="pull-right">
                    <a id="paymill_capture" class="btn btn-primary">Capture</a>
                    <a id="paymill_refund" class="btn btn-primary">Refund</a>
                </div>
                  </div> 
                <div class="breadcrumb" align="left">
                    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                    <?php } ?>
                </div>

               </div>
  <div class="panel-body">
        <div class="box">

            <div class="content">
                    <div class="table-responsive"> 
                <table class="table table-bordered table-hover">
                <tbody>
                    <tr>
                        <td><?php echo $text_order_id; ?></td>
                        <td><?php echo $data_orderId; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $text_store_name; ?></td>
                        <td><?php echo $data_storename; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $text_firstname; ?></td>
                        <td><?php echo $data_customer_firstname; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $text_lastname; ?></td>
                        <td><?php echo $data_customer_lastname; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $text_email; ?></td>
                        <td><?php echo $data_customer_email; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $column_total; ?></td>
                        <td><?php echo $data_order_total; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $text_date_added; ?></td>
                        <td><?php echo $data_order_date_added; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $text_payment_method; ?></td>
                        <td><?php echo $data_order_payment_method; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo $text_order_status; ?></td>
                        <td><?php echo $data_order_status; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
    <div id="dialog-message"></div>
    </div>
</div>


<?php echo $footer; ?>