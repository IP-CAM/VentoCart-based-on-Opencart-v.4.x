<?php
namespace Ventocart\Admin\Model\Report;
/**
 * Class Online
 *
 * @package Ventocart\Admin\Model\Report
 */
class Online extends \Ventocart\System\Engine\Model {
	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function getOnline(array $data = []): array {
		$sql = "SELECT `co`.`ip`, `co`.`customer_id`, `co`.`url`, `co`.`referer`, `co`.`date_added` FROM `" . DB_PREFIX . "customer_online` `co` LEFT JOIN `" . DB_PREFIX . "customer` `c` ON (`co`.`customer_id` = `c`.`customer_id`)";

		$implode = [];

		if (!empty($data['filter_ip'])) {
			$implode[] = "`co`.`ip` LIKE '" . $this->db->escape((string)$data['filter_ip']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "`co`.`customer_id` > '0' AND LCASE(CONCAT(`c`.`firstname`, ' ', `c`.`lastname`)) LIKE '" . $this->db->escape(oc_strtolower($data['filter_customer'])) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sql .= " ORDER BY `co`.`date_added` DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * @param array $data
	 *
	 * @return int
	 */
	public function getTotalOnline(array $data = []): int {
		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "customer_online` `co` LEFT JOIN `" . DB_PREFIX . "customer` `c` ON (`co`.`customer_id` = `c`.`customer_id`)";

		$implode = [];

		if (!empty($data['filter_ip'])) {
			$implode[] = "`co`.`ip` LIKE '" . $this->db->escape((string)$data['filter_ip']) . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "`co`.`customer_id` > '0' AND LCASE(CONCAT(`c`.`firstname`, ' ', `c`.`lastname`)) LIKE '" . $this->db->escape(oc_strtolower($data['filter_customer'])) . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return (int)$query->row['total'];
	}
}
