<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 14-01-2019
 * Time: 23:07
 */

class Reports extends My_Controller {

    private $user_type = null;
//    private $page_title = WEBSITE_NAME;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }


    public function details_report() {
        if($this->check_permission(array(1,5),null) == true) {
            $data = array();

            $data['book_master'] = $this->db->get_where('book_master')->result_array();
            $data['class_sec_hdr'] = $this->db->get_where('class_sec_hdr')->result_array();
            $data['student_details'] = $this->db->get_where('student_details')->result_array();
            $data['tab_title'] = 'Reports';
            $data['section_heading'] = 'Reports <small>(Print)</small>';
            $data['menu_name'] = 'Reports';

            $this->load->view('Reports_v', $data);
        }
    }


    public function generate_details_print_format() {
        if($this->check_permission(array(1,5),null) == true) {
            $this->load->model('Reports_m');
            $data = $this->Reports_m->generate_details_print_format();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_std_reg_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_std_reg_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function std_consc_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->std_consc_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_std_consc_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_std_consc_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function all_tran_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->all_tran_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_all_tran_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_all_tran_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function all_fees_type1_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->all_fees_type1_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_all_fees_type1_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_all_fees_type1_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function all_fees_type2_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->all_fees_type2_report();
            $this->load->view($data['page'], $data['data']);
        }
    }
    
    public function student_strength() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->student_strength();
            $this->load->view($data['page'], $data['data']);
        }
    }


    public function print_student_strength_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_student_strength_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    

    public function print_all_fees_type2_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_all_fees_type2_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function std_fees_ledger_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->std_fees_ledger_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_std_fees_ledger_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_std_fees_ledger_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function single_month_dues_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->single_month_dues_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_single_month_dues_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_single_month_dues_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function all_dues_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->all_dues_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_all_dues_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_all_dues_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function payment_type_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->payment_type_report();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_payment_type_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_payment_type_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function library_register() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->library_register();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_library_register() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_library_register();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function books_register() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->books_register();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_books_register() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_books_register();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function class_routine() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->class_routine();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_class_routine() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_class_routine();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function teacher_routine() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->teacher_routine();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_teacher_routine() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_teacher_routine();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function print_master_routine() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_master_routine();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function student_list() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->student_list();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function print_student_list_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_student_list_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    
    public function print_student_category_list_report() {
        if($this->user_type != 1 && $this->user_type != 2) { //if not logged-in
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
        } else { //if admin already logged-in
            $this->load->model('Reports_m');
            $data = $this->Reports_m->print_student_category_list_report();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    

    

}