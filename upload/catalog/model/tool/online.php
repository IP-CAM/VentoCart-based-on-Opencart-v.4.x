<?php
namespace Ventocart\Catalog\Model\Tool;
/**
 * Class Online
 *
 * @package Ventocart\Catalog\Model\Tool
 */
class Online extends \Ventocart\System\Engine\Model {
	/**
	 * @param string $ip
	 * @param int    $customer_id
	 * @param string $url
	 * @param string $referer
	 *
	 * @return void
	 */
	public function addOnline(string $ip, int $customer_id, string $url, string $referer): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_online` WHERE `date_added` < '" . date('Y-m-d H:i:s', strtotime('-' . (int)$this->config->get('config_customer_online_expire') . ' hour')) . "'");

		$this->db->query("REPLACE INTO `" . DB_PREFIX . "customer_online` SET `ip` = '" . $this->db->escape($ip) . "', `customer_id` = '" . (int)$customer_id . "', `url` = '" . $this->db->escape($url) . "', `referer` = '" . $this->db->escape($referer) . "', `date_added` = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
	}
}
