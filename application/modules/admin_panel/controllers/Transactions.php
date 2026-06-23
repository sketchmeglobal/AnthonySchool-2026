<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 16-01-2019
 * Time: 21:59
 */

class Transactions extends My_Controller {

    private $user_type = null;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');
        $this->load->library('tcpdf/Pdf');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function monthly_fees_report() {
        if($this->check_permission(array(1,2,6),61) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->monthly_fees_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function yearly_fees_report() {
        if($this->check_permission(array(1,2,6),62) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->yearly_fees_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function new_admission_fees_report() {
        if($this->check_permission(array(1,2,6),63) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->new_admission_fees_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_monthly_fess($hdr_id) {
        if($this->check_permission(array(1,2,4,6),61) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->print_monthly_fess($hdr_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            }
            elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function print_yearly_fess($hdr_id) {
        if($this->check_permission(array(1,2,6),62) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->print_yearly_fess($hdr_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function print_new_admission_fess($hdr_id) {
        if($this->check_permission(array(1,2,6),63) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->print_new_admission_fess($hdr_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function fees_collection() {
       if($this->check_permission(array(1,2,6),null) == true) {
            $data = array();

            $data['tab_title'] = 'Transactions';
            $data['section_heading'] = 'Transactions <small>(Click Enter Your Transaction)</small>';
            $data['menu_name'] = 'Transactions';
            $this->load->view('common_transaction_component_v', $data);
        }
    }

    public function voucher_entry() {
        if($this->check_permission(array(1,2,6),52) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->voucher_entry();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function add_voucher_entry() {
        if($this->check_permission(array(1,2,6),52) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->add_voucher_entry();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function form_add_voucher_entry() {
        if($this->check_permission(array(1,2,6),52) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->form_add_voucher_entry();
            redirect(base_url($data['page']));
        }
    }

    public function edit_voucher_entry($vchr_hdr_id) {
        if($this->check_permission(array(1,2,6),52) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->edit_voucher_entry($vchr_hdr_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_edit_voucher_entry() {
        if($this->check_permission(array(1,2,6),52) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->form_edit_voucher_entry();
            redirect(base_url($data['page']));
        }
    }


    public function print_fees($student_id) {
        if($this->check_permission(array(1,2,6),null) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->print_fees($student_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function print_all_students_fee($fee_category) {
        if($this->check_permission(array(1,2,6),null) == true) {
            $this->load->model('Transactions_m');
            $data = $this->Transactions_m->print_all_students_fee($fee_category);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    

}