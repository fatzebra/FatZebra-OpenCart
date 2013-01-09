<?php if (isset($header)){ echo $header; } ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?>
      <a href="<?php echo $breadcrumb['href']; ?>">
        <?php echo $breadcrumb['text']; ?>
      </a>
    <?php } ?>
  </div>
  
  <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="left"></div>
    <div class="right"></div>
    <div class="heading">
      <h1 style="background-image: url('view/image/payment.png');">
        <?php echo $heading_title; ?>
      </h1>
      <div class="buttons">
        <a onclick="$('#form').submit();" class="button">
          <span><?php echo $button_save; ?></span>
        </a>
        <a onclick="location = '<?php echo $cancel; ?>';" class="button">
          <span><?php echo $button_cancel; ?></span>
        </a>
      </div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><label for="fatzebra_username">Username</label>:</td>
            <td>
              <input type="text" name="fatzebra_username" id="fatzebra_username" value="<?php echo $fatzebra_username; ?>"/>
              <?php if ($error_username) { ?>
                <span class="error"><?php echo $error_username; ?></span>
              <?php } ?>
            </td>
            <td>
              You can find your Username and Token in your <a href="https://dashboard.fatzebra.com.au/account#api" target="_fatzebra">Merchant Dashboard</a>
            </td>
          </tr>
          <tr>
            <td><label for="fatzebra_token">Token</label>:</td>
            <td>
              <input type="text" name="fatzebra_token" id="fatzebra_token" value="<?php echo $fatzebra_token; ?>" />
              <?php if ($error_token) { ?>
                <span class="error"><?php echo $error_token; ?></span>
              <?php } ?>
            </td>
            <td></td>
          </tr>
          <tr>
            <td><label for="fatzebra_shared_secret">Shared Secret</label>:</td>
            <td>
              <input type="text" name="fatzebra_shared_secret" id="fatzebra_shared_secret" value="<?php echo $fatzebra_shared_secret; ?>" />
              <?php if ($error_shared_secret) { ?>
                <span class="error"><?php echo $error_shared_secret; ?></span>
              <?php } ?>
            </td>
            <td></td>
          </tr>
          <tr>
            <td><label for="fatzebra_test_mode">Test Mode</label>:</td>
            <td>
              <select name="fatzebra_test_mode" id="fatzebra_test_mode">
                <option value="0" <?php echo $fatzebra_test_mode ? "" : "selected='selected'";?>>Off</option>
                <option value="1" <?php echo $fatzebra_test_mode ? "selected='selected'" : "";?>>On</option>
              </select>
            </td>
            <td>
              Test mode allows you to switch the gateway to a test environment regardless of being Live or Sandbox.
            </td>
          </tr>
          <tr>
            <td><label for="fatzebra_sandbox_mode">Sandbox Mode</label>:</td>
            <td>
              <select name="fatzebra_sandbox_mode" id="fatzebra_sandbox_mode">
                <option value="0" <?php echo $fatzebra_sandbox_mode ? "" : "selected='selected'";?>>Off</option>
                <option value="1" <?php echo $fatzebra_sandbox_mode ? "selected='selected'" : "";?>>On</option>
              </select>
            </td>
            <td>
              When using Sandbox mode please ensure you are using your Sandbox credentials. Live credentials will not work in the Sandbox gateway.
            </td>
          </tr>
          <tr>
            <td><label for="fatzebra_show_logo">Show 'Fat Zebra Certified' logo</label>:</td>
            <td>
              <select name="fatzebra_show_logo" id="fatzebra_show_logo">
                <option value="0" <?php echo $fatzebra_show_logo ? "" : "selected='selected'";?>>No</option>
                <option value="1" <?php echo $fatzebra_show_logo ? "selected='selected'" : "";?>>Yes</option>
              </select>
            </td>
            <td>
              Display the 'Fat Zebra Certified' logo to improve consumer confidence.
            </td>
          </tr>
      	  <tr>
            <td>Gateway Status</td>
            <td>
              <select name="fatzebra_status">
                <option value="1" <?php echo $fatzebra_status ? "selected='selected'" : ""; ?>>
                  <?php echo $text_enabled; ?>
                </option>
                <option value="0" <?php echo $fatzebra_status ? "" : "selected='selected'"; ?>>
                  <?php echo $text_disabled; ?>
                </option>
              </select>
            </td>
            <td>Enable or disable this payment gateway.</td>
          </tr>
      	  <tr>
            <td>Order Status</td>
            <td>
              <select name="fatzebra_order_status">
                <?php foreach ($order_statuses as $order_status) { ?>

                  <option value="<?php echo $order_status['order_status_id']; ?>" <?php echo (int)$fatzebra_order_status == (int)$order_status['order_status_id'] ? "selected='selected'" : ""; ?>>
                    <?php echo $order_status['name']; ?>
                  </option>
                <?php } ?>
              </select>
            </td>
            <td>Set the status of orders made with this payment module to this value.</td>
          </tr>
          <tr>
            <td>Sort Order</td>
            <td>
              <input type="text" name="fatzebra_sort_order" value="<?php echo $fatzebra_sort_order || "1"; ?>" size="1" />
            </td>
            <td>
              Define where this payment gateway shows up in the list of options. The lower the number the closest to the top it will be.
            </td>
          </tr>
          <tr>
            <td>Accepted Cards</td>
            <td>    
              <select multiple="multiple" id="fatzebra_accepted_cards" name="fatzebra_accepted_cards[]">
                <?php foreach(array("VISA", "MasterCard", "AMEX", "JCB") as $card) { ?>
                  <option value="<?php echo strtolower($card); ?>"<?php echo (in_array(strtolower($card), $fatzebra_accepted_cards)) ? "selected='selected'" : ""; ?>><?php echo $card; ?></option>
                <?php } ?>
              </select>
            </td>
            <td>
              Specify the card types you accept.
            </td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php if (isset($footer)){ echo $footer; } ?>