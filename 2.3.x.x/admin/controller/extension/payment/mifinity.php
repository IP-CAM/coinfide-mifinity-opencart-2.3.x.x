<?php
class ControllerExtensionPaymentMiFinity extends Controller {

	private $error = array();

	public function index() {
		$this->load->language('extension/payment/mifinity');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('mifinity', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_authorisation'] = $this->language->get('text_authorisation');
		$data['text_sale'] = $this->language->get('text_sale');
		$data['text_transparent'] = $this->language->get('text_transparent');
		$data['text_iframe'] = $this->language->get('text_iframe');

		$data['entry_paymode'] = $this->language->get('entry_paymode');
		$data['entry_test'] = $this->language->get('entry_test');
//		$data['entry_payment_type'] = $this->language->get('entry_payment_type');
		$data['entry_transaction'] = $this->language->get('entry_transaction');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_order_status_refund'] = $this->language->get('entry_order_status_refund');
		$data['entry_order_status_cancel'] = $this->language->get('entry_order_status_cancel');
		$data['entry_order_status_fraud'] = $this->language->get('entry_order_status_fraud');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_username'] = $this->language->get('entry_username');
        $data['entry_password'] = $this->language->get('entry_password');
        $data['entry_api_key'] = $this->language->get('entry_api_key');
//        $data['entry_account_holder_id'] = $this->language->get('entry_account_holder_id');
        $data['entry_account_number_eur'] = $this->language->get('entry_account_number_eur');
        $data['entry_account_number_usd'] = $this->language->get('entry_account_number_usd');
//		$data['entry_password'] = $this->language->get('entry_password');
//		$data['entry_transaction_method'] = $this->language->get('entry_transaction_method');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['help_testmode'] = $this->language->get('help_testmode');
		$data['help_username'] = $this->language->get('help_username');
        $data['help_api_key'] = $this->language->get('help_api_key');
//        $data['help_account_holder_id'] = $this->language->get('help_account_holder_id');
        $data['help_account_number_eur'] = $this->language->get('help_account_number_eur');
        $data['help_account_number_usd'] = $this->language->get('help_account_number_usd');
		$data['help_password'] = $this->language->get('help_password');
//		$data['help_transaction_method'] = $this->language->get('help_transaction_method');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['username'])) {
			$data['error_username'] = $this->error['username'];
		} else {
			$data['error_username'] = '';
		}

        if (isset($this->error['password'])) {
            $data['error_password'] = $this->error['password'];
        } else {
            $data['error_password'] = '';
        }

        if (isset($this->error['api_key'])) {
            $data['error_api_key'] = $this->error['api_key'];
        } else {
            $data['error_api_key'] = '';
        }

//        if (isset($this->error['account_holder_id'])) {
//            $data['error_account_holder_id'] = $this->error['account_holder_id'];
//        } else {
//            $data['error_account_holder_id'] = '';
//        }

        if (isset($this->error['account_number_eur'])) {
            $data['error_account_number_eur'] = $this->error['account_number_eur'];
        } else {
            $data['error_account_number_eur'] = '';
        }

        if (isset($this->error['account_number_usd'])) {
            $data['error_account_number_usd'] = $this->error['account_number_usd'];
        } else {
            $data['error_account_number_usd'] = '';
        }

//		if (isset($this->error['password'])) {
//			$data['error_password'] = $this->error['password'];
//		} else {
//			$data['error_password'] = '';
//		}

//		if (isset($this->error['payment_type'])) {
//			$data['error_payment_type'] = $this->error['payment_type'];
//		} else {
//			$data['error_payment_type'] = '';
//		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/mifinity', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/mifinity', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

		if (isset($this->request->post['mifinity_payment_gateway'])) {
			$data['mifinity_payment_gateway'] = $this->request->post['mifinity_payment_gateway'];
		} else {
			$data['mifinity_payment_gateway'] = $this->config->get('mifinity_payment_gateway');
		}

		if (isset($this->request->post['mifinity_paymode'])) {
			$data['mifinity_paymode'] = $this->request->post['mifinity_paymode'];
		} else {
			$data['mifinity_paymode'] = $this->config->get('mifinity_paymode');
		}

		if (isset($this->request->post['mifinity_test'])) {
			$data['mifinity_test'] = $this->request->post['mifinity_test'];
		} else {
			$data['mifinity_test'] = $this->config->get('mifinity_test');
		}

//		if (isset($this->request->post['mifinity_payment_type'])) {
//			$data['mifinity_payment_type'] = $this->request->post['mifinity_payment_type'];
//		} else {
//			$data['mifinity_payment_type'] = $this->config->get('mifinity_payment_type');
//		}

		if (isset($this->request->post['mifinity_transaction'])) {
			$data['mifinity_transaction'] = $this->request->post['mifinity_transaction'];
		} else {
			$data['mifinity_transaction'] = $this->config->get('mifinity_transaction');
		}

		if (isset($this->request->post['mifinity_standard_geo_zone_id'])) {
			$data['mifinity_standard_geo_zone_id'] = $this->request->post['mifinity_standard_geo_zone_id'];
		} else {
			$data['mifinity_standard_geo_zone_id'] = $this->config->get('mifinity_standard_geo_zone_id');
		}

		if (isset($this->request->post['mifinity_order_status_id'])) {
			$data['mifinity_order_status_id'] = $this->request->post['mifinity_order_status_id'];
		} else {
			$data['mifinity_order_status_id'] = $this->config->get('mifinity_order_status_id');
		}

		if (isset($this->request->post['mifinity_order_status_refunded_id'])) {
			$data['mifinity_order_status_refunded_id'] = $this->request->post['mifinity_order_status_refunded_id'];
		} else {
			$data['mifinity_order_status_refunded_id'] = $this->config->get('mifinity_order_status_refunded_id');
		}

		if (isset($this->request->post['mifinity_order_status_cancel_id'])) {
			$data['mifinity_order_status_cancel_id'] = $this->request->post['mifinity_order_status_cancel_id'];
		} else {
			$data['mifinity_order_status_cancel_id'] = $this->config->get('mifinity_order_status_cancel_id');
		}

		if (isset($this->request->post['mifinity_order_status_fraud_id'])) {
			$data['mifinity_order_status_fraud_id'] = $this->request->post['mifinity_order_status_fraud_id'];
		} else {
			$data['mifinity_order_status_fraud_id'] = $this->config->get('mifinity_order_status_fraud_id');
		}

//		if (isset($this->request->post['mifinity_transaction_method'])) {
//			$data['mifinity_transaction_method'] = $this->request->post['mifinity_transaction_method'];
//		} else {
//			$data['mifinity_transaction_method'] = $this->config->get('mifinity_transaction_method');
//		}

		if (isset($this->request->post['mifinity_username'])) {
			$data['mifinity_username'] = $this->request->post['mifinity_username'];
		} else {
			$data['mifinity_username'] = $this->config->get('mifinity_username');
		}


        if (isset($this->request->post['mifinity_password'])) {
            $data['mifinity_password'] = $this->request->post['mifinity_password'];
        } else {
            $data['mifinity_password'] = $this->config->get('mifinity_password');
        }

        if (isset($this->request->post['mifinity_api_key'])) {
            $data['mifinity_api_key'] = $this->request->post['mifinity_api_key'];
        } else {
            $data['mifinity_api_key'] = $this->config->get('mifinity_api_key');
        }

//        if (isset($this->request->post['mifinity_account_holder_id'])) {
//            $data['mifinity_account_holder_id'] = $this->request->post['mifinity_account_holder_id'];
//        } else {
//            $data['mifinity_account_holder_id'] = $this->config->get('mifinity_account_holder_id');
//        }

        if (isset($this->request->post['mifinity_account_number_eur'])) {
            $data['mifinity_account_number_eur'] = $this->request->post['mifinity_account_number_eur'];
        } else {
            $data['mifinity_account_number_eur'] = $this->config->get('mifinity_account_number_eur');
        }

        if (isset($this->request->post['mifinity_account_number_usd'])) {
            $data['mifinity_account_number_usd'] = $this->request->post['mifinity_account_number_usd'];
        } else {
            $data['mifinity_account_number_usd'] = $this->config->get('mifinity_account_number_usd');
        }

//		if (isset($this->request->post['mifinity_password'])) {
//			$data['mifinity_password'] = $this->request->post['mifinity_password'];
//		} else {
//			$data['mifinity_password'] = $this->config->get('mifinity_password');
//		}

		if (isset($this->request->post['mifinity_status'])) {
			$data['mifinity_status'] = $this->request->post['mifinity_status'];
		} else {
			$data['mifinity_status'] = $this->config->get('mifinity_status');
		}

		if (isset($this->request->post['mifinity_sort_order'])) {
			$data['mifinity_sort_order'] = $this->request->post['mifinity_sort_order'];
		} else {
			$data['mifinity_sort_order'] = $this->config->get('mifinity_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/mifinity', $data));
	}

