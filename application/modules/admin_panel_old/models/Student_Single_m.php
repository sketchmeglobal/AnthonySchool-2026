<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 13-02-2019
 * Time: 12:09
 */

class Student_Single_m extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    private function company_name($class_school_id, $type=NULL)
    {
        $company = '';

        // $type => 'class', 'school'
        // 1=Nursery, 2=Primary, 3=Secondary, 4=Higher Secondary, 5=Default
        if ($type == 'school') {
            if (in_array('all', $class_school_id)) {
                $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>5))->row();
            }else{
                if (count($class_school_id) > 1) {
                    $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>5))->row();
                }else{
                    if (array_key_exists(0, $class_school_id)) {
                        $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>$class_school_id[0]))->row();
                    }
                }
            }

            return $company;
        }else{
            if(in_array("all", $class_school_id)){
                $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>5))->row();
            }else{
                if(@count($class_school_id) == 1){
                    $this->db->where_in('CS_SEQ', $class_school_id);
                    $class_type = $this->db->get('class_sec_hdr')->row();
                    $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>$class_type->Class_Type))->row();
                }else{
                    if(@count($class_school_id) > 1){
                        $this->db->where_in('CS_SEQ', $class_school_id);
                        $this->db->group_by("Class_Type");
                        $class_type = $this->db->get('class_sec_hdr')->result();
                        // echo "<pre>"; print_r($class_type); die();
                        if(count($class_type) > 1){
                            $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>5))->row();
                        }else{
                            $this->db->where_in('CS_SEQ', $class_school_id);
                            $class_type = $this->db->get('class_sec_hdr')->row();
                            $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>$class_type->Class_Type))->row();
                        }
                    }else{
                        $this->db->where_in('CS_SEQ', $class_school_id);
                        $class_type = $this->db->get('class_sec_hdr')->row();
                        $company    = $this->db->get_where('company', array('SCHOOL_TYPE'=>$class_type->Class_Type))->row();

                    }
                }
            }
            return $company;
        }

    }

    public function student_exam() {
        $data['section'] = 'student_exam';

        $data['tab_title'] = 'Examination';
        $data['menu_name'] = 'Examination';

        return array('type' => 'load_view', 'page' => 'student_single_v', 'data' => $data);
    }

    public function exam_answer_save() {
        $EXAM_SEQ = $this->input->post('exam_seq');
        $qs_id = $this->input->post('qs_id');
        $answers = $this->input->post('answers');
        $STD_SEQ = $this->session->tbl_id;

        //insert/update answers
        $this->db->where('EXAM_SEQ', $EXAM_SEQ);
        $this->db->where('qs_id', $qs_id);
        $this->db->where('STD_SEQ', $STD_SEQ);
        $ans_row = $this->db->get('exam_answers')->row();

        //insert
        if(count((array)$ans_row) == 0) {
            unset($data_insert);
            $data_insert['EXAM_SEQ'] = $EXAM_SEQ;
            $data_insert['qs_id'] = $qs_id;
            $data_insert['STD_SEQ'] = $STD_SEQ;
            $data_insert['answers'] = $answers;

            $this->db->insert('exam_answers', $data_insert);
        }
        //update
        else {
            unset($data_update);
            $data_update['answers'] = $answers;

            $this->db->where('ea_id', $ans_row->ea_id);
            $this->db->update('exam_answers', $data_update);
        }

        $data['type'] = 'success';
        $data['msg'] = 'Answers saved.';
        return $data;
    }

    public function mcq_exam_answer_save() {
        $EXAM_SEQ = $this->input->post('exam_seq');
        $mcq_qs_id = $this->input->post('mcq_qs_id');
        $ques_arr = $this->input->post('ques');
        $ans_arr = $this->input->post('ans');
        $STD_SEQ = $this->session->tbl_id;

        foreach($ques_arr as $ques_id) {
            //if answer given to this ques
            if(array_key_exists($ques_id, $ans_arr)) {
                $option_selected = $ans_arr[$ques_id];
                $marks = 0.00;

                //check if ans is correct
                $this->db->where('mcq_q_id', $ques_id);
                $this->db->where('mcq_qs_id', $mcq_qs_id);
                $ques_row = $this->db->get('mcq_questions')->row();
                if($option_selected == $ques_row->answer) {
                    $marks = $ques_row->marks;
                }

                //insert/update answers
                $this->db->where('EXAM_SEQ', $EXAM_SEQ);
                $this->db->where('mcq_qs_id', $mcq_qs_id);
                $this->db->where('mcq_q_id', $ques_id);
                $this->db->where('STD_SEQ', $STD_SEQ);
                $ans_row = $this->db->get('mcq_exam_answers')->row();

                //insert
                if(count((array)$ans_row) == 0) {
                    unset($data_insert);
                    $data_insert['EXAM_SEQ'] = $EXAM_SEQ;
                    $data_insert['mcq_qs_id'] = $mcq_qs_id;
                    $data_insert['mcq_q_id'] = $ques_id;
                    $data_insert['STD_SEQ'] = $STD_SEQ;
                    $data_insert['option_selected'] = $option_selected;
                    $data_insert['marks'] = $marks;

                    $this->db->insert('mcq_exam_answers', $data_insert);
                }
                //update
                else {
                    unset($data_update);
                    $data_update['option_selected'] = $option_selected;
                    $data_update['marks'] = $marks;

                    $this->db->where('mcq_ea_id', $ans_row->mcq_ea_id);
                    $this->db->update('mcq_exam_answers', $data_update);
                }
            }
        }

        $data['type'] = 'success';
        $data['msg'] = 'Answers saved.';
        return $data;
    }

    public function student_homework() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Student_Single/student_homework'));
            $crud->set_theme('datatables');
            $crud->set_subject('Home Work');
            $class_id = $this->db->get_where('student_details', array('STD_SEQ' => $this->session->tbl_id))->row()->STD_CS_SEQ;
            $crud->where('class_id', $class_id);
            $crud->where('release_date <=', date('Y-m-d'));
            $crud->set_table('homeworks');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_read();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $crud->columns('release_date','sub_id','homework');

            $crud->display_as('class_id','Class & Section');
            $crud->display_as('sub_id','Subject Name');
            $crud->display_as('release_date','Date');
            $crud->display_as('homework','Home Work');
            $crud->display_as('doc','Document');
            $crud->display_as('ans_date','Answer Release Date');
            $crud->display_as('answer','Answer');
            $crud->display_as('ans_doc','Answer Document');

            $crud->set_field_upload('doc','assets/admin_panel/homework_files');
            $crud->set_field_upload('ans_doc','assets/admin_panel/homework_files');

            $crud->set_relation('class_id', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');
            $crud->set_relation('sub_id', 'subject', 'sub_name');

            $crud->add_action('View', '', 'admin/student_homework_details', 'ui-icon-document');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Home Work';
            $output->section_heading = 'Home Work <small>(Read)</small>';
            $output->menu_name = 'Home Work';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function student_homework_details($hw_id) {
        $this->db->join('subject', 'subject.sub_id = homeworks.sub_id', 'left');
        $this->db->where('hw_id', $hw_id);
        $row_hw = $this->db->get('homeworks')->row();
        //if homework not found || homework not belongs to my class || if homework publish date not came yet
        $my_class_id = $this->db->get_where('student_details', array('STD_SEQ' => $this->session->tbl_id))->row()->STD_CS_SEQ;
        $publish_date = date('Y-m-d', strtotime($row_hw->release_date));
        $today = date('Y-m-d');
        if(count((array)$row_hw) == 0 || $my_class_id != $row_hw->class_id || $publish_date > $today) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'No content found.');
            return array('page'=>'admin/student_homework');
        }

        $data['homework'] = $row_hw;
        $data['section'] = 'student_homework_details';

        $data['tab_title'] = 'Home Work Details';
        $data['menu_name'] = 'Home Work Details';

        return array('type' => 'load_view', 'page' => 'student_single_v', 'data' => $data);
    }

    public function student_library() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Student_Single/student_library'));
            $crud->set_theme('datatables');
            $crud->set_subject('Books');
            $crud->set_table('book_master');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();
            $crud->unset_read();

            $crud->columns('Accession_No','sub_id','Book_Name','Author','Availability');

            $crud->display_as('sub_id', 'Subject');

            $crud->set_relation('sub_id', 'subject', 'sub_name');

            $crud->callback_column('Availability', array($this, '_callback_Availability'));

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Books';
            $output->section_heading = 'Books';
            $output->menu_name = 'Books';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    public function _callback_Availability($value, $row) {
        if($row->Total_Copies > 0) {
            $avl = "Available";
        } else {
            $avl = "Not Available";
        }

        $today = date('Y-m-d');
        $this->db->select('COUNT(lb_dtl_id) as total');
        $this->db->where('date_return >', $today);
        $this->db->where('BOOK_SEQ', $row->BOOK_SEQ);
        $this->db->or_where('date_return', '0000-00-00');
        $this->db->where('BOOK_SEQ', $row->BOOK_SEQ);
        $rs = $this->db->get("library_dtl")->row();
        if(count((array)$rs) > 0){
            $in_stock = $row->Total_Copies - $rs->total;
            if($in_stock > 0){
                $avl = "Available";
            } else {
                $avl = "Not Available";
            }
        }

        return $avl;
    }

    public function my_routine() {
        $class = $this->db->get_where('student_details', array('STD_SEQ' => $this->session->tbl_id))->row()->STD_CS_SEQ;

        $company = $this->company_name((array)$class);
        $this->db->where('CS_SEQ', $class);
        $cls = $this->db->get('class_sec_hdr')->row();

        $this->db->select('sub_s_name,TCH_NAME');
        $this->db->join('subject', 'subject.sub_id = routine.sub_id', 'left');
        $this->db->join('teacher', 'teacher.TCH_SRLNO = routine.tch_id', 'left');
        $this->db->where('class_id', $class);
        $routine = $this->db->get('routine')->result_array();

        //if no routine found for that class
        if(count($routine) == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'Routine not created for that class.');
            return array('page'=>'admin/dashboard');
        }

        //-------------------------------------------------------------------------------------------------------

        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $doc_name = 'Routine of Class '.$cls->Class_Name.' - '.$cls->Sec_Name;
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company->COM_NAME);
        $pdf->SetTitle($doc_name);
        $pdf->SetSubject($doc_name);
        $pdf->SetKeywords($doc_name.', smg, developed by: www.fb.com/pran93');

        // set default header data
        $html_header = <<<EOD
    <div style="text-align:center;">
    <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
    <br>
    $company->COM_ADD2 , $company->COM_CITY
    <br>
    <strong>Routine of Class: <span style="background-color: black;color: white;"> $cls->Class_Name - $cls->Sec_Name </span></strong>
    <hr align="left">
    </div>
