<?php ob_start();
class ControllerPaymentFatZebra extends Controller {
    protected function index() {
        $this->load->language('payment/fatzebra');

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_back'] = $this->language->get('button_back');


        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->data['item_name'] = html_entity_decode($this->config->get('config_store'), ENT_QUOTES, 'UTF-8');
        $this->data['currency_code'] = "AUD";
        $this->data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], FALSE);
        $this->data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
        $this->data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
        $this->data['email'] = $order_info['email'];
        $this->data['address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
        $this->data['address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
        $this->data['city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
        $this->data['postcode'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
        $this->data['invoice'] = $this->session->data['order_id'] . ' - ' . html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
        $this->data['transaction_number'] = $this->session->data['order_id'];
        $this->data['country'] = $order_info['payment_iso_code_3'];


        $this->data['return_path'] = $this->url->link("payment/fatzebra/callback", '', 'SSL');

        $ver_data = array($this->data["transaction_number"],
                          (int)($this->data["amount"] * 100),
                          $this->data["currency_code"],
                          $this->data["return_path"]);
        $this->data['verification_value'] = hash_hmac("md5", implode(":", $ver_data), $this->config->get("fatzebra_shared_secret"));

        if($this->config->get('fatzebra_test_mode') == 1 || $this->config->get('fatzebra_sandbox_mode') == 1) {
          $this->data['text_testing'] = $this->language->get('text_testing'); 
          $this->data['direct_action'] = "https://gateway.sandbox.fatzebra.com.au/v2/purchases/direct/" . $this->config->get("fatzebra_username");
        } else {
            $this->data['direct_action'] = "https://gateway.fatzebra.com.au/v2/purchases/direct/" . $this->config->get("fatzebra_username");
        }


        $this->data['fatzebra_accepted_cards'] = $this->config->get('fatzebra_accepted_cards');
        if (is_null($this->data['fatzebra_accepted_cards'])) {
            $this->data['fatzebra_accepted_cards'] = array("visa", "mastercard");
        }

        $this->data['show_logo'] = $this->config->get('fatzebra_show_logo');


        $this->data['back'] = HTTPS_SERVER . 'index.php?route=checkout/payment';
        $this->id = 'payment';

        isset($_GET['error']) ? $this->data['error'] = $_GET['error'] : '';

        $this->data['action'] = HTTP_SERVER . 'index.php?route=payment/fatzebra/process_payment&order_id=' . $this->session->data['order_id'];

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/fatzebra.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/fatzebra.tpl';
        } else {
            $this->template = 'default/template/payment/fatzebra.tpl';
        }
    
        $this->render();

    } //end index function

    function callback() {
        global $log;
        $this->load->model('checkout/order');
        $order_id = $this->session->data['order_id'];
        
        switch($this->request->get["r"]){
            case 1:
                // Approved
                $order_info = $this->model_checkout_order->getOrder($order_id);

                $this->model_checkout_order->confirm($order_id, 
                                                     $this->config->get('fatzebra_order_status'), 
                                                         "Transaction ID: " . $this->request->get['id'], true);
                header('Location: index.php?route=checkout/success');
                exit();


            break;
            case 2:
                // Declined
                $response = "fatzebra_transaction_message=" . $this->request->get['message'];
                $response .= "&fatzebra_transaction_id=" . $this->request->get['id'];

                $this->model_checkout_order->update($order_id, 
                                                 10, // 10 = Failed
                                                 "Transaction ID: " . $this->request->get['id'] . ", Message: " . $this->request->get['message'], false);
                header("Location: ".$this->config->get('config_url')."index.php?route=payment/fatzebra/error&" . $response);
                exit();
            break;

            case 95:
                // Merchant not found
                $log->write("Merchant not found - please ensure the Fat Zebra username is correct.");
                $response = "fatzebra_gateway_error=true";
                $response .= "&fatzebra_gateway_errors[]=Merchant not found - Please contact the website owner (95/$order_id)";
                $errors = "Merchant not found - please ensure your Fat Zebra username is correct.";
            break;

            case 96:
                // Reference taken
                $log->write("Reference already taken. Existing order details: " . print_r($this->request->get['existing'], true));
                $response = "fatzebra_gateway_error=true";
                $response .= "&fatzebra_gateway_errors[]=Reference Already Taken - Please try again (96/$order_id)";
                $errors = "Reference already taken.";
            break;

            case 97:
                // Validation error
                $log->write("Validation error - customer did not fill in required fields.");
                $response = "fatzebra_gateway_error=true";

                foreach($this->request->get['errors'] as $e) {
                    $response .= "&fatzebra_gateway_errors[]=$e";
                }

                $errors = "Validation Error - Missing Fields.";
            break;

            case 99:
                // Verification error
                $log->write("Verification error - please ensure the Fat Zebra shared secret is correct.");
                $response = "fatzebra_gateway_error=true";
                $response .= "&fatzebra_gateway_errors[]=Verification Error - Unable to verify request (99/$order_id)";
                $errors = "Verification error - unable to verify request (please ensure the shared secret is set correctly).";
            break;

            case 999:
                // Gateway error
                $response = "fatzebra_gateway_error=true";
                $response .= "&fatzebra_gateway_errors[]=Gateway Error - Please contact the website owner (999/$order_id)";
                $errors = "Gateway Error - Please contact Fat Zebra.";
            break;
        }

        $this->model_checkout_order->update($order_id, 
                                             10, // 10 is for Failed.
                                             "Order Failed: " . $errors, false);
        header("Location: ".$this->config->get('config_url')."index.php?route=payment/fatzebra/error&" . $response);
        exit();
    }

    function error() {
        $this->language->load('payment/fatzebra_error');
            
        $this->document->setTitle($this->language->get('heading_title'));   
        
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['heading_message'] = $this->language->get('heading_message');
        $this->data['text_error'] = $this->language->get('text_error');
        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['continue'] = $this->url->link('checkout/cart', 'SSL');

        $this->data['button_tryagain'] = $this->language->get('button_tryagain');
        $this->data['tryagain'] = $this->url->link('checkout/checkout', 'SSL');

        $this->data['fatzebra_transaction_id'] = (isset($this->request->get['fatzebra_transaction_id'])) ? $this->request->get['fatzebra_transaction_id'] : '';
        $this->data['fatzebra_transaction_message'] = (isset($this->request->get['fatzebra_transaction_message'])) ? $this->request->get['fatzebra_transaction_message'] : '';
        $this->data['fatzebra_gateway_error'] = (isset($this->request->get['fatzebra_gateway_error'])) ? $this->request->get["fatzebra_gateway_error"] : false; 
        $this->data['fatzebra_gateway_errors'] = (isset($this->request->get["fatzebra_gateway_errors"])) ? $this->request->get["fatzebra_gateway_errors"] : array(); // Returned an array of error messages
        $this->id = 'content';

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/fatzebra_error.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/fatzebra_error.tpl';
        } else {
            $this->template = 'default/template/payment/fatzebra_error.tpl';
        }

        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render(TRUE));
    }

} //end Controller class
?>