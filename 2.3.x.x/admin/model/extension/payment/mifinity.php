<?php

class ModelExtensionPaymentMiFinity extends Model {

	public function install() {
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mifinity_order` (
			  `mifinity_order_id` int(11) NOT NULL AUTO_INCREMENT,
			  `order_id` int(11) NOT NULL,
			  `created` DATETIME NOT NULL,
			  `modified` DATETIME NOT NULL,
			  `amount` DECIMAL( 10, 2 ) NOT NULL,
			  `currency_code` CHAR(3) NOT NULL,
			  `transaction_id` VARCHAR(24) NOT NULL,
			  `debug_data` TEXT,
			  `capture_status` INT(1) DEFAULT NULL,
			  `void_status` INT(1) DEFAULT NULL,
			  `refund_status` INT(1) DEFAULT NULL,
			  PRIMARY KEY (`mifinity_order_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mifinity_transactions` (
			  `mifinity_transaction_id` int(11) NOT NULL AUTO_INCREMENT,
			  `mifinity_order_id` int(11) NOT NULL,
			  `transaction_id` VARCHAR(24) NOT NULL,
			  `created` DATETIME NOT NULL,
			  `type` ENUM('auth', 'payment', 'refund', 'void') DEFAULT NULL,
			  `amount` DECIMAL( 10, 2 ) NOT NULL,
			  PRIMARY KEY (`mifinity_transaction_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mifinity_card` (
			  `card_id` INT(11) NOT NULL AUTO_INCREMENT,
			  `customer_id` INT(11) NOT NULL,
			  `order_id` INT(11) NOT NULL,
			  `token` VARCHAR(50) NOT NULL,
			  `digits` VARCHAR(4) NOT NULL,
			  `expiry` VARCHAR(5) NOT NULL,
			  `type` VARCHAR(50) NOT NULL,
			  PRIMARY KEY (`card_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
	}

	public function uninstall() {
		//$this->model_setting_setting->deleteSetting($this->request->get['extension']);
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "mifinity_order`;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "mifinity_transactions`;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "mifinity_card`;");
	}

	public function getOrder($order_id) {
		$qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mifinity_order` WHERE `order_id` = '" . (int)$order_id . "' LIMIT 1");

		if ($qry->num_rows) {
			$order = $qry->row;
			$order['transactions'] = $this->getTransactions($order['mifinity_order_id']);
			return $order;
		} else {
			return false;
		}
	}

	public function addRefundRecord($order, $result) {
		$transaction_id = $result->TransactionID;
		$total_amount = $result->Refund->TotalAmount / 100;
		$refund_amount = $order['refund_amount'] + $total_amount;

		if (isset($order['refund_transaction_id']) && !empty($order['refund_transaction_id'])) {
			$order['refund_transaction_id'] .= ',';
		}
		$order['refund_transaction_id'] .= $transaction_id;

		$this->db->query("UPDATE `" . DB_PREFIX . "mifinity_order` SET `modified` = NOW(), refund_amount = '" . (double)$refund_amount . "', `refund_transaction_id` = '" . $this->db->escape($order['refund_transaction_id']) . "' WHERE mifinity_order_id = '" . $order['mifinity_order_id'] . "'");
	}

	public function capture($order_id, $capture_amount, $currency) {
		$mifinity_order = $this->getOrder($order_id);

		if ($mifinity_order && $capture_amount > 0 ) {

			$capture_data = new stdClass();
//			$capture_data->Payment = new stdClass();
//			$capture_data->Payment->TotalAmount = (int)number_format($capture_amount, 2, '.', '') * 100;
//			$capture_data->Payment->CurrencyCode = $currency;
//			$capture_data->TransactionID = $mifinity_order['transaction_id'];
            //todo
            $mifinity_accountHolderId = html_entity_decode($this->config->get('mifinity_account_holder_id'), ENT_QUOTES, 'UTF-8');

			if ($this->config->get('mifinity_test')) {
				$url = 'https://demo.mifinitypay.com/account-holders/'.$mifinity_accountHolderId.'/gateway/payment/capture';
			} else {
				$url = 'https://demo.mifinitypay.com/account-holders/'.$mifinity_accountHolderId.'/gateway/payment/capture';
			}

			$response = $this->sendCurl($url, $capture_data);

			return json_decode($response);

		} else {
			return false;
		}
	}

	public function updateCaptureStatus($mifinity_order_id, $status) {
		$this->db->query("UPDATE `" . DB_PREFIX . "mifinity_order` SET `capture_status` = '" . (int)$status . "' WHERE `mifinity_order_id` = '" . (int)$mifinity_order_id . "'");
	}

	public function updateTransactionId($mifinity_order_id, $transaction_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "mifinity_order` SET `transaction_id` = '" . $transaction_id . "' WHERE `mifinity_order_id` = '" . (int)$mifinity_order_id . "'");
	}

