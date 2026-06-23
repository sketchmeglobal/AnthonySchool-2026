<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 22-12-2018
 * Time: 18:52
 */

class Students extends My_Controller {

    private $user_type = null;

    public function __construct() {
        parent::__construct();

        $this->load->library('grocery_CRUD');
        $this->load->library('tcpdf/Pdf');

        if($this->session->has_userdata('user_id')) { //if logged-in
            $this->user_type = $this->session->usertype;
        }
    }

    public function print_certificate($STD_SEQ) {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->print_certificate($STD_SEQ);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    
    public function leaving_certificate() {
        if($this->check_permission(array(1,2,6),42) == true) {
            // $this->load->library('qr-code-master/Ciqrcode');
            $this->load->model('Students_m');
            $data = $this->Students_m->leaving_certificate();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function add_leaving_certificate() {
        if($this->check_permission(array(1,2,6),42) == true) {
            // $this->load->library('qr-code-master/Ciqrcode');
            $this->load->model('Students_m');
            $data = $this->Students_m->add_leaving_certificate();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    
    
    public function print_leaving_certificate($certificate_id = null) {
        if($this->check_permission(array(1,2,6),42) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->print_leaving_certificate($certificate_id);
            if($data !== null) {
                if( $data['type'] == 'load_view') {
                    $this->load->view($data['page'], $data['data']);
                } elseif( $data['type'] == 'redirect') {
                    redirect(base_url($data['page']));
                }
            }
        }
    }

    public function character_certificate() {
        if($this->check_permission(array(1,2,6),43) == true) {
            // $this->load->library('qr-code-master/Ciqrcode');
            $this->load->model('Students_m');
            $data = $this->Students_m->character_certificate();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function add_character_certificate() {
        if($this->check_permission(array(1,2,6),43) == true) {
            // $this->load->library('qr-code-master/Ciqrcode');
            $this->load->model('Students_m');
            $data = $this->Students_m->add_character_certificate();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }
    
    public function print_character_certificate($certificate_id = null) {
        if($this->check_permission(array(1,2,6),43) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->print_character_certificate($certificate_id);
            if($data !== null) {
                if( $data['type'] == 'load_view') {
                    $this->load->view($data['page'], $data['data']);
                } elseif( $data['type'] == 'redirect') {
                    redirect(base_url($data['page']));
                }
            }
        }
    }
    
    public function general_letter() {
        if($this->check_permission(array(1,2,6),44) == true) {
            // $this->load->library('qr-code-master/Ciqrcode');
            $this->load->model('Students_m');
            $data = $this->Students_m->general_letter();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function add_general_letter() {
        if($this->check_permission(array(1,2,6),44) == true) {
            // $this->load->library('qr-code-master/Ciqrcode');
            $this->load->model('Students_m');
            $data = $this->Students_m->add_general_letter();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function print_general_letter($certificate_id = null) {
        if($this->check_permission(array(1,2,6),44) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->print_general_letter($certificate_id);
            if($data !== null) {
                if( $data['type'] == 'load_view') {
                    $this->load->view($data['page'], $data['data']);
                } elseif( $data['type'] == 'redirect') {
                    redirect(base_url($data['page']));
                }
            }
        }
    }

    public function student_parent_details_datatables() {
        if($this->check_permission(array(1,2,6),36) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->student_parent_details_datatables();
            $this->load->view('common_v1', $data);
        }
    }
    
    public function student_parent_details_edit() {
        if($this->check_permission(array(1,2,6),36) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->student_parent_details_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function student_auto_roll() {
        if($this->check_permission(array(1,2,6),37) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->student_auto_roll();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function form_student_auto_roll() {
        if($this->check_permission(array(1,2,6),37) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->form_student_auto_roll();
            redirect(base_url($data['page']));
        }
    }

    public function admit_card() {
        if($this->check_permission(array(1,2,6),38) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->admit_card();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function ajax_update_std_admit_card() {
        // if($this->check_permission(array(1,2,3,6),38) == true) {
        //     $this->session->set_flashdata('title', 'Log-in!');
        //     $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
        //     redirect(base_url('admin'));
        // } else { //if admin already logged-in
            
        // }
        $this->load->model('Students_m');
        $data = $this->Students_m->ajax_update_std_admit_card();
        echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
        exit();
    }
    
    public function ajax_update_std_admit_card1() {
        if($this->check_permission(array(1,2,3,6),null) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->ajax_update_std_admit_card1();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }
    
    public function ajax_std_reg_no_on_admit_card() {
        if($this->check_permission(array(1,2,3,6),null) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->ajax_std_reg_no_on_admit_card();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function print_admit_card() {
        if($this->check_permission(array(1,2,6),38) == true) {
            $this->load->library('qr-code-master/Ciqrcode');
            $this->load->model('Students_m');
            $data = $this->Students_m->print_admit_card();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function identity_card() {
        if($this->check_permission(array(1,2,6),39) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->identity_card();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function print_identity_card() {
        if($this->check_permission(array(1,2,6),39) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->print_identity_card();
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function routine() {
        if($this->check_permission(array(1,2,6),40) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->routine();
            $this->load->view($data['page'], $data['data']); 
        }
    }
    
    public function routine_edit() {
        if($this->check_permission(array(1,2,6),40) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->routine_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function generate_routine() {
        if($this->check_permission(array(1,2,6),40) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->generate_routine();
            redirect(base_url($data['page']));
        }
    }

    public function add_routine($cls_id) {
        if($this->check_permission(array(1,2,6),40) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->add_routine($cls_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_add_routine() {
        if($this->check_permission(array(1,2,6),40) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->form_add_routine();
            redirect(base_url($data['page']));
        }
    }

    public function edit_routine($cls_id) {
        if($this->check_permission(array(1,2,6),40) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->edit_routine($cls_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_edit_routine() {
        if($this->check_permission(array(1,2,6),40) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->form_edit_routine();
            redirect(base_url($data['page']));
        }
    }

    public function ajax_teacher_availability() {
        if($this->check_permission(array(1,2,6),null) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->ajax_teacher_availability();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function library() {
        if($this->check_permission(array(1,2,6),41) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->library();
            $this->load->view($data['page'], $data['data']);
        }
    }
    
    public function library_edit() {
        if($this->check_permission(array(1,2,6),41) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->library_edit();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function add_library_tran() {
        if($this->check_permission(array(1,2,6),41) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->add_library_tran();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function ajax_update_std_table_data() {
        if($this->check_permission(array(1,2,6),null) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->ajax_update_std_table_data();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function form_add_library_tran() {
        if($this->check_permission(array(1,2,6),41) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->form_add_library_tran();
            redirect(base_url($data['page']));
        }
    }

    public function edit_library_tran($hdr_id) {
        if($this->check_permission(array(1,2,6),41) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->edit_library_tran($hdr_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_edit_library_tran() {
        if($this->check_permission(array(1,2,6),41) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->form_edit_library_tran();
            redirect(base_url($data['page']));
        }
    }


    /*Customm Student Form*/

    public function add_student() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->add_student();
            $this->load->view($data['page'], $data['data']);
        }
    }

    public function check_reg_no(){
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->check_reg_no();
            echo json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG);
            exit();
        }
    }

    public function delete_student($st_id) {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->delete_student($st_id);
            if( $data['type'] == 'load_view') {
                $this->load->view($data['page'], $data['data']);
            } elseif( $data['type'] == 'redirect') {
                redirect(base_url($data['page']));
            }
        }
    }

    public function form_add_student() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->form_add_student();
            redirect(base_url($data['page']));
        }
    }
    
    public function insert_data_for_new_users()
    {
        
        //  $this->load->database();

        // $usernames = $this->db->select('username')->get('users')->result_array();
        // $existingUsernames = array_map(function($u) {
        //     return strtolower(trim($u['username']));
        // }, $usernames);
    
        // $students = $this->db->select('STD_SEQ, STD_REGNO, STD_DOB')
        //     ->from('student_details')
        //     ->where('STD_LAST_SESSION', 2025)
        //     ->where('STD_CURRENT_SESSION', 2025)
        //     ->get()
        //     ->result();
    
        // $inserted = 0;
        // foreach ($students as $row) {
        //     $std_regno = strtolower(trim($row->STD_REGNO));
    
        //     if (!in_array($std_regno, $existingUsernames)) {
        //         $formattedDob = date('dmY', strtotime($row->STD_DOB));
        //         $hashedPassword = hash('sha256', $formattedDob);
    
        //         $data = [
        //             'usertype' => 4,
        //             'tbl_id' => $row->STD_SEQ,
        //             'username' => $row->STD_REGNO,
        //             'pass' => $hashedPassword,
        //             'verified' => 1,
        //             'registration_date' => date('Y-m-d H:i:s')
        //         ];
    
        //         $this->db->insert('users', $data);
        //         $inserted++;
        //     }
        // }
    
        // echo $inserted . " students inserted.";
        
        $this->load->database();

        $usernames = $this->db->select('username')->get('users')->result_array();
        $existingUsernames = array_map(function($u) {
            return strtolower(trim($u['username']));
        }, $usernames);
    
        $students = $this->db->select('STD_SEQ, STD_REGNO, STD_DOB')->get('student_details')->result();
    
        $inserted = 0;
    
        foreach ($students as $row) {
            $std_regno = strtolower(trim($row->STD_REGNO));
    
            if (!in_array($std_regno, $existingUsernames)) {
                $formattedDob = date('dmY', strtotime($row->STD_DOB));
                $hashedPassword = hash('sha256', $formattedDob);
    
                $data = [
                    'usertype' => 4,
                    'tbl_id' => $row->STD_SEQ,
                    'username' => $row->STD_REGNO,
                    'pass' => $hashedPassword,
                    'verified' => 1,
                    'registration_date' => date('Y-m-d H:i:s')
                ];
    
                $this->db->insert('users', $data);
                $inserted++;
            }
        }
    
        echo $inserted . " students inserted.";

   
    }

    public function edit_student($st_id) {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->edit_student($st_id);
            $this->load->view($data['page'], $data['data']);
        }
    }
    
    public function form_edit_student() {
        if($this->check_permission(array(1,2,6),28) == true) {
            $this->load->model('Students_m');
            $data = $this->Students_m->form_edit_student();
            redirect(base_url($data['page']));
        }
    }
}