<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 09-07-17
 * Time: xx:xx
 */

class Dashboard_m extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function dashboard() {

        //admin
        if($this->session->usertype == 1) {
            try{
                $crud = new grocery_CRUD();
                $crud->set_crud_url_path(base_url('admin_panel/Dashboard/dashboard'));
                $crud->set_theme('flexigrid');
                $crud->set_subject('School Name');
                $crud->set_table('company');
                $crud->unset_export();
                $crud->unset_print();
                $crud->unset_read();
                $crud->unset_delete();

                $crud->unset_add();
                $crud->unset_clone();
                $crud->unset_fields('COM_FIN_YEAR','SCHOOL_TYPE');

                $crud->columns('COM_NAME','SCHOOL_TYPE');

                // $crud->required_fields('COM_NAME', 'COM_ADD1', 'COM_CITY', 'COM_PIN', 'COM_PHONE');

                $crud->display_as('COM_NAME','School Name');
                $crud->display_as('COM_CITY','City');
                $crud->display_as('SCHOOL_TYPE','School');
                $crud->display_as('COM_PHONE','Phone');
                $crud->display_as('COM_ADD1','Address  1');
                $crud->display_as('COM_ADD2','Address 2');
                $crud->display_as('COM_PIN','Pincode');
                $crud->display_as('COM_FAX','FAX No.');
                $crud->display_as('COM_EMAIL','Email Address');
                $crud->display_as('IMAGE_PATH','School Picture');
                $crud->display_as('COM_VATNO','VAT No.');
                $crud->display_as('ESTD_CODE_NO','ESTD Code ');
                $crud->display_as('BANK_NAME','Bank Name');
                $crud->display_as('BRANCH_NAME','Branch Name');
                $crud->display_as('BANK_ADDR','Bank Address');
                $crud->display_as('COM_FIN_YEAR','Financial Year');

                $crud->set_relation('SCHOOL_TYPE', 'class_type', 'name');

                $crud->set_field_upload('HEADMASTER_SIGN','assets/img');

                $output = $crud->render();
                //rending extra value to $output
                $output->tab_title = 'School Details';
                $output->section_heading = 'School Details <small>(Edit)</small>';
                $output->menu_name = 'Dashboard';
                $output->add_button = '';

                return array('page'=>'dashboard_v', 'data'=>$output); //loading dashboard view page
            } catch(Exception $e) {
                show_error($e->getMessage().' --- '.$e->getTraceAsString());
            }
        }
        //student
        if($this->session->usertype == 4) {
            $std_id = $this->session->tbl_id;
            $std_rs = $this->db->where('STD_SEQ', $std_id)->get('student_details')->row();
            $cls_id = $std_rs->STD_CS_SEQ;
            $class_type = $this->db->where('CS_SEQ', $cls_id)->get('class_sec_hdr')->row()->Class_Type;

            //-------------------monthly fees-------------------
            if($class_type == 4){
                $months_arr = array("MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12","JAN"=>"1","FEB"=>"2","MAR"=>"3","APR"=>"4");
                if(date('Y-m', strtotime((CURRENT_YEAR+1).'-5')) > date('Y-m')) {
                    //take only till selected month (month from date input)
                    $months_arr = array_slice($months_arr, 0, array_search(date('m'), array_values($months_arr))+1, true);
                }
            }
            else{
                $months_arr = array("JAN"=>"1","FEB"=>"2","MAR"=>"3","APR"=>"4","MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12");
                if (CURRENT_YEAR == date('Y')) {
                    //take only till selected month (month from date input)
                    $months_arr = array_slice($months_arr, 0, array_search(date('m'), array_values($months_arr))+1, true);
                }
            }

            $this->db->select('FEES_DTL_MONTH, SUM(fees_monthly_dtl.FEES_DTL_AMOUNT) as total_mon_fees');
            $this->db->where('FEES_DTL_STD_SEQ', $std_id);
            $this->db->where('FEES_DTL_STD_CS_SEC', $cls_id);
            $this->db->where('FEES_DTL_COL_DATE <=', date('Y-m-d'));
            $this->db->group_by('FEES_DTL_MONTH');
            $paid_rs = $this->db->get('fees_monthly_dtl')->result_array();

            $paid_months = array_column($paid_rs, 'FEES_DTL_MONTH');
            $due_months = array_diff($months_arr, $paid_months);
            $due_month_names = '';
            $due_month_names = implode(', ', array_keys($due_months));
//            echo "<pre>"; print_r($due_month_names); die();

            //-------------------yearly fees-------------------
            $this->db->where('FM_HDR_STD_SEQ', $std_id);
            $this->db->where('FM_HDR_STD_CS_SEQ', $cls_id);
            $this->db->where('FM_HDR_FIN_YEAR', CURRENT_YEAR);
            $paid_yearly_fee_rs = $this->db->get('fees_yearly_hdr')->result_array();

            $yearly_fee_status = 'due';
            if (count($paid_yearly_fee_rs) > 0) {
                $yearly_fee_status = 'paid';
            }


            $output['due_month_names'] = $due_month_names;
            $output['yearly_fee_status'] = $yearly_fee_status;
            $output['section_heading'] = '';
            $output['add_button'] = '';
            $output['output'] = '';
            return array('page'=>'dashboard_v', 'data'=>$output);
        }
        //other
        else {
            $output['section_heading'] = '';
            $output['add_button'] = '';
            $output['output'] = '';
            return array('page'=>'dashboard_v', 'data'=>$output); //loading dashboard view page
        }

        #copy student image from 2023 to 2024
//        $std_rs = $this->db->get_where('student_details', array('STD_SEQ >='=>'879'))->result();
//        foreach ($std_rs as $std) {
//            if ($std->STD_IMAGE_PATH) {
//                copy('../2023-24/assets/img/students/' . $std->STD_IMAGE_PATH, 'assets/img/students/' . $std->STD_IMAGE_PATH);
//            }
//        }

        #make user account for students
//        $std_rs = $this->db->get('student_details')->result();
//        foreach ($std_rs as $std) {
//            #skip if account exists
//            $user_rs = $this->db->get_where('users', array('usertype'=>4, 'tbl_id'=>$std->STD_SEQ))->result();
//            if (count($user_rs) > 0) { continue; }
//
//            $data_insert['usertype'] = 4; //student
//            $data_insert['tbl_id'] = $std->STD_SEQ;
//            $data_insert['username'] = $std->STD_REGNO;
//            $data_insert['pass'] = hash('sha256', date('dmY',strtotime($std->STD_DOB))); //encrypting password with sha256 encoding;
//            $data_insert['verified'] = 1;
//            $data_insert['registration_date'] = date('Y-m-d H:i:s');
//            $this->db->insert('users', $data_insert);
//        }

    }

} // /.Dashboard_m model