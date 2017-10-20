<?php if (isset($error)) { ?>
    <div class="alert alert-danger">Payment Error: <?php echo $error; ?></div>
  <?php } else { ?>

  <?php if (isset($text_testing)) { ?>
    <div class="alert alert-warning"><?php echo $text_testing; ?></div>
  <?php } ?>

  <div class="buttons">
    <div class="pull-right">
      <input type="button" value="<?php echo $button_pay; ?>" id="button-confirm" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
    </div>
  </div>

  <div id="widget-container"></div>

  <script src="<?php echo $url?>/widgets/sgpg.js?58190a411dc3"></script>

  <script>
      document.getElementById('button-confirm').onclick = function(e) {
          // your code

          var widget = showPaymentIframe("widget-container", {
              token: '<?php echo $initializationToken; ?>',
              complete: function () {
                  setTimeout(function () {
                      widget.close();
                  }, 10000);
              },
              fail: function () {
//                  alert('Payment has been failed');
                  window.location.href = "<?php echo $failure; ?>";
              },
              success: function () {
                  // don't trust this response, wait for the notification on the url specified in returnUrl
//                  alert('Payment has been processed');
                  window.location.href = "<?php echo $success; ?>";
              }
          });

      };
  </script>

<?php } ?>