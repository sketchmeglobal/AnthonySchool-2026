<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 11-01-2019
 * Time: 02:09
 */
error_reporting(0);
@ini_set('display_errors', 0);

class Fees_m extends CI_Model
{

    public function __construct()
    {
        parent::__construct();


        $this->db->query("SET sql_mode = ' ' ");


    }

    public function monthly_fees($hdr_id)
    {
        try {
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Fees/monthly_fees'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Monthly Fees');
            $crud->order_by('STD_CS_SEQ', 'ASC');
            $crud->set_table('student_details');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $crud->columns('STD_CS_SEQ', 'STD_ROLLNO', 'STD_REGNO', 'STD_SRLNO', 'STD_FNAME', 'STD_MNAME', 'STD_LNAME');
            $crud->display_as('STD_CS_SEQ', 'Class & Section Name');
            $crud->display_as('STD_ROLLNO', 'Roll No');
            $crud->display_as('STD_REGNO', 'Reg. No');
            $crud->display_as('STD_SRLNO', 'Adm. No');
            $crud->display_as('STD_FNAME', 'First Name');
            $crud->display_as('STD_MNAME', 'Middle Name');
            $crud->display_as('STD_LNAME', 'Last Name');

            $crud->set_relation('STD_CS_SEQ', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');

            $crud->add_action('Proceed', base_url() . 'assets/grocery_crud/themes/flexigrid/css/images/next.gif', 'admin/add_monthly_fees');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Monthly Fees';
            $output->section_heading = 'Monthly Fees <small>(Add)</small>';
            $output->menu_name = 'Monthly Fees';
            $output->add_button = '';
            if (isset($hdr_id)) {
                $output->print = 'admin/print_monthly_fess/' . $hdr_id;
            }

            return array('page' => 'common_v', 'data' => $output); //loading common view page
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function add_monthly_fees($std_id)
    {
        $data['user_type'] = $this->session->usertype;
        $data['url_param'] = 'admin/add_monthly_fees/';
        if ($std_id == '') {
            $data['tab_title'] = 'Add Monthly Fees';
            $data['menu_name'] = 'Add Monthly Fees';
            return array('type' => 'load_view', 'page' => 'fees_v', 'data' => $data);
        }
        $this->db->where('STD_SEQ', $std_id);
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $row = $this->db->get('student_details')->row();
        if (count((array)$row) == 0) { //if student not exists in student table
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oops!');
            $this->session->set_flashdata('msg', 'Student does not exists.');
            return array('type' => 'redirect', 'page' => 'admin/monthly_fees_report');
        } else {
            $year = $this->db->get('company')->row()->COM_FIN_YEAR;
            $this->db->select('FEES_DTL_MONTH');
            $this->db->where('FEES_DTL_STD_SEQ', $std_id);
            $this->db->where('FEES_DTL_STD_CS_SEC', $row->STD_CS_SEQ);
            $this->db->where('FEES_DTL_FIN_YEAR', $year);
            $this->db->group_by('FEES_DTL_MONTH');
            $result = $this->db->get('fees_monthly_dtl')->result_array();
            if (count($result) >= '12') { //if student paid all monthly fees for his class
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'That student paid all monthly fees, no fees remain.');
                return array('type' => 'redirect', 'page' => 'admin/monthly_fees_report');
            } else { //adding monthly fees
                if ($row->STD_CONSC == 1) { //if student granted for concession
                    $this->db->select('fees_concession.*, acc_master.*');
                    $this->db->where('std_id', $std_id);
                    $this->db->where('class_id', $row->STD_CS_SEQ);
                    $this->db->where('CS_FEES_TYPE', '0');
                    $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                    $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = acc_master.ACC_MASTER_CODE', 'left');
                    $this->db->group_by('fees_concession.fc_id');
                    $result_all_fees = $this->db->get('fees_concession')->result_array();
                    //   echo $this->db->last_query();
                    //  die();
                    $concession_fees = array_sum(array_map(function ($value) {
                        return $value['Fees'];
                    }, $result_all_fees));
                    if($concession_fees == 0){
                        $stddata = array(
                            "STD_CONSC" => 0,
                        );
                        $this->db->update('student_details',$stddata, array('STD_SEQ'=>$std_id));
                        
                        $this->db->where('CS_SEQ', $row->STD_CS_SEQ);
                        $this->db->where('CS_FEES_TYPE', '0');
                        $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                        $result_all_fees = $this->db->get('class_sec_dtl')->result_array();
                    }
                    if (count($result_all_fees) == 0) { //if concession fees not added yet
                        if ($this->session->usertype == 1) {
                            //die();
                            /*$this->session->set_flashdata('type', 'error');
                            $this->session->set_flashdata('title', 'Stop!');
                            $this->session->set_flashdata('msg', 'Add Concession fees first.');
                            return array('type' => 'redirect', 'page' => "admin/add_concession_fees/$std_id/monthly");*/
                        } else {
                            $this->session->set_flashdata('type', 'error');
                            $this->session->set_flashdata('title', 'Stop!');
                            $this->session->set_flashdata('msg', 'Concession fees for that student is not added yet.');

                            if ($this->session->usertype == 4) {
                                return array('type' => 'redirect', 'page' => 'admin/dashboard');
                            }
                            return array('type' => 'redirect', 'page' => 'admin/monthly_fees_report');
                        }
                    }
                } else { //if student is not granted for concession
                    /*echo $row->STD_CS_SEQ;
                    die();*/
                    $this->db->where('CS_SEQ', $row->STD_CS_SEQ);
                    $this->db->where('CS_FEES_TYPE', '0');
                    $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                    $result_all_fees = $this->db->get('class_sec_dtl')->result_array();
                    //  echo $this->db->last_query();
                    //  die();
                    if (count($result_all_fees) == 0) { //if fees not added yet
                        $this->session->set_flashdata('type', 'error');
                        $this->session->set_flashdata('title', 'Stop!');
                        $this->session->set_flashdata('msg', 'Fees for that class is not added yet.');
                        return array('type' => 'redirect', 'page' => 'admin/monthly_fees_report');
                    }
                }
                //check if all yearly fees paid or not
                $year = $this->db->get('company')->row()->COM_FIN_YEAR;
                $this->db->select('FEES_DTL_ACC_SEQ');
                $this->db->where('FEES_DTL_STD_SEQ', $std_id);
                $this->db->where('FEES_DTL_STD_CS_SEC', $row->STD_CS_SEQ);
                $this->db->where('FEES_DTL_FIN_YEAR', $year);
                $result_paid_yearly_fee = $this->db->get('fees_yearly_dtl')->result_array();
                // echo "<pre>"; print_r($result_paid_yearly_fee); die();
                // echo $this->db->last_query(); die();
                /*if ($row->STD_CONSC == 1) { //if student granted for concession
                    $this->db->select('fees_concession.*, acc_master.*');
                    $this->db->where('std_id', $std_id);
                    $this->db->where('class_id', $row->STD_CS_SEQ);
                    // $this->db->where('CS_FEES_TYPE', '1');
                    $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                    $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = acc_master.ACC_MASTER_CODE', 'left');
                    $this->db->group_by('fees_concession.fc_id');
                    $result_all_yearly_fee = $this->db->get('fees_concession')->result_array();
                    // echo $this->db->last_query(); die();
                    // echo "<pre>"; print_r($result_all_yearly_fee); die();
                } else { //if student is not granted for concession*/
                    $this->db->where('CS_SEQ', $row->STD_CS_SEQ);
                    $this->db->where('CS_FEES_TYPE', '1'); //yearly fees
                    $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                    $result_all_yearly_fee = $this->db->get('class_sec_dtl')->result_array();
                    // echo $this->db->last_query(); die();
                //}
                 // echo count($result_all_yearly_fee); die();
                if (count($result_paid_yearly_fee) >= count($result_all_yearly_fee)) {
                    $data['all_yearly_fees_paid'] = 'yes';
                } else {
                    $data['all_yearly_fees_paid'] = 'no';
                }
                // echo $data['all_yearly_fees_paid']; die();
                
                // echo $data['all_yearly_fees_paid']; die();
                //check if all new admission fees paid or not
                $year = $this->db->get('company')->row()->COM_FIN_YEAR;
                $this->db->select('FEES_DTL_ACC_SEQ');
                $this->db->where('FEES_DTL_STD_SEQ', $std_id);
                $this->db->where('FEES_DTL_STD_CS_SEC', $row->STD_CS_SEQ);
                $this->db->where('FEES_DTL_FIN_YEAR', $year);
                $result_paid_new_adm_fee = $this->db->get('fees_newadm_dtl')->result_array();
                /*if ($row->STD_CONSC == 1) { //if student granted for concession
                    $this->db->where('std_id', $std_id);
                    $this->db->where('class_id', $row->STD_CS_SEQ);
                    // $this->db->where('CS_FEES_TYPE', '2');
                    $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                    $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = acc_master.ACC_MASTER_CODE', 'left');
                    $this->db->group_by('fees_concession.fc_id');
                    $result_all_new_adm_fee = $this->db->get('fees_concession')->result_array();
                } else { //if student is not granted for concession*/
                    $this->db->where('CS_SEQ', $row->STD_CS_SEQ);
                    $this->db->where('CS_FEES_TYPE', '2'); //New admission fees
                    $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                    $result_all_new_adm_fee = $this->db->get('class_sec_dtl')->result_array();
                /*}*/
                if (count($result_paid_new_adm_fee) >= count($result_all_new_adm_fee)) {
                    $data['all_new_adm_fees_paid'] = 'yes';
                } else {
                    $data['all_new_adm_fees_paid'] = 'no';
                }


                $total_fees = array_sum(array_map(function ($value) {
                    return $value['Fees'];
                }, $result_all_fees));

                if($row->Class_Type == 4){
                    $months_arr = array("May"=>"5","June"=>"6","July"=>"7","August"=>"8","September"=>"9","October"=>"10","November"=>"11","December"=>"12","January"=>"1","February"=>"2","March"=>"3","April"=>"4");
                }else{
                    $months_arr = array("January"=>"1","February"=>"2","March"=>"3","April"=>"4","May"=>"5","June"=>"6","July"=>"7","August"=>"8","September"=>"9","October"=>"10","November"=>"11","December"=>"12");
                }
                $months_paid_arr = array_map(function ($value) {
                    return $value['FEES_DTL_MONTH'];
                }, $result); // array of months for which payment is done
                $months_remain_arr = array_diff($months_arr, $months_paid_arr); //array of months for which payment is not done yet

                $get_auto_index = $this->db->query("SHOW TABLE STATUS LIKE 'fees_monthly_hdr'")->row()->Auto_increment;
                $string = 'RM' . date("y") . $get_auto_index . date("md");
                $rcpt_no = substr($string, 0, 15);

                $this->session->set_userdata('all_monthly_fees', $result_all_fees);
                $this->session->set_userdata('total_fees', $total_fees);

                $data['all_fees'] = $result_all_fees;
                $data['months_remain_arr'] = $months_remain_arr;
                $data['rcpt_no'] = $rcpt_no;
                $data['std_id'] = $std_id;
                $data['class_id'] = $row->STD_CS_SEQ;
                $data['form_type'] = 'monthly_fees';
                //temporary code
                $this->db->select('FM_HDR_COL_DATE');
                $this->db->order_by('FM_HDR_SRLNO', 'DESC');
                $this->db->limit('1');
                $row_date = $this->db->get('fees_monthly_hdr')->row();
                if ($this->session->has_userdata('sessoin_collect_date')) {
                    $date = $this->session->userdata('sessoin_collect_date');
                }else{
                    if (count((array)$row_date) > 0) {
                     $date = date('Y-m-d', strtotime($row_date->FM_HDR_COL_DATE));
                    } else {
                     $date = date('Y-m-d');
                    }
                }
                if ($this->session->has_userdata('session_collect_bank')) {
                    $bank_nm = $this->session->userdata('session_collect_bank');
                }else{
                    if (count((array)$row_date) > 0) {
                     $bank_nm = $row_date->FM_HDR_B_NAME;
                    }
                }
                if ($this->session->has_userdata('session_collect_payment_type')) {
                    $payment_type = $this->session->userdata('session_collect_payment_type');
                }else{
                    if (count((array)$row_date) > 0) {
                        $payment_type = $row_date->FM_HDR_P_TYP;
                    }
                }
                // echo $total_fees;
                // die;
                $data['date'] = $date;
                $data['bank_nm'] = $bank_nm;
                $data['payment_type'] = $payment_type;

                $data['tab_title'] = 'Add Monthly Fees';
                $data['total_fees'] = $total_fees;
                $data['section_heading'] = '<h4>Student Name: <strong>' . $row->STD_FNAME . ' ' . $row->STD_MNAME . ' ' . $row->STD_LNAME . '</strong><br> Reg. No: <strong>' . $row->STD_REGNO . '</strong><br> Class & Sec: <strong>' . $row->Class_Name . ' - ' . $row->Sec_Name . '</strong><br> Roll No: <strong>' . $row->STD_ROLLNO . '</strong></h4>';
                $data['menu_name'] = 'Add Monthly Fees';

                return array('type' => 'load_view', 'page' => 'fees_v', 'data' => $data);
            }
        }
    }


    public function form_add_consc_fees()
    {
        if ($this->input->post('submit') == 'submit_concession_fee') { //if form submitted

            $std_id = $this->input->post('std_id');

            $class_id = $this->input->post('class_id');

            $success_val = $this->input->post('success_val');

            $data_update['STD_CONSC'] = 1;

            //updating Student details
            $this->db->where('STD_SEQ', $std_id);
            $this->db->update('student_details', $data_update);

            //add initial concession fees if not added yet
            $this->db->where('std_id', $std_id);
            $this->db->where('class_id', $class_id);
            $result = $this->db->get('fees_concession')->result_array();
            if(count($result) == 0) {
                $this->db->where('CS_SEQ', $class_id);
                $fees_rs = $this->db->get('class_sec_dtl')->result_array();
                foreach ($fees_rs as $fee) {
                    unset($data_insert);
                    $data_insert['std_id'] = $std_id;
                    $data_insert['class_id'] = $class_id;
                    $data_insert['ACC_MASTER_CODE'] = $fee['ACC_MASTER_CODE'];
                    $data_insert['Fees'] = $fee['Fees'];

                    //inserting data
                    $this->db->insert('fees_concession', $data_insert);
                }
            }

            return array('type' => 'redirect', 'page' => "admin/edit_concession_fees/$std_id/$success_val");

        } else if ($this->input->post('submit') == 'update_concession_fee') { //if form submitted

            $std_id = $this->input->post('std_id');

            $class_id = $this->input->post('class_id');

            $success_val = $this->input->post('success_val');

            return array('type' => 'redirect', 'page' => "admin/edit_concession_fees/$std_id/$success_val");

        } else if ($this->input->post('submit') == 'add_monthly_yearly_fee1') { //if form submitted
            $std_id = $this->input->post('std_id');
            $class_id = $this->input->post('class_id');
            $year = $this->db->get('company')->row()->COM_FIN_YEAR;
            $this->db->where('STD_SEQ', $std_id);
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $row = $this->db->get('student_details')->row();
            $this->db->select('FEES_DTL_ACC_SEQ');
            $this->db->where('FEES_DTL_STD_SEQ', $std_id);
            $this->db->where('FEES_DTL_STD_CS_SEC', $class_id);
            $this->db->where('FEES_DTL_FIN_YEAR', $year);
            $result = $this->db->get('fees_yearly_dtl')->result_array();
            /*if ($row->STD_CONSC == 1) { //if student granted for concession
                $this->db->select('fees_concession.*, acc_master.*');
                $this->db->where('std_id', $std_id);
                $this->db->where('class_id', $class_id);
                $this->db->where('CS_FEES_TYPE', '1');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = acc_master.ACC_MASTER_CODE', 'left');
                $this->db->group_by('fees_concession.fc_id');
                $result_all_fees = $this->db->get('fees_concession')->result_array();
            } else { //if student is not granted for concession*/
                $this->db->where('CS_SEQ', $class_id);
                $this->db->where('CS_FEES_TYPE', '1'); //yearly fees
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                $result_all_fees = $this->db->get('class_sec_dtl')->result_array();
            /*}*/
            //if student paid all yearly fees for his class
            if (count($result) >= count($result_all_fees)) {
                // die();
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'That student paid all yearly fees, no fees remain.');
                return array('type' => 'redirect', 'page' => "admin/add_monthly_fees/$std_id");
            } else {
                return array('type' => 'redirect', 'page' => "admin/add_yearly_fees/$std_id");
            }
        } else if ($this->input->post('submit') == 'add_monthly_yearly_fee2') { //if form submitted
            $std_id = $this->input->post('std_id');

            $class_id = $this->input->post('class_id');
            $year = $this->db->get('company')->row()->COM_FIN_YEAR;
            $this->db->where('STD_SEQ', $std_id);
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $row = $this->db->get('student_details')->row();
            $this->db->select('FEES_DTL_ACC_SEQ');
            $this->db->where('FEES_DTL_STD_SEQ', $std_id);
            $this->db->where('FEES_DTL_STD_CS_SEC', $class_id);
            $this->db->where('FEES_DTL_FIN_YEAR', $year);
            $result = $this->db->get('fees_yearly_dtl')->result_array();
            /*if ($row->STD_CONSC == 1) { //if student granted for concession
                $this->db->select('fees_concession.*, acc_master.*');
                $this->db->where('std_id', $std_id);
                $this->db->where('class_id', $class_id);
                $this->db->where('CS_FEES_TYPE', '1');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = acc_master.ACC_MASTER_CODE', 'left');
                $this->db->group_by('fees_concession.fc_id');
                $result_all_fees = $this->db->get('fees_concession')->result_array();
            } else { //if student is not granted for concession*/
                $this->db->where('CS_SEQ', $class_id);
                $this->db->where('CS_FEES_TYPE', '1'); //yearly fees
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                $result_all_fees = $this->db->get('class_sec_dtl')->result_array();
            /*}*/
            //if student paid all yearly fees for his class
            if (count($result) >= count($result_all_fees)) {
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'That student paid all yearly fees, no fees remain.');
                return array('type' => 'redirect', 'page' => "admin/add_new_admission_fees/$std_id");
            } else {
                return array('type' => 'redirect', 'page' => "admin/add_yearly_fees/$std_id");
            }
        } else if ($this->input->post('submit') == 'add_yearly_monthly_fee') { //if form submitted

            $std_id = $this->input->post('std_id');

            $class_id = $this->input->post('class_id');

            return array('type' => 'redirect', 'page' => "admin/add_monthly_fees/$std_id");

        } else if ($this->input->post('submit') == 'add_monthly_new_adms_fee1') { //if form submitted
            $std_id = $this->input->post('std_id');

            $class_id = $this->input->post('class_id');
            $year = $this->db->get('company')->row()->COM_FIN_YEAR;
            $this->db->where('STD_SEQ', $std_id);
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $row = $this->db->get('student_details')->row();
            $this->db->select('FEES_DTL_ACC_SEQ');
            $this->db->where('FEES_DTL_STD_SEQ', $std_id);
            $this->db->where('FEES_DTL_STD_CS_SEC', $class_id);
            $this->db->where('FEES_DTL_FIN_YEAR', $year);
            $result = $this->db->get('fees_newadm_dtl')->result_array();
            /*if ($row->STD_CONSC == 1) { //if student granted for concession
                $this->db->select('fees_concession.*, acc_master.*');
                $this->db->where('std_id', $std_id);
                $this->db->where('class_id', $class_id);
                $this->db->where('CS_FEES_TYPE', '2');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = acc_master.ACC_MASTER_CODE', 'left');
                $this->db->group_by('fees_concession.fc_id');
                $result_all_fees = $this->db->get('fees_concession')->result_array();
            } else { //if student is not granted for concession*/
                $this->db->where('CS_SEQ', $class_id);
                $this->db->where('CS_FEES_TYPE', '2'); //yearly fees
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                $result_all_fees = $this->db->get('class_sec_dtl')->result_array();
            /*}*/
            //if student paid all yearly fees for his class
            if (count($result) >= count($result_all_fees)) {
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'That student paid all new admission fees, no fees remain.');
                return array('type' => 'redirect', 'page' => "admin/add_monthly_fees/$std_id");
            } else {
                return array('type' => 'redirect', 'page' => "admin/add_new_admission_fees/$std_id");
            }
        } else if ($this->input->post('submit') == 'add_monthly_new_adms_fee2') { //if form submitted
            $std_id = $this->input->post('std_id');

            $class_id = $this->input->post('class_id');
            $year = $this->db->get('company')->row()->COM_FIN_YEAR;
            $this->db->where('STD_SEQ', $std_id);
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $row = $this->db->get('student_details')->row();
            $this->db->select('FEES_DTL_ACC_SEQ');
            $this->db->where('FEES_DTL_STD_SEQ', $std_id);
            $this->db->where('FEES_DTL_STD_CS_SEC', $class_id);
            $this->db->where('FEES_DTL_FIN_YEAR', $year);
            $result = $this->db->get('fees_newadm_dtl')->result_array();
            /*if ($row->STD_CONSC == 1) { //if student granted for concession
                $this->db->select('fees_concession.*, acc_master.*');
                $this->db->where('std_id', $std_id);
                $this->db->where('class_id', $class_id);
                $this->db->where('CS_FEES_TYPE', '2');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = acc_master.ACC_MASTER_CODE', 'left');
                $this->db->group_by('fees_concession.fc_id');
                $result_all_fees = $this->db->get('fees_concession')->result_array();
            } else { //if student is not granted for concession*/
                $this->db->where('CS_SEQ', $class_id);
                $this->db->where('CS_FEES_TYPE', '2'); //yearly fees
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                $result_all_fees = $this->db->get('class_sec_dtl')->result_array();
            /*}*/
            //if student paid all yearly fees for his class
            if (count($result) >= count($result_all_fees)) {
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'That student paid all new admission fees, no fees remain.');
                return array('type' => 'redirect', 'page' => "admin/add_yearly_fees/$std_id");
            } else {
                return array('type' => 'redirect', 'page' => "admin/add_new_admission_fees/$std_id");
            }
        }

    }


    public function form_add_monthly_fees()
    {
        // echo "<pre>"; print_r($this->session->userdata()); die();
        if ($this->input->post('submit') == 'submit_monthly_fees') { //if form submitted

            $all_fees = $this->session->userdata('all_monthly_fees');
            $net_fees = $this->session->userdata('net_monthly_fees');

            $std_id = $this->input->post('std_id');
            $class_id = $this->input->post('class_id');
            $date = date('Y-m-d', strtotime($this->input->post('date')));
            $payment_type = $this->input->post('payment_type');
            $bank_name = $this->input->post('bank_name');
            $card_no = $this->input->post('card_no');
            $encash_date = $this->input->post('encash_date');
            $late_fine = $this->input->post('late_fine');
            $concession_fine = $this->input->post('concession_fine');
            $modify_tution_fee = $this->input->post('modify_tution_fee');
            $checkbox = $this->input->post('checkbox');
            $checkbox_fees_mt = $this->input->post('checkbox_fees_mt');

            $this->session->set_userdata('sessoin_collect_date', $date);
            $this->session->set_userdata('session_collect_bank', $bank_name);
            $this->session->set_userdata('session_collect_payment_type', $payment_type);

            $get_auto_index = $this->db->query("SHOW TABLE STATUS LIKE 'fees_monthly_hdr'")->row()->Auto_increment;
            $string = 'RM' . date("y") . $get_auto_index . date("md");
            $rcpt_no = substr($string, 0, 15);
            $fin_year = $this->db->get('company')->row()->COM_FIN_YEAR;

            $data_insert_hdr['FM_HDR_RCPT_NO'] = $rcpt_no;
            $data_insert_hdr['FM_HDR_STD_SEQ'] = $std_id;
            $data_insert_hdr['FM_HDR_STD_CS_SEQ'] = $class_id;
            $data_insert_hdr['FM_HDR_P_TYP'] = $payment_type;
            $data_insert_hdr['FM_HDR_B_NAME'] = $bank_name;
            $data_insert_hdr['FM_HDR_CARD_NO'] = $card_no;
            $data_insert_hdr['encash_date'] = $encash_date;
            $data_insert_hdr['FM_HDR_LATE_FEES'] = $late_fine;
            $data_insert_hdr['FM_HDR_CONC_FEES'] = $concession_fine;
            $data_insert_hdr['FM_HDR_TOT_FEES'] = $net_fees;
            $data_insert_hdr['FM_HDR_FIN_YEAR'] = $fin_year;
            $data_insert_hdr['FM_HDR_COL_DATE'] = $date;

            //inserting data

            //echo "<pre>"; print_r($data_insert_hdr);    die();
            //error_reporting( E_ALL );

            if ($this->db->insert('fees_monthly_hdr', $data_insert_hdr)) {
                //echo $this->db->last_query();
                //echo "<pre>"; print_r($all_fees);    die();
                foreach ($checkbox as $month) {
                    // $fee['ACC_MASTER_CODE']
                    foreach ($all_fees as $fee) {
                        if (in_array($fee['ACC_MASTER_CODE'], $checkbox_fees_mt)) {
                            $data_insert_dtl['FEES_DTL_HDR_SRLNO'] = $get_auto_index;
                            $data_insert_dtl['FEES_DTL_STD_SEQ'] = $std_id;
                            $data_insert_dtl['FEES_DTL_STD_CS_SEC'] = $class_id;
                            $data_insert_dtl['FEES_DTL_MONTH'] = $month;
                            $data_insert_dtl['FEES_DTL_ACC_SEQ'] = $fee['ACC_MASTER_CODE'];
                            if ($fee['ACC_MASTER_CODE'] == 4 and $modify_tution_fee > 0) {
                                $data_insert_dtl['FEES_DTL_AMOUNT'] = $modify_tution_fee;
                            } else {
                                $data_insert_dtl['FEES_DTL_AMOUNT'] = $fee['Fees'];
                            }

                            $data_insert_dtl['FEES_DTL_FIN_YEAR'] = $fin_year;
                            $data_insert_dtl['FEES_DTL_COL_DATE'] = $date;
                            //inserting data


                            //echo "<pre>"; print_r($data_insert_dtl);    die();
                            $this->db->insert('fees_monthly_dtl', $data_insert_dtl);
                        }
                    }
                }
            }
            //echo "<pre>"; print_r($all_fees);    die();

            $this->session->unset_userdata(array('all_fees', 'total_fees', 'net_fees'));

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Monthly fees payment successful.');

            //die();

            if ($this->session->usertype == 4) {
                return array('page' => 'admin/dashboard');
            }
            //return array('page'=>'admin/add_monthly_fees/'.$std_id, 'type'=>'redirect');
            $student = $this->db->get_where('student_details', array('STD_SEQ'=>$std_id))->row();
            $last_student_name = $student->STD_FNAME.' '.$student->STD_MNAME.' '.$student->STD_LNAME.' - '.$student->STD_REGNO;
            
            redirect(base_url('admin/add_monthly_fees?print=print_monthly_fess&val=' . $get_auto_index.'&last_student_name='.$last_student_name));


            //die();
            /*
            return array('page'=>'admin/add_monthly_fees/'.$std_id);

            $data['net_fees'] = $net_fees;
            $data['std_id'] = $std_id;
            
            $this->session->set_userdata('student_id', $std_id);
            return array('page'=>'payment', 'data'=> $data);*/


        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');


            /*if ($this->session->usertype == 4) {
                return array('page'=>'admin/dashboard');
            }
            return array('page'=>'admin/monthly_fees');*/
        }
    }


    public function monthly_fees_payment_complete()
    {

        $data_insert = array();
        date_default_timezone_set("Asia/Calcutta");   //India time (GMT+5:30)
        $data_insert['student_id'] = $this->session->userdata('student_id');
        $data_insert['sbi_ref_no'] = $this->input->post('sbi_ref_no');
        $data_insert['status_desc'] = $this->input->post('status_desc');
        $data_insert['status'] = $this->input->post('status');
        $data_insert['amount'] = $this->input->post('amount');
        $data_insert['ref_no'] = $this->input->post('ref_no');
        $data_insert['checkSum'] = $this->input->post('checkSum');
        $data_insert['created_at'] = date("Y-m-d");

        if ($this->input->post('status') == 'Failure') {
            $this->db->delete('fees_monthly_hdr', array('FM_HDR_STD_SEQ' => $this->session->userdata('student_id')));

            $this->db->delete('fees_monthly_dtl', array('FEES_DTL_STD_SEQ' => $this->session->userdata('student_id')));
        }


        //inserting data
        if ($this->db->insert('payment_succ_data', $data_insert)) {
            $this->session->unset_userdata('student_id');
            return array('status' => 'success');
        }

    }

    public function ajax_net_fee()
    {
        $late_fine = $this->input->post('late_fine');
        $array = array();
        $concession_fine = $this->input->post('concession_fine');
        if ($this->input->post('modify_tution_fee') > 0) {
            $modify_tution_fee = $this->input->post('modify_tution_fee');
            // echo "<pre>"; print_r($this->session->userdata('all_fees')); die();
            $total_fees = 0;
            foreach ($this->session->userdata('all_monthly_fees') as $fes) {
                if ($fes['CS_FEES_TYPE'] == 0) {
                    if ($fes['ACC_MASTER_CODE'] == 4) {
                        $total_fees += $modify_tution_fee;
                        $array['tution_fees'] = sprintf("%.2f", $modify_tution_fee);
                    } else {
                        $total_fees += $fes['Fees'];
                    }
                }
            }
        } else {
            $total_fees = $this->session->userdata('total_fees');
            foreach ($this->session->userdata('all_monthly_fees') as $fes) {
                if ($fes['CS_FEES_TYPE'] == 0 and $fes['ACC_MASTER_CODE'] == 4) {
                    $array['tution_fees'] = sprintf("%.2f", $fes['Fees']);
                }
            }
        }
        // echo $total_fees; die();
        $total_months = 1;
        if ($this->input->post('total_months') > 0) {
            $total_months = $this->input->post('total_months');
        }
        $array['total_months'] = $total_months;
        $array['total_fees'] = $total_fees;
        $array['total_fees_cal'] = $total_fees * $total_months;
        $array['net_fees'] = ($total_fees - $concession_fine) * $total_months + $late_fine;
        $this->session->set_userdata('net_fees', $array['net_fees']);
        $this->session->set_userdata('net_monthly_fees', $array['net_fees']);
        return $array;
    }

    public function yearly_fees($hdr_id)
    {
        try {
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Fees/yearly_fees'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Yearly Fees');
            $crud->order_by('STD_CS_SEQ', 'ASC');
            $crud->set_table('student_details');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $crud->columns('STD_CS_SEQ', 'STD_ROLLNO', 'STD_REGNO', 'STD_SRLNO', 'STD_FNAME', 'STD_MNAME', 'STD_LNAME');
            $crud->display_as('STD_CS_SEQ', 'Class & Section Name');
            $crud->display_as('STD_ROLLNO', 'Roll No');
            $crud->display_as('STD_REGNO', 'Reg. No');
            $crud->display_as('STD_SRLNO', 'Adm. No');
            $crud->display_as('STD_FNAME', 'First Name');
            $crud->display_as('STD_MNAME', 'Middle Name');
            $crud->display_as('STD_LNAME', 'Last Name');

            $crud->set_relation('STD_CS_SEQ', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');

            $crud->add_action('Proceed', base_url() . 'assets/grocery_crud/themes/flexigrid/css/images/next.gif', 'admin/add_yearly_fees');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Yearly Fees';
            $output->section_heading = 'Yearly Fees <small>(Add)</small>';
            $output->menu_name = 'Yearly Fees';
            $output->add_button = '';
            if (isset($hdr_id)) {
                $output->print = 'admin/print_yearly_fess/' . $hdr_id;
            }
            return array('page' => 'common_v', 'data' => $output); //loading common view page
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function add_yearly_fees($std_id) {
        $data['user_type'] = $this->session->usertype;
        $data['url_param'] = 'admin/add_yearly_fees/';
        if ($std_id == '') {
            $data['tab_title'] = 'Add Yearly Fees';
            $data['menu_name'] = 'Add Yearly Fees';
            return array('type' => 'load_view', 'page' => 'fees_v', 'data' => $data);
        }
        $this->db->where('STD_SEQ', $std_id);
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $row = $this->db->get('student_details')->row();
        if (count((array)$row) == 0) { //if student not exists in student table
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oops!');
            $this->session->set_flashdata('msg', 'Student does not exists.');
            return array('type' => 'redirect', 'page' => 'admin/yearly_fees_report');
        } else {
            $year = $this->db->get('company')->row()->COM_FIN_YEAR;

            $this->db->select('FEES_DTL_ACC_SEQ');
            $this->db->where('FEES_DTL_STD_SEQ', $std_id);
            $this->db->where('FEES_DTL_STD_CS_SEC', $row->STD_CS_SEQ);
            $this->db->where('FEES_DTL_FIN_YEAR', $year);
            $result = $this->db->get('fees_yearly_dtl')->result_array();

            $this->db->where('FM_HDR_STD_SEQ', $std_id);
            $this->db->where('FM_HDR_STD_CS_SEQ', $row->STD_CS_SEQ);
            $this->db->where('FM_HDR_FIN_YEAR', $year);
            $fees_hdr_row = $this->db->get('fees_yearly_hdr')->row();

            //adding yearly fees
            /*if ($row->STD_CONSC == 1) { //if student granted for concession
                $this->db->select('fees_concession.*, acc_master.*');
                $this->db->where('std_id', $std_id);
                $this->db->where('class_id', $row->STD_CS_SEQ);
                $this->db->where('CS_FEES_TYPE', '1');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = acc_master.ACC_MASTER_CODE', 'left');
                $this->db->group_by('fees_concession.fc_id');
                $result_all_fees = $this->db->get('fees_concession')->result_array();
                echo count($result_all_fees); die();
                if (count($result_all_fees) == 0) { //if concession fees not added yet
                    if ($this->session->usertype == 1) {
                        $this->session->set_flashdata('type', 'error');
                        $this->session->set_flashdata('title', 'Stop!');
                        $this->session->set_flashdata('msg', 'Add Concession fees first.');
                        return array('type' => 'redirect', 'page' => "admin/add_concession_fees/$std_id/yearly");
                        $data['concession_fees_add'] = false;
                    } else {
                        $this->session->set_flashdata('type', 'error');
                        $this->session->set_flashdata('title', 'Stop!');
                        $this->session->set_flashdata('msg', 'Concession fees for that student is not added yet.');
                        return array('type' => 'redirect', 'page' => 'admin/yearly_fees_report');
                    }
                }else{
                    $data['concession_fees_add'] = true;
                }
            } else { //if student is not granted for concession*/
                $this->db->where('CS_SEQ', $row->STD_CS_SEQ);
                $this->db->where('CS_FEES_TYPE', '1'); //yearly fees
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                $result_all_fees = $this->db->get('class_sec_dtl')->result_array();
                if (count($result_all_fees) == 0) { //if fees not added yet
                    $this->session->set_flashdata('type', 'error');
                    $this->session->set_flashdata('title', 'Stop!');
                    $this->session->set_flashdata('msg', 'Fees for that class is not added yet.');
                    return array('type' => 'redirect', 'page' => 'admin/yearly_fees_report');
                }
            /*}*/
            
           

            //if student have any due amount
            if( (count($fees_hdr_row) > 0) && ($fees_hdr_row->due_amount != 0) ) {
                $data['tab_title'] = 'Pay Due Fees';
                $data['section_heading'] = '<h4>Student Name: <strong>' . $row->STD_FNAME . ' ' . $row->STD_MNAME . ' ' . $row->STD_LNAME . '</strong><br> Reg. No: <strong>' . $row->STD_REGNO . '</strong><br> Class & Sec: <strong>' . $row->Class_Name . ' - ' . $row->Sec_Name . '</strong><br> Roll No: <strong>' . $row->STD_ROLLNO . '</strong></h4>';
                $data['menu_name'] = 'Pay Due Fees';
                $data['form_type'] = 'yearly_due_fees';
                $data['class_id'] = $row->STD_CS_SEQ;
                $data['fees_hdr_row'] = $fees_hdr_row;
                
                return array('type' => 'load_view', 'page' => 'fees_v', 'data' => $data);
            }
           

            //if student paid all yearly fees for his class
            if (count($result) >= count($result_all_fees)) {
                //die('on fees');
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'That student paid all yearly fees, no fees remain.');
                return array('type' => 'redirect', 'page' => 'admin/yearly_fees_report');
            }
            //check if all new admission fees paid or not
            $year = $this->db->get('company')->row()->COM_FIN_YEAR;
            $this->db->select('FEES_DTL_ACC_SEQ');
            $this->db->where('FEES_DTL_STD_SEQ', $std_id);
            $this->db->where('FEES_DTL_STD_CS_SEC', $row->STD_CS_SEQ);
            $this->db->where('FEES_DTL_FIN_YEAR', $year);
            $result_paid_new_adm_fee = $this->db->get('fees_newadm_dtl')->result_array();
            /*if ($row->STD_CONSC == 1) { //if student granted for concession
                $this->db->where('std_id', $std_id);
                $this->db->where('class_id', $row->STD_CS_SEQ);
                $this->db->where('CS_FEES_TYPE', '2');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = acc_master.ACC_MASTER_CODE', 'left');
                $this->db->group_by('fees_concession.fc_id');
                $result_all_new_adm_fee = $this->db->get('fees_concession')->result_array();
            } else { //if student is not granted for concession*/
                $this->db->where('CS_SEQ', $row->STD_CS_SEQ);
                $this->db->where('CS_FEES_TYPE', '2'); //New admission fees
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                $result_all_new_adm_fee = $this->db->get('class_sec_dtl')->result_array();
            /*}*/
            if (count($result_paid_new_adm_fee) >= count($result_all_new_adm_fee)) {
                $data['all_new_adm_fees_paid'] = 'yes';
            } else {
                $data['all_new_adm_fees_paid'] = 'no';
            }


            $total_fees = array_sum(array_map(function ($value) {
                return $value['Fees'];
            }, $result_all_fees));
            
            $all_fees_arr = array_map(function ($value) {
                return $value['ACC_MASTER_CODE'];
            }, $result_all_fees); //array of all yearly fees id
            $fees_paid_arr = array_map(function ($value) {
                return $value['FEES_DTL_ACC_SEQ'];
            }, $result); //array of fees id for which payment is done
            $fees_remain_arr = array_diff($all_fees_arr, $fees_paid_arr); //array of fees id for which payment is not done yet
            //creating array of remaining fees id with fees name as key, like: Array( [EXAMINATION FEES] => 8 [ELECTRICITY FEES] => 7 )
            foreach ($fees_remain_arr as $f) {
                $this->db->select('ACC_MASTER_NAME');
                $this->db->where('ACC_MASTER_CODE', $f);
                $fees_name = $this->db->get('acc_master')->row();
                if (!empty($fees_name->ACC_MASTER_NAME)) {
                    $fees_remain_arr_with_key[$fees_name->ACC_MASTER_NAME] = $f;
                }
            }
            $get_auto_index = $this->db->query("SHOW TABLE STATUS LIKE 'fees_yearly_hdr'")->row()->Auto_increment;
            $string = 'RY' . date("y") . $get_auto_index . date("md");
            $rcpt_no = substr($string, 0, 15);

            $this->session->set_userdata('all_fees_yearly', $result_all_fees);

            $data['all_fees'] = $result_all_fees;
            $data['total_fees'] = $total_fees;
            $data['fees_remain_arr'] = $fees_remain_arr_with_key;
            $data['rcpt_no'] = $rcpt_no;
            $data['std_id'] = $std_id;
            $data['class_id'] = $row->STD_CS_SEQ;
            $data['form_type'] = 'yearly_fees';
            //temporary code
            $this->db->select('FM_HDR_COL_DATE');
            $this->db->order_by('FM_HDR_SRLNO', 'DESC');
            $this->db->limit('1');
            $row_date = $this->db->get('fees_yearly_hdr')->row();
            /*if (count((array)$row_date) > 0) $date = date('Y-m-d', strtotime($row_date->FM_HDR_COL_DATE)); else $date = date('Y-m-d');
            $data['date'] = $date;*/

            if ($this->session->has_userdata('sessoin_collect_date')) {
                $date = $this->session->userdata('sessoin_collect_date');
            }else{
                if (count((array)$row_date) > 0) {
                 $date = date('Y-m-d', strtotime($row_date->FM_HDR_COL_DATE));
                } else {
                 $date = date('Y-m-d');
                }
            }
            if ($this->session->has_userdata('session_collect_bank')) {
                $bank_nm = $this->session->userdata('session_collect_bank');
            }else{
                if (count((array)$row_date) > 0) {
                 $bank_nm = $row_date->FM_HDR_B_NAME;
                }
            }
            if ($this->session->has_userdata('session_collect_payment_type')) {
                $payment_type = $this->session->userdata('session_collect_payment_type');
            }else{
                if (count((array)$row_date) > 0) {
                    $payment_type = $row_date->FM_HDR_P_TYP;
                }
            }
            $data['date'] = $date;
            $data['bank_nm'] = $bank_nm;
            $data['payment_type'] = $payment_type;

            $data['tab_title'] = 'Add Yearly Fees';
            $data['section_heading'] = '<h4>Student Name: <strong>' . $row->STD_FNAME . ' ' . $row->STD_MNAME . ' ' . $row->STD_LNAME . '</strong><br> Reg. No: <strong>' . $row->STD_REGNO . '</strong><br> Class & Sec: <strong>' . $row->Class_Name . ' - ' . $row->Sec_Name . '</strong><br> Roll No: <strong>' . $row->STD_ROLLNO . '</strong></h4>';
            $data['menu_name'] = 'Add Yearly Fees';
            if($this->session->usertype == "4"){
               return array('type' => 'load_view', 'page' => 'fees_v_student', 'data' => $data); 
            }else{
               return array('type' => 'load_view', 'page' => 'fees_v', 'data' => $data); 
            }
            
        }
    }

    public function form_add_yearly_fees(){
        if ($this->input->post('submit') == 'submit_yearly_fees') { //if form submitted
            $all_fees = $this->session->userdata('all_fees_yearly');
            $net_fees = $this->session->userdata('net_fees_yearly');
            $edit_fee = $this->input->post('edit_fee');
            $std_id = $this->input->post('std_id');
            $class_id = $this->input->post('class_id');
            $date = date('Y-m-d', strtotime($this->input->post('date')));
            $payment_type = $this->input->post('payment_type');
            $bank_name = $this->input->post('bank_name');
            $card_no = $this->input->post('card_no');
            $encash_date = $this->input->post('encash_date');
            $late_fine = $this->input->post('late_fine');
            $concession_fine = $this->input->post('concession_fine');
            $due_amount = $this->input->post('due_amount');
            $checkbox = $this->input->post('checkbox');

            $this->session->set_userdata('sessoin_collect_date', $date);
            $this->session->set_userdata('session_collect_bank', $bank_name);
            $this->session->set_userdata('session_collect_payment_type', $payment_type);

            $get_auto_index = $this->db->query("SHOW TABLE STATUS LIKE 'fees_yearly_hdr'")->row()->Auto_increment;
            $string = 'RY' . date("y") . $get_auto_index . date("md");
            $rcpt_no = substr($string, 0, 15);
            $fin_year = $this->db->get('company')->row()->COM_FIN_YEAR;

            $data_insert_hdr['FM_HDR_RCPT_NO'] = $rcpt_no;
            $data_insert_hdr['FM_HDR_STD_SEQ'] = $std_id;
            $data_insert_hdr['FM_HDR_STD_CS_SEQ'] = $class_id;
            $data_insert_hdr['FM_HDR_P_TYP'] = $payment_type;
            $data_insert_hdr['FM_HDR_B_NAME'] = $bank_name;
            $data_insert_hdr['FM_HDR_CARD_NO'] = $card_no;
            $data_insert_hdr['encash_date'] = $encash_date;
            $data_insert_hdr['FM_HDR_LATE_FEES'] = $late_fine;
            $data_insert_hdr['FM_HDR_CONC_FEES'] = $concession_fine;
            $data_insert_hdr['FM_HDR_TOT_FEES'] = $net_fees;
            $data_insert_hdr['FM_HDR_FIN_YEAR'] = $fin_year;
            $data_insert_hdr['FM_HDR_COL_DATE'] = $date;
            $data_insert_hdr['due_amount'] = $due_amount;
            //inserting data
            $this->db->insert('fees_yearly_hdr', $data_insert_hdr);

            foreach ($all_fees as $fee) {
                if (in_array($fee['ACC_MASTER_CODE'], $checkbox)) {
                    $data_insert_dtl['FEES_DTL_HDR_SRLNO'] = $get_auto_index;
                    $data_insert_dtl['FEES_DTL_STD_SEQ'] = $std_id;
                    $data_insert_dtl['FEES_DTL_STD_CS_SEC'] = $class_id;
                    $data_insert_dtl['FEES_DTL_ACC_SEQ'] = $fee['ACC_MASTER_CODE'];
//                    $data_insert_dtl['FEES_DTL_AMOUNT'] = $fee['Fees'];
                    $data_insert_dtl['FEES_DTL_AMOUNT'] = $edit_fee[$fee['ACC_MASTER_CODE']];
                    $data_insert_dtl['FEES_DTL_FIN_YEAR'] = $fin_year;
                    $data_insert_dtl['FEES_DTL_COL_DATE'] = $date;
                    //inserting data
                    $this->db->insert('fees_yearly_dtl', $data_insert_dtl);
                }
            }

            $this->session->unset_userdata(array('all_fees_yearly', 'net_fees_yearly'));

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Yearly fees payment successful.');
//            return array('page'=>'admin/yearly_fees_report');
            $student = $this->db->get_where('student_details', array('STD_SEQ'=>$std_id))->row();
            $last_student_name = $student->STD_FNAME.' '.$student->STD_MNAME.' '.$student->STD_LNAME.' - '.$student->STD_REGNO;
            return array('page' => 'admin/add_yearly_fees?print=print_yearly_fess&val=' . $get_auto_index.'&last_student_name='.$last_student_name);

        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page' => 'admin/yearly_fees_report');
        }
    }

    public function form_add_yearly_fees_due(){
        if ($this->input->post('submit') == 'submit_yearly_fees_due') { //if form submitted
            $due_amount = $this->input->post('due_amount');
            $hdr_id = $this->input->post('hdr_id');

            $data_update_hdr['due_amount'] = $due_amount;
            //inserting data
            $this->db->where('FM_HDR_SRLNO', $hdr_id);
            $this->db->update('fees_yearly_hdr', $data_update_hdr);

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Yearly due fees payment successful.');
            return array('page' => 'admin/add_yearly_fees');
        }
        else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page' => 'admin/yearly_fees_report');
        }
    }

    public function ajax_net_fee_yearly()
    {
        $late_fine = $this->input->post('late_fine');
        $concession_fine_yearly = $this->input->post('concession_fine_yearly');
        $fees_id_arr = $this->input->post('fees_id_arr');
        $edited_fee_total = $this->input->post('edited_fee_total');
        $all_fees = $this->session->userdata('all_fees_yearly');
//        $total_fees = 0.00;
        $total_fees = $edited_fee_total;

//        if($fees_id_arr) {
//            foreach ($all_fees as $arr) {
//                if (in_array($arr['ACC_MASTER_CODE'], $fees_id_arr)) {
//                    $total_fees += $arr['Fees'];
//                }
//            }
//        }

        $array['total_fees'] = $total_fees;
        $array['net_fees'] = $total_fees + $late_fine - $concession_fine_yearly;
        $array['paying_id'] = $fees_id_arr;
        $this->session->set_userdata('net_fees_yearly', $array['net_fees']);
        return $array;
    }

    public function ajax_net_fee_adm()
    {
        $late_fine = $this->input->post('late_fine_adm');
        $concession_fine = $this->input->post('concession_fine_adm');
        $fees_id_arr = $this->input->post('fees_id_arr');
        $edited_fee_total = $this->input->post('edited_fee_total');
        $all_fees = $this->session->userdata('all_fees_adm');
//        $total_fees = 0.00;
        $total_fees = $edited_fee_total;

//        if($fees_id_arr) {
//            foreach ($all_fees as $arr) {
//                if (in_array($arr['ACC_MASTER_CODE'], $fees_id_arr)) {
//                    $total_fees += $arr['Fees'];
//                }
//            }
//        }

        $array['total_fees'] = $total_fees;
        $array['net_fees'] = $total_fees + $late_fine - $concession_fine;
        $array['paying_id'] = $fees_id_arr;
        $this->session->set_userdata('net_fees_adm', $array['net_fees']);
        return $array;
    }

    public function ajax_net_fee_monthly()
    {
        $late_fine = $this->input->post('late_fine');
        $concession_fine = $this->input->post('concession_fine');
        $fees_id_arr = $this->input->post('fees_id_arr');
        $modify_tution_fee = $this->input->post('modify_tution_fee');


        $all_fees = $this->session->userdata('all_fees');
        $total_fees = 0.00;

        if ($fees_id_arr) {
            foreach ($all_fees as $arr) {
                if (in_array($arr['ACC_MASTER_CODE'], $fees_id_arr)) {
                    $total_fees += $arr['Fees'];
                }
            }
        }

        $array['total_fees'] = $total_fees;
        $array['net_fees'] = $total_fees + $late_fine - $concession_fine - $modify_tution_fee;
        $array['paying_id'] = $fees_id_arr;
        $this->session->set_userdata('net_fees', $array['net_fees']);
        return $array;
    }

    public function new_admission_fees($hdr_id)
    {
        try {
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Fees/new_admission_fees'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('New Admission Fees');
            $crud->order_by('STD_CS_SEQ', 'ASC');
            $crud->set_table('student_details');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $crud->columns('STD_CS_SEQ', 'STD_ROLLNO', 'STD_REGNO', 'STD_SRLNO', 'STD_FNAME', 'STD_MNAME', 'STD_LNAME');
            $crud->display_as('STD_CS_SEQ', 'Class & Section Name');
            $crud->display_as('STD_ROLLNO', 'Roll No');
            $crud->display_as('STD_REGNO', 'Reg. No');
            $crud->display_as('STD_SRLNO', 'Adm. No');
            $crud->display_as('STD_FNAME', 'First Name');
            $crud->display_as('STD_MNAME', 'Middle Name');
            $crud->display_as('STD_LNAME', 'Last Name');

            $crud->set_relation('STD_CS_SEQ', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');

            $crud->add_action('Proceed', base_url() . 'assets/grocery_crud/themes/flexigrid/css/images/next.gif', 'admin/add_new_admission_fees');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'New Admission Fees';
            $output->section_heading = 'New Admission Fees <small>(Add)</small>';
            $output->menu_name = 'New Admission Fees';
            $output->add_button = '';
            if (isset($hdr_id)) {
                $output->print = 'admin/print_new_admission_fess/' . $hdr_id;
            }

            return array('page' => 'common_v', 'data' => $output); //loading common view page
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function add_new_admission_fees($std_id)
    {
        $data['user_type'] = $this->session->usertype;
        $data['url_param'] = 'admin/add_new_admission_fees/';
        if ($std_id == '') {
            $data['tab_title'] = 'Add New Admission Fees';
            $data['menu_name'] = 'Add New Admission Fees';
            return array('type' => 'load_view', 'page' => 'fees_v', 'data' => $data);
        }

        $this->db->where('STD_SEQ', $std_id);
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $row = $this->db->get('student_details')->row();
        if (count((array)$row) == 0) { //if student not exists in student table
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oops!');
            $this->session->set_flashdata('msg', 'Student does not exists.');
            return array('type' => 'redirect', 'page' => 'admin/new_admission_fees_report');
        } else {
            $year = $this->db->get('company')->row()->COM_FIN_YEAR;
            $this->db->select('FEES_DTL_ACC_SEQ');
            $this->db->where('FEES_DTL_STD_SEQ', $std_id);
            $this->db->where('FEES_DTL_STD_CS_SEC', $row->STD_CS_SEQ);
            $this->db->where('FEES_DTL_FIN_YEAR', $year);
            $result = $this->db->get('fees_newadm_dtl')->result_array();
            //adding yearly fees
            /*if ($row->STD_CONSC == 1) { //if student granted for concession
                $this->db->where('std_id', $std_id);
                $this->db->where('class_id', $row->STD_CS_SEQ);
                $this->db->where('CS_FEES_TYPE', '2');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = acc_master.ACC_MASTER_CODE', 'left');
                $this->db->group_by('fees_concession.fc_id');
                $result_all_fees = $this->db->get('fees_concession')->result_array();
                if (count($result_all_fees) == 0) { //if concession fees not added yet
                    if ($this->session->usertype == 1) {
                        $this->session->set_flashdata('type', 'error');
                        $this->session->set_flashdata('title', 'Stop!');
                        $this->session->set_flashdata('msg', 'Add Concession fees first.');
                        return array('type' => 'redirect', 'page' => "admin/add_concession_fees/$std_id/newadms");
                    } else {
                        $this->session->set_flashdata('type', 'error');
                        $this->session->set_flashdata('title', 'Stop!');
                        $this->session->set_flashdata('msg', 'Concession fees for that student is not added yet.');
                        return array('type' => 'redirect', 'page' => 'admin/new_admission_fees_report');
                    }
                }
            } else { //if student is not granted for concession*/
                $this->db->where('CS_SEQ', $row->STD_CS_SEQ);
                $this->db->where('CS_FEES_TYPE', '2'); //New admission fees
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                $result_all_fees = $this->db->get('class_sec_dtl')->result_array();

                //echo count($result_all_fees);  die();
                if (count($result_all_fees) == 0) { //if fees not added yet
                    $this->session->set_flashdata('type', 'error');
                    $this->session->set_flashdata('title', 'Stop!');
                    $this->session->set_flashdata('msg', 'Fees for that class is not added yet.');
                    return array('type' => 'redirect', 'page' => 'admin/new_admission_fees_report');
                }
            /*}*/
            //if student paid all new admission fees for his class
            if (count($result) >= count($result_all_fees)) {
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'That student paid all new admission fees, no fees remain.');
                return array('type' => 'redirect', 'page' => 'admin/new_admission_fees_report');
            }
            //check if all yearly fees paid or not
            $year = $this->db->get('company')->row()->COM_FIN_YEAR;
            $this->db->select('FEES_DTL_ACC_SEQ');
            $this->db->where('FEES_DTL_STD_SEQ', $std_id);
            $this->db->where('FEES_DTL_STD_CS_SEC', $row->STD_CS_SEQ);
            $this->db->where('FEES_DTL_FIN_YEAR', $year);
            $result_paid_yearly_fee = $this->db->get('fees_yearly_dtl')->result_array();
            /*if ($row->STD_CONSC == 1) { //if student granted for concession
                $this->db->select('fees_concession.*, acc_master.*');
                $this->db->where('std_id', $std_id);
                $this->db->where('class_id', $row->STD_CS_SEQ);
                $this->db->where('CS_FEES_TYPE', '1');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = acc_master.ACC_MASTER_CODE', 'left');
                $this->db->group_by('fees_concession.fc_id');
                $result_all_yearly_fee = $this->db->get('fees_concession')->result_array();
            } else { //if student is not granted for concession*/
                $this->db->where('CS_SEQ', $row->STD_CS_SEQ);
                $this->db->where('CS_FEES_TYPE', '1'); //yearly fees
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                $result_all_yearly_fee = $this->db->get('class_sec_dtl')->result_array();
            /*}*/
            if (count($result_paid_yearly_fee) >= count($result_all_yearly_fee)) {
                $data['all_yearly_fees_paid'] = 'yes';
            } else {
                $data['all_yearly_fees_paid'] = 'no';
            }
            $total_fees = array_sum(array_map(function ($value) {
                return $value['Fees'];
            }, $result_all_fees));
            $all_fees_arr = array_map(function ($value) {
                return $value['ACC_MASTER_CODE'];
            }, $result_all_fees); //array of all new admission fees id
            $fees_paid_arr = array_map(function ($value) {
                return $value['FEES_DTL_ACC_SEQ'];
            }, $result); //array of fees id for which payment is done
            $fees_remain_arr = array_diff($all_fees_arr, $fees_paid_arr); //array of fees id for which payment is not done yet
            //creating array of remaining fees id with fees name as key, like: Array( [EXAMINATION FEES] => 8 [ELECTRICITY FEES] => 7 )
            foreach ($fees_remain_arr as $f) {
                $this->db->select('ACC_MASTER_NAME');
                $this->db->where('ACC_MASTER_CODE', $f);
                $fees_name = $this->db->get('acc_master')->row();
                $fees_remain_arr_with_key[$fees_name->ACC_MASTER_NAME] = $f;
            }

            $get_auto_index = $this->db->query("SHOW TABLE STATUS LIKE 'fees_newadm_hdr'")->row()->Auto_increment;
            $string = 'RN' . date("y") . $get_auto_index . date("md");
            $rcpt_no = substr($string, 0, 15);

            $this->session->set_userdata('all_fees_adm', $result_all_fees);

            $data['all_fees'] = $result_all_fees;
            $data['total_fees'] = $total_fees;
            $data['fees_remain_arr'] = $fees_remain_arr_with_key;
            $data['rcpt_no'] = $rcpt_no;
            $data['std_id'] = $std_id;
            $data['class_id'] = $row->STD_CS_SEQ;
            $data['form_type'] = 'new_admission_fees';
            //temporary code
            $this->db->select('FM_HDR_COL_DATE');
            $this->db->order_by('FM_HDR_SRLNO', 'DESC');
            $this->db->limit('1');
            $row_date = $this->db->get('fees_newadm_hdr')->row();
            if (count((array)$row_date) > 0) $date = date('Y-m-d', strtotime($row_date->FM_HDR_COL_DATE)); else $date = date('Y-m-d');
            $data['date'] = $date;

            if ($this->session->has_userdata('sessoin_collect_date')) {
                    $date = $this->session->userdata('sessoin_collect_date');
            }else{
                if (count((array)$row_date) > 0) {
                 $date = date('Y-m-d', strtotime($row_date->FM_HDR_COL_DATE));
                } else {
                 $date = date('Y-m-d');
                }
            }
            if ($this->session->has_userdata('session_collect_bank')) {
                $bank_nm = $this->session->userdata('session_collect_bank');
            }else{
                if (count((array)$row_date) > 0) {
                 $bank_nm = $row_date->FM_HDR_B_NAME;
                }
            }
            if ($this->session->has_userdata('session_collect_payment_type')) {
                $payment_type = $this->session->userdata('session_collect_payment_type');
            }else{
                if (count((array)$row_date) > 0) {
                    $payment_type = $row_date->FM_HDR_P_TYP;
                }
            }
            $data['date'] = $date;
            $data['bank_nm'] = $bank_nm;
            $data['payment_type'] = $payment_type;

            $data['tab_title'] = 'Add New Admission Fees';
            $data['section_heading'] = '<h4>Student Name: <strong>' . $row->STD_FNAME . ' ' . $row->STD_MNAME . ' ' . $row->STD_LNAME . '</strong><br> Reg. No: <strong>' . $row->STD_REGNO . '</strong><br> Class & Sec: <strong>' . $row->Class_Name . ' - ' . $row->Sec_Name . '</strong><br> Roll No: <strong>' . $row->STD_ROLLNO . '</strong></h4>';
            $data['menu_name'] = 'Add New Admission Fees';

            return array('type' => 'load_view', 'page' => 'fees_v', 'data' => $data);
        }
    }

    public function form_add_new_admission_fees() {
        // echo "<pre>"; print_r($this->input->post()); die();
        if ($this->input->post('submit') == 'submit_new_admission_fees') { //if form submitted
            $all_fees = $this->session->userdata('all_fees_adm');
            $net_fees = $this->session->userdata('net_fees_adm');
            $edit_fee = $this->input->post('edit_fee_adm');
            $std_id = $this->input->post('std_id');
            $class_id = $this->input->post('class_id');
            $date = date('Y-m-d', strtotime($this->input->post('date')));
            $payment_type = $this->input->post('payment_type');
            $bank_name = $this->input->post('bank_name');
            $card_no = $this->input->post('card_no');
            $encash_date = $this->input->post('encash_date');
            $late_fine = $this->input->post('late_fine');
            $concession_fine = $this->input->post('concession_fine');
            $checkbox = $this->input->post('checkbox');

            $this->session->set_userdata('sessoin_collect_date', $date);
            $this->session->set_userdata('session_collect_bank', $bank_name);
            $this->session->set_userdata('session_collect_payment_type', $payment_type);

            $get_auto_index = $this->db->query("SHOW TABLE STATUS LIKE 'fees_newadm_hdr'")->row()->Auto_increment;
            $string = 'RN' . date("y") . $get_auto_index . date("md");
            $rcpt_no = substr($string, 0, 15);
            $fin_year = $this->db->get('company')->row()->COM_FIN_YEAR;

            $data_insert_hdr['FM_HDR_RCPT_NO'] = $rcpt_no;
            $data_insert_hdr['FM_HDR_STD_SEQ'] = $std_id;
            $data_insert_hdr['FM_HDR_STD_CS_SEQ'] = $class_id;
            $data_insert_hdr['FM_HDR_P_TYP'] = $payment_type;
            $data_insert_hdr['FM_HDR_B_NAME'] = $bank_name;
            $data_insert_hdr['FM_HDR_CARD_NO'] = $card_no;
            $data_insert_hdr['encash_date'] = $encash_date;
            $data_insert_hdr['FM_HDR_LATE_FEES'] = $late_fine;
            $data_insert_hdr['FM_HDR_CONC_FEES'] = $concession_fine;
            $data_insert_hdr['FM_HDR_TOT_FEES'] = $net_fees;
            $data_insert_hdr['FM_HDR_FIN_YEAR'] = $fin_year;
            $data_insert_hdr['FM_HDR_COL_DATE'] = $date;
            //inserting data
            $this->db->insert('fees_newadm_hdr', $data_insert_hdr);

            foreach ($all_fees as $fee) {
                if (in_array($fee['ACC_MASTER_CODE'], $checkbox)) {
                    $data_insert_dtl['FEES_DTL_HDR_SRLNO'] = $get_auto_index;
                    $data_insert_dtl['FEES_DTL_STD_SEQ'] = $std_id;
                    $data_insert_dtl['FEES_DTL_STD_CS_SEC'] = $class_id;
                    $data_insert_dtl['FEES_DTL_ACC_SEQ'] = $fee['ACC_MASTER_CODE'];
//                    $data_insert_dtl['FEES_DTL_AMOUNT'] = $fee['Fees'];
                    $data_insert_dtl['FEES_DTL_AMOUNT'] = $edit_fee[$fee['ACC_MASTER_CODE']];
                    $data_insert_dtl['FEES_DTL_FIN_YEAR'] = $fin_year;
                    $data_insert_dtl['FEES_DTL_COL_DATE'] = $date;
                    //inserting data
                    $this->db->insert('fees_newadm_dtl', $data_insert_dtl);
                }
            }

            $this->session->unset_userdata(array('all_fees_adm', 'net_fees_adm'));

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'New admission fees payment successful.');
//            return array('page'=>'admin/new_admission_fees_report');

            $student = $this->db->get_where('student_details', array('STD_SEQ'=>$std_id))->row();
            $last_student_name = $student->STD_FNAME.' '.$student->STD_MNAME.' '.$student->STD_LNAME.' - '.$student->STD_REGNO;
            return array('page' => 'admin/add_new_admission_fees?print=print_new_admission_fess&val=' . $get_auto_index.'&last_student_name='.$last_student_name);
        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page' => 'admin/new_admission_fees_report');
        }
    }

} // /.Fees_m model