<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 18-12-2018
 * Time: 21:56
 */

class Accounts_m extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function _callback_fees_type($value, $row) {
        $row = $this->db->query("SELECT fees_type.name FROM acc_master LEFT JOIN fees_type ON acc_master.Fees_Type = fees_type.ft_id WHERE `ACC_MASTER_CODE`=$row->ACC_MASTER_CODE")->row();
        return $row->name;
    }
    
    

    



} // /.Accounts_m model