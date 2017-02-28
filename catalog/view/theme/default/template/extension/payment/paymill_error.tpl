<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>


  <div id="content">
   <div class="page-header">
      <div class="container-fluid">
         
        <h1> <center><?php echo $heading_title; ?></center></h1>
       
      </div>
  </div>

        <div class="container-fluid">
           <table>
                <tr>
               <div class="alert alert-danger fade in">
                <center >
                    <?php echo $error_message; ?>
                </center>
                </div>
                </tr>
               <tr>
              <center>
                <a onclick="location = '<?php echo str_replace('&', '&amp;', $cart); ?>'" class="btn btn-primary"><span><?php echo $button_viewcart; ?></span></a>
               </center>
                </tr>
            </table>
        </div>


</div>
<?php echo $footer; ?>


