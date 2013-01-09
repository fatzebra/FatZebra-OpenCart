<style type="text/css">
  .card-logo {
    vertical-align: bottom;
    opacity: 0.5;
  }
</style>

<?php if (isset($error)) { ?>
  <div class="warning"><?php echo $error; ?></div>
<?php } ?>
<form action="<?php echo $direct_action; ?>" method="post" id="payment">
  <input type="hidden" name="amount" value="<?php echo (int)($amount * 100); ?>" />
  <input type="hidden" name="currency" value="<?php echo $currency_code; ?>" />
  <input type="hidden" name="reference" value="<?php echo $transaction_number; ?>" />
  <input type="hidden" name="return_path" value="<?php echo $return_path; ?>" />
  <input type="hidden" name="verification" value="<?php echo $verification_value; ?>" />
  <?php if (isset($text_testing)) { ?>
  <div class="warning"><?php echo $text_testing; ?></div>
  <?php } ?>
    <strong>Credit Card Payment</strong>
    <div class="content">
      <?php if ($show_logo) { ?>
        <a href="https://www.fatzebra.com.au" target="_blank" title="Fat Zebra Certified" style="float: right">
          <img src="/catalog/view/theme/default/image/payment/fatzebra-certified.png" alt="Fat Zebra Certified" width="168" height="171" border="0" />
        </a>
      <?php } ?>
      <ul id="error-placeholder">

      </ul>
      <table id="fatzebra_payment">
        <tr>
          <td>
            <span><label for="card_holder_name">Card Holder Name</label>:</span>
          </td>
          <td>
            <input name="card_holder" type="text" id="card_holder_name" class="required" title="Card Holder is required" />
          </td>
        </tr>
        <tr>
          <td>
            <span><label for="card_number">Card Number</label>:</span>
          </td>
          <td>
            <input name="card_number" type="text" maxlength="17" id="card_number" class="required number" title="Card number is requried" />
            <?php foreach($fatzebra_accepted_cards as $card_type) { ?>
              <img src="/catalog/view/theme/default/image/payment/<?php echo strtolower($card_type); ?>.png" alt="<?php echo $card_type; ?>" data-name="<?php echo strtolower($card_type); ?>" class="card-logo" id="card-<?php echo strtolower($card_type); ?>" />
            <?php } ?>
          </td>
        </tr>
        <tr>
          <td>
            <span><label for="cvv">Security Code</label>:</span>
          </td>
          <td>
            <input name="cvv" type="text" maxlength="4" id="cvv" size="5" class="required number" title="Security Code is required" />
          </td>
        </tr>
        <tr>
          <td>
            <span><label for="card_expiry_month">Expiry Date</label>:</span></td>
          <td>
            <select name="expiry_month" id="card_expiry_month">
            	<?php for($i = 1; $i <= 12; $i++) { ?>
                <option value="<?php echo $i; ?>"<?php echo (date("m") == (int)$i) ? "selected='selected'" : ""; ?>><?php echo $i; ?></option>
            	<?php } ?>
            </select>
            /
            <select name="expiry_year" id="card_expiry_year">
          	  <?php for($i = date("Y"); $i < date("Y") + 20; $i++) { ?>
            	  <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
              <?php } ?>
          	</select>
          </td>
        </tr>
      </table>
</form>
</div>

<div class="buttons">
  <div class="right">
    <a onclick="$('#payment').validateAndSubmit();" class="button">
      <span><?php echo $button_confirm; ?></span>
    </a>
  </div>
</div>

<script type="text/javascript">
  function initCardHandler() {
    var field = document.getElementById("card_number");
    field.onkeyup = function(e) {
      try {
        var val = field.value;
        if (val.length === 0) return;

        // Grey out all the cards
        var logos = document.getElementsByClassName("card-logo");
        for( var i = 0; i <= logos.length; i++) {
          try { logos[i].style.opacity = 0.5; } catch(e) {}
        }

        if (val.match(/^4/)) {
          var logo = document.getElementById("card-visa");
          logo.style.opacity = 1.0;
          return;
        }

        if (val.match(/^5/)) {
          var logo = document.getElementById("card-mastercard");
          logo.style.opacity = 1.0;
          return; 
        }

        if (val.match(/^3[47]/)) {
          var logo = document.getElementById("card-amex");
          logo.style.opacity = 1.0;
          return; 
        }

        if (val.match(/^35/)) {
          var logo = document.getElementById("card-jcb");
          logo.style.opacity = 1.0;
          return; 
        }
      } catch(e) {
        // Prevent any errors from this sugar ruining someones day...
      }
    };

    jQuery("input.required").blur(function(e) {
      if (jQuery(this).val().length !== 0) {
        jQuery(this).removeClass("required");
      } else {
        jQuery(this).addClass("required");
      }
    });
  }

  initCardHandler();

  $.fn.extend({
    validate: function() {
      var errors = jQuery("#error-placeholder");
      errors.empty();
      this.each(function() {
        var  obj = jQuery(this);
        if (obj.hasClass("required") && obj.val().length === 0) {
          var err = obj.attr("title");
          errors.append("<li>" + err + "</li>");
        }
      });

      return (errors.children().length === 0);
    },

    validateAndSubmit: function() {
      if (jQuery("#payment input").validate()) {
        jQuery("#payment").submit();
      }
    }
  });
</script>