<?php
class ControllerPaymentFatZebra extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('payment/fatzebra');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('fatzebra', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success'); 
            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_all_zones'] = $this->language->get('text_all_zones');
        $this->data['text_none'] = $this->language->get('text_none');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['text_authorization'] = $this->language->get('text_authorization');
        $this->data['text_sale'] = $this->language->get('text_sale');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        $this->data['tab_general'] = $this->language->get('tab_general');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['username'])) {
            $this->data['error_username'] = $this->error['username'];
        } else {
            $this->data['error_username'] = '';
        }

        if (isset($this->error['token'])) {
            $this->data['error_token'] = $this->error['token'];
        } else {
            $this->data['error_token'] = '';
        }

        if (isset($this->error['shared_secret'])) {
            $this->data["error_shared_secret"] = $this->error['shared_secret'];
        } else {
            $this->data['error_shared_secret'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),            
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_payment'),
            'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('payment/fatzebra', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('payment/fatzebra', 'token=' . $this->session->data['token'], 'SSL');
        
        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['fatzebra_test_mode'])) {
            $this->data['fatzebra_test_mode'] = $this->request->post['fatzebra_test_mode'];
        } else {
            $this->data['fatzebra_test_mode'] = $this->config->get('fatzebra_test_mode');
        }

        if (isset($this->request->post['fatzebra_sandbox_mode'])) {
            $this->data['fatzebra_sandbox_mode'] = $this->request->post['fatzebra_sandbox_mode'];
        } else {
            $this->data['fatzebra_sandbox_mode'] = $this->config->get('fatzebra_sandbox_mode');
        }

        if (isset($this->request->post['fatzebra_show_logo'])) {
            $this->data['fatzebra_show_logo'] = $this->request->post['fatzebra_show_logo'];
        } else {
            $this->data['fatzebra_show_logo'] = $this->config->get('fatzebra_show_logo');
        }


        if (isset($this->request->post['fatzebra_order_status'])) {
            $this->data['fatzebra_order_status'] = $this->request->post['fatzebra_order_status'];
        } else {
            $this->data['fatzebra_order_status'] = $this->config->get('fatzebra_order_status') || 15; // Processed
        }

        $this->load->model('localisation/order_status');
        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();


        if (isset($this->request->post['fatzebra_username'])) {
            $this->data['fatzebra_username'] = $this->request->post['fatzebra_username'];
        } else {
            $this->data['fatzebra_username'] = $this->config->get('fatzebra_username');
        }

        if (isset($this->request->post['fatzebra_token'])) {
            $this->data['fatzebra_token'] = $this->request->post['fatzebra_token'];
        } else {
            $this->data['fatzebra_token'] = $this->config->get('fatzebra_token');
        }

        if (isset($this->request->post['fatzebra_shared_secret'])) {
            $this->data['fatzebra_shared_secret'] = $this->request->post['fatzebra_shared_secret'];
        } else {
            $this->data['fatzebra_shared_secret'] = $this->config->get('fatzebra_shared_secret');
        }

        if (isset($this->request->post['fatzebra_status'])) {
            $this->data['fatzebra_status'] = $this->request->post['fatzebra_status'];
        } else {
            $this->data['fatzebra_status'] = $this->config->get('fatzebra_status');
        }

        if (isset($this->request->post['fatzebra_sort_order'])) {
            $this->data['fatzebra_sort_order'] = $this->request->post['fatzebra_sort_order'];
        } else {
            $this->data['fatzebra_sort_order'] = $this->config->get('fatzebra_sort_order');
        }

        if (isset($this->request->post["fatzebra_accepted_cards"])) {
            $this->data['fatzebra_accepted_cards'] = $this->request->post['fatzebra_accepted_cards'];
        } else {
            $this->data['fatzebra_accepted_cards'] = $this->config->get('fatzebra_accepted_cards');
        }

        if (is_null($this->data['fatzebra_accepted_cards'])) {
            $this->data['fatzebra_accepted_cards'] = array("visa", "mastercard");
        }

        $this->template = 'payment/fatzebra.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));

    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/fatzebra'))
        {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['fatzebra_username'] || empty($this->request->post['fatzebra_username'])) {
            $this->error['username'] = $this->language->get('error_username');
        }

        if (!$this->request->post['fatzebra_token'] || empty($this->request->post['fatzebra_token'])) {
            $this->error['token'] = $this->language->get('error_token');
        }

        if (!$this->request->post['fatzebra_shared_secret'] || empty($this->request->post['fatzebra_shared_secret'])) {
            $this->error['shared_secret'] = $this->language->get('error_shared_secret');
        }

        return !$this->error;
    }
}
?>