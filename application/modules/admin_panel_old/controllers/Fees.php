<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 11-01-2019
 * Time: 02:08
 */

class Fees extends My_Controller {

    private $user_type = null;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function monthly_fees($hdr_id=null) {
        if($this->check_permission(array(1,2,4,6),61) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->monthly_fees($hdr_id);
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function add_monthly_fees($std_id='') {
        if($this->check_permission(array(1,2,4,6),61) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->add_monthly_fees($std_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_add_monthly_fees() {
        if($this->check_permission(array(1,2,4,6),61) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->form_add_monthly_fees();
            $this->load->view($data['page'],$data['data']);
        }
    }
    
    
    public function form_add_consc_fees() {
        if($this->check_permission(array(1,2,4,6),29) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->form_add_consc_fees();
            redirect(base_url($data['page']));
        }
    }

     public function monthly_fees_payment_insert() {
        if($this->check_permission(array(1,2,4,6),58) == true) {
            $this->load->model('Fees_m');
            $data = array();
            $data['encdata'] =  $_POST['encdata'];
            $this->load->view('payment_insert',$data);
        }
    }

    public function monthly_fees_payment_complete() {
        if($this->check_permission(array(1,2,4,6),58) == true) {
            $this->load->model('Fees_m');
            $data = array();
            $data = $this->Fees_m->monthly_fees_payment_complete();
            $data['sbi_ref_no'] = $this->input->post('sbi_ref_no');
            if($data['status'] == "success"){
                if($this->input->post('status') == "Success"){
                    $this->load->view('payment_complete',$data);
                }else{
                    $this->load->view('payment_incomplete',$data);
                }
            }else{
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('msg', 'No permission.');
                redirect(base_url('admin/dashboard'));
            }
        }
    }

    public function ajax_net_fee() {
        if($this->check_permission(array(1,2,4,6),null) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->ajax_net_fee();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function yearly_fees($hdr_id=null) {
        if($this->check_permission(array(1,2,6),59) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->yearly_fees($hdr_id);
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function add_yearly_fees($std_id='') {
        if($this->check_permission(array(1,2,6,4),62) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->add_yearly_fees($std_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_add_yearly_fees() {
        if($this->check_permission(array(1,2,6),62) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->form_add_yearly_fees();
            redirect(base_url($data['page']));
        }
    }

    public function form_add_yearly_fees_due() {
        if($this->check_permission(array(1,2,6),62) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->form_add_yearly_fees_due();
            redirect(base_url($data['page']));
        }
    }

    public function ajax_net_fee_yearly() {
        //if($this->check_permission(array(1,2,6),null) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->ajax_net_fee_yearly();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        //}
    }

    public function ajax_net_fee_adm() {
        if($this->check_permission(array(1,2,6),null) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->ajax_net_fee_adm();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function ajax_net_fee_monthly() {
        if($this->check_permission(array(1,2,6),null) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->ajax_net_fee_monthly();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function new_admission_fees($hdr_id=null) {
        if($this->check_permission(array(1,2,6),63) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->new_admission_fees($hdr_id);
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function add_new_admission_fees($std_id='') {
        if($this->check_permission(array(1,2,6),63) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->add_new_admission_fees($std_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_add_new_admission_fees() {
        if($this->check_permission(array(1,2,6),63) == true) {
            $this->load->model('Fees_m');
            $data = $this->Fees_m->form_add_new_admission_fees();
            redirect(base_url($data['page']));
        }
    }

}