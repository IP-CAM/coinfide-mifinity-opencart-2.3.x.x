<?php if (isset($error)) { ?>
	<div class="alert alert-danger">Payment Error: <?php echo $error; ?></div>
<?php } else { ?>
	<form action="<?php echo $action; ?>" method="POST" class="form-horizontal" id="mifinity-payment-form">
	  <fieldset id="payment">
		<legend><?php echo $text_credit_card; ?></legend>
		<input type="hidden" name="MIFINITY_ACCESSCODE" value="<?php echo $AccessCode; ?>" />
		<?php if (isset($text_testing)) { ?>
			<div class="alert alert-warning"><?php echo $text_testing; ?></div>
		<?php } ?>
		<div class="form-group">
		  <div class="col-sm-12">
			<ul>
			 <!-- <?php if ($payment_type['visa'] == 1 || $payment_type['mastercard'] == 1) { ?> -->
				  <label><input type="radio" name="MIFINITY_PAYMENTTYPE" id="mifinity-radio-cc" value="creditcard" checked="checked" onchange="javascript:select_MiFinityPaymentOption('creditcard')" />
					<!-- <?php if ($payment_type['visa'] == 1) { ?> -->
						<img src="catalog/view/theme/default/image/mifinity_creditcard_visa.png" height="30" alt="Visa" />
					<!-- <?php } ?>
					<?php if ($payment_type['mastercard'] == 1) { ?> -->
						<img src="catalog/view/theme/default/image/mifinity_creditcard_master.png" height="30" alt="MasterCard" />
					<!-- <?php } ?> -->
				  </label>
			  <!-- <?php } ?> -->
			 <!-- <?php if ($payment_type['paypal'] == 1) { ?>
				  <label><input type="radio" name="MIFINITY_PAYMENTTYPE" value="paypal" onchange="javascript:select_MiFinityPaymentOption(paypal)" /> <img src="catalog/view/theme/default/image/mifinity_paypal.png" height="30" alt="'.$text_card_type_pp.'" /></label> ';
			  <?php } ?> -->
			</ul>
		  </div>
		</div>
		<!-- <?php if ($payment_type['paypal'] == 1) { ?>
			<p id="tip-paypal" style="display:none;"><?php echo $text_type_help; ?><?php echo $text_card_type_pp; ?></p>
		<?php } ?> -->
		<!-- <?php if ($payment_type['visa'] == 1 || $payment_type['mastercard'] == 1) { ?> -->
			<div id="creditcard-info">
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="mifinity-cardname"><?php echo $entry_cc_name; ?></label>
				<div class="col-sm-10">
				  <input name="MIFINITY_CARDNAME" type="text" value="" id="mifinity-cardname" placeholder="<?php echo $entry_cc_name; ?>"  autocomplete="off" class="form-control"/>
				  <span id="mifinitycard-error" class="text-danger"></span>
				</div>
			  </div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="mifinity-cardnumber"><?php echo $entry_cc_number; ?></label>
				<div class="col-sm-10">
				  <input name="MIFINITY_CARDNUMBER" type="text" maxlength="19" id="mifinity-cardnumber" value="" placeholder="<?php echo $entry_cc_number; ?>"  autocomplete="off" class="form-control" pattern="\d*" />
				  <span id="mifinitynumber-error" class="text-danger"></span>
				</div>
			  </div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="mifinity-card-expiry-month"><?php echo $entry_cc_expire_date; ?></label>
				<div class="col-sm-2">
				  <select name="MIFINITY_CARDEXPIRYMONTH" id="mifinity-card-expiry-month" class="form-control">
					<?php foreach ($months as $month) { ?>
						<option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
					<?php } ?>
				  </select>
				</div>
				<div class="col-sm-2">
				  <select name="MIFINITY_CARDEXPIRYYEAR" id="mifinity-card-expiry-year" class="form-control">
					<?php foreach ($year_expire as $year) { ?>
						<option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
					<?php } ?>
				  </select><div id="expiry-error" class="text-danger"></div>
				</div>
			  </div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="mifinity-cardcvn"><?php echo $entry_cc_cvv2; ?></label>
				<div class="col-sm-10">
				  <input name="MIFINITY_CARDCVN" type="text" maxlength="4" value="" placeholder="<?php echo $entry_cc_cvv2; ?>" id="mifinity-cardcvn" autocomplete="off" class="form-control" pattern="\d*" />
				  <span id="cvn-details" class="help">
					<?php echo $help_cvv; ?>
				  </span>
				  <br>
				  <span id="mifinitycvn-error" class="text-danger"></span>
				</div>
			  </div>
			</div>
	 <!-- <?php } ?> -->
	  </fieldset>
	</form>
	<div class="buttons">
	  <div class="pull-right">
		<input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
	  </div>
	</div>
	<script language="JavaScript" type="text/javascript" >//<!--
	    function select_MiFinityPaymentOption(v) {
	      if ($("#creditcard-info").length) {
	        $("#creditcard-info").hide();
	      }
//	      if ($("#tip-paypal").length) {
//	        $("#tip-paypal").hide();
//	      }
	      if ($("#tip-masterpass").length) {
	        $("#tip-masterpass").hide();
	      }
	      if (v == 'creditcard') {
	        $("#creditcard-info").show();
	      } else {
	        $("#tip-" + v).show();
	      }
	    }
	//--></script>
	<script type="text/javascript"><!--
	$('#button-confirm').bind('click', function () {

	      if ($('#mifinity-radio-cc').is(':checked')) {
	        var mifinity_error = false;
	        if ($('#mifinity-cardname').val().length < 1) {
	          mifinity_error = true;
	          $('#mifinitycard-error').html('Card Holder\'s Name must be entered');
	        } else {
	          $('#mifinitycard-error').empty();
	        }

	        var ccnum_regex = new RegExp("^[0-9]{13,19}$");
	        if (!ccnum_regex.test($('#mifinity-cardnumber').val().replace(/ /g, '')) || !luhn10($('#mifinity-cardnumber').val())) {
	          mifinity_error = true;
	          $('#mifinitynumber-error').html('Card Number appears invalid');
	        } else {
	          $('#mifinitynumber-error').empty();
	        }

	        var cc_year = parseInt($('#mifinity-card-expiry-year').val(), 10);
	        var cc_month = parseInt($('#mifinity-card-expiry-month').val(), 10);

	        var cc_expiry = new Date(cc_year, cc_month, 1);
	        var cc_expired = new Date(cc_expiry - 1);
	        var today = new Date();

	        if (today.getTime() > cc_expired.getTime()) {
	          mifinity_error = true;
	          $('#expiry-error').html('This expiry date has passed');
	        } else {
	          $('#expiry-error').empty();
	        }

	        var ccv_regex = new RegExp("^[0-9]{3,4}$");
	        if (!ccv_regex.test($('#mifinity-cardcvn').val().replace(/ /g, ''))) {
	          mifinity_error = true;
	          $('#mifinitycvn-error').html('CVV/CSV Number appears invalid');
	        } else {
	          $('#mifinitycvn-error').empty();
	        }

	        if (mifinity_error) {
	          return false;
	        }
	      }

	      $('#mifinity-payment-form').submit();
	      $('#button-confirm').button('loading');
	      $("#button-confirm").prop('disabled', true);

	    });

	    var luhn10 = function (a, b, c, d, e) {
	      for (d = +a[b = a.length - 1], e = 0; b--; ) {
	        c = +a[b], d += ++e % 2 ? 2 * c % 10 + (c > 4) : c;
	      }
	      return !(d % 10)
	    };

	//--></script>

<?php } ?>