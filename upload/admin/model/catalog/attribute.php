<?php
namespace Ventocart\Admin\Model\Catalog;

class Attribute extends \Ventocart\System\Engine\Model {
    public function addAttribute(array $data): int {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "attribute` SET  `sort_order` = '" . (int)$data['sort_order'] . "'");

        $attribute_id = $this->db->getLastId();

        foreach ($data['attribute_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "attribute_description` SET `attribute_id` = '" . (int)$attribute_id . "', `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($value['name']) . "'");
        }

        return $attribute_id;
    }

    public function editAttribute(int $attribute_id, array $data): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "attribute` SET `sort_order` = '" . (int)$data['sort_order'] . "' WHERE `attribute_id` = '" . (int)$attribute_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "attribute_description` WHERE `attribute_id` = '" . (int)$attribute_id . "'");

        foreach ($data['attribute_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "attribute_description` SET `attribute_id` = '" . (int)$attribute_id . "', `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($value['name']) . "'");
        }
    }

    public function deleteAttribute(int $attribute_id): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "attribute` WHERE `attribute_id` = '" . (int)$attribute_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "attribute_description` WHERE `attribute_id` = '" . (int)$attribute_id . "'");
    }

    public function getAttribute(int $attribute_id): array {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "attribute` a LEFT JOIN `" . DB_PREFIX . "attribute_description` ad ON (a.`attribute_id` = ad.`attribute_id`) WHERE a.`attribute_id` = '" . (int)$attribute_id . "' AND ad.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");
        return $query->row;
    }
    
    public function getAttributesChilds($language_id, $text, $attribute_id, $attribute_type): array {
        $sql = "SELECT * FROM `" . DB_PREFIX . "product_attribute` 
                WHERE `attribute_id` = '" . (int)$attribute_id . "' AND `language_id` = '" . (int)$language_id . "'";
    
        if ($attribute_type == 'value_text') {
            $sql .= " AND `value_text` LIKE '%" . $this->db->escape($text) . "%' AND `value_text`  <> ''";
            $sql .= " GROUP BY   `value_text` ";  
        } elseif ($attribute_type == 'text') {
            
            $sql .= " AND `text` LIKE '%" . $this->db->escape($text) . "%'  AND `text` <> ''";
            $sql .= " GROUP BY   `text` ";  
        }
    
   
        $sql .= " LIMIT 10 ";
    
        $query = $this->db->query($sql);
        return $query->rows;
    }
    
    public function getAttributes(array $data = []): array {
        if (!isset($data['exclude'])) {
            $data['exclude'] = 0;
        }
        $sql = "
        SELECT *
        FROM `" . DB_PREFIX . "attribute` a
        LEFT JOIN `" . DB_PREFIX . "attribute_description` ad ON (a.`attribute_id` = ad.`attribute_id`)
        WHERE ad.`language_id` = '" . (int)$this->config->get('config_language_id') . "'
            AND NOT EXISTS (
                SELECT 1
                FROM `" . DB_PREFIX . "product_attribute` pa
                WHERE pa.`attribute_id` = a.`attribute_id`
                    AND pa.`product_id` = '" . (int)$data['exclude'] . "'
            )
    ";
        if (!empty($data['filter_name'])) {
            $sql .= " AND ad.`name` LIKE '" . $this->db->escape((string)$data['filter_name'] . '%') . "'";
        }
        // Add sorting conditions
        $sort_data = [
            'ad.name',
            'a.sort_order'
        ];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY ad.name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        // Add limit conditions
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

    public function getDescriptions(int $attribute_id): array {
        $attribute_data = [];

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "attribute_description` WHERE `attribute_id` = '" . (int)$attribute_id . "'");

        foreach ($query->rows as $result) {
            $attribute_data[$result['language_id']] = ['name' => $result['name']];
        }

        return $attribute_data;
    }

    public function getTotalAttributes(): int {
        $query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "attribute`");

        return (int)$query->row['total'];
    }

    public function getTotalAttributesByAttributeGroupId(int $attribute_group_id): int {
        $query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "attribute`");

        return (int)$query->row['total'];
    }
}
