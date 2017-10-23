<?php
class ControllerExtensionPaymentMiFinity extends Controller {
    public function index() {
        $this->load->language('extension/payment/mifinity');

		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['button_pay'] = $this->language->get('button_pay');
		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['entry_cc_name'] = $this->language->get('entry_cc_name');
		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');

//		$data['text_card_type_pp'] = $this->language->get('text_card_type_pp');
		$data['text_type_help'] = $this->language->get('text_type_help');

		$data['help_cvv'] = $this->language->get('help_cvv');

//		$data['payment_type'] = $this->config->get('mifinity_payment_type');

		$data['months'] = array();

		for ($i = 1; $i <= 12; $i++) {
			$data['months'][] = array(
				'text' => sprintf('%02d', $i),
				'value' => sprintf('%02d', $i)
			);
		}

		$today = getdate();

		$data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$data['year_expire'][] = array(
				'text' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}

		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);

		if ($this->config->get('mifinity_test')) {
			$data['text_testing'] = $this->language->get('text_testing');
			$data['url'] = 'https://demo.mifinitypay.com';
		} else {
			$data['url'] = 'https://secure.mifinitypay.com';
		}


        $tel = preg_replace('/\D+/', '', (string)substr($order_info['telephone'], 0, 32));

		$data['firstName'] = (string)substr($order_info['payment_firstname'], 0, 50);
		$data['lastName'] = (string)substr($order_info['payment_lastname'], 0, 50);
//		$data['CompanyName'] = (string)substr($order_info['payment_company'], 0, 50);
//		$data['Street1'] = (string)substr($order_info['payment_address_1'], 0, 50);
//		$data['Street2'] = (string)substr($order_info['payment_address_2'], 0, 50);
        $data['city'] = (string)substr($order_info['payment_city'], 0, 50);
//		$data['State'] = (string)substr($order_info['payment_zone'], 0, 50);
//		$data['PostalCode'] = (string)substr($order_info['payment_postcode'], 0, 30);
		$data['country'] = strtolower($order_info['payment_iso_code_2']);
		$data['email'] = $order_info['email'];
		$data['phone'] = $tel;

        $request = new stdClass();

//        $request->returnUrl = $this->url->link('extension/payment/mifinity/callback', '', true);
//        $request->processors = array("NXAQCR","NXEWALLET");

        $request->processors = null;

        $traceID =$order_info['order_id']. ' - #'  . '/'.time();
        $request->traceId = $traceID;
        $request->validationKey = hash('sha256', $traceID);
        $request->returnUrl = $this->url->link('extension/payment/mifinity/callback', 'validationKey=' . $request->validationKey, true);

        $request->client = new stdClass();
		$request->client->firstName = (string)substr($order_info['shipping_firstname'], 0, 50);
		$request->client->lastName = (string)substr($order_info['shipping_lastname'], 0, 50);
        $request->client->emailAddress = $order_info['email'];
        $request->client->phone = $tel;

        $request->clientReference = $request->client->firstName . ' ' . $request->client->lastName;

        $request->address = new stdClass();
		$request->address->addressLine1 = (string)substr($order_info['shipping_address_1'], 0, 50);
		$request->address->addressLine2 = (string)substr($order_info['shipping_address_2'], 0, 50);
		$request->address->city = (string)substr($order_info['shipping_city'], 0, 50);
//		$request->client->State = (string)substr($order_info['shipping_zone'], 0, 50);
        if (!empty($order_info['shipping_postcode'])) {
            $request->address->postalCode = $order_info['shipping_postcode'];
            }else if (!empty($order_info['payment_postcode'])) {
            $request->address->postalCode = $order_info['payment_postcode'];
            } else {

        }
		$request->address->countryCode = strtolower($order_info['shipping_iso_code_2']);

		$invoice_desc = '';
		foreach ($this->cart->getProducts() as $product) {
//			$item_price = $this->currency->format($product['price'], $order_info['currency_code'], false, false);
			$invoice_desc .= $product['name'] .' / '. $product['quantity'].' , ';
		}
		$invoice_desc = (string)substr($invoice_desc, 0, -2);
		if (strlen($invoice_desc) > 254) {
			$invoice_desc = (string)substr($invoice_desc, 0, 251) . '...';
		}

//		$shipping = $this->currency->format($order_info['total'] - $this->cart->getSubTotal(), $order_info['currency_code'], false, false);
//
//		if ($shipping > 0) {
//			$item = new stdClass();
//			$item->SKU = '';
//			$item->Description = (string)substr($this->language->get('text_shipping'), 0, 26);
//			$item->Quantity = 1;
//			$item->UnitCost = $shipping * 100;
//			$item->Total = $shipping * 100;
//			$request->Items[] = $item;
//		}

