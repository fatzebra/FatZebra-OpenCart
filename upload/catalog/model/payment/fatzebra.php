<?php
  class ModelPaymentFatzebra extends Model {
      public function getMethod($address) {
      $this->load->language('payment/fatzebra');

      $method_data = array(
        'code'         => 'fatzebra',
        'title'        => $this->language->get('text_title'),
        'sort_order'   => $this->config->get('fatzebra_sort_order')
      );

        return $method_data;
      }
  }
?>