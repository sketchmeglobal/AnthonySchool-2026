<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 09-07-17
 * Time: xx:xx
 */

class Dashboard extends My_Controller {

    private $user_type = null;
    private $page_title = WEBSITE_NAME;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function index() {
        redirect(base_url('admin/dashboard'));
    }

    public function dashboard() {
        if($this->user_type == null) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Dashboard_m');
            $data = $this->Dashboard_m->dashboard();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function error_404() {
        $this->load->library('user_agent');
        $data['referrer_url'] = $this->agent->referrer();
        $this->load->view('error_404_v', $data);
    }

    public function js_disabled() {
        $this->load->library('user_agent');
        $data['referrer_url'] = $this->agent->referrer();
        $this->load->view('js_disabled_v', $data);
    }

   /* public function data_update()
    { //die();
        $this->db->select('CS_SEQ, Class_Name, Sec_Name');

        $st = $this->db->get('class_sec_hdr')->result();

        foreach ($st as $key => $value) {
            $st_arr = array();

            if (empty($value->STD_MNAME)) {
                $st_full_name = $value->STD_FNAME.' '.$value->STD_LNAME;
            }else{
                $st_full_name = $value->STD_FNAME.' '.$value->STD_MNAME.' '.$value->STD_LNAME;
            }

            $st_full_name = $value->Class_Name.'-'.$value->Sec_Name;

            

            $st_arr['class_sec'] = $st_full_name;

            $this->db->where('CS_SEQ', $value->CS_SEQ);
            $this->db->update('class_sec_hdr', $st_arr);
        }

        //student_update
    }*/

}
