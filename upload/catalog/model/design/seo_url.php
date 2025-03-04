<?php
namespace Ventocart\Catalog\Model\Design;
/**
 * Class Seo Url
 *
 * @package Ventocart\Catalog\Model\Design
 */
class SeoUrl extends \Ventocart\System\Engine\Model
{
	/**
	 * @param string $keyword
	 *
	 * @return array
	 */
	public function getSeoUrlByKeyword(string $keyword): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE (`keyword` = '" . $this->db->escape($keyword) . "' OR `keyword` LIKE '" . $this->db->escape('%/' . $keyword) . "')  LIMIT 1");

		return $query->row;
	}

	/**
	 * @param string $key
	 * @param string $value
	 *
	 * @return array
	 */
	public function getSeoUrlByKeyValue(string $key, string $value): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `key` = '" . $this->db->escape($key) . "' AND `value` = '" . $this->db->escape($value) . "'   AND `language_id` = '" . (int) $this->config->get('config_language_id') . "'");

		return $query->row;
	}
}
