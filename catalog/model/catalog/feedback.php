<?php
Class ModelCatalogFeedback extends Model {
    public function getFeedbacks() {
                $query = $this->db->query("SELECT DISTINCT * FROM " .
            DB_PREFIX. "feedback f 
            LEFT JOIN " . DB_PREFIX."feedback_description fd 
            ON (f.feedback_id = fd.feedback_id) 
            LEFT JOIN " . DB_PREFIX. "feedback_to_store f2s 
            ON (f.feedback_id = f2s.feedback_id)
            WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            AND f2s.store_id = '" . (int)$this->config->get('config_store_id') . "' 
            AND f.status = '1'");
        return $query->rows;
    }
    public function getTotalFeedbacks() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " .
            DB_PREFIX . "feedback f 
            LEFT JOIN " . DB_PREFIX . "feedback_to_store f2s ON (f.feedback_id = f2s.feedback_id)
                WHERE f2s.store_id = '" . (int)$this->config->get('config_store_id') . "' 
                AND f.status = '1'");
        return $query->row['total'];
    }
}