		$request->money = new stdClass();
//		$request->money->amount = number_format($amount, 2, '.', '') * 100;
        $request->money->amount = $amount   ;
        $request->money->currency = $order_info['currency_code'];

        $request->description = $invoice_desc;
        if ($order_info['currency_code'] == 'USD'){
            $request->destinationAccountNumber = $this->config->get('mifinity_account_number_usd');
        } else if($order_info['currency_code'] == 'EUR') {$request->destinationAccountNumber = $this->config->get('mifinity_account_number_eur');
        } else {$request->destinationAccountNumber = $this->config->get('mifinity_account_number_eur');
        }

        $request->checkThreeDSecure = true;


		$this->load->model('extension/payment/mifinity');
		$template = 'mifinity';

        $result = $this->model_extension_payment_mifinity->getAuth('grant_type=client_credentials');
        $accountHolderId = $result ->accountHolderId;

		if ($this->config->get('mifinity_paymode') == 'iframe') {
//			$request->CancelUrl = $this->url->link('checkout/failure');
//			$request->CustomerReadOnly = true;
			$result = $this->model_extension_payment_mifinity->initIframe($request,$accountHolderId);

			$template = 'mifinity_iframe';
		} else {
//			$result = $this->model_extension_payment_mifinity->getAccessCode($request);
            //todo
		}

		// Check if any error returns
		if (isset($result->errors)) {
			$lbl_error = $result->errors . "<br />\n";
			$this->log->write('MiFinity Payment error: ' . $lbl_error);
		}

		if (isset($lbl_error)) {
			$data['error'] = $lbl_error;
		} else {
			if ($this->config->get('mifinity_paymode') == 'iframe') {
				$data['accountHolderId'] = $accountHolderId;
                $data['initializationToken'] = $result->payload[0]->initializationToken;
			}else{
                //todo
            }
		}

//		var_dump($request);
//        return $this->load->view('extension/payment/mifinity', $data);
//                    print_r($data);
//        print_r($template);
//        $data['action'] = $this->url->link('extension/payment/mifinity/checkout', '', true);
        $data['success'] = $this->url->link('checkout/success', '', true);
//        $data['failure'] = $this->url->link('checkout/failure', '', true);
        $data['failure'] = $this->url->link('extension/payment/mifinity/failure', 'order_id=' . $order_info['order_id'], true);


        $order_info['debug_data'] = json_encode($result);
        $this->model_extension_payment_mifinity->addOrder($order_info);
        return $this->load->view('extension/payment/' . $template, $data);
	}

	public function callback() {
		$this->load->language('extension/payment/mifinity');
        $this->load->model('extension/payment/mifinity');
        $body = file_get_contents('php://input');
        $this->log->write('MiFinity Payment request POST ' . $body);
        if (isset($this->request->get['validationKey'])|| isset($this->request->get['amp;validationKey'])) {
            if (isset($this->request->get['amp;validationKey'])) {
                $validationKey = $this->request->get['amp;validationKey'];

            } else {
                $validationKey = $this->request->get['validationKey'];

            }
            $result = $req = json_decode($body);

            $traceId =  $result->traceId;
            $matches = array();
            preg_match('/(.+) - #.+/', $traceId, $matches);
            $order_id = $matches[1];
            $this->load->model('checkout/order');

            $order_info = $this->model_checkout_order->getOrder($order_id);

            $mifinity_order_data = $this->model_extension_payment_mifinity->getOrder($order_id);
//
//                $mifinity_order_data = array(
//                    'order_id' => $order_id,
//                    'transaction_id' => $traceId,
//                    'amount' => $order_info['amount'],
//                    'currency_code' => $order_info['currency_code'],
//                    'debug_data' => json_encode($result)
//                );

            $this->model_extension_payment_mifinity->updateOrder($mifinity_order_data['mifinity_order_id'] , $traceId, 'payment', $order_info);

//                $mifinity_order_id = $this->model_extension_payment_mifinity->addOrder($mifinity_order_data);
//                $this->model_extension_payment_mifinity->addTransaction($mifinity_order_id, $result->TransactionID, $order_info);

//                  if ($fraud) {
//                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('mifinity_order_status_fraud_id'));
//                } else {
                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('mifinity_order_status_id'));
//                }
        }
    }

    public function failure() {
        $this->load->model('checkout/order');
//        print_r($this->request);
        if (isset($this->request->get['order_id']) || isset($this->request->get['amp;order_id'])) {
            if (isset($this->request->get['amp;order_id'])) {
                $order_id = $this->request->get['amp;order_id'];
            } else {
                $order_id = $this->request->get['order_id'];
            }
//            $order_info = $this->model_checkout_order->getOrder($order_id);
            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('mifinity_order_status_cancel_id'));
            $this->response->redirect($this->url->link('checkout/failure', '', true));
        }
    }

}