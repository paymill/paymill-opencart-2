<?php echo $header; ?><?php echo $column_left; ?>

 <div id="content">

               <div class="page-header">
                  <div class="container-fluid">
                
                    <h1><?php echo $headingTitle; ?></h1>
                        <div class="breadcrumb" align="left">
                            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                            <?php } ?>
                        </div>
                  </div>
              </div>



            <link rel="stylesheet" type="text/css" href="<?php echo $paymillCSS; ?>" />
            <script type="text/javascript" src="<?php echo $paymillJS; ?>"></script>

    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
                        <div class="row">
                             <div class="col-sm-3">
                            <input type="search" name="searchValue" value="<?php echo $paymillInputSearch;?>" class="form-control">
                                 </div>
                                 <div class="col-sm-2">
                            <input type="checkbox" name="connectedSearch" <?php if($paymillCheckboxConnectedSearch == "on"){ echo "checked"; }?> > <?php echo $paymillCheckboxConnectedSearch; ?>
                                  </div>
                                 <div class="col-sm-1">
                                  <input type='number' id='paymillGoToPage' min='0' max='<?php echo $paymillPageMax;?>' value='<?php echo $paymillPage;?>'> / <?php echo $paymillPageMax;?>
                                  </div>
                                                   
                           <div class="col-sm-1">
                                 <button type="button"  onclick="ChangePage();" class="btn btn-default"> <span><?php echo $button_search; ?></span></button>
                                </div>


                                 <div class="col-sm-1">
                                 <button type="button" onclick="submitForm('delete');" class="btn btn-danger"> <span><?php echo $button_delete; ?></span></button>
                                </div>
                         <div class="pull-right">
                             <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
                            </div>
                      </div>
                          


                 
                </div>
            <div class="panel-body">
                 <form method="POST" action="<?php echo $paymillAction;?>" enctype="multipart/form-data" id="paymillForm">
                        <input type='hidden' name='page' value='<?php echo $paymillPage; ?>'/>
                        <div class="box">
                            <div class="content">
                                 <div class="table-responsive"
>                                <table class="list">
                                    <thead>
                                        <tr>
                                            <td width="1" style="text-align: center;">
                                                <input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);">
                                            </td>
                                            <td class="left"><?php echo $paymillTableHeadDate; ?></td>
                                            <td class="left"><?php echo $paymillTableHeadID; ?></td>
                                            <td class="left"><?php echo $paymillTableHeadMessage; ?></td>
                                            <td class="left"><?php echo $paymillTableHeadDebug; ?></td>
                                        </tr>
                                    </thead>
                                    <?php foreach($paymillEntries as $id => $row){ ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <input type="checkbox" name="selected[]" value="<?php echo $row['id']; ?>">
                                        </td>
                                        <td class="left"><?php echo $row['date']; ?></td>
                                        <td class="left"><?php echo $row['identifier']; ?></td>
                                        <td class="left"><?php echo $row['message']; ?></td>
                                        <?php if(strlen($row['debug']) > 200){ ?>
                                        <td class="left">

                                            <?php
                                            echo "<a onclick='showDetails(\"".urlencode($row['debug'])."\");' class='btn btn-default'>";
                                            echo "<span>$paymillTableShowDetails</span>";
                                            echo "</a>";
                                            ?>
                                        </td>
                                        <?php }else{ ?>
                                        <td class="left"><pre><?php echo $row['debug']; ?></pre></td>
                                        <?php } ?>
                                    </tr>
                                    <?php } ?>
                                </table>
                                <table class="list" id="paymillDetail">
                                    <thead>
                                        <tr>
                                            <td><?php echo $paymillTableHeadDetail; ?></td>
                                        </tr>
                                    </thead>
                                    <tr>
                                        <td id="paymillDetailContent">&nbsp;</td>
                                    </tr>
                                </table>
                            </div>
                         </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>
