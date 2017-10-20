<?php
class ModelExtensionPaymentMiFinity extends Model {
	public function getMethod($address, $total) {
		$this->load->language('extension/payment/mifinity');

		if ($this->config->get('mifinity_status')) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('mifinity_standard_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
			if (!$this->config->get('mifinity_standard_geo_zone_id')) {
				$status = true;
			} elseif ($query->num_rows) {
				$status = true;
			} else {
				$status = false;
			}
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code' => 'mifinity',
				'title' => $this->language->get('text_title'),
				'terms' => '',
				'sort_order' => $this->config->get('mifinity_sort_order')
			);
		}

		return $method_data;
	}

	public function addOrder($order_data) {

		$this->db->query("INSERT INTO `" . DB_PREFIX . "mifinity_order` SET `order_id` = '" . (int)$order_data['order_id'] . "', `created` = NOW(), `modified` = NOW(), `debug_data` = '" . $this->db->escape($order_data['debug_data']) . "', `amount` = '" . $this->currency->format($order_data['total'], $order_data['currency_code'], false, false) . "', `currency_code` = '" . $this->db->escape($order_data['currency_code']) . "', `transaction_id` = ''");

		return $this->db->getLastId();
	}

    public function getOrder($order_id) {
        $qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mifinity_order` WHERE `order_id` = '" . (int)$order_id . "' LIMIT 1");

        if ($qry->num_rows) {
            return $qry->row;
        } else {
            return false;
        }
    }

	public function updateOrder($mifinity_order_id, $mifinity_transaction_id, $type, $order_info) {
		$this->db->query("UPDATE `" . DB_PREFIX . "mifinity_order` SET `transaction_id` = '" . $this->db->escape($mifinity_transaction_id) . "', `modified` = now() WHERE `order_id` = '" . (int)$order_info['order_id'] . "'");

		$this->addTransaction($mifinity_order_id, $type, $order_info);

	}

	public function addTransaction($mifinity_order_id, $type, $order_info) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "mifinity_transactions` SET `mifinity_order_id` = '" . (int)$mifinity_order_id . "', `created` = NOW(), `type` = '" . $this->db->escape($type) . "', `amount` = '" . $this->currency->format($order_info['total'], $order_info['currency_code'], false, false) . "'");

		return $this->db->getLastId();
	}

	public function getCards($customer_id) {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mifinity_card WHERE customer_id = '" . (int)$customer_id . "'");

		$card_data = array();

		$this->load->model('account/address');

		foreach ($query->rows as $row) {

			$card_data[] = array(
				'card_id' => $row['card_id'],
				'customer_id' => $row['customer_id'],
				'token' => $row['token'],
				'digits' => '**** ' . $row['digits'],
				'expiry' => $row['expiry'],
				'type' => $row['type'],
			);
		}
		return $card_data;
	}

	public function checkToken($token_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mifinity_card WHERE token_id = '" . (int)$token_id . "'");
		if ($query->num_rows) {
			return true;
		} else {
			return false;
		}
	}

	public function addCard($order_id, $card_data) {
		$this->db->query("INSERT into " . DB_PREFIX . "mifinity_card SET customer_id = '" . $this->db->escape($card_data['customer_id']) . "', order_id = '" . $this->db->escape($order_id) . "', digits = '" . $this->db->escape($card_data['Last4Digits']) . "', expiry = '" . $this->db->escape($card_data['ExpiryDate']) . "', type = '" . $this->db->escape($card_data['CardType']) . "'");
	}

	public function updateCard($order_id, $token) {
		$this->db->query("UPDATE " . DB_PREFIX . "mifinity_card SET token = '" . $this->db->escape($token) . "' WHERE order_id = '" . (int)$order_id . "'");
	}

	public function updateFullCard($card_id, $token, $card_data) {
		$this->db->query("UPDATE " . DB_PREFIX . "mifinity_card SET token = '" . $this->db->escape($token) . "', digits = '" . $this->db->escape($card_data['Last4Digits']) . "', expiry = '" . $this->db->escape($card_data['ExpiryDate']) . "', type = '" . $this->db->escape($card_data['CardType']) . "' WHERE card_id = '" . (int)$card_id . "'");
	}

	public function deleteCard($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "mifinity_card WHERE order_id = '" . (int)$order_id . "'");
	}

//	public function getAccessCode($request) {
//		if ($this->config->get('mifinity_test')) {
//			$url = 'https://demo.mifinitypay.com/AccessCodes';
//		} else {
//			$url = 'https://api.mifinitypay.com/AccessCodes';
//		}
//
//		$response = $this->sendCurl($url, $request);
//		$response = json_decode($response);
//
//		return $response;
//	}

	public function initIframe($request) {
        $mifinity_accountHolderId = html_entity_decode($this->config->get('mifinity_account_holder_id'), ENT_QUOTES, 'UTF-8');
		if ($this->config->get('mifinity_test')) {
			$url = 'https://demo.mifinitypay.com/api/account-holders/'.$mifinity_accountHolderId.'/gateway/init-iframe';
		} else {
			$url = 'https://secure.mifinitypay.com/api/account-holders/'.$mifinity_accountHolderId.'/gateway/init-iframe';
		}

		$response = $this->sendCurl($url, $request);
//		$response = json_decode($response);

		return $response;
	}

	public function getStatusPayment($validationKey) {
        $mifinity_accountHolderId = html_entity_decode($this->config->get('mifinity_account_holder_id'), ENT_QUOTES, 'UTF-8');
		if ($this->config->get('mifinity_test')) {
            $url = 'https://demo.mifinitypay.com/api/account-holders/'.$mifinity_accountHolderId.'/gateway/payment-status/'.$validationKey;
        } else {
            $url = 'https://secure.mifinitypay.com/api/account-holders/'.$mifinity_accountHolderId.'/gateway/payment-status/'.$validationKey;
        }

		$response = $this->sendCurl($url, '', false);
//		$response = json_decode($response);

		return $response;
	}

	public function sendCurl($url, $data, $is_post=true) {
		$ch = curl_init($url);
        $mifinity_api_key = html_entity_decode($this->config->get('mifinity_api_key'), ENT_QUOTES, 'UTF-8');
//		$mifinity_username = html_entity_decode($this->config->get('mifinity_username'), ENT_QUOTES, 'UTF-8');
//		$mifinity_password = html_entity_decode($this->config->get('mifinity_password'), ENT_QUOTES, 'UTF-8');

		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json;charset=UTF-8","api-version: 1", "key:".$mifinity_api_key));
//		curl_setopt($ch, CURLOPT_USERPWD, $mifinity_username . ":" . $mifinity_password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if ($is_post) {
		    curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		} else {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		}

		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        if ($this->config->get('mifinity_test')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }else{
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        }
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);


		$response = curl_exec($ch);
//        print_r(json_decode($response));

		if (curl_errno($ch) != CURLE_OK) {
			$response = new stdClass();
			$response->errors = "POST Error: " . curl_error($ch) . " URL: $url";
			$this->log->write(array('error' => curl_error($ch), 'errno' => curl_errno($ch)), 'cURL failed');
			$response = json_decode($response);
		} else {
			$info = curl_getinfo($ch);
			if ($info['http_code'] != 200 && $info['http_code'] != 201) {
                $response = json_decode($response);
				$response->errors = 'Error connecting to MiFinity: ' . $info['http_code'];
			} else{ $response = json_decode($response);}
		}

		curl_close($ch);

		return $response;
	}

}
