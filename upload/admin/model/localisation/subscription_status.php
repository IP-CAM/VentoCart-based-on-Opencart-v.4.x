<?php
namespace Ventocart\Admin\Model\Localisation;
/**
 * Class SubscriptionStatus
 *
 * @package Ventocart\Admin\Model\Localisation
 */
class SubscriptionStatus extends \Ventocart\System\Engine\Model {
	/**
	 * @param array $data
	 *
	 * @return int
	 */
	public function addSubscriptionStatus(array $data): int {
		foreach ($data['subscription_status'] as $language_id => $value) {
			if (isset($subscription_status_id)) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "subscription_status` SET `subscription_status_id` = '" . (int)$subscription_status_id . "', `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "subscription_status` SET `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($value['name']) . "'");

				$subscription_status_id = $this->db->getLastId();
			}
		}

		$this->cache->delete('subscription_status');

		return $subscription_status_id;
	}

	/**
	 * @param int   $subscription_status_id
	 * @param array $data
	 *
	 * @return void
	 */
	public function editSubscriptionStatus(int $subscription_status_id, array $data): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "subscription_status` WHERE `subscription_status_id` = '" . (int)$subscription_status_id . "'");

		foreach ($data['subscription_status'] as $language_id => $value) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "subscription_status` SET `subscription_status_id` = '" . (int)$subscription_status_id . "', `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($value['name']) . "'");
		}

		$this->cache->delete('subscription_status');
	}

	/**
	 * @param int $subscription_status_id
	 *
	 * @return void
	 */
	public function deleteSubscriptionStatus(int $subscription_status_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "subscription_status` WHERE `subscription_status_id` = '" . (int)$subscription_status_id . "'");

		$this->cache->delete('subscription_status');
	}

	/**
	 * @param int $subscription_status_id
	 *
	 * @return array
	 */
	public function getSubscriptionStatus(int $subscription_status_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "subscription_status` WHERE `subscription_status_id` = '" . (int)$subscription_status_id . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function getSubscriptionStatuses(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "subscription_status` WHERE `language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `name`";

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

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$subscription_status_data = $this->cache->get('subscription_status.' . md5($sql));

		if (!$subscription_status_data) {
			$query = $this->db->query($sql);

			$subscription_status_data = $query->rows;

			$this->cache->set('subscription_status.' . md5($sql), $subscription_status_data);
		}

		return $subscription_status_data;
	}

	/**
	 * @param int $subscription_status_id
	 *
	 * @return array
	 */
	public function getDescriptions(int $subscription_status_id): array {
		$subscription_status_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "subscription_status` WHERE `subscription_status_id` = '" . (int)$subscription_status_id . "'");

		foreach ($query->rows as $result) {
			$subscription_status_data[$result['language_id']] = ['name' => $result['name']];
		}

		return $subscription_status_data;
	}

	/**
	 * @return int
	 */
	public function getTotalSubscriptionStatuses(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "subscription_status` WHERE `language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return (int)$query->row['total'];
	}
}
