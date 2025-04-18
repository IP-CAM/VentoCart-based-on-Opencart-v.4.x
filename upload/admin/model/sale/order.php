<?php
namespace Ventocart\Admin\Model\Sale;
/**
 * Class Order
 *
 * @package Ventocart\Admin\Model\Sale
 */
class Order extends \Ventocart\System\Engine\Model
{
	/**
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function getOrder(int $order_id): array
	{

		$order_query = $this->db->query("SELECT *, (SELECT `os`.`name` FROM `" . DB_PREFIX . "order_status` `os` WHERE `os`.`order_status_id` = `o`.`order_status_id` AND `os`.`language_id` = '" . (int) $this->config->get('config_language_id') . "') AS `order_status` FROM `" . DB_PREFIX . "order` `o` WHERE `o`.`order_id` = '" . (int) $order_id . "'");

		if ($order_query->num_rows) {
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE `country_id` = '" . (int) $order_query->row['payment_country_id'] . "'");

			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE `zone_id` = '" . (int) $order_query->row['payment_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}

			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE `country_id` = '" . (int) $order_query->row['shipping_country_id'] . "'");

			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}

			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE `zone_id` = '" . (int) $order_query->row['shipping_zone_id'] . "'");

			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}

			$reward = 0;

			$order_product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int) $order_id . "'");

			foreach ($order_product_query->rows as $product) {
				$reward += $product['reward'];
			}

			$this->load->model('customer/customer');

			$affiliate_info = $this->model_customer_customer->getCustomer($order_query->row['affiliate_id']);

			if ($affiliate_info) {
				$affiliate = $affiliate_info['firstname'] . ' ' . $affiliate_info['lastname'];
			} else {
				$affiliate = '';
			}

			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}

			return [
				'order_id' => $order_query->row['order_id'],
				'invoice_no' => $order_query->row['invoice_no'],
				'invoice_prefix' => $order_query->row['invoice_prefix'],
				'store_name' => $order_query->row['store_name'],
				'store_url' => $order_query->row['store_url'],
				'customer_id' => $order_query->row['customer_id'],
				'customer_group_id' => $order_query->row['customer_group_id'],
				'firstname' => $order_query->row['firstname'],
				'lastname' => $order_query->row['lastname'],
				'email' => $order_query->row['email'],
				'telephone' => $order_query->row['telephone'],
				'custom_field' => json_decode($order_query->row['custom_field'], true),
				'payment_address_id' => $order_query->row['payment_address_id'],
				'payment_firstname' => $order_query->row['payment_firstname'],
				'payment_lastname' => $order_query->row['payment_lastname'],
				'payment_company' => $order_query->row['payment_company'],
				'payment_address_1' => $order_query->row['payment_address_1'],
				'payment_phone' => $order_query->row['payment_phone'],
				'payment_postcode' => $order_query->row['payment_postcode'],
				'payment_city' => $order_query->row['payment_city'],
				'payment_zone_id' => $order_query->row['payment_zone_id'],
				'payment_zone' => $order_query->row['payment_zone'],
				'payment_zone_code' => $payment_zone_code,
				'payment_country_id' => $order_query->row['payment_country_id'],
				'payment_country' => $order_query->row['payment_country'],
				'payment_iso_code_2' => $payment_iso_code_2,
				'payment_iso_code_3' => $payment_iso_code_3,
				'payment_address_format' => $order_query->row['payment_address_format'],
				'payment_custom_field' => json_decode($order_query->row['payment_custom_field'], true),
				'payment_method' => json_decode($order_query->row['payment_method'], true),
				'shipping_address_id' => $order_query->row['shipping_address_id'],
				'shipping_firstname' => $order_query->row['shipping_firstname'],
				'shipping_lastname' => $order_query->row['shipping_lastname'],
				'shipping_company' => $order_query->row['shipping_company'],
				'shipping_address_1' => $order_query->row['shipping_address_1'],
				'shipping_phone' => $order_query->row['shipping_phone'],
				'shipping_postcode' => $order_query->row['shipping_postcode'],
				'shipping_city' => $order_query->row['shipping_city'],
				'shipping_zone_id' => $order_query->row['shipping_zone_id'],
				'shipping_zone' => $order_query->row['shipping_zone'],
				'shipping_zone_code' => $shipping_zone_code,
				'shipping_country_id' => $order_query->row['shipping_country_id'],
				'shipping_country' => $order_query->row['shipping_country'],
				'shipping_iso_code_2' => $shipping_iso_code_2,
				'shipping_iso_code_3' => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_custom_field' => json_decode($order_query->row['shipping_custom_field'], true),
				'shipping_method' => json_decode($order_query->row['shipping_method'], true),
				'comment' => $order_query->row['comment'],
				'total' => $order_query->row['total'],
				'reward' => $reward,
				'order_status_id' => $order_query->row['order_status_id'],
				'order_status' => $order_query->row['order_status'],
				'affiliate_id' => $order_query->row['affiliate_id'],
				'affiliate' => $affiliate,
				'commission' => $order_query->row['commission'],
				'language_id' => $order_query->row['language_id'],
				'language_code' => $language_code,
				'currency_id' => $order_query->row['currency_id'],
				'currency_code' => $order_query->row['currency_code'],
				'currency_value' => $order_query->row['currency_value'],
				'ip' => $order_query->row['ip'],
				'forwarded_ip' => $order_query->row['forwarded_ip'],
				'user_agent' => $order_query->row['user_agent'],
				'accept_language' => $order_query->row['accept_language'],
				'date_added' => $order_query->row['date_added'],
				'date_modified' => $order_query->row['date_modified']
			];
		} else {
			return [];
		}
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function getOrders(array $data = []): array
	{
		$sql = "SELECT `o`.`order_id`, CONCAT(`o`.`firstname`, ' ', `o`.`lastname`) AS customer, (SELECT `os`.`name` FROM `" . DB_PREFIX . "order_status` `os` WHERE `os`.`order_status_id` = `o`.`order_status_id` AND `os`.`language_id` = '" . (int) $this->config->get('config_language_id') . "') AS order_status, `o`.`store_name`, `o`.`shipping_method`, `o`.`total`, `o`.`currency_code`, `o`.`currency_value`, `o`.`date_added`, `o`.`date_modified` FROM `" . DB_PREFIX . "order` `o`";

		if (!empty($data['filter_order_status'])) {
			$implode = [];

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "`o`.`order_status_id` = '" . (int) $order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} elseif (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
			$sql .= " WHERE `o`.`order_status_id` = '" . (int) $data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE `o`.`order_status_id` > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND `o`.`order_id` = '" . (int) $data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer_id'])) {
			$sql .= " AND `o`.`customer_id` = '" . (int) $data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND LCASE(CONCAT(`o`.`firstname`, ' ', `o`.`lastname`)) LIKE '" . $this->db->escape('%' . oc_strtolower($data['filter_customer']) . '%') . "'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND LCASE(`o`.`email`) LIKE '" . $this->db->escape('%' . (string) $data['filter_email'] . '%') . "'";
		}

		if (!empty($data['filter_date_from'])) {
			$sql .= " AND DATE(`o`.`date_added`) >= DATE('" . $this->db->escape((string) $data['filter_date_from']) . "')";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(`o`.`date_added`) <= DATE('" . $this->db->escape((string) $data['filter_date_to']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND `o`.`total` = '" . (float) $data['filter_total'] . "'";
		}

		$sort_data = [
			'o.order_id',
			'o.store_name',
			'customer',
			'order_status',
			'o.date_added',
			'o.date_modified',
			'o.total'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `o`.`order_id`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
		}

		$order_data = [];

		$query = $this->db->query($sql);

		foreach ($query->rows as $key => $result) {
			$order_data[$key] = $result;

			$order_data[$key]['custom_field'] = isset($result['custom_field']) ? json_decode($result['custom_field'], true) : null;
			$order_data[$key]['payment_custom_field'] = isset($result['payment_custom_field']) ? json_decode($result['payment_custom_field'], true) : null;
			$order_data[$key]['payment_method'] = isset($result['payment_method']) ? json_decode($result['payment_method'], true) : null;
			$order_data[$key]['shipping_custom_field'] = isset($result['shipping_custom_field']) ? json_decode($result['shipping_custom_field'], true) : null;
			$order_data[$key]['shipping_method'] = isset($result['shipping_method']) ? json_decode($result['shipping_method'], true) : null;
		}

		return $order_data;
	}

	/**
	 * @param int $subscription_id
	 *
	 * @return array
	 */
	public function getOrdersBySubscriptionId(int $subscription_id): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE `subscription_id` = '" . (int) $subscription_id . "'");

