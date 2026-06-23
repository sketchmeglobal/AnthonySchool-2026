<?php
/**
 * Coded by: Pran Krishna Das
 * Social: sketchmeglobal.com
 * CI: 3.0.6
 * Date: 22-12-2018
 * Time: 18:52
 */

class Students_m extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
//    public function _callback_BeforeInsert_StdDtls($post_array){
//        $get_auto_index = $this->db->query("SHOW TABLE STATUS LIKE 'student_details'")->row()->Auto_increment;
//        $string = 'SN'.date("Y").$get_auto_index.date("mdH");
//        $serial_no = substr($string, 0, 12);
//
//        $post_array['STD_SRLNO'] = $serial_no;
//        return $post_array;
//    }
    public function _callback_AfterInsert_StdDtls($post_array, $primary_key){
        $pass_encrypted = hash('sha256', $post_array['STD_PH_NO']); //encrypting password with sha256 encoding

        //creating details for login
        $data_insert['usertype'] = '4'; //login type student
        $data_insert['tbl_id'] = $primary_key; //primary_id of student_details table
        $data_insert['email'] = $post_array['STD_EMAIL'];
        $data_insert['pass'] = $pass_encrypted;
        $data_insert['verified'] = 1;
        $this->db->insert('users', $data_insert);

        //creating parent details
        $data_insert2['STD_SEQ'] = $primary_key; //primary_id of student_details table
        $this->db->insert('student_parent_details', $data_insert2);
    }

    public function print_certificate($STD_SEQ) {
        $company = $this->db->get('company')->row();

        $this->db->select('student_details.*, STD_FTH_NAME, Class_Name');
        $this->db->join('student_parent_details', 'student_parent_details.STD_SEQ = student_details.STD_SEQ', 'left');
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $this->db->where("student_details.STD_SEQ", $STD_SEQ);
        $std_row = $this->db->get('student_details')->row();

        return array('page'=>'common_v', 'data'=>$output); //loading common view page

        //-------------------------------------------------------------------------------------------------------

        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $doc_name = 'Student Certificate ' . $std_row->Class_Name . ' - ' . $std_row->Sec_Name . ' - ' . $std_row->STD_FNAME.' '.$std_row->STD_MNAME.' '.$std_row->STD_LNAME;
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company->COM_NAME);
        $pdf->SetTitle($doc_name);
        $pdf->SetSubject($doc_name);
        $pdf->SetKeywords($doc_name . ', smg, developed by: sketchmeglobal.com');

        // set default header data
        $pdf->setPrintHeader(false);

        // set header and footer fonts and size
//        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
//        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        //remove footer
        $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 10);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // -----------------------------------------------------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        $pdf->SetFont('times', '', 11, '', true);

        // Add a page
        $pdf->AddPage('P', 'A4');

        $dob = date('d-m-Y', strtotime($std_row->STD_DOB));
        $today_date = date('d-m-Y');
        $html = <<<EOD
<table>
    <tr>
        <td>
            <div align="right">$today_date</div>
            
            <br><br><br><br><br><br><br><br>
            
            <div>
                <h2 style="font-size: 20px; text-align: center"><u>Whomever it may concern</u></h2>
                
                <p style="font-size: 20px; text-align: justify">This is to state that
                    $std_row->STD_FNAME $std_row->STD_MNAME $std_row->STD_LNAME S/O/D/O $std_row->STD_FTH_NAME 
                    resident of $std_row->STD_ADDR_1 is a student of our school.
                    He/She is in class $std_row->Class_Name in the academic session $company->COM_FIN_YEAR. 
                    His/Her date of birth is recorded as $dob in the school admission register [NO. $std_row->STD_REGNO].
                    
                    <br>
                    <br>The above stated information is true to the best of my knowledge.
                    
                    <br><br><br><br><br>
                    <br>Headmaster
                </p>
            </div>
        </td>
    </tr>
