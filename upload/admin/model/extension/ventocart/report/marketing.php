<?php
namespace Ventocart\Admin\Model\Extension\Ventocart\Report;
/**
 * Class Marketing
 *
 * @package Ventocart\Admin\Model\Extension\Ventocart\Report
 */
class Marketing extends \Ventocart\System\Engine\Model {
	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function getMarketing(array $data = []): array {
		$sql = "SELECT m.`marketing_id`, m.`name` AS campaign, m.`code`, m.`clicks` AS clicks, (SELECT COUNT(DISTINCT `order_id`) FROM `" . DB_PREFIX . "order` o1 WHERE o1.`marketing_id` = m.`marketing_id`";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o1.`order_status_id` = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o1.`order_status_id` > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o1.`date_added`) >= DATE('" . $this->db->escape((string)$data['filter_date_start']) . "')";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o1.`date_added`) <= DATE('" . $this->db->escape((string)$data['filter_date_end']) . "')";
		}

		$sql .= ") AS `orders`, (SELECT SUM(`total`) FROM `" . DB_PREFIX . "order` o2 WHERE o2.`marketing_id` = m.`marketing_id`";

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o2.`order_status_id` = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o2.`order_status_id` > '0'";
		}

		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o2.`date_added`) >= DATE('" . $this->db->escape((string)$data['filter_date_start']) . "')";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o2.`date_added`) <= DATE('" . $this->db->escape((string)$data['filter_date_end']) . "')";
		}

		$sql .= " GROUP BY o2.`marketing_id`) AS `total` FROM `" . DB_PREFIX . "marketing` m ORDER BY m.`date_added` ASC";

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
	public function getTotalMarketing(array $data = []): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "marketing`");

		return (int)$query->row['total'];
	}
}