		return $query->rows;
	}

	/**
	 * @param int $subscription_id
	 *
	 * @return int
	 */
	public function getTotalOrdersBySubscriptionId(int $subscription_id): int
	{
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order` WHERE `subscription_id` = '" . (int) $subscription_id . "'");

		return (int) $query->row['total'];
	}

	/**
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function getProducts(int $order_id): array
	{

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int) $order_id . "' ORDER BY order_product_id ASC");

		foreach ($query->rows as $index => $row) {
			if (!empty($row['variation_id'])) {
				$queryvar = $this->db->query("SELECT * FROM `" . DB_PREFIX . "variations` WHERE `variation_id` = '" . (int) $row['variation_id'] . "'");
				if (isset($queryvar->row['model']))
					$query->rows[$index]['model'] = $queryvar->row['model'];
				if (isset($queryvar->row['sku']))
					$query->rows[$index]['sku'] = $queryvar->row['sku'];

			} else {
				$queryvar = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . (int) $row['product_id'] . "'");
				if (isset($queryvar->row['model']))
					$query->rows[$index]['model'] = $queryvar->row['model'];
				if (isset($queryvar->row['sku']))
					$query->rows[$index]['sku'] = $queryvar->row['sku'];
			}


		}
		return $query->rows;
	}

	/**
	 * @param int $product_id
	 *
	 * @return int
	 */
	public function getTotalProductsByProductId(int $product_id): int
	{
		$sql = "SELECT SUM(`op`.`quantity`) AS `total` FROM `" . DB_PREFIX . "order_product` `op` LEFT JOIN `" . DB_PREFIX . "order` `o` ON (`op`.`order_id` = `o`.`order_id`) WHERE `op`.`product_id` = '" . (int) $product_id . "' AND `order_status_id` > '0'";

		$query = $this->db->query($sql);

		return (int) $query->row['total'];
	}

	/**
	 * @param int $order_id
	 * @param int $order_product_id
	 *
	 * @return array
	 */
	public function getOptions(int $order_id, int $order_product_id): array
	{
		$query = $this->db->query("
    SELECT 
        a.*, 
        b.option_id 
    FROM 
        `" . DB_PREFIX . "order_option` AS a
    LEFT JOIN 
        `" . DB_PREFIX . "order_product` AS c 
    ON 
        a.order_product_id = c.order_product_id
    LEFT JOIN 
        `" . DB_PREFIX . "product_options` AS b
    ON 
        c.product_id = b.product_id
    AND 
        a.product_option_id = b.product_option_id
    WHERE 
        a.order_id = '" . (int) $order_id . "' 
    AND 
        a.order_product_id = '" . (int) $order_product_id . "'
");


		return $query->rows;
	}

	public function formatOptionsForCart($options): array
	{
		$cart_format = [];
		foreach ($options as $result) {
			if (
				$result['type'] != "text" && $result['type'] != "file" && $result['type'] != "textarea"
				&& $result['type'] != "date" && $result['type'] != "time" && $result['type'] != "datetime"
			) {
				$cart_format[$result['option_id']] = $result['product_option_id'];
			} else {
				$cart_format[$result['product_option_id']] = $result['value'];


			}
		}
		return $cart_format;
	}
	/**
	 * @param int $order_id
	 * @param int $order_product_id
	 *
	 * @return array
	 */
	public function getSubscription(int $order_id, int $order_product_id): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_subscription` WHERE `order_id` = '" . (int) $order_id . "' AND `order_product_id` = '" . (int) $order_product_id . "'");

		return $query->row;
	}




	/**
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function getTotals(int $order_id): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = '" . (int) $order_id . "' ORDER BY `sort_order`");

		return $query->rows;
	}

	/**
	 * @param array $data
	 *
	 * @return int
	 */
	public function getTotalOrders(array $data = []): int
	{
		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order`";

		if (!empty($data['filter_order_status'])) {
			$implode = [];

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "`order_status_id` = '" . (int) $order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} elseif (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
			$sql .= " WHERE `order_status_id` = '" . (int) $data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE `order_status_id` > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND `order_id` = '" . (int) $data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer_id'])) {
			$sql .= " AND `customer_id` = '" . (int) $data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND LCASE(CONCAT(`firstname`, ' ', `lastname`)) LIKE '" . $this->db->escape('%' . oc_strtolower($data['filter_customer']) . '%') . "'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND LCASE(`email`) LIKE '" . $this->db->escape('%' . oc_strtolower($data['filter_email']) . '%') . "'";
		}

		if (!empty($data['filter_date_from'])) {
			$sql .= " AND DATE(`date_added`) >= DATE('" . $this->db->escape((string) $data['filter_date_from']) . "')";
		}

		if (!empty($data['filter_date_to'])) {
			$sql .= " AND DATE(`date_added`) <= DATE('" . $this->db->escape((string) $data['filter_date_to']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND `total` = '" . (float) $data['filter_total'] . "'";
		}

		$query = $this->db->query($sql);

		return (int) $query->row['total'];
	}




	public function getProductByOrderProductId(int $order_id, $product_id)
	{
		// Execute the SQL query to fetch rows from the order_product table based on order_id and product_id
		$order_product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int) $order_id . "' AND `product_id` = '" . (int) $product_id . "'");

		// Return the total number of rows returned by the query
		return $order_product_query->rows;
	}


	/**
	 * @param int $order_status_id
	 *
	 * @return int
	 */
	public function getTotalOrdersByOrderStatusId(int $order_status_id): int
	{
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order` WHERE `order_status_id` = '" . (int) $order_status_id . "' AND `order_status_id` > '0'");

		return (int) $query->row['total'];
	}

	/**
	 * @return int
	 */
	public function getTotalOrdersByProcessingStatus(): int
	{
		$implode = [];

		$order_statuses = $this->config->get('config_processing_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "`order_status_id` = '" . (int) $order_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode));

			return (int) $query->row['total'];
		} else {
			return 0;
		}
	}

	/**
	 * @return int
	 */
	public function getTotalOrdersByCompleteStatus(): int
	{
		$implode = [];

		$order_statuses = $this->config->get('config_complete_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "`order_status_id` = '" . (int) $order_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order` WHERE " . implode(" OR ", $implode) . "");

			return (int) $query->row['total'];
		} else {
			return 0;
		}
	}

	/**
	 * @param int $language_id
	 *
	 * @return int
	 */
	public function getTotalOrdersByLanguageId(int $language_id): int
	{
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order` WHERE `language_id` = '" . (int) $language_id . "' AND `order_status_id` > '0'");

		return (int) $query->row['total'];
	}

	/**
	 * @param int $currency_id
	 *
	 * @return int
	 */
	public function getTotalOrdersByCurrencyId(int $currency_id): int
	{
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order` WHERE `currency_id` = '" . (int) $currency_id . "' AND `order_status_id` > '0'");

		return (int) $query->row['total'];
	}

	/**
	 * @param array $data
	 *
	 * @return float
	 */
	public function getTotalSales(array $data = []): float
	{
		$sql = "SELECT SUM(`total`) AS `total` FROM `" . DB_PREFIX . "order`";

		if (!empty($data['filter_order_status'])) {
			$implode = [];

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "`order_status_id` = '" . (int) $order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} elseif (isset($data['filter_order_status_id']) && $data['filter_order_status_id'] !== '') {
			$sql .= " WHERE `order_status_id` = '" . (int) $data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE `order_status_id` > '0'";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND `order_id` = '" . (int) $data['filter_order_id'] . "'";
		}



		if (!empty($data['filter_customer_id'])) {
			$sql .= " AND `customer_id` = '" . (int) $data['filter_customer_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$sql .= " AND LCASE(CONCAT(`firstname`, ' ', `lastname`)) LIKE '" . $this->db->escape('%' . oc_strtolower($data['filter_customer']) . '%') . "'";
		}

		if (!empty($data['filter_email'])) {
			$sql .= " AND LCASE(`email`) LIKE '" . $this->db->escape('%' . oc_strtolower($data['filter_email']) . '%') . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(`date_added`) = DATE('" . $this->db->escape((string) $data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(`date_modified`) = DATE('" . $this->db->escape((string) $data['filter_date_modified']) . "')";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND `total` = '" . (float) $data['filter_total'] . "'";
		}

		$query = $this->db->query($sql);

		return (int) $query->row['total'];
	}

	/**
	 * @param int $order_id
	 *
	 * @return string
	 */
	public function createInvoiceNo(int $order_id): string
	{
		$order_info = $this->getOrder($order_id);

		if ($order_info && !$order_info['invoice_no']) {
			$query = $this->db->query("SELECT MAX(`invoice_no`) AS `invoice_no` FROM `" . DB_PREFIX . "order` WHERE `invoice_prefix` = '" . $this->db->escape($order_info['invoice_prefix']) . "'");

			if ($query->row['invoice_no']) {
				$invoice_no = $query->row['invoice_no'] + 1;
			} else {
				$invoice_no = 1;
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `invoice_no` = '" . (int) $invoice_no . "', `invoice_prefix` = '" . $this->db->escape($order_info['invoice_prefix']) . "' WHERE `order_id` = '" . (int) $order_id . "'");

			return $order_info['invoice_prefix'] . $invoice_no;
		}

		return '';
	}

	/**
	 * @param int $order_id
	 *
	 * @return int
	 */
	public function getRewardTotal(int $order_id): int
	{
		$query = $this->db->query("SELECT SUM(reward) AS `total` FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . (int) $order_id . "'");

		return (int) $query->row['total'];
	}

	/**
	 * @param int $order_id
	 * @param int $start
	 * @param int $limit
	 *
	 * @return array
	 */
	public function getHistories(int $order_id, int $start = 0, int $limit = 10): array
	{
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT `oh`.`date_added`, `os`.`name` AS `status`, `oh`.`comment`, `oh`.`notify` FROM `" . DB_PREFIX . "order_history` `oh` LEFT JOIN `" . DB_PREFIX . "order_status` `os` ON `oh`.`order_status_id` = `os`.`order_status_id` WHERE `oh`.`order_id` = '" . (int) $order_id . "' AND `os`.`language_id` = '" . (int) $this->config->get('config_language_id') . "' ORDER BY `oh`.`date_added` DESC LIMIT " . (int) $start . "," . (int) $limit);

		return $query->rows;
	}

	/**
	 * @param int $order_id
	 *
	 * @return int
	 */
	public function getTotalHistories(int $order_id): int
	{
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order_history` WHERE `order_id` = '" . (int) $order_id . "'");

		return (int) $query->row['total'];
	}

	/**
	 * @param int $order_status_id
	 *
	 * @return int
	 */
	public function getTotalHistoriesByOrderStatusId(int $order_status_id): int
	{
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order_history` WHERE `order_status_id` = '" . (int) $order_status_id . "'");

		return (int) $query->row['total'];
	}

	/**
	 * @param array $products
	 * @param int   $start
	 * @param int   $end
	 *
	 * @return array
	 */
	public function getEmailsByProductsOrdered(array $products, int $start, int $end): array
	{
		$implode = [];

		foreach ($products as $product_id) {
			$implode[] = "op.`product_id` = '" . (int) $product_id . "'";
		}

		$query = $this->db->query("SELECT DISTINCT `o`.`email` FROM `" . DB_PREFIX . "order` `o` LEFT JOIN `" . DB_PREFIX . "order_product` `op` ON (`o`.`order_id` = `op`.`order_id`) WHERE (" . implode(" OR ", $implode) . ") AND `o`.`order_status_id` <> '0' LIMIT " . (int) $start . "," . (int) $end);

		return $query->rows;
	}

	/**
	 * @param array $products
	 *
	 * @return int
	 */
	public function getTotalEmailsByProductsOrdered(array $products): int
	{
		$implode = [];

		foreach ($products as $product_id) {
			$implode[] = "`op`.`product_id` = '" . (int) $product_id . "'";
		}

		$query = $this->db->query("SELECT COUNT(DISTINCT `o`.`email`) AS `total` FROM `" . DB_PREFIX . "order` `o` LEFT JOIN `" . DB_PREFIX . "order_product` `op` ON (`o`.`order_id` = `op`.`order_id`) WHERE (" . implode(" OR ", $implode) . ") AND `o`.`order_status_id` <> '0'");

		return (int) $query->row['total'];
	}
}
