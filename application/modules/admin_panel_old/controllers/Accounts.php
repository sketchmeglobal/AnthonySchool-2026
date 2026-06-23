<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 18-12-2018
 * Time: 21:55
 */

class Accounts extends My_Controller {

    private $user_type = null;
//    private $page_title = WEBSITE_NAME;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');
        $this->load->library('tcpdf/Pdf');
        
        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function index() {
        redirect(base_url('admin/account_master'));
    }
    
    
    
    

    



}