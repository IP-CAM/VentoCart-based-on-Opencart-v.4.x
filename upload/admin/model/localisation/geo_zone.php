<?php
namespace Ventocart\Admin\Model\Localisation;
/**
 * Class GeoZone
 *
 * @package Ventocart\Admin\Model\Localisation
 */
class GeoZone extends \Ventocart\System\Engine\Model {
	/**
	 * @param array $data
	 *
	 * @return int
	 */
	public function addGeoZone(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "geo_zone` SET `name` = '" . $this->db->escape((string)$data['name']) . "', `description` = '" . $this->db->escape((string)$data['description']) . "'");

		$geo_zone_id = $this->db->getLastId();

		if (isset($data['zone_to_geo_zone'])) {
			foreach ($data['zone_to_geo_zone'] as $value) {
				$this->db->query("DELETE FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$geo_zone_id . "' AND `country_id` = '" . (int)$value['country_id'] . "' AND `zone_id` = '" . (int)$value['zone_id'] . "'");

				$this->db->query("INSERT INTO `" . DB_PREFIX . "zone_to_geo_zone` SET `country_id` = '" . (int)$value['country_id'] . "', `zone_id` = '" . (int)$value['zone_id'] . "', `geo_zone_id` = '" . (int)$geo_zone_id . "'");
			}
		}

		$this->cache->delete('geo_zone');

		return $geo_zone_id;
	}

	/**
	 * @param int   $geo_zone_id
	 * @param array $data
	 *
	 * @return void
	 */
	public function editGeoZone(int $geo_zone_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "geo_zone` SET `name` = '" . $this->db->escape((string)$data['name']) . "', `description` = '" . $this->db->escape((string)$data['description']) . "' WHERE `geo_zone_id` = '" . (int)$geo_zone_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$geo_zone_id . "'");

		if (isset($data['zone_to_geo_zone'])) {
			foreach ($data['zone_to_geo_zone'] as $value) {
				$this->db->query("DELETE FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$geo_zone_id . "' AND `country_id` = '" . (int)$value['country_id'] . "' AND `zone_id` = '" . (int)$value['zone_id'] . "'");

				$this->db->query("INSERT INTO `" . DB_PREFIX . "zone_to_geo_zone` SET `country_id` = '" . (int)$value['country_id'] . "', `zone_id` = '" . (int)$value['zone_id'] . "', `geo_zone_id` = '" . (int)$geo_zone_id . "'");
			}
		}

		$this->cache->delete('geo_zone');
	}

	/**
	 * @param int $geo_zone_id
	 *
	 * @return void
	 */
	public function deleteGeoZone(int $geo_zone_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "geo_zone` WHERE `geo_zone_id` = '" . (int)$geo_zone_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$geo_zone_id . "'");

		$this->cache->delete('geo_zone');
	}

	/**
	 * @param int $geo_zone_id
	 *
	 * @return array
	 */
	public function getGeoZone(int $geo_zone_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "geo_zone` WHERE `geo_zone_id` = '" . (int)$geo_zone_id . "'");

		return $query->row;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function getGeoZones(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "geo_zone`";

		$sort_data = [
			'name',
			'description'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `name`";
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

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$geo_zone_data = $this->cache->get('geo_zone.' . md5($sql));

		if (!$geo_zone_data) {
			$query = $this->db->query($sql);

			$geo_zone_data = $query->rows;

			$this->cache->set('geo_zone.' . md5($sql), $geo_zone_data);
		}

		return $geo_zone_data;
	}

	/**
	 * @return int
	 */
	public function getTotalGeoZones(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "geo_zone`");

		return (int)$query->row['total'];
	}

	/**
	 * @param int $geo_zone_id
	 *
	 * @return array
	 */
	public function getZoneToGeoZones(int $geo_zone_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$geo_zone_id . "'");

		return $query->rows;
	}

	/**
	 * @param int $geo_zone_id
	 *
	 * @return int
	 */
	public function getTotalZoneToGeoZoneByGeoZoneId(int $geo_zone_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `geo_zone_id` = '" . (int)$geo_zone_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * @param int $country_id
	 *
	 * @return int
	 */
	public function getTotalZoneToGeoZoneByCountryId(int $country_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `country_id` = '" . (int)$country_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * @param int $zone_id
	 *
	 * @return int
	 */
	public function getTotalZoneToGeoZoneByZoneId(int $zone_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "zone_to_geo_zone` WHERE `zone_id` = '" . (int)$zone_id . "'");

		return (int)$query->row['total'];
	}
}
