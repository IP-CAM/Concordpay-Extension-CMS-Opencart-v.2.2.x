<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?>
        <li><a
                    href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>

    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-concordpay" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><i class="fa fa-credit-card"></i> <?php echo $heading_title; ?></h1>
        </div>
    </div>

    <div class="container-fluid">
        <div class="panel-body">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-concordpay"
                  class="form-horizontal">
                <div class="form-group required">
                    <label class="col-sm-4 control-label"><?php echo $entry_merchant; ?></label>

                    <div class="col-sm-8"><input type="text" name="concordpay_merchant"
                                                 value="<?php echo $concordpay_merchant; ?>" class="form-control"/>
                        <?php if ($error_merchant) { ?>
                        <div class="text-danger"><?php echo $error_merchant; ?></div>
                        <?php } ?></div>
                </div>
                <div class="form-group required">
                    <label class="col-sm-4 control-label"><?php echo $entry_secretkey; ?></label>

                    <div class="col-sm-8"><input type="text" name="concordpay_secretkey"
                                                 value="<?php echo $concordpay_secretkey; ?>" class="form-control"/>
                        <?php if ($error_secretkey) { ?>
                        <div class="text-danger"><?php echo $error_secretkey; ?></div>
                        <?php } ?></div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo $entry_currency; ?></label>

                    <div class="col-sm-8"><input type="text" name="concordpay_currency"
                                                 value="<?php echo ($concordpay_currency == "") ? "UAH" :
                        $concordpay_currency; ?>" class="form-control"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo $entry_language; ?></label>

                    <div class="col-sm-8"><input type="text" name="concordpay_language"
                                                 value="<?php echo ($concordpay_language == "") ?
                        "RU" : $concordpay_language; ?>" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo $entry_order_status; ?></label>

                    <div class="col-sm-8"><select name="concordpay_order_status_id" class="form-control">
                            <?php
                                foreach ($order_statuses as $order_status) {

                                $st = ($order_status['order_status_id'] == $concordpay_order_status_id) ? ' selected="selected" ' : "";
                                ?>
                            <option value="<?php echo $order_status['order_status_id']; ?>"
                            <?= $st ?> ><?php echo $order_status['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo $entry_status; ?></label>

                    <div class="col-sm-8"><select name="concordpay_status" class="form-control">
                            <?php $st0 = $st1 = "";
                                 if ( $concordpay_status == 0 ) $st0 = 'selected="selected"';
                                  else $st1 = 'selected="selected"';
                                ?>
                            <option value="1"
                            <?= $st1 ?> ><?php echo $text_enabled; ?></option>
                            <option value="0"
                            <?= $st0 ?> ><?php echo $text_disabled; ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label"><?php echo $entry_sort_order; ?></label>

                    <div class="col-sm-4"><input type="text" name="concordpay_sort_order"
                                                 value="<?php echo $concordpay_sort_order; ?>" class="form-control"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<?php echo $footer; ?>