</table>
EOD;

        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document
        $pdf->Output($doc_name . '.pdf', 'I');
    }

    public function general_letter() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Students/general_letter'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('General Letter');
            $crud->set_table('student_certificates');
            $crud->where('category', 'general');

            $crud->unset_export();
            $crud->unset_read();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_delete();

            $crud->columns('class_sec_id','std_id','category','certificate_no','certificate_date');
            $crud->display_as('class_sec_id', 'Class - Sec');
            $crud->display_as('std_id', 'Student Name');

            $crud->fields('class_sec_id', 'std_id', 'certificate_date');

            $crud->required_fields('class_sec_id', 'std_id', 'certificate_date');

            $crud->set_relation('class_sec_id', 'class_sec_hdr', 'class_sec');
            $crud->set_relation('std_id', 'student_details', '{ST_FULL_NAME}{space}-{space}{STD_REGNO}');

            $crud->field_type('fin_year', 'hidden', FINANCIAL_YEAR);

            $crud->add_action('Print', base_url('assets/grocery_crud/themes/flexigrid/css/images/print.png'), 'admin/print_general_letter','ui-icon-pencil');
            $crud->callback_column('certificate_no', array($this, '_callback_certificate_no'));

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'General Letter';
            $output->section_heading = 'General Letter <small>(Edit)</small>';
            $output->menu_name = 'General Letter Details';
            $output->add_button = '<a class="btn btn-success" target="_blank" href="'.base_url('admin/add_general_letter').'">Add General Letter</a>';

            return array('type' => 'load_view', 'page'=>'common_v', 'data'=>$output); //loading common view page

        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function add_general_letter() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'general_letter';

        $data['tab_title'] = 'Print General Letter';
        $data['section_heading'] = 'Print General Letter <small> (Print) </small>';
        $data['menu_name'] = 'Print General Letter';

        return array('type' => 'load_view', 'page' => 'accounts_v', 'data' => $data);
    }

    public function print_general_letter($certificate_id) {
        if($this->input->post()){

            $data['form_data'] = $this->input->post();
            $certificate_no = '';

            $ctype = $this->db->get_where('class_sec_hdr', array('CS_SEQ' => $data['form_data']['class']))->row()->Class_Type;
            $classes_within_ctype_sql = "SELECT GROUP_CONCAT(CS_SEQ) AS all_class_sec FROM class_sec_hdr WHERE Class_Type =" . $ctype;
            $classes_within_ctype = $this->db->query($classes_within_ctype_sql)->result()[0]->all_class_sec;
            $classes_within_ctype = str_replace("'", "",$classes_within_ctype);

            $sql1 = "SELECT * FROM `student_certificates` WHERE `class_sec_id` IN(".$classes_within_ctype.") AND `category` = 'general' AND `status` = 1";
            $nr = $this->db->query($sql1)->num_rows();

            if($nr == 0){
                $certificate_no = '0001';
            }else{

                $sql2 = "SELECT * FROM `student_certificates` WHERE `class_sec_id` IN(".$classes_within_ctype.") AND `category` = 'general' AND `status` = 1 ORDER BY sc_id DESC LIMIT 1";
                $last_cert_no = $this->db->query($sql2)->row()->certificate_no;

                $certificate_no = $this->custom_left_zero_pad($last_cert_no + 1);
            }



            $insertArray = array(
                'fin_year' =>  FINANCIAL_YEAR,
                'std_id' =>  $data['form_data']['student_name'],
                'class_sec_id' =>  $data['form_data']['class'],
                'category' =>  'general',
                'certificate_no' =>  $certificate_no,
                'certificate_date' =>  $data['form_data']['certificate_date']
            );

            if($this->db->insert('student_certificates', $insertArray)){
                $certificate_id = $this->db->insert_id();
                redirect('admin/print_general_letter/' . $certificate_id,'refresh');
            }else{
                die('Issues regarding insert!');
            }

        } else{

            $cert_row = $this->db->get_where('student_certificates', array('sc_id' => $certificate_id))->row();
            $class_id = $cert_row->class_sec_id;
            $STD_SEQ = $cert_row->std_id;
            $data['certificate_no'] = $cert_row->certificate_no;
            $data['certificate_date'] = $cert_row->certificate_date;

            $this->db->where_in('CS_SEQ', $class_id);
            $class_type = $this->db->get('class_sec_hdr')->row();
            $data['class_type'] = $class_type->Class_Type;
            $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>$class_type->Class_Type))->row();
            $data['company'] = $company;

            $this->db->select('student_details.*, STD_FTH_NAME, Class_Name');
            $this->db->join('student_parent_details', 'student_parent_details.STD_SEQ = student_details.STD_SEQ', 'left');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where("student_details.STD_SEQ", $STD_SEQ);
            $std_row = $this->db->get('student_details')->result();

            $data['tab_title'] = "General Letter";
            $data['std_row'] = $std_row;
            $data['classes'] = $this->db->get_where('class_sec_hdr', array('CS_SEQ' => $std_row[0]->STD_CS_SEQ))->result();

            $data['print_section'] = 'general_letter';
            $this->load->view('common_print_v', $data);

        }
    }


    public function character_certificate() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Students/character_certificate'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Character Certificate');
            $crud->set_table('student_certificates');
            $crud->where('category', 'character');

            $crud->unset_export();
            $crud->unset_read();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_delete();

            $crud->columns('class_sec_id','std_id','category','certificate_no','certificate_date');
            $crud->display_as('class_sec_id', 'Class - Sec');
            $crud->display_as('std_id', 'Student Name');
            $crud->display_as('is_promotion_made', 'Promotion Has Been');

            $crud->unset_fields('certificate_no', 'category', 'reason_for_leaving', 'created_date', 'modified_date', 'status');

            $crud->required_fields('class_sec_id','std_id','district', 'was_in_class_upto','certificate_remarks','is_promotion_made','certificate_date');

            $crud->set_relation('class_sec_id', 'class_sec_hdr', 'class_sec');
            $crud->set_relation('std_id', 'student_details', '{ST_FULL_NAME}{space}-{space}{STD_REGNO}');

            $crud->field_type('fin_year', 'hidden', FINANCIAL_YEAR);

            $crud->add_action('Print', base_url('assets/grocery_crud/themes/flexigrid/css/images/print.png'), 'admin/print_character_certificate','ui-icon-pencil');
            $crud->callback_column('certificate_no', array($this, '_callback_certificate_no'));

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Character Certificate';
            $output->section_heading = 'Character Certificate <small>(Edit)</small>';
            $output->menu_name = 'Character Certificate Details';
            $output->add_button = '<a class="btn btn-success" target="_blank" href="'.base_url('admin/add_character_certificate').'">Add Character Certificate</a>';

            return array('type' => 'load_view', 'page'=>'common_v', 'data'=>$output); //loading common view page

        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function add_character_certificate() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'character_certificate';

        $data['tab_title'] = 'Print Character Certificate';
        $data['section_heading'] = 'Print Character Certificate <small> (Print) </small>';
        $data['menu_name'] = 'Print Character Certificate';

        return array('type' => 'load_view', 'page' => 'accounts_v', 'data' => $data);
    }

    public function print_character_certificate($certificate_id) {

        if($this->input->post()){

            $data['form_data'] = $this->input->post();
            $certificate_no = '';

            $ctype = $this->db->get_where('class_sec_hdr', array('CS_SEQ' => $data['form_data']['class']))->row()->Class_Type;
            $classes_within_ctype_sql = "SELECT GROUP_CONCAT(CS_SEQ) AS all_class_sec FROM class_sec_hdr WHERE Class_Type =" . $ctype;
            $classes_within_ctype = $this->db->query($classes_within_ctype_sql)->result()[0]->all_class_sec;
            $classes_within_ctype = str_replace("'", "",$classes_within_ctype);

            $sql1 = "SELECT * FROM `student_certificates` WHERE `class_sec_id` IN(".$classes_within_ctype.") AND `category` = 'character' AND `status` = 1";
            $nr = $this->db->query($sql1)->num_rows();

            if($nr == 0){
                $certificate_no = '0001';
            }else{

                $sql2 = "SELECT * FROM `student_certificates` WHERE `class_sec_id` IN(".$classes_within_ctype.") AND `category` = 'character' AND `status` = 1 ORDER BY sc_id DESC LIMIT 1";
                $last_cert_no = $this->db->query($sql2)->row()->certificate_no;

                $certificate_no = $this->custom_left_zero_pad($last_cert_no + 1);
            }

            $insertArray = array(
                'fin_year' =>  FINANCIAL_YEAR,
                'std_id' =>  $data['form_data']['student_name'],
                'class_sec_id' =>  $data['form_data']['class'],
                'category' =>  'character',
                'certificate_no' =>  $certificate_no,
                'district' =>  $data['form_data']['district'],
                'was_in_class_upto' =>  $data['form_data']['upto'],
                // 'reason_for_leaving' =>  $data['form_data']['reason_for_leaving'],
                'certificate_remarks' =>  $data['form_data']['certificate_remarks'],
                'is_promotion_made' =>  $data['form_data']['promotion_has_been'],
                'certificate_date' =>  $data['form_data']['certificate_date']
            );

            if($this->db->insert('student_certificates', $insertArray)){
                $certificate_id = $this->db->insert_id();
                redirect('admin/print_character_certificate/' . $certificate_id, 'refresh');
            }else{
                die('Issues regarding insert!');
            }

        } else{

            $cert_row = $this->db->get_where('student_certificates', array('sc_id' => $certificate_id))->row();
            $class_id = $cert_row->class_sec_id;
            $STD_SEQ = $cert_row->std_id;
            $data['district'] = $cert_row->district;
            $data['upto'] = $cert_row->was_in_class_upto;
            $data['certificate_no'] = $cert_row->certificate_no;
            $data['certificate_remarks'] = $cert_row->certificate_remarks;
            $data['promotion_has_been'] = $cert_row->is_promotion_made;
            $data['certificate_date'] = $cert_row->certificate_date;
            $data['reason_for_leaving'] = $cert_row->reason_for_leaving;

            $this->db->where_in('CS_SEQ', $class_id);
            $class_type = $this->db->get('class_sec_hdr')->row();
            $data['class_type'] = $class_type->Class_Type;
            $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>$class_type->Class_Type))->row();
            $data['company'] = $company;

            $this->db->select('student_details.*, STD_FTH_NAME, Class_Name');
            $this->db->join('student_parent_details', 'student_parent_details.STD_SEQ = student_details.STD_SEQ', 'left');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where("student_details.STD_SEQ", $STD_SEQ);
            $std_row = $this->db->get('student_details')->result();

            $data['tab_title'] = "Leaving Certificate";
            $data['std_row'] = $std_row;
            $data['classes'] = $this->db->get_where('class_sec_hdr', array('CS_SEQ' => $std_row[0]->STD_CS_SEQ))->result();

            $data['print_section'] = 'leaving_certificate';
            $this->load->view('common_print_v', $data);

        }

    }

    public function leaving_certificate() {

        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Students/leaving_certificate'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Leaving Certificate');
            $crud->set_table('student_certificates');
            $crud->where('category', 'transfer_or_leaving');

            $crud->unset_export();
            $crud->unset_read();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_delete();

            $crud->columns('class_sec_id','std_id','category','certificate_no','certificate_date');
            $crud->display_as('class_sec_id', 'Class - Sec');
            $crud->display_as('std_id', 'Student Name');
            $crud->display_as('is_promotion_made', 'Promotion Has Been');

            $crud->unset_fields('certificate_no', 'category', 'created_date', 'modified_date', 'status');

            $crud->required_fields('class_sec_id','std_id','district', 'was_in_class_upto', 'reason_for_leaving','certificate_remarks','is_promotion_made','certificate_date');

            $crud->set_relation('class_sec_id', 'class_sec_hdr', 'class_sec');
            $crud->set_relation('std_id', 'student_details', '{ST_FULL_NAME}{space}-{space}{STD_REGNO}');

            $crud->field_type('fin_year', 'hidden', FINANCIAL_YEAR);

            $crud->add_action('Print', base_url('assets/grocery_crud/themes/flexigrid/css/images/print.png'), 'admin/print_leaving_certificate','ui-icon-pencil');
            $crud->callback_column('certificate_no', array($this, '_callback_certificate_no'));

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Leaving Certificate';
            $output->section_heading = 'Leaving Certificate <small>(Edit)</small>';
            $output->menu_name = 'Leaving Certificate Details';
            $output->add_button = '<a class="btn btn-success" target="_blank" href="'.base_url('admin/add_leaving_certificate').'">Add Leaving Certificate</a>';

            return array('type' => 'load_view', 'page'=>'common_v', 'data'=>$output); //loading common view page

        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }

    }
    public function _callback_certificate_no($value, $row) {
        $sql = "SELECT Class_Type FROM student_details 
        LEFT JOIN class_sec_hdr ON student_details.STD_CS_SEQ = class_sec_hdr.CS_SEQ 
        WHERE student_details.STD_SEQ=$row->std_id";

        $ct = $this->db->query($sql)->row()->Class_Type;

        if($ct == 1){
            return PREFIX_NUR_KG . $row->certificate_no;
        } else if($ct == 2){
            return PREFIX_PRIMARY . $row->certificate_no;
        } else if($ct == 3){
            return PREFIX_HIGH . $row->certificate_no;
        }else{
            return PREFIX_HIGHER . $row->certificate_no;
        }

    }

    public function add_leaving_certificate() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'leaving_certificate';

        $data['tab_title'] = 'Print Leaving Certificate';
        $data['section_heading'] = 'Print Leaving Certificate <small> (Print) </small>';
        $data['menu_name'] = 'Print Leaving Certificate';

        return array('type' => 'load_view', 'page' => 'accounts_v', 'data' => $data);
    }

    public function print_leaving_certificate($certificate_id) {

        if($this->input->post()){

            $data['form_data'] = $this->input->post();
            $certificate_no = '';

            $ctype = $this->db->get_where('class_sec_hdr', array('CS_SEQ' => $data['form_data']['class']))->row()->Class_Type;
            $classes_within_ctype_sql = "SELECT GROUP_CONCAT(CS_SEQ) AS all_class_sec FROM class_sec_hdr WHERE Class_Type =" . $ctype;
            $classes_within_ctype = $this->db->query($classes_within_ctype_sql)->result()[0]->all_class_sec;
            $classes_within_ctype = str_replace("'", "",$classes_within_ctype);

            $sql1 = "SELECT * FROM `student_certificates` WHERE `class_sec_id` IN(".$classes_within_ctype.") AND `category` = 'transfer_or_leaving' AND `status` = 1";
            $nr = $this->db->query($sql1)->num_rows();

            if($nr == 0){
                $certificate_no = '0001';
            }else{

                $sql2 = "SELECT * FROM `student_certificates` WHERE `class_sec_id` IN(".$classes_within_ctype.") AND `category` = 'transfer_or_leaving' AND `status` = 1 ORDER BY sc_id DESC LIMIT 1";
                $last_cert_no = $this->db->query($sql2)->row()->certificate_no;

                $certificate_no = $this->custom_left_zero_pad($last_cert_no + 1);
            }



            $insertArray = array(
                'fin_year' =>  FINANCIAL_YEAR,
                'std_id' =>  $data['form_data']['student_name'],
                'class_sec_id' =>  $data['form_data']['class'],
                'category' =>  'transfer_or_leaving',
                'certificate_no' =>  $certificate_no,
                'district' =>  $data['form_data']['district'],
                'was_in_class_upto' =>  $data['form_data']['upto'],
                'reason_for_leaving' =>  $data['form_data']['reason_for_leaving'],
                'certificate_remarks' =>  $data['form_data']['certificate_remarks'],
                'is_promotion_made' =>  $data['form_data']['promotion_has_been'],
                'certificate_date' =>  $data['form_data']['certificate_date']
            );

            if($this->db->insert('student_certificates', $insertArray)){
                $certificate_id = $this->db->insert_id();
                redirect('admin/print_leaving_certificate/' . $certificate_id,'refresh');
            }else{
                die('Issues regarding insert!');
            }

        } else{

            $cert_row = $this->db->get_where('student_certificates', array('sc_id' => $certificate_id))->row();
            $class_id = $cert_row->class_sec_id;
            $STD_SEQ = $cert_row->std_id;
            $data['district'] = $cert_row->district;
            $data['upto'] = $cert_row->was_in_class_upto;
            $data['certificate_no'] = $cert_row->certificate_no;
            $data['certificate_remarks'] = $cert_row->certificate_remarks;
            $data['promotion_has_been'] = $cert_row->is_promotion_made;
            $data['certificate_date'] = $cert_row->certificate_date;
            $data['reason_for_leaving'] = $cert_row->reason_for_leaving;

            $this->db->where_in('CS_SEQ', $class_id);
            $class_type = $this->db->get('class_sec_hdr')->row();
            $data['class_type'] = $class_type->Class_Type;
            $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>$class_type->Class_Type))->row();
            $data['company'] = $company;

            $this->db->select('student_details.*, STD_FTH_NAME, Class_Name');
            $this->db->join('student_parent_details', 'student_parent_details.STD_SEQ = student_details.STD_SEQ', 'left');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where("student_details.STD_SEQ", $STD_SEQ);
            $std_row = $this->db->get('student_details')->result();

            $data['tab_title'] = "Leaving Certificate";
            $data['std_row'] = $std_row;
            $data['classes'] = $this->db->get_where('class_sec_hdr', array('CS_SEQ' => $std_row[0]->STD_CS_SEQ))->result();

            $data['print_section'] = 'leaving_certificate';
            $this->load->view('common_print_v', $data);

        }

    }

    public function student_parent_details_datatables() {
        $query = "SELECT student_parent_details.STD_P_SEQ, CONCAT(`Class_Name`, '-', `Sec_Name`) AS myclass, student_details.ST_FULL_NAME, student_parent_details.STD_FTH_NAME, student_parent_details.STD_MTH_NAME 
        FROM student_parent_details
        LEFT JOIN student_details ON student_parent_details.STD_SEQ = student_details.STD_SEQ
        LEFT JOIN class_sec_hdr ON student_details.STD_CS_SEQ = class_sec_hdr.CS_SEQ WHERE student_details.STD_STATUS=0";

        $data['menu_name'] = 'Parent details';
        $data['outputs']  = $this->db->query($query)->result();

        return $data;
    }

    public function student_parent_details_edit() {
        try{
            $crud = new grocery_CRUD();
            // $crud->set_crud_url_path(base_url('admin_panel/Students/student_parent_details'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Student Parent Details');
            $crud->set_table('student_parent_details');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_add();
            $crud->unset_delete();

            $crud->columns('Class & Sec','STD_SEQ','STD_FTH_NAME','STD_MTH_NAME','STD_LG_NAME');
            $crud->required_fields('STD_FTH_NAME','STD_MTH_NAME');
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

            $crud->callback_column('Class & Sec', array($this, '_callback_class_sec'));

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Student Parent Details';
            $output->section_heading = 'Student Parent Details <small>(Edit)</small>';
            $output->menu_name = 'Student Parent Details';
            $output->add_button = '';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page

        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    public function _callback_class_sec($value, $row) {
        $row = $this->db->query("SELECT `Class_Name`,`Sec_Name` FROM student_details LEFT JOIN class_sec_hdr ON student_details.STD_CS_SEQ = class_sec_hdr.CS_SEQ WHERE student_details.STD_SEQ=$row->STD_SEQ")->row();
        return $row->Class_Name.' - '.$row->Sec_Name;
    }

    public function student_auto_roll() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'student_auto_roll';

        $data['tab_title'] = 'Auto Allot Student Roll No.';
        $data['section_heading'] = 'Automatically Assign Roll No to Students';
        $data['menu_name'] = 'Auto Allot Student Roll No.';

        return array('type' => 'load_view', 'page' => 'students_v', 'data' => $data);
    }

    public function form_student_auto_roll() {
        //if form not submitted
        if($this->input->post('submit') != 'submit_student_auto_roll') {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/student_auto_roll');
        }

        $class_id = $this->input->post('class');

        $this->db->select('STD_SEQ, STD_FNAME, STD_MNAME, STD_LNAME');
        $this->db->where('STD_CS_SEQ', $class_id);
        $this->db->where('STD_STATUS', 0);
        $this->db->where('STD_LEFT', 0);
        $this->db->order_by('STD_FNAME, STD_MNAME, STD_LNAME');
        $rs_std = $this->db->get('student_details')->result_array();

        if(count($rs_std) == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Eee!');
            $this->session->set_flashdata('msg', 'No students found in that class.');
            return array('page'=>'admin/student_auto_roll');
        }
        $seq = 1;
        foreach ($rs_std as $std) {
            $data_update['STD_ROLLNO'] = $seq;
            $this->db->where('STD_SEQ', $std['STD_SEQ']);
            $this->db->update('student_details', $data_update);

            $seq++;
        }

        $this->session->set_flashdata('type', 'success');
        $this->session->set_flashdata('title', 'Zaap!');
        $this->session->set_flashdata('msg', "All student's roll number updated successfully.");
        return array('page'=>'admin/student_auto_roll');
    }

    public function admit_card() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'admit_card';

        $data['tab_title'] = 'Print Admit Card';
        $data['section_heading'] = 'Print Admit Card <small>(Print)</small>';
        $data['menu_name'] = 'Print Admit Card';

        return array('type' => 'load_view', 'page' => 'accounts_v', 'data' => $data);
    }

    public function ajax_update_std_admit_card() {
        $class_id = $this->input->post('class_id');

        $this->db->select('STD_SEQ,STD_FNAME,STD_MNAME,STD_LNAME,STD_ROLLNO');
        $this->db->where("STD_LEFT", 0);
        $this->db->where("STD_STATUS", 0);
        $this->db->where('STD_CS_SEQ', $class_id);
        $this->db->order_by('STD_ROLLNO');
        $std = $this->db->get('student_details')->result_array();

        $html_std = '';
        $html_std .= <<<EOD
<label class="checkbox-custom check-warning col-lg-12">
    <input class="" value="" type="checkbox" id="checkbox_select_all" name="" >
    <label for="checkbox_select_all">Select All</label>
</label>
EOD;
        //creating individual student table row
        foreach($std as $s) {
            $html_std .= <<<EOD
<label class="checkbox-custom check-success col-lg-4">
    <input value="{$s['STD_SEQ']}" name="std[]" id="std_{$s['STD_SEQ']}" type="checkbox" class="select_std checkbox_msksht">
    <label for="std_{$s['STD_SEQ']}">{$s['STD_ROLLNO']} - {$s['STD_FNAME']} {$s['STD_MNAME']} {$s['STD_LNAME']}</label>
</label>
EOD;
        }

        $array['html_std'] = $html_std;
        return $array;
    }

    public function ajax_update_std_admit_card1() {
        $class_id = $this->input->post('class_id');

        $this->db->select('STD_SEQ,STD_FNAME,STD_MNAME,STD_LNAME,STD_ROLLNO');
        $this->db->where("STD_LEFT", 0);
        $this->db->where("STD_STATUS", 0);
        $this->db->where('STD_CS_SEQ', $class_id);
        $this->db->order_by('STD_ROLLNO');
        $std = $this->db->get('student_details')->result_array();

        $html_std = '';
        //creating individual student table row
        foreach($std as $s) {
            $html_std .= <<<EOD
<option value="{$s['STD_SEQ']}">{$s['STD_ROLLNO']} - {$s['STD_FNAME']} {$s['STD_MNAME']} {$s['STD_LNAME']}</option>
EOD;
        }

        $array['html_std'] = $html_std;
        return $array;
    }

    public function ajax_std_reg_no_on_admit_card() {
        $std_reg_no = $this->input->post('std_reg_no');

        $this->db->select('STD_SEQ,STD_FNAME,STD_MNAME,STD_LNAME,STD_ROLLNO');
        $this->db->where("STD_LEFT", 0);
        $this->db->where("STD_STATUS", 0);
        $this->db->where('STD_REGNO', $std_reg_no);
        $this->db->order_by('STD_ROLLNO');
        $std = $this->db->get('student_details')->result_array();

        $html_std = '';
        //creating individual student table row
        foreach($std as $s) {
            $html_std .= <<<EOD
<label class="checkbox-custom check-success col-lg-4">
    <input checked value="{$s['STD_SEQ']}" name="std[]" id="std_{$s['STD_SEQ']}" type="checkbox" class="select_std">
    <label for="std_{$s['STD_SEQ']}">{$s['STD_ROLLNO']} - {$s['STD_FNAME']} {$s['STD_MNAME']} {$s['STD_LNAME']}</label>
</label>
EOD;
        }

        $array['html_std'] = $html_std;
        return $array;
    }

    public function print_admit_card() {
        if($this->input->post('submit') == 'print_admit_card') { //if form submitted
            // print_r($_POST);
            if(!empty($this->input->post('std_reg_no'))){

                $row_details = $this->db->get_where('student_details', array('STD_REGNO' => $this->input->post('std_reg_no')))->row();
                if(!empty($row_details)){
                    $class = $row_details->STD_CS_SEQ;
                }else{
                    $this->session->set_flashdata('type', 'error');
                    $this->session->set_flashdata('title', 'Naa!');
                    $this->session->set_flashdata('msg', 'Class not found.');
                    redirect(base_url('admin/admit_card'));
                }

            }else{
                $class = $this->input->post('class');
            }


            $test_name = $this->input->post('test_name');
            $std_id_arr = $this->input->post('std[]');

            $company = $this->db->get('company')->row();
            $this->db->where('CS_SEQ', $class);
            $cls = $this->db->get('class_sec_hdr')->row();

            $cls_type = $cls->Class_Type;
            $com = $this->db->get_where('company',array('SCHOOL_TYPE' => $cls_type))->row()->COM_NAME;

            if($cls_type == 1){
                $imgsrc = base_url('assets/img/SCHOOL_LOGO_NUR.jpg');
            }else{
                $imgsrc = base_url('assets/img/SCHOOL_LOGO.jpg');
            }

            //if class does not exists
            if (count($cls) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Class not found.');
                return array('page' => 'admin/admit_card');
            }
            //if no student selected
            if (count($std_id_arr) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'No Student Selected.');
                return array('page' => 'admin/admit_card');
            }

            $this->db->select('STD_SEQ,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME');
            $this->db->where_in("STD_SEQ", $std_id_arr);
            $std_arr = $this->db->order_by('STD_ROLLNO','ASC')->get('student_details')->result_array();

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Admit Cards of Class ' . $cls->Class_Name . ' - ' . $cls->Sec_Name;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords($doc_name . ', smg, developed by: sketchmeglobal.com');

            // set default header data
            $pdf->setPrintHeader(false);

            // set header and footer fonts and size
//            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, 10);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // -----------------------------------------------------------------------------------------------------

            // set default font subsetting mode
            $pdf->setFontSubsetting(true);

            // Set font
            $pdf->SetFont('times', '', 10, '', true);

            // Add a page
            $pdf->AddPage('P', 'A4');

            $html = <<<EOD
<table>
<br/><br/>
<tr>
<td>
EOD;
            $count = 1;
            $section_per_page = 1;
            foreach ($std_arr as $v) { //this loop is for all selected students
                $params['data'] = <<<EOD
                Test: $test_name
                Name: {$v['STD_FNAME']} {$v['STD_MNAME']} {$v['STD_LNAME']}
                Class: $cls->Class_Name
                Sec: $cls->Sec_Name
                Roll: {$v['STD_ROLLNO']}
                School: $com
                
                Created by: www.sketchmeglobal.com
EOD;
                $params['level'] = 'H';
                $params['size'] = 2;
                $params['savename'] = 'assets/admin_panel/img/qr_codes/admit_card_qr/'.$v['STD_SEQ'].'_AdmitCard.png';
                $qr = base_url('assets/img/logo_stan.jpg');
                $sign = base_url('assets/img/'.$company->HEADMASTER_SIGN);

                $html .= <<<EOD
<table cellspacing="0" cellpadding="0" border="1" style="float:right;width:325px;">
<tr>
<td align="center" width="60%">
    <span style="font-size: 13px; margin: 0;"><strong>$com</strong></span>
    <br>
    <span style="font-size: 10px; margin: 0;">$company->COM_ADD2 , $company->COM_CITY</span>
    <br>
    <span style="font-size: 15px; margin: 0;"><strong>ADMIT CARD</strong></span>
    <br>
    <span>$test_name</span>
</td>
<td align="center" width="40%">
    <br/>
    <br style="line-height: 6px"/>
    <img src="$imgsrc" width="70px" height="70px" />
</td>
</tr>

<tr>
<td colspan="2">
    <div style="text-align: left">
        <span style="font-size: 13px; margin: 0;">Name: <strong>{$v['STD_FNAME']} {$v['STD_MNAME']} {$v['STD_LNAME']}</strong></span>
        <br>
        <span style="font-size: 15px; margin: 0;">Class: <strong>$cls->Class_Name</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sec: <strong>$cls->Sec_Name</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Roll: <strong>{$v['STD_ROLLNO']}</strong></span>
    </div>
    <span style="text-align: right;">
        <img src="{$sign}" alt="Headmaster Sign" width="50px"> &nbsp;&nbsp;&nbsp;&nbsp;
        <br/>
        ____________
        <br>
    Headmaster
    </span>
</td>
</tr>
</table>
EOD;

                //8 section per page
                if($section_per_page == 8) {
                    $html .= '<br pagebreak="true" /></td></tr><tr><td>';
                    $section_per_page = 0;
                    $count = 1;
                }
                //2 section per row
                elseif ($count == 1) {
                    $html .= '</td><td>';
                    $count++;
                }
                else {
                    $html .= '</td></tr><br><tr><td>';
                    $count = 1;
                }

                $section_per_page++;
            }
            $html .= <<<EOD
</td>
</tr>
</table>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        }
        elseif($this->input->post('submit') == 'print_admit_card_blank') {
            $class = $this->input->post('class');
            $test_name = $this->input->post('test_name');

            $company = $this->db->get('company')->row();
            $this->db->where('CS_SEQ', $class);
            $cls = $this->db->get('class_sec_hdr')->row();

            $cls_type = $cls->Class_Type;
            $com = $this->db->get_where('company',array('SCHOOL_TYPE' => $cls_type))->row()->COM_NAME;

            if($cls_type == 1){
                $imgsrc = base_url('assets/img/SCHOOL_LOGO_NUR.jpg');
            }else{
                $imgsrc = base_url('assets/img/SCHOOL_LOGO.jpg');
            }

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Admit Cards';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords($doc_name . ', smg, developed by: sketchmeglobal.com');

            // set default header data
            $pdf->setPrintHeader(false);

            // set header and footer fonts and size
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, 10);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // -----------------------------------------------------------------------------------------------------

            // set default font subsetting mode
            $pdf->setFontSubsetting(true);

            // Set font
            $pdf->SetFont('times', '', 10, '', true);

            // Add a page
            $pdf->AddPage('P', 'A4');

            $html = <<<EOD