EOD;
        $pdf->setHtmlHeader($html_header, false);

        // set header and footer fonts and size
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(10, 20, 10);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // -----------------------------------------------------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        $pdf->SetFont('times', '', 11, '', true);

        // Add a page
        $pdf->AddPage('L', 'A4');

        // Set some content to print
        $html = <<<EOD
            <table cellspacing="" border="1" align="Center">
                <thead>
                <tr>
                    <th height="25"><strong>Day</strong></th>
                    <th><strong>1st Period</strong></th>
                    <th><strong>2nd Period</strong></th>
                    <th><strong>3rd Period</strong></th>
                    <th><strong>4th Period</strong></th>
                    <th width="5%"><strong>Brk.</strong></th>
                    <th><strong>5th Period</strong></th>
                    <th><strong>6th Period</strong></th>
                    <th><strong>7th Period</strong></th>
                    <th><strong>8th Period</strong></th>
                </tr>
                </thead>
                <tbody>
EOD;

        $days = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
        $index = 0;

        //days loop
        $day_counter = 1;
        foreach ($days as $v) {
            $html .= '<tr>
            <td height="75"><strong><br/>Day '.$day_counter++.'</strong></td>';
            //periods loop
            for($i=0; $i<8; $i++) {
                $html .= <<<EOD
                    <td>
                        {$routine[$index]['sub_s_name']}
                        <br>
                        {$routine[$index]['TCH_NAME']}
                    </td>
EOD;
                if ($i == 3) {
                    $html .= <<<EOD
                        <td width="5%"><br/><br/>•</td>
EOD;
                }
                $index++;
            }
            $html .= "</tr>";
        }
        $html .= <<<EOD
                </tbody>
            </table>
