<?php
namespace Ventocart\Admin\Model\Design;
/**
 * Class Seo Url
 *
 * @package Ventocart\Admin\Model\Design
 */
class SeoUrl extends \Ventocart\System\Engine\Model
{
	/**
	 * @param array $data
	 *
	 * @return int
	 */
	public function addSeoUrl(array $data): int
	{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` SET `language_id` = '" . (int) $data['language_id'] . "', `key` = '" . $this->db->escape((string) $data['key']) . "', `value` = '" . $this->db->escape((string) $data['value']) . "', `keyword` = '" . $this->db->escape((string) $this->convertToSeoFriendly($data['keyword'])) . "', `sort_order` = '" . (int) $data['sort_order'] . "'");

		return $this->db->getLastId();
	}

	/**
	 * @param int   $seo_url_id
	 * @param array $data
	 *
	 * @return void
	 */
	public function editSeoUrl(int $seo_url_id, array $data): void
	{
		$this->db->query("UPDATE `" . DB_PREFIX . "seo_url` SET `language_id` = '" . (int) $data['language_id'] . "', `key` = '" . $this->db->escape((string) $data['key']) . "', `value` = '" . $this->db->escape((string) $data['value']) . "', `keyword` = '" . $this->db->escape((string) $this->convertToSeoFriendly($data['keyword'])) . "', `sort_order` = '" . (int) $data['sort_order'] . "' WHERE `seo_url_id` = '" . (int) $seo_url_id . "'");
	}

	/**
	 * @param int $seo_url_id
	 *
	 * @return void
	 */
	public function deleteSeoUrl(int $seo_url_id): void
	{
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE `seo_url_id` = '" . (int) $seo_url_id . "'");
	}

	/**
	 * @param int $seo_url_id
	 *
	 * @return array
	 */
	public function getSeoUrl(int $seo_url_id): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `seo_url_id` = '" . (int) $seo_url_id . "'");

		return $query->row;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function getSeoUrls(array $data = []): array
	{
		$sql = "SELECT *, (SELECT `name` FROM `" . DB_PREFIX . "language` `l` WHERE `l`.`language_id` = `su`.`language_id`) AS `language` FROM `" . DB_PREFIX . "seo_url` `su`";

		$implode = [];

		if (!empty($data['filter_keyword'])) {
			$implode[] = "LCASE(`keyword`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_keyword'])) . "'";
		}

		if (!empty($data['filter_key'])) {
			$implode[] = "LCASE(`key`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_key'])) . "'";
		}

		if (!empty($data['filter_value'])) {
			$implode[] = "LCASE(`value`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_value'])) . "'";
		}

		if (!empty($data['filter_language_id']) && $data['filter_language_id'] !== '') {
			$implode[] = "`language_id` = '" . (int) $data['filter_language_id'] . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = [
			'keyword',
			'key',
			'value',
			'sort_order',
			'language_id'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY `" . $data['sort'] . "`";
		} else {
			$sql .= " ORDER BY `key`";
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
	 * @param array $data
	 *
	 * @return int
	 */
	public function getTotalSeoUrls(array $data = []): int
	{
		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "seo_url`";

		$implode = [];

		if (!empty($data['filter_keyword'])) {
			$implode[] = "LCASE(`keyword`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_keyword'])) . "'";
		}

		if (!empty($data['filter_key'])) {
			$implode[] = "LCASE(`key`) = '" . $this->db->escape(oc_strtolower($data['filter_key'])) . "'";
		}

		if (!empty($data['filter_value'])) {
			$implode[] = "LCASE(`value`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_value'])) . "'";
		}

		if (!empty($data['filter_language_id']) && $data['filter_language_id'] !== '') {
			$implode[] = "`language_id` = '" . (int) $data['filter_language_id'] . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return (int) $query->row['total'];
	}

	/**
	 * @param string $key
	 * @param string $value
	 * @param int    $language_id
	 *
	 * @return array
	 */
	public function getSeoUrlByKeyValue(string $key, string $value, int $language_id): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `key` = '" . $this->db->escape($key) . "' AND `value` = '" . $this->db->escape($value) . "'   AND `language_id` = '" . (int) $language_id . "'");

		return $query->row;
	}

	/**
	 * @param string $keyword
	 * @param int    $language_id
	 *
	 * @return array
	 */
	public function getSeoUrlByKeyword(string $keyword, int $language_id = 0): array
	{
		$sql = "SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE (`keyword` = '" . $this->db->escape($keyword) . "' OR LCASE(`keyword`) LIKE '" . $this->db->escape('%/' . oc_strtolower($keyword)) . "')";

		if ($language_id) {
			$sql .= " AND `language_id` = '" . (int) $language_id . "'";
		}

		$query = $this->db->query($sql);

		return $query->row;
	}

	private function convertToSeoFriendly($text)
	{
		// Transliterate non-Latin characters
		$text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text);

		// Convert to lowercase
		$text = strtolower($text);

		// Replace spaces with dashes, but preserve existing slashes
		$text = preg_replace('/[^a-z0-9\/]+/', '-', $text);

		// Remove leading and trailing dashes or slashes
		$text = trim($text, '-/');

		return $text;
	}
}
