<?php
namespace Ventocart\Catalog\Model\Account;
/**
 * Class Affiliate
 *
 * @package Ventocart\Catalog\Model\Account
 */
class Affiliate extends \Ventocart\System\Engine\Model
{
	/**
	 * @param int   $customer_id
	 * @param array $data
	 *
	 * @return void
	 */
	public function addAffiliate(int $customer_id, array $data): void
	{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_affiliate` SET `customer_id` = '" . (int) $customer_id . "', `company` = '" . $this->db->escape((string) $data['company']) . "', `website` = '" . $this->db->escape((string) $data['website']) . "', `tracking` = '" . $this->db->escape(oc_token(10)) . "', `commission` = '" . (float) $this->config->get('config_affiliate_commission') . "', `tax` = '" . $this->db->escape((string) $data['tax']) . "', `payment_method` = '" . $this->db->escape((string) $data['payment_method']) . "', `cheque` = '" . $this->db->escape((string) $data['cheque']) . "', `paypal` = '" . $this->db->escape((string) $data['paypal']) . "', `bank_name` = '" . $this->db->escape((string) $data['bank_name']) . "', `bank_branch_number` = '" . $this->db->escape((string) $data['bank_branch_number']) . "', `bank_swift_code` = '" . $this->db->escape((string) $data['bank_swift_code']) . "', `bank_account_name` = '" . $this->db->escape((string) $data['bank_account_name']) . "', `bank_account_number` = '" . $this->db->escape((string) $data['bank_account_number']) . "', `custom_field` = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', `status` = '" . (int) !$this->config->get('config_affiliate_approval') . "', `date_added` = NOW()");

		if ($this->config->get('config_affiliate_approval')) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_approval` SET `customer_id` = '" . (int) $customer_id . "', `type` = 'affiliate', `date_added` = NOW()");
		}
	}

	/**
	 * @param int   $customer_id
	 * @param array $data
	 *
	 * @return void
	 */
	public function editAffiliate(int $customer_id, array $data): void
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "customer_affiliate` SET `company` = '" . $this->db->escape((string) $data['company']) . "', `website` = '" . $this->db->escape((string) $data['website']) . "', `commission` = '" . (float) $this->config->get('config_affiliate_commission') . "', `tax` = '" . $this->db->escape((string) $data['tax']) . "', `payment_method` = '" . $this->db->escape((string) $data['payment_method']) . "', `cheque` = '" . $this->db->escape((string) $data['cheque']) . "', `paypal` = '" . $this->db->escape((string) $data['paypal']) . "', `bank_name` = '" . $this->db->escape((string) $data['bank_name']) . "', `bank_branch_number` = '" . $this->db->escape((string) $data['bank_branch_number']) . "', `bank_swift_code` = '" . $this->db->escape((string) $data['bank_swift_code']) . "', `bank_account_name` = '" . $this->db->escape((string) $data['bank_account_name']) . "', `bank_account_number` = '" . $this->db->escape((string) $data['bank_account_number']) . "', `custom_field` = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "' WHERE `customer_id` = '" . (int) $customer_id . "'");
	}

	/**
	 * @param int $customer_id
	 *
	 * @return array
	 */
	public function getAffiliate(int $customer_id): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_affiliate` WHERE `customer_id` = '" . (int) $customer_id . "'");

		if ($query->num_rows) {
			return $query->row + ['custom_field' => json_decode($query->row['custom_field'], true)];
		} else {
			return [];
		}
	}

	/**
	 * @param string $code
	 *
	 * @return array
	 */
	public function getAffiliateByTracking(string $code): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_affiliate` WHERE `tracking` = '" . $this->db->escape($code) . "'");

		if ($query->num_rows) {
			return $query->row + ['custom_field' => json_decode($query->row['custom_field'], true)];
		} else {
			return [];
		}
	}

	/**
	 * @param int    $customer_id
	 * @param string $ip
	 * @param string $country
	 *
	 * @return void
	 */
	public function addReport(int $customer_id, string $ip, string $country = ''): void
	{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_affiliate_report` SET `customer_id` = '" . (int) $customer_id . "', `ip` = '" . $this->db->escape($ip) . "', `country` = '" . $this->db->escape($country) . "', `date_added` = NOW()");
	}
}