EOD;

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document
        $pdf->Output($doc_name . '.pdf', 'I');
    }

    public function my_details() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Student_Single/my_details'));
            $crud->set_theme('datatables');
            $crud->set_subject('My Details');
            $crud->where('STD_SEQ', $this->session->tbl_id);
            $crud->set_table('student_details');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_add();
            $crud->unset_delete();
            $crud->unset_clone();

            $crud->columns('STD_CS_SEQ','STD_ROLLNO','STD_REGNO','STD_FNAME','STD_MNAME','STD_LNAME','STD_IMAGE_PATH');
            $crud->required_fields('STD_STATE','STD_BLDGRP','STD_RLGN','STD_CAT');
            $crud->unset_fields('STD_CS_SEQ','STD_REGNO','STD_ROLLNO','STD_FNAME','STD_MNAME','STD_LNAME','space','STD_SEX','STD_EMAIL','STD_CONSC','STD_SUB67','STD_LAST_CLASS_NEW','STD_TC_NO','STD_TC_DT','STD_DT_LV','STD_RSN_LEAVE','STD_PRM','STD_UID1','STD_BY1','STD_UID2','STD_BY2','STD_LEFT','STD_PROMOTED');

            $crud->display_as('STD_CS_SEQ', 'Class & Sec');
            $crud->display_as('STD_FNAME', 'First Name');
            $crud->display_as('STD_MNAME', 'Middle Name');
            $crud->display_as('STD_LNAME', 'Last Name');
            $crud->display_as('STD_REGNO', 'Registration No');
            $crud->display_as('STD_SRLNO', 'Serial No');
            $crud->display_as('STD_ROLLNO', 'Roll No');
            $crud->display_as('STD_DOA', 'Date of Admission');
            $crud->display_as('STD_EMAIL', 'Email Address');
            $crud->display_as('STD_PH_NO', 'Phone No');
            $crud->display_as('STD_SEX', 'Gender');
            $crud->display_as('STD_DOB', 'Date of Birth');
            $crud->display_as('STD_HOUSE', 'House');
            $crud->display_as('STD_STATE', 'State');
            $crud->display_as('STD_ADDR_0', 'Street No');
            $crud->display_as('STD_ADDR_1', 'Street Address');
            $crud->display_as('STD_ADDR_2', 'City / Village');
            $crud->display_as('STD_ADDR_3', 'District');
            $crud->display_as('STD_ADDR_4', 'Post Office');
            $crud->display_as('STD_ADDR_5', 'Pin Code');
            $crud->display_as('STD_MTNG', 'Mother Tongue');
            $crud->display_as('STD_2LANG', '2nd Language');
            $crud->display_as('STD_3LANG', '3rd Language');
            $crud->display_as('STD_CONSC', 'Allow Concession');
            $crud->display_as('STD_RLGN', 'Religion');
            $crud->display_as('STD_CAT', 'Caste');
            $crud->display_as('STD_RC', 'Roman Catholic?');
            $crud->display_as('STD_PHYDSBL', 'Physical Disability');
            $crud->display_as('STD_BLDGRP', 'Blood Group');
            $crud->display_as('STD_SUB67', '6th / 7th Subject');
            $crud->display_as('STD_LAST_SCHOOL', 'Previous School Name');
            $crud->display_as('STD_LAST_CLASS', 'Last Class Attended at Previous School');
            $crud->display_as('STD_OLD_TC_NO', 'Previous School TC No');
            $crud->display_as('STD_OLD_TC_DT', 'Previous School TC Date');
            $crud->display_as('STD_OLD_DT_LV', 'Previous School Leaving Date');
            $crud->display_as('STD_LAST_CLASS_NEW', 'Last Class Attended at Our School');
            $crud->display_as('STD_TC_NO', 'TC No');
            $crud->display_as('STD_TC_DT', 'TC Date');
            $crud->display_as('STD_DT_LV', 'Leaving Date');
            $crud->display_as('STD_RSN_LEAVE', 'Reason to Leave');
            $crud->display_as('STD_PRM', 'Promotion');
            $crud->display_as('STD_UID1', 'U-ID 1');
            $crud->display_as('STD_BY1', 'Board Year-1');
            $crud->display_as('STD_UID2', 'U-ID 2');
            $crud->display_as('STD_BY2', 'Board Year-2');
            $crud->display_as('STD_LEFT', 'Left from School?');
            $crud->display_as('STD_PROMOTED', 'Promoted?');
            $crud->display_as('STD_IMAGE_PATH', 'Student Photograph');

            $crud->set_relation('STD_CS_SEQ', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');
            $crud->set_relation('STD_HOUSE', 'house', 'name');
            $crud->set_relation('STD_STATE', 'states', 'name');
            $crud->set_relation('STD_RLGN', 'religion', 'name');

            $crud->field_type('STD_SEX', 'true_false', array('1' => 'Male', '0' => 'Female'));
            $crud->field_type('STD_CONSC', 'true_false', array('1' => 'Yes', '0' => 'No'));
            $crud->field_type('STD_RC', 'true_false', array('1' => 'Yes', '0' => 'No'));
            $crud->field_type('STD_PHYDSBL', 'true_false', array('1' => 'Yes', '0' => 'No'));
            $crud->field_type('STD_PRM', 'true_false', array('1' => 'Granted', '0' => 'Not Granted'));
            $crud->field_type('STD_LEFT', 'true_false', array('1' => 'Yes', '0' => 'No'));
            $crud->field_type('STD_PROMOTED', 'true_false', array('1' => 'Yes', '0' => 'No'));
            $crud->field_type('STD_CAT', 'dropdown', array('General'=>'General', 'SC'=>'SC', 'ST'=>'ST' , 'OBC'=>'OBC', 'Other'=>'Other'));
            $crud->field_type('STD_BLDGRP', 'dropdown', array('A+'=>'A+', 'A-'=>'A-', 'B+'=>'B+' , 'B-'=>'B-', 'AB+'=>'AB+', 'AB-'=>'AB-', 'O+'=>'O+', 'O-'=>'O-', 'Unknown'=>'Unknown'));
            $crud->field_type('STD_SRLNO', 'invisible');
            $crud->field_type('STD_LAST_MOD_DT', 'hidden', date("Y-m-d H:i:s"));
            $crud->field_type('STD_PH_NO', 'readonly');
            $crud->field_type('STD_DOB', 'readonly');
            $crud->field_type('STD_DOA', 'readonly');
            $crud->set_field_upload('STD_IMAGE_PATH','assets/img/students');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'My Details';
            $output->section_heading = 'My Details <small>(Edit)</small>';
            $output->menu_name = 'My Details';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function parent_details() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Student_Single/parent_details'));
            $crud->set_theme('datatables');
            $crud->set_subject('Parent Details');
            $crud->where('student_parent_details.STD_SEQ', $this->session->tbl_id);
            $crud->set_table('student_parent_details');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_delete();

            $crud->columns('STD_SEQ','STD_FTH_NAME','STD_MTH_NAME','STD_LG_NAME');
            $crud->unset_fields('STD_SEQ');

            $crud->display_as('STD_SEQ', 'Student Name - Reg. No');
            $crud->display_as('STD_FTH_NAME', 'Father Name');
            $crud->display_as('STD_FTH_PHNO', 'Father Phone No');
            $crud->display_as('STD_FTH_MOB', 'Father Mobile No');
            $crud->display_as('STD_FTH_EMAIL', 'Father Email');
            $crud->display_as('STD_FTH_QLF', 'Father Qualification');
            $crud->display_as('STD_FTH_DSGN', 'Father Designation');
            $crud->display_as('STD_FTH_OCP', 'Father Occupation');
            $crud->display_as('STD_FTH_SPOCP', 'Father Specific Occupation');
            $crud->display_as('STD_FTH_STATE', 'Father State');
            $crud->display_as('STD_FTH_ADDR_0', 'Father Street No');
            $crud->display_as('STD_FTH_ADDR_1', 'Father Street Address');
            $crud->display_as('STD_FTH_ADDR_2', 'Father City / Village');
            $crud->display_as('STD_FTH_ADDR_3', 'Father District');
            $crud->display_as('STD_FTH_ADDR_4', 'Father Post Office');
            $crud->display_as('STD_FTH_ADDR_5', 'Father Pin Code');
            $crud->display_as('STD_MTH_NAME', 'Mother Name');
            $crud->display_as('STD_MTH_PHNO', 'Mother Phone No');
            $crud->display_as('STD_MTH_MOB', 'Mother Mobile No');
            $crud->display_as('STD_MTH_EMAIL', 'Mother Email');
            $crud->display_as('STD_MTH_QLF', 'Mother Qualification');
            $crud->display_as('STD_MTH_DSGN', 'Mother Designation');
            $crud->display_as('STD_MTH_OCP', 'Mother Occupation');
            $crud->display_as('STD_MTH_SPOCP', 'Mother Specific Occupation');
            $crud->display_as('STD_MTH_STATE', 'Mother State');
            $crud->display_as('STD_MTH_ADDR_0', 'Mother Street No');
            $crud->display_as('STD_MTH_ADDR_1', 'Mother Street Address');
            $crud->display_as('STD_MTH_ADDR_2', 'Mother City / Village');
            $crud->display_as('STD_MTH_ADDR_3', 'Mother District');
            $crud->display_as('STD_MTH_ADDR_4', 'Mother Post Office');
            $crud->display_as('STD_MTH_ADDR_5', 'Mother Pin Code');
            $crud->display_as('STD_LG_NAME', 'Local Guardian Name');
            $crud->display_as('STD_LG_RLN', 'Local Guardian Relation');
            $crud->display_as('STD_LG_PHNO', 'Local Guardian Phone No');
            $crud->display_as('STD_LG_MOB', 'Local Guardian Mobile No');
            $crud->display_as('STD_LG_EMAIL', 'Local Guardian Email');
            $crud->display_as('STD_LG_QLF', 'Local Guardian Qualification');
            $crud->display_as('STD_LG_DSGN', 'Local Guardian Designation');
            $crud->display_as('STD_LG_OCP', 'Local Guardian Occupation');
            $crud->display_as('STD_LG_SPOCP', 'Local Guardian Specific Occupation');
            $crud->display_as('STD_LG_STATE', 'Local Guardian State');
            $crud->display_as('STD_LG_ADDR_0', 'Local Guardian Street No');
            $crud->display_as('STD_LG_ADDR_1', 'Local Guardian Street Address');
            $crud->display_as('STD_LG_ADDR_2', 'Local Guardian City / Village');
            $crud->display_as('STD_LG_ADDR_3', 'Local Guardian District');
            $crud->display_as('STD_LG_ADDR_4', 'Local Guardian Post Office');
            $crud->display_as('STD_LG_ADDR_5', 'Local Guardian Pin Code');

            $crud->set_relation('STD_SEQ', 'student_details', '{STD_FNAME}{space}{STD_MNAME}{space}{STD_LNAME}{space}-{space}{STD_REGNO}');
            $crud->set_relation('STD_FTH_STATE', 'states', 'name');
            $crud->set_relation('STD_MTH_STATE', 'states', 'name');
            $crud->set_relation('STD_LG_STATE', 'states', 'name');

            $crud->field_type('STD_FTH_QLF', 'dropdown', array('Graduate'=>'Graduate','Post Graduate'=>'Post Graduate','Under Graduate'=>'Under Graduate','Below 10th Standard'=>'Below 10th Standard','Other'=>'Other','Unknown'=>'Unknown'));
            $crud->field_type('STD_FTH_DSGN', 'dropdown', array('Engineer'=>'Engineer','Doctor'=>'Doctor','Commissioner'=>'Commissioner','Teacher'=>'Teacher','Businessman'=>'Businessman','Self Employed'=>'Self Employed','Clerk'=>'Clerk','Officer'=>'Officer','Supervisor'=>'Supervisor','General Staff'=>'General Staff','Other'=>'Other','Unknown'=>'Unknown'));
            $crud->field_type('STD_FTH_OCP', 'dropdown', array('Service'=>'Service','Business'=>'Business','Self Employed'=>'Self Employed','Govt. Service'=>'Govt. Service','Other'=>'Other','Unknown'=>'Unknown'));
            $crud->field_type('STD_FTH_SPOCP', 'dropdown', array('Railways'=>'Railways','Income Tax'=>'Income Tax','Manufacturer'=>'Manufacturer','Other'=>'Other','Unknown'=>'Unknown'));
            $crud->field_type('STD_MTH_QLF', 'dropdown', array('Graduate'=>'Graduate','Post Graduate'=>'Post Graduate','Under Graduate'=>'Under Graduate','Below 10th Standard'=>'Below 10th Standard','Other'=>'Other','Unknown'=>'Unknown'));
            $crud->field_type('STD_MTH_DSGN', 'dropdown', array('Engineer'=>'Engineer','Doctor'=>'Doctor','Commissioner'=>'Commissioner','Teacher'=>'Teacher','Businessman'=>'Businessman','Self Employed'=>'Self Employed','Clerk'=>'Clerk','Officer'=>'Officer','Supervisor'=>'Supervisor','General Staff'=>'General Staff','Other'=>'Other','Unknown'=>'Unknown'));
            $crud->field_type('STD_MTH_OCP', 'dropdown', array('Service'=>'Service','Business'=>'Business','Self Employed'=>'Self Employed','Govt. Service'=>'Govt. Service','Other'=>'Other','Unknown'=>'Unknown'));
            $crud->field_type('STD_MTH_SPOCP', 'dropdown', array('Railways'=>'Railways','Income Tax'=>'Income Tax','Manufacturer'=>'Manufacturer','Other'=>'Other','Unknown'=>'Unknown'));
            $crud->field_type('STD_LG_QLF', 'dropdown', array('Graduate'=>'Graduate','Post Graduate'=>'Post Graduate','Under Graduate'=>'Under Graduate','Below 10th Standard'=>'Below 10th Standard','Other'=>'Other','Unknown'=>'Unknown'));
            $crud->field_type('STD_LG_DSGN', 'dropdown', array('Engineer'=>'Engineer','Doctor'=>'Doctor','Commissioner'=>'Commissioner','Teacher'=>'Teacher','Businessman'=>'Businessman','Self Employed'=>'Self Employed','Clerk'=>'Clerk','Officer'=>'Officer','Supervisor'=>'Supervisor','General Staff'=>'General Staff','Other'=>'Other','Unknown'=>'Unknown'));
            $crud->field_type('STD_LG_OCP', 'dropdown', array('Service'=>'Service','Business'=>'Business','Self Employed'=>'Self Employed','Govt. Service'=>'Govt. Service','Other'=>'Other','Unknown'=>'Unknown'));
            $crud->field_type('STD_LG_SPOCP', 'dropdown', array('Railways'=>'Railways','Income Tax'=>'Income Tax','Manufacturer'=>'Manufacturer','Other'=>'Other','Unknown'=>'Unknown'));
            $crud->field_type('STD_P_LAST_MOD_DT', 'hidden', date("Y-m-d H:i:s"));
            $crud->field_type('STD_FTH_NAME', 'readonly');
            $crud->field_type('STD_MTH_NAME', 'readonly');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Parent Details';
            $output->section_heading = 'Parent Details <small>(Edit)</small>';
            $output->menu_name = 'Parent Details';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function my_dues() {
        $class = $this->db->get_where('student_details', array('STD_SEQ' => $this->session->tbl_id))->row()->STD_CS_SEQ;

        $company = $this->db->get('company')->row();
        $this->db->where('CS_SEQ', $class);
        $cls = $this->db->get('class_sec_hdr')->row();

        $this->db->select('STD_SEQ,STD_FNAME,STD_MNAME,STD_LNAME,STD_REGNO,STD_ROLLNO,STD_CONSC');
        $this->db->where('STD_SEQ', $this->session->tbl_id);
        $std = $this->db->get('student_details')->result_array();

        $this->db->select('SUM(Fees) as total_fees');
        $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
        $this->db->where('CS_SEQ', $class);
        $this->db->where('Fees_Type', '1'); //monthly fees
        $this->db->where('Fees !=', '0');
        $monthly_fees_rs = $this->db->get('class_sec_dtl')->result_array();
        $monthly_fees = 0.00;
        if(count($monthly_fees_rs[0]['total_fees']) > 0) {$monthly_fees = $monthly_fees_rs[0]['total_fees'];} else {$monthly_fees = 'Not Set';}

        $this->db->select('SUM(Fees) as total_fees');
        $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
        $this->db->where('CS_SEQ', $class);
        $this->db->where('Fees_Type', '2'); //yearly fees
        $this->db->where('Fees !=', '0');
        $yearly_fees_rs = $this->db->get('class_sec_dtl')->result_array();
        $yearly_fees = 0.00;
        if(count($yearly_fees_rs[0]['total_fees']) > 0) {$yearly_fees = $yearly_fees_rs[0]['total_fees'];} else {$yearly_fees = 'Not Set';}

        $months_arr = array("January"=>"1","February"=>"2","March"=>"3","April"=>"4","May"=>"5","June"=>"6","July"=>"7","August"=>"8","September"=>"9","October"=>"10","November"=>"11","December"=>"12");

        //-------------------------------------------------------------------------------------------------------

        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $doc_name = 'All Dues Report';
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company->COM_NAME);
        $pdf->SetTitle($doc_name);
        $pdf->SetSubject($doc_name);
        $pdf->SetKeywords('All Dues Report, smg, developed by: www.fb.com/pran93');

        // set default header data
        $html_header = <<<EOD
<div style="text-align:center;">
<span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
<br>
$company->COM_ADD2 , $company->COM_CITY
<br>
<strong style="font-size: 13px">All Dues of <span style="background-color: black;color: white;"> {$std[0]['STD_FNAME']} {$std[0]['STD_MNAME']} {$std[0]['STD_LNAME']} </span></strong>
<hr align="left">
</div>
EOD;
        $pdf->setHtmlHeader($html_header, false);

        // set header and footer fonts and size
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(10, 20, 10);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // -----------------------------------------------------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        $pdf->SetFont('times', '', 15, '', true);

        // Add a page
        $pdf->AddPage('P', 'A4');

        $count_total_due_std = 0;

        // Set some content to print
        $html = '';

        foreach ($std as $s) { //this loop is for all students of selected class
            $std_total_fees = 0.00;

            $this->db->select('FEES_DTL_MONTH');
            $this->db->where('FEES_DTL_STD_SEQ', $s['STD_SEQ']);
            $this->db->where('FEES_DTL_STD_CS_SEC', $class);
            $this->db->group_by('FEES_DTL_MONTH');
            $paid_months_rs = $this->db->get('fees_monthly_dtl')->result_array();
            $paid_months = array_column($paid_months_rs, 'FEES_DTL_MONTH');
            $due_months = array_diff($months_arr, $paid_months);

            $this->db->select('SUM(FEES_DTL_AMOUNT) as total_amount');
            $this->db->where('FEES_DTL_STD_SEQ', $s['STD_SEQ']);
            $this->db->where('FEES_DTL_STD_CS_SEC', $class);
            $this->db->group_by('FEES_DTL_STD_SEQ');
            $paid_yearly_fees_rs = $this->db->get('fees_yearly_dtl')->result_array();
            $paid_yearly_fees = 0.00;
            if(count($paid_yearly_fees_rs) > 0) {$paid_yearly_fees = $paid_yearly_fees_rs[0]['total_amount'];}

            if($s['STD_CONSC'] == '1') { //if student is granted for concession
                $this->db->select('SUM(Fees) as total_amount');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->where('std_id', $s['STD_SEQ']);
                $this->db->where('class_id', $class);
                $this->db->where('Fees_Type', '1'); //monthly fees
                $this->db->where('Fees !=', '0');
                $this->db->group_by('Fees_Type');
                $monthly_fees_con_rs = $this->db->get('fees_concession')->result_array();
                if(count($monthly_fees_con_rs) > 0) {$monthly_fees_con = $monthly_fees_con_rs[0]['total_amount'];} else {$monthly_fees_con = 'Not Set';}

                $this->db->select('SUM(Fees) as total_amount');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->where('std_id', $s['STD_SEQ']);
                $this->db->where('class_id', $class);
                $this->db->where('Fees_Type', '2'); //yearly fees
                $this->db->where('Fees !=', '0');
                $this->db->group_by('Fees_Type');
                $yearly_fees_con_rs = $this->db->get('fees_concession')->result_array();
                if(count($yearly_fees_con_rs) > 0) {$yearly_fees_con = $yearly_fees_con_rs[0]['total_amount'];} else {$yearly_fees_con = 'Not Set';}

                $amount_m = $monthly_fees_con;
                if($yearly_fees_con == 'Not Set') {$amount_y = $yearly_fees_con;} else {$amount_y = $yearly_fees_con - $paid_yearly_fees;}
            } else { //if student is not granted for concession
                $amount_m = $monthly_fees;
                if($yearly_fees == 'Not Set') {$amount_y = $yearly_fees;} else {$amount_y = $yearly_fees - $paid_yearly_fees;}
            }

            if(count($due_months) > 0 || $amount_y > 0 || $amount_y === 'Not Set') { //if any dues found
                $count_total_due_std++;
                $html .= '<span style="font-size: 17px">Class & Sec: <strong>'.$cls->Class_Name.' - '.$cls->Sec_Name.'</strong> • Roll: <strong>'.$s['STD_ROLLNO'].'</strong> • Reg. No: <strong>'.$s['STD_REGNO'].'</strong></span>';
                $html .= <<<EOD
<hr>
<table cellspacing="2">
<thead>
<tr>
<th><strong>Dues of</strong></th>
<th align="right"><strong>Amount</strong></th>
</tr>
</thead>
<tbody>
EOD;
                foreach ($due_months as $key => $m) { //monthly dues
                    if($amount_m != 'Not Set') {$std_total_fees += $amount_m; $amount_m1 = number_format($amount_m,2);}
                    $html .= <<<EOD
<tr>
<td>$key</td>
<td align="right">$amount_m1</td>
</tr>
EOD;
                }
                if($amount_y > 0 || $amount_y === 'Not Set') { //yearly dues
                    if($amount_y != 'Not Set') {$std_total_fees += $amount_y; $amount_y = number_format($amount_y,2);}
                    $html .= <<<EOD
<tr>
<td>Yearly Fees</td>
<td align="right">$amount_y</td>
</tr>
EOD;
                }
                $html .= '
<tr>
<td><strong><span style="background-color: black;color: white;"> Total Due </span></strong></td>
<td align="right"><strong><span style="background-color: black;color: white;">'.number_format($std_total_fees,2).'</span></strong></td>
</tr>
</tbody>
</table>
<hr>&nbsp;
<br>';
            }
        }

        if($count_total_due_std == 0) {
            $this->session->set_flashdata('type', 'warning');
            $this->session->set_flashdata('title', 'Hurrah!');
            $this->session->set_flashdata('msg', 'No dues found.');
            return array('page'=>'admin/dashboard');
        }

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document
        $pdf->Output($doc_name . '.pdf', 'I');
    }

    public function monthly_pay_hist() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Student_Single/monthly_pay_hist'));
            $crud->set_theme('datatables');
            $crud->set_subject('Monthly Fees');
            $crud->where('FM_HDR_STD_SEQ', $this->session->tbl_id);
            $crud->set_table('fees_monthly_hdr');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_read();
            $crud->unset_edit();
            $crud->unset_delete();

            $crud->columns('FM_HDR_RCPT_NO','FM_HDR_TOT_FEES','FM_HDR_COL_DATE');

            $crud->display_as('FM_HDR_RCPT_NO', 'Recp. No');
            $crud->display_as('FM_HDR_TOT_FEES', 'Total Fees');
            $crud->display_as('FM_HDR_COL_DATE', 'Collection Date');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Monthly Payment History';
            $output->section_heading = 'Monthly Payment History';
            $output->menu_name = 'Monthly Payment History';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function yearly_pay_hist() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Student_Single/yearly_pay_hist'));
            $crud->set_theme('datatables');
            $crud->set_subject('Yearly Fees');
            $crud->where('FM_HDR_STD_SEQ', $this->session->tbl_id);
            $crud->set_table('fees_yearly_hdr');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_read();
            $crud->unset_edit();
            $crud->unset_delete();

            $crud->columns('FM_HDR_RCPT_NO','FM_HDR_TOT_FEES','FM_HDR_COL_DATE');

            $crud->display_as('FM_HDR_RCPT_NO', 'Recp. No');
            $crud->display_as('FM_HDR_TOT_FEES', 'Total Fees');
            $crud->display_as('FM_HDR_COL_DATE', 'Collection Date');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Yearly Payment History';
            $output->section_heading = 'Yearly Payment History';
            $output->menu_name = 'Yearly Payment History';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function admission_pay_hist() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Student_Single/admission_pay_hist'));
            $crud->set_theme('datatables');
            $crud->set_subject('New Admission Fees');
            $crud->where('FM_HDR_STD_SEQ', $this->session->tbl_id);
            $crud->set_table('fees_newadm_hdr');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_read();
            $crud->unset_edit();
            $crud->unset_delete();

            $crud->columns('FM_HDR_RCPT_NO','FM_HDR_TOT_FEES','FM_HDR_COL_DATE');

            $crud->display_as('FM_HDR_RCPT_NO', 'Recp. No');
            $crud->display_as('FM_HDR_TOT_FEES', 'Total Fees');
            $crud->display_as('FM_HDR_COL_DATE', 'Collection Date');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Admission Payment History';
            $output->section_heading = 'Admission Payment History';
            $output->menu_name = 'Admission Payment History';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function my_progress() {
        $class = $this->db->get_where('student_details', array('STD_SEQ' => $this->session->tbl_id))->row()->STD_CS_SEQ;

        $company = $this->db->get('company')->row();
        $this->db->where('CS_SEQ', $class);
        $cls = $this->db->get('class_sec_hdr')->row();

        $this->db->select('EXAM_SEQ,Exam_Name');
        $this->db->join("exam_test", 'exam_test.EXAM_SEQ = MH_TEST_SEQ', 'left');
        $this->db->where("MH_CLASS_SEQ", $class);
        $this->db->group_by("MH_TEST_SEQ");
        $exam_name = $this->db->get('marks_hdr')->result_array();
        $total_exam = count($exam_name);

        //if no exam marks entry for that class
        if ($total_exam == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'No exam marks entry found.');
            return array('page' => 'admin/dashboard');
        }

        $this->db->select('STD_SEQ,STD_REGNO,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME');
        $this->db->where('STD_SEQ', $this->session->tbl_id);
        $std = $this->db->get('student_details')->result_array();

        $this->db->select('sub_id,sub_s_name');
        $this->db->join('subject', 'subject.sub_id = CS_Sub_id', 'left');
        $this->db->where('CS_SEQ', $class);
        $this->db->order_by('Sorting');
        $sub = $this->db->get('class_sub_link')->result_array();
        $total_sub = count($sub);

        //-------------------------------------------------------------------------------------------------------

        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $doc_name = 'Progress Report of '.$std[0]['STD_FNAME'].' '.$std[0]['STD_MNAME'].' '.$std[0]['STD_LNAME'];
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company->COM_NAME);
        $pdf->SetTitle($doc_name);
        $pdf->SetSubject($doc_name);
        $pdf->SetKeywords($doc_name . ', smg, developed by: www.fb.com/pran93');

        // set default header data
        $html_header = <<<EOD