<table>
<br/><br/>
<tr>
<td>
EOD;
            $count = 1;
            $section_per_page = 1;
            while ($section_per_page <= 8) { //this loop is for all selected students

                $html .= <<<EOD
<table cellspacing="0" cellpadding="0" border="1" style="float:right;width:325px;">
<tr>
<td align="center" width="60%">
    <span style="font-size: 13px; margin: 0;"><strong>$com</strong></span>
    <br>
    <span style="font-size: 10px; margin: 0;">$company->COM_ADD2 , $company->COM_CITY</span>
    <br>
    <span style="font-size: 15px; margin: 0;"><strong>ADMIT CARD</strong></span>
    <br>
    <span>$test_name</span>
</td>
<td align="center" width="40%">
    <br/>
    <br style="line-height: 6px"/>
    <img src="$imgsrc" width="70px" height="70px" />
</td>
</tr>

<tr>
<td colspan="2">
    <div style="text-align: left">
    <span style="font-size: 13px; margin: 0;">Name: </span>
    <br>
    <span style="font-size: 13px; margin: 0;">Class: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sec: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Roll: </span>
    </div>
    <span style="text-align: right;">
    ____________
    <br>
    Headmaster
    </span>
</td>
</tr>
</table>
EOD;

                //2 section per row
                if ($count == 1) {
                    $html .= '</td><td>';
                    $count++;
                }
                else {
                    $html .= '</td></tr><br><tr><td>';
                    $count = 1;
                }

                $section_per_page++;
            }
            $html .= <<<EOD
