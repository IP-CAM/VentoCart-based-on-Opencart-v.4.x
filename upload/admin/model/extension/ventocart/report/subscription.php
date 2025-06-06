<?php
namespace Ventocart\Admin\Model\Extension\Ventocart\Report;
/**
 * Class Subscription
 *
 * @package Ventocart\Admin\Model\Extension\Ventocart\Report
 */
class Subscription extends \Ventocart\System\Engine\Model {
    /**
     * @param array $data
     *
     * @return array
     */
    public function getSubscriptions(array $data = []): array {
        $sql = "SELECT MIN(`s`.`date_added`) AS date_start, MAX(`s`.`date_added`) AS date_end, COUNT(*) AS `subscriptions`, SUM((SELECT SUM(`ot`.`value`) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.`order_id` = `s`.`order_id` AND ot.`code` = 'tax' GROUP BY ot.`order_id`)) AS tax, SUM(`s`.`quantity`) AS `products`, SUM(`s`.`price`) AS `total` FROM `" . DB_PREFIX . "subscription` `s`";
        
        if (!empty($data['filter_subscription_status_id'])) {
            $sql .= " WHERE `s`.`subscription_status_id` = '" . (int)$data['filter_subscription_status_id'] . "'";
        } else {
            $sql .= " WHERE `s`.`subscription_status_id` > '0'";
        }
        
        if (!empty($data['filter_date_start'])) {
            $sql .= " AND DATE(`s`.`date_added`) >= DATE('" . $this->db->escape((string)$data['filter_date_start']) . "')";
        }
        
        if (!empty($data['filter_date_end'])) {
            $sql .= " AND DATE(`s`.`date_added`) <= DATE('" . $this->db->escape((string)$data['filter_date_end']) . "')";
        }
        
        if (!empty($data['filter_group'])) {
            $group = $data['filter_group'];
        } else {
            $group = 'week';
        }
        
        switch ($group) {
            case 'day';
                $sql .= " GROUP BY YEAR(`s`.`date_added`), MONTH(`s`.`date_added`), DAY(`s`.`date_added`)";
                break;
            default:
            case 'week':
                $sql .= " GROUP BY YEAR(`s`.`date_added`), WEEK(`s`.`date_added`)";
                break;
            case 'month':
                $sql .= " GROUP BY YEAR(`s`.`date_added`), MONTH(`s`.`date_added`)";
                break;
            case 'year':
                $sql .= " GROUP BY YEAR(`s`.`date_added`)";
                break;
        }
        
        $sql .= " ORDER BY `s`.`date_added` DESC";
        
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
    public function getTotalSubscriptions(array $data = []): int {
        if (!empty($data['filter_group'])) {
            $group = $data['filter_group'];
        } else {
            $group = 'week';
        }
        
        switch ($group) {
            case 'day';
                $sql = "SELECT COUNT(DISTINCT YEAR(`date_added`), MONTH(`date_added`), DAY(`date_added`)) AS `total` FROM `" . DB_PREFIX . "subscription`";
                break;
            default:
            case 'week':
                $sql = "SELECT COUNT(DISTINCT YEAR(`date_added`), WEEK(`date_added`)) AS `total` FROM `" . DB_PREFIX . "subscription`";
                break;
            case 'month':
                $sql = "SELECT COUNT(DISTINCT YEAR(`date_added`), MONTH(`date_added`)) AS `total` FROM `" . DB_PREFIX . "subscription`";
                break;
            case 'year':
                $sql = "SELECT COUNT(DISTINCT YEAR(`date_added`)) AS `total` FROM `" . DB_PREFIX . "subscription`";
                break;
        }
        
        if (!empty($data['filter_subscription_status_id'])) {
            $sql .= " WHERE `subscription_status_id` = '" . (int)$data['filter_subscription_status_id'] . "'";
        } else {
            $sql .= " WHERE `subscription_status_id` > '0'";
        }
        
        if (!empty($data['filter_date_start'])) {
            $sql .= " AND DATE(`date_added`) >= DATE('" . $this->db->escape((string)$data['filter_date_start']) . "')";
        }
        
        if (!empty($data['filter_date_end'])) {
            $sql .= " AND DATE(`date_added`) <= DATE('" . $this->db->escape((string)$data['filter_date_end']) . "')";
        }
        
        $query = $this->db->query($sql);
        
        return (int)$query->row['total'];
    }
}
