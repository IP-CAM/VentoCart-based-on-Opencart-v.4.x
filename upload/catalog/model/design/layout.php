<?php
namespace Ventocart\Catalog\Model\Design;
/**
 * Class Layout
 *
 * @package Ventocart\Catalog\Model\Design
 */
class Layout extends \Ventocart\System\Engine\Model
{
	/**
	 * @param string $route
	 *
	 * @return int
	 */
	public function getLayout(string $route): int
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "layout_route` WHERE '" . $this->db->escape($route) . "' LIKE `route`   ORDER BY `route` DESC LIMIT 1");

		if ($query->num_rows) {
			return (int) $query->row['layout_id'];
		} else {
			return 0;
		}
	}

	/**
	 * @param int    $layout_id
	 * @param string $position
	 *
	 * @return array
	 */
	public function getModules(int $layout_id, string $position): array
	{
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "layout_module` WHERE `layout_id` = '" . (int) $layout_id . "' AND `position` = '" . $this->db->escape($position) . "' ORDER BY `sort_order`");

		return $query->rows;
	}
}
