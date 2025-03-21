<?php
namespace Ventocart\Admin\Model\User;
/**
 * Class Api
 *
 * @package Ventocart\Admin\Model\User
 */
class Api extends \Ventocart\System\Engine\Model
{
	/**
	 * @param array $data
	 *
	 * @return int
	 */
	public function addApi(array $data): int
	{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "api` SET `username` = '" . $this->db->escape((string) $data['username']) . "', `key` = '" . $this->db->escape((string) $data['key']) . "', `status` = '" . (bool) (isset($data['status']) ? $data['status'] : 0) . "', `date_added` = NOW(), `date_modified` = NOW()");

		$api_id = $this->db->getLastId();

		return $api_id;
	}

	/**
	 * @param int   $api_id
	 * @param array $data
	 *
	 * @return void
	 */
	public function editApi(int $api_id, array $data): void
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "api` SET `username` = '" . $this->db->escape((string) $data['username']) . "', `key` = '" . $this->db->escape((string) $data['key']) . "', `status` = '" . (bool) (isset($data['status']) ? $data['status'] : 0) . "', `date_modified` = NOW() WHERE `api_id` = '" . (int) $api_id . "'");

		// The IP-related deletion and insertion have been removed.

	}

	/**
	 * @param int $api_id
	 *
	 * @return void
	 */
	public function deleteApi(int $api_id): void
	{
		$this->db->query("DELETE FROM `" . DB_PREFIX . "api` WHERE `api_id` = '" . (int) $api_id . "'");
	}

	/**
	 * @param int $api_id
	 *
	 * @return array
	 */
	public function getApi(int $api_id): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api` WHERE `api_id` = '" . (int) $api_id . "'");

		return $query->row;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function getApis(array $data = []): array
	{
		$sql = "SELECT * FROM `" . DB_PREFIX . "api`";

		$sort_data = [
			'username',
			'status',
			'date_added',
			'date_modified'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `username`";
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

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * @return int
	 */
	public function getTotalApis(): int
	{
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "api`");

		return (int) $query->row['total'];
	}



	/**
	 * @param int $api_id
	 *
	 * @return array
	 */


	/**
	 * @param int    $api_id
	 * @param string $session_id
	 * @param string $ip
	 *
	 * @return int
	 */
	public function addSession(int $api_id, string $session_id, string $ip): int
	{

		$this->db->query("INSERT INTO `" . DB_PREFIX . "api_session` SET `api_id` = '" . (int) $api_id . "', `session_id` = '" . $this->db->escape($session_id) . "', `ip` = '" . $this->db->escape($ip) . "', `date_added` = NOW(), `date_modified` = NOW()");

		return $this->db->getLastId();
	}


	/**
	 * @param int $api_id
	 *
	 * @return array
	 */
	public function getSessions(int $api_id): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_session` WHERE `api_id` = '" . (int) $api_id . "'");

		return $query->rows;
	}

	/**
	 * @param int $api_session_id
	 *
	 * @return void
	 */
	public function deleteSession(int $api_session_id): void
	{
		$this->db->query("DELETE FROM `" . DB_PREFIX . "api_session` WHERE `api_session_id` = '" . (int) $api_session_id . "'");
	}

	/**
	 * @param string $session_id
	 *
	 * @return void
	 */
	public function deleteSessionBySessionId(string $session_id): void
	{
		$this->db->query("DELETE FROM `" . DB_PREFIX . "api_session` WHERE `session_id` = '" . $this->db->escape($session_id) . "'");
	}
}