	public function void($order_id) {
		$mifinity_order = $this->getOrder($order_id);
		if ($mifinity_order) {

			$data = new stdClass();
//			$data->TransactionID = $mifinity_order['transaction_id'];
            //todo
            $mifinity_accountHolderId = html_entity_decode($this->config->get('mifinity_account_holder_id'), ENT_QUOTES, 'UTF-8');

			if ($this->config->get('mifinity_test')) {
                $url = 'https://demo.mifinitypay.com/api/account-holders/'.$mifinity_accountHolderId.'/gateway/payment/void-authorization';
			} else {
                $url = 'https://secure.mifinitypay.com/api/account-holders/'.$mifinity_accountHolderId.'/gateway/payment/void-authorization';
			}

			$response = $this->sendCurl($url, $data);

			return json_decode($response);

		} else {
			return false;
		}
	}

	public function updateVoidStatus($mifinity_order_id, $status) {
		$this->db->query("UPDATE `" . DB_PREFIX . "mifinity_order` SET `void_status` = '" . (int)$status . "' WHERE `mifinity_order_id` = '" . (int)$mifinity_order_id . "'");
	}

	public function refund($order_id, $refund_amount) {
		$mifinity_order = $this->getOrder($order_id);

		if ($mifinity_order && $refund_amount > 0) {

			$refund_data = new stdClass();
//			$refund_data->Refund = new stdClass();
//			$refund_data->Refund->TotalAmount = (int)number_format($refund_amount, 2, '.', '') * 100;
//			$refund_data->Refund->TransactionID = $mifinity_order['transaction_id'];
            //todo
        $mifinity_accountHolderId = html_entity_decode($this->config->get('mifinity_account_holder_id'), ENT_QUOTES, 'UTF-8');

			if ($this->config->get('mifinity_test')) {

				$url = 'https://demo.mifinitypay.com/api/account-holders/'.$mifinity_accountHolderId.'/gateway/payment/revert';
			} else {
				$url = 'https://secure.mifinitypay.com/api/account-holders/'.$mifinity_accountHolderId.'/gateway/payment/revert';
			}

			$response = $this->sendCurl($url, $refund_data);

			return json_decode($response);
		} else {
			return false;
		}
	}

	public function updateRefundStatus($mifinity_order_id, $status) {
		$this->db->query("UPDATE `" . DB_PREFIX . "mifinity_order` SET `refund_status` = '" . (int)$status . "' WHERE `mifinity_order_id` = '" . (int)$mifinity_order_id . "'");
	}

	public function sendCurl($url, $data) {
		$ch = curl_init($url);

        $mifinity_api_key = html_entity_decode($this->config->get('mifinity_api_key'), ENT_QUOTES, 'UTF-8');
//		$mifinity_username = html_entity_decode($this->config->get('mifinity_username'), ENT_QUOTES, 'UTF-8');
//		$mifinity_password = html_entity_decode($this->config->get('mifinity_password'), ENT_QUOTES, 'UTF-8');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Api-Version: 1"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("key: ".$mifinity_api_key));
//		curl_setopt($ch, CURLOPT_USERPWD, $mifinity_username . ":" . $mifinity_password);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        if ($this->config->get('mifinity_test')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //todo
        }else{
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        }

		$response = curl_exec($ch);

		if (curl_errno($ch) != CURLE_OK) {
			$response = new stdClass();
			$response->errors = "POST Error: " . curl_error($ch) . " URL: $url";
			$response = json_encode($response);
		} else {
			$info = curl_getinfo($ch);
			if ($info['http_code'] == 401 || $info['http_code'] == 404) {
				$response = new stdClass();
				$response->errors = "Please check the API Key and Password";
				$response = json_encode($response);
			}
		}

		curl_close($ch);

		return $response;
	}

	private function getTransactions($mifinity_order_id) {
		$qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mifinity_transactions` WHERE `mifinity_order_id` = '" . (int)$mifinity_order_id . "'");

		if ($qry->num_rows) {
			return $qry->rows;
		} else {
			return false;
		}
	}

	public function addTransaction($mifinity_order_id, $transactionid, $type, $total, $currency) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "mifinity_transactions` SET `mifinity_order_id` = '" . (int)$mifinity_order_id . "', `created` = NOW(), `transaction_id` = '" . $this->db->escape($transactionid) . "', `type` = '" . $this->db->escape($type) . "', `amount` = '" . $this->currency->format($total, $currency, false, false) . "'");
	}

	public function getTotalCaptured($mifinity_order_id) {
		$query = $this->db->query("SELECT SUM(`amount`) AS `total` FROM `" . DB_PREFIX . "mifinity_transactions` WHERE `mifinity_order_id` = '" . (int)$mifinity_order_id . "' AND `type` = 'payment' ");

		return (double)$query->row['total'];
	}

	public function getTotalRefunded($mifinity_order_id) {
		$query = $this->db->query("SELECT SUM(`amount`) AS `total` FROM `" . DB_PREFIX . "mifinity_transactions` WHERE `mifinity_order_id` = '" . (int)$mifinity_order_id . "' AND `type` = 'refund'");

		return (double)$query->row['total'];
	}

}