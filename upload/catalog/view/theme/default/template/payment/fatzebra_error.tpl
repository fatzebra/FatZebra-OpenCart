<?php echo $header; ?>
<div class="top">
  <h1><?php echo $heading_title; ?></h1>
</div>
<div class="middle">
  <table width="100%" border="0" class="content" style="min-height:200px; height:200px;">
    <tr>
      <td colspan="2" align="center">
        <strong>
          Transaction Failed
          <?php if ($fatzebra_gateway_error) { ?>: Gateway Error<?php } ?>
        </strong>
      </td>
    </tr>
    <?php if ($fatzebra_gateway_error) { ?>
      <tr>
        <td colspan="2">
          There has been an error processing this transaction with the payment gateway:
          <ul>
            <?php foreach($fatzebra_gateway_errors as $error) { ?>
              <li><?php echo $error; ?></li>
            <?php } ?>
          </ul>
        </td>
      </tr>
    <?php } else { ?>
      <?php if(!empty($fatzebra_transaction_id)) { ?>
        <tr>
          <td align="right"><strong>Transaction ID:</strong></td>
          <td><?php echo $fatzebra_transaction_id; ?></td>
        </tr>
      <?php }
        if(!empty($fatzebra_transaction_message)) { ?>
        <tr>
          <td align="right"><strong>Message:</strong></td>
          <td>&nbsp;<?php echo $fatzebra_transaction_message; ?></td>
        </tr>
      <?php } ?>
    <?php } ?>
  </table>
  <div class="buttons">
    <a href="<?php echo $continue; ?>" class="button">
      <span>
        <?php echo $button_continue; ?>
      </span>
    </a>
    <a href="<?php echo $tryagain; ?>" class="button">
      <span>
        <?php echo $button_tryagain; ?>
      </span>
    </a>
  </div>
</div>
<div class="bottom">&nbsp;</div>
<?php echo $footer; ?>