</td>
</tr>
</table>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');
        }
        else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/admit_card');
        }
    }

    public function identity_card() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'identity_card';

        $data['tab_title'] = 'Print Identity Card';
        $data['section_heading'] = 'Print Identity Card <small>(Print)</small>';
        $data['menu_name'] = 'Print Identity Card';

        return array('type' => 'load_view', 'page' => 'accounts_v', 'data' => $data);
    }

    public function print_identity_card() {
        if($this->input->post('submit') == 'print_identity_card') { //if form submitted
            $class = $this->input->post('class');
            $std_id_arr = $this->input->post('std[]');

            $company = $this->db->get('company')->row();
            $this->db->where('CS_SEQ', $class);
            $cls = $this->db->get('class_sec_hdr')->row();

            $cls_type = $cls->Class_Type;
            $com = $this->db->get_where('company',array('SCHOOL_TYPE' => $cls_type))->row()->COM_NAME;

            if($cls_type == 1){
                $imgsrc = base_url('assets/img/SCHOOL_LOGO_NUR.jpg');
            }else{
                $imgsrc = base_url('assets/img/SCHOOL_LOGO.jpg');
            }

            //if class does not exists
            if (count((array)$cls) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Class not found.');
                return array('page' => 'admin/identity_card');
            }
            //if no student selected
            if (count($std_id_arr) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'No Student Selected.');
                return array('page' => 'admin/identity_card');
            }

            $this->db->select('states.name as state,STD_FTH_NAME,STD_REGNO,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME,STD_DOB,STD_PH_NO,STD_ADDR_0,STD_ADDR_1,STD_ADDR_2,STD_ADDR_5,STD_IMAGE_PATH');
            $this->db->join('student_parent_details', 'student_parent_details.STD_SEQ = student_details.STD_SEQ', 'left');
            $this->db->join('states', 'states.state_id = student_details.STD_STATE', 'left');
            $this->db->order_by('STD_ROLLNO')->where_in("student_details.STD_SEQ", $std_id_arr);
            $std_arr = $this->db->get('student_details')->result_array();

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Identity Cards of Class ' . $cls->Class_Name . ' - ' . $cls->Sec_Name;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords($doc_name . ', smg, developed by: sketchmeglobal.com');

            // set default header data
            $pdf->setPrintHeader(false);

            // set header and footer fonts and size
//            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(3, 3, 3);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, 10);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // -----------------------------------------------------------------------------------------------------

            // set default font subsetting mode
            $pdf->setFontSubsetting(true);

            // Set font
            $pdf->SetFont('times', '', 11, '', true);

            // Add a page
            $pdf->AddPage('P', 'A4');

            $html = <<<EOD
<table>
<tr>
<td>
EOD;
            $even_odd=1;
            $count = 1;
            $section_per_page = 1;
            foreach ($std_arr as $v) { //this loop is for all selected students
                $dob = date('d-m-Y', strtotime($v['STD_DOB']));
                // table position
                if(!$even_odd%2){
                    $float_val = 'right';
                }else{
                    $float_val = 'left';
                }
                //6 section per page
                if($section_per_page > 6) {
                    $html .= '<br pagebreak="true"/>';
                    $html .= '</td></tr><br><tr><td>';
                    $section_per_page = 1;
                }

                $html .= <<<EOD
<table cellspacing="0" cellpadding="1" border="1" style="float:{$float_val};width:350px;height: 400px;">
<tr>
    <td align="center" width="75%">
        <span style="font-size: 13px; margin: 0;"><strong>$com</strong></span>
        <br>
        <span style="font-size: 10px; margin: 0;">$company->COM_ADD2 , $company->COM_CITY</span>
        <br>
        <span style="font-size: 15px; margin: 0;"><strong>IDENTITY CARD</strong></span>
    </td>
    <td align="center" width="25%" style="margin: 10px">
        <br/>
        <br style="line-height: 8px"/>
        <img src="$imgsrc" width="50px" height="50px" />
    </td>
</tr>
<tr>
<td align="center">    
    <div style="text-align: left;font-size: 13px">
    Name: <span style="font-size:12px"><strong>{$v['STD_FNAME']} {$v['STD_MNAME']} {$v['STD_LNAME']}</strong></span>
    <br>
    Class: <strong>$cls->Class_Name</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sec: <strong>$cls->Sec_Name</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Roll: <strong>{$v['STD_ROLLNO']}</strong>
    <br>
    Reg. No: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>{$v['STD_REGNO']}</strong>
    <br>
    Date of Birth: &nbsp;&nbsp;&nbsp; <strong>$dob</strong>
    <br>
    Father's Name: <span style="font-size: 11px; margin: 0;">&nbsp; <strong>{$v['STD_FTH_NAME']}</strong></span>
    <br>
    Phone No: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>{$v['STD_PH_NO']}</strong>
    </div>
    
</td>
<td align="center">
    <br/>
    <br style=""/>
    <img src="assets/img/students/{$v['STD_IMAGE_PATH']}" width="75px" height="75px" />
</td>
</tr>
<tr>
<td style="height:100px;" colspan="2" align="center">    
    <div style="text-align: left;font-size: 12px">
    Address:  
            <strong>
            {$v['STD_ADDR_0']} {$v['STD_ADDR_1']} {$v['STD_ADDR_2']} {$v['STD_ADDR_5']}
            </strong>
    </div>
    <span style="text-align: right;">
    <br>
    ____________
    <br>
    Headmaster
    </span>
</td>
</tr>

</table>
EOD;


                if ($count == 1) {
                    $html .= '</td><td>';
                    $count++;
                } else {
                    $html .= '</td></tr><br><tr><td>';
                    $count = 1;
                }

                $section_per_page++;
            }
            $html .= <<<EOD
</td>
</tr>
</table>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        }
        elseif($this->input->post('submit') == 'print_identity_card_blank') { //if form submitted
            $class = $this->input->post('class');

            $company = $this->db->get('company')->row();
            $this->db->where('CS_SEQ', $class);
            $cls = $this->db->get('class_sec_hdr')->row();

            $cls_type = $cls->Class_Type;
            $com = $this->db->get_where('company',array('SCHOOL_TYPE' => $cls_type))->row()->COM_NAME;

            if($cls_type == 1){
                $imgsrc = base_url('assets/img/SCHOOL_LOGO_NUR.jpg');
            }else{
                $imgsrc = base_url('assets/img/SCHOOL_LOGO.jpg');
            }
            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Identity Cards';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords($doc_name . ', smg, developed by: sketchmeglobal.com');

            // set default header data
            $pdf->setPrintHeader(false);

            // set header and footer fonts and size
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(3, 3, 3);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, 10);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // -----------------------------------------------------------------------------------------------------

            // set default font subsetting mode
            $pdf->setFontSubsetting(true);

            // Set font
            $pdf->SetFont('times', '', 11, '', true);

            // Add a page
            $pdf->AddPage('P', 'A4');

            $html = <<<EOD
<table>
<tr>
<td>
EOD;
            $even_odd=1;
            $count = 1;
            $section_per_page = 1;
            while ($section_per_page <= 6) {
                // table position
                if(!$even_odd%2){
                    $float_val = 'right';
                }else{
                    $float_val = 'left';
                }

                $html .= <<<EOD
<table cellspacing="0" cellpadding="1" border="1" style="float:{$float_val};width:350px;height: 400px;">
<tr>
    <td align="center" width="75%">
        <span style="font-size: 13px; margin: 0;"><strong>$com</strong></span>
        <br>
        <span style="font-size: 10px; margin: 0;">$company->COM_ADD2 , $company->COM_CITY</span>
        <br>
        <span style="font-size: 15px; margin: 0;"><strong>IDENTITY CARD</strong></span>
    </td>
    <td align="center" width="25%" style="margin: 10px">
        <br/>
        <br style="line-height: 8px"/>
        <img src="$imgsrc" width="50px" height="50px" />
    </td>
</tr>
<tr>
<td align="center">    
    <div style="text-align: left;font-size: 13px">
        <br>
        <br>
        <br>
        <br>
        <br>
    </div>
</td>
<td align="center">
    <br/>
    <br style=""/>
</td>
</tr>
<tr>
<td colspan="2" align="center">    
    <div style="text-align: left;font-size: 12px">
        <br>
    </div>
    <span style="text-align: right;">
    <br>
    ____________
    <br>
    Headmaster
    </span>
</td>
</tr>

</table>
EOD;


                if ($count == 1) {
                    $html .= '</td><td>';
                    $count++;
                } else {
                    $html .= '</td></tr><br><tr><td>';
                    $count = 1;
                }

                $section_per_page++;
            }
            $html .= <<<EOD
