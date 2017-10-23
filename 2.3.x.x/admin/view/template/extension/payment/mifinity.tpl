<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-mifinity" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
	<?php if ($error_warning) { ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
	<?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-mifinity" class="form-horizontal">
         <!-- <div class="form-group">
            <label class="col-sm-2 control-label" for="input-paymode"><?php echo $entry_paymode; ?></label>
            <div class="col-sm-10">
              <select name="mifinity_paymode" id="input-test" class="form-control">
				<?php if ($mifinity_paymode == 'iframe') { ?>
					<option value="iframe" selected="selected"><?php echo $text_iframe; ?></option>
					<option value="transparent"><?php echo $text_transparent; ?></option>
				<?php } else { ?>
					<option value="iframe"><?php echo $text_iframe; ?></option>
					<option value="transparent" selected="selected"><?php echo $text_transparent; ?></option>
				<?php } ?>
              </select>
            </div>
          </div> -->
            <input type="hidden" name="mifinity_paymode" value="iframe" />
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-test"><span data-toggle="tooltip" title="<?php echo $help_testmode; ?>"><?php echo $entry_test; ?></span></label>
            <div class="col-sm-10">
              <select name="mifinity_test" id="input-test" class="form-control">
				<?php if ($mifinity_test) { ?>
					<option value="1" selected="selected"><?php echo $text_yes; ?></option>
					<option value="0"><?php echo $text_no; ?></option>
				<?php } else { ?>
					<option value="1"><?php echo $text_yes; ?></option>
					<option value="0" selected="selected"><?php echo $text_no; ?></option>
				<?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-username"><span data-toggle="tooltip" title="<?php echo $help_username; ?>"><?php echo $entry_username; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="mifinity_username" value="<?php echo $mifinity_username; ?>" placeholder="<?php echo $entry_username; ?>" id="input-username" class="form-control"/>
			  <?php if ($error_username) { ?>
				  <div class="text-danger"><?php echo $error_username; ?></div>
			  <?php } ?>
            </div>
          </div>
            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-password">
                    <span data-toggle="tooltip"
                          title="<?php echo $help_password; ?>"><?php echo $entry_password; ?></span>
                </label>
                <div class="col-sm-10">
                    <input type="text" name="mifinity_password" value="<?php echo $mifinity_password; ?>"
                           placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control"/>
                    <?php if ($error_password) { ?>
                    <div class="text-danger"><?php echo $error_password; ?></div>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-api-key">
                    <span data-toggle="tooltip"
                          title="<?php echo $help_api_key; ?>"><?php echo $entry_api_key; ?></span>
                </label>
                <div class="col-sm-10">
                    <input type="text" name="mifinity_api_key" value="<?php echo $mifinity_api_key; ?>"
                           placeholder="<?php echo $entry_api_key; ?>" id="input-api-key" class="form-control"/>
                    <?php if ($error_api_key) { ?>
                    <div class="text-danger"><?php echo $error_api_key; ?></div>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-account-number-eur">
                    <span data-toggle="tooltip"
                          title="<?php echo $help_account_number_eur; ?>"><?php echo $entry_account_number_eur; ?></span>
                </label>
                <div class="col-sm-10">
                    <input type="text" name="mifinity_account_number_eur" value="<?php echo $mifinity_account_number_eur; ?>"
                           placeholder="<?php echo $entry_account_number_eur; ?>" id="input-account-number-eur" class="form-control"/>
                    <?php if ($error_account_number_eur) { ?>
                    <div class="text-danger"><?php echo $error_account_number_eur; ?></div>
                    <?php } ?>
                </div>
            </div>
	        <div class="form-group required">
		        <label class="col-sm-2 control-label" for="input-account-number-usd">
                    <span data-toggle="tooltip"
                          title="<?php echo $help_account_number_usd; ?>"><?php echo $entry_account_number_usd; ?></span>
		        </label>
		        <div class="col-sm-10">
			        <input type="text" name="mifinity_account_number_usd" value="<?php echo $mifinity_account_number_usd; ?>"
			               placeholder="<?php echo $entry_account_number_usd; ?>" id="input-account-number-usd" class="form-control"/>
			        <?php if ($error_account_number_usd) { ?>
			        <div class="text-danger"><?php echo $error_account_number_usd; ?></div>
			        <?php } ?>
		        </div>
	        </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="mifinity_status" id="input-status" class="form-control">
				<?php if ($mifinity_status) { ?>
					<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
					<option value="0"><?php echo $text_disabled; ?></option>
				<?php } else { ?>
					<option value="1"><?php echo $text_enabled; ?></option>
					<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
				<?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
            <div class="col-sm-10">
              <select name="mifinity_standard_geo_zone_id" id="input-geo-zone" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
				<?php foreach ($geo_zones as $geo_zone) { ?><?php if ($geo_zone['geo_zone_id'] == $mifinity_standard_geo_zone_id) { ?>
						<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
					<?php } else { ?>
						<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
					<?php } ?><?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
            <div class="col-sm-10">
              <select name="mifinity_order_status_id" id="input-order-status" class="form-control">
				<?php foreach ($order_statuses as $order_status) { ?><?php if ($order_status['order_status_id'] == $mifinity_order_status_id) { ?>
						<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
					<?php } else { ?>
						<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
					<?php } ?><?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status-refund"><?php echo $entry_order_status_refund; ?></label>
            <div class="col-sm-10">
              <select name="mifinity_order_status_refunded_id" id="input-order-status-refund" class="form-control">
				<?php foreach ($order_statuses as $order_status) { ?><?php if ($order_status['order_status_id'] == $mifinity_order_status_refunded_id) { ?>
						<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
					<?php } else { ?>
						<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
					<?php } ?><?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status-cancel"><?php echo $entry_order_status_cancel; ?></label>
            <div class="col-sm-10">
              <select name="mifinity_order_status_cancel_id" id="input-order-status-cancel" class="form-control">
				<?php foreach ($order_statuses as $order_status) { ?><?php if ($order_status['order_status_id'] == $mifinity_order_status_cancel_id) { ?>
						<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
					<?php } else { ?>
						<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
					<?php } ?><?php } ?>
			  </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status-fraud"><?php echo $entry_order_status_fraud; ?></label>
            <div class="col-sm-10">
              <select name="mifinity_order_status_fraud_id" id="input-order-status-fraud" class="form-control">
				<?php foreach ($order_statuses as $order_status) { ?><?php if ($order_status['order_status_id'] == $mifinity_order_status_fraud_id) { ?>
						<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
					<?php } else { ?>
						<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
					<?php } ?><?php } ?>
			  </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="mifinity_sort_order" value="<?php echo $mifinity_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control"/>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>