<?php
namespace Ventocart\Catalog\Model\Account;
/**
 * Class Download
 *
 * @package Ventocart\Catalog\Model\Account
 */
class Download extends \Ventocart\System\Engine\Model
{
	/**
	 * @param int $download_id
	 *
	 * @return array
	 */
	public function getDownload(int $download_id): array
	{
		$implode = [];

		$order_statuses = (array) $this->config->get('config_complete_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "`o`.`order_status_id` = '" . (int) $order_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT `d`.`filename`, `d`.`mask` FROM `" . DB_PREFIX . "order` `o` LEFT JOIN `" . DB_PREFIX . "order_product` `op` ON (`o`.`order_id` = `op`.`order_id`) LEFT JOIN `" . DB_PREFIX . "product_to_download` `p2d` ON (`op`.`product_id` = `p2d`.`product_id`) LEFT JOIN `" . DB_PREFIX . "download` `d` ON (`p2d`.`download_id` = `d`.`download_id`) WHERE `o`.`customer_id` = '" . (int) $this->customer->getId() . "' AND (" . implode(" OR ", $implode) . ") AND `d`.`download_id` = '" . (int) $download_id . "'");

			return $query->row;
		}

		return [];
	}

	/**
	 * @param int $start
	 * @param int $limit
	 *
	 * @return array
	 */
	public function getDownloads(int $start = 0, int $limit = 20): array
	{
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 20;
		}

		$implode = [];

		$order_statuses = (array) $this->config->get('config_complete_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "`o`.`order_status_id` = '" . (int) $order_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT DISTINCT `d`.`download_id`, `o`.`order_id`, `o`.`date_added`, `dd`.`name`, `d`.`filename` FROM `" . DB_PREFIX . "order` `o` LEFT JOIN `" . DB_PREFIX . "order_product` `op` ON (`o`.`order_id` = `op`.`order_id`) LEFT JOIN `" . DB_PREFIX . "product_to_download` `p2d` ON (`op`.`product_id` = `p2d`.`product_id`) LEFT JOIN `" . DB_PREFIX . "download` `d` ON (`p2d`.`download_id` = `d`.`download_id`) LEFT JOIN `" . DB_PREFIX . "download_description` `dd` ON (`d`.`download_id` = `dd`.`download_id`) WHERE `o`.`customer_id` = '" . (int) $this->customer->getId() .
				"' AND (" . implode(" OR ", $implode) . ") AND `dd`.`language_id` = '" . (int) $this->config->get('config_language_id') . "' ORDER BY `dd`.`name` ASC LIMIT " . (int) $start . "," . (int) $limit);

			return $query->rows;
		}

		return [];
	}

	/**
	 * @return int
	 */
	public function getTotalDownloads(): int
	{
		$implode = [];

		$order_statuses = (array) $this->config->get('config_complete_status');

		foreach ($order_statuses as $order_status_id) {
			$implode[] = "`o`.`order_status_id` = '" . (int) $order_status_id . "'";
		}

		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order` `o` LEFT JOIN `" . DB_PREFIX . "order_product` `op` ON (`o`.`order_id` = `op`.`order_id`) LEFT JOIN `" . DB_PREFIX . "product_to_download` `p2d` ON (`op`.`product_id` = `p2d`.`product_id`) WHERE `o`.`customer_id` = '" . (int) $this->customer->getId() .
				"' AND (" . implode(" OR ", $implode) . ") AND `p2d`.`download_id` > 0");

			return $query->row['total'];
		}

		return 0;
	}

	/**
	 * @param int    $download_id
	 * @param string $ip
	 * @param string $country
	 *
	 * @return void
	 */
	public function addReport(int $download_id, string $ip, string $country = ''): void
	{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "download_report` SET `download_id` = '" . (int) $download_id . "', `ip` = '" . $this->db->escape($ip) . "', `country` = '" . $this->db->escape($country) . "', `date_added` = NOW()");
	}
}