</td>
</tr>
</table>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        }
        else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/identity_card');
        }
    }

    public function routine() {
        try{

            $output = new \stdClass();
            $this->db->query("SET sql_mode = ''");
            $query = "SELECT CS_SEQ,rtn_id,class_id, Class_Name,Sec_Name FROM `routine` RIGHT JOIN class_sec_hdr ON class_sec_hdr.CS_SEQ = routine.class_id GROUP BY CS_SEQ,class_id ";
            $output->all_routines = $this->db->query($query)->result();


            /*if (count((array)$query) == 0) { //if routine for that class is not added yet
                $output->button = '<a href="'.base_url('admin/add_routine/'.$row->CS_SEQ).'" class="btn btn-info" role="button">
                        <span class="ui-button-text">&nbsp;Add</span>
                       </a>';
            } else {
                $output->button = '<a href="'.base_url('admin/edit_routine/'.$row->CS_SEQ).'" class="btn btn-info" role="button">
                            <span class="ui-button-text">&nbsp;Edit</span>
                        </a>';
            }*/

            $output->menu_name = 'Routine';

            return array('page'=>'common_master', 'data'=>$output); //loading master common view page

        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function routine_edit() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Students/routine_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Routine');
            $crud->order_by('CS_SEQ', 'ASC');
            $crud->set_table('class_sec_hdr');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_read();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $crud->columns('Class_Name','Sec_Name','Actions');

            $crud->callback_column('Actions', array($this, '_callback_routine_action_buttons'));

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Routine';
            $output->section_heading = 'Routine <small>(Add / Edit)</small>';
            $output->menu_name = 'Routine';
            $output->add_button = '<a href="'.base_url('admin/generate_routine').'" class="btn btn-success" role="button">Auto-Generate Routine (System Designed)</a><br><br>';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    public function _callback_routine_action_buttons($value, $row) {
        $this->db->where('class_id', $row->CS_SEQ);
        $query = $this->db->get('routine')->row();

        if (count((array)$query) == 0) { //if routine for that class is not added yet
            $button = '<a href="'.base_url('admin/add_routine/'.$row->CS_SEQ).'" class="edit_button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
                        <span class="ui-button-icon-primary ui-icon ui-icon-plus"></span>
                        <span class="ui-button-text">&nbsp;Add</span>
                    </a>';
        } else {
            $button = '<a href="'.base_url('admin/edit_routine/'.$row->CS_SEQ).'" class="edit_button ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button">
                        <span class="ui-button-icon-primary ui-icon ui-icon-pencil"></span>
                        <span class="ui-button-text">&nbsp;Edit</span>
                    </a>';
        }
        return $button;
    }

    public function generate_routine() {
        $days_arr = array('Monday','Tuesday','Wednesday','Thursday','Friday');
        $periods_arr = array('1','2','3','4','5','6','7','8');
        $this->db->select('CS_SEQ,Class_Name');
        $class_arr = $this->db->get('class_sec_hdr')->result_array();

        foreach ($class_arr as $cls) {
            //subjects of that class
            $this->db->select('CS_Sub_id');
            $this->db->where('CS_SEQ', $cls['CS_SEQ']);
            $this->db->where('CS_Sub_id !=', '20'); //except physical education
            $this->db->order_by('Sorting', 'ASC');
            $rs_subjects = $this->db->get('class_sub_link')->result_array();
            $subjects = array_column($rs_subjects,'CS_Sub_id');

            foreach ($days_arr as $day) {
                foreach ($periods_arr as $period) {
                    $tch_id = '';

                    //skip insert if routine already exists for that period
                    $this->db->where('day', $day);
                    $this->db->where('class_id', $cls['CS_SEQ']);
                    $this->db->where('period', $period);
                    $rs_routine = $this->db->get('routine')->result_array();
                    if(count($rs_routine) > 0) {
                        continue;
                    }

                    //wednesday 1st period Physical Education class for all classes
                    if($day == 'Wednesday' && $period == '1') {
                        $this->db->select('TCH_SRLNO');
                        $this->db->like('TCH_SUBJECTS', '20'); //physical education
                        $row_tch = $this->db->get('teacher')->row();
                        if(count($row_tch) > 0)  $tch_id = $row_tch->TCH_SRLNO;

                        $data_insert['day'] = $day;
                        $data_insert['class_id'] = $cls['CS_SEQ'];
                        $data_insert['period'] = $period;
                        $data_insert['sub_id'] = '20'; //physical education
                        $data_insert['tch_id'] = $tch_id;
                        //inserting data
                        $this->db->insert('routine', $data_insert);
                    }
                    else {
                        $sub_id = current($subjects);

                        //checkin if class for this subject already assign in that day
                        $unq_sub = false;
                        while($unq_sub == false) {
                            $this->db->where('day', $day);
                            $this->db->where('class_id', $cls['CS_SEQ']);
                            $this->db->where('sub_id', $sub_id);
                            $rs_sub = $this->db->get('routine')->result_array();
                            //if subject already assigned on that day
                            if(count($rs_sub) > 0) {
                                next($subjects);
                                if(current($subjects) == null) reset($subjects);
                                $sub_id = current($subjects);
                            }
                            //if subject not assigned on that day
                            else {
                                $unq_sub = true;
                            }
                        }

                        //1st period always taken by class teacher of that class
                        if($period == '1'){
                            $this->db->select('TCH_SRLNO');
                            $this->db->where('TCH_CS_SEQ', $cls['CS_SEQ']);
                            $row_tch = $this->db->get('teacher')->row();
                            if(count($row_tch) > 0) $tch_id = $row_tch->TCH_SRLNO;
                        }
                        else {
                            //select teachers of that class and subjects
                            $this->db->select('TCH_SRLNO');
                            $this->db->where("(TCH_CLASSES LIKE '".$cls['CS_SEQ']."' OR TCH_CLASSES LIKE '".$cls['CS_SEQ'].",%' OR TCH_CLASSES LIKE '%,".$cls['CS_SEQ'].",%' OR TCH_CLASSES LIKE '%,".$cls['CS_SEQ']."' OR TCH_CLASSES LIKE '')", NULL, FALSE);
                            $this->db->where("(TCH_SUBJECTS LIKE '".$sub_id."' OR TCH_SUBJECTS LIKE '".$sub_id.",%' OR TCH_SUBJECTS LIKE '%,".$sub_id.",%' OR TCH_SUBJECTS LIKE '%,".$sub_id."' OR TCH_SUBJECTS LIKE '')", NULL, FALSE);
                            $this->db->order_by('TCH_SUBJECTS', 'DESC');
                            $this->db->order_by('TCH_CLASSES', 'DESC');
                            $rs_tch = $this->db->get('teacher')->result_array();

                            //selecting a teacher of that cls and sub if his/her max class not exceed
                            foreach ($rs_tch as $tch) {
                                $this->db->where('TCH_SRLNO', $tch['TCH_SRLNO']);
                                $tch_max_cls = $this->db->get('teacher')->row()->TCH_MAX_CLS;

                                $this->db->select('rtn_id');
                                $this->db->where('tch_id', $tch['TCH_SRLNO']);
                                $this->db->group_by(array("day", "period", "sub_id", "tch_id"));
                                $rs_tch_cls_taken = $this->db->get('routine')->result_array();
                                $tch_cls_taken = count($rs_tch_cls_taken);

                                if($tch_max_cls > $tch_cls_taken) {
                                    $tch_id = $tch['TCH_SRLNO'];
                                    break;
                                }
                            }

                            //check if combined class
                            $this->db->where('sub_id', $sub_id);
                            $combination_cls = $this->db->get('subject')->row()->comb;
                            //if combination class
                            if($combination_cls == 1) {
                                $this->db->select('CS_SEQ');
                                $this->db->where('Class_Name', $cls['Class_Name']);
                                $this->db->where('CS_SEQ !=', $cls['CS_SEQ']);
                                $same_class_arr = $this->db->get('class_sec_hdr')->result_array();

                                foreach ($same_class_arr as $same_cls) {
                                    //skip insert if routine already exists for that period
                                    $this->db->where('day', $day);
                                    $this->db->where('class_id', $same_cls['CS_SEQ']);
                                    $this->db->where('period', $period);
                                    $rs_routine = $this->db->get('routine')->result_array();
                                    if(count($rs_routine) > 0) {
                                        continue;
                                    }

                                    $data_insert['day'] = $day;
                                    $data_insert['class_id'] = $same_cls['CS_SEQ'];
                                    $data_insert['period'] = $period;
                                    $data_insert['sub_id'] = $sub_id;
                                    $data_insert['tch_id'] = $tch_id;
                                    //inserting data
                                    $this->db->insert('routine', $data_insert);
                                }
                            }
                        }

                        $data_insert['day'] = $day;
                        $data_insert['class_id'] = $cls['CS_SEQ'];
                        $data_insert['period'] = $period;
                        $data_insert['sub_id'] = $sub_id;
                        $data_insert['tch_id'] = $tch_id;
                        //inserting data
                        $this->db->insert('routine', $data_insert);

                        next($subjects);
                        if(current($subjects) == null) reset($subjects);
                    }
                }
            }
        }

        $this->session->set_flashdata('type', 'success');
        $this->session->set_flashdata('title', 'Voila!');
        $this->session->set_flashdata('msg', 'Routine created for all classes.');
        return array('page'=>'admin/routine');
    }

    public function add_routine($cls_id) {
        $this->db->where('CS_SEQ', $cls_id);
        $cls = $this->db->get('class_sec_hdr')->row();
        //if class does not exists
        if(count($cls) == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oops!');
            $this->session->set_flashdata('msg', 'Class does not exists.');
            return array('type'=>'redirect', 'page'=>'admin/routine');
        }

        $this->db->where('class_id', $cls_id);
        $check = $this->db->get('routine')->row();
        //if routine for that class already added
        if(count($check) > 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Zzz!');
            $this->session->set_flashdata('msg', 'Routine for that class already added.');
            return array('type'=>'redirect', 'page'=>'admin/routine');
        }

        $days = array('Monday','Tuesday','Wednesday','Thursday','Friday');
        $this->db->select('sub_id,sub_name');
        $this->db->join('subject', 'subject.sub_id = class_sub_link.CS_Sub_id', 'left');
        $this->db->where('CS_SEQ', $cls_id);
        $this->db->order_by('Sorting');
        $subjects = $this->db->get('class_sub_link')->result_array();
        $this->db->select('TCH_SRLNO,TCH_NAME');
        $teachers = $this->db->get('teacher')->result_array();

        $data['class'] = $cls_id;
        $data['days'] = $days;
        $data['subjects'] = $subjects;;
        $data['teachers'] = $teachers;
        $data['form_type'] = 'add_routine';

        $data['tab_title'] = 'Add Routine';
        $data['section_heading'] = '<h4>Class & Section: <strong>'.$cls->Class_Name.' - '.$cls->Sec_Name.'</strong></h4>';
        $data['menu_name'] = 'Add Routine';

        return array('type'=>'load_view', 'page'=>'accounts_v', 'data'=>$data);
    }

    public function form_add_routine() {
        if($this->input->post('submit') == 'submit_add_routine') { //if form submitted
            $subject_arr = $this->input->post('subject');
            $teacher_arr = $this->input->post('teacher');
            $class_id = $this->input->post('class');

            $days = array('Monday','Tuesday','Wednesday','Thursday','Friday');

            //days loop
            foreach($days as $day) {
                //periods loop
                for($i=1; $i<=8; $i++) {
                    //subject 1, on the same period
                    unset($data_insert);
                    $data_insert['class_id'] = $class_id;
                    $data_insert['day'] = $day;
                    $data_insert['period'] = $i;
                    $data_insert['sub_no'] = 1;
                    $data_insert['sub_id'] = $subject_arr[$day][$i][1];
                    $data_insert['tch_id'] = $teacher_arr[$day][$i][1];
                    //inserting data
                    $this->db->insert('routine', $data_insert);

                    //subject 2, on the same period
                    unset($data_insert);
                    $data_insert['class_id'] = $class_id;
                    $data_insert['day'] = $day;
                    $data_insert['period'] = $i;
                    $data_insert['sub_no'] = 2;
                    $data_insert['sub_id'] = $subject_arr[$day][$i][2];
                    $data_insert['tch_id'] = $teacher_arr[$day][$i][2];
                    //inserting data
                    $this->db->insert('routine', $data_insert);
                }
            }

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Routine created.');
            return array('page'=>'admin/routine');
        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/routine');
        }
    }

    public function edit_routine($cls_id) {
        $this->db->where('CS_SEQ', $cls_id);
        $cls = $this->db->get('class_sec_hdr')->row();
        //if class does not exists
        if(count($cls) == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oops!');
            $this->session->set_flashdata('msg', 'Class does not exists.');
            return array('type'=>'redirect', 'page'=>'admin/routine');
        }

        $this->db->where('class_id', $cls_id);
        $check = $this->db->get('routine')->row();
        //if routine for that class not added yet
        if(count($check) == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Zzz!');
            $this->session->set_flashdata('msg', 'Routine for that class is not added yet.');
            return array('type'=>'redirect', 'page'=>'admin/routine');
        }

        $days = array('Monday','Tuesday','Wednesday','Thursday','Friday');
        $this->db->select('sub_id,sub_name');
        $this->db->join('subject', 'subject.sub_id = class_sub_link.CS_Sub_id', 'left');
        $this->db->where('CS_SEQ', $cls_id);
        $this->db->order_by('Sorting');
        $subjects = $this->db->get('class_sub_link')->result_array();
        $this->db->select('TCH_SRLNO,TCH_NAME');
        $teachers = $this->db->get('teacher')->result_array();
        $this->db->select('rtn_id,sub_id,tch_id,day,period,sub_no');
        $this->db->where('class_id', $cls_id);
        $routine = $this->db->get('routine')->result_array();

        $data['class'] = $cls_id;
        $data['days'] = $days;
        $data['subjects'] = $subjects;;
        $data['teachers'] = $teachers;
        $data['routine'] = $routine;
        $data['form_type'] = 'edit_routine';

        $data['tab_title'] = 'Edit Routine';
        $data['section_heading'] = '<h4>Class & Section: <strong>'.$cls->Class_Name.' - '.$cls->Sec_Name.'</strong></h4>';
        $data['menu_name'] = 'Edit Routine';

        return array('type'=>'load_view', 'page'=>'accounts_v', 'data'=>$data);
    }

    public function form_edit_routine() {
        if($this->input->post('submit') == 'submit_edit_routine') { //if form submitted
            $routine_arr = $this->input->post('rtn');
            $subject_arr = $this->input->post('subject');
            $teacher_arr = $this->input->post('teacher');

            //routine loop
            foreach($routine_arr as $v) {
                $data_update['sub_id'] = $subject_arr[$v];
                $data_update['tch_id'] = $teacher_arr[$v];

                //updating data
                $this->db->where('rtn_id', $v);
                $this->db->update('routine', $data_update);
            }

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Routine updated.');
            return array('page'=>'admin/routine');
        }else if($this->input->post('delete') == 'delete_edit_routine') {
            $class = $this->input->post('class');
         
            $this->db->where('class_id', $class);
            $this->db->delete('routine');
            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Routine deleted.');
            return array('page'=>'admin/routine');
        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/routine'); 
        } 
    }

    public function ajax_teacher_availability() {
        $tch_id = $this->input->post('tch_id');
        $cls_id = $this->input->post('cls_id');
        $sub_id = $this->input->post('sub_id');
        $day = $this->input->post('day');
        $period = $this->input->post('period');

        if($tch_id == ''){
            $array['status'] = '';
            return $array;
        }

        $this->db->where('sub_id', $sub_id);
        $rs_sub = $this->db->get('subject')->row();
        if(count($rs_sub) > 0){
            if($rs_sub->comb == 1){
                $array['status'] = '';
                return $array;
            }
        }

        $this->db->select('Class_Name,Sec_Name');
        $this->db->join("class_sec_hdr", 'class_sec_hdr.CS_SEQ = routine.class_id', 'left');
        $this->db->where("tch_id", $tch_id);
        $this->db->where("day", $day);
        $this->db->where('period', $period);
        $this->db->where('class_id !=', $cls_id);
        $row = $this->db->get('routine')->row();

        if(count($row) == 0){
            $array['status'] = '';
            return $array;
        }

        $array['status'] = 'booked';
        $array['msg'] = 'That teacher is already assigned for '.$row->Class_Name.' - '.$row->Sec_Name.' in this period.';
        return $array;
    }

    public function library() {
        try{

            $output = new \stdClass();
            $query = "SELECT lb_hdr_id,Class_Name,Sec_Name,date_issue FROM library_hdr LEFT JOIN class_sec_hdr ON class_sec_hdr.CS_SEQ = library_hdr.CS_SEQ";

            $output->menu_name = 'Library';
            $output->all_library  = $this->db->query($query)->result();
            //  print_r($data);
            return array('page'=>'common_master', 'data'=>$output); //loading common view page

        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }

    public function library_edit() {
        try{
            $crud = new grocery_CRUD();
            $crud->set_crud_url_path(base_url('admin_panel/Students/library_edit'));
            $crud->set_theme('flexigrid');
            $crud->set_subject('Library Transaction');
            $crud->order_by('CS_SEQ,date_issue', 'ASC');
            $crud->set_table('library_hdr');
            $crud->unset_export();
            $crud->unset_print();
            $crud->unset_clone();
            $crud->unset_read();
            $crud->unset_add();
            $crud->unset_edit();
            $crud->unset_delete();

            $crud->display_as('CS_SEQ', 'Class & Sec');
            $crud->display_as('date_issue', 'Date Issued');
            $crud->set_relation('CS_SEQ', 'class_sec_hdr', '{Class_Name} - {Sec_Name}');

            $crud->add_action('Edit', base_url('assets/grocery_crud/themes/flexigrid/css/images/edit.png'), 'admin/edit_library_tran','ui-icon-pencil');

            $output = $crud->render();
            //rending extra value to $output
            $output->tab_title = 'Library Transaction';
            $output->section_heading = 'Library Transaction <small>(Add / Edit)</small>';
            $output->menu_name = 'Library Transaction';
            $output->add_button = '<a href="'.base_url().'admin/add_library_tran" class="btn btn-success" role="button"><span class="fa fa-book"></span> Issue New Books</a>';

            return array('page'=>'common_v', 'data'=>$output); //loading common view page
        } catch(Exception $e) {
            show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }
    }
    public function add_library_tran() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'add_library_tran';

        $data['tab_title'] = 'New Book Issue';
        $data['section_heading'] = 'New Book Issue';
        $data['menu_name'] = 'New Book Issue';

        return array('page' => 'students_v', 'data' => $data);
    }

    public function ajax_update_std_table_data() {
        $class_id = $this->input->post('class_id');

        $this->db->select('BOOK_SEQ,Accession_No,Book_Name');
        $books = $this->db->get('book_master')->result_array();
        $this->db->select('STD_SEQ,STD_FNAME,STD_MNAME,STD_LNAME,STD_ROLLNO');
        $this->db->where('STD_CS_SEQ', $class_id);
        $this->db->where('STD_STATUS', 0);
        $this->db->order_by('STD_ROLLNO');
        $std = $this->db->get('student_details')->result_array();

        $html = '';
        $html_book = <<<EOD
                <select name="book_id[]" class="form-control round-input" >
                    <option value="">Select book</option>
EOD;
        foreach($books as $b) {
            $html_book .='<option value="'.$b['BOOK_SEQ'].'">'.$b['Accession_No'].' - '.$b['Book_Name'].'</option>';
        }
        $html_book .= '</select>';

        //creating individual student table row
        foreach($std as $s) {
            $html .= <<<EOD
<tr>
<td>{$s['STD_ROLLNO']}</td>
<td>{$s['STD_FNAME']} {$s['STD_MNAME']} {$s['STD_LNAME']}</td>
<td>$html_book</td>
<td><input name="return_date[]" class="return_date form-control round-input" value="" type="date" /></td>

<input name="std_id[]" value="{$s['STD_SEQ']}" type="hidden" />
</tr>
EOD;
        }

        return $html;
    }

    public function form_add_library_tran() {
        if($this->input->post('submit') == 'submit_add_library_tran') { //if form submitted
            $class_id = $this->input->post('class');
            $issue_date = $this->input->post('issue_date');
            $std_id_arr = $this->input->post('std_id[]');
            $book_id_arr = $this->input->post('book_id[]');
            $return_date_arr = $this->input->post('return_date[]');
            $total_row = count($std_id_arr);

            $this->db->where('CS_SEQ', $class_id);
            $cls_rs = $this->db->get('class_sec_hdr')->result_array();
            //if class does not exists
            if(count($cls_rs) < 1){
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Class does not exists.');
                return array('page'=>'admin/library');
            }
            //if no student in that class
            if($total_row < 1){
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'No student found for that class.');
                return array('page'=>'admin/library');
            }

            $get_auto_index = $this->db->query("SHOW TABLE STATUS LIKE 'library_hdr'")->row()->Auto_increment;

            $data_insert_hdr['CS_SEQ'] = $class_id;
            $data_insert_hdr['date_issue'] = $issue_date;
            //inserting data
            $this->db->insert('library_hdr', $data_insert_hdr);

            for($i=0; $i < $total_row; $i++) {
                $data_insert_dtl['lb_hdr_id'] = $get_auto_index;
                $data_insert_dtl['CS_SEQ'] = $class_id;
                $data_insert_dtl['STD_SEQ'] = $std_id_arr[$i];
                $data_insert_dtl['BOOK_SEQ'] = $book_id_arr[$i];
                $data_insert_dtl['date_issue'] = $issue_date;
                $data_insert_dtl['date_return'] = $return_date_arr[$i];
                //inserting data
                $this->db->insert('library_dtl', $data_insert_dtl);
            }

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Books successfully issued.');
            return array('page'=>'admin/library');
        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/library');
        }
    }

    public function edit_library_tran($hdr_id) {
        $this->db->where('lb_hdr_id', $hdr_id);
        $lb_hdr_rs = $this->db->get('library_hdr')->row();
        if(count($lb_hdr_rs) < 1){ //if library header does not exists
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'No transaction found.');
            return array('page'=>'admin/library');
        }

        $this->db->where('CS_SEQ', $lb_hdr_rs->CS_SEQ);
        $cls = $this->db->get('class_sec_hdr')->row();
        $this->db->select('BOOK_SEQ,Accession_No,Book_Name');
        $books = $this->db->get('book_master')->result_array();
        $this->db->select('lb_dtl_id,BOOK_SEQ,date_return,STD_FNAME,STD_MNAME,STD_LNAME,STD_ROLLNO');
        $this->db->join('student_details', 'student_details.STD_SEQ = library_dtl.STD_SEQ', 'left');
        $this->db->where('lb_hdr_id', $hdr_id);
        $lb_dtl_rs = $this->db->get('library_dtl')->result_array();

        $data['lb_hdr'] = $lb_hdr_rs;
        $data['lb_dtl'] = $lb_dtl_rs;
        $data['books'] = $books;
        $data['form_type'] = 'edit_library_tran';

        $data['tab_title'] = 'Edit Issued Book';
        $data['section_heading'] = 'Edit Issued Book';
        $data['section_heading'] = 'Class & Sec : <strong>'.$cls->Class_Name.' - '.$cls->Sec_Name.'</strong>';
        $data['menu_name'] = 'Edit Issued Book';

        return array('type' => 'load_view', 'page' => 'students_v', 'data' => $data);
    }

    public function form_edit_library_tran() {
        if($this->input->post('submit') == 'submit_edit_library_tran') { //if form submitted
            $issue_date = $this->input->post('issue_date');
            $book_id_arr = $this->input->post('book_id[]');
            $return_date_arr = $this->input->post('return_date[]');
            $lb_dtl_arr = $this->input->post('lb_dtl[]');
            $lb_hdr = $this->input->post('lb_hdr');

            $data_update_hdr['date_issue'] = $issue_date;
            //updating data
            $this->db->where('lb_hdr_id', $lb_hdr);
            $this->db->update('library_hdr', $data_update_hdr);

            foreach($lb_dtl_arr as $lb_dtl_id) {
                $data_update_dtl['BOOK_SEQ'] = $book_id_arr[$lb_dtl_id];
                $data_update_dtl['date_issue'] = $issue_date;
                $data_update_dtl['date_return'] = $return_date_arr[$lb_dtl_id];
                //updating data
                $this->db->where('lb_dtl_id', $lb_dtl_id);
                $this->db->update('library_dtl', $data_update_dtl);
            }

            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Successfully updated.');
            return array('page'=>'admin/library');
        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('page'=>'admin/library');
        }
    }


    /*Custom student Form*/

    public function add_student()
    {
        $cls = $this->db->get('class_sec_hdr')->result_array();
        $religion = $this->db->get('religion')->result();
        $sub = $this->db->get('subject')->result();

        $states = $this->db->get('states')->result();

        // STD_STATE

        $data['class'] = $cls;
        $data['states'] = $states;
        $data['religion'] = $religion;
        $data['sub'] = $sub;
        $data['form_type'] = 'student_add';

        //$this->db->select('STD_DOA');
        $this->db->order_by('STD_SEQ', 'DESC');
        $this->db->limit('1');
        $row_date = $this->db->get('student_details')->row();

        // echo "<pre>"; print_r($row_date); die();

        if(count((array)$row_date) > 0) $date = date('Y-m-d', strtotime($row_date->STD_DOA)); else $date = date('Y-m-d');
        $data['date_doa'] = $date;

        $data['tab_title'] = 'Add Student';
        $data['section_heading'] = 'Add Student <small>(Add)</small>';
        $data['menu_name'] = 'Add Student';

        return array('type' => 'load_view', 'page' => 'students_v', 'data' => $data);
    }





    public function check_reg_no() {
        $STD_REGNO = $this->input->post('STD_REGNO');
        $STD_SEQ = $this->input->post('STD_SEQ');
        if($STD_SEQ){
            $rs = $this->db->where('STD_SEQ !=', $STD_SEQ)->get_where('student_details', array('STD_REGNO' => $STD_REGNO))->num_rows();

            if($rs != 0) {
                $data = 'Registration no. already exists.';
            }else{
                $data='true';
            }
        }else{
            $rs = $this->db->get_where('student_details', array('STD_REGNO' => $STD_REGNO))->num_rows();
            // echo $this->db->last_query();die;
            if($rs != '0') {
                $data = 'Registration no. already exists.';
            }else{
                $data='true';
            }
        }


        return $data;
    }

    public function delete_student($st_id) {

        if($this->db->update('student_details', ['STD_STATUS'=>1], array('STD_SEQ' => $st_id))){
            $this->session->set_flashdata('type', 'success');
            $this->session->set_flashdata('msg', 'Successfully deleted.');
        }

        return array('type' => 'redirect', 'page' => 'admin/student_details');


    }
    public function form_add_student()
    {
        // echo "<pre>"; print_r($this->input->post()); die();

        $data = array();

        if ($this->input->post('submit') == 'add_student') {
            $insertArray = array();
            /* Name Work */

            if(empty($this->input->post('STD_MNAME'))){
                $stfullnm = $this->input->post('STD_FNAME') . ' ' . $this->input->post('STD_LNAME');
            }else{
                $stfullnm = $this->input->post('STD_FNAME') . ' '. $this->input->post('STD_MNAME') .' '. $this->input->post('STD_LNAME');
            }

            //  echo "<pre>"; print_r($this->input->post()); die();
            /*----------*/
            $insertArray['STD_REGNO'] = $this->input->post('STD_REGNO');

            $insertArray['STD_FNAME'] = $this->input->post('STD_FNAME');
            $insertArray['STD_MNAME'] = $this->input->post('STD_MNAME');
            $insertArray['STD_LNAME'] = $this->input->post('STD_LNAME');
            $insertArray['STD_CURRENT_SESSION'] = $this->input->post('STD_CURRENT_SESSION');
            $insertArray['ST_FULL_NAME'] =  $stfullnm;
            $insertArray['STD_EMAIL'] = $this->input->post('STD_EMAIL');
            $insertArray['STD_CS_SEQ'] = $this->input->post('STD_CS_SEQ');
            $insertArray['STD_DOB'] = $this->date_format($this->input->post('STD_DOB'));
            $insertArray['STD_DOA'] = $this->date_format($this->input->post('STD_DOA'));
            $insertArray['STD_ROLLNO'] = $this->input->post('STD_ROLLNO');
            $insertArray['STD_RC'] = $this->input->post('STD_RC');
            $insertArray['STD_CAT'] = $this->input->post('STD_CAT');
            $insertArray['STD_PHYDSBL'] = $this->input->post('STD_PHYDSBL');
            $insertArray['STD_CONSC'] = $this->input->post('STD_CONSC');
            $insertArray['STD_NAT'] = $this->input->post('STD_NAT');
            $insertArray['STD_RLGN'] = $this->input->post('STD_RLGN');
            $insertArray['STD_STATE'] = $this->input->post('STD_STATE');
            $insertArray['STD_LEFT'] = $this->input->post('STD_LEFT');
            $insertArray['STD_ADDR_0'] = $this->input->post('STD_ADDR_0');
            $insertArray['STD_PH_NO'] = $this->input->post('STD_PH_NO');
            $insertArray['STD_2LANG'] = $this->input->post('STD_2LANG');
            $insertArray['STD_3LANG'] = $this->input->post('STD_3LANG');
            $insertArray['STD_LAST_SCHOOL'] = $this->input->post('STD_LAST_SCHOOL');
            $insertArray['STD_LAST_CLASS'] = $this->input->post('STD_LAST_CLASS');
            $insertArray['STD_TC_NO'] = $this->input->post('STD_TC_NO');
            $insertArray['STD_TC_DT'] = $this->date_format($this->input->post('STD_TC_DT'));
            $insertArray['STD_DT_LV'] = $this->date_format($this->input->post('STD_DT_LV'));
            $insertArray['STD_PROMOTED'] = $this->input->post('STD_PROMOTED');
            $insertArray['STD_PRM'] = $this->input->post('STD_PRM');
            $insertArray['STD_BLDGRP'] = $this->input->post('STD_BLDGRP');
            $insertArray['STD_SEX'] = $this->input->post('STD_SEX');
            $insertArray['PoT_PoW'] = $this->input->post('pot_pow');
            $insertArray['aadhaar_id'] = $this->input->post('aadhaar_id');
            $insertArray['STD_SECOND_LANG'] = $this->input->post('second_language');
            $insertArray['STD_THIRD_LANG'] = $this->input->post('third_language');
            $insertArray['banglar_shiksha_id'] = $this->input->post('banglar_shiksha_id');

            if(isset($_FILES['STD_IMAGE_PATH'])){
                $upload_path = 'assets/img/students/' ;
                $file_type = 'jpg|jpeg|png';
                $user_file_name = 'STD_IMAGE_PATH';
                $return_data = $this->_upload_files($_FILES['STD_IMAGE_PATH'], $upload_path, $file_type, $user_file_name);
                if($return_data['status'] == 'success'){
                    $insertArray['STD_IMAGE_PATH'] = $return_data['filename'];
                }else{
                    $return_data['msg'];
                }
            }

            if($this->db->insert('student_details', $insertArray)){
                $std_id = $this->db->insert_id();

                //add initial concession fees
                if($this->input->post('STD_CONSC') == 1) {
                    $class_id = $this->input->post('STD_CS_SEQ');

                    $this->db->where('CS_SEQ', $class_id);
                    $fees_rs = $this->db->get('class_sec_dtl')->result_array();

                    foreach($fees_rs as $fee) {
                        unset($data_insert);
                        $data_insert['std_id'] = $std_id;
                        $data_insert['class_id'] = $class_id;
                        $data_insert['ACC_MASTER_CODE'] = $fee['ACC_MASTER_CODE'];
                        $data_insert['Fees'] = $fee['Fees'];

                        //inserting data
                        $this->db->insert('fees_concession', $data_insert);
                    }
                }

                //insert parent details
                $insertParentArray = array();
                $insertParentArray['STD_FTH_NAME'] = $this->input->post('STD_FTH_NAME');
                $insertParentArray['STD_MTH_NAME'] = $this->input->post('STD_MTH_NAME');
                $insertParentArray['STD_GRD_NAME'] = $this->input->post('STD_GRD_NAME');
                $insertParentArray['STD_FTH_PHNO'] = $this->input->post('STD_FTH_PHNO');
                $insertParentArray['STD_FTH_OCP'] = $this->input->post('STD_FTH_OCP');
                $insertParentArray['STD_SEQ'] = $std_id;

                if($this->db->insert('student_parent_details', $insertParentArray)){
                    $this->session->set_flashdata('type', 'success');
                    $this->session->set_flashdata('title', 'Zaap!');
                    $this->session->set_flashdata('msg', "Student Registration successfully.");
                    return array('page' => 'admin/student_details');
                }

            }

        }
    }


    public function edit_student($st_id)
    {
        $cls = $this->db->get('class_sec_hdr')->result_array();
        $religion = $this->db->get('religion')->result();
        $sub = $this->db->get('subject')->result();
        $this->db->select('student_details.*, student_parent_details.STD_FTH_NAME, student_parent_details.STD_MTH_NAME, student_parent_details.STD_GRD_NAME, student_parent_details.STD_FTH_PHNO, student_parent_details.STD_FTH_OCP');
        $this->db->join('student_parent_details','student_parent_details.STD_SEQ=student_details.STD_SEQ','left');
        $student_details = $this->db->get_where('student_details', array('student_details.STD_SEQ'=>$st_id))->row();


        $states = $this->db->get('states')->result();


        $data['states'] = $states;
        $data['class'] = $cls;
        $data['religion'] = $religion;
        $data['sub'] = $sub;
        $data['student_details'] = $student_details;
        $data['form_type'] = 'student_edit';

        $data['tab_title'] = 'Edit Student';
        $data['section_heading'] = 'Edit Student <small>(Edit)</small>';
        $data['menu_name'] = 'Edit Student';

        // echo "<pre>"; print_r($data); die();

        return array('type' => 'load_view', 'page' => 'students_v', 'data' => $data);
    }

    public function form_edit_student()
    {
        // echo "<pre>"; print_r($this->input->post()); die();

        $data = array();

        if ($this->input->post('submit') == 'edit_student') {
            $st_id = $this->input->post('STD_SEQ');
            $student_details = $this->db->get_where('student_details', array('STD_SEQ'=>$st_id))->row();

            // echo "<pre>"; print_r($student_details); die();
            $updateArray = array();
            /* Name Work */

            //   echo "<pre>"; print_r($this->input->post()); die();

            /*error_reporting(E_ALL);
            ini_set('display_errors', 1);*/


            if(empty($this->input->post('STD_MNAME'))){
                $stfullnm = $this->input->post('STD_FNAME') . ' ' . $this->input->post('STD_LNAME');
            }else{
                $stfullnm = $this->input->post('STD_FNAME') . ' '. $this->input->post('STD_MNAME') .' '. $this->input->post('STD_LNAME');
            }

            /*----------*/
            $updateArray['STD_REGNO'] = $this->input->post('STD_REGNO');


            if(isset($_FILES['STD_IMAGE_PATH'])){

                $upload_path = 'assets/img/students/' ;
                $file_type = 'jpg|jpeg|png';
                $user_file_name = 'STD_IMAGE_PATH';
                $return_data = $this->_upload_files($_FILES['STD_IMAGE_PATH'], $upload_path, $file_type, $user_file_name);
                if($return_data['status'] == 'success'){
                    @unlink($upload_path.$student_details->STD_IMAGE_PATH);
                    $updateArray['STD_IMAGE_PATH'] = $return_data['filename'];
                }else{
                    $return_data['msg'];
                }


            }
            $updateArray['STD_FNAME'] = $this->input->post('STD_FNAME');
            $updateArray['STD_MNAME'] = $this->input->post('STD_MNAME');
            $updateArray['STD_LNAME'] = $this->input->post('STD_LNAME');
            $updateArray['ST_FULL_NAME'] =  $stfullnm;
            $updateArray['STD_EMAIL'] = $this->input->post('STD_EMAIL');
            $updateArray['STD_CURRENT_SESSION'] = $this->input->post('STD_CURRENT_SESSION');
            $updateArray['STD_CS_SEQ'] = $this->input->post('STD_CS_SEQ');
            $updateArray['STD_DOB'] = $this->date_format($this->input->post('STD_DOB'));
            $updateArray['STD_DOA'] = $this->date_format($this->input->post('STD_DOA'));
            $updateArray['STD_ROLLNO'] = $this->input->post('STD_ROLLNO');
            $updateArray['STD_RC'] = $this->input->post('STD_RC');
            $updateArray['STD_CAT'] = $this->input->post('STD_CAT');
            $updateArray['STD_PHYDSBL'] = $this->input->post('STD_PHYDSBL');
            $updateArray['STD_CONSC'] = $this->input->post('STD_CONSC');
            $updateArray['STD_NAT'] = $this->input->post('STD_NAT');
            $updateArray['STD_RLGN'] = $this->input->post('STD_RLGN');
            $updateArray['STD_STATE'] = $this->input->post('STD_STATE');
            $updateArray['STD_ADDR_0'] = $this->input->post('STD_ADDR_0');
            $updateArray['STD_PH_NO'] = $this->input->post('STD_PH_NO');
            $updateArray['STD_2LANG'] = $this->input->post('STD_2LANG');
            $updateArray['STD_3LANG'] = $this->input->post('STD_3LANG');
            $updateArray['STD_LAST_SCHOOL'] = $this->input->post('STD_LAST_SCHOOL');
            $updateArray['STD_LAST_CLASS'] = $this->input->post('STD_LAST_CLASS');
            $updateArray['STD_TC_NO'] = $this->input->post('STD_TC_NO');
            $updateArray['STD_TC_DT'] = $this->date_format($this->input->post('STD_TC_DT'));
            $updateArray['STD_DT_LV'] = $this->date_format($this->input->post('STD_DT_LV'));
            $updateArray['STD_PROMOTED'] = $this->input->post('STD_PROMOTED');
            $updateArray['STD_LEFT'] = $this->input->post('STD_LEFT');
            $updateArray['STD_PRM'] = $this->input->post('STD_PRM');
            $updateArray['STD_BLDGRP'] = $this->input->post('STD_BLDGRP');
            $updateArray['STD_SEX'] = $this->input->post('STD_SEX');
            $updateArray['STD_PRM'] = $this->input->post('STD_PRM');
            $updateArray['PoT_PoW'] = $this->input->post('pot_pow');
            $updateArray['aadhaar_id'] = $this->input->post('aadhaar_id');
            $updateArray['banglar_shiksha_id'] = $this->input->post('banglar_shiksha_id');
            $updateArray['STD_STATUS'] = $this->input->post('STD_STATUS');
            $updateArray['STD_SECOND_LANG'] = $this->input->post('second_language');
            $updateArray['STD_THIRD_LANG'] = $this->input->post('third_language');



            if($this->db->update('student_details', $updateArray, array('STD_SEQ'=>$st_id))){

                //add initial concession fees
                if($this->input->post('STD_CONSC') == 1) {
                    $class_id = $this->input->post('STD_CS_SEQ');

                    $this->db->where('std_id', $st_id);
                    $this->db->where('class_id', $class_id);
                    $result = $this->db->get('fees_concession')->result_array();

                    //insert fees
                    if(count($result) == 0) {
                        $this->db->where('CS_SEQ', $class_id);
                        $fees_rs = $this->db->get('class_sec_dtl')->result_array();

                        foreach ($fees_rs as $fee) {
                            unset($data_insert);
                            $data_insert['std_id'] = $st_id;
                            $data_insert['class_id'] = $class_id;
                            $data_insert['ACC_MASTER_CODE'] = $fee['ACC_MASTER_CODE'];
                            $data_insert['Fees'] = $fee['Fees'];

                            //inserting data
                            $this->db->insert('fees_concession', $data_insert);
                        }
                    }

                }

                $updateParentArray = array();
                $updateParentArray['STD_FTH_NAME'] = $this->input->post('STD_FTH_NAME');
                $updateParentArray['STD_MTH_NAME'] = $this->input->post('STD_MTH_NAME');
                $updateParentArray['STD_GRD_NAME'] = $this->input->post('STD_GRD_NAME');
                $updateParentArray['STD_FTH_PHNO'] = $this->input->post('STD_FTH_PHNO');
                $updateParentArray['STD_FTH_OCP'] = $this->input->post('STD_FTH_OCP');
                
                $this->db->where('STD_SEQ',$st_id);
                $query = $this->db->get('student_parent_details');
                $parent = $query->row();
                if(empty($parent)){
                     $updateParentArray['STD_SEQ'] = $st_id;
                     if( $this->db->insert('student_parent_details', $updateParentArray)){
                        $this->session->set_flashdata('type', 'success');
                        $this->session->set_flashdata('title', 'Zaap!');
                        $this->session->set_flashdata('msg', "Data update successfully.");
                        return array('page' => 'admin/student_details');
                    }
                }else{
                     if( $this->db->update('student_parent_details', $updateParentArray, array('STD_SEQ'=>$st_id))){
                        $this->session->set_flashdata('type', 'success');
                        $this->session->set_flashdata('title', 'Zaap!');
                        $this->session->set_flashdata('msg', "Data update successfully.");
                        return array('page' => 'admin/student_details');
                    }
                }

               

            }

        }
    }

    private function date_format($date)
    {
        $newDate = date("Y-m-d", strtotime($date));

        return $newDate;
    }

    private function _upload_files($files, $upload_path, $file_type, $user_file_name){

        // date_default_timezone_set("Asia/Kolkata");

        $uploadedFileData = array();

        $config = array(
            'upload_path'   => $upload_path,
            'allowed_types' => $file_type,
            'overwrite'     => 1,
        );

        $this->load->library('upload', $config);

        //foreach ($files['name'] as $key => $image) {

        $_FILES['file']['name']       = rand(1000,9999).$_FILES[$user_file_name]['name'];
        $_FILES['file']['type']       = $_FILES[$user_file_name]['type'];
        $_FILES['file']['tmp_name']   = $_FILES[$user_file_name]['tmp_name'];
        $_FILES['file']['error']      = $_FILES[$user_file_name]['error'];
        $_FILES['file']['size']       = $_FILES[$user_file_name]['size'];

        // $config['file_name'] = date('His') .'_'. $image;

        $this->upload->initialize($config);

        if ($this->upload->do_upload('file')) {

            $imageData = $this->upload->data();

            $new_array = array(
                'filename' => $imageData['file_name'],
                'status' => 'success',
                'msg' => 'OK'
            );

            $final_array = array_merge($uploadedFileData, $new_array);

        } else {
            $new_array = array(
                'filename' => null,
                'status' => 'error',
                'msg' => 'Type or Size Mismatch'
            );

            $final_array = array_merge($uploadedFileData, $new_array);
        }
        //}

        return $final_array;
    }

    private function custom_left_zero_pad($num){
        if(strlen($num) == 1){
            $num = sprintf('%04d', $num);
        }else if(strlen($num) == 2){
            $num = sprintf('%03d', $num);
        }else if(strlen($num) == 3){
            $num = sprintf('%02d', $num);
        }else if(strlen($num) == 4){
            $num = sprintf('%01d', $num);
        }
        return $num;
    }

} // /.Students_m model