<div style="text-align:center;">
<span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
<br>
$company->COM_ADD2 , $company->COM_CITY
<br>
<strong style="font-size: 13px">Progress Report of <span style="background-color: black;color: white;"> {$std[0]['STD_FNAME']} {$std[0]['STD_MNAME']} {$std[0]['STD_LNAME']} </span></strong>
<hr align="left">
</div>
EOD;
        $pdf->setHtmlHeader($html_header, false);

        // set header and footer fonts and size
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(10, 20, 10);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // -----------------------------------------------------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        $pdf->SetFont('times', '', 9, '', true);

        // Add a page
        $pdf->AddPage('L', 'A4');

        // Set some content to print
        $html = '';

        foreach ($std as $s) { //this loop is for all students of selected class

            $html .= <<<EOD
<span style="font-size: 17px">Class & Sec: <strong>$cls->Class_Name - $cls->Sec_Name</strong> • Roll: <strong>{$s['STD_ROLLNO']}</strong> • Reg. No: <strong>{$s['STD_REGNO']}</strong></span>
<br>
<table border="1">
<thead>
<tr>
<th width="80"><strong>Exam Name</strong></th>
EOD;
            foreach ($sub as $subject) { //sub names
                $html .= <<<EOD
<th align="center"><strong>{$subject['sub_s_name']}</strong></th>
EOD;
            }
            $html .= <<<EOD
<th width="30" align="center"><strong>Total</strong></th>
<th width="30" align="center"><strong>Avg</strong></th>
<th width="60" align="center"><strong>Remarks</strong></th>
</tr>
</thead>
<tbody>
EOD;

            $sub_total = array_fill(0, $total_sub, 0);
            foreach ($exam_name as $ex) { //exam names
                $html .= <<<EOD
<tr>
<td width="80">{$ex['Exam_Name']}</td>
EOD;
                $exam_total = 0;
                $sub_total_index = 0;
                foreach ($sub as $subject) { //sub names
                    $this->db->select('MD_MARKS');
                    $this->db->where('MD_CLASS_SEQ', $class);
                    $this->db->where('MD_TEST_SEQ', $ex['EXAM_SEQ']);
                    $this->db->where('MD_SUB_SEQ', $subject['sub_id']);
                    $this->db->where('MD_STD_SEQ', $s['STD_SEQ']);
                    $marks_rs = $this->db->get('marks_dtl')->result_array();
                    $marks_arr = array_column($marks_rs, 'MD_MARKS');
                    if($marks_arr){$marks = $marks_arr[0]; $exam_total+=$marks;} else {$marks = '';}

                    $html .= <<<EOD
<td align="center">$marks</td>
EOD;
                    $sub_total[$sub_total_index] += $marks;
                    $sub_total_index++;
                }

                $exam_avg = round($exam_total/$total_sub);
                $html .= <<<EOD
<td width="30" align="center"><strong>$exam_total</strong></td>
<td width="30" align="center"><strong>$exam_avg</strong></td>
<td width="60" align="center"></td>
</tr>
EOD;
            }

            $html .= <<<EOD
<tr>
<td width="80"><strong>Average</strong></td>
EOD;
            foreach ($sub_total as $sub_tot){
                $sub_avg = round($sub_tot/$total_exam);
                $html .= '<td align="center"><strong>'.$sub_avg.'</strong></td>';
            }
            $html .= <<<EOD
<td width="30" align="center"></td>
<td width="30" align="center"></td>
<td width="60" align="center"></td>
</tr>
</tbody>
</table>
<br>
<br>
EOD;
        }

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document
        $pdf->Output($doc_name . '.pdf', 'I');
    }

    public function my_progress_report() {
        //check if marksheet is blocked or not
        $user_rs = $this->db->get_where('users', array('tbl_id' => $this->session->tbl_id))->row();
        $fees_month_rs = $this->db->get_where('settings', array('id' => 1))->row();
        
        $this->db->select_max('FEES_DTL_MONTH', 'last_paid_month');
        $this->db->from('fees_monthly_dtl');
        $this->db->where('FEES_DTL_STD_SEQ', $this->session->tbl_id);
        $this->db->where('FEES_DTL_FIN_YEAR', 2025);
        $query = $this->db->get();
        
        $last_paid_month = $query->row()->last_paid_month;

        if($user_rs->marksheet_blocked == 1) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Access Denied!');
            $this->session->set_flashdata('msg', 'Please contact school authorities.');
            return array('type' => 'redirect', 'page' => 'admin/dashboard');
        }
        
        if($last_paid_month < $fees_month_rs->student_due_fees_month){
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Fees Not CLear!');
            $this->session->set_flashdata('msg', 'Please contact school authorities.');
            return array('type' => 'redirect', 'page' => 'admin/dashboard');
        }
        
        

        $data = array();
        $class = $this->db->get_where('student_details', array('STD_SEQ' => $this->session->tbl_id))->row()->STD_CS_SEQ;
        $data['cs_seq'] = $class;
        $company = $this->db->get('company')->row();
        $data['company'] = $company;
        $std_id = $this->session->tbl_id;

        $this->db->select('student_details.STD_SEQ,STD_REGNO,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME,STD_PH_NO,STD_FTH_NAME,STD_MTH_NAME');
        $this->db->join('student_parent_details','student_parent_details.STD_SEQ=student_details.STD_SEQ','left');
        $this->db->where("student_details.STD_SEQ", $std_id);
        $std = $this->db->get('student_details')->result_array();
        $data['student_details'] = $std;

        $data['class'] = $this->db->where('CS_SEQ', $class)->get('class_sec_hdr')->row()->Class_Name;
        $data['sec'] = $this->db->where('CS_SEQ', $class)->get('class_sec_hdr')->row()->Sec_Name;
        $data['class_teacher'] = $this->db->join('teacher','TCH_SRLNO=class_teacher','left')->where('CS_SEQ', $class)->get('class_sec_hdr')->row()->TCH_NAME;
        $data['exam_terms'] = $this->db->get_where('exam_terms', array('status' => 1))->result();
        $data['subjects'] = $this->db->select('class_sub_link.*,sub_name')
            ->join('subject','sub_id=CS_Sub_id','left')
            ->where('CS_SEQ', $class)
            ->order_by('Sorting', 'ASC')
            ->get('class_sub_link')->result();
        $class_tch_rs = $this->db->join('teacher','TCH_SRLNO=class_teacher','left')->where('CS_SEQ', $class)->get('class_sec_hdr')->row();
     
        $data['class_teacher_sign'] = $class_tch_rs->TCH_SIGN;
        $data['all_grades'] = $this->db->get('grades')->result();

        $data['tab_title'] = 'Progress Report';
        $data['form_type'] = 'progress_report_type_2';

        return array('type' => 'load_view', 'page' => 'progress_report_print', 'data' => $data);
    }

} // /.Student_Single_m model