<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 11-02-2019
 * Time: 19:39
 */

class Administrations_m extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function create_account() {
        $this->db->select('tbl_id');
        $this->db->where('usertype', '2');
        $ids_rs = $this->db->get('users')->result_array();
        $ids = array_column($ids_rs, 'tbl_id');

        $this->db->select('EMP_SEQ,EMP_CODE,EMP_NAME');
        $this->db->where('EMP_DEPT', '2'); //emp type accountant
        if(count($ids) > 0) {
            $this->db->where_not_in('EMP_SEQ', $ids);
        }
        $this->db->order_by('EMP_NAME');
        $employees = $this->db->get('employee')->result_array();

        $data['employees'] = $employees;
        $data['form_type'] = 'create_account';

        $data['tab_title'] = 'Create Account';
        $data['section_heading'] = 'Create Account <small>(Operator / Accountant)</small>';
        $data['menu_name'] = 'Create Account';

        return array('type'=>'load_view', 'page'=>'accounts_v', 'data'=>$data);
    }

    public function add_create_account() {
        if($this->input->post('submit') == 'add_create_account') { //if form submitted
            $emp_id = $this->input->post('emp_id');
            $username = $this->input->post('username');
            $pass = $this->input->post('pass');
            $pass_encrypted = hash('sha256', $pass); //encrypting password with sha256 encoding

            $emp_row = $this->db->where('EMP_SEQ', $emp_id)->get('employee')->row();
            //if employee not exists
            if(count((array)$emp_row) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Employee does not exists.');
                return array('page'=>'admin/create_account');
            }

            $user_row = $this->db->where('username', $username)->get('users')->row();
            //if username already used
            if(count((array)$user_row) > 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Sorry!');
                $this->session->set_flashdata('msg', 'That username is already used.');
                return array('page'=>'admin/create_account');
            }

            $data_insert['usertype'] = '2'; //accountant
            $data_insert['tbl_id'] = $emp_id;
            $data_insert['username'] = $username;
            $data_insert['pass'] = $pass_encrypted;
            $data_insert['verified'] = '1';
            //inserting data
            $this->db->insert('users', $data_insert);

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Account created.');
            return array('page'=>'admin/create_account');
        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/create_account');
        }
    }

    public function add_create_account_operator() {
        if($this->input->post('submit') == 'add_create_account_operator') { //if form submitted
            $firstname = $this->input->post('firstname');
            $lastname = $this->input->post('lastname');
            $username = $this->input->post('username');
            $pass = $this->input->post('pass');
            $pass_encrypted = hash('sha256', $pass); //encrypting password with sha256 encoding

            $user_row = $this->db->where('username', $username)->get('users')->row();
            //if username already used
            if(count((array)$user_row) > 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Sorry!');
                $this->session->set_flashdata('msg', 'That username is already used.');
                return array('page'=>'admin/create_account');
            }

            unset($data_insert);
            $data_insert['usertype'] = '6'; //operator
            $data_insert['username'] = $username;
            $data_insert['pass'] = $pass_encrypted;
            $data_insert['verified'] = '1';
            //inserting data
            $this->db->insert('users', $data_insert);
            $user_id = $this->db->insert_id();

            unset($data_insert);
            $data_insert['user_id'] = $user_id;
            $data_insert['firstname'] = $firstname;
            $data_insert['lastname'] = $lastname;
            //inserting data
            $this->db->insert('user_details', $data_insert);

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Account created.');
            return array('page'=>'admin/create_account');
        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/create_account');
        }
    }

    public function manage_users() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Administrations/manage_users'));
            $crud->set_theme('datatables');
            $crud->set_subject('Manage Users');
            $crud->where('usertype NOT IN (1)'); //exclude admin
            $crud->set_table('users');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();
            $crud->unset_add();
            $crud->unset_delete();
            $crud->unset_clone();

            $crud->columns('usertype','actual_userid','username','email');
            $crud->fields('actual_userid','username','email','pass','verified','blocked');

            $crud->set_relation('actual_userid','teacher','TCH_NAME');

            $crud->display_as('pass', 'New Password');
            $crud->display_as('actual_userid', 'Main User');

            $crud->field_type('usertype', 'dropdown', array('1'=>'Admin', '2'=>'Accountant', '3'=>'Teacher' , '4'=>'Student', '5'=>'Librarian', '6'=>'Operator'));
            $crud->field_type('blocked', 'true_false', array('1' => 'Yes', '0' => 'No'));
            $crud->field_type('verified', 'true_false', array('1' => 'Yes', '0' => 'No'));

            $crud->callback_field('pass',array($this,'set_password_input_to_empty'));
            $crud->callback_before_update(array($this,'_callback_NewPassEncode'));
            $crud->add_action('Permissions', '', '','ui-icon-key',array($this,'manage_users_action'));

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Manage Users';
            $output->section_heading = 'Manage Users <small>(Edit)</small>';
            $output->menu_name = 'Manage Users';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    public function set_password_input_to_empty() {
        return "<input type='password' name='pass' value='' />";
    }
    public function _callback_NewPassEncode($post_array, $primary_key) {
        if(!empty($post_array['pass'])) {
            $new_pass = $post_array['pass'];
            $post_array['pass'] = hash('sha256', $new_pass); //encrypting password with sha256 encoding
        } else {
            unset($post_array['pass']);
        }

        return $post_array;
    }
    function manage_users_action($primary_key , $row)
    {
        return base_url('admin/set_user_permissions/'.$primary_key);
    }

    public function set_user_permissions($user_id) {
        $this->db->where('user_id', $user_id);
        $row_user = $this->db->get('users')->result();
        //if user not exists in user table
        if(count($row_user) == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'User not found.');
            return array('type'=>'redirect', 'page'=>'admin/manage_users');
        }

        $user_type = $row_user[0]->usertype;

        //library panel permissions
        if ($user_type == 5) {
            $usertype_val = 'Librarian';
            $menus = $this->db->where('user_type', 5)->where('status', 1)->order_by('priority')->get('user_menus')->result();
        }
        //operator panel permissions
        elseif ($user_type == 6) {
            $usertype_val = 'Operator';
            $menus = $this->db->where('user_type', 6)->where('status', 1)->order_by('priority')->get('user_menus')->result();
        }
        //unknown usertype
        else {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Sorry!');
            $this->session->set_flashdata('msg', 'Permissions for this user type not found.');
            return array('type'=>'redirect', 'page'=>'admin/manage_users');
        }

        $this->db->where('user_id', $user_id);
        $rs_user_prm = $this->db->get('user_permissions')->result_array();
        $prm_arr = array();
        if(count($rs_user_prm) > 0) {
            $prm_arr = array_column($rs_user_prm, 'permission', 'menu_id');
        }

        $data['user_id'] = $user_id;
        $data['menus'] = $menus;
        $data['prm_arr'] = $prm_arr;
        $data['usertype'] = $usertype_val;
        $data['section'] = 'set_user_permissions';

        $data['tab_title'] = 'Set User Permissions';
        $data['menu_name'] = 'Set User Permissions';

        return array('type' => 'load_view', 'page' => 'administrations_v', 'data' => $data);
    }

    public function form_set_user_permissions() {
        //if form not submitted
        if($this->input->post('submit') != 'submit_set_user_permissions_form') {
            $data['type'] = 'error';
            $data['msg'] = 'Something went wrong.';
            return $data;
        }

        $user_id = $this->input->post('user_id');
        $permission = $this->input->post('permission');

        $this->db->where('user_id', $user_id);
        $row_user = $this->db->get('users')->row();
        //if user not exists in user table
        if(count($row_user) == 0) {
            $data['type'] = 'error';
            $data['msg'] = 'User not found.';
            return $data;
        }

        $menus_rs = array();
        //librarian set permissions
        if($row_user->usertype == 5) {
            $menus_rs = $this->db->where('user_type', 5)->where('status', 1)->order_by('priority')->get('user_menus')->result(); //librarian menus
        }
        //operator set permissions
        if($row_user->usertype == 6) {
            $menus_rs = $this->db->where('user_type', 6)->where('status', 1)->order_by('priority')->get('user_menus')->result(); //librarian menus
        }

        if(count($menus_rs) > 0){
            foreach ($menus_rs as $menu) {
                isset($permission[$menu->menu_id]) ? $bool = 1 : $bool = 0;

                $this->db->where('user_id', $user_id);
                $this->db->where('menu_id', $menu->menu_id);
                $row_user_prm = $this->db->get('user_permissions')->row();

                //insert
                if (count($row_user_prm) == 0) {
                    $data_insert['user_id'] = $user_id;
                    $data_insert['menu_id'] = $menu->menu_id;
                    $data_insert['permission'] = $bool;
                    //inserting data
                    $this->db->trans_start();
                    $this->db->insert('user_permissions', $data_insert);
                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        # Something went wrong.
                        echo $this->db->last_query();
                        $this->db->trans_rollback();

                        $data['type'] = 'danger';
                        $data['msg'] = 'Permission set unsuccessful.';
                    } else {
                        # Everything is Perfect.
                        # Committing data to the database.
                        $this->db->trans_commit();

                        $data['type'] = 'success';
                        $data['msg'] = 'Permission set successfully.';
                    }
                }
                //update
                else {
                    $data_update['permission'] = $bool;
                    //update data
                    $this->db->where('prm_id', $row_user_prm->prm_id);
                    $this->db->update('user_permissions', $data_update);

                    $data['type'] = 'success';
                    $data['msg'] = 'Permission set successfully.';
                }
            }
        }

        return $data;
    }

    public function student_control() {
        $cls = $this->db->get('class_sec_hdr')->result_array();
         $allstd = $this->db->get('student_details')->result_array();

        $data['class'] = $cls;
         $data['students'] = $allstd;
        $data['section'] = 'student_control';
        $data['usertype'] = 'Student';
        $data['tab_title'] = 'Student Control';
        $data['menu_name'] = 'Student Control';
        
        $this->db->where('id',1);
        $query = $this->db->get('settings');
        $data['settings'] = $query->row();

        return array('type' => 'load_view', 'page' => 'administrations_v', 'data' => $data);
    }

    public function ajax_fetch_students_by_class() {
        $class_id = $this->input->post('class_id');

        $this->db->select('STD_SEQ,STD_FNAME,STD_MNAME,STD_LNAME,STD_ROLLNO,STD_REGNO,STD_DOB');
        $this->db->where("STD_LEFT", 0);
        $this->db->where("STD_STATUS", 0);
        $this->db->where('STD_CS_SEQ', $class_id);
        $this->db->order_by('STD_ROLLNO');
        $std = $this->db->get('student_details')->result_array();

        $html_std = $html_std2 = '';
        //creating individual student table row
        foreach($std as $s) {
            //fetch user blocked status
            $this->db->where('usertype', 4);
            $this->db->where('tbl_id', $s['STD_SEQ']);
            $user_rs = $this->db->get('users')->row();
            $selected = $selected2 = '';
            if(count($user_rs) > 0 && $user_rs->blocked == 1) {
                $selected = 'checked';
            }
            if(count($user_rs) > 0 && $user_rs->marksheet_blocked == 1) {
                $selected2 = 'checked';
            }

            $html_std .= <<<EOD
<label class="checkbox-custom check-success col-lg-4">
    <input {$selected} value="{$s['STD_SEQ']}" name="std[]" id="std_{$s['STD_SEQ']}" type="checkbox" class="select_all">
    <label for="std_{$s['STD_SEQ']}">{$s['STD_ROLLNO']} - {$s['STD_FNAME']} {$s['STD_MNAME']} {$s['STD_LNAME']} - {$s['STD_REGNO']}</label>
</label>
EOD;
            $html_std2 .= <<<EOD
<label class="checkbox-custom check-success col-lg-4">
    <input {$selected2} value="{$s['STD_SEQ']}" name="std2[]" id="std2_{$s['STD_SEQ']}" type="checkbox" class="select_all2">
    <label for="std2_{$s['STD_SEQ']}">{$s['STD_ROLLNO']} - {$s['STD_FNAME']} {$s['STD_MNAME']} {$s['STD_LNAME']} - {$s['STD_REGNO']}</label>
</label>
EOD;
        }

        $array['html_std'] = $html_std;
        $array['html_std2'] = $html_std2;
        return $array;
    }

    public function form_student_control() {
        //block login form
        if($this->input->post('submit') == 'block_login') {
            $class = $this->input->post('class');
            $selected_std = $this->input->post('std[]');

            $this->db->where('CS_SEQ', $class);
            $cls = $this->db->get('class_sec_hdr')->row();
            //if class does not exists
            if (count((array)$cls) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Class not found.');
                return array('page' => 'admin/student_control');
            }

            $this->db->select('STD_SEQ');
            $this->db->where("STD_CS_SEQ", $class);
            $std_all = $this->db->get('student_details')->result_array();
            $std_all = array_column($std_all, 'STD_SEQ');

            $unselected_std = array_diff($std_all, (array)$selected_std);

            //block selected students
            if (count($selected_std) > 0) {
                $data_update = array(
                    "blocked" => 1,
                );
                $this->db->where('usertype', 4);
                $this->db->where_in('tbl_id', $selected_std);
                $this->db->update('users', $data_update);
            }
            //unblock unselected students
            if (count($unselected_std) > 0) {
                $data_update = array(
                    "blocked" => 0,
                );
                $this->db->where('usertype', 4);
                $this->db->where_in('tbl_id', $unselected_std);
                $this->db->update('users', $data_update);
            }

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('title', 'Done!');
            $this->session->set_flashdata('msg', 'Students are blocked/unblocked successfully.');
            return array('page' => 'admin/student_control');
        }

        //block progress-report form
        if($this->input->post('submit') == 'block_marksheet') {
            $class = $this->input->post('class');
            $selected_std = $this->input->post('std2[]');

            $this->db->where('CS_SEQ', $class);
            $cls = $this->db->get('class_sec_hdr')->row();
            //if class does not exists
            if (count((array)$cls) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Class not found.');
                return array('page' => 'admin/student_control');
            }

            $this->db->select('STD_SEQ');
            $this->db->where("STD_CS_SEQ", $class);
            $std_all = $this->db->get('student_details')->result_array();
            $std_all = array_column($std_all, 'STD_SEQ');

            $unselected_std = array_diff($std_all, (array)$selected_std);

            //block selected students
            if (count($selected_std) > 0) {
                $data_update = array(
                    "marksheet_blocked" => 1,
                );
                $this->db->where('usertype', 4);
                $this->db->where_in('tbl_id', $selected_std);
                $this->db->update('users', $data_update);
            }
            //unblock unselected students
            if (count($unselected_std) > 0) {
                $data_update = array(
                    "marksheet_blocked" => 0,
                );
                $this->db->where('usertype', 4);
                $this->db->where_in('tbl_id', $unselected_std);
                $this->db->update('users', $data_update);
            }

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('title', 'Done!');
            $this->session->set_flashdata('msg', 'Progress reports are blocked/unblocked successfully.');
            return array('page' => 'admin/student_control');
        }
        
        
    }

} // /.Administrations_m model