	public function install() {
		$this->load->model('extension/payment/mifinity');
		$this->model_extension_payment_mifinity->install();
	}

	public function uninstall() {
		$this->load->model('extension/payment/mifinity');
		$this->model_extension_payment_mifinity->uninstall();
	}

	// Legacy 2.0.0
	public function orderAction() {
		return $this->order();
	}

	// Legacy 2.0.3
	public function action() {
		return $this->order();
	}

	public function order() {
		if ($this->config->get('mifinity_status')) {
			$this->load->model('extension/payment/mifinity');

			$mifinity_order = $this->model_extension_payment_mifinity->getOrder($this->request->get['order_id']);

			if (!empty($mifinity_order)) {
				$this->load->language('extension/payment/mifinity');

				$mifinity_order['total'] = $mifinity_order['amount'];
				$mifinity_order['total_formatted'] = $this->currency->format($mifinity_order['amount'], $mifinity_order['currency_code'], 1, true);

				$mifinity_order['total_captured'] = $this->model_extension_payment_mifinity->getTotalCaptured($mifinity_order['mifinity_order_id']);
				$mifinity_order['total_captured_formatted'] = $this->currency->format($mifinity_order['total_captured'], $mifinity_order['currency_code'], 1, true);

				$mifinity_order['uncaptured'] = $mifinity_order['total'] - $mifinity_order['total_captured'];

				$mifinity_order['total_refunded'] = $this->model_extension_payment_mifinity->getTotalRefunded($mifinity_order['mifinity_order_id']);
				$mifinity_order['total_refunded_formatted'] = $this->currency->format($mifinity_order['total_refunded'], $mifinity_order['currency_code'], 1, true);

				$mifinity_order['unrefunded'] = $mifinity_order['total_captured'] - $mifinity_order['total_refunded'];

				$data['text_payment_info'] = $this->language->get('text_payment_info');
				$data['text_order_total'] = $this->language->get('text_order_total');
				$data['text_void_status'] = $this->language->get('text_void_status');
				$data['text_transactions'] = $this->language->get('text_transactions');
				$data['text_column_amount'] = $this->language->get('text_column_amount');
				$data['text_column_type'] = $this->language->get('text_column_type');
				$data['text_column_created'] = $this->language->get('text_column_created');
				$data['text_column_transactionid'] = $this->language->get('text_column_transactionid');
				$data['btn_refund'] = $this->language->get('btn_refund');
				$data['btn_capture'] = $this->language->get('btn_capture');
				$data['text_confirm_refund'] = $this->language->get('text_confirm_refund');
				$data['text_confirm_capture'] = $this->language->get('text_confirm_capture');

				$data['text_total_captured'] = $this->language->get('text_total_captured');
				$data['text_total_refunded'] = $this->language->get('text_total_refunded');
				$data['text_capture_status'] = $this->language->get('text_capture_status');
				$data['text_refund_status'] = $this->language->get('text_refund_status');

				$data['text_empty_refund'] = $this->language->get('text_empty_refund');
				$data['text_empty_capture'] = $this->language->get('text_empty_capture');

				$data['mifinity_order'] = $mifinity_order;
				$data['token'] = $this->request->get['token'];
				$data['order_id'] = $this->request->get['order_id'];

				return $this->load->view('extension/payment/mifinity_order', $data);
			}
		}
	}

