<?php
namespace Ventocart\Catalog\Model\Localisation;
/**
 * Class Country
 *
 * @package Ventocart\Catalog\Model\Localisation
 */
class Country extends \Ventocart\System\Engine\Model {
	/**
	 * @param int $country_id
	 *
	 * @return array
	 */
	public function getCountry(int $country_id): array {
		$query = $this->db->query("SELECT *, `c`.`name` FROM `" . DB_PREFIX . "country` `c` LEFT JOIN `" . DB_PREFIX . "address_format` af ON (`c`.`address_format_id` = `af`.`address_format_id`) WHERE `c`.`country_id` = '" . (int)$country_id . "' AND `c`.`status` = '1'");

		return $query->row;
	}

	/**
	 * @param string $iso_code_2
	 *
	 * @return array
	 */
	public function getCountryByIsoCode2(string $iso_code_2): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "country` WHERE `iso_code_2` = '" . $this->db->escape($iso_code_2) . "' AND `status` = '1'";

		$country_data = $this->cache->get('country.'. md5($sql));

		if (!$country_data) {
			$query = $this->db->query($sql);

			$country_data = $query->rows;

			$this->cache->set('country.'. md5($sql), $country_data);
		}

		return $country_data;
	}

	/**
	 * @param string $iso_code_3
	 *
	 * @return array
	 */
	public function getCountryByIsoCode3(string $iso_code_3): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "country` WHERE `iso_code_3` = '" . $this->db->escape($iso_code_3) . "' AND `status` = '1'";

		$country_data = $this->cache->get('country.'. md5($sql));

		if (!$country_data) {
			$query = $this->db->query($sql);

			$country_data = $query->rows;

			$this->cache->set('country.'. md5($sql), $country_data);
		}

		return $country_data;
	}

	/**
	 * @return array
	 */
	public function getCountries(): array {
		$sql = "SELECT *, `c`.`name` FROM `" . DB_PREFIX . "country` `c` LEFT JOIN `" . DB_PREFIX . "address_format` `af` ON (`c`.`address_format_id` = `af`.`address_format_id`) WHERE `c`.`status` = '1' ORDER BY `c`.`name` ASC";

		$country_data = $this->cache->get('country.'. md5($sql));

		if (!$country_data) {
			$query = $this->db->query($sql);

			$country_data = $query->rows;

			$this->cache->set('country.'. md5($sql), $country_data);
		}

		return $country_data;
	}
}
