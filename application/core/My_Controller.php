<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * Coded for: www.nubedigitals.com
 * CI: 3.0.6
 * Purpose:
 * Date: 30-09-2016
 * Time: 12:18
 */

Class My_Controller extends MX_Controller {

    private $user_type = null;

    function __construct() {
        parent::__construct();

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function check_permission($auth_usertype = array(), $menu_id = null) {
        # auth_usertype = 1:admin, 2:accountant, 3:teacher, 4:student, 5:librarian, 6:operator

        // echo '<pre>',print_r($auth_usertype), '</pre>';
        // echo $menu_id;

        //if not logged-in
        if($this->user_type == null) {
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        }

        //if no special permission required (should be logged-in only)
        if(count($auth_usertype) == 0) {
            return true;
        }

        //fetch menu permission
        $menu_permission = 1;
        if($menu_id != null) {
            $this->db->where('user_id', $this->session->user_id);
            $this->db->where('menu_id', $menu_id);
            $prm_row = $this->db->get('user_permissions')->row();

            if(!empty($prm_row)) {
                $menu_permission = $prm_row->permission;
            }
        }

        //check authorised usertype & menu permission
        if($this->user_type == 1 && in_array(1, $auth_usertype)) { //admin don't need menu permission
            return true;
        }
        elseif(in_array($this->user_type, $auth_usertype) && $menu_permission == 1) {
            return true;
        }
        else {
            $this->session->set_flashdata('title', 'Prohibited!');
            $this->session->set_flashdata('msg', 'You do not have permission to access that page, kindly contact Administrator.');
            redirect(base_url('admin/dashboard'));
        }
    }

}