	public function refund() {
		$this->load->language('extension/payment/mifinity');

		$order_id = $this->request->post['order_id'];
		$refund_amount = (double)$this->request->post['refund_amount'];

		if ($order_id && $refund_amount > 0) {
			$this->load->model('extension/payment/mifinity');
			$result = $this->model_extension_payment_mifinity->refund($order_id, $refund_amount);

			// Check if any error returns
			if (isset($result->errors) || $result === false) {
				$json['error'] = true;
				$reason = '';
				if ($result === false) {
					$reason = $this->config->get('text_unknown_failure');
				} else {
						$reason = $result->errors;
				}
				$json['message'] = $this->config->get('text_refund_failed') . $reason;
			} else {
				$mifinity_order = $this->model_extension_payment_mifinity->getOrder($order_id);
				$this->model_extension_payment_mifinity->addTransaction($mifinity_order['mifinity_order_id'], $result->Refund->TransactionID, 'refund', $result->Refund->TotalAmount, $mifinity_order['currency_code']);

				$total_captured = $this->model_extension_payment_mifinity->getTotalCaptured($mifinity_order['mifinity_order_id']);
				$total_refunded = $this->model_extension_payment_mifinity->getTotalRefunded($mifinity_order['mifinity_order_id']);
				$refund_status = 0;

				if ($total_captured == $total_refunded) {
					$refund_status = 1;
					$this->model_extension_payment_mifinity->updateRefundStatus($mifinity_order['mifinity_order_id'], $refund_status);
				}

				$json['data'] = array();
				$json['data']['transactionid'] = $result->TransactionID;
				$json['data']['created'] = date("Y-m-d H:i:s");
				$json['data']['amount'] = number_format($refund_amount, 2, '.', '');
				$json['data']['total_refunded_formatted'] = $this->currency->format($total_refunded, $mifinity_order['currency_code'], 1, true);
				$json['data']['refund_status'] = $refund_status;
				$json['data']['remaining'] = $total_captured - $total_refunded;
				$json['message'] = $this->language->get('text_refund_success');
				$json['error'] = false;
			}
		} else {
			$json['error'] = true;
			$json['message'] = 'Missing data';
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function capture() {
		$this->load->language('extension/payment/mifinity');

		$order_id = $this->request->post['order_id'];
		$capture_amount = (double)$this->request->post['capture_amount'];

		if ($order_id && $capture_amount > 0) {
			$this->load->model('extension/payment/mifinity');
			$mifinity_order = $this->model_extension_payment_mifinity->getOrder($order_id);
			$result = $this->model_extension_payment_mifinity->capture($order_id, $capture_amount, $mifinity_order['currency_code']);

			// Check if any error returns
			if (isset($result->errors) || $result === false) {
				$json['error'] = true;
				$reason = '';
				if ($result === false) {
					$reason = $this->config->get('text_unknown_failure');
				} else {
                    $reason = $result->errors;
				}
				$json['message'] = $this->config->get('text_capture_failed') . $reason;
			} else {
				$this->model_extension_payment_mifinity->addTransaction($mifinity_order['mifinity_order_id'], $result->TransactionID, 'payment', $capture_amount, $mifinity_order['currency_code']);

				$total_captured = $this->model_extension_payment_mifinity->getTotalCaptured($mifinity_order['mifinity_order_id']);
				$total_refunded = $this->model_extension_payment_mifinity->getTotalRefunded($mifinity_order['mifinity_order_id']);

				$remaining = $mifinity_order['amount'] - $capture_amount;
				if ($remaining <= 0) {
					$remaining = 0;
				}

				$this->model_extension_payment_mifinity->updateCaptureStatus($mifinity_order['mifinity_order_id'], 1);
				$this->model_extension_payment_mifinity->updateTransactionId($mifinity_order['mifinity_order_id'], $result->TransactionID);

				$json['data'] = array();
				$json['data']['transactionid'] = $result->TransactionID;
				$json['data']['created'] = date("Y-m-d H:i:s");
				$json['data']['amount'] = number_format($capture_amount, 2, '.', '');
				$json['data']['total_captured_formatted'] = $this->currency->format($total_captured, $mifinity_order['currency_code'], 1, true);
				$json['data']['capture_status'] = 1;
				$json['data']['remaining'] = $remaining;
				$json['message'] = $this->language->get('text_capture_success');
				$json['error'] = false;
			}
		} else {
			$json['error'] = true;
			$json['message'] = 'Missing data';
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/mifinity')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (!$this->request->post['mifinity_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}
        if (!$this->request->post['mifinity_password']) {
            $this->error['mifinity_password'] = $this->language->get('mifinity_password');
        }
        if (!$this->request->post['mifinity_api_key']) {
            $this->error['api_key'] = $this->language->get('error_api_key');
        }
//        if (!$this->request->post['mifinity_account_holder_id']) {
//            $this->error['account_holder_id'] = $this->language->get('error_account_holder_id');
//        }
        if (!$this->request->post['mifinity_account_number_eur']) {
            $this->error['account_number_eur'] = $this->language->get('error_account_number_eur');
        }
        if (!$this->request->post['mifinity_account_number_usd']) {
            $this->error['account_number_usd'] = $this->language->get('error_account_number_usd');
        }
//		if (!$this->request->post['mifinity_password']) {
//			$this->error['password'] = $this->language->get('error_password');
//		}
//		if (!isset($this->request->post['mifinity_payment_type'])) {
//			$this->error['payment_type'] = $this->language->get('error_payment_type');
//		}

		return !$this->error;
	}

}