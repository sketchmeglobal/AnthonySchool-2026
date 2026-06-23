<?php
/**
 * Coded by: Pran Krishna Das
 * Social: https://sketchmeglobal.com
 * CI: 3.0.6
 * Date: 14-01-2019
 * Time: 23:07
 */

class Reports_m extends CI_Model {

    public function __construct() {
        parent::__construct();

        // echo
        $this->db->query("SET sql_mode = ' ' "); 
        error_reporting(0);
        @ini_set('display_errors', 0);
    }

    public function std_reg_report() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'std_reg_report';

        $data['tab_title'] = 'Student Registration Report';
        $data['section_heading'] = 'Student Registration Report <small>(Print)</small>';
        $data['menu_name'] = 'Student Registration Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
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

public function print_std_reg_report() {
    if($this->input->post('submit') == 'print_std_reg_report') { //if form submitted
        $class = $this->input->post('class[]');
        $rpt_type = $this->input->post('rpt_type');
        $adm_year = $this->input->post('adm_year');
        
        // NEW FILTER VARIABLES
        $reg_number = $this->input->post('reg_number');
        $telephone = $this->input->post('telephone');
        $official_telephone = $this->input->post('official_telephone');
       
        //echo "<pre>"; print_r($this->input->post()); die();

        $company = $this->company_name($class);
        if(!in_array("all", $class)) {
            $this->db->where_in('CS_SEQ', $class);
        }
        $class = $this->db->get('class_sec_hdr')->result();

        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $doc_name = 'Student Registration Report';
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company->COM_NAME);
        $pdf->SetTitle($doc_name);
        $pdf->SetSubject('Student Registration Report');
        $pdf->SetKeywords('Student Registration Report, smg, developed by: https://sketchmeglobal.com');

        // set default header data
        $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2 , $company->COM_CITY
        <br>
        <strong><span style="background-color: black;color: white;"> Student Registration Report </span></strong>
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

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        $pdf->SetFont('times', '', 8, '', true);

        // Add a page
        $pdf->AddPage('L', 'A4');

        // Set some content to print
        $html = '';
        $count = 0;

        foreach ($class as $cls) {
            // HELPER FUNCTION TO APPLY ADDITIONAL FILTERS
            $this->apply_additional_filters($reg_number, $telephone, $official_telephone, $adm_year);
            
            if ($this->input->post('rpt_type')) {
                if ($this->input->post('rpt_type') == "tel") {
                    $this->db->select('STD_ROLLNO,ST_FULL_NAME,class_sec_hdr.class_sec as class_sec, STD_PH_NO');
                    $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
                    $this->db->where("STD_LEFT", 0);
                    $this->db->where('STD_STATUS', 0);
                    $this->db->where('STD_CS_SEQ', $cls->CS_SEQ);
                    
                    // APPLY ADDITIONAL FILTERS
                    $this->apply_additional_filters($reg_number, $telephone, $official_telephone, $adm_year);
                    
                    $this->db->order_by("STD_ROLLNO");
                    $std = $this->db->get('student_details')->result();
                    $tot_std = count($std);

                    if ($tot_std > 0) {
                        $count++;
                        $html .= '<span style="font-size: 15px;">Class & Section: <strong>' . $cls->Class_Name . ' - ' . $cls->Sec_Name . '</strong></span>
                            <span style="font-size: 11px;">(Total Students: <strong>' . $tot_std . ')</strong></span>
                            <hr>';
                        $html .= <<<EOD
                    <br>
                    <table cellspacing="2" style="100%">
                        <thead>
                        <tr>
                            <th ><strong>Roll#</strong></th>
                            <th ><strong>Student Name</strong></th>
                            <th ><strong>Class-Sec</strong></th>
                            <th ><strong>Phone(Office)</strong></th>
                        </tr>
                        </thead>
                        <tbody>
EOD;
                        $sr_no = 1;
                        foreach ($std as $ind=>$val) {
                            if ($ind == 0) {
                                $html .= '<hr>';
                            }
                            $html .= '<tr>
                                    <td>'.$val->STD_ROLLNO.'</td>
                                    <td>' .$val->ST_FULL_NAME.'</td>
                                    <td>' . $val->class_sec . '</td>
                                    <td>' . $val->STD_PH_NO . '</td>
                                </tr><hr> &nbsp;';
                            $sr_no++;
                        }
                        $html .= <<<EOD
                            </tbody>
                        </table>
                        <hr>
                        &nbsp;
                        <br>
                        <br>
EOD;
                    }
                }
                else if($this->input->post('rpt_type') == "dob"){
                    $month = $this->input->post('dob_month');

                    $this->db->select('STD_ROLLNO,ST_FULL_NAME, STD_DOB');
                    $this->db->where("STD_LEFT", 0);
                    $this->db->where('STD_STATUS', 0);
                    $this->db->where('STD_CS_SEQ', $cls->CS_SEQ);
                    
                    // APPLY ADDITIONAL FILTERS
                    $this->apply_additional_filters($reg_number, $telephone, $official_telephone, $adm_year);
                    
                    if($month != "all"){
                        $this->db->where('MONTH(STD_DOB)', $month);
                    }
                    $this->db->order_by("STD_ROLLNO");
                    $std = $this->db->get('student_details')->result();
                    $tot_std = count($std);

                    if ($tot_std > 0) {
                        $count++;
                        $html .= '<span style="font-size: 15px;">Class & Section: <strong>' . $cls->Class_Name . ' - ' . $cls->Sec_Name . '</strong></span>
                                <span style="font-size: 11px;">(Total Students: <strong>' . $tot_std . ')</strong></span>
                                <hr>';
                        $html .= <<<EOD
                        <br>
                        <table cellspacing="2" style="100%">
                            <thead>
                            <tr>
                                <th><strong>Roll#</strong></th>
                                <th><strong>Student Name</strong></th>
                                <th><strong>Date of Birth</strong></th>
                            </tr>
                            </thead>
                            <tbody>
EOD;
                        foreach ($std as $ind=>$val) {
                            if ($ind == 0) {
                                $html .= '<hr>';
                            }
                            $html .= '<tr>
                                        <td>'.$val->STD_ROLLNO.'</td>
                                        <td>' .$val->ST_FULL_NAME.'</td>
                                        <td>' . date("d-m-Y", strtotime($val->STD_DOB)) . '</td>
                                    </tr>';
                            if ($this->input->post('class') == 'all') {
                                if (count($std) == $ind) {
                                    $html .='<hr> &nbsp;';
                                }
                            }else{
                                $html .='<hr> &nbsp;';
                            }
                        }
                        $html .= <<<EOD
                                </tbody>
                            </table>
EOD;
                    }
                }
                else if ($this->input->post('rpt_type') == "pro") {
                    $this->db->select('STD_ROLLNO,ST_FULL_NAME, STD_REGNO, STD_PRM');
                    $this->db->where("STD_LEFT", 0);
                    $this->db->where('STD_STATUS', 0);
                    $this->db->where('STD_CS_SEQ', $cls->CS_SEQ);
                    
                    // APPLY ADDITIONAL FILTERS
                    $this->apply_additional_filters($reg_number, $telephone, $official_telephone, $adm_year);
                    
                    $this->db->order_by("STD_ROLLNO");
                    $std = $this->db->get('student_details')->result();
                    $tot_std = count($std);

                    if ($tot_std > 0) {
                        $count++;
                        $html .= '<span style="font-size: 15px;">Class & Section: <strong>' . $cls->Class_Name . ' - ' . $cls->Sec_Name . '</strong></span>
                                <span style="font-size: 11px;">(Total Students: <strong>' . $tot_std . ')</strong></span>
                                <hr>';
                        $html .= <<<EOD
                        <br>
                        <table cellspacing="2" style="100%">
                            <thead>
                            <tr>
                                <th><strong>Roll#</strong></th>
                                <th><strong>Student Name</strong></th>
                                <th><strong>Reg.No</strong></th>
                                <th><strong>Promotion</strong></th>
                            </tr>
                            </thead>
                            <tbody>
EOD;
                        foreach ($std as $ind=>$val) {
                            if ($ind == 0) {
                                $html .= '<hr>';
                            }
                            if ($val->STD_PRM == 1) $promo = "Granted"; else $promo = "Not Granted";
                            $html .= '<tr>
                                        <td>'.$val->STD_ROLLNO.'</td>
                                        <td>' .$val->ST_FULL_NAME.'</td>
                                        <td>' .$val->STD_REGNO.'</td>
                                        <td>' .$promo.'</td>
                                    </tr><hr>';
                        }
                        $html .= <<<EOD
                                </tbody>
                            </table>
EOD;
                    }
                }
                else if ($this->input->post('rpt_type') == "aadhaar") {
                    $this->db->select('STD_ROLLNO,ST_FULL_NAME,STD_REGNO,class_sec_hdr.class_sec as class_sec, aadhaar_id, banglar_shiksha_id');
                    $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
                    $this->db->where("STD_LEFT", 0);
                    $this->db->where('STD_STATUS', 0);
                    $this->db->where('STD_CS_SEQ', $cls->CS_SEQ);
                    
                    // APPLY ADDITIONAL FILTERS
                    $this->apply_additional_filters($reg_number, $telephone, $official_telephone, $adm_year);
                    
                    $this->db->order_by("STD_ROLLNO");
                    $std = $this->db->get('student_details')->result();
                    $tot_std = count($std);

                    if ($tot_std > 0) {
                        $count++;
                        $html .= '<span style="font-size: 15px;">Class & Section: <strong>' . $cls->Class_Name . ' - ' . $cls->Sec_Name . '</strong></span>
                            <span style="font-size: 11px;">(Total Students: <strong>' . $tot_std . ')</strong></span>
                            <hr>';
                        $html .= <<<EOD
                    <br>
                    <table cellspacing="2" style="100%">
                        <thead>
                        <tr>
                            <th ><strong>Roll#</strong></th>
                            <th ><strong>Student Name</strong></th>
                            <th><strong>Reg.No</strong></th>
                            <th ><strong>Class-Sec</strong></th>
                            <th ><strong>Aadhaar ID</strong></th>
                            <th ><strong>Banglar Shiksha ID</strong></th>
                        </tr>
                        </thead>
                        <tbody>
EOD;
                        $sr_no = 1;
                        foreach ($std as $ind=>$val) {
                            if ($ind == 0) {
                                $html .= '<hr>';
                            }
                            $html .= '<tr>
                                    <td>'.$val->STD_ROLLNO.'</td>
                                    <td>' .$val->ST_FULL_NAME.'</td>
                                    <td>' .$val->STD_REGNO.'</td>
                                    <td>' . $val->class_sec . '</td>
                                    <td>' . $val->aadhaar_id . '</td>
                                    <td>' . $val->banglar_shiksha_id . '</td>
                                </tr><hr> &nbsp;';
                            $sr_no++;
                        }
                        $html .= <<<EOD
                            </tbody>
                        </table>
                        <hr>
                        &nbsp;
                        <br>
                        <br>
EOD;
                    }
                }
            }else{
                $this->db->select('STD_REGNO,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME,STD_SEX,STD_DOB,STD_PH_NO,STD_DOA,STD_ADDR_0,STD_FTH_NAME,religion.name as religion');
                $this->db->join('student_parent_details', 'student_parent_details.STD_SEQ = student_details.STD_SEQ', 'left');
                $this->db->join('religion', 'religion.religion_id = student_details.STD_RLGN', 'left');
                $this->db->where("STD_LEFT", 0);
                $this->db->where('STD_STATUS', 0);
                $this->db->where('STD_CS_SEQ', $cls->CS_SEQ);
                
                // APPLY ADDITIONAL FILTERS
                $this->apply_additional_filters($reg_number, $telephone, $official_telephone, $adm_year);
                
                $this->db->group_by([
                    'STD_ROLLNO', 'STD_REGNO', 'STD_FNAME', 'STD_MNAME', 
                    'STD_LNAME', 'STD_SEX', 'STD_DOB', 'STD_PH_NO', 
                    'STD_DOA', 'STD_ADDR_0', 'STD_FTH_NAME', 'religion.name'
                ]);
                $this->db->order_by("STD_ROLLNO");
                $std = $this->db->get('student_details')->result();
                $tot_std = count($std);
                if ($tot_std > 0) {
                    $count++;
                    $html .= '<span style="font-size: 15px;">Class & Section: <strong>' . $cls->Class_Name . ' - ' . $cls->Sec_Name . '</strong></span>
                            <span style="font-size: 11px;">(Total Students: <strong>' . $tot_std . ')</strong></span>
                            <hr>';
                    $html .= <<<EOD
                    <br>
                    <table cellspacing="3" cellpadding="2" >
                        <thead>
                        <tr>
                            <th width="25"><strong>#</strong></th>
                            <th width="50"><strong>Reg. No</strong></th>
                            <th width="40" align="center"><strong>Roll</strong></th>
                            <th width="120"><strong>Student Name</strong></th>
                            <th width="45" align="center"><strong>Gender</strong></th>
                            <th width="60" align="center"><strong>Dt. of Birth</strong></th>
                            <th width="60"><strong>Religion</strong></th>
                            <th width="75"><strong>Phone No</strong></th>
                            <th width="120"><strong>Father's Name</strong></th>
                            <th width="75" align="center"><strong>Dt. of Admission</strong></th>
                            <th width="200"><strong>Address</strong></th>
                        </tr>
                        </thead>
                        <tbody>
EOD;
                    $sr_no = 1;
                    foreach ($std as $val) {
                        if ($val->STD_SEX == 1) $gender = "M"; else $gender = "F";
                        $html .= '<tr>
                                    <td width="25">' . $sr_no . '</td>
                                    <td width="50">' . $val->STD_REGNO . '</td>
                                    <td width="40" align="center">' . $val->STD_ROLLNO . '</td>
                                    <td width="120">' . $val->STD_FNAME . ' ' . $val->STD_MNAME . ' ' . $val->STD_LNAME . '</td>
                                    <td width="45" align="center">' . $gender . '</td>
                                    <td width="60" align="center">' . date("d-m-Y", strtotime($val->STD_DOB)) . '</td>
                                    <td width="60">' . $val->religion . '</td>
                                    <td width="75">' . $val->STD_PH_NO . '</td>
                                    <td width="120">' . $val->STD_FTH_NAME . '</td>
                                    <td width="75" align="center">' . date("d-m-Y", strtotime($val->STD_DOA)) . '</td>
                                    <td width="200">' . $val->STD_ADDR_0 . '</td>
                                </tr>';
                        $sr_no++;
                    }
                    $html .= <<<EOD
                            </tbody>
                        </table>
                        <hr>
                        &nbsp;
                        <br>
                        <br>
EOD;
                }
            }
            if ($count != count($class)) {
                $html .= <<<EOD
                        <br pagebreak="true"/>
EOD;
            }
        }
        if($count == 0) {
            $this->session->set_flashdata('type', 'warning');
            $this->session->set_flashdata('title', 'Zzz!');
            $this->session->set_flashdata('msg', 'No student found.');
            return array('type' => 'redirect', 'page'=>'admin/std_reg_report');
        }
        
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // Close and output PDF document
        $pdf->Output($doc_name . '.pdf', 'I'); 
    } else {
        $this->session->set_flashdata('type', 'error');
        $this->session->set_flashdata('title', 'Oh Snap!');
        $this->session->set_flashdata('msg', 'Something went wrong.');
        return array('type' => 'redirect', 'page'=>'admin/std_reg_report');
    }
}

// NEW HELPER METHOD TO APPLY ADDITIONAL FILTERS
private function apply_additional_filters($reg_number, $telephone, $official_telephone, $adm_year) {
    // Registration number filter
    if (!empty($reg_number)) {
        $this->db->like('STD_REGNO', $reg_number);
    }
    
    // Telephone number filter (matches STD_PH_NO field)
    if (!empty($telephone)) {
        $this->db->like('STD_PH_NO', $telephone);
    }
    
    // Official telephone filter (assuming this is also STD_PH_NO or you can modify for different field)
    if (!empty($official_telephone)) {
        $this->db->like('STD_PH_NO', $official_telephone);
    }
    
    // Admission year filter (existing logic)
    if($adm_year){
        $this->db->where('RIGHT(STD_REGNO,2)', $adm_year);
    }
}

    public function std_consc_report() {

        $cls = $this->db->get('class_sec_hdr')->result_array();
        $class_type = $this->db->get('class_type')->result_array();

        $data['class_type'] = $class_type;
        $data['class'] = $cls;
        $data['form_type'] = 'std_consc_report';

        $data['tab_title'] = 'Student Concession Report';
        $data['section_heading'] = 'Student Concession Report <small>(Print)</small>';
        $data['menu_name'] = 'Student Concession Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function print_std_consc_report() {
        if($this->input->post('submit') == 'print_std_consc_report') { //if form submitted
            $class = $this->input->post('class');
            $class_type = $this->input->post('class_type');

            $company = $this->company_name((array)$class);

            $this->db->select('STD_SEQ,STD_CS_SEQ,STD_REGNO,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME,Class_Name,Sec_Name');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where("STD_CONSC", 1);
            $this->db->where("STD_LEFT", 0);
            $this->db->where_in('STD_CS_SEQ', $class);
            $this->db->order_by("Class_Name,Sec_Name,STD_ROLLNO");
            $std = $this->db->get('student_details')->result_array();

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Student Concession Report';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('Student Concession Report');
            $pdf->SetKeywords('Student Concession Report, smg, developed by: https://sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
            <div style="text-align:center;">
            <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
            <br>
            $company->COM_ADD2 , $company->COM_CITY
            <br>
            <strong><span style="background-color: black;color: white;"> Student Concession Report </span></strong>
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
            $pdf->SetFont('times', '', 8, '', true);

            // Add a page
            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $html = '';

            $grand_fees = 0.00;
            $grand_con_fees = 0.00;
            $grand_con_amount = 0.00;
            $count = 0;
            foreach($std as $v) {
                $this->db->select('fees_concession.Fees as con_fees,ACC_MASTER_NAME,fees_type.name as fees_type,class_sec_dtl.Fees');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.CS_SEQ = fees_concession.class_id AND class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('fees_type', 'fees_type.ft_id = acc_master.Fees_Type', 'left');
                $this->db->where("std_id", $v['STD_SEQ']);
                $this->db->where("class_id", $v['STD_CS_SEQ']);
                $this->db->where("class_sec_dtl.Fees !=", 0);
                $fees_con = $this->db->get('fees_concession')->result_array();

                // echo $this->db->last_query(); die;

                if(count($fees_con) > 0){ //if concession fees exists for that student
                    $count++;
                    $html .= '<span style="font-size: 13px">Class & Sec: <strong>'.$v['Class_Name'].' - '.$v['Sec_Name'].'</strong> • Roll: <strong>'.$v['STD_ROLLNO'].'</strong> • Name: <strong>'.$v['STD_FNAME'].' '.$v['STD_MNAME'].' '.$v['STD_LNAME'].'</strong> • Reg. No: <strong>'.$v['STD_REGNO'].'</strong></span>';
                    $html .= <<<EOD
                    <hr>
                    <table cellspacing="2">
                        <thead>
                        <tr>
                            <th width="200"><strong>Fees Name</strong></th>
                            <th width="80"><strong>Fees Type</strong></th>
                            <th width="60" align="right"><strong>Actual Fees</strong></th>
                            <th width="90" align="right"><strong>Concession Fees</strong></th>
                            <th width="100" align="right"><strong>Concession Amount</strong></th>
                            <th width="110" align="right"><strong>Concession Percentage</strong></th>
                        </tr>
                        </thead>
                        <tbody>
EOD;
                    foreach($fees_con as $val) {

                        // echo '<pre>', print_r($val), '</pre>';

                        if($val['Fees'] == $val['con_fees']) { //excluding 0% concession rows
                            continue;
                        }

                        $con_amount = $val['Fees'] - $val['con_fees'];
                        $con_percentage = $con_amount * 100 / $val['Fees'];
                        $grand_fees += $val['Fees'];
                        $grand_con_fees += $val['con_fees'];
                        $grand_con_amount += $con_amount;
                        $html .= '<tr>
                                    <td width="200">'.$val['ACC_MASTER_NAME'].'</td>
                                    <td width="80">'.$val['fees_type'].'</td>
                                    <td width="60" align="right">'.$val['Fees'].'</td>
                                    <td width="90" align="right">'.$val['con_fees'].'</td>
                                    <td width="100" align="right">'.number_format($con_amount,2).'</td>
                                    <td width="110" align="right">'.number_format($con_percentage,2).' %</td>
                                </tr>';
                    }
                    $html .= <<<EOD
                            </tbody>
                        </table>
                        <hr>
                        &nbsp;
                        <br>
                        <br>
EOD;
                }
            }

            if($count == 0) {
                $this->session->set_flashdata('type', 'warning');
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'No student found.');
                return array('type' => 'redirect', 'page'=>'admin/std_consc_report');
            }

            $grand_percentage = number_format($grand_con_amount * 100 / $grand_fees, 2);
            $grand_fees = number_format($grand_fees,2);
            $grand_con_fees = number_format($grand_con_fees,2);
            $grand_con_amount = number_format($grand_con_amount,2);

            $html .= <<<EOD
    <table cellspacing="2" style="font-size: 13px" border="1">
    <thead>
    <tr>
        <th width="250"><strong>Grand Total</strong></th>
        <th width="100" align="right"><strong>$grand_fees</strong></th>
        <th width="100" align="right"><strong>$grand_con_fees</strong></th>
        <th width="100" align="right"><strong>$grand_con_amount</strong></th>
        <th width="100" align="right"><strong>$grand_percentage %</strong></th>
    </tr>
    </thead>
    </table>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name.'.pdf', 'I');

        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/std_consc_report');
        }
    }

    public function print_std_consc_report_2() {
        if($this->input->post('submit') == 'print_std_consc_report_2') { //if form submitted
            $class_type = $this->input->post('class_type');
            $class = $this->input->post('class');

            $company = $this->company_name((array)$class);

            $query = "SELECT `STD_SEQ`, `STD_CS_SEQ`, `STD_REGNO`, `STD_ROLLNO`, `STD_FNAME`, `STD_MNAME`, `STD_LNAME`, `Class_Name`, `Sec_Name` FROM `student_details` LEFT JOIN `class_sec_hdr` ON `class_sec_hdr`.`CS_SEQ` = `student_details`.`STD_CS_SEQ` WHERE `STD_CONSC` = 1 AND `STD_LEFT` =0 AND `STD_CS_SEQ` IN(".implode(',',$class).") ORDER BY FIELD(`Class_Name`, 'PRE-NUR', 'NUR', 'KG', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'), `Sec_Name`, `STD_ROLLNO`";
            $std = $this->db->query($query)->result_array();
//             echo $this->db->last_query(); die;

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Student Concession Report';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('Student Concession Report');
            $pdf->SetKeywords('Student Concession Report, smg, developed by: sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
            <div style="text-align:center;">
            <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
            <br>
            $company->COM_ADD2 , $company->COM_CITY
            <br>
            <strong><span style="background-color: black;color: white;"> Student Concession Report </span></strong>
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
            $pdf->SetFont('times', '', 8, '', true);

            // Add a page
            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $html = '';

            $html .= '<hr>
                <table cellspacing="2">
                    <thead>
                        <tr>
                            <th width="10"><strong>#</strong></th>
                            <th width="45"><strong>Reg. No.</strong></th>
                            <th width="45"><strong>Class Sec.</strong></th>
                            <th width="100"><strong>Std Name</strong></th>
                            <th width="80"><strong>Fees Type</strong></th>
                            <th width="60" align="right"><strong>Actual Fees</strong></th>
                            <th width="90" align="right"><strong>Concession Fees</strong></th>
                            <th width="100" align="right"><strong>Concession Amount</strong></th>
                            <th width="110" align="right"><strong>Concession Percentage</strong></th>
                        </tr>
                    </thead>
                    <tbody>';

            $grand_fees = 0.00;
            $grand_con_fees = 0.00;
            $grand_con_amount = 0.00;
            $count = 0;
            $iter = 0;
            foreach($std as $v) {
                $this->db->select('fees_concession.Fees as con_fees,ACC_MASTER_NAME,fees_type.name as fees_type,class_sec_dtl.Fees');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.CS_SEQ = fees_concession.class_id AND class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('fees_type', 'fees_type.ft_id = acc_master.Fees_Type', 'left');
                $this->db->where("std_id", $v['STD_SEQ']);
                $this->db->where_in("class_id", $v['STD_CS_SEQ']);
                $this->db->where("class_sec_dtl.Fees !=", 0);
                $fees_con = $this->db->get('fees_concession')->result_array();
                // echo $this->db->last_query(); die;
                if(count($fees_con) > 0){ //if concession fees exists for that student
                    $count++;
                    //$html .= '<span style="font-size: 13px">Class & Sec: <strong>'.$v['Class_Name'].' - '.$v['Sec_Name'].'</strong> • Roll: <strong>'.$v['STD_ROLLNO'].'</strong> • Name: <strong>'.$v['STD_FNAME'].' '.$v['STD_MNAME'].' '.$v['STD_LNAME'].'</strong> • Reg. No: <strong>'.$v['STD_REGNO'].'</strong></span>';

                    foreach($fees_con as $val) {
                        if($val['Fees'] == $val['con_fees']) { //excluding 0% concession rows
                            continue;
                        }
                        $con_amount = $val['Fees'] - $val['con_fees'];
                        $con_percentage = $con_amount * 100 / $val['Fees'];
                        $grand_fees += $val['Fees'];
                        $grand_con_fees += $val['con_fees'];
                        $grand_con_amount += $con_amount;
                        $html .= '<tr>
                                    <td width="10">'.++$iter.'</td>
                                    <td width="45">'.$v['STD_REGNO'].'</td>
                                    <td width="45">'.$v['Class_Name'].' - '.$v['Sec_Name'].'</td>
                                    <td width="100">'.$v['STD_FNAME'].' '.$v['STD_MNAME'].' '.$v['STD_LNAME'].'</td>
                                    <td width="80">' .$val['ACC_MASTER_NAME']. ' - '.$val['fees_type'].'</td>
                                    <td width="60" align="right">'.$val['Fees'].'</td>
                                    <td width="90" align="right">'.$val['con_fees'].'</td>
                                    <td width="100" align="right">'.number_format($con_amount,2).'</td>
                                    <td width="110" align="right">'.number_format($con_percentage,2).' %</td>
                                </tr>';
                    }
                }
            }

            $html .= '</tbody>
                        </table>
                        <hr>
                        &nbsp;
                        <br>
                        <br>';

            if($count == 0) {
                $this->session->set_flashdata('type', 'warning');
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'No student found.');
                return array('type' => 'redirect', 'page'=>'admin/std_consc_report');
            }

            $grand_percentage = number_format($grand_con_amount * 100 / $grand_fees, 2);
            $grand_fees = number_format($grand_fees,2);
            $grand_con_fees = number_format($grand_con_fees,2);
            $grand_con_amount = number_format($grand_con_amount,2);

            $html .= <<<EOD
    <table cellspacing="2" style="font-size: 13px" border="1">
    <thead>
    <tr>
        <th width="250"><strong>Grand Total</strong></th>
        <th width="100" align="right"><strong>$grand_fees</strong></th>
        <th width="100" align="right"><strong>$grand_con_fees</strong></th>
        <th width="100" align="right"><strong>$grand_con_amount</strong></th>
        <th width="100" align="right"><strong>$grand_percentage %</strong></th>
    </tr>
    </thead>
    </table>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name.'.pdf', 'I');

        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/std_consc_report');
        }
    }

    public function print_std_consc_report_3() {
        if($this->input->post('submit') == 'print_std_consc_report_percentage_wise') { //if form submitted
            $sr_iter = 0;
            $class_type = $this->input->post('class_type');
            $class = $this->input->post('class');

            $company = $this->company_name((array)$class);

            $query = "SELECT `STD_SEQ`, `STD_CS_SEQ`, `STD_REGNO`, `STD_ROLLNO`, `STD_FNAME`, `STD_MNAME`, `STD_LNAME`, `Class_Name`, `Sec_Name` FROM `student_details` LEFT JOIN `class_sec_hdr` ON `class_sec_hdr`.`CS_SEQ` = `student_details`.`STD_CS_SEQ` WHERE `STD_CONSC` = 1 AND `STD_LEFT` =0 AND `STD_CS_SEQ` IN(".implode(',',$class).") ORDER BY FIELD(`Class_Name`, 'PRE-NUR', 'NUR', 'KG', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'), `Sec_Name`, `STD_ROLLNO`";
            $std = $this->db->query($query)->result_array();
//             echo $this->db->last_query(); die;

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Student Concession Report';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('Student Concession Report');
            $pdf->SetKeywords('Student Concession Report, smg, developed by: sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
            <div style="text-align:center;">
            <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
            <br>
            $company->COM_ADD2 , $company->COM_CITY
            <br>
            <strong><span style="background-color: black;color: white;"> Student Concession Report </span></strong>
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
            $pdf->SetFont('times', '', 8, '', true);

            // Add a page
            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $html = '';

            $html .= '<hr>
                <table cellspacing="2"> 
                    <thead>
                        <tr>
                            <th width="20"><strong>#</strong></th>
                            <th width="50"><strong>Reg. No.</strong></th>
                            <th width="50"><strong>Class Sec.</strong></th>
                            <th width="100"><strong>Std Name</strong></th>
                            <th width="80"><strong>Fees Type</strong></th>
                            <th width="50" align="right"><strong>Actual Fees</strong></th>
                            <th width="80" align="right"><strong>Concession Fees</strong></th>
                            <th width="100" align="right"><strong>Concession Amount</strong></th>
                            <th width="110" align="right"><strong>Concession Percentage</strong></th>
                        </tr>
                    </thead>
                    <tbody>';

            $grand_fees = 0.00;
            $grand_con_fees = 0.00;
            $grand_con_amount = 0.00;
            $count = 0;

            $this->db->query("DROP TABLE IF EXISTS temp_group_concession");
            $sql = "CREATE TEMPORARY TABLE `temp_group_concession` (
                `STD_REGNO` varchar(150) NOT NULL,
                `Class_Name` varchar(150) NOT NULL,
                `Sec_Name` varchar(150) NOT NULL,
                `STD_ROLLNO` int(11) NOT NULL,
                `STD_NAME` varchar(150) NOT NULL,
                `ACC_MASTER_NAME` varchar(150) NOT NULL,
                `Fees` varchar(150) NOT NULL,
                `con_fees` varchar(150) NOT NULL,
                `con_amount` double NOT NULL,
                `con_percentage` double NOT NULL
            ) ENGINE=MyISAM";

            $this->db->query($sql);

            foreach($std as $v) {
                $this->db->select('fees_concession.Fees as con_fees,ACC_MASTER_NAME,fees_type.name as fees_type,class_sec_dtl.Fees, 
                round(((class_sec_dtl.Fees - fees_concession.Fees) * 100)/class_sec_dtl.Fees,2) AS conc_percentage');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.CS_SEQ = fees_concession.class_id AND class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                $this->db->join('fees_type', 'fees_type.ft_id = acc_master.Fees_Type', 'left');
                $this->db->where("std_id", $v['STD_SEQ']);
                $this->db->where_in("class_id", $v['STD_CS_SEQ']);
                $this->db->where("class_sec_dtl.Fees !=", 0);
                $this->db->order_by('conc_percentage');

                $fees_con = $this->db->get('fees_concession')->result_array();

                // echo $this->db->last_query() . '<br><hr>';

                if(count($fees_con) > 0){ //if concession fees exists for that student
                    $count++;

                    foreach($fees_con as $val) {
                        if($val['Fees'] == $val['con_fees']) { //excluding 0% concession rows
                            continue;
                        }
                        $con_amount = $val['Fees'] - $val['con_fees'];
                        $con_percentage = $con_amount * 100 / $val['Fees'];
                        $grand_fees += $val['Fees'];
                        $grand_con_fees += $val['con_fees'];
                        $grand_con_amount += $con_amount;
                        $insert_array = array(
                            'STD_REGNO' => $v['STD_REGNO'],
                            'Class_Name' => $v['Class_Name'],
                            'Sec_Name' => $v['Sec_Name'],
                            'STD_ROLLNO' => $v['STD_ROLLNO'],
                            'STD_NAME' => $v['STD_FNAME'].' '.$v['STD_MNAME'].' '.$v['STD_LNAME'],
                            'ACC_MASTER_NAME' => $val['ACC_MASTER_NAME'],
                            'Fees' => $val['Fees'],
                            'con_fees' => $val['con_fees'],
                            'con_amount' => $con_amount,
                            'con_percentage' => number_format($con_percentage,2)
                        );
                        $this->db->insert('temp_group_concession', $insert_array);
                        $insert_array = array();

                    }
                }
            }

            // echo '<pre>', print_r($this->db->order_by('con_percentage')->get('temp_group_concession')->result_array()), '</pre>';

//            $ordered = $this->db->order_by('con_percentage')->get('temp_group_concession')->result_array();
            $query = "SELECT * FROM `temp_group_concession` ORDER BY con_percentage, FIELD(`Class_Name`, 'PRE-NUR', 'NUR', 'KG', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'), `Sec_Name`, `STD_ROLLNO`";
            $ordered = $this->db->query($query)->result_array();

            $divider = array();

            foreach($ordered as $ord){

                if(!in_array($ord['con_percentage'], $divider)){
                    $sr_iter = 0;
                    array_push($divider, $ord['con_percentage']);
                    $html .= '<tr><td style="font-size: 14px;" width="650"> <strong>For: '.$ord['con_percentage'].'%</strong></td></tr><hr style="line-height: -0.3">';
                }

                $html .= '<tr border="1">
                        <td width="20">'.++$sr_iter.'</td>
                        <td width="50">'.$ord['STD_REGNO'].'</td>
                        <td width="50">'.$ord['Class_Name'].' - '.$ord['Sec_Name'].'</td>
                        <td width="100">'.$ord['STD_NAME'].'</td>
                        <td width="80">' .$ord['ACC_MASTER_NAME'].'</td>
                        <td width="50" align="right">'.$ord['Fees'].'</td>
                        <td width="80" align="right">'.$ord['con_fees'].'</td>
                        <td width="100" align="right">'.number_format($ord['con_amount'],2, '.', '').'</td>
                        <td width="110" align="right">'.$ord['con_percentage'].' %</td>
                    </tr>';

            }

            $html .= '</tbody>
                        </table>
                        <hr>
                        &nbsp;
                        <br>
                        <br>';

            if($count == 0) {
                $this->session->set_flashdata('type', 'warning');
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'No student found.');
                return array('type' => 'redirect', 'page'=>'admin/std_consc_report');
            }

            $grand_percentage = number_format($grand_con_amount * 100 / $grand_fees, 2);
            $grand_fees = number_format($grand_fees,2);
            $grand_con_fees = number_format($grand_con_fees,2);
            $grand_con_amount = number_format($grand_con_amount,2);

            $html .= <<<EOD
    <table cellspacing="2" style="font-size: 13px" border="1">
    <thead>
    <tr>
        <th width="250"><strong>Grand Total</strong></th>
        <th width="100" align="right"><strong>$grand_fees</strong></th>
        <th width="100" align="right"><strong>$grand_con_fees</strong></th>
        <th width="100" align="right"><strong>$grand_con_amount</strong></th>
        <th width="100" align="right"><strong>$grand_percentage %</strong></th>
    </tr>
    </thead>
    </table>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name.'.pdf', 'I');

        }
        elseif($this->input->post('submit') == 'print_std_consc_report_class_wise') { //if form submitted
            $class_type = $this->input->post('class_type');
            $class = $this->input->post('class');
            $company = $this->company_name((array)$class);

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Student Concession Report';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('Student Concession Report');
            $pdf->SetKeywords('Student Concession Report, smg, developed by: sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
            <div style="text-align:center;">
            <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
            <br>
            $company->COM_ADD2 , $company->COM_CITY
            <br>
            <strong><span style="background-color: black;color: white;"> Student Concession Report </span></strong>
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
            $pdf->SetFont('times', '', 8, '', true);

            // Add a page
            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $html = '';

            $grand_fees = 0.00;
            $grand_con_fees = 0.00;
            $grand_con_amount = 0.00;
            $count = 0;

            $class_rs = $this->db->where_in('CS_SEQ', $class)
                ->get('class_sec_hdr')->result();
            #loop through all classes
            foreach ($class_rs as $cls) {
                $sr_iter = 0;
                $query = "SELECT `STD_SEQ`, `STD_CS_SEQ`, `STD_REGNO` FROM `student_details` WHERE `STD_CONSC` = 1 AND `STD_LEFT` = 0 AND `STD_CS_SEQ` = $cls->CS_SEQ";
                $std = $this->db->query($query)->result_array();
//                echo $this->db->last_query(); die();

                $this->db->query("DROP TABLE IF EXISTS temp_group_concession");
                $sql = "CREATE TEMPORARY TABLE `temp_group_concession` (
                `STD_REGNO` varchar(150) NOT NULL,
                `Fees` varchar(150) NOT NULL,
                `con_fees` varchar(150) NOT NULL,
                `con_amount` double NOT NULL,
                `con_percentage` double NOT NULL
            ) ENGINE=MyISAM";

                $this->db->query($sql);

                foreach($std as $v) {
                    $this->db->select('fees_concession.Fees as con_fees,ACC_MASTER_NAME,fees_type.name as fees_type,class_sec_dtl.Fees, 
                round(((class_sec_dtl.Fees - fees_concession.Fees) * 100)/class_sec_dtl.Fees,2) AS conc_percentage');
                    $this->db->join('class_sec_dtl', 'class_sec_dtl.CS_SEQ = fees_concession.class_id AND class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                    $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                    $this->db->join('fees_type', 'fees_type.ft_id = acc_master.Fees_Type', 'left');
                    $this->db->where("std_id", $v['STD_SEQ']);
                    $this->db->where_in("class_id", $v['STD_CS_SEQ']);
                    $this->db->where("class_sec_dtl.CS_FEES_TYPE", 0); //monthly
                    $this->db->where("class_sec_dtl.Fees !=", 0);
                    $this->db->order_by('conc_percentage');
                    $fees_con = $this->db->get('fees_concession')->result_array();

//                    echo $this->db->last_query(); die();

                    if(count($fees_con) > 0){ //if concession fees exists for that student
                        $count++;
                        foreach($fees_con as $val) {
//                            if($val['Fees'] == $val['con_fees']) { //excluding 0% concession rows
//                                continue;
//                            }
                            $con_amount = $val['Fees'] - $val['con_fees'];
                            $con_percentage = $con_amount * 100 / $val['Fees'];
//                            $grand_fees += $val['Fees'];
//                            $grand_con_fees += $val['con_fees'];
//                            $grand_con_amount += $con_amount;
                            $insert_array = array(
                                'STD_REGNO' => $v['STD_REGNO'],
                                'Fees' => $val['Fees'],
                                'con_fees' => $val['con_fees'],
                                'con_amount' => $con_amount,
                                'con_percentage' => number_format($con_percentage,2)
                            );
                            $this->db->insert('temp_group_concession', $insert_array);
                            $insert_array = array();
                        }
                    }
                }
//                 echo '<pre>', print_r($this->db->order_by('con_percentage')->get('temp_group_concession')->result_array()), '</pre>';

                $query = "SELECT *,COUNT(STD_REGNO) as total_std FROM `temp_group_concession` GROUP BY con_percentage ORDER BY con_percentage";
                $ordered = $this->db->query($query)->result_array();
//                echo '<pre>', print_r($ordered); die();

                $divider = array();

                $html .= '<tr>
<td style="font-size: 16px;" width="650"> <strong>'.$cls->class_sec.'</strong></td>
</tr>
<hr style="line-height: -0.3">';

                $html .= '<hr>
                <table cellspacing="2"> 
                    <thead>
                        <tr>
                            <th width="20"><strong>#</strong></th>
                            <th width="110" align="right"><strong>Concession Percentage</strong></th>
                            <th width="100" align="right"><strong>Total Conc. Student</strong></th>
                            <th width="100" align="right"><strong>Actual Fees</strong></th>
                            <th width="100" align="right"><strong>Concession Fees</strong></th>
                            <th width="100" align="right"><strong>Concession Amount</strong></th>
                        </tr>
                    </thead>
                    <tbody>';

                foreach($ordered as $ord){
                    $html .= '<tr border="1">
                        <td width="20">'.++$sr_iter.'</td>
                        <td width="110" align="right">'.$ord['con_percentage'].' %</td>
                        <td width="100" align="right">'.$ord['total_std'].'</td>
                        <td width="100" align="right">'.$ord['Fees'].'</td>
                        <td width="100" align="right">'.$ord['con_fees'].'</td>
                        <td width="100" align="right">'.number_format($ord['con_amount'],2, '.', '').'</td>
                    </tr>';
                }

                $html .= '</tbody>
                        </table>
                        <hr>
                        &nbsp;
                        <br>
                        <br>';
            }
            if($count == 0) {
                $this->session->set_flashdata('type', 'warning');
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'No student found.');
                return array('type' => 'redirect', 'page'=>'admin/std_consc_report');
            }

//            $grand_percentage = number_format($grand_con_amount * 100 / $grand_fees, 2);
//            $grand_fees = number_format($grand_fees,2);
//            $grand_con_fees = number_format($grand_con_fees,2);
//            $grand_con_amount = number_format($grand_con_amount,2);

//            $html .= <<<EOD
//    <table cellspacing="2" style="font-size: 13px" border="1">
//    <thead>
//    <tr>
//        <th width="230"><strong>Grand Total</strong></th>
//        <th width="100" align="right"><strong>$grand_fees</strong></th>
//        <th width="100" align="right"><strong>$grand_con_fees</strong></th>
//        <th width="100" align="right"><strong>$grand_con_amount</strong></th>
//    </tr>
//    </thead>
//    </table>
//EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name.'.pdf', 'I');

        }
        elseif($this->input->post('submit') == 'print_std_consc_report_new_percentage_wise') {
    $class_type = $this->input->post('class_type');
    $class = $this->input->post('class');
    $company = $this->company_name((array)$class);

    // Get all students with concession
    $query = "SELECT `STD_SEQ`, `STD_CS_SEQ`, `STD_REGNO`, `STD_ROLLNO`, `STD_FNAME`, `STD_MNAME`, `STD_LNAME`, `Class_Name`, `Sec_Name` 
              FROM `student_details` 
              LEFT JOIN `class_sec_hdr` ON `class_sec_hdr`.`CS_SEQ` = `student_details`.`STD_CS_SEQ` 
              WHERE `STD_CONSC` = 1 AND `STD_LEFT` = 0 AND `STD_CS_SEQ` IN(".implode(',',$class).") 
              ORDER BY FIELD(`Class_Name`, 'PRE-NUR', 'NUR', 'KG', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'), `Sec_Name`";
    $std = $this->db->query($query)->result_array();

    // Create temporary table for concession data
    $this->db->query("DROP TABLE IF EXISTS temp_concession_matrix");
    $sql = "CREATE TEMPORARY TABLE `temp_concession_matrix` (
        `class_sec` varchar(150) NOT NULL,
        `concession_percentage` varchar(10) NOT NULL,
        `student_count` int(11) DEFAULT 0
    ) ENGINE=MyISAM";
    $this->db->query($sql);

    // Define standard concession percentages
    $standard_percentages = array('5%', '10%', '20%', '30%', '40%', '50%', '60%', '70%', '80%');
    
    // Collect all class sections and concession data
    $class_sections = array();
    $concession_data = array();

    foreach($std as $v) {
        $class_sec = $v['Class_Name'] . '-' . $v['Sec_Name'];
        if (!in_array($class_sec, $class_sections)) {
            $class_sections[] = $class_sec;
        }

        // Get concession details for this student
        $this->db->select('fees_concession.Fees as con_fees, class_sec_dtl.Fees, 
                          round(((class_sec_dtl.Fees - fees_concession.Fees) * 100)/class_sec_dtl.Fees,0) AS conc_percentage');
        $this->db->join('class_sec_dtl', 'class_sec_dtl.CS_SEQ = fees_concession.class_id AND class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
        $this->db->where("std_id", $v['STD_SEQ']);
        $this->db->where("class_id", $v['STD_CS_SEQ']);
        $this->db->where("class_sec_dtl.Fees !=", 0);
        $fees_con = $this->db->get('fees_concession')->result_array();

        if(count($fees_con) > 0) {
            foreach($fees_con as $val) {
                if($val['Fees'] != $val['con_fees']) { // Only non-zero concessions
                    $percentage = round(($val['Fees'] - $val['con_fees']) * 100 / $val['Fees'], 0);
                    $percentage_str = $percentage . '%';
                    
                    // Initialize if not exists
                    if (!isset($concession_data[$class_sec])) {
                        $concession_data[$class_sec] = array();
                    }
                    if (!isset($concession_data[$class_sec][$percentage_str])) {
                        $concession_data[$class_sec][$percentage_str] = 0;
                    }
                    
                    $concession_data[$class_sec][$percentage_str]++;
                }
            }
        }
    }

    // Sort class sections according to school hierarchy
    $class_order = array('PRE-NUR', 'NUR', 'KG', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
    usort($class_sections, function($a, $b) use ($class_order) {
        $class_a = explode('-', $a)[0];
        $class_b = explode('-', $b)[0];
        
        $pos_a = array_search($class_a, $class_order);
        $pos_b = array_search($class_b, $class_order);
        
        if ($pos_a === $pos_b) {
            return strcmp($a, $b);
        }
        return $pos_a - $pos_b;
    });

    // Create PDF document
    //$pdf = new Pdf('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
      $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $doc_name = 'Student Concession Report - Matrix Format';
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($company->COM_NAME);
    $pdf->SetTitle($doc_name);
    $pdf->SetSubject('Student Concession Report');
    $pdf->SetKeywords('Student Concession Report, Matrix Format');

    // Set header
    $html_header = <<<EOD
    <div style="text-align:center;">
        <span style="font-size: 14px;"><strong>$company->COM_NAME</strong></span><br>
        <span style="font-size: 10px;">$company->COM_ADD2, $company->COM_CITY</span><br>
        <strong><span style="background-color: black;color: white; font-size: 12px;"> 2026 Concession Reports </span></strong>
    </div>
EOD;
    $pdf->setHtmlHeader($html_header, false);

    // Set margins and fonts
    $pdf->SetMargins(5, 25, 5);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->setFontSubsetting(true);
    $pdf->SetFont('times', '', 9, '', true);

    // Add page
    $pdf->AddPage('P', 'A4');

    // Build HTML table
    $html = '<div style="text-align: center;">
             <table border="1" cellspacing="0" cellpadding="2" style="margin: 0 auto; border-collapse: collapse;">';
    
    // Header row
    $html .= '<tr>';
    $html .= '<th width="70" style="background-color: #f0f0f0; text-align: center; font-size: 9px;"><strong>Class</strong></th>';
    foreach($standard_percentages as $perc) {
        $html .= '<th width="45" style="background-color: #f0f0f0; text-align: center; font-size: 9px;"><strong>' . $perc . '</strong></th>';
    }
    $html .= '<th width="50" style="background-color: #f0f0f0; text-align: center; font-size: 9px;"><strong>TOTAL</strong></th>';
    $html .= '</tr>';

    // Data rows
    $column_totals = array_fill_keys($standard_percentages, 0);
    $grand_total = 0;

    foreach($class_sections as $class_sec) {
        $html .= '<tr>';
        $html .= '<td width="70" style="text-align: left; font-weight: bold; font-size: 9px;">' . $class_sec . '</td>';
        
        $row_total = 0;
        foreach($standard_percentages as $perc) {
            $count = isset($concession_data[$class_sec][$perc]) ? $concession_data[$class_sec][$perc] : 0;
            $html .= '<td width="45" style="text-align: center; font-size: 9px;">' . ($count > 0 ? $count : '') . '</td>';
            $column_totals[$perc] += $count;
            $row_total += $count;
        }
        
        $html .= '<td width="50" style="text-align: center; font-weight: bold; font-size: 9px;">' . ($row_total > 0 ? $row_total : '') . '</td>';
        $html .= '</tr>';
        $grand_total += $row_total;
    }

    // Total row
    $html .= '<tr style="background-color: #f0f0f0;">';
    $html .= '<td width="70" style="text-align: center; font-weight: bold; font-size: 9px;">TOTAL</td>';
    foreach($standard_percentages as $perc) {
        $html .= '<td width="45" style="text-align: center; font-weight: bold; font-size: 9px;">' . ($column_totals[$perc] > 0 ? $column_totals[$perc] : '') . '</td>';
    }
    $html .= '<td width="50" style="text-align: center; font-weight: bold; font-size: 9px;">' . $grand_total . '</td>';
    $html .= '</tr>';

    $html .= '</table></div>';

    // Check if any data exists
    if($grand_total == 0) {
        $this->session->set_flashdata('type', 'warning');
        $this->session->set_flashdata('title', 'Zzz!');
        $this->session->set_flashdata('msg', 'No student concession data found.');
        return array('type' => 'redirect', 'page'=>'admin/std_consc_report');
    }

    // Write HTML to PDF
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    // Output PDF
    $pdf->Output($doc_name.'.pdf', 'I');
}
        else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/std_consc_report');
        }
    }

    public function print_session_consc_report() {
        if($this->input->post('submit') == 'print_session_consc_report') { //if form submitted
            $class_type = $this->input->post('class_type');
            $class = $this->input->post('class');
            $total_months = $this->input->post('total_months');
            $company = $this->company_name((array)$class);

            $table_data = array();
            foreach($class as $cls) {
                $class_rs = $this->db->get_where('class_sec_hdr', array('CS_SEQ'=>$cls))->row();
                $fee_rs = $this->db->get_where('class_sec_dtl', array('CS_SEQ'=>$cls,'ACC_MASTER_CODE'=>4))->row();
                if(!$fee_rs->Fees) {continue;}
                $total_std = $this->db->select("COUNT(STD_SEQ) as std_count")->get_where('student_details', array('STD_CS_SEQ'=>$cls,'STD_LEFT'=>0,'STD_CURRENT_SESSION'=>CURRENT_YEAR))->row()->std_count;
                if($total_std == 0) {continue;}
                $conc_std = $this->db->select("STD_SEQ")->get_where('student_details', array('STD_CS_SEQ'=>$cls,'STD_LEFT'=>0,'STD_CURRENT_SESSION'=>CURRENT_YEAR,'STD_CONSC'=>1))->result();
                $total_conc_std = count($conc_std);
                $total_non_conc_std = $total_std - $total_conc_std;
                $total_conc_amount = 0;
                if($total_conc_std > 0){
                    foreach ($conc_std as $cs) {
                        $conc_rs = $this->db->get_where('fees_concession', array('std_id'=>$cs->STD_SEQ,'class_id'=>$cls,'ACC_MASTER_CODE'=>4))->row();
                        if($conc_rs->Fees){
                            $total_conc_amount += $conc_rs->Fees; //concession tution fee
                        } else {
                            $total_conc_amount += $fee_rs->Fees; //actual tution fee
                        }
                    }
                }

                $actual_fee = $fee_rs->Fees * $total_std * $total_months; //tution_fee * total_student * month_count
                $estimated_collection = (($fee_rs->Fees * $total_non_conc_std) + $total_conc_amount) * $total_months;
                $concession_amount = $actual_fee - $estimated_collection;
                $concession_percentage = ($concession_amount * 100) / $actual_fee;

                $table_data[] = array(
                    'class' => $class_rs->Class_Name,
                    'secction' => $class_rs->Sec_Name,
                    'total_months' => $total_months,
                    'total_std' => $total_std,
                    'total_non_conc_std' => $total_non_conc_std,
                    'total_conc_std' => $total_conc_std,
                    'actual_fee' => $actual_fee,
                    'estimated_collection' => $estimated_collection,
                    'concession_amount' => $concession_amount,
                    'concession_percentage' => $concession_percentage,
                );
            }

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Student Concession Report';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('Student Concession Report');
            $pdf->SetKeywords('Student Concession Report, smg, developed by: sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
            <div style="text-align:center;">
            <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
            <br>
            $company->COM_ADD2 , $company->COM_CITY
            <br>
            <strong><span style="background-color: black;color: white;"> Student Concession Report </span></strong>
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
            $pdf->SetFont('times', '', 8, '', true);

            // Add a page
            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $html = '';

            $html .= '
                <table cellspacing="2"> 
                    <thead>
                        <tr>
                            <th width="20"><strong>#</strong></th>
                            <th width="90"><strong>Class - Sec.</strong></th>
                            <th width="50"><strong>Total Std. - Conc. Std.</strong></th>
                            <th width="40"><strong>Months</strong></th>
                            <th width="100" align="right"><strong>Actual Fee <br/> (W/O Conc.)</strong></th>
                            <th width="130" align="right"><strong>Estimated Collection <br/> (W Conc.)</strong></th>
                            <th width="120" align="right"><strong>Concession Amount</strong></th>
                            <th width="100" align="right"><strong>Concession %</strong></th>
                        </tr>
                    </thead>
                    <hr>
                    <tbody>';

            $grand_actual_fee = 0.00;
            $grand_estimated_collection = 0.00;
            $grand_concession_amount = 0.00;
            $count = 0;
            $grand_total_std = 0;
            $grand_total_conc_std = 0;

            foreach($table_data as $v) {
                $html .= '<tr>
                        <td width="20">'.++$count.'</td>
                        <td width="90">'.$v['class'].' - '.$v['secction'].'</td>
                        <td width="50">'. $v['total_std']. ' - ' .$v['total_conc_std'] .'</td>
                        <td width="40">'. $total_months .'</td>
                        <td width="100" align="right">'.number_format($v['actual_fee'],2).'</td>
                        <td width="130" align="right">'.number_format($v['estimated_collection'],2).'</td>
                        <td width="120" align="right">'.number_format($v['concession_amount'],2).'</td>
                        <td width="100" align="right">'.number_format($v['concession_percentage'],2).' %</td>
                    </tr>';

                $grand_actual_fee += $v['actual_fee'];
                $grand_estimated_collection += $v['estimated_collection'];
                $grand_concession_amount += $v['concession_amount'];
                $grand_total_std +=$v['total_std'];
                $grand_total_conc_std +=$v['total_conc_std'];
            }

            $grand_percentage = ($grand_concession_amount*100)/$grand_actual_fee;
            $html .= '
</tbody>
</table>
            <table cellspacing="2" style="font-size: 13px" border="1">
            <thead>
            <tr>
                <th width="100"><strong>Grand Total</strong></th>
                <th width="100" align="left"><strong>'.$grand_total_std.'-'.$grand_total_conc_std.'</strong></th>
               
                <th width="100" align="right"><strong>'.number_format($grand_actual_fee,2).'</strong></th>
                <th width="130" align="right"><strong>'.number_format($grand_estimated_collection,2).'</strong></th>
                <th width="120" align="right"><strong>'.number_format($grand_concession_amount,2).'</strong></th>
                <th width="100" align="right"><strong>'.number_format($grand_percentage,2).' %</strong></th>
            </tr>
            </thead>
            </table>
';

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name.'.pdf', 'I');
        }
        elseif($this->input->post('submit') == 'print_consc_range_report') { //if form submitted
            $class_type = $this->input->post('class_type');
            $class = $this->input->post('class');
            $company = $this->company_name((array)$class);

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Student Concession Report';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('Student Concession Report');
            $pdf->SetKeywords('Student Concession Report, smg, developed by: sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
            <div style="text-align:center;">
            <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
            <br>
            $company->COM_ADD2 , $company->COM_CITY
            <br>
            <strong><span style="background-color: black;color: white;"> Student Concession Report </span></strong>
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
            $pdf->SetFont('times', '', 8, '', true);

            // Add a page
            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $html = '';

            $html .= '<hr>
                <table cellspacing="2">
                    <thead>
                        <tr>
                            <th width="15"><strong>#</strong></th>
                            <th width="55"><strong>Class - Sec</strong></th>
                            <th width="55"><strong>10 %</strong></th>
                            <th width="55"><strong>15 %</strong></th>
                            <th width="55"><strong>20 %</strong></th>
                            <th width="55"><strong>25 %</strong></th>
                            <th width="55"><strong>30 %</strong></th>
                            <th width="55"><strong>40 %</strong></th>
                            <th width="55"><strong>50 %</strong></th>
                            <th width="55"><strong>60 %</strong></th>
                            <th width="55"><strong>75 %</strong></th>
                            <th width="55"><strong>85 %</strong></th>
                            <th width="50"><strong>Cons. Std</strong></th>
                        </tr>
                    </thead>
                    <tbody>';

            $this->db->where_in("CS_SEQ", $class);
            $this->db->order_by("FIELD(`Class_Name`, 'PRE-NUR', 'NUR', 'KG', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'), `Sec_Name`");
            $class_rs = $this->db->get('class_sec_hdr')->result();

            $grand_counter = array_fill(1,10,0);
            $std_grand_total = 0;
            $iter = 0;
            foreach ($class_rs as $cls) {
                $total_conc_std = 0;
                $counter = array_fill(1,10,0);

                $query = "SELECT `STD_SEQ`, `STD_CS_SEQ`, `STD_REGNO`, `STD_ROLLNO`, `STD_FNAME`, `STD_MNAME`, `STD_LNAME`, `Class_Name`, `Sec_Name` FROM `student_details`
                    LEFT JOIN `class_sec_hdr` ON `class_sec_hdr`.`CS_SEQ` = `student_details`.`STD_CS_SEQ`
                    WHERE `STD_CONSC`=1 AND `STD_LEFT`=0 AND `STD_CS_SEQ`=$cls->CS_SEQ";
                $std = $this->db->query($query)->result_array();
                foreach($std as $v) {
                    $this->db->select('fees_concession.Fees as con_fees,ACC_MASTER_NAME,fees_type.name as fees_type,class_sec_dtl.Fees');
                    $this->db->join('class_sec_dtl', 'class_sec_dtl.CS_SEQ = fees_concession.class_id AND class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                    $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                    $this->db->join('fees_type', 'fees_type.ft_id = acc_master.Fees_Type', 'left');
                    $this->db->where("std_id", $v['STD_SEQ']);
                    $this->db->where_in("class_id", $v['STD_CS_SEQ']);
                    $this->db->where("class_sec_dtl.Fees !=", 0);
                    $fees_con = $this->db->get('fees_concession')->result_array();
                    // echo $this->db->last_query(); die;
                    if(count($fees_con) > 0){ //if concession fees exists for that student
                        foreach($fees_con as $val) {
                            if($val['Fees'] == $val['con_fees']) { //excluding 0% concession rows
                                continue;
                            }
                            $con_amount = $val['Fees'] - $val['con_fees'];
                            $con_percentage = $con_amount * 100 / $val['Fees'];
                            if($con_percentage > 0) {
                                $total_conc_std++;
                                $std_grand_total++;
                            }

                            switch ($con_percentage) {
                                case $con_percentage > 0 && $con_percentage <= 12 :
                                    $counter[1]++; $grand_counter[1]++; break;
                                case $con_percentage > 12 && $con_percentage <= 15 :
                                    $counter[2]++; $grand_counter[2]++; break;
                                case $con_percentage > 15 && $con_percentage <= 20 :
                                    $counter[3]++; $grand_counter[3]++; break;
                                case $con_percentage > 20 && $con_percentage <= 25 :
                                    $counter[4]++; $grand_counter[4]++; break;
                                case $con_percentage > 25 && $con_percentage <= 33 :
                                    $counter[5]++; $grand_counter[5]++; break;
                                case $con_percentage > 33 && $con_percentage <= 44 :
                                    $counter[6]++; $grand_counter[6]++; break;
                                case $con_percentage > 44 && $con_percentage <= 55 :
                                    $counter[7]++; $grand_counter[7]++; break;
                                case $con_percentage > 55 && $con_percentage <= 65 :
                                    $counter[8]++; $grand_counter[8]++; break;
                                case $con_percentage > 65 && $con_percentage <= 75 :
                                    $counter[9]++; $grand_counter[9]++; break;
                                case $con_percentage > 75 && $con_percentage <= 90 :
                                    $counter[10]++; $grand_counter[10]++; break;
                            }
                        }
                    }
                }
                $html .= '<tr>
                        <td width="15">'.++$iter.'</td>
                        <td width="55">'.$v['Class_Name'].' - '.$v['Sec_Name'].'</td>
                        <td width="55">'.$counter[1].'</td>
                        <td width="55">'.$counter[2].'</td>
                        <td width="55">'.$counter[3].'</td>
                        <td width="55">'.$counter[4].'</td>
                        <td width="55">'.$counter[5].'</td>
                        <td width="55">'.$counter[6].'</td>
                        <td width="55">'.$counter[7].'</td>
                        <td width="55">'.$counter[8].'</td>
                        <td width="55">'.$counter[9].'</td>
                        <td width="55">'.$counter[10].'</td>
                        <td width="50">'.$total_conc_std.'</td>
                    </tr>';
            }

            $html .= '</tbody>
                        </table>
                        <hr>
                        &nbsp;
                        <br>
                        <br>';

            $html .= <<<EOD
    <table style="font-size: 13px" border="1">
    <thead>
    <tr>
        <th width="70"><strong>Total</strong></th>
        <th width="55"><strong>$grand_counter[1]</strong></th>
        <th width="55"><strong>$grand_counter[2]</strong></th>
        <th width="55"><strong>$grand_counter[3]</strong></th>
        <th width="55"><strong>$grand_counter[4]</strong></th>
        <th width="55"><strong>$grand_counter[5]</strong></th>
        <th width="55"><strong>$grand_counter[6]</strong></th>
        <th width="55"><strong>$grand_counter[7]</strong></th>
        <th width="55"><strong>$grand_counter[8]</strong></th>
        <th width="55"><strong>$grand_counter[9]</strong></th>
        <th width="55"><strong>$grand_counter[10]</strong></th>
        <th width="50"><strong>$std_grand_total</strong></th>
    </tr>
    </thead>
    </table>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name.'.pdf', 'I');

        }
        else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/std_consc_report');
        }
    }

    public function all_tran_report() {
        $cls = $this->db->get('class_sec_hdr')->result_array();
        $cls_type = $this->db->get('class_type')->result_array();

        $data['class'] = $cls;
        $data['class_type'] = $cls_type;
        $data['form_type'] = 'all_tran_report';

        $data['tab_title'] = 'All Transaction Report';
        $data['section_heading'] = 'All Transaction Report <small>(Print)</small>';
        $data['menu_name'] = 'All Transaction Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }



// application/controllers/admin_panel/Reports.php
// Method: print_all_tran_report

public function print_all_tran_report() {
    if($this->input->post('submit') == 'print_all_tran_report') { //if form submitted
        $class = $this->input->post('class');
        $class_type = $this->input->post('class_type');
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');
        $fees_type_filter = $this->input->post('fees_type_filter');

        $company = $this->company_name($class_type, 'school');
        
        // Build query cache for fetching fee headers
        $this->db->start_cache();
        $this->db->select('FM_HDR_B_NAME, FM_HDR_SRLNO, FM_HDR_RCPT_NO,FM_HDR_LATE_FEES,FM_HDR_TOT_FEES,FM_HDR_P_TYP,FM_HDR_COL_DATE,STD_REGNO,STD_SRLNO,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME,Class_Name,Sec_Name, FM_HDR_FIN_YEAR');
        $this->db->join('student_details', 'student_details.STD_SEQ = FM_HDR_STD_SEQ', 'left');
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FM_HDR_STD_CS_SEQ', 'left');
        
        if($class_type != '' and  !in_array('all', $class_type)) {
            $this->db->where_in("Class_Type", $class_type);
        }
        if($class != 'all') {
            $this->db->where_in("FM_HDR_STD_CS_SEQ", $class);
        }
        if($date_from != null && $date_to != null) {
            if($date_from > $date_to) { //if from date is greater than to date
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'From Date must be equals to or less than To Date.');
                return array('type' => 'redirect', 'page'=>'admin/all_tran_report');
            }
            $this->db->where('FM_HDR_COL_DATE >=', $date_from);
            $this->db->where('FM_HDR_COL_DATE <=', $date_to);
        }
        $this->db->order_by("FM_HDR_COL_DATE");
        $this->db->stop_cache();

        $hdr_monthly = $this->db->get('fees_monthly_hdr')->result_array();
        $hdr_yearly = $this->db->get('fees_yearly_hdr')->result_array();
        $hdr_newadm = $this->db->get('fees_newadm_hdr')->result_array();
        $this->db->flush_cache();

        if(count($hdr_monthly) == 0 && count($hdr_yearly) == 0 && count($hdr_newadm) == 0) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Zzz!');
            $this->session->set_flashdata('msg', 'No transaction found.');
            return array('type' => 'redirect', 'page'=>'admin/all_tran_report');
        }

        // Create temporary table for unique dates
        $this->db->query("DROP TABLE IF EXISTS temp_table");
        $this->db->query("CREATE TEMPORARY TABLE temp_table (`FM_HDR_COL_DATE` date)");

        $this->db->start_cache();
        $this->db->select('FM_HDR_COL_DATE');
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FM_HDR_STD_CS_SEQ', 'left');
        if($class_type != '' and  !in_array('all', $class_type)) {
            $this->db->where_in("Class_Type", $class_type);
        }
        if($class != 'all') {
            $this->db->where_in("FM_HDR_STD_CS_SEQ", $class);
        }
        if($date_from != null && $date_to != null) {
            $this->db->where('FM_HDR_COL_DATE >=', $date_from);
            $this->db->where('FM_HDR_COL_DATE <=', $date_to);
        }
        $this->db->group_by('FM_HDR_COL_DATE');
        $this->db->stop_cache();
        
        $dates_monthly = $this->db->get_compiled_select('fees_monthly_hdr');
        $dates_yearly = $this->db->get_compiled_select('fees_yearly_hdr');
        $dates_newadm = $this->db->get_compiled_select('fees_newadm_hdr');
        $this->db->flush_cache();
        
        $this->db->query("INSERT INTO temp_table $dates_monthly");
        $this->db->query("INSERT INTO temp_table $dates_yearly");
        $this->db->query("INSERT INTO temp_table $dates_newadm");

        $this->db->group_by("FM_HDR_COL_DATE");
        $this->db->order_by("FM_HDR_COL_DATE");
        $temp_table = $this->db->get('temp_table')->result_array();

        // PDF Setup
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $doc_name = 'All Transaction Report';
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company->COM_NAME);
        $pdf->SetTitle($doc_name);
        $pdf->SetSubject('All Transaction Report');
        $pdf->SetKeywords('All Transaction Report, smg, developed by: www.fb.com/sketchmeglobal');

        if($date_from == null || $date_to == null) {
            $date_range = 'All Dates';
        } else {
            $date_range = date("d-m-Y", strtotime($date_from)).' to '.date("d-m-Y", strtotime($date_to));
        }

        // Set default header data
        $html_header = <<<EOD
    <div style="text-align:center;">
    <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
    <br>
    $company->COM_ADD2 , $company->COM_CITY
    <br>
    <strong>All Transaction Report: <span style="background-color: black;color: white;"> $date_range </span></strong>
    <hr align="left" width="1000">
    </div>
EOD;
        $pdf->setHtmlHeader($html_header, false);

        // Set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(10, 20, 10);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        $pdf->SetFont('times', '', 10, '', true);

        // Add a page
        $pdf->AddPage('L', 'A4');

        // Initialize HTML content
        $html = '';

        // Initialize grand totals BEFORE the loop
        $grand_fees = 0.00;
        $grand_total_canara_bank = 0.00;
        $grand_total_federal_bank = 0.00;
        $grand_total_online = 0.00;

        foreach ($temp_table as $v) { //this loop is for all unique dates in which transaction was made
            // Initialize date-wise totals INSIDE the loop
            $dt_wise_total = 0.00;
            $dt_wise_online_total = 0.00;
            $dt_wise_total_canara_bank = 0.00;
            $dt_wise_total_federal_bank = 0.00;
            
            $html .= '<span style="font-size: 13px">Date: <strong>'.date("d-m-Y", strtotime($v["FM_HDR_COL_DATE"])).'</strong></span>';
            $html .= <<<EOD
            <hr>
            <table >
                <thead>
                <tr>
                    <th width="130" align="left"><strong> Rcpt.No </strong></th>
                    <th width="210" align="left"><strong> Student Name </strong></th>
                    <th width="70" align="center"><strong> Reg.No </strong></th>
                    <th width="90" align="center"><strong> Class & Sec </strong></th>
                    <th width="30" align="center"><strong> Roll </strong></th>
                    <th width="90" align="center"><strong> Fees Type </strong></th>
                    <th width="120" align="center"><strong> Month-Year </strong></th>
                    <th width="70" align="center"><strong>Online</strong></th>
                    <th width="30" align="center"><strong>CHQ</strong></th>
                    <th width="50" align="center"><strong> C B </strong></th>
                    <th width="50" align="center"><strong> F B </strong></th>
                    <th width="70" align="center"><strong> Amount </strong></th>
                </tr>
                </thead>
                <tbody>
EOD;

            // Process Monthly Fees
            if($fees_type_filter == 'all' or $fees_type_filter == 'monthly'){
                $keys_m = array_keys(array_column($hdr_monthly, 'FM_HDR_COL_DATE'), $v['FM_HDR_COL_DATE']);
                $fees_type = "MLY";
                
                foreach ($keys_m as $index) {
                    $val = $hdr_monthly[$index];
                    $dt_wise_total += $val['FM_HDR_TOT_FEES'];
                    $hdr_id = $val['FM_HDR_SRLNO'];
                    
                    $months_arr = array("JAN"=>"1","FEB"=>"2","MAR"=>"3","APR"=>"4","MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12");
                    
                    $this->db->select('FEES_DTL_MONTH');
                    $this->db->where('FEES_DTL_HDR_SRLNO', $hdr_id);
                    $this->db->group_by('FEES_DTL_MONTH');
                    $pd_month = $this->db->get('fees_monthly_dtl')->result_array();

                    $mnth = implode(' ', array_keys(array_intersect($months_arr, array_unique(array_map(function($value){return $value['FEES_DTL_MONTH'];}, $pd_month)))));
                    $yr = substr($val['FM_HDR_FIN_YEAR'], -2);
                    
                    $canara_bank_m = 0;
                    $federal_bank_m = 0;
                    
                    if ($val['FM_HDR_B_NAME'] == 'CANARA') {
                        $canara_bank_m = (int)$val['FM_HDR_TOT_FEES'];
                    }

                    if ($val['FM_HDR_B_NAME'] == 'FEDERAL') {
                        $federal_bank_m = (int)$val['FM_HDR_TOT_FEES'];
                    }

                    $online = 0;
                    if ($val['FM_HDR_P_TYP'] == 'Online') {
                        $online = (int)$val['FM_HDR_TOT_FEES'];
                        // If online and Federal Bank both have values, subtract online from FB
                        if ($federal_bank_m > 0) {
                            $federal_bank_m = $federal_bank_m - $online;
                        }
                    }
                    
                    $dt_wise_total_canara_bank += $canara_bank_m;
                    $dt_wise_total_federal_bank += $federal_bank_m;
                    $dt_wise_online_total += $online;

                    $chq = '';
                    if ($val['FM_HDR_P_TYP'] == 'Deposit') {
                        $chq = 'CHQ';
                    }

                    $html .= '<tr>
                            <td width="130" align="left"> '.$val['FM_HDR_RCPT_NO'].'</td>
                            <td width="210" align="left"> '.$val['STD_FNAME'].' '.$val['STD_MNAME'].' '.$val['STD_LNAME'].'</td>
                            <td width="70" align="center">'.$val['STD_REGNO'].'</td>
                            <td width="90" align="center">'.$val['Class_Name'].'-'.$val['Sec_Name'].'</td>
                            <td width="30" align="center">'.$val['STD_ROLLNO'].'</td>                            
                            <td width="90" align="center">'.$fees_type.'</td>
                            <td width="120" align="center">'.$mnth.' - '.$yr.'</td>
                            <td width="70" align="center">'.$online.'</td>
                            <td width="30" align="center">'.$chq.'</td>
                            <td width="50" align="center">'.$canara_bank_m.'</td>
                            <td width="50" align="center">'.$federal_bank_m.'</td>
                            <td width="70" align="center">'.(int)$val['FM_HDR_TOT_FEES'].'</td>
                        </tr>';
                }
            }
            
            // Process Yearly Fees
            if($fees_type_filter == 'all' or $fees_type_filter == 'yearly'){
                $keys_y = array_keys(array_column($hdr_yearly, 'FM_HDR_COL_DATE'), $v['FM_HDR_COL_DATE']);
                $fees_type = "YLY";
                
                foreach ($keys_y as $index) {
                    $val = $hdr_yearly[$index];
                    $dt_wise_total += $val['FM_HDR_TOT_FEES'];
                    $yr = substr($val['FM_HDR_FIN_YEAR'], -2);
                    
                    $canara_bank_y = 0;
                    $federal_bank_y = 0;
                    
                    if ($val['FM_HDR_B_NAME'] == 'CANARA') {
                        $canara_bank_y = (int)$val['FM_HDR_TOT_FEES'];
                    }

                    if ($val['FM_HDR_B_NAME'] == 'FEDERAL') {
                        $federal_bank_y = (int)$val['FM_HDR_TOT_FEES'];
                    }

                    $online = 0;
                    if ($val['FM_HDR_P_TYP'] == 'Online') {
                        $online = (int)$val['FM_HDR_TOT_FEES'];
                        // If online and Federal Bank both have values, subtract online from FB
                        if ($federal_bank_y > 0) {
                            $federal_bank_y = $federal_bank_y - $online;
                        }
                    }
                    
                    $dt_wise_total_canara_bank += $canara_bank_y;
                    $dt_wise_total_federal_bank += $federal_bank_y;
                    $dt_wise_online_total += $online;

                    $chq = '';
                    if ($val['FM_HDR_P_TYP'] == 'Deposit') {
                        $chq = 'CHQ';
                    }

                    $html .= '<tr>
                            <td width="130" align="left"> '.$val['FM_HDR_RCPT_NO'].'</td>
                            <td width="210" align="left"> '.$val['STD_FNAME'].' '.$val['STD_MNAME'].' '.$val['STD_LNAME'].'</td>
                            <td width="70" align="center">'.$val['STD_REGNO'].'</td>
                            <td width="90" align="center">'.$val['Class_Name'].'-'.$val['Sec_Name'].'</td>
                            <td width="30" align="center">'.$val['STD_ROLLNO'].'</td>
                            <td width="90" align="center">'.$fees_type.'</td>
                            <td width="120" align="center">YRL - '.$yr.'</td>
                            <td width="70" align="center">'.$online.'</td>
                            <td width="30" align="center">'.$chq.'</td>
                            <td width="50" align="center">'.$canara_bank_y.'</td>
                            <td width="50" align="center">'.$federal_bank_y.'</td>
                            <td width="70" align="center">'.(int)$val['FM_HDR_TOT_FEES'].'</td>
                        </tr>';
                }
            }
            
            // Process New Admission Fees
            if($fees_type_filter == 'all' or $fees_type_filter == 'nadmission'){
                $keys_n = array_keys(array_column($hdr_newadm, 'FM_HDR_COL_DATE'), $v['FM_HDR_COL_DATE']);
                $fees_type = "N-ADM";
                
                foreach ($keys_n as $index) {
                    $val = $hdr_newadm[$index];
                    $yr = substr($val['FM_HDR_FIN_YEAR'], -2);
                    $dt_wise_total += $val['FM_HDR_TOT_FEES'];

                    $canara_bank_n = 0;
                    $federal_bank_n = 0;
                    
                    if ($val['FM_HDR_B_NAME'] == 'CANARA') {
                        $canara_bank_n = (int)$val['FM_HDR_TOT_FEES'];
                    }

                    if ($val['FM_HDR_B_NAME'] == 'FEDERAL') {
                        $federal_bank_n = (int)$val['FM_HDR_TOT_FEES'];
                    }

                    $online = 0;
                    if ($val['FM_HDR_P_TYP'] == 'Online') {
                        $online = (int)$val['FM_HDR_TOT_FEES'];
                        // If online and Federal Bank both have values, subtract online from FB
                        if ($federal_bank_n > 0) {
                            $federal_bank_n = $federal_bank_n - $online;
                        }
                    }
                    
                    $dt_wise_total_canara_bank += $canara_bank_n;
                    $dt_wise_total_federal_bank += $federal_bank_n;
                    $dt_wise_online_total += $online;

                    $chq = '';
                    if ($val['FM_HDR_P_TYP'] == 'Deposit') {
                        $chq = 'CHQ';
                    }

                    $html .= '<tr>
                            <td width="130" align="left"> '.$val['FM_HDR_RCPT_NO'].'</td>
                            <td width="210" align="left"> '.$val['STD_FNAME'].' '.$val['STD_MNAME'].' '.$val['STD_LNAME'].'</td>
                            <td width="70" align="center">'.$val['STD_REGNO'].'</td>
                            <td width="90" align="center">'.$val['Class_Name'].'-'.$val['Sec_Name'].'</td>
                            <td width="30" align="center">'.$val['STD_ROLLNO'].'</td>
                            <td width="90" align="center">'.$fees_type.'</td>
                            <td width="120" align="center">YRL - '.$yr.'</td>
                            <td width="70" align="center">'.$online.'</td>
                            <td width="30" align="center">'.$chq.'</td>
                            <td width="50" align="center">'.$canara_bank_n.'</td>
                            <td width="50" align="center">'.$federal_bank_n.'</td>
                            <td width="70" align="center">'.(int)$val['FM_HDR_TOT_FEES'].'</td>
                        </tr>';
                }
            }

            // Accumulate grand totals
            $grand_fees += $dt_wise_total;
            $grand_total_canara_bank += $dt_wise_total_canara_bank;
            $grand_total_federal_bank += $dt_wise_total_federal_bank;
            $grand_total_online += $dt_wise_online_total;

            $html .= <<<EOD
                </tbody>
                <hr>
                <tr>
                    <td width="130" align="left"> <strong>Date Wise Total</strong></td>  
                    <td width="210" align="left"> </td>
                    <td width="70" align="center"></td>
                    <td width="90" align="center"></td>
                    <td width="30" align="center"></td>
                    <td width="90" align="center"></td>
                    <td width="120" align="center"></td>
                    <td width="70" align="center"><strong>$dt_wise_online_total</strong></td>
                    <td width="30" align="center"></td>
                    <td width="50" align="center"><strong>$dt_wise_total_canara_bank</strong></td>
                    <td width="50" align="center"><strong>$dt_wise_total_federal_bank</strong></td>
                    <td width="70" align="right"><strong>$dt_wise_total</strong></td>
                </tr>
            </table>
            <hr>
            &nbsp;
            <br>
            <br>
EOD;
        }

        // Grand Total Section
        $html .= <<<EOD
<table cellspacing="2" style="font-size: 13px" border="1">
<thead>
<tr>
    <th width="130" align="left"><strong>Grand Total</strong></th>
    <th width="210" align="left"></th>
    <th width="70" align="center"></th>
    <th width="90" align="center"></th>
    <th width="30" align="center"></th>
    <th width="90" align="center"></th>
    <th width="120" align="center"></th>
    <th width="70" align="center"><strong>$grand_total_online</strong></th>
    <th width="30" align="center"></th>
    <th width="50" align="center"><strong>$grand_total_canara_bank</strong></th>
    <th width="50" align="center"><strong>$grand_total_federal_bank</strong></th>
    <th width="80" align="center"><strong>$grand_fees</strong></th>
</tr>
</thead>
</table>
EOD;

        // Write HTML to PDF
        $pdf->writeHTMLCell(290, 0, 5, $pdf->GetY(), $html, 0, 1, 0, false, 'center', true);

        // Output PDF
        $pdf->Output($doc_name . '.pdf', 'I');

    } else { //if form not submitted
        $this->session->set_flashdata('type', 'error');
        $this->session->set_flashdata('title', 'Oh Snap!');
        $this->session->set_flashdata('msg', 'Something went wrong.');
        return array('type' => 'redirect', 'page'=>'admin/all_tran_report');
    }
}

    public function student_strength() {
        $cls = $this->db->get('class_sec_hdr')->result_array();
        $class_type = $this->db->get('class_type')->result_array();

        $data['class_type'] = $class_type;
        $data['class'] = $cls;
        $data['form_type'] = 'std_strength_report';

        $data['tab_title'] = 'Student Strength Report';
        $data['section_heading'] = 'Student Strength Report <small>(Print)</small>';
        $data['menu_name'] = 'Student Strength Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function print_student_strength_report() {
        $class_type = $this->input->post('class_type');
        $class_id = $this->input->post('class');
        $company = $this->company_name((array)$class_id);

        if (empty($class_id)) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'Something went wrong');
            return array('type' => 'redirect', 'page'=>'admin/student_strength');
        }
        elseif($this->input->post('submit') == "print_student_strength_report") {
            $this->db->start_cache();
            $this->db->select('count(STD_SEQ) AS total_students, class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_CS_SEQ', $class_id);
            $this->db->where("STD_PROMOTED", 1);
            $this->db->where("STD_LEFT", 0);
            $this->db->where('STD_STATUS', 0);
            $this->db->order_by('class_sec_hdr.class_order','ASC');
            $this->db->group_by("STD_CS_SEQ");
            $this->db->stop_cache();
            $hdr_monthly = $this->db->get('student_details')->result_array();
            $this->db->flush_cache();

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Student Strength';
            $pdf->SetCreator(PDF_CREATOR);

            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('Student Strength Report');
            $pdf->SetKeywords('All Transaction Report, smg, developed by: https://sketchmeglobal.com');


            $pdf->SetAuthor($company->COM_NAME);

            // set default header data
            $html_header = <<<EOD
    <div style="text-align:center;">
    <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
    <br>
    $company->COM_ADD2 , $company->COM_CITY
    <br>
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

            // Set some content to print
            $html = '';

            $html .= <<<EOD
            <hr>
            <table cellspacing="2">
                <thead>
                <tr style="border: 1px solid black;">
                    <th width="50" align="center"><strong>#</strong></th>
                    <th width="450"><strong> Class Sec</strong></th>
                    <th width="200" align="center"><strong> Total Strength</strong></th>
                </tr>
                </thead>
                <tbody>
EOD;
            $st_ttl = 0;
            $serial = 1;  // Add counter
            foreach ($hdr_monthly as $index) {
                $st_ttl += $index['total_students'];
                $html .= '<tr>
                        <td width="50" align="center">'.$serial.'</td>
                        <td width="450">'.$index['class_sec'].'</td>
                        <td width="200" align="center">'.$index['total_students'].'</td>
                    </tr>';
                $serial++;  // Increment counter
            }

            $html .= <<<EOD
                </tbody>
                <hr>
            </table>
            <hr>
             Student Strangth &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$st_ttl}
            &nbsp;
            <br>
            <br>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');
        }
        elseif($this->input->post('submit') == "print_catholic_report") {
            $this->db->where_in("CS_SEQ", $class_id);
            $this->db->order_by("FIELD(`Class_Name`, 'PRE-NUR', 'NUR', 'KG', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'), `Sec_Name`");
            $class_rs = $this->db->get('class_sec_hdr')->result();

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Student Strength';
            $pdf->SetCreator(PDF_CREATOR);

            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('Student Strength Report');
            $pdf->SetKeywords('All Transaction Report, smg, developed by: https://sketchmeglobal.com');


            $pdf->SetAuthor($company->COM_NAME);

            // set default header data
            $html_header = <<<EOD
    <div style="text-align:center;">
    <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
    <br>
    $company->COM_ADD2 , $company->COM_CITY
    <br>
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

            // Set some content to print
            $html = '';

            $html .= <<<EOD
            <hr>
            <table cellspacing="2">
                <thead>
                <tr style="border: 1px solid black;">
                    <th width="200"><strong> Class Sec</strong></th>
                    <th width="150" align="center"><strong> Total Student</strong></th>
                    <th width="150" align="center"><strong> Catholic</strong></th>
                    <th width="150" align="center"><strong> Non-Catholic</strong></th>
                </tr>
                </thead>
                <tbody>
EOD;
            $grand_total = array_fill(1,2,0);
            foreach ($class_rs as $cls) {
                $this->db->select('count(STD_SEQ) AS counter');
                $this->db->where('STD_CS_SEQ', $cls->CS_SEQ);
                $this->db->where("STD_RC", 1); //catholic
                $this->db->where("STD_PROMOTED", 1);
                $this->db->where("STD_LEFT", 0);
                $this->db->where('STD_STATUS', 0);
                //$this->db->where("STD_LEFT", 0);
                $cath_total = $this->db->get('student_details')->row()->counter;

                $this->db->select('count(STD_SEQ) AS counter');
                $this->db->where('STD_CS_SEQ', $cls->CS_SEQ);
                $this->db->where("STD_RC", 0); //non-catholic
                $this->db->where("STD_PROMOTED", 1); //only promoted
                $this->db->where("STD_LEFT", 0); //not left from school
                $this->db->where('STD_STATUS', 0); //avtive students
                //$this->db->where("STD_LEFT", 0); //not left from school
                $non_cath_total = $this->db->get('student_details')->row()->counter;

                $html .= '<tr>
                    <td width="200">' . $cls->class_sec . '</td>
                    <td width="150" align="center">' . ($cath_total + $non_cath_total) . '</td>
                    <td width="150" align="center">' . $cath_total . '</td>
                    <td width="150" align="center">' . $non_cath_total . '</td>
                </tr>';

                $grand_total[1] += $cath_total;
                $grand_total[2] += $non_cath_total;
                $grand_total[3] += $cath_total + $non_cath_total;
            }

            $html .= <<<EOD
                </tbody>
                <hr>
                <tfoot>
                <tr>
                    <th width="200"><strong> Total Student</strong></th>
                    <th width="150" align="center"><strong>$grand_total[3]</strong></th>
                    <th width="150" align="center"><strong>$grand_total[1]</strong></th>
                    <th width="150" align="center"><strong>$grand_total[2]</strong></th>
                </tr>
                </tfoot>
            </table>
            <hr>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');
        }
    }

    public function all_fees_type1_report() {
        $data['form_type'] = 'all_fees_type1_report';

        $cls_type = $this->db->get('class_type')->result_array();
        $data['class_type'] = $cls_type;
        $data['tab_title'] = 'All Fees Report (Date Wise)';
        $data['section_heading'] = 'All Fees Report (Date Wise) <small>(Print)</small>';
        $data['menu_name'] = 'All Fees Report (Date Wise)';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function print_all_fees_type1_report() {
        if($this->input->post('submit') == 'print_all_fees_type1_report') { //if form submitted
            $date_from = $this->input->post('date_from');
            $date_to = $this->input->post('date_to');
            $month = $this->input->post('month');
            $class_type = $this->input->post('class_type[]');

            $months_arr = array("JAN"=>"1","FEB"=>"2","MAR"=>"3","APR"=>"4","MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12");
            $selected_month = array_search($month , $months_arr);



            $company = $this->company_name($class_type, 'school');

            $this->db->query("DROP TABLE IF EXISTS temp_table");
            $this->db->query("CREATE TEMPORARY TABLE temp_table (`FEES_DTL_COL_DATE` date)");

            $this->db->start_cache();
            $this->db->select('FEES_DTL_COL_DATE');
            if($date_from != null && $date_to != null) {
                if($date_from > $date_to) { //if from date is greater than to date
                    $this->session->set_flashdata('type', 'error');
                    $this->session->set_flashdata('title', 'Naa!');
                    $this->session->set_flashdata('msg', 'From Date must be equals to or less than To Date.');
                    return array('type' => 'redirect', 'page'=>'admin/all_fees_type1_report');
                }
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                $this->db->where('FEES_DTL_COL_DATE >=', $date_from);
                $this->db->where('FEES_DTL_COL_DATE <=', $date_to);
            }
            $this->db->group_by('FEES_DTL_COL_DATE');
            $this->db->stop_cache();
            // $this->db->get('fees_yearly_dtl');
            // echo $this->db->last_query(); die();
            $dates_monthly = $this->db->get_compiled_select('fees_monthly_dtl');
            $dates_yearly = $this->db->get_compiled_select('fees_yearly_dtl');
            $dates_newadm = $this->db->get_compiled_select('fees_newadm_dtl');
            $this->db->flush_cache();



            $this->db->query("INSERT INTO temp_table $dates_monthly");
            $this->db->query("INSERT INTO temp_table $dates_yearly");
            $this->db->query("INSERT INTO temp_table $dates_newadm");

            $this->db->group_by("FEES_DTL_COL_DATE");
            $this->db->order_by("FEES_DTL_COL_DATE");
            $temp_table = $this->db->get('temp_table')->result_array();

            // echo "<pre>"; print_r($temp_table); die();

            if(count($temp_table) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'No transaction found.');
                return array('type' => 'redirect', 'page'=>'admin/all_fees_type1_report');
            }

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'All Fees Report (Date Wise)';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('All Fees Report (Date Wise)');
            $pdf->SetKeywords('All Fees Report (Date Wise), smg, developed by: https://sketchmeglobal.com');

            if($date_from == null || $date_to == null) {
                $date_range = 'All Dates';
            } else {
                $date_range = date("d-m-Y", strtotime($date_from)).' to '.date("d-m-Y", strtotime($date_to));
            }

            // set default header data
            $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2 , $company->COM_CITY
        <br>
        <strong>All Fees Report (Date Wise): <span style="background-color: black;color: white;"> $date_range </span></strong>
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
            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $html = '';

            $grand_fees = 0;
            $grand_fees_total_canara_bank = 0;
            $grand_fees_total_federal_bank = 0;

            foreach ($temp_table as $v) { //this loop is for all unique dates in which transaction was made
                $dt_wise_total = 0;
                $dt_wise_total_canara_bank = 0;
                $dt_wise_total_federal_bank = 0;
                $html .= '<span style="font-size: 13px">Date: <strong>'.date("d-m-Y", strtotime($v["FEES_DTL_COL_DATE"])).' '.$selected_month.'</strong></span>';
                $html .= <<<EOD
                <hr width="685">
                <table cellspacing="2">
                    <thead>
                    <tr>
                        <th width="250"><strong>Fees Name</strong></th>
                        <th width="50"><strong>Fees Type</strong></th>
                        <th width="80" align="right"><strong>Total Tran</strong></th>
                        <th width="80" align="right"><strong>Canara Bank</strong></th>
                        <th width="80" align="right"><strong>Federal Bank</strong></th>
                        <th width="80" align="right"><strong>Total Amount</strong></th>
                    </tr>
                    </thead>
                    <tbody>
EOD;
                $this->db->start_cache();
                $this->db->select('FEES_DTL_ACC_SEQ, ACC_MASTER_NAME,COUNT(FEES_DTL_ACC_SEQ) as total_row,SUM(FEES_DTL_AMOUNT) as total_amount');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = FEES_DTL_ACC_SEQ', 'left');
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                $this->db->where("FEES_DTL_COL_DATE", $v["FEES_DTL_COL_DATE"]);
                $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                $this->db->group_by("FEES_DTL_ACC_SEQ");
                $this->db->order_by("ACC_MASTER_NAME");
                $this->db->stop_cache();
                $dtl_monthly = $this->db->get('fees_monthly_dtl')->result_array();
                $dtl_yearly = $this->db->get('fees_yearly_dtl')->result_array();
                $dtl_newadm = $this->db->get('fees_newadm_dtl')->result_array();
                $this->db->flush_cache();


                /*-------------------------Monthly Section------------------------*/
                foreach ($dtl_monthly as $val) {
                    if($val['total_amount'] == 0) {
                        continue;
                    }

                    /*Total Late Fees*/
                    $this->db->select('COALESCE(SUM(fees_monthly_hdr.FM_HDR_LATE_FEES),0) as total_late_fees, COUNT(NULLIF(FM_HDR_LATE_FEES,0)) AS total_late_row');
                    $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = fees_monthly_hdr.FM_HDR_STD_CS_SEQ', 'left');
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                    $this->db->where("fees_monthly_hdr.FM_HDR_B_NAME", 'CANARA');
                    $this->db->where("fees_monthly_hdr.FM_HDR_COL_DATE", $v["FEES_DTL_COL_DATE"]);
                    $total_late_fees_canara_bank_m = $this->db->get('fees_monthly_hdr')->row();

                    $this->db->select('COALESCE(SUM(fees_monthly_hdr.FM_HDR_LATE_FEES),0) as total_late_fees, COUNT(NULLIF(FM_HDR_LATE_FEES,0)) AS total_late_row');
                    $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = fees_monthly_hdr.FM_HDR_STD_CS_SEQ', 'left');
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                    $this->db->where("fees_monthly_hdr.FM_HDR_B_NAME", 'FEDERAL');
                    $this->db->where("fees_monthly_hdr.FM_HDR_COL_DATE", $v["FEES_DTL_COL_DATE"]);
                    $total_late_fees_federal_bank_m = $this->db->get('fees_monthly_hdr')->row();
//                 echo $this->db->last_query(); die();
                    //echo "<pre>"; print_r($total_late_fees_m); die();

                    // $val['total_amount'] = $val['total_amount'] + $total_late_fees_canara_bank_m->total_late_fees;
                    /*---------------*/
                    /*Canara Bank*/
                    $this->db->select('SUM(FEES_DTL_AMOUNT) as total_canara_bank');
                    $this->db->join('fees_monthly_dtl', 'fees_monthly_hdr.FM_HDR_SRLNO = fees_monthly_dtl.FEES_DTL_HDR_SRLNO', 'left');
                    $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                    $this->db->where("fees_monthly_dtl.FEES_DTL_COL_DATE", $v["FEES_DTL_COL_DATE"]);
                    $this->db->where("fees_monthly_dtl.FEES_DTL_ACC_SEQ", $val["FEES_DTL_ACC_SEQ"]);
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                    $this->db->where("fees_monthly_hdr.FM_HDR_B_NAME", 'CANARA');
                    $this->db->group_by("fees_monthly_dtl.FEES_DTL_ACC_SEQ");
                    // $this->db->order_by("ACC_MASTER_NAME");
                    $this->db->stop_cache();
                    $canara_bank_m = $this->db->get('fees_monthly_hdr')->row();

                    (int)$canara_bank_m = empty($canara_bank_m)?'0':$canara_bank_m->total_canara_bank;

                    $dt_wise_total_canara_bank += $canara_bank_m;

                    /*echo $this->db->last_query();
                    echo "<pre>"; print_r($canara_bank_m);
                    die();*/
                    /*-----------*/
                    /*Fedreal Bank*/
                    $this->db->select('SUM(FEES_DTL_AMOUNT) as total_federal_bank');
                    $this->db->join('fees_monthly_dtl', 'fees_monthly_hdr.FM_HDR_SRLNO = fees_monthly_dtl.FEES_DTL_HDR_SRLNO', 'left');
                    $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                    $this->db->where("fees_monthly_dtl.FEES_DTL_COL_DATE", $v["FEES_DTL_COL_DATE"]);
                    $this->db->where("fees_monthly_dtl.FEES_DTL_ACC_SEQ", $val["FEES_DTL_ACC_SEQ"]);
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                    $this->db->where("fees_monthly_hdr.FM_HDR_B_NAME", 'FEDERAL');
                    $this->db->group_by("fees_monthly_dtl.FEES_DTL_ACC_SEQ");
                    // $this->db->order_by("ACC_MASTER_NAME");
                    $this->db->stop_cache();
                    $federal_bank_m = $this->db->get('fees_monthly_hdr')->row();

                    (int)$federal_bank_m = empty($federal_bank_m)?'0':$federal_bank_m->total_federal_bank;

                    $dt_wise_total_federal_bank += $federal_bank_m;
                    /*echo $this->db->last_query();
                    echo "<pre>"; print_r($canara_bank_m);
                    die();*/
                    /*------------*/
                    // total late fees count
                    $this->db->select('*');
                    $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = fees_monthly_hdr.FM_HDR_STD_CS_SEQ', 'left');
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                    $this->db->where("fees_monthly_hdr.FM_HDR_LATE_FEES !=", '0.00');
                    $this->db->where("fees_monthly_hdr.FM_HDR_COL_DATE", $v["FEES_DTL_COL_DATE"]);
                    $late_fees_frequency = $this->db->get('fees_monthly_hdr')->num_rows();

                    $fees_type = "Monthly";
//                    $grand_fees = $grand_fees+$val['total_amount'];

                    $html .= '<tr>
                            <td width="250">'.$val['ACC_MASTER_NAME'].'</td>
                            <td width="50">'.$fees_type.'</td>
                            <td width="80" align="right">'.$val['total_row'].'</td>
                            <td width="80" align="right">'.(int)$canara_bank_m.'</td>
                            <td width="80" align="right">'.(int)$federal_bank_m.'</td>
                            <td width="80" align="right">'.(int)$val['total_amount'].'</td>
                        </tr>';

                    if ($total_late_fees_canara_bank_m->total_late_fees !=0 || $total_late_fees_federal_bank_m->total_late_fees !=0) {
                        $html .= '<tr>
                            <td width="250">Late Fees</td>
                            <td width="50">'.$fees_type.'</td>
                            <td width="80" align="right">'.(int)$late_fees_frequency.'</td>
                            <td width="80" align="right">'.(int)$total_late_fees_canara_bank_m->total_late_fees.'</td>
                            <td width="80" align="right">'.(int)$total_late_fees_federal_bank_m->total_late_fees.'</td>
                            <td width="80" align="right">'.(int)($total_late_fees_canara_bank_m->total_late_fees + $total_late_fees_federal_bank_m->total_late_fees).'</td>
                        </tr>';
                        $dt_wise_total_canara_bank += $total_late_fees_canara_bank_m->total_late_fees;
                        $dt_wise_total_federal_bank += $total_late_fees_federal_bank_m->total_late_fees;
                    }


                    $dt_wise_total += $val['total_amount'] + ($total_late_fees_canara_bank_m->total_late_fees + $total_late_fees_federal_bank_m->total_late_fees);
                }
                /*---------------------------------------------------------------*/

                /*-------------------------Yearly Section------------------------*/
                foreach ($dtl_yearly as $val) {
                    if($val['total_amount'] == 0) {
                        continue;
                    }

                    /*Canara Bank*/
                    $this->db->select('(SUM(FEES_DTL_AMOUNT)+SUM(fees_yearly_hdr.FM_HDR_LATE_FEES)) as total_canara_bank');
                    $this->db->join('fees_yearly_dtl', 'fees_yearly_hdr.FM_HDR_SRLNO = fees_yearly_dtl.FEES_DTL_HDR_SRLNO', 'left');
                    $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                    $this->db->where("fees_yearly_dtl.FEES_DTL_COL_DATE", $v["FEES_DTL_COL_DATE"]);
                    $this->db->where("fees_yearly_dtl.FEES_DTL_ACC_SEQ", $val["FEES_DTL_ACC_SEQ"]);
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                    $this->db->where("fees_yearly_hdr.FM_HDR_B_NAME", 'CANARA');

                    $this->db->group_by("fees_yearly_dtl.FEES_DTL_ACC_SEQ");
                    // $this->db->order_by("ACC_MASTER_NAME");
                    $this->db->stop_cache();
                    $canara_bank_y = $this->db->get('fees_yearly_hdr')->row();
                    (int)$canara_bank_y = empty($canara_bank_y)?'0':$canara_bank_y->total_canara_bank;

                    $dt_wise_total_canara_bank += $canara_bank_y;
                    /*echo $this->db->last_query();
                    echo "<pre>"; print_r($canara_bank_m);
                    die();*/
                    /*-----------*/

                    /*Fedreal Bank*/
                    $this->db->select('(SUM(FEES_DTL_AMOUNT)+SUM(fees_yearly_hdr.FM_HDR_LATE_FEES)) as total_federal_bank');
                    $this->db->join('fees_yearly_dtl', 'fees_yearly_hdr.FM_HDR_SRLNO = fees_yearly_dtl.FEES_DTL_HDR_SRLNO', 'left');
                    $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                    $this->db->where("fees_yearly_dtl.FEES_DTL_COL_DATE", $v["FEES_DTL_COL_DATE"]);
                    $this->db->where("fees_yearly_dtl.FEES_DTL_ACC_SEQ", $val["FEES_DTL_ACC_SEQ"]);
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                    $this->db->where("fees_yearly_hdr.FM_HDR_B_NAME", 'FEDERAL');

                    $this->db->group_by("fees_yearly_dtl.FEES_DTL_ACC_SEQ");
                    // $this->db->order_by("ACC_MASTER_NAME");
                    $this->db->stop_cache();
                    $federal_bank_y = $this->db->get('fees_yearly_hdr')->row();

                    (int)$federal_bank_y = empty($federal_bank_y)?'0':$federal_bank_y->total_federal_bank;

                    $dt_wise_total_federal_bank += $federal_bank_y;

                    /*echo $this->db->last_query();
                    echo "<pre>"; print_r($canara_bank_m);
                    die();*/
                    /*------------*/
                    $fees_type = "Yearly";
//                    $grand_fees = $grand_fees+$val['total_amount'];
                    $dt_wise_total += $val['total_amount'];
                    $html .= '<tr>
                            <td width="250">'.$val['ACC_MASTER_NAME'].'</td>
                            <td width="50">'.$fees_type.'</td>
                            <td width="80" align="right">'.$val['total_row'].'</td>
                            <td width="80" align="right">'.(int)$canara_bank_y.'</td>
                            <td width="80" align="right">'.(int)$federal_bank_y.'</td>
                            <td width="80" align="right">'.(int)$val['total_amount'].'</td>
                        </tr>';
                }
                /*-------------------------------------------------------------------*/

                /*------------------------New Admission Section---------------------*/
                foreach ($dtl_newadm as $val) {
                    if($val['total_amount'] == 0) {
                        continue;
                    }

                    /*Canara Bank*/
                    $this->db->select('(SUM(FEES_DTL_AMOUNT)+SUM(fees_newadm_hdr.FM_HDR_LATE_FEES)) as total_canara_bank');
                    $this->db->join('fees_newadm_dtl', 'fees_newadm_hdr.FM_HDR_SRLNO = fees_newadm_dtl.FEES_DTL_HDR_SRLNO', 'left');
                    $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                    $this->db->where("fees_newadm_dtl.FEES_DTL_COL_DATE", $v["FEES_DTL_COL_DATE"]);
                    $this->db->where("fees_newadm_dtl.FEES_DTL_ACC_SEQ", $val["FEES_DTL_ACC_SEQ"]);
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                    $this->db->where("fees_newadm_hdr.FM_HDR_B_NAME", 'CANARA');

                    $this->db->group_by("fees_newadm_dtl.FEES_DTL_ACC_SEQ");
                    // $this->db->order_by("ACC_MASTER_NAME");
                    $this->db->stop_cache();
                    $canara_bank_n = $this->db->get('fees_newadm_hdr')->row();

                    (int)$canara_bank_n = empty($canara_bank_n)?'0':$canara_bank_n->total_canara_bank;

                    $dt_wise_total_canara_bank += $canara_bank_n;
                    /*echo $this->db->last_query();

                    echo "<pre>"; print_r($canara_bank_m);

                    die();*/
                    /*-----------*/

                    /*Fedreal Bank*/
                    $this->db->select('(SUM(FEES_DTL_AMOUNT)+SUM(fees_newadm_hdr.FM_HDR_LATE_FEES)) as total_federal_bank');
                    $this->db->join('fees_newadm_dtl', 'fees_newadm_hdr.FM_HDR_SRLNO = fees_newadm_dtl.FEES_DTL_HDR_SRLNO', 'left');
                    $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                    $this->db->where("fees_newadm_dtl.FEES_DTL_COL_DATE", $v["FEES_DTL_COL_DATE"]);
                    $this->db->where("fees_newadm_dtl.FEES_DTL_ACC_SEQ", $val["FEES_DTL_ACC_SEQ"]);
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                    $this->db->where("fees_newadm_hdr.FM_HDR_B_NAME", 'FEDERAL');

                    $this->db->group_by("fees_newadm_dtl.FEES_DTL_ACC_SEQ");
                    // $this->db->order_by("ACC_MASTER_NAME");
                    $this->db->stop_cache();
                    $federal_bank_n = $this->db->get('fees_newadm_hdr')->row();

                    (int)$federal_bank_n = empty($federal_bank_n)?'0':$federal_bank_n->total_federal_bank;

                    $dt_wise_total_federal_bank += $federal_bank_n;

                    /*echo $this->db->last_query();

                    echo "<pre>"; print_r($canara_bank_m);

                    die();*/
                    $fees_type = "New Adm.";
//                    $grand_fees = $grand_fees+$val['total_amount'];
                    $dt_wise_total += $val['total_amount'];
                    $html .= '<tr>
                            <td width="250">'.$val['ACC_MASTER_NAME'].'</td>
                            <td width="50">'.$fees_type.'</td>
                            <td width="80" align="right">'.$val['total_row'].'</td>
                            <td width="80" align="right">'.(int)$canara_bank_n.'</td>
                            <td width="80" align="right">'.(int)$federal_bank_n.'</td>
                            <td width="80" align="right">'.(int)$val['total_amount'].'</td>
                        </tr>';
                }
                /*---------------------------------------------------------------------*/

                $grand_fees += $dt_wise_total;
                $grand_fees_total_canara_bank += $dt_wise_total_canara_bank;
                $grand_fees_total_federal_bank += $dt_wise_total_federal_bank;
                $dt_wise_total = $dt_wise_total;
                $dt_wise_total_canara_bank = $dt_wise_total_canara_bank;
                $dt_wise_total_federal_bank = $dt_wise_total_federal_bank;
                $html .= <<<EOD
                    </tbody>
                    <hr width="685">
                    <tr>
                        <td width="250"><strong>Date Wise Total</strong></td>
                        <td width="50"></td>
                        <td width="80" align="right"></td>
                        <td width="80" align="right"><strong>$dt_wise_total_canara_bank</strong></td>
                        <td width="80" align="right"><strong>$dt_wise_total_federal_bank</strong></td>
                        <td width="80" align="right"><strong>$dt_wise_total</strong></td>
                    </tr>
                </table>
                <hr width="685">
                &nbsp;
                <br>
EOD;
            }

            $grand_fees = $grand_fees;
            $grand_fees_total_canara_bank = $grand_fees_total_canara_bank;
            $grand_fees_total_federal_bank = $grand_fees_total_federal_bank;
            $html .= <<<EOD
<table cellspacing="2" style="font-size: 15px" border="1">
<thead>
<tr>
    <td width="420"><strong>Grand Total</strong></td>
    <td width="80" align="right"><strong>$grand_fees_total_canara_bank</strong></td>
    <td width="80" align="right"><strong>$grand_fees_total_federal_bank</strong></td>
    <td width="80" align="right"><strong>$grand_fees</strong></td>
</tr>
</thead>
</table>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/all_fees_type1_report');
        }
    }

    public function all_fees_type2_report() {
        $data['form_type'] = 'all_fees_type2_report';
        $cls_type = $this->db->get('class_type')->result_array();
        $data['class_type'] = $cls_type;
        $data['tab_title'] = 'All Fees Report (Month Wise)';
        $data['section_heading'] = 'All Fees Report (Month Wise) <small>(Print)</small>';
        $data['menu_name'] = 'All Fees Report (Month Wise)';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function print_all_fees_type2_report() {
        if($this->input->post('submit') == 'print_all_fees_type2_report') { //if form submitted
            $date_from = $this->input->post('date_from');
            $date_to = $this->input->post('date_to');
            $class_type = $this->input->post('class_type[]');

            $company = $this->company_name($class_type, 'school');

            $this->db->start_cache();
            $this->db->select('FEES_DTL_ACC_SEQ, ACC_MASTER_NAME,COUNT(FEES_DTL_ACC_SEQ) as total_row,SUM(FEES_DTL_AMOUNT) as total_amount');
            $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = FEES_DTL_ACC_SEQ', 'join');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');

            if (!in_array('all', $class_type)) {
                $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
            }
            if($date_from != null && $date_to != null) {
                if($date_from > $date_to) { //if from date is greater than to date
                    $this->session->set_flashdata('type', 'error');
                    $this->session->set_flashdata('title', 'Naa!');
                    $this->session->set_flashdata('msg', 'From Date must be equals to or less than To Date.');
                    return array('type' => 'redirect', 'page'=>'admin/all_fees_type2_report');
                }
                $this->db->where('FEES_DTL_COL_DATE >=', $date_from);
                $this->db->where('FEES_DTL_COL_DATE <=', $date_to);
            }
            $this->db->group_by('FEES_DTL_ACC_SEQ');
            $this->db->order_by("ACC_MASTER_NAME");
            $this->db->stop_cache();
            $dtl_monthly = $this->db->get('fees_monthly_dtl')->result_array();
            $dtl_yearly = $this->db->get('fees_yearly_dtl')->result_array();
            $dtl_newadm = $this->db->get('fees_newadm_dtl')->result_array();
            $this->db->flush_cache();

            if(count($dtl_monthly) == 0 && count($dtl_yearly) == 0 && count($dtl_newadm) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'No transaction found.');
                return array('type' => 'redirect', 'page'=>'admin/all_fees_type2_report');
            }

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'All Fees Report (Month Wise)';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('All Fees Report (Month Wise)');
            $pdf->SetKeywords('All Fees Report (Month Wise), smg, developed by: https://sketchmeglobal.com');

            if($date_from == null || $date_to == null) {
                $date_range = 'All Dates';
            } else {
                $date_range = date("d-m-Y", strtotime($date_from)).' to '.date("d-m-Y", strtotime($date_to));
            }

            // set default header data
            $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2 , $company->COM_CITY
        <br>
        <strong> All Fees Report (Month Wise): <span style="background-color: black;color: white;"> $date_range </span></strong>
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
            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $grand_fees = 0.00;
            $grand_fees_total_canara_bank = 0.00;
            $grand_fees_total_federal_bank = 0.00;
            $html = '<span style="font-size: 15px; text-align: center;"><strong>Fees A/C Head Wise Compact Report</strong></span>';
            $html .= <<<EOD
            <hr>
            <table >
                <thead>
                <tr>
                    <th width="260"><strong>Fees Name</strong></th>
                    <th width="50"><strong>Fees Type</strong></th>
                    <th width="80" align="right"><strong>Total Tran</strong></th>
                    <th width="80" align="right"><strong>Canara Bank</strong></th>
                    <th width="80" align="right"><strong>Federal Bank</strong></th>
                    <th width="80" align="right"><strong>Total Amount</strong></th>
                </tr>
                </thead>
                <tbody>
EOD;
            foreach ($dtl_monthly as $val) {
                if($val['total_amount'] == 0) {
                    continue;
                }

                /*Canara Bank Monthly*/
                $this->db->select('SUM(FEES_DTL_AMOUNT) as total_canara_bank');
                $this->db->join('fees_monthly_dtl', 'fees_monthly_hdr.FM_HDR_SRLNO = fees_monthly_dtl.FEES_DTL_HDR_SRLNO', 'left');
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                $this->db->where('FM_HDR_COL_DATE >=', $date_from);
                $this->db->where('FM_HDR_COL_DATE <=', $date_to);
                $this->db->where("fees_monthly_dtl.FEES_DTL_ACC_SEQ", $val["FEES_DTL_ACC_SEQ"]);
                if ($class_type != 'all') {
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                }
                $this->db->where("fees_monthly_hdr.FM_HDR_B_NAME", 'CANARA');

                $this->db->group_by("fees_monthly_dtl.FEES_DTL_ACC_SEQ");
                // $this->db->order_by("ACC_MASTER_NAME");
                $this->db->stop_cache();
                $canara_bank_m = $this->db->get('fees_monthly_hdr')->row();
                (int)$canara_bank_m = empty($canara_bank_m)?'0':$canara_bank_m->total_canara_bank;
                $grand_fees_total_canara_bank += $canara_bank_m;
                /*echo $this->db->last_query();
                echo "<pre>"; print_r($canara_bank_m);
                die();*/
                /*-----------*/
                /*Fedreal Bank Monthly*/
                $this->db->select('SUM(FEES_DTL_AMOUNT) as total_federal_bank');
                $this->db->join('fees_monthly_dtl', 'fees_monthly_hdr.FM_HDR_SRLNO = fees_monthly_dtl.FEES_DTL_HDR_SRLNO', 'left');
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                $this->db->where('FM_HDR_COL_DATE >=', $date_from);
                $this->db->where('FM_HDR_COL_DATE <=', $date_to);
                $this->db->where("fees_monthly_dtl.FEES_DTL_ACC_SEQ", $val["FEES_DTL_ACC_SEQ"]);
                if (!in_array('all', $class_type)) {
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                }
                $this->db->where("fees_monthly_hdr.FM_HDR_B_NAME", 'FEDERAL');

                $this->db->group_by("fees_monthly_dtl.FEES_DTL_ACC_SEQ");
                // $this->db->order_by("ACC_MASTER_NAME");
                $this->db->stop_cache();
                $federal_bank_m = $this->db->get('fees_monthly_hdr')->row();
                (int)$federal_bank_m = empty($federal_bank_m)?'0':$federal_bank_m->total_federal_bank;
                $grand_fees_total_federal_bank += $federal_bank_m;
                /*echo $this->db->last_query();
                echo "<pre>"; print_r($canara_bank_m);
                die();*/
                /*------------*/
                $fees_type = "Monthly";
                $grand_fees += $val['total_amount'];
                $html .= '<tr>
                        <td width="260">'.$val['ACC_MASTER_NAME'].'</td>
                        <td width="50">'.$fees_type.'</td>
                        <td width="80" align="right">'.$val['total_row'].'</td>
                        <td width="80" align="right">'.(int)$canara_bank_m.'</td>
                        <td width="80" align="right">'.(int)$federal_bank_m.'</td>
                        <td width="80" align="right">'.(int)$val['total_amount'].'</td>
                    </tr>';
            }
            foreach ($dtl_yearly as $val) {
                if($val['total_amount'] == 0) {
                    continue;
                }
                /*Canara Bank Yearly*/
                $this->db->select('SUM(FEES_DTL_AMOUNT) as total_canara_bank');
                $this->db->join('fees_yearly_dtl', '   fees_yearly_hdr.FM_HDR_SRLNO = fees_yearly_dtl.FEES_DTL_HDR_SRLNO', 'left');
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                $this->db->where('FM_HDR_COL_DATE >=', $date_from);
                $this->db->where('FM_HDR_COL_DATE <=', $date_to);
                $this->db->where("fees_yearly_dtl.FEES_DTL_ACC_SEQ", $val["FEES_DTL_ACC_SEQ"]);
                if (!in_array('all', $class_type)) {
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                }
                $this->db->where("  fees_yearly_hdr.FM_HDR_B_NAME", 'CANARA');

                $this->db->group_by("fees_yearly_dtl.FEES_DTL_ACC_SEQ");
                // $this->db->order_by("ACC_MASTER_NAME");
                $this->db->stop_cache();
                $canara_bank_y = $this->db->get('   fees_yearly_hdr')->row();
                (int)$canara_bank_y = empty($canara_bank_y)?'0':$canara_bank_y->total_canara_bank;
                $grand_fees_total_canara_bank += $canara_bank_y;
                /*echo $this->db->last_query();
                echo "<pre>"; print_r($canara_bank_m);
                die();*/
                /*-----------*/
                /*Fedreal Bank Yearly*/
                $this->db->select('SUM(FEES_DTL_AMOUNT) as total_federal_bank');
                $this->db->join('fees_yearly_dtl', '   fees_yearly_hdr.FM_HDR_SRLNO = fees_yearly_dtl.FEES_DTL_HDR_SRLNO', 'left');
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                $this->db->where('FM_HDR_COL_DATE >=', $date_from);
                $this->db->where('FM_HDR_COL_DATE <=', $date_to);
                $this->db->where("fees_yearly_dtl.FEES_DTL_ACC_SEQ", $val["FEES_DTL_ACC_SEQ"]);
                if (!in_array('all', $class_type)) {
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                }
                $this->db->where("  fees_yearly_hdr.FM_HDR_B_NAME", 'FEDERAL');

                $this->db->group_by("fees_yearly_dtl.FEES_DTL_ACC_SEQ");
                // $this->db->order_by("ACC_MASTER_NAME");
                $this->db->stop_cache();
                $federal_bank_y = $this->db->get('  fees_yearly_hdr')->row();
                (int)$federal_bank_y = empty($federal_bank_y)?'0':$federal_bank_y->total_federal_bank;
                $grand_fees_total_federal_bank += $federal_bank_y;
                /*echo $this->db->last_query();
                echo "<pre>"; print_r($canara_bank_m);
                die();*/
                /*------------*/
                $fees_type = "Yearly";
                $grand_fees += $val['total_amount'];
                $html .= '<tr>
                        <td width="260">'.$val['ACC_MASTER_NAME'].'</td>
                        <td width="50">'.$fees_type.'</td>
                        <td width="80" align="right">'.$val['total_row'].'</td>
                        <td width="80" align="right">'.(int)$canara_bank_y.'</td>
                        <td width="80" align="right">'.(int)$federal_bank_y.'</td>
                        <td width="80" align="right">'.(int)$val['total_amount'].'</td>
                    </tr>';
            }
            foreach ($dtl_newadm as $val) {
                if($val['total_amount'] == 0) {
                    continue;
                }
                /*Canara Bank Admission*/
                $this->db->select('SUM(FEES_DTL_AMOUNT) as total_canara_bank');
                $this->db->join('   fees_newadm_dtl', 'fees_newadm_hdr.FM_HDR_SRLNO =fees_newadm_dtl.FEES_DTL_HDR_SRLNO', 'left');
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                $this->db->where('FM_HDR_COL_DATE >=', $date_from);
                $this->db->where('FM_HDR_COL_DATE <=', $date_to);
                $this->db->where("  fees_newadm_dtl.FEES_DTL_ACC_SEQ", $val["FEES_DTL_ACC_SEQ"]);
                if (!in_array('all', $class_type)) {
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                }
                $this->db->where("fees_newadm_hdr.FM_HDR_B_NAME", 'CANARA');

                $this->db->group_by("   fees_newadm_dtl.FEES_DTL_ACC_SEQ");
                // $this->db->order_by("ACC_MASTER_NAME");
                $this->db->stop_cache();
                $canara_bank_n = $this->db->get('fees_newadm_hdr')->row();
                (int)$canara_bank_n = empty($canara_bank_n)?'0':$canara_bank_n->total_canara_bank;
                $grand_fees_total_canara_bank += $canara_bank_n;
                /*echo $this->db->last_query();
                echo "<pre>"; print_r($canara_bank_m);
                die();*/
                /*-----------*/
                /*Fedreal Bank Admission*/
                $this->db->select('SUM(FEES_DTL_AMOUNT) as total_federal_bank');
                $this->db->join('   fees_newadm_dtl', 'fees_newadm_hdr.FM_HDR_SRLNO =fees_newadm_dtl.FEES_DTL_HDR_SRLNO', 'left');
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FEES_DTL_STD_CS_SEC', 'left');
                $this->db->where('FM_HDR_COL_DATE >=', $date_from);
                $this->db->where('FM_HDR_COL_DATE <=', $date_to);
                $this->db->where("  fees_newadm_dtl.FEES_DTL_ACC_SEQ", $val["FEES_DTL_ACC_SEQ"]);
                if (!in_array('all', $class_type)) {
                    $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
                }
                $this->db->where("fees_newadm_hdr.FM_HDR_B_NAME", 'FEDERAL');
                $this->db->group_by("fees_newadm_dtl.FEES_DTL_ACC_SEQ");
                // $this->db->order_by("ACC_MASTER_NAME");
                $this->db->stop_cache();
                $federal_bank_n = $this->db->get('fees_newadm_hdr')->row();
                (int)$federal_bank_n = empty($federal_bank_n)?'0':$federal_bank_n->total_federal_bank;
                $grand_fees_total_federal_bank += $federal_bank_n;
                /*echo $this->db->last_query();
                echo "<pre>"; print_r($canara_bank_m);
                die();*/
                /*------------*/
                $fees_type = "New Adm.";
                $grand_fees += $val['total_amount'];
                $html .= '<tr>
                        <td width="260">'.$val['ACC_MASTER_NAME'].'</td>
                        <td width="50">'.$fees_type.'</td>
                        <td width="80" align="right">'.$val['total_row'].'</td>
                        <td width="80" align="right">'.(int)$canara_bank_n.'</td>
                        <td width="80" align="right">'.(int)$federal_bank_n.'</td>
                        <td width="80" align="right">'.(int)$val['total_amount'].'</td>
                    </tr>';
            }

            $grand_fees = $grand_fees;
            $grand_fees_total_canara_bank = $grand_fees_total_canara_bank;
            $grand_fees_total_federal_bank = $grand_fees_total_federal_bank;

            $html .= <<<EOD
</tbody>
</table>
<hr>
&nbsp;
<br>
<br>
<table cellspacing="2" style="font-size: 15px" border="1">
<thead>
<tr>
    <th width="420"><strong>Grand Total</strong></th>
    <th width="80" align="right"><strong>$grand_fees_total_canara_bank</strong></th>
    <th width="80" align="right"><strong>$grand_fees_total_federal_bank</strong></th>
    <th width="80" align="right"><strong>$grand_fees</strong></th>
</tr>
</thead>
</table>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/all_fees_type2_report');
        }
    }

    
    public function std_fees_ledger_report() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'std_fees_ledger_report';

        $data['tab_title'] = 'Student Paid/Due Fees Ledger Report';
        $data['section_heading'] = 'Student Paid/Due Fees Ledger Report <small>(Print)</small>';
        $data['menu_name'] = 'Student Paid/Due Fees Ledger Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }
    
    public function print_due_undertaking_report(){
         if($this->input->post('submit') == 'print_due_undertaking_report') {
            
            $class = $this->input->post('undertaking_class');
            $paid_date = date('d-M-Y',strtotime($this->input->post('paid_date')));
            $student = $this->input->post('undertaking_student');
          
            $month = $this->input->post('month');
            $dt = explode("/",$month);
            $mnth = $dt[0];
            $yer = $dt[1];
           
            $company = $this->db->get_where('company',array('SCHOOL_TYPE' => 4))->row();
            
            $month = str_pad($mnth, 2, '0', STR_PAD_LEFT);
            $start_date = date("Y-m-d", mktime(0, 0, 0, $month, 1, $yer));
            $end_date = date("Y-m-t", mktime(0, 0, 0, $month, 1, $yer));
            $this->db->where('effective_from <=', $end_date);
            $signature = $this->db->get('signatures')->row();
            
            $this->db->where_in('CS_SEQ', $class);
            $classes =  $this->db->get('class_sec_hdr')->result();

            
            $months_arr = array("JAN"=>"1","FEB"=>"2","MAR"=>"3","APR"=>"4","MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12");
            
            //take only till selected month (month from date input)
            $months_arr = array_slice($months_arr, 0, array_search($mnth, array_values($months_arr))+1, true);
            $selected_month = array_search($mnth , $months_arr);
            
            
           
            $this->db->select('student_details.STD_SEQ, student_details.STD_CONSC,student_details.ST_FULL_NAME,student_details.STD_ROLLNO,student_parent_details.STD_FTH_NAME,student_parent_details.STD_MTH_NAME,student_details.STD_PH_NO,class_sec_hdr.Class_Name,class_sec_hdr.Sec_Name');
            $this->db->from('student_details');
            $this->db->join('student_parent_details', 'student_details.STD_SEQ = student_parent_details.STD_SEQ');
            $this->db->join('class_sec_hdr', 'student_details.STD_CS_SEQ = class_sec_hdr.CS_SEQ');
            $this->db->where('student_details.STD_SEQ', $student);
            
            $query = $this->db->get();
            $result = $query->result();
            
            $student_name = $result[0]->ST_FULL_NAME;
            $father_name = $result[0]->STD_FTH_NAME;
            $mother_name = $result[0]->STD_MTH_NAME;
            $class_name = $result[0]->Class_Name;
            $section_name = $result[0]->Sec_Name;
            $roll_no = $result[0]->STD_ROLLNO;
            $mobile = $result[0]->STD_PH_NO;
            $std_consc = $result[0]->STD_CONSC;
            
             /*Yearly Fees */
            $this->db->select('ACC_MASTER_NAME,Fees');
            $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
            $this->db->where('CS_SEQ', $class);
            $this->db->where('class_sec_dtl.CS_FEES_TYPE', '1'); //yearly fees
            $this->db->where('Fees !=', '0');
            $yearly_fees_result = $this->db->get('class_sec_dtl')->result_array();

            $yearly_total_fees = array_sum(array_column($yearly_fees_result, 'Fees'));

            $this->db->select('SUM(FEES_DTL_AMOUNT) as total_paid_year_fees');
            $this->db->where('FEES_DTL_STD_SEQ', $student);
            $this->db->where('FEES_DTL_STD_CS_SEC', $class);
            $this->db->where('FEES_DTL_FIN_YEAR', $company->COM_FIN_YEAR);
            $result_paid_yearly_fee = $this->db->get('fees_yearly_dtl')->result_array();

            $yearly_due_fees = ($yearly_total_fees - $result_paid_yearly_fee[0]['total_paid_year_fees']);
           
            if ($std_consc == '1') {
                $this->db->select('fees_concession.Fees');
                $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE  and class_sec_dtl.CS_SEQ = fees_concession.class_id', 'left');
                $this->db->where('fees_concession.std_id', $student);
                $this->db->where('class_sec_dtl.CS_SEQ', $class);
                $this->db->where('class_sec_dtl.CS_FEES_TYPE', '0');  //monthly fees
                $this->db->where('fees_concession.ACC_MASTER_CODE', '4');  //only tution fees
                $this->db->where('class_sec_dtl.Fees !=', '0');
                $monthly_fees_con = $this->db->get('fees_concession')->result_array();

                // echo $this->db->last_query(); die();
                if (count($monthly_fees_con) > 0) {
                    $total_month_amount = array_sum(array_column($monthly_fees_con, 'Fees'));
                } else {
                    $this->db->select('ACC_MASTER_NAME,Fees');
                    $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                    $this->db->where('CS_SEQ', $class);
                    $this->db->where('CS_FEES_TYPE', '0'); //monthly fees
                    $this->db->where('acc_master.ACC_MASTER_CODE', '4');  // Tution Fees Only
                    $this->db->where('Fees !=', '0');
                    $monthly_fees_result = $this->db->get('class_sec_dtl')->result_array();
                    // echo $this->db->last_query(); die();
                    $total_month_amount = array_sum(array_column($monthly_fees_result, 'Fees'));
                }

            }else{

                $this->db->select('ACC_MASTER_NAME,Fees');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                $this->db->where('CS_SEQ', $class);
                $this->db->where('CS_FEES_TYPE', '0'); //monthly fees
                $this->db->where('Fees !=', '0');
                $monthly_fees_result = $this->db->get('class_sec_dtl')->result_array();

                $monthly_fees = array_sum(array_column($monthly_fees_result, 'Fees'));
                $tot_fees = array_sum(array_column($monthly_fees_result, 'Fees'));
                $total_month_amount = $tot_fees;

            }
            
            $month_names = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
                7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
            ];
            
            $special_classes = [26, 27, 28, 29];

            if (in_array($class, $special_classes)) {
                if ($mnth >= 5) {
                    $months_arr = range(5, $mnth);
                } else {
                    $months_arr = array_merge(range(5, 12), range(1, $mnth));
                }
            } else {
                $months_arr = range(1, $mnth);
            }
            
            $this->db->select('FEES_DTL_MONTH');
            $this->db->where('FEES_DTL_STD_SEQ', $student);
            $this->db->where('FEES_DTL_MONTH <=', $mnth);
            $this->db->group_by('FEES_DTL_MONTH');
            $paid_rs = $this->db->get('fees_monthly_dtl')->result_array();
            $paid_months = array_column($paid_rs, 'FEES_DTL_MONTH');
            $due_months = array_diff($months_arr, $paid_months);
            sort($due_months);
            $start_month = $month_names[reset($due_months)];
            $end_month = $month_names[end($due_months)];
            $total_due_month = count($due_months);
            //$all_due_month_names = implode(', ', array_keys($due_months));
            $all_due_month_names = $start_month === $end_month ? $start_month : "$start_month-$end_month";
            
            $total_month_due = ($total_month_amount * $total_due_month);
            $grand_total = ($total_month_amount * $total_due_month) + $yearly_due_fees;
              
            
            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'STATEMENT OF UNDERTAKING ';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('ST. ANTHONY’S HIGH SCHOOL');
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords($doc_name.', smg, developed by: https://sketchmeglobal.com');

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
           
            //  $html="<section class='sheet padding-10mm;' style='border:1px solid black'>";

            //             $html .= "<div class='body-content' style='padding:10px'>
            //                     <header>
            //                         <h3 class='text-center'>ST. ANTHONY’S HIGH SCHOOL</h3>
            //                         <h4 class='text-center'>19 MARKET STREET, KOLKATA- 700087</h4>
            //                         <h4 class='text-center'><u>STATEMENT OF UNDERTAKING </u></h4><br>
            //                         <h4 class='text-center'>OFFICE COPY</h4>
                                   
            //                     </header>
            //                 <mainbody>
            //                     <p>
            //                         To
            //                         <br>
            //                         The Headmaster
            //                         <br>
            //                         St. Anthony’s high school
            //                         <br>
            //                         19 Market Street,
            //                         <br>
            //                         Kolkata- 700087 
            //                         <br><br>
                                  
            //                     </p>
                           
            //                      <p style='line-height: 2.5;'>I Mr. / Mrs. <u>$father_name/$mother_name</u> Father/ Mother of  <u>$student_name</u>  of class  <u>$class_name</u>  section  <u>$section_name</u>  roll no $roll_no   
            //                      will clear the pending fees amounting to  $all_due_month_names $yer - Rs.$total_month_due/-, ANNUAL FEES <u>$yearly_due_fees</u>  TOTAL RS.= <b>$grand_total</b>/-by or before <u><b>$paid_date</b></u>’  
            //                      failing which the school can take necessary action as deemed fit. </p>
            //                      <br/><br />
            //                          ____________________________
            //                     </p><br>
            //                     <p>Parents Signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mobile No:<u><b>$mobile</b></u></p>
            //                     <hr>
            //                      <header>
            //                         <h3 class='text-center'>ST. ANTHONY’S HIGH SCHOOL</h3>
            //                         <h4 class='text-center'>19 MARKET STREET, KOLKATA- 700087</h4>
            //                         <h4 class='text-center'><u>STATEMENT OF UNDERTAKING </u></h4><br>
            //                         <h4 class='text-center'>PARENTS COPY</h4>
                                   
            //                     </header>
            //                 <mainbody>
            //                     <p>
            //                         To
            //                         <br>
            //                         The Headmaster
            //                         <br>
            //                         St. Anthony’s high school
            //                         <br>
            //                         19 Market Street,
            //                         <br>
            //                         Kolkata- 700087 
            //                         <br><br>
                                  
            //                     </p>
                           
            //                      <p style='line-height: 2.5;'>I Mr. / Mrs. <u>$student_name</u> Father/ Mother of  <u>$gurdian_name</u>  of class  <u>$class_name</u>  section  <u>$section_name</u>  roll no $roll_no   
            //                      will clear the pending fees amounting to  $all_due_month_names $yer - Rs.$total_month_due/-, ANNUAL FEES <u>$yearly_due_fees</u>  TOTAL RS.= <b>$grand_total</b>/-by or before <u><b>$paid_date</b></u>’  
            //                      failing which the school can take necessary action as deemed fit. </p>
            //                      <br/><br />
            //                          ____________________________
            //                     </p><br>
            //                     <p>Parents Signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mobile No:<u><b>$mobile</b></u></p>
            //                     ";
            
           $html = "
                    <section class='sheet' style='padding: 10mm; border: 1px solid black; height: 100%; box-sizing: border-box; display: flex; flex-direction: column; justify-content: space-between;'>
                        <!-- Office Copy -->
                        <div style='width: 100%; height: 50%; margin-bottom: 2%; border-bottom: 1px solid black; padding: 10px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: center;'>
                            <header>
                                <h3 class='text-center'>ST. ANTHONY’S HIGH SCHOOL</h3>
                                <h4 class='text-center'>19 MARKET STREET, KOLKATA- 700087</h4>
                                <h4 class='text-center'><u>STATEMENT OF UNDERTAKING</u></h4><br>
                                <h4 class='text-center'>OFFICE COPY</h4>
                            </header>
                            <mainbody>
                                <p>
                                    To<br>
                                    The Headmaster<br>
                                    St. Anthony’s High School<br>
                                    19 Market Street,<br>
                                    Kolkata- 700087<br><br>
                                </p>
                                <p style='line-height: 2.5;'>
                                    I Mr. / Mrs. <u>$father_name/$mother_name</u>, Father/ Mother of <u>$student_name</u> of class <u>$class_name</u> section <u>$section_name</u>, roll no $roll_no   
                                    will clear the pending fees amounting to $all_due_month_names $yer - Rs.$total_month_due/-, ANNUAL FEES <u>$yearly_due_fees</u>, TOTAL RS.= <b>$grand_total</b>/- by or before <u><b>$paid_date</b></u>, 
                                    failing which the school can take necessary action as deemed fit.
                                </p>
                                <br><br>
                                <p>____________________________</p><br>
                                <p>Parents Signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mobile No: <u><b>$mobile</b></u></p>
                            </mainbody>
                        </div>
                        
                        <!-- Parents Copy -->
                        <div style='width: 100%; height: 50%; padding: 10px; box-sizing: border-box; display: flex; flex-direction: column; justify-content: center;'>
                            <header>
                                <h3 class='text-center'>ST. ANTHONY’S HIGH SCHOOL</h3>
                                <h4 class='text-center'>19 MARKET STREET, KOLKATA- 700087</h4>
                                <h4 class='text-center'><u>STATEMENT OF UNDERTAKING</u></h4><br>
                                <h4 class='text-center'>PARENTS COPY</h4>
                            </header>
                            <mainbody>
                                <p>
                                    To<br>
                                    The Headmaster<br>
                                    St. Anthony’s High School<br>
                                    19 Market Street,<br>
                                    Kolkata- 700087<br><br>
                                </p>
                                <p style='line-height: 2.5;'>
                                    I Mr. / Mrs. <u>$father_name/$mother_name</u>, Father/ Mother of <u>$student_name</u> of class <u>$class_name</u> section <u>$section_name</u>, roll no $roll_no   
                                    will clear the pending fees amounting to $all_due_month_names $yer - Rs.$total_month_due/-, ANNUAL FEES <u>$yearly_due_fees</u>, TOTAL RS.= <b>$grand_total</b>/- by or before <u><b>$paid_date</b></u>, 
                                    failing which the school can take necessary action as deemed fit.
                                </p>
                                <br><br>
                                <p>____________________________</p><br>
                                <p>Parents Signature &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mobile No: <u><b>$mobile</b></u></p>
                            </mainbody>
                        </div>
                    </section>";



                        
                    

                    $data['tab_title'] = 'Due Undertaking Report';
                    $data['section_heading'] = 'Due Undertaking Report <small>(Print)</small>';
                    $data['menu_name'] = 'Due Undertaking Report';
                    $data['sections'] = $html;
                   

                    return array('type' => 'load_view', 'page' => 'due_undertaking_report', 'data' => $data);
           
         }
    }

    public function print_std_fees_ledger_report() {
        if($this->input->post('submit') == 'print_std_paid_fees_ledger_report') { //if form submitted
            $class = $this->input->post('class');

            $company = $this->company_name((array)$class);
            if($class != 'all') {
                $this->db->where('CS_SEQ', $class);
            }
            $this->db->order_by('Class_Name,Sec_Name');
            $cls = $this->db->get('class_sec_hdr')->result_array();

            if(count($cls) == 0) { //if class does not exists
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Class not found.');
                return array('type' => 'redirect', 'page'=>'admin/std_fees_ledger_report');
            }

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Student Fees Ledger Report';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('Student Fees Ledger Report');
            $pdf->SetKeywords('Student Fees Ledger Report, smg, developed by: https://sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
            <div style="text-align:center;">
            <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
            <br>
            $company->COM_ADD2 , $company->COM_CITY
            <br>
            <strong><span style="background-color: black;color: white;"> Student Paid Fees Ledger Report </span></strong>
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
            $pdf->SetFont('times', '', 8, '', true);

            $grand_fees = array('0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00');

            foreach ($cls as $v) { //this loop is for all classes
                $html = '';
                $this->db->select('STD_SEQ,STD_REGNO,STD_SRLNO,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME');
                $this->db->where("STD_LEFT", 0);
                $this->db->where('STD_CS_SEQ', $v['CS_SEQ']);
                $this->db->order_by("STD_ROLLNO");
                $std = $this->db->get('student_details')->result_array();
                $tot_std = count($std);
                $class_wise_fees = array('0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00');

                if ($tot_std > 0) { //if students exists in that class
                    // Add a page
                    $pdf->AddPage('L', 'A4');

                    $html .= '<span style="font-size: 15px;">Class & Section: <strong>' . $v["Class_Name"] . ' - ' . $v["Sec_Name"] . '</strong></span>
                            <span style="font-size: 11px;">(Total Students: <strong>' . $tot_std . ')</strong></span>
                            <hr>';
                    $html .= <<<EOD
                    <table cellspacing="2">
                        <thead>
                        <tr>
                            <th width="25"><strong>#</strong></th>
                            <th width="60"><strong>Adm. No</strong></th>
                            <th width="30"><strong>Roll</strong></th>
                            <th width="150"><strong>Student Name</strong></th>
                            <th width="35" align="right"><strong>April</strong></th>
                            <th width="35" align="right"><strong>May</strong></th>
                            <th width="35" align="right"><strong>June</strong></th>
                            <th width="35" align="right"><strong>July</strong></th>
                            <th width="40" align="right"><strong>August</strong></th>
                            <th width="55" align="right"><strong>September</strong></th>
                            <th width="45" align="right"><strong>October</strong></th>
                            <th width="55" align="right"><strong>November</strong></th>
                            <th width="55" align="right"><strong>December</strong></th>
                            <th width="45" align="right"><strong>January</strong></th>
                            <th width="45" align="right"><strong>February</strong></th>
                            <th width="40" align="right"><strong>March</strong></th>
                            <th width="60" align="right"><strong>Yearly Fees</strong></th>
                            <th width="80" align="right"><strong>Total</strong></th>
                        </tr>
                        </thead>
                        <tbody>
EOD;
                    $srl_no = 1;
                    foreach ($std as $s) { //this loop is for all students in that class
                        $this->db->select('FEES_DTL_MONTH,SUM(FEES_DTL_AMOUNT) as sum');
                        $this->db->where("FEES_DTL_STD_SEQ", $s["STD_SEQ"]);
                        $this->db->where("FEES_DTL_STD_CS_SEC", $v["CS_SEQ"]);
                        $this->db->group_by("FEES_DTL_MONTH");
                        $dtl = $this->db->get('fees_monthly_dtl')->result_array();

                        $this->db->select('SUM(FEES_DTL_AMOUNT) as sum');
                        $this->db->where("FEES_DTL_STD_SEQ", $s["STD_SEQ"]);
                        $this->db->where("FEES_DTL_STD_CS_SEC", $v["CS_SEQ"]);
                        $this->db->group_by("FEES_DTL_STD_SEQ");
                        $dtl_y = $this->db->get('fees_yearly_dtl')->result_array();
                        if($dtl_y != null) {$y_amount = $dtl_y[0]["sum"];} else {$y_amount = 0.00;}
                        $student_wise_total = 0.00;

                        $html .= '<tr>
                                    <td width="25">'.$srl_no.'</td>
                                    <td width="60">'.$s["STD_SRLNO"].'</td>
                                    <td width="30">'.$s["STD_ROLLNO"].'</td>
                                    <td width="150">'.$s["STD_FNAME"].' '.$s["STD_MNAME"].' '.$s["STD_LNAME"].'</td>';

                        //april
                        $key = array_keys(array_column($dtl, 'FEES_DTL_MONTH'), 4);
                        if($key != null) {$m_amount = $dtl[$key[0]]["sum"];} else {$m_amount = 0.00;}
                        $class_wise_fees[0] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="35" align="right">'.number_format($m_amount,2).'</td>';
                        //may
                        $key = array_keys(array_column($dtl, 'FEES_DTL_MONTH'), 5);
                        if($key != null) {$m_amount = $dtl[$key[0]]["sum"];} else {$m_amount = 0.00;}
                        $class_wise_fees[1] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="35" align="right">'.number_format($m_amount,2).'</td>';
                        //june
                        $key = array_keys(array_column($dtl, 'FEES_DTL_MONTH'), 6);
                        if($key != null) {$m_amount = $dtl[$key[0]]["sum"];} else {$m_amount = 0.00;}
                        $class_wise_fees[2] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="35" align="right">'.number_format($m_amount,2).'</td>';
                        //july
                        $key = array_keys(array_column($dtl, 'FEES_DTL_MONTH'), 7);
                        if($key != null) {$m_amount = $dtl[$key[0]]["sum"];} else {$m_amount = 0.00;}
                        $class_wise_fees[3] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="35" align="right">'.number_format($m_amount,2).'</td>';
                        //august
                        $key = array_keys(array_column($dtl, 'FEES_DTL_MONTH'), 8);
                        if($key != null) {$m_amount = $dtl[$key[0]]["sum"];} else {$m_amount = 0.00;}
                        $class_wise_fees[4] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="40" align="right">'.number_format($m_amount,2).'</td>';
                        //september
                        $key = array_keys(array_column($dtl, 'FEES_DTL_MONTH'), 9);
                        if($key != null) {$m_amount = $dtl[$key[0]]["sum"];} else {$m_amount = 0.00;}
                        $class_wise_fees[5] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="55" align="right">'.number_format($m_amount,2).'</td>';
                        //october
                        $key = array_keys(array_column($dtl, 'FEES_DTL_MONTH'), 10);
                        if($key != null) {$m_amount = $dtl[$key[0]]["sum"];} else {$m_amount = 0.00;}
                        $class_wise_fees[6] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="45" align="right">'.number_format($m_amount,2).'</td>';
                        //november
                        $key = array_keys(array_column($dtl, 'FEES_DTL_MONTH'), 11);
                        if($key != null) {$m_amount = $dtl[$key[0]]["sum"];} else {$m_amount = 0.00;}
                        $class_wise_fees[7] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="55" align="right">'.number_format($m_amount,2).'</td>';
                        //december
                        $key = array_keys(array_column($dtl, 'FEES_DTL_MONTH'), 12);
                        if($key != null) {$m_amount = $dtl[$key[0]]["sum"];} else {$m_amount = 0.00;}
                        $class_wise_fees[8] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="55" align="right">'.number_format($m_amount,2).'</td>';
                        //january
                        $key = array_keys(array_column($dtl, 'FEES_DTL_MONTH'), 1);
                        if($key != null) {$m_amount = $dtl[$key[0]]["sum"];} else {$m_amount = 0.00;}
                        $class_wise_fees[9] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="45" align="right">'.number_format($m_amount,2).'</td>';
                        //february
                        $key = array_keys(array_column($dtl, 'FEES_DTL_MONTH'), 2);
                        if($key != null) {$m_amount = $dtl[$key[0]]["sum"];} else {$m_amount = 0.00;}
                        $class_wise_fees[10] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="45" align="right">'.number_format($m_amount,2).'</td>';
                        //march
                        $key = array_keys(array_column($dtl, 'FEES_DTL_MONTH'), 3);
                        if($key != null) {$m_amount = $dtl[$key[0]]["sum"];} else {$m_amount = 0.00;}
                        $class_wise_fees[11] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="40" align="right">'.number_format($m_amount,2).'</td>';

                        $class_wise_fees[12] += $y_amount; $student_wise_total += $y_amount;
                        $class_wise_fees[13] += $student_wise_total;
                        $html .= '<td width="60" align="right">'.number_format($y_amount,2).'</td>
                                    <td width="80" align="right">'.number_format($student_wise_total,2).'</td>            
                                </tr>';
                        $srl_no++;
                    }

                    $html .= '
                        </tbody>
                        <hr>
                        <tr>
                            <td width="265"><strong>Class Wise Total</strong></td>
                            <td width="45" align="right">'.number_format($class_wise_fees[0],2).'</td>
                            <td width="35" align="right">'.number_format($class_wise_fees[1],2).'</td>
                            <td width="35" align="right">'.number_format($class_wise_fees[2],2).'</td>
                            <td width="35" align="right">'.number_format($class_wise_fees[3],2).'</td>
                            <td width="40" align="right">'.number_format($class_wise_fees[4],2).'</td>
                            <td width="55" align="right">'.number_format($class_wise_fees[5],2).'</td>
                            <td width="45" align="right">'.number_format($class_wise_fees[6],2).'</td>
                            <td width="55" align="right">'.number_format($class_wise_fees[7],2).'</td>
                            <td width="55" align="right">'.number_format($class_wise_fees[8],2).'</td>
                            <td width="45" align="right">'.number_format($class_wise_fees[9],2).'</td>
                            <td width="45" align="right">'.number_format($class_wise_fees[10],2).'</td>
                            <td width="40" align="right">'.number_format($class_wise_fees[11],2).'</td>
                            <td width="60" align="right">'.number_format($class_wise_fees[12],2).'</td>
                            <td width="80" align="right">'.number_format($class_wise_fees[13],2).'</td>
                        </tr>
                    </table>
                    <hr>
                    &nbsp;
                    <br>
                    <br>';

                    // Print text using writeHTMLCell()
                    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
                }
                //adding class wise fees to grand fees
                for($i=0; $i<=13; $i++) {
                    $grand_fees[$i] += $class_wise_fees[$i];
                }
            }

            if($html == '') { //if no content
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'There is no student for that class.');
                return array('type' => 'redirect', 'page'=>'admin/std_fees_ledger_report');
            }

            $html1 = '
            <hr>
                <table cellspacing="2"  border="">
                <tbody>
                <tr style="font-size: 7px">
                    <td width="270"><strong style="font-size: 15px">Grand Total</strong></td>
                    <td width="35" align="right">'.number_format($grand_fees[0],2).'</td>
                    <td width="35" align="right">'.number_format($grand_fees[1],2).'</td>
                    <td width="35" align="right">'.number_format($grand_fees[2],2).'</td>
                    <td width="35" align="right">'.number_format($grand_fees[3],2).'</td>
                    <td width="40" align="right">'.number_format($grand_fees[4],2).'</td>
                    <td width="55" align="right">'.number_format($grand_fees[5],2).'</td>
                    <td width="45" align="right">'.number_format($grand_fees[6],2).'</td>
                    <td width="55" align="right">'.number_format($grand_fees[7],2).'</td>
                    <td width="55" align="right">'.number_format($grand_fees[8],2).'</td>
                    <td width="45" align="right">'.number_format($grand_fees[9],2).'</td>
                    <td width="45" align="right">'.number_format($grand_fees[10],2).'</td>
                    <td width="40" align="right">'.number_format($grand_fees[11],2).'</td>
                    <td width="60" align="right">'.number_format($grand_fees[12],2).'</td>
                    <td width="80" align="right">'.number_format($grand_fees[13],2).'</td>
                </tr>
                </tbody>
                </table>
            <hr>';

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html1, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        }
        elseif($this->input->post('submit') == 'print_std_due_fees_ledger_report') { //if form submitted
            $class = $this->input->post('class');

            $company = $this->company_name((array)$class);
            if($class != 'all') {
                $this->db->where('CS_SEQ', $class);
            }
            $this->db->order_by('Class_Name,Sec_Name');
            $cls = $this->db->get('class_sec_hdr')->result_array();

            if(count($cls) == 0) { //if class does not exists
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Class not found.');
                return array('type' => 'redirect', 'page'=>'admin/std_fees_ledger_report');
            }

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Student Fees Ledger Report';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('Student Fees Ledger Report');
            $pdf->SetKeywords('Student Fees Ledger Report, smg, developed by: https://sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
            <div style="text-align:center;">
            <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
            <br>
            $company->COM_ADD2 , $company->COM_CITY
            <br>
            <strong><span style="background-color: black;color: white;"> Student Due Fees Ledger Report </span></strong>
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
            $pdf->SetFont('times', '', 8, '', true);

            $grand_fees = array('0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00');

            foreach ($cls as $v) { //this loop is for all classes
                $html = '';
                $this->db->select('STD_SEQ,STD_REGNO,STD_SRLNO,STD_ROLLNO,STD_FNAME,STD_MNAME,STD_LNAME,STD_CONSC');
                $this->db->where("STD_LEFT", 0);
                $this->db->where('STD_CS_SEQ', $v['CS_SEQ']);
                $this->db->order_by("STD_ROLLNO");
                $std = $this->db->get('student_details')->result_array();
                $tot_std = count($std);
                $class_wise_fees = array('0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00');

                if ($tot_std > 0) { //if students exists in that class
                    // Add a page
                    $pdf->AddPage('L', 'A4');

                    $html .= '<span style="font-size: 15px;">Class & Section: <strong>' . $v["Class_Name"] . ' - ' . $v["Sec_Name"] . '</strong></span>
                            <span style="font-size: 11px;">(Total Students: <strong>' . $tot_std . ')</strong></span>
                            <hr>';
                    $html .= <<<EOD
                    <table cellspacing="2">
                        <thead>
                        <tr>
                            <th width="25"><strong>#</strong></th>
                            <th width="60"><strong>Adm. No</strong></th>
                            <th width="30"><strong>Roll</strong></th>
                            <th width="150"><strong>Student Name</strong></th>
                            <th width="35" align="right"><strong>April</strong></th>
                            <th width="35" align="right"><strong>May</strong></th>
                            <th width="35" align="right"><strong>June</strong></th>
                            <th width="35" align="right"><strong>July</strong></th>
                            <th width="40" align="right"><strong>August</strong></th>
                            <th width="55" align="right"><strong>September</strong></th>
                            <th width="45" align="right"><strong>October</strong></th>
                            <th width="55" align="right"><strong>November</strong></th>
                            <th width="55" align="right"><strong>December</strong></th>
                            <th width="45" align="right"><strong>January</strong></th>
                            <th width="45" align="right"><strong>February</strong></th>
                            <th width="40" align="right"><strong>March</strong></th>
                            <th width="60" align="right"><strong>Yearly Fees</strong></th>
                            <th width="80" align="right"><strong>Total</strong></th>
                        </tr>
                        </thead>
                        <tbody>
EOD;
                    $srl_no = 1;
                    foreach ($std as $s) { //this loop is for all students in that class
                        $student_wise_total = 0.00;
                        $months_arr = array("APR"=>"4","MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12","JAN"=>"1","FEB"=>"2","MAR"=>"3");

                        $this->db->select('FEES_DTL_MONTH');
                        $this->db->where('FEES_DTL_STD_SEQ', $s['STD_SEQ']);
                        $this->db->where('FEES_DTL_STD_CS_SEC', $v["CS_SEQ"]);
                        $this->db->group_by('FEES_DTL_MONTH');
                        $paid_rs = $this->db->get('fees_monthly_dtl')->result_array();
                        $paid_months = array_column($paid_rs, 'FEES_DTL_MONTH');
                        $due_months = array_diff($months_arr, $paid_months);

                        #get student's MONTHLY fee
                        #concession fee
                        if ($s['STD_CONSC'] == 1) {
                            $this->db->select('COALESCE(SUM(fees_concession.Fees), 0) AS monthly_fee');
                            $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                            $this->db->where('fees_concession.std_id', $s['STD_SEQ']);
                            $this->db->where('class_sec_dtl.CS_SEQ', $v["CS_SEQ"]);
                            $this->db->where('class_sec_dtl.CS_FEES_TYPE', '0');
                            $monthly_fee = $this->db->get('fees_concession')->result_array();

                            #if concession fees not set, then fetch regular fees
                            if ($monthly_fee[0]['monthly_fee'] == '0.00') {
                                $this->db->select('COALESCE(SUM(Fees), 0) AS monthly_fee');
                                $this->db->join('student_details', 'student_details.STD_CS_SEQ = class_sec_dtl.CS_SEQ', 'left');
                                $this->db->where('student_details.STD_SEQ', $s['STD_SEQ']);
                                $this->db->where('class_sec_dtl.CS_SEQ', $v["CS_SEQ"]);
                                $this->db->where('class_sec_dtl.CS_FEES_TYPE', 0);
                                $monthly_fee = $this->db->get('class_sec_dtl')->result_array();
                            }
                        }
                        #regular fees
                        else {
                            $this->db->select('COALESCE(SUM(Fees), 0) AS monthly_fee');
                            $this->db->join('student_details', 'student_details.STD_CS_SEQ = class_sec_dtl.CS_SEQ', 'left');
                            $this->db->where('student_details.STD_SEQ', $s['STD_SEQ']);
                            $this->db->where('class_sec_dtl.CS_SEQ', $v["CS_SEQ"]);
                            $this->db->where('class_sec_dtl.CS_FEES_TYPE', 0);
                            $monthly_fee = $this->db->get('class_sec_dtl')->result_array();
                        }
//                        echo '<pre>',print_r($due_months); die();

                        #get student's YEARLY fee
                        $this->db->select('FM_HDR_TOT_FEES as total_paid_year_fees, FM_HDR_CONC_FEES, due_amount');
                        $this->db->where('FM_HDR_STD_SEQ', $s['STD_SEQ']);
                        $this->db->where('FM_HDR_STD_CS_SEQ', $v["CS_SEQ"]);
                        $this->db->where('FM_HDR_FIN_YEAR', $company->COM_FIN_YEAR);
                        $result_paid_yearly_fee = $this->db->get('fees_yearly_hdr')->result_array();
                        $yearly_num_rows = $this->db->where('FM_HDR_STD_SEQ', $s['STD_SEQ'])
                            ->where('FM_HDR_STD_CS_SEQ', $v["CS_SEQ"])
                            ->where('FM_HDR_FIN_YEAR', $company->COM_FIN_YEAR)
                            ->get('fees_yearly_hdr')->num_rows();

                        $this->db->select('SUM(Fees) as yearly_fees');
                        $this->db->join('student_details', 'student_details.STD_CS_SEQ = class_sec_dtl.CS_SEQ', 'left');
                        $this->db->where('student_details.STD_SEQ', $s['STD_SEQ']);
                        $this->db->where('CS_SEQ', $s['STD_SEQ']);
                        $this->db->where('CS_FEES_TYPE', 1); //yearly fees
                        $result_all_yearly_fee = $this->db->get('class_sec_dtl')->result_array();

                        if($yearly_num_rows == 0){
                            $yearly_fees = ($result_all_yearly_fee[0]['yearly_fees']);
                        }
                        elseif($result_paid_yearly_fee[0]['due_amount'] == 0){
                            $yearly_fees = 0.00;
                        }
                        elseif ($result_paid_yearly_fee[0]['total_paid_year_fees']+$result_paid_yearly_fee[0]['FM_HDR_CONC_FEES'] >= $result_all_yearly_fee[0]['yearly_fees']) {
                            $yearly_fees = 0.00;
                        }
                        else {
                            $yearly_fees = ($result_all_yearly_fee[0]['yearly_fees'] - $result_paid_yearly_fee[0]['total_paid_year_fees']+$result_paid_yearly_fee[0]['FM_HDR_CONC_FEES']);
                        }

                        $html .= '<tr>
                                    <td width="25">'.$srl_no.'</td>
                                    <td width="60">'.$s["STD_SRLNO"].'</td>
                                    <td width="30">'.$s["STD_ROLLNO"].'</td>
                                    <td width="150">'.$s["STD_FNAME"].' '.$s["STD_MNAME"].' '.$s["STD_LNAME"].'</td>';

                        //april
                        $key = array_keys($due_months, 4);
                        if($key != null) {$m_amount = $monthly_fee[0]["monthly_fee"];} else {$m_amount = 0.00;}
                        $class_wise_fees[0] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="35" align="right">'.number_format($m_amount,2).'</td>';
                        //may
                        $key = array_keys($due_months, 5);
                        if($key != null) {$m_amount = $monthly_fee[0]["monthly_fee"];} else {$m_amount = 0.00;}
                        $class_wise_fees[1] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="35" align="right">'.number_format($m_amount,2).'</td>';
                        //june
                        $key = array_keys($due_months, 6);
                        if($key != null) {$m_amount = $monthly_fee[0]["monthly_fee"];} else {$m_amount = 0.00;}
                        $class_wise_fees[2] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="35" align="right">'.number_format($m_amount,2).'</td>';
                        //july
                        $key = array_keys($due_months, 7);
                        if($key != null) {$m_amount = $monthly_fee[0]["monthly_fee"];} else {$m_amount = 0.00;}
                        $class_wise_fees[3] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="35" align="right">'.number_format($m_amount,2).'</td>';
                        //august
                        $key = array_keys($due_months, 8);
                        if($key != null) {$m_amount = $monthly_fee[0]["monthly_fee"];} else {$m_amount = 0.00;}
                        $class_wise_fees[4] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="40" align="right">'.number_format($m_amount,2).'</td>';
                        //september
                        $key = array_keys($due_months, 9);
                        if($key != null) {$m_amount = $monthly_fee[0]["monthly_fee"];} else {$m_amount = 0.00;}
                        $class_wise_fees[5] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="55" align="right">'.number_format($m_amount,2).'</td>';
                        //october
                        $key = array_keys($due_months, 10);
                        if($key != null) {$m_amount = $monthly_fee[0]["monthly_fee"];} else {$m_amount = 0.00;}
                        $class_wise_fees[6] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="45" align="right">'.number_format($m_amount,2).'</td>';
                        //november
                        $key = array_keys($due_months, 11);
                        if($key != null) {$m_amount = $monthly_fee[0]["monthly_fee"];} else {$m_amount = 0.00;}
                        $class_wise_fees[7] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="55" align="right">'.number_format($m_amount,2).'</td>';
                        //december
                        $key = array_keys($due_months, 12);
                        if($key != null) {$m_amount = $monthly_fee[0]["monthly_fee"];} else {$m_amount = 0.00;}
                        $class_wise_fees[8] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="55" align="right">'.number_format($m_amount,2).'</td>';
                        //january
                        $key = array_keys($due_months, 1);
                        if($key != null) {$m_amount = $monthly_fee[0]["monthly_fee"];} else {$m_amount = 0.00;}
                        $class_wise_fees[9] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="45" align="right">'.number_format($m_amount,2).'</td>';
                        //february
                        $key = array_keys($due_months, 2);
                        if($key != null) {$m_amount = $monthly_fee[0]["monthly_fee"];} else {$m_amount = 0.00;}
                        $class_wise_fees[10] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="45" align="right">'.number_format($m_amount,2).'</td>';
                        //march
                        $key = array_keys($due_months, 3);
                        if($key != null) {$m_amount = $monthly_fee[0]["monthly_fee"];} else {$m_amount = 0.00;}
                        $class_wise_fees[11] += $m_amount; $student_wise_total += $m_amount;
                        $html .= '<td width="40" align="right">'.number_format($m_amount,2).'</td>';

                        $class_wise_fees[12] += $yearly_fees; $student_wise_total += $yearly_fees;
                        $class_wise_fees[13] += $student_wise_total;
                        $html .= '<td width="60" align="right">'.number_format($yearly_fees,2).'</td>
                                  <td width="80" align="right">'.number_format($student_wise_total,2).'</td>            
                                </tr>';
                        $srl_no++;
                    }

                    $html .= '
                        </tbody>
                        <hr>
                        <tr>
                            <td width="265"><strong>Class Wise Total</strong></td>
                            <td width="45" align="right">'.number_format($class_wise_fees[0],2).'</td>
                            <td width="35" align="right">'.number_format($class_wise_fees[1],2).'</td>
                            <td width="35" align="right">'.number_format($class_wise_fees[2],2).'</td>
                            <td width="35" align="right">'.number_format($class_wise_fees[3],2).'</td>
                            <td width="40" align="right">'.number_format($class_wise_fees[4],2).'</td>
                            <td width="55" align="right">'.number_format($class_wise_fees[5],2).'</td>
                            <td width="45" align="right">'.number_format($class_wise_fees[6],2).'</td>
                            <td width="55" align="right">'.number_format($class_wise_fees[7],2).'</td>
                            <td width="55" align="right">'.number_format($class_wise_fees[8],2).'</td>
                            <td width="45" align="right">'.number_format($class_wise_fees[9],2).'</td>
                            <td width="45" align="right">'.number_format($class_wise_fees[10],2).'</td>
                            <td width="40" align="right">'.number_format($class_wise_fees[11],2).'</td>
                            <td width="60" align="right">'.number_format($class_wise_fees[12],2).'</td>
                            <td width="80" align="right">'.number_format($class_wise_fees[13],2).'</td>
                        </tr>
                    </table>
                    <hr>
                    &nbsp;
                    <br>
                    <br>';

                    // Print text using writeHTMLCell()
                    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
                }
                //adding class wise fees to grand fees
                for($i=0; $i<=13; $i++) {
                    $grand_fees[$i] += $class_wise_fees[$i];
                }
            }

            if($html == '') { //if no content
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'There is no student for that class.');
                return array('type' => 'redirect', 'page'=>'admin/std_fees_ledger_report');
            }

            $html1 = '
            <hr>
                <table cellspacing="2"  border="">
                <tbody>
                <tr style="font-size: 7px">
                    <td width="270"><strong style="font-size: 15px">Grand Total</strong></td>
                    <td width="35" align="right">'.number_format($grand_fees[0],2).'</td>
                    <td width="35" align="right">'.number_format($grand_fees[1],2).'</td>
                    <td width="35" align="right">'.number_format($grand_fees[2],2).'</td>
                    <td width="35" align="right">'.number_format($grand_fees[3],2).'</td>
                    <td width="40" align="right">'.number_format($grand_fees[4],2).'</td>
                    <td width="55" align="right">'.number_format($grand_fees[5],2).'</td>
                    <td width="45" align="right">'.number_format($grand_fees[6],2).'</td>
                    <td width="55" align="right">'.number_format($grand_fees[7],2).'</td>
                    <td width="55" align="right">'.number_format($grand_fees[8],2).'</td>
                    <td width="45" align="right">'.number_format($grand_fees[9],2).'</td>
                    <td width="45" align="right">'.number_format($grand_fees[10],2).'</td>
                    <td width="40" align="right">'.number_format($grand_fees[11],2).'</td>
                    <td width="60" align="right">'.number_format($grand_fees[12],2).'</td>
                    <td width="80" align="right">'.number_format($grand_fees[13],2).'</td>
                </tr>
                </tbody>
                </table>
            <hr>';

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html1, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        }
        else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/std_fees_ledger_report');
        }
    }

    public function single_month_dues_report() {
        $cls = $this->db->get('class_sec_hdr')->result_array();
        $class_type = $this->db->get('class_type')->result_array();

        $data['class'] = $cls;
        $data['class_type'] = $class_type;
        $data['form_type'] = 'single_month_dues_report';

        $data['tab_title'] = 'Single Month Dues Report';
        $data['section_heading'] = 'Single Month Dues Report <small>(Print)</small>';
        $data['menu_name'] = 'Single Month Dues Report';
        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function print_single_month_dues_report() {
        if($this->input->post('submit') == 'print_single_month_dues_report') {
            $school = $this->input->post('school[]');
            $class = $this->input->post('class');
            $class_type = $this->input->post('class_type');
            $month = $this->input->post('month');
            $dt = explode("/",$month);
            $mnth = $dt[0];
            $yer = $dt[1];
            $letter = $this->input->post('letter');
            $multi_month = $this->input->post('multi_month');
            
            // NEW: Capture payment deadline date
            $payment_date = $this->input->post('payment_date');
            $formatted_payment_date = date('d/m/Y', strtotime($payment_date));

            $company = $this->db->get_where('company',array('SCHOOL_TYPE' => $class_type))->row();

            $month = str_pad($mnth, 2, '0', STR_PAD_LEFT);
            $start_date = date("Y-m-d", mktime(0, 0, 0, $month, 1, $yer));
            $end_date = date("Y-m-t", mktime(0, 0, 0, $month, 1, $yer));
            $this->db->where('effective_from <=', $end_date);
            $signature = $this->db->get('signatures')->row();
            // print_r($this->db->last_query());
            // die;

            $this->db->where_in('CS_SEQ', $class);
            $classes =  $this->db->get('class_sec_hdr')->result();

            if($class_type == 4){
                $months_arr = array("MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12","JAN"=>"1","FEB"=>"2","MAR"=>"3","APR"=>"4");
            }else{
                $months_arr = array("JAN"=>"1","FEB"=>"2","MAR"=>"3","APR"=>"4","MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12");
            }
            //take only till selected month (month from date input)
            $months_arr = array_slice($months_arr, 0, array_search($mnth, array_values($months_arr))+1, true);
            $selected_month = array_search($mnth , $months_arr);


#------------------------------------------TCPDF--------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'All Dues of '.$selected_month;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords($doc_name.', smg, developed by: https://sketchmeglobal.com');

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

            foreach($classes as $cls) {
                $class = $cls->CS_SEQ;

                $this->db->select('FEES_DTL_STD_SEQ');
                $this->db->where('FEES_DTL_STD_CS_SEC', $class);
                if ($multi_month == 'yes') {
                    $this->db->where('FEES_DTL_MONTH <=', $mnth);
                    $this->db->having('COUNT(DISTINCT FEES_DTL_MONTH) =', $mnth);
                }else{
                    $this->db->where('FEES_DTL_MONTH', $mnth);
                }
                $this->db->where('FEES_DTL_FIN_YEAR', $yer);
                $this->db->group_by('FEES_DTL_STD_SEQ');
                $paid_std_result = $this->db->get('fees_monthly_dtl')->result_array();
//            echo $this->db->last_query(); die();
                $paid_std_id = array_column($paid_std_result, 'FEES_DTL_STD_SEQ');
//            echo "<pre>"; print_r($paid_std_id); die();

                $this->db->select('student_details.STD_SEQ,student_details.ST_FULL_NAME,STD_FNAME,STD_MNAME,STD_LNAME,STD_REGNO,STD_SRLNO,STD_ROLLNO,STD_CONSC,STD_FTH_NAME,STD_MTH_NAME,STD_CS_SEQ, student_details.STD_ADDR_0');
                $this->db->join('student_parent_details', 'student_parent_details.STD_SEQ = student_details.STD_SEQ', 'left');
                $this->db->where('STD_CS_SEQ', $class);
                $this->db->where('STD_LEFT', 0);
                if($paid_std_id != null){
                    $this->db->where_not_in('student_details.STD_SEQ', $paid_std_id);
                }
                $this->db->order_by('STD_ROLLNO');
                $due_std = $this->db->get('student_details')->result_array();

                $total_std = count($due_std);

                if($total_std == 0) { //if no due student found
                    $this->session->set_flashdata('type', 'warning');
                    $this->session->set_flashdata('title', 'Hurrah!');
                    $this->session->set_flashdata('msg', 'No dues for till the month.');
                    return array('type' => 'redirect', 'page'=>'admin/single_month_dues_report');
                }

                $this->db->select('ACC_MASTER_NAME,Fees');
                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                $this->db->where('CS_SEQ', $class);
                $this->db->where('CS_FEES_TYPE', '0'); //monthly fees
                $this->db->where('Fees !=', '0');
                $monthly_fees_result = $this->db->get('class_sec_dtl')->result_array();
                $monthly_fees = array_sum(array_column($monthly_fees_result, 'Fees'));
                $tot_fees = array_sum(array_column($monthly_fees_result, 'Fees'));
                $total_month_amount = $tot_fees;

                #individual letter
                if ($letter == 'one') {
                    $html = '';
                    foreach ($due_std as $val) { //this loop is for all students whose monthly fees are due

                        $grand_total = 0;

                        $date = date('d/m/Y');
                        $year = date('Y');
                        $amount = $monthly_fees;
                        $fees_rs = $monthly_fees_result;

                        /*Yearly Fees */
                        $this->db->select('ACC_MASTER_NAME,Fees');
                        $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                        $this->db->where('CS_SEQ', $class);
                        $this->db->where('class_sec_dtl.CS_FEES_TYPE', '1'); //yearly fees
                        $this->db->where('Fees !=', '0');
                        $yearly_fees_result = $this->db->get('class_sec_dtl')->result_array();

                        $yearly_total_fees = array_sum(array_column($yearly_fees_result, 'Fees'));

                        $this->db->select('SUM(FEES_DTL_AMOUNT) as total_paid_year_fees');
                        $this->db->where('FEES_DTL_STD_SEQ', $val['STD_SEQ']);
                        $this->db->where('FEES_DTL_STD_CS_SEC', $class);
                        $this->db->where('FEES_DTL_FIN_YEAR', $company->COM_FIN_YEAR);
                        $result_paid_yearly_fee = $this->db->get('fees_yearly_dtl')->result_array();

                        $yearly_due_fees = ($yearly_total_fees - $result_paid_yearly_fee[0]['total_paid_year_fees']);

                        if ($val['STD_CONSC'] == '1') {
                            $this->db->select('fees_concession.Fees');
                            $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE  and class_sec_dtl.CS_SEQ = fees_concession.class_id', 'left');
                            $this->db->where('fees_concession.std_id', $val['STD_SEQ']);
                            $this->db->where('class_sec_dtl.CS_SEQ', $class);
                            $this->db->where('class_sec_dtl.CS_FEES_TYPE', '0');  //monthly fees
                            $this->db->where('fees_concession.ACC_MASTER_CODE', '4');  //only tution fees
                            $this->db->where('class_sec_dtl.Fees !=', '0');
                            $monthly_fees_con = $this->db->get('fees_concession')->result_array();

                            // echo $this->db->last_query(); die();
                            if (count($monthly_fees_con) > 0) {
                                $total_month_amount = array_sum(array_column($monthly_fees_con, 'Fees'));
                            } else {
                                $this->db->select('ACC_MASTER_NAME,Fees');
                                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                                $this->db->where('CS_SEQ', $class);
                                $this->db->where('CS_FEES_TYPE', '0'); //monthly fees
                                $this->db->where('acc_master.ACC_MASTER_CODE', '4');  // Tution Fees Only
                                $this->db->where('Fees !=', '0');
                                $monthly_fees_result = $this->db->get('class_sec_dtl')->result_array();
                                // echo $this->db->last_query(); die();
                                $total_month_amount = array_sum(array_column($monthly_fees_result, 'Fees'));
                            }

                        }else{

                            $this->db->select('ACC_MASTER_NAME,Fees');
                            $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                            $this->db->where('CS_SEQ', $class);
                            $this->db->where('CS_FEES_TYPE', '0'); //monthly fees
                            $this->db->where('Fees !=', '0');
                            $monthly_fees_result = $this->db->get('class_sec_dtl')->result_array();

                            // echo $this->db->last_query(); die();
                            //print_r($monthly_fees);die();

                            $monthly_fees = array_sum(array_column($monthly_fees_result, 'Fees'));
                            $tot_fees = array_sum(array_column($monthly_fees_result, 'Fees'));
                            $total_month_amount = $tot_fees;

                        }

                        //findout all due months
                        if ($multi_month == 'yes') {
                            $this->db->select('FEES_DTL_MONTH');
                            $this->db->where('FEES_DTL_STD_SEQ', $val['STD_SEQ']);
                            $this->db->where('FEES_DTL_MONTH <=', $mnth);
                            $this->db->group_by('FEES_DTL_MONTH');
                            $paid_rs = $this->db->get('fees_monthly_dtl')->result_array();
                            $paid_months = array_column($paid_rs, 'FEES_DTL_MONTH');
                            $due_months = array_diff($months_arr, $paid_months);
                            $total_due_month = count($due_months);
                            $all_due_month_names = implode(', ', array_keys($due_months));
                        } else {
                            $total_due_month = 1;
                            $all_due_month_names = array_search($mnth, $months_arr);
                        }

                        // Set some content to print
                        $fname = $val['STD_FTH_NAME'];
                        $mname = $val['STD_MTH_NAME'];
                        $addr = $val['STD_ADDR_0'];
                        $fullname = $val['ST_FULL_NAME'];
                        $reg_no = $val['STD_REGNO'];
                        $roll_no = $val['STD_ROLLNO'];

                        $html.="<section class='sheet padding-10mm' style='padding-top: 59px;'>";

                        $html .= "<div class='body-content'><header>
                                <h3 class='text-center'>$company->COM_NAME</h3>
                                <h4 class='text-center'>$company->COM_ADD2</h4>
                                <hr>    
                                <h5 class='text-right'>Date: $date</h5>
                            </header>
            
                            <mainbody>
                                <p>
                                    To
                                    <br>
                                    Mr. $fname
                                    <br>
                                    Mrs. $mname
                                    <br>
                                    $addr
                                    <br><br><br/>
                                    <div style='text-align:center'>
                                        <b>Sub:</b> <u>Outstanding fees of $fullname of <b>Class</b> ".$cls->Class_Name." - ".$cls->Sec_Name.", <b>Reg. No.</b> $reg_no & <b>Roll No.</b>$roll_no</u>
                                    </div>
                                </p>
                                <br><br/>
                                <p style='text-align:center'><strong><u>DEFAULTER FOR THE MONTH OF $all_due_month_names - $year</u></strong></p>
                                <p>This letter is to inform you that the following fees are found to be outstanding against the account of <strong>$fullname</strong></p>
                                <p class='text-center'>
                                    <strong><u>THE FEES DETAILS ARE</u></strong>
                                </p>";
                        $grand_total = ($total_month_amount * $total_due_month) + $yearly_due_fees;
                        $html .= "<table>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td>Tuition Fees</td>
                                            <td>=</td>
                                            <td>$total_month_amount x $total_due_month </td>
                                            <td></td>
                                            <td></td>
                                        </tr>";
                        if ($yearly_due_fees > 0) {
                            $html .= "<tr>
                                            <td></td>
                                            <td>Yearly Fees</td>
                                            <td>=</td>
                                            <td> $yearly_due_fees</td>
                                            <td></td>
                                            <td></td>
                                        </tr>";
                        }
                        $html .= "<tr>
                                            <td colspan='6'> <hr> </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Total</td>
                                            <td>=</td>
                                            <td>$grand_total</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br/>
                                <p>
                                    We would request you to kindly make the payment by <strong>$formatted_payment_date</strong>, along with the next month.
                                    If you have already made the payment before receiving of this letter, kindly ignore it. In case of any discrepancy, please contact the school office.
                                </p>
                                <br/><br/>
                                <p>
                                Thanking You<br>
                                Your Sincerely,<br>
                                For, <strong>$company->COM_NAME</strong>
                                </p>         
                                <img src='".base_url('/assets/img/'.$signature->signature)."' height='50' />
                                <p>Head of The Institution</p>  
                                <br/>
                                <hr class='style-eight' />
                                <p>
                                <br/>
                                Parents ($fname and $mname) are requested to please sign it and return it to the student ($fullname) for submission to the Class teacher.
                                <br/><br/>
                                Parents Signature With Date ____________________________   ____________________________ 
                                </p>
                            </mainbody>
                        </div>";
                        $html .= "</section>";
                    }

                    $data['tab_title'] = 'All Dues Report';
                    $data['section_heading'] = 'All Dues Report <small>(Print)</small>';
                    $data['menu_name'] = 'All Dues Report';
                    $data['sections'] = $html;

                    return array('type' => 'load_view', 'page' => 'multi_dues_report', 'data' => $data);
                }
                #individual letter
                else if ($letter == 'two') {
                    $html = "<section class='sheet padding-10mm'>";
                    $page_break = 0;
                    foreach ($due_std as $val) { //this loop is for all students whose monthly fees are due

                        $grand_total = 0;

                        $date = date('d/m/Y');
                        $year = date('Y');
                        $amount = $monthly_fees;
                        $fees_rs = $monthly_fees_result;

                        /*Yearly Fees */
                        $this->db->select('ACC_MASTER_NAME,Fees');
                        $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                        $this->db->where('CS_SEQ', $class);
                        $this->db->where('class_sec_dtl.CS_FEES_TYPE', '1'); //yearly fees
                        $this->db->where('Fees !=', '0');
                        $yearly_fees_result = $this->db->get('class_sec_dtl')->result_array();

                        $yearly_total_fees = array_sum(array_column($yearly_fees_result, 'Fees'));

                        $this->db->select('SUM(FEES_DTL_AMOUNT) as total_paid_year_fees');
                        $this->db->where('FEES_DTL_STD_SEQ', $val['STD_SEQ']);
                        $this->db->where('FEES_DTL_STD_CS_SEC', $class);
                        $this->db->where('FEES_DTL_FIN_YEAR', $company->COM_FIN_YEAR);
                        $result_paid_yearly_fee = $this->db->get('fees_yearly_dtl')->result_array();

                        $yearly_due_fees = ($yearly_total_fees - $result_paid_yearly_fee[0]['total_paid_year_fees']);

                        if ($val['STD_CONSC'] == '1') { 
                            $this->db->select('fees_concession.Fees');
                            $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE  and class_sec_dtl.CS_SEQ = fees_concession.class_id', 'left');
                            $this->db->where('fees_concession.std_id', $val['STD_SEQ']);
                            $this->db->where('class_sec_dtl.CS_SEQ', $class);
                            $this->db->where('class_sec_dtl.CS_FEES_TYPE', '0');  //monthly fees
                            $this->db->where('fees_concession.ACC_MASTER_CODE', '4');  //only tution fees
                            $this->db->where('class_sec_dtl.Fees !=', '0');
                            $monthly_fees_con = $this->db->get('fees_concession')->result_array();

                            // echo $this->db->last_query(); die();
                            if (count($monthly_fees_con) > 0) {
                                $total_month_amount = array_sum(array_column($monthly_fees_con, 'Fees'));
                            } else {
                                $this->db->select('ACC_MASTER_NAME,Fees');
                                $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                                $this->db->where('CS_SEQ', $class);
                                $this->db->where('CS_FEES_TYPE', '0'); //monthly fees
                                $this->db->where('acc_master.ACC_MASTER_CODE', '4');  // Tution Fees Only
                                $this->db->where('Fees !=', '0');
                                $monthly_fees_result = $this->db->get('class_sec_dtl')->result_array();
                                // echo $this->db->last_query(); die();
                                $total_month_amount = array_sum(array_column($monthly_fees_result, 'Fees'));
                            }

                        }else{

                            $this->db->select('ACC_MASTER_NAME,Fees');
                            $this->db->join('acc_master', 'acc_master.ACC_MASTER_CODE = class_sec_dtl.ACC_MASTER_CODE', 'left');
                            $this->db->where('CS_SEQ', $class);
                            $this->db->where('CS_FEES_TYPE', '0'); //monthly fees
                            $this->db->where('Fees !=', '0');
                            $monthly_fees_result = $this->db->get('class_sec_dtl')->result_array();

                            // echo $this->db->last_query(); die();
                            //print_r($monthly_fees);die();

                            $monthly_fees = array_sum(array_column($monthly_fees_result, 'Fees'));
                            $tot_fees = array_sum(array_column($monthly_fees_result, 'Fees'));
                            $total_month_amount = $tot_fees;

                        }

                        //findout all due months
                        if ($multi_month == 'yes') {
                            $this->db->select('FEES_DTL_MONTH');
                            $this->db->where('FEES_DTL_STD_SEQ', $val['STD_SEQ']);
                            $this->db->where('FEES_DTL_MONTH <=', $mnth);
                            $this->db->group_by('FEES_DTL_MONTH');
                            $paid_rs = $this->db->get('fees_monthly_dtl')->result_array();
                            $paid_months = array_column($paid_rs, 'FEES_DTL_MONTH');
                            $due_months = array_diff($months_arr, $paid_months);
                            $total_due_month = count($due_months);
                            $all_due_month_names = implode(', ', array_keys($due_months));
                        } else {
                            $total_due_month = 1;
                            $all_due_month_names = array_search($mnth, $months_arr);
                        }

                        // Set some content to print
                        $fname = $val['STD_FTH_NAME'];
                        $mname = $val['STD_MTH_NAME'];
                        $addr = $val['STD_ADDR_0'];
                        $fullname = $val['ST_FULL_NAME'];
                        $reg_no = $val['STD_REGNO'];
                        $roll_no = $val['STD_ROLLNO'];

                        if($page_break%2 == 0 and $page_break!=0){
                            $html.="</section><section class='sheet padding-10mm'>";
                        }

                        if($page_break%2 == 1){
                            $html .= '<hr class="style_2">';
                        }

                        $html .= "<div class='body-content'><header>
                                <h3 class='text-center'>$company->COM_NAME</h3>
                                <h4 class='text-center'>$company->COM_ADD2</h4>
                                <hr>    
                                <h5 class='text-right'>Date: $date</h5>
                            </header>
            
                            <mainbody>
                                <p>
                                    To
                                    <br>
                                    Mr. $fname
                                    <br>
                                    Mrs. $mname
                                    <br>
                                    $addr
                                    <br><br><br/>
                                    <div style='text-align:center'>
                                        <b>Sub:</b> <u>Outstanding fees of $fullname of <b>Class</b> ".$cls->Class_Name." - ".$cls->Sec_Name.", <b>Reg. No.</b> $reg_no & <b>Roll No.</b>$roll_no</u>
                                    </div>
                                </p>
                                <br><br/>
                                <p style='text-align:center'><strong><u>DEFAULTER FOR THE MONTH OF $all_due_month_names - $year</u></strong></p>
                                <p>This letter is to inform you that the following fees are found to be outstanding against the account of <strong>$fullname</strong></p>
                                <p class='text-center'>
                                    <strong><u>THE FEES DETAILS ARE</u></strong>
                                </p>";
                        $grand_total = ($total_month_amount * $total_due_month) + $yearly_due_fees;
                        $html .= "<table>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td>Tuition Fees</td>
                                            <td>=</td>
                                            <td>$total_month_amount x $total_due_month </td>
                                            <td></td>
                                            <td></td>
                                        </tr>";
                        if ($yearly_due_fees > 0) {
                            $html .= "<tr>
                                            <td></td>
                                            <td>Yearly Fees</td>
                                            <td>=</td>
                                            <td> $yearly_due_fees</td>
                                            <td></td>
                                            <td></td>
                                        </tr>";
                        }
                        $html .= "<tr>
                                            <td colspan='6'> <hr> </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>Total</td>
                                            <td>=</td>
                                            <td>$grand_total</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br/>    
                                <p>
                                    We would request you to kindly make the by____________________________ , the earliest, along with the next month. 
                                    If you have already made the payment before receiving of this letter, kindly ignore it. In case of any discrepancy, please contact the school office.
                                </p>
                                <br/><br/>
                                <p>            
                                Thanking You<br>
                                Your Sincerely,<br>
                                For, <strong>$company->COM_NAME</strong>
                                </p>         
                                <img src='".base_url('/assets/img/'.$signature->signature)."' height='50' />
                                <p>Head of The Institution</p>  
                                <br/>
                                <hr class='style-eight' />
                                <p>
                                <br/>
                                Parents ($fname and $mname) are requested to please sign it and return it to the student ($fullname) for submission to the Class teacher.
                                <br/><br/>
                                Parents Signature With Date ____________________________   ____________________________ 
                                </p>
                            </mainbody>
                        </div>";
                        $page_break++;
                    }
                    $html .= "</section>";

                    $data['tab_title'] = 'All Dues Report';
                    $data['section_heading'] = 'All Dues Report <small>(Print)</small>';
                    $data['menu_name'] = 'All Dues Report';
                    $data['sections'] = $html;

                    return array('type' => 'load_view', 'page' => 'multi_dues_report', 'data' => $data);
                }
                #all dues report
                else {
                    // set header and footer fonts and size
                    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
                    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                    // set default header data
                    $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2 , $company->COM_CITY
        <br>
        <strong>All Dues of: <span style="background-color: black;color: white;"> $selected_month </span> Class & Sec: <span style="background-color: black;color: white;"> $cls->Class_Name - $cls->Sec_Name </span> Total Students: <span style="background-color: black;color: white;"> $total_std </span></strong>
        <hr align="left">
        </div>
EOD;
                    $pdf->setHtmlHeader($html_header, false);

                    // set default font subsetting mode
                    $pdf->setFontSubsetting(true);

                    // Set font
                    $pdf->SetFont('times', '', 15, '', true);

                    // Add a page
                    $pdf->AddPage('P', 'A4');

                    $grand_fees = 0.00;

                    // Set some content to print
                    $html = <<<EOD
            <table cellspacing="2">
                <thead>
                <tr>
                    <th width="180"><strong>Admission No</strong></th>
                    <th width="280"><strong>Student Name</strong></th>
                    <th width="70"><strong>Roll</strong></th>
                    <th width="125"align="right"><strong>Due Amount</strong></th>
                </tr>
                </thead>
                <tbody>
EOD;
                    foreach ($due_std as $val) { //this loop is for all students whose monthly fees are due
                        $amount = $monthly_fees;

                        if ($val['STD_CONSC'] == '1') {
                            $this->db->select('COALESCE(SUM(fees_concession.Fees),0) as total_amount');
                            $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                            $this->db->where('fees_concession.std_id', $val['STD_SEQ']);
                            $this->db->where('fees_concession.class_id', $class);
                            $this->db->where('class_sec_dtl.CS_SEQ', $val['STD_CS_SEQ']);
                            $this->db->where('class_sec_dtl.CS_FEES_TYPE', '0'); //monthly fees
                            $this->db->where('fees_concession.Fees !=', '0');
                            $monthly_fees_con = $this->db->get('fees_concession')->result_array();
//                        echo $this->db->last_query(); die();

                            if ($monthly_fees_con[0]['total_amount'] != '0.00') {
                                $amount = $monthly_fees_con[0]['total_amount'];
                            }
//                        print_r($amount);die();
                        }
                        $grand_fees += $amount;
                        $amount = number_format($amount, 2);
                        $html .= '<tr>
                        <td width="180">' . $val['STD_SRLNO'] . '</td>
                        <td width="280">' . $val['STD_FNAME'] . ' ' . $val['STD_MNAME'] . ' ' . $val['STD_LNAME'] . '</td>
                        <td width="70">' . $val['STD_ROLLNO'] . '</td>
                        <td width="125" align="right">' . $amount . '</td>
                    </tr>';
                    }

                    $grand_fees = number_format($grand_fees, 2);
                    $html .= <<<EOD
                </tbody>
            </table>
            <br>
            <br>
            
<table cellspacing="2" style="font-size: 19px" border="1">
<thead>
<tr>
    <th width="500"><strong>Grand Total</strong></th>
    <th width="150" align="right"><strong>$grand_fees</strong></th>
</tr>
</thead>
</table>
EOD;

                    // Print text using writeHTMLCell()
                    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
                }
                // Close and output PDF document
                $pdf->Output($doc_name . '.pdf', 'I');
            }
        }
        else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/single_month_dues_report');
        }
    }

    public function all_dues_report() {
        $cls = $this->db->get('class_sec_hdr')->result_array();
        $class_type = $this->db->get('class_type')->result_array();

        $data['class'] = $cls;
        $data['class_type'] = $class_type;
        $data['form_type'] = 'all_dues_report';

        $data['tab_title'] = 'All Dues Report';
        $data['section_heading'] = 'All Dues Report <small>(Print)</small>';
        $data['menu_name'] = 'All Dues Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function outstanding_total_report() {
        $cls = $this->db->get('class_sec_hdr')->result_array();
        $class_type = $this->db->get('class_type')->result_array();

        $data['class'] = $cls;
        $data['class_type'] = $class_type;
        $data['form_type'] = 'outstanding_total_report';

        $data['tab_title'] = 'Outstanding Total Report';
        $data['section_heading'] = 'Outstanding Total Report <small>(Print)</small>';
        $data['menu_name'] = 'Outstanding Total Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function print_all_dues_report() {
        if($this->input->post('submit') == 'print_all_dues_report') { //if form submitted
            $class_type = $this->input->post('class_type');
            $classes = $this->input->post('class');
            $eff_date = date("Y-m-d", strtotime(str_replace('/','-', $this->input->post('eff_date'))));
            $eff_month = date('m', strtotime($eff_date));
            $without_amount = $this->input->post('without_amount');

            $company = $this->db->get_where('company',array('SCHOOL_TYPE' => $class_type))->row();

            if($class_type == 4){
                $months_arr = array("MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12","JAN"=>"1","FEB"=>"2","MAR"=>"3","APR"=>"4");
                if(date('Y-m', strtotime((CURRENT_YEAR+1).'-5')) > date('Y-m')) {
                    //take only till selected month (month from date input)
                    $months_arr = array_slice($months_arr, 0, array_search($eff_month, array_values($months_arr))+1, true);
                }
            }else{
                $months_arr = array("JAN"=>"1","FEB"=>"2","MAR"=>"3","APR"=>"4","MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12");
                if (CURRENT_YEAR == date('Y')) {
                    //take only till selected month (month from date input)
                    $months_arr = array_slice($months_arr, 0, array_search($eff_month, array_values($months_arr))+1, true);
                }
            }
//            print_r($months_arr); die();


//----------------------------------------TCPDF-----------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'All Dues Report';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords('All Dues Report, smg, developed by: https://sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
<div style="text-align:center;">
<span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
<br>
$company->COM_ADD2 , $company->COM_CITY
<br>
<strong style="font-size: 13px"><span style="background-color: black;color: white;">All Dues Report</span></strong>
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

            // set default font subsetting mode
            $pdf->setFontSubsetting(true);

            // Set font
            $pdf->SetFont('helvetica', '', 9, '', true);
// -----------------------------------------------------------------------------------------------------

            $all_m_gd_total = 0.00;
            $all_y_gd_total = 0.00;
            $all_main_gd_total = 0.00;
            $all_grand_total_details = array();

            //loop for each class
            foreach ($classes as $class) {
                // Add a page
                $pdf->AddPage('P', 'A4');

                $this->db->where('CS_SEQ', $class);
                $cls = $this->db->get('class_sec_hdr')->row();

                $this->db->select('STD_SEQ,ST_FULL_NAME, STD_FNAME,STD_MNAME,STD_LNAME,STD_REGNO,STD_DOB,STD_SRLNO,STD_ROLLNO,STD_CONSC, class_sec_hdr.class_sec as class_sec, STD_CS_SEQ');
                $this->db->where('STD_CS_SEQ', $class);
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ=student_details.STD_CS_SEQ', 'left');
                $this->db->where('STD_LEFT', 0);
                $this->db->where('STD_STATUS', 0);
                $this->db->order_by('STD_CS_SEQ,STD_ROLLNO');
//                $this->db->where('STD_REGNO', "P/057/21");
                $std = $this->db->get('student_details')->result_array();
                // echo"<pre>";
                // print_r($std);
                // echo"</pre>";
                //if(count($yearly_fees_rs[0]['total_fees']) > 0) {$yearly_fees = $yearly_fees_rs[0]['total_fees'];} else {$yearly_fees = 'Not Set';}

                $arr = array();
                foreach ($std as $key => $std_value) {
                    // print_r($std_value);die();
                    $arr[$key]['st_id'] = $std_value['STD_SEQ'];
                    $arr[$key]['st_name'] = $std_value['ST_FULL_NAME'];
                    $arr[$key]['st_reg'] = $std_value['STD_REGNO'];
//                    $arr[$key]['st_reg'] = $std_value['STD_REGNO'].' '.date('dmY', strtotime($std_value['STD_DOB']));
                    $arr[$key]['st_cs'] = $std_value['class_sec'];
                    $arr[$key]['st_roll'] = $std_value['STD_ROLLNO'];

                    $this->db->select('FEES_DTL_MONTH, sum(fees_monthly_dtl.FEES_DTL_AMOUNT) as total_mon_fees');
                    $this->db->where('FEES_DTL_STD_SEQ', $std_value['STD_SEQ']);
                    $this->db->where('FEES_DTL_STD_CS_SEC', $class);
                    $this->db->where('DATE_FORMAT(`FEES_DTL_COL_DATE`, "%Y-%m") <= DATE_FORMAT("' . $eff_date . '", "%Y-%m")');
                    $this->db->group_by('FEES_DTL_MONTH');
                    $paid_rs = $this->db->get('fees_monthly_dtl')->result_array();
                    //print_r($paid_r);
//                echo $this->db->last_query(); die();
                    $paid_months = array_column($paid_rs, 'FEES_DTL_MONTH');
//                print_r($paid_months); die();
                    $due_months = array_diff($months_arr, $paid_months);
//                print_r($due_months); die();
                    $fullDiff = array_merge(array_diff($months_arr, $paid_months));
//                 echo "<pre>"; print_r(count($fullDiff)); die();

                    $due_month = implode(', ', array_map(
                        function ($v, $k) {
                            return $k;
                        },
                        $fullDiff,
                        array_keys($fullDiff)
                    ));
//                 echo "<pre>"; print_r($paid_months); die();
                    $arr[$key]['due_months'] = $due_month;
//                 echo "<pre>"; print_r($due_month); die();

                    /*month total*/
                    $month_fees_due = 0;
                    /* 1 for granted cons and  0 for not granted cons  */
                    if ($std_value['STD_CONSC'] == 1) {
                        $this->db->select('COALESCE(SUM(fees_concession.Fees),0) AS total_single_month_due_fees');
                        $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE and class_sec_dtl.CS_SEQ = fees_concession.class_id', 'left');
                        $this->db->where('fees_concession.std_id', $std_value['STD_SEQ']);
//                    $this->db->where('fees_concession.class_id', $class);
                        $this->db->where('class_sec_dtl.CS_SEQ', $std_value['STD_CS_SEQ']);
                        $this->db->where('class_sec_dtl.CS_FEES_TYPE', '0');
//                    $this->db->group_by('fees_concession.fc_id');
                        $month_fees_due = $this->db->get('fees_concession')->result_array();

//                     echo $this->db->last_query(); die();
//                     echo $month_fees_due[0]['total_single_month_due_fees']; die();

                        //if concession fees not set, then fetch actual fees
                        if ($month_fees_due[0]['total_single_month_due_fees'] == '0.00') {
                            $this->db->select('COALESCE(SUM(Fees),0) AS total_single_month_due_fees');
                            $this->db->join('student_details', 'student_details.STD_CS_SEQ = class_sec_dtl.CS_SEQ ', 'left');
                            $this->db->where('CS_FEES_TYPE', 0);
                            $this->db->where('CS_SEQ', $class);
                            $this->db->where('student_details.STD_SEQ', $std_value['STD_SEQ']);
                            $month_fees_due = $this->db->get('class_sec_dtl')->result_array();
                            // echo $this->db->last_query();
                            // echo $month_fees_due[0]['total_single_month_due_fees']; die();
                        }
                        // echo "<pre>"; print_r(@$month_fees_due[0]['total_single_month_due_fees']);
                        // echo $this->db->last_query();
                         //echo $this->db->last_query(); echo "<br>";

                    } else {
                        $this->db->select('COALESCE(SUM(Fees),0) AS total_single_month_due_fees');
                        $this->db->join('student_details', 'student_details.STD_CS_SEQ = class_sec_dtl.CS_SEQ', 'left');
                        $this->db->where('CS_FEES_TYPE', 0); //monthly fee
                        $this->db->where('CS_SEQ', $class);
                        $this->db->where('student_details.STD_SEQ', $std_value['STD_SEQ']);
                        $month_fees_due = $this->db->get('class_sec_dtl')->result_array();
                    //echo $this->db->last_query(); echo "<br>"; 
                    }
                    /*if(empty($paid_m_rs)){
                        $paid_m_total_rs = 'Due';
                    }else{
                        $paid_m_total_rs = $paid_m_rs->FM_HDR_TOT_FEES;
                    }*/
                    // echo   $arr[$key]['st_name'] .":".$month_fees_due[0]['total_single_month_due_fees'];
                    // echo "<br>";
                    $arr[$key]['total_due_mon_fees'] = @$month_fees_due[0]['total_single_month_due_fees'] * @count($fullDiff);
                    //echo "<pre>".var_dump($month_fees_due)."</pre>"; die();
                    /*----------*/

                    /*yearly fees total*/
                    $result_all_yearly_fee = 0;

                    $this->db->select('FM_HDR_TOT_FEES as total_paid_year_fees, FM_HDR_CONC_FEES, due_amount');
                    $this->db->where('FM_HDR_STD_SEQ', $std_value['STD_SEQ']);
                    $this->db->where('FM_HDR_STD_CS_SEQ', $class);
                    $this->db->where('FM_HDR_FIN_YEAR', $company->COM_FIN_YEAR);
                    $result_paid_yearly_fee = $this->db->get('fees_yearly_hdr')->result_array();
                    $yearly_num_rows = $this->db->where('FM_HDR_STD_SEQ', $std_value['STD_SEQ'])->get('fees_yearly_hdr')->num_rows();
//                    echo $this->db->last_query(); echo "<br>"; die();

                    $this->db->select('SUM(Fees) as total_due_year_fees');
                    $this->db->join('student_details', 'student_details.STD_CS_SEQ = class_sec_dtl.CS_SEQ', 'left');
                    $this->db->where('CS_FEES_TYPE', 1); //yearly fees
                    $this->db->where('CS_SEQ', $class);
                    $this->db->where('student_details.STD_SEQ', $std_value['STD_SEQ']);
                    $result_all_yearly_fee = $this->db->get('class_sec_dtl')->result_array();
//                     echo $this->db->last_query(); echo "<br>"; die();
//                }

                    if($yearly_num_rows == 0){

                        $arr[$key]['total_year_fees_due'] = ($result_all_yearly_fee[0]['total_due_year_fees']);

                    }else if($result_paid_yearly_fee[0]['due_amount'] == 0){

                        $arr[$key]['total_year_fees_due'] = "Paid";

                    }else if ($result_paid_yearly_fee[0]['total_paid_year_fees']+$result_paid_yearly_fee[0]['FM_HDR_CONC_FEES'] >= $result_all_yearly_fee[0]['total_due_year_fees']) {

                        $arr[$key]['total_year_fees_due'] = "Paid";

                    } else {

                        $arr[$key]['total_year_fees_due'] = ($result_all_yearly_fee[0]['total_due_year_fees'] - $result_paid_yearly_fee[0]['total_paid_year_fees']+$result_paid_yearly_fee[0]['FM_HDR_CONC_FEES']);

                    }

//                echo "<pre>".var_dump(@$result_all_yearly_fee[0]['total_due_year_fees'])."</pre>"; die();
                    /*----------------*/
                }
                // die();
                //echo "<pre>"; print_r($arr); die();

                $grand_fees = 0.00;
                $count_total_due_std = 0;

                // Set some content to print
                $html = '';
                $date = date('F, Y', strtotime($eff_date));
                $html .= <<<EOD
<table cellspacing="2">
<thead>
<tr>
<th width="300"><strong>FEES DUE STATEMENT FOR THE SESSION $company->COM_FIN_YEAR</strong></th>
<th><strong></strong></th>
<th><strong>Dues till: $date</strong></th>
</tr>
</thead>
</table>
<hr width="680">
EOD;

                if ($without_amount == 'yes') {
                    $html .= <<<EOD
<table style="line-height: 2;">
<thead>
<tr>
<th width="200"><strong>Student Names</strong></th>
<th><strong>Reg.No</strong></th>
<th><strong>(Class-Sec) - Roll</strong></th>
<th width="120"><strong>Months</strong></th>
<th><strong>Yearly</strong></th>
</tr>
</thead>
<tbody>
EOD;
                } else {
                    // echo"<pre>";
                    // print_r($arr);
                    //  echo"</pre>";
                    $html .= <<<EOD
<table style="line-height: 2;">
<thead>
<tr>
<th width="200"><strong>Student Names</strong></th>
<th><strong>Reg.No</strong></th>
<th><strong>(Class-Sec) - Roll</strong></th>
<th width="120"><strong>Months</strong></th>
<th><strong>Monthly Total</strong></th>
<th><strong>Yearly</strong></th>
<th><strong>Total</strong></th>
</tr>
</thead>
<tbody>
EOD;
                }
                $m_gd_total = 0.00;
                $y_gd_total = 0.00;
                $main_gd_total = 0.00;
                //echo $m_gd_total = array_sum(array_column($arr, 'total_due_mon_fees'));
                foreach ($arr as $s) { //this loop is for all students of selected class

                    // echo '<pre>',print_r($s),'</pre>';

                    $total_month_year_due_fees = 0;
                    $m_gd_total += $s["total_due_mon_fees"];
                    //$m_gd_total = number_format($m_gd_total, 2);
                    if ($s["total_year_fees_due"] == "Paid") {
                        $s["total_year_fees_due"] = "<p style='color: #037d03;'>Paid</p>";
                        $total_month_year_due_fees = $s["total_due_mon_fees"];
                    } else {
                        $s["total_year_fees_due"] = $s["total_year_fees_due"];
                        $total_month_year_due_fees = $s["total_due_mon_fees"] + $s["total_year_fees_due"];
                        $y_gd_total += $s["total_year_fees_due"];
                    }
                    $main_gd_total += $total_month_year_due_fees;

                    if ($total_month_year_due_fees == 0) {
                        continue;
                    }

                    $s["total_due_mon_fees"] = $s["total_due_mon_fees"];

                    if ($without_amount == 'yes') {
                        $html .= '<tr>
                                    <td width="200" style="font-size: 10px;">' . $s["st_name"] . '</td>
                                    <td>' . $s["st_reg"] . '</td>
                                    <td>(' . $s["st_cs"] . ') - ' . $s["st_roll"] . '</td>
                                    <td width="120" style="font-size: 10px;">' . $s["due_months"] . '</td>
                                    <td>' . $s["total_year_fees_due"] . '</td>
                                </tr>';
                    } else {
                        $html .= '<tr>
                                    <td width="200" style="font-size: 10px;">' . $s["st_name"] . '</td>
                                    <td>' . $s["st_reg"] . '</td>
                                    <td>(' . $s["st_cs"] . ') - ' . $s["st_roll"] . '</td>
                                    <td width="120" style="font-size: 8px;">' . $s["due_months"] . '</td>
                                    <td>' . $s["total_due_mon_fees"] . '</td>
                                    <td>' . $s["total_year_fees_due"] . '</td>
                                    <td>' . $total_month_year_due_fees . '</td>
                                </tr>';
                    }
                    $html .= '<hr style="line-height: -0.3" width="680">';
                }

                $html .= <<<EOD
</tbody>
EOD;

                if ($without_amount == 'yes') {
                    $html .= <<<EOD
</table>
EOD;
                } else {
                    $html .= <<<EOD
<hr width="680">
<tfoot>
<tr>
  <td><strong>GRAND TOTAL</strong></td>
  <td></td>
  <td></td>
  <td></td>
  <td><strong>$m_gd_total </strong></td>
  <td><strong>$y_gd_total</strong></td>
  <td><strong>$main_gd_total</strong></td>
</tr>
</tfoot>
</table>
EOD;
                }

                $all_grand_total_details[] = array(
                    'class_sec'=>$cls->class_sec,
                    'monthly_total'=>$m_gd_total,
                    'yearly_total'=>$y_gd_total,
                    'grand_total'=>$main_gd_total,
                );

                $all_m_gd_total += $m_gd_total;
                $all_y_gd_total += $y_gd_total;
                $all_main_gd_total += $main_gd_total;

                // Print text using writeHTMLCell()
                $pdf->writeHTMLCell(160, 0, '', 20, $html, 0, 1, 0, true, '', true);
            }

            //all class grand total
            if ($without_amount != 'yes') {
                $pdf->AddPage('P', 'A4');

                $html = <<<EOD
<table cellspacing="2">
<thead>
<tr>
<th width="300"><strong>FEES DUE STATEMENT FOR THE SESSION $company->COM_FIN_YEAR</strong></th>
<th><strong></strong></th>
<th><strong>Dues till: $date</strong></th>
</tr>
</thead>
</table>
<hr width="680">

<table style="line-height: 2;">
<thead>
<tr>
    <th><strong>Description</strong></th>
    <th><strong>Monthly Grand Total</strong></th>
    <th><strong>Yearly Grand Total</strong></th>
    <th><strong>Grand Total</strong></th>
</tr>
</thead>
<tbody>
EOD;

                foreach($all_grand_total_details as $agtd) {
                    $html .= "<tr>
        <td>". $agtd['class_sec'] ." Total</td>
        <td>". $agtd['monthly_total'] ."</td>
        <td>". $agtd['yearly_total'] ."</td>
        <td>". $agtd['grand_total'] ."</td>
    </tr>";
                }

                $html .= <<<EOD
<hr/>
<tr>
  <td><strong>Grand Total</strong></td>
  <td><strong>$all_m_gd_total</strong></td>
  <td><strong>$all_y_gd_total</strong></td>
  <td><strong>$all_main_gd_total</strong></td>
</tr>
</tbody>
</table>
EOD;

                $pdf->writeHTMLCell(160, 0, '', 20, $html, 0, 1, 0, true, '', true);
            }

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');
        }
        else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/all_dues_report');
        }
    }

    public function print_outstanding_total_report() {
        if($this->input->post('submit') == 'print_outstanding_total_report') { //if form submitted
            $classes = $this->input->post('class');
            $eff_date = date("Y-m-d", strtotime(str_replace('/','-', $this->input->post('eff_date'))));
            $eff_month = date('m', strtotime($eff_date));




            $months_arr = array("JAN"=>"1","FEB"=>"2","MAR"=>"3","APR"=>"4","MAY"=>"5","JUN"=>"6","JUL"=>"7","AUG"=>"8","SEP"=>"9","OCT"=>"10","NOV"=>"11","DEC"=>"12");
            if (CURRENT_YEAR == date('Y')) {
                //take only till selected month (month from date input)
                $months_arr = array_slice($months_arr, 0, array_search($eff_month, array_values($months_arr))+1, true);
            }

//            print_r($months_arr); die();


//----------------------------------------TCPDF-----------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Total Outstanding Report';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords('Total Outstanding Report, smg, developed by: https://sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
<div style="text-align:center;">
<span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
<br>
$company->COM_ADD2 , $company->COM_CITY
<br>
<strong style="font-size: 13px"><span style="background-color: black;color: white;">Total Outstanding Report</span></strong>
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

            // set default font subsetting mode
            $pdf->setFontSubsetting(true);

            // Set font
            $pdf->SetFont('helvetica', '', 9, '', true);
// -----------------------------------------------------------------------------------------------------

            $all_m_gd_total = 0.00;
            $all_y_gd_total = 0.00;
            $all_main_gd_total = 0.00;
            $all_grand_total_details = array();

            //loop for each class
            foreach ($classes as $class) {
                // Add a page


                $this->db->where('CS_SEQ', $class);
                $cls = $this->db->get('class_sec_hdr')->row();

                $this->db->select('STD_SEQ,ST_FULL_NAME, STD_FNAME,STD_MNAME,STD_LNAME,STD_REGNO,STD_DOB,STD_SRLNO,STD_ROLLNO,STD_CONSC, class_sec_hdr.class_sec as class_sec, STD_CS_SEQ');
                $this->db->where('STD_CS_SEQ', $class);
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ=student_details.STD_CS_SEQ', 'left');
                $this->db->where('STD_LEFT', 0);
                $this->db->order_by('STD_CS_SEQ,STD_ROLLNO');
//                $this->db->where('STD_REGNO', "P/057/21");
                $std = $this->db->get('student_details')->result_array();

                //if(count($yearly_fees_rs[0]['total_fees']) > 0) {$yearly_fees = $yearly_fees_rs[0]['total_fees'];} else {$yearly_fees = 'Not Set';}

                $arr = array();
                foreach ($std as $key => $std_value) {
                    $arr[$key]['st_id'] = $std_value['STD_SEQ'];
                    $arr[$key]['st_name'] = $std_value['ST_FULL_NAME'];
                    $arr[$key]['st_reg'] = $std_value['STD_REGNO'];
//                    $arr[$key]['st_reg'] = $std_value['STD_REGNO'].' '.date('dmY', strtotime($std_value['STD_DOB']));
                    $arr[$key]['st_cs'] = $std_value['class_sec'];
                    $arr[$key]['st_roll'] = $std_value['STD_ROLLNO'];

                    $this->db->select('FEES_DTL_MONTH, sum(fees_monthly_dtl.FEES_DTL_AMOUNT) as total_mon_fees');
                    $this->db->where('FEES_DTL_STD_SEQ', $std_value['STD_SEQ']);
                    $this->db->where('FEES_DTL_STD_CS_SEC', $class);
                    $this->db->where('DATE_FORMAT(`FEES_DTL_COL_DATE`, "%Y-%m") <= DATE_FORMAT("' . $eff_date . '", "%Y-%m")');
                    $this->db->group_by('FEES_DTL_MONTH');
                    $paid_rs = $this->db->get('fees_monthly_dtl')->result_array();
//                echo $this->db->last_query(); die();
                    $paid_months = array_column($paid_rs, 'FEES_DTL_MONTH');
//                print_r($paid_months); die();
                    $due_months = array_diff($months_arr, $paid_months);
//                print_r($due_months); die();
                    $fullDiff = array_merge(array_diff($months_arr, $paid_months));
//                 echo "<pre>"; print_r(count($fullDiff)); die();

                    $due_month = implode(', ', array_map(
                        function ($v, $k) {
                            return $k;
                        },
                        $fullDiff,
                        array_keys($fullDiff)
                    ));
//                 echo "<pre>"; print_r($paid_months); die();
                    $arr[$key]['due_months'] = $due_month;
//                 echo "<pre>"; print_r($due_month); die();

                    /*month total*/
                    $month_fees_due = 0;
                    /* 1 for granted cons and  0 for not granted cons  */
                    if ($std_value['STD_CONSC'] == 1) {
                        $this->db->select('COALESCE(SUM(fees_concession.Fees),0) AS total_single_month_due_fees');
                        $this->db->join('class_sec_dtl', 'class_sec_dtl.ACC_MASTER_CODE = fees_concession.ACC_MASTER_CODE', 'left');
                        $this->db->where('fees_concession.std_id', $std_value['STD_SEQ']);
//                    $this->db->where('fees_concession.class_id', $class);
                        $this->db->where('class_sec_dtl.CS_SEQ', $std_value['STD_CS_SEQ']);
                        $this->db->where('class_sec_dtl.CS_FEES_TYPE', '0');
//                    $this->db->group_by('fees_concession.fc_id');
                        $month_fees_due = $this->db->get('fees_concession')->result_array();

//                     echo $this->db->last_query(); die();
//                     echo $month_fees_due[0]['total_single_month_due_fees']; die();

                        //if concession fees not set, then fetch actual fees
                        if ($month_fees_due[0]['total_single_month_due_fees'] == '0.00') {
                            $this->db->select('COALESCE(SUM(Fees),0) AS total_single_month_due_fees');
                            $this->db->join('student_details', 'student_details.STD_CS_SEQ = class_sec_dtl.CS_SEQ', 'left');
                            $this->db->where('CS_FEES_TYPE', 0);
                            $this->db->where('CS_SEQ', $class);
                            $this->db->where('student_details.STD_SEQ', $std_value['STD_SEQ']);
                            $month_fees_due = $this->db->get('class_sec_dtl')->result_array();
                            // echo $this->db->last_query();
                            // echo $month_fees_due[0]['total_single_month_due_fees']; die();
                        }
                        // echo "<pre>"; print_r(@$month_fees_due[0]['total_single_month_due_fees']);
                        // echo $this->db->last_query();

                    } else {
                        $this->db->select('COALESCE(SUM(Fees),0) AS total_single_month_due_fees');
                        $this->db->join('student_details', 'student_details.STD_CS_SEQ = class_sec_dtl.CS_SEQ', 'left');
                        $this->db->where('CS_FEES_TYPE', 0); //monthly fee
                        $this->db->where('CS_SEQ', $class);
                        $this->db->where('student_details.STD_SEQ', $std_value['STD_SEQ']);
                        $month_fees_due = $this->db->get('class_sec_dtl')->result_array();
//                     echo $this->db->last_query(); echo "<br>"; die();
                    }
                    /*if(empty($paid_m_rs)){
                        $paid_m_total_rs = 'Due';
                    }else{
                        $paid_m_total_rs = $paid_m_rs->FM_HDR_TOT_FEES;
                    }*/
                    $arr[$key]['total_due_mon_fees'] = @$month_fees_due[0]['total_single_month_due_fees'] * @count($fullDiff);
                    //echo "<pre>".var_dump($month_fees_due)."</pre>"; die();
                    /*----------*/

                    /*yearly fees total*/
                    $result_all_yearly_fee = 0;

                    $this->db->select('FM_HDR_TOT_FEES as total_paid_year_fees, FM_HDR_CONC_FEES, due_amount');
                    $this->db->where('FM_HDR_STD_SEQ', $std_value['STD_SEQ']);
                    $this->db->where('FM_HDR_STD_CS_SEQ', $class);
                    $this->db->where('FM_HDR_FIN_YEAR', $company->COM_FIN_YEAR);
                    $result_paid_yearly_fee = $this->db->get('fees_yearly_hdr')->result_array();
                    $yearly_num_rows = $this->db->where('FM_HDR_STD_SEQ', $std_value['STD_SEQ'])->get('fees_yearly_hdr')->num_rows();
//                    echo $this->db->last_query(); echo "<br>"; die();

                    $this->db->select('SUM(Fees) as total_due_year_fees');
                    $this->db->join('student_details', 'student_details.STD_CS_SEQ = class_sec_dtl.CS_SEQ', 'left');
                    $this->db->where('CS_FEES_TYPE', 1); //yearly fees
                    $this->db->where('CS_SEQ', $class);
                    $this->db->where('student_details.STD_SEQ', $std_value['STD_SEQ']);
                    $result_all_yearly_fee = $this->db->get('class_sec_dtl')->result_array();
//                     echo $this->db->last_query(); echo "<br>"; die();
//                }

                    if($yearly_num_rows == 0){

                        $arr[$key]['total_year_fees_due'] = ($result_all_yearly_fee[0]['total_due_year_fees']);

                    }else if($result_paid_yearly_fee[0]['due_amount'] == 0){

                        $arr[$key]['total_year_fees_due'] = "Paid";

                    }else if ($result_paid_yearly_fee[0]['total_paid_year_fees']+$result_paid_yearly_fee[0]['FM_HDR_CONC_FEES'] >= $result_all_yearly_fee[0]['total_due_year_fees']) {

                        $arr[$key]['total_year_fees_due'] = "Paid";

                    } else {

                        $arr[$key]['total_year_fees_due'] = ($result_all_yearly_fee[0]['total_due_year_fees'] - $result_paid_yearly_fee[0]['total_paid_year_fees']+$result_paid_yearly_fee[0]['FM_HDR_CONC_FEES']);

                    }

//                echo "<pre>".var_dump(@$result_all_yearly_fee[0]['total_due_year_fees'])."</pre>"; die();
                    /*----------------*/
                }
                // die();
                // echo "<pre>"; print_r($arr); die();

                $grand_fees = 0.00;
                $count_total_due_std = 0;

                // Set some content to print
                $html = '';
                $date = date('F, Y', strtotime($eff_date));



                $m_gd_total = 0.00;
                $y_gd_total = 0.00;
                $main_gd_total = 0.00;
                //echo $m_gd_total = array_sum(array_column($arr, 'total_due_mon_fees'));
                foreach ($arr as $s) { //this loop is for all students of selected class

                    // echo '<pre>',print_r($s),'</pre>';

                    $total_month_year_due_fees = 0;
                    $m_gd_total += $s["total_due_mon_fees"];
                    //$m_gd_total = number_format($m_gd_total, 2);
                    if ($s["total_year_fees_due"] == "Paid") {
                        $s["total_year_fees_due"] = "<p style='color: #037d03;'>Paid</p>";
                        $total_month_year_due_fees = $s["total_due_mon_fees"];
                    } else {
                        $s["total_year_fees_due"] = $s["total_year_fees_due"];
                        $total_month_year_due_fees = $s["total_due_mon_fees"] + $s["total_year_fees_due"];
                        $y_gd_total += $s["total_year_fees_due"];
                    }
                    $main_gd_total += $total_month_year_due_fees;

                    if ($total_month_year_due_fees == 0) {
                        continue;
                    }

                    $s["total_due_mon_fees"] = $s["total_due_mon_fees"];


                }





                $all_grand_total_details[] = array(
                    'class_sec'=>$cls->class_sec,
                    'monthly_total'=>$m_gd_total,
                    'yearly_total'=>$y_gd_total,
                    'grand_total'=>$main_gd_total,
                );

                $all_m_gd_total += $m_gd_total;
                $all_y_gd_total += $y_gd_total;
                $all_main_gd_total += $main_gd_total;


            }

            //all class grand total

            $pdf->AddPage('P', 'A4');

            $html = <<<EOD
<table cellspacing="2">
<thead>
<tr>
<th width="300"><strong>FEES DUE STATEMENT FOR THE SESSION $company->COM_FIN_YEAR</strong></th>
<th><strong></strong></th>
<th><strong>Dues till: $date</strong></th>
</tr>
</thead>
</table>
<hr width="680">

<table style="line-height: 2;">
<thead>
<tr>
    <th><strong>Description</strong></th>
    <th><strong>Monthly Grand Total</strong></th>
    <th><strong>Yearly Grand Total</strong></th>
    <th><strong>Grand Total</strong></th>
</tr>
</thead>
<tbody>
EOD;

            foreach($all_grand_total_details as $agtd) {
                $html .= "<tr>
        <td>". $agtd['class_sec'] ." Total</td>
        <td>". $agtd['monthly_total'] ."</td>
        <td>". $agtd['yearly_total'] ."</td>
        <td>". $agtd['grand_total'] ."</td>
    </tr>";
            }

            $html .= <<<EOD
<hr/>
<tr>
  <td><strong>Grand Total</strong></td>
  <td><strong>$all_m_gd_total</strong></td>
  <td><strong>$all_y_gd_total</strong></td>
  <td><strong>$all_main_gd_total</strong></td>
</tr>
</tbody>
</table>
EOD;

            $pdf->writeHTMLCell(160, 0, '', 20, $html, 0, 1, 0, true, '', true);


            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');
        }
        else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/all_dues_report');
        }
    }

    public function payment_type_report() {
        $data['form_type'] = 'payment_type_report';

        $cls_type = $this->db->get('class_type')->result_array();
        $data['class_type'] = $cls_type;

        $data['tab_title'] = 'Payment Type Report';
        $data['section_heading'] = 'Payment Type Report <small>(Print)</small>';
        $data['menu_name'] = 'Payment Type Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function print_payment_type_report() {
        if($this->input->post('submit') == 'print_payment_type_report') { //if form submitted
            $date_from = $this->input->post('date_from');
            $date_to = $this->input->post('date_to');
            $class_type = $this->input->post('class_type[]');

            if($date_from != '' && $date_to != '') {
                if($date_from > $date_to) { //if from date is greater than to date
                    $this->session->set_flashdata('type', 'error');
                    $this->session->set_flashdata('title', 'Naa!');
                    $this->session->set_flashdata('msg', 'From Date must be equals to or less than To Date.');
                    return array('type' => 'redirect', 'page'=>'admin/payment_type_report');
                }
            }

            $company = $this->db->get_where('company', array('SCHOOL_TYPE' => 5))->row();

            //monthly fees
            $this->db->select('FM_HDR_P_TYP, SUM(FM_HDR_TOT_FEES) as total');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FM_HDR_STD_CS_SEQ', 'left');
            $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
            if($date_from != '') {$this->db->where('DATE(FM_HDR_COL_DATE) >=', $date_from);}
            if($date_to != '') {$this->db->where('DATE(FM_HDR_COL_DATE) <=', $date_to);}
            $this->db->group_by('FM_HDR_P_TYP');
            $monthly_fees = $this->db->get('fees_monthly_hdr')->result_array();

            //yearly fees
            $this->db->select('FM_HDR_P_TYP, SUM(FM_HDR_TOT_FEES) as total');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FM_HDR_STD_CS_SEQ', 'left');
            $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
            if($date_from != '') {$this->db->where('DATE(FM_HDR_COL_DATE) >=', $date_from);}
            if($date_to != '') {$this->db->where('DATE(FM_HDR_COL_DATE) <=', $date_to);}
            $this->db->group_by('FM_HDR_P_TYP');
            $yearly_fees = $this->db->get('fees_yearly_hdr')->result_array();

            //new admission fees
            $this->db->select('FM_HDR_P_TYP, SUM(FM_HDR_TOT_FEES) as total');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = FM_HDR_STD_CS_SEQ', 'left');
            $this->db->where_in("class_sec_hdr.Class_Type", $class_type);
            if($date_from != '') {$this->db->where('DATE(FM_HDR_COL_DATE) >=', $date_from);}
            if($date_to != '') {$this->db->where('DATE(FM_HDR_COL_DATE) <=', $date_to);}
            $this->db->group_by('FM_HDR_P_TYP');
            $new_admission_fees = $this->db->get('fees_newadm_hdr')->result_array();

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Payment Type Report';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords('Payment Type Report, smg, developed by: https://sketchmeglobal.com');

            if($date_from == '' || $date_to == '') {
                $date_range = 'All Dates';
            } else {
                $date_range = date("d-m-Y", strtotime($date_from)).' to '.date("d-m-Y", strtotime($date_to));
            }

            // set default header data
            $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2 , $company->COM_CITY
        <br>
        <strong>Payment Type Report: <span style="background-color: black;color: white;"> $date_range </span></strong>
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

            // Set some content to print
            $html = '';
            $grand_total = 0;

            //monthly
            $html .= '<h3 style="text-align: center">Monthly Fees Collection</h3>';
            $html .= <<<EOD
                <hr>
                <table cellspacing="2">
                    <thead>
                    <tr>
                        <th><strong>Payment Type</strong></th>
                        <th align="right"><strong>Amount</strong></th>
                    </tr>
                    </thead>
                    <tbody>
EOD;
            $section_total = 0;
            foreach ($monthly_fees as $val) {
                $section_total += $val['total'];
                $html .= <<<EOD
                        <tr>
                            <td>{$val['FM_HDR_P_TYP']}</td>
                            <td align="right">{$val['total']}</td>
                        </tr>
EOD;
            }
            $grand_total += $section_total;
            $section_total = number_format($section_total, 2);
            $html .= <<<EOD
                    </tbody>
                        <hr>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td align="right"><strong>$section_total</strong></td>
                        </tr>
                    </table>
                    <hr>
                    &nbsp;
EOD;

            //yearly
            $html .= '<h3 style="text-align: center">Yearly Fees Collection</h3>';
            $html .= <<<EOD
                <hr>
                <table cellspacing="2">
                    <thead>
                    <tr>
                        <th><strong>Payment Type</strong></th>
                        <th align="right"><strong>Amount</strong></th>
                    </tr>
                    </thead>
                    <tbody>
EOD;
            $section_total = 0;
            foreach ($yearly_fees as $val) {
                $section_total += $val['total'];
                $html .= <<<EOD
                        <tr>
                            <td>{$val['FM_HDR_P_TYP']}</td>
                            <td align="right">{$val['total']}</td>
                        </tr>
EOD;
            }
            $grand_total += $section_total;
            $section_total = number_format($section_total, 2);
            $html .= <<<EOD
                    </tbody>
                        <hr>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td align="right"><strong>$section_total</strong></td>
                        </tr>
                    </table>
                    <hr>
                    &nbsp;
EOD;

            //new admission
            $html .= '<h3 style="text-align: center">New Admission Fees Collection</h3>';
            $html .= <<<EOD
                <hr>
                <table cellspacing="2">
                    <thead>
                    <tr>
                        <th><strong>Payment Type</strong></th>
                        <th align="right"><strong>Amount</strong></th>
                    </tr>
                    </thead>
                    <tbody>
EOD;
            $section_total = 0;
            foreach ($new_admission_fees as $val) {
                $section_total += $val['total'];
                $html .= <<<EOD
                        <tr>
                            <td>{$val['FM_HDR_P_TYP']}</td>
                            <td align="right">{$val['total']}</td>
                        </tr>
EOD;
            }
            $grand_total += $section_total;
            $section_total = number_format($section_total, 2);
            $html .= <<<EOD
                    </tbody>
                        <hr>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td align="right"><strong>$section_total</strong></td>
                        </tr>
                    </table>
                    <hr>
                    &nbsp;
                    <br>
EOD;

            //grand total
            $grand_total = number_format($grand_total, 2);
            $html .= <<<EOD
                    <table cellspacing="2" border="1">
                        <thead>
                        <tr>
                            <th><strong>Grand Total</strong></th>
                            <th align="right"><strong>$grand_total</strong></th>
                        </tr>
                        </thead>
                    </table>
                    <br>
                    <div style="text-align: right"><small>Note: Amount is including late fines.</small></div>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/payment_type_report');
        }
    }

    public function library_register() {
        $data['form_type'] = 'library_register';

        $data['tab_title'] = 'Library Register';
        $data['section_heading'] = 'Library Register <small>(Print)</small>';
        $data['menu_name'] = 'Library Register';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function print_library_register() {
        if($this->input->post('submit') == 'print_library_register') { //if form submitted
            $date_from = $this->input->post('date_from');
            $date_to = $this->input->post('date_to');

            $company = $this->db->get_where('company', array('SCHOOL_TYPE' => 5))->row();

            $this->db->select('date_issue,date_return,Class_Name,Sec_Name,STD_FNAME,STD_MNAME,STD_LNAME,STD_REGNO,STD_ROLLNO,Book_Name');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = library_dtl.CS_SEQ', 'left');
            $this->db->join('student_details', 'student_details.STD_SEQ = library_dtl.STD_SEQ', 'left');
            $this->db->join('book_master', 'book_master.BOOK_SEQ = library_dtl.BOOK_SEQ', 'left');
            if($date_from != null && $date_to != null) {
                if($date_from > $date_to) { //if from date is greater than to date
                    $this->session->set_flashdata('type', 'error');
                    $this->session->set_flashdata('title', 'Naa!');
                    $this->session->set_flashdata('msg', 'From Date must be equals to or less than To Date.');
                    return array('type' => 'redirect', 'page'=>'admin/library_register');
                }
                $this->db->where('date_issue >=', $date_from);
                $this->db->where('date_issue <=', $date_to);
            }
            $this->db->where('library_dtl.BOOK_SEQ !=', '');
            $this->db->order_by('date_issue,Class_Name,Sec_Name,STD_ROLLNO');
            $lib_tran = $this->db->get('library_dtl')->result_array();
            $total_tran = count($lib_tran);

            //if no transaction found
            if($total_tran == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'No transaction found.');
                return array('type' => 'redirect', 'page'=>'admin/library_register');
            }

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Library Register';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords('Library Register, smg, developed by: https://sketchmeglobal.com');

            if($date_from == null || $date_to == null) {
                $date_range = 'All Dates';
            } else {
                $date_range = date("d-m-Y", strtotime($date_from)).' to '.date("d-m-Y", strtotime($date_to));
            }

            // set default header data
            $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2 , $company->COM_CITY
        <br>
        <strong>Library Register: <span style="background-color: black;color: white;"> $date_range </span> Total Transactions: <span style="background-color: black;color: white;"> $total_tran </span></strong>
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
            $pdf->SetFont('times', '', 10, '', true);

            // Add a page
            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $html = <<<EOD
                <table cellspacing="2">
                    <thead>
                    <tr>
                        <th width="70"><strong>Issue Date</strong></th>
                        <th width="80"><strong>Class & Sec</strong></th>
                        <th width="40"><strong>Roll</strong></th>
                        <th width="150"><strong>Student Name</strong></th>
                        <th width="70"><strong>Reg. No</strong></th>
                        <th width="175"><strong>Book Name</strong></th>
                        <th width="70"><strong>Return Date</strong></th>
                    </tr>
                    </thead>
                    <tbody>
EOD;

            foreach ($lib_tran as $val) {
                $issue_date = date('d-m-Y',strtotime($val['date_issue']));
                $return_date = date('d-m-Y',strtotime($val['date_return']));
                $html .= <<<EOD
                        <tr>
                            <td width="70">$issue_date</td>
                            <td width="80">{$val['Class_Name']} - {$val['Sec_Name']}</td>
                            <td width="40">{$val['STD_ROLLNO']}</td>
                            <td width="150">{$val['STD_FNAME']} {$val['STD_MNAME']} {$val['STD_LNAME']}</td>
                            <td width="70">{$val['STD_REGNO']}</td>
                            <td width="175">{$val['Book_Name']}</td>
                            <td width="70">$return_date</td>
                        </tr>
EOD;
            }

            $html .= <<<EOD
                    </tbody>
                </table>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/library_register');
        }
    }

    public function books_register() {
        $this->db->select('BOOK_SEQ,Accession_No,Book_Name');
        $books = $this->db->get('book_master')->result_array();

        $data['books'] = $books;
        $data['form_type'] = 'books_register';

        $data['tab_title'] = 'Books Register';
        $data['section_heading'] = 'Books Register <small>(Print)</small>';
        $data['menu_name'] = 'Books Register';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function print_books_register() {
        if($this->input->post('submit') == 'print_books_register') { //if form submitted
            $book_id = $this->input->post('book');

            $company = $this->db->get_where('company', array("SCHOOL_TYPE"=>5))->row();

            $this->db->select('library_dtl.BOOK_SEQ,Book_Name,COUNT(library_dtl.BOOK_SEQ) as total');
            $this->db->join('book_master', 'book_master.BOOK_SEQ = library_dtl.BOOK_SEQ', 'left');
            if($book_id != 'all') { //specific book
                $this->db->where('library_dtl.BOOK_SEQ', $book_id);
            }
            $this->db->where('library_dtl.BOOK_SEQ !=', '');
            $this->db->group_by('library_dtl.BOOK_SEQ');
            $books_arr = $this->db->get('library_dtl')->result_array();

            //if no transaction found
            if(count($books_arr) == 0) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Zzz!');
                $this->session->set_flashdata('msg', 'No transaction found.');
                return array('type' => 'redirect', 'page'=>'admin/books_register');
            }

            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $doc_name = 'Books Register';
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords('Books Register, smg, developed by: https://sketchmeglobal.com');

            if($book_id == 'all') {
                $book_name = 'All Books';
            } else {
                $book_name = array_column($books_arr, 'Book_Name');
                $book_name = $book_name[0];
            }

            // set default header data
            $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2 , $company->COM_CITY
        <br>
        <strong>Books Register: <span style="background-color: black;color: white;"> $book_name </span></strong>
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
            $pdf->SetFont('times', '', 13, '', true);

            // Add a page
            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $html = '';

            foreach ($books_arr as $v) {
                $this->db->select('date_issue,date_return,Class_Name,Sec_Name,STD_FNAME,STD_MNAME,STD_LNAME,STD_REGNO,STD_ROLLNO');
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = library_dtl.CS_SEQ', 'left');
                $this->db->join('student_details', 'student_details.STD_SEQ = library_dtl.STD_SEQ', 'left');
                $this->db->where_in('BOOK_SEQ', $v['BOOK_SEQ']);
                $this->db->order_by('date_issue,Class_Name,Sec_Name,STD_ROLLNO');
                $lib_tran = $this->db->get('library_dtl')->result_array();

                $html .= <<<EOD
                <div style="font-size: 17px">Book Name: <strong>{$v['Book_Name']}</strong> • Total Transaction: <strong>{$v['total']}</strong></div>
                <hr>
                <table cellspacing="2">
                    <thead>
                    <tr>
                        <th width="100"><strong>Issue Date</strong></th>
                        <th width="100"><strong>Class & Sec</strong></th>
                        <th width="50"><strong>Roll</strong></th>
                        <th width="190"><strong>Student Name</strong></th>
                        <th width="110"><strong>Reg. No</strong></th>
                        <th width="100"><strong>Return Date</strong></th>
                    </tr>
                    </thead>
                    <tbody>
EOD;

                foreach ($lib_tran as $val) {
                    $issue_date = date('d-m-Y',strtotime($val['date_issue']));
                    $return_date = date('d-m-Y',strtotime($val['date_return']));
                    $html .= <<<EOD
                        <tr>
                            <td width="100">$issue_date</td>
                            <td width="100">{$val['Class_Name']} - {$val['Sec_Name']}</td>
                            <td width="50">{$val['STD_ROLLNO']}</td>
                            <td width="190">{$val['STD_FNAME']} {$val['STD_MNAME']} {$val['STD_LNAME']}</td>
                            <td width="110">{$val['STD_REGNO']}</td>
                            <td width="100">$return_date</td>
                        </tr>
EOD;
                }

                $html .= <<<EOD
                    </tbody>
                </table>
<hr>
EOD;
            }

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/books_register');
        }
    }

    public function class_routine() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'class_routine';

        $data['tab_title'] = 'Class Routine';
        $data['section_heading'] = 'Class Routine <small>(Print)</small>';
        $data['menu_name'] = 'Class Routine';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function print_class_routine() {
        if($this->input->post('submit') == 'print_class_routine') { //if form submitted
            $classArr = $this->input->post('class');
             $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            foreach($classArr as $class){
               $company = $this->company_name((array)$class);
                $this->db->where('CS_SEQ', $class);
                $cls = $this->db->get('class_sec_hdr')->row();
                //if class does not exists
                if(count((array)$cls) == 0) {
                    $this->session->set_flashdata('type', 'error');
                    $this->session->set_flashdata('title', 'Naa!');
                    $this->session->set_flashdata('msg', 'Class does not exists.');
                    return array('type' => 'redirect', 'page'=>'admin/class_routine');
                }
                
                $class_teacher_id = $cls->class_teacher;
                $this->db->where('TCH_SRLNO', $class_teacher_id);
                $teacher = $this->db->get('teacher')->row();
                
                $this->db->select('*');
                $this->db->where('STD_CS_SEQ', $class);
                $this->db->where('student_details.STD_LEFT', 0);
                $this->db->where('student_details.STD_STATUS', 0);
                $this->db->order_by('STD_ROLLNO');
                $query = $this->db->get('student_details');
                $totalstd = $query->num_rows();
                
                $this->db->select('day,period,sub_no,sub_s_name,TCH_NAME');
                $this->db->join('subject', 'subject.sub_id = routine.sub_id', 'left');
                $this->db->join('teacher', 'teacher.TCH_SRLNO = routine.tch_id', 'left');
                $this->db->where('class_id', $class);
                $routine = $this->db->get('routine')->result_array();
    //            $routine = $this->db->get_compiled_select('routine');
    //            echo '<pre>';print_r($routine);echo '</pre>';exit();
    
                //if no routine found for that class
                if(count($routine) == 0) {
                    $this->session->set_flashdata('type', 'error');
                    $this->session->set_flashdata('title', 'Naa!');
                    $this->session->set_flashdata('msg', 'Routine not created for that class.');
                    return array('type' => 'redirect', 'page'=>'admin/class_routine');
                }
                $doc_name = 'Routine of Class '.$cls->Class_Name.' - '.$cls->Sec_Name;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords($doc_name.', smg, developed by: https://sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2 , $company->COM_CITY
        <br>
        <strong style="margin-top:10px;">Routine of Class: <span style="background-color: black;color: white; font-size:14px "> $cls->Class_Name - $cls->Sec_Name </span></strong>
        
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
                <table cellspacing="" border="1" align="Center" >
                    <thead>
                    <tr>
                        <th height="25"><strong>Day</strong></th>
                        <th width="10.7%"><strong >1st Period</strong></th>
                        <th width="10.7%"><strong>2nd Period</strong></th>
                        <th width="10.7%"><strong>3rd Period</strong></th>
                        <th width="10.7%"><strong>4th Period</strong></th>
                        <th width="5%"><strong>Brk.</strong></th>
                        <th width="10.7%"><strong>5th Period</strong></th>
                        <th width="10.7%"><strong>6th Period</strong></th>
                        <th width="10.7%"><strong>7th Period</strong></th>
                        <th width="10.7%"><strong>8th Period</strong></th>
                    </tr>
                    </thead>
                    <tbody>
EOD;

            $days = array('Monday','Tuesday','Wednesday','Thursday','Friday');

            //days loop
            $day_counter = 1;
            foreach ($days as $v) {
                $html .= '<tr>
                        <td height="75"><strong><br/>Day '.$day_counter++.'</strong></td>';
                //periods loop
                for($i=1; $i<=8; $i++) {
                    //searching for that day and period in routine-array
                    foreach($routine as $key => $val) {
                        if ( $val['day'] == $v && $val['period'] == $i && $val['sub_no'] == 1) {
                            $index = $key;
                            break;
                        }
                    }
                    foreach($routine as $key => $val) {
                        if ( $val['day'] == $v && $val['period'] == $i && $val['sub_no'] == 2) {
                            $index2 = $key;
                            break;
                        }
                    }

                    $html .= <<<EOD
                        <td width="10.7%">
                            {$routine[$index]['sub_s_name']}
                            <br>
                            <span style="margin-top:15px !important"><b>{$routine[$index]['TCH_NAME']} </b></span>                         
                            <br>
                            {$routine[$index2]['sub_s_name']}
                            <br>
                            <span style="margin-top:15px !important"><b>{$routine[$index2]['TCH_NAME']}</b></span>
                        </td>
EOD;
                    if ($i == 4) {
                        $html .= <<<EOD
                        <td width="5%"><br/><br/>•</td>
EOD;
                    }
                }
                $html .= "</tr>";
            }
            $html .= <<<EOD
                    </tbody>
                </table>
EOD;
            $html .='<br><div style="margin-top:10px"><span style="font-weight:bold;">Class Teacher Name :</span> '.$teacher->TCH_NAME.'</div>';
            $html .='<div style="margin-top:10px"><span style="font-weight:bold;">Total Student :</span> '.$totalstd.'</div>';

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            }
            
            

            
            //-------------------------------------------------------------------------------------------------------

            // create new PDF document
          

            // set document information
            

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/class_routine');
        }
    }

    public function teacher_routine() {
        $this->db->select('TCH_SRLNO,TCH_NAME');
        $teachers = $this->db->get('teacher')->result_array();

        $data['teachers'] = $teachers;
        $data['form_type'] = 'teacher_routine';

        $data['tab_title'] = 'Teacher Routine';
        $data['section_heading'] = 'Teacher Routine <small>(Print)</small>';
        $data['menu_name'] = 'Teacher Routine';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function print_teacher_routine() {
        if($this->input->post('submit') == 'print_teacher_routine') { //if form submitted
            //$teacher_id = $this->input->post('teacher');
            $teacherArr = $this->input->post('teacher');
             $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            foreach($teacherArr as $teacher_id){
                $company = $this->db->get_where('company', array('SCHOOL_TYPE' => 5))->row();
                $this->db->where('TCH_SRLNO', $teacher_id);
                $teacher = $this->db->get('teacher')->row();
    
                //if teacher does not exists
                if(count((array)$teacher) == 0) {
                    $this->session->set_flashdata('type', 'error');
                    $this->session->set_flashdata('title', 'Naa!');
                    $this->session->set_flashdata('msg', 'Teacher does not exists.');
                    return array('type' => 'redirect', 'page'=>'admin/teacher_routine');
                }
    
                $this->db->select('day,period,Class_Name,Sec_Name,sub_s_name');
                $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = routine.class_id', 'left');
                $this->db->join('subject', 'subject.sub_id = routine.sub_id', 'left');
                $this->db->where('tch_id', $teacher_id);
                $routine = $this->db->get('routine')->result_array();
    
                //if no routine found for that teacher
                if(count($routine) == 0) {
                    $this->session->set_flashdata('type', 'error');
                    $this->session->set_flashdata('title', 'Naa!');
                    $this->session->set_flashdata('msg', 'No class assigned for that teacher.');
                    return array('type' => 'redirect', 'page'=>'admin/teacher_routine');
                }
                // set document information
            $doc_name = 'Routine for '.$teacher->TCH_NAME;
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject($doc_name);
            $pdf->SetKeywords($doc_name.', smg, developed by: https://sketchmeglobal.com');

            // set default header data
            $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD2 , $company->COM_CITY
        <br>
        <strong>Routine for <span style="background-color: black;color: white; font-size:14px"> $teacher->TCH_NAME </span></strong>
       
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
                        <th width="10.7%" height="25"><strong>Day</strong></th>
                        <th width="10.7%"><strong>1st Period</strong></th>
                        <th width="10.7%"><strong>2nd Period</strong></th>
                        <th width="10.7%"><strong>3rd Period</strong></th>
                        <th width="10.7%"><strong>4th Period</strong></th>
                        <th width="5%"><strong>Brk.</strong></th>
                        <th width="10.7%"><strong>5th Period</strong></th>
                        <th width="10.7%"><strong>6th Period</strong></th>
                        <th width="10.7%"><strong>7th Period</strong></th>
                        <th width="10.7%"><strong>8th Period</strong></th>
                    </tr>
                    </thead>
                    <tbody>
EOD;

            $days = array('Monday','Tuesday','Wednesday','Thursday','Friday');

            //days loop
            $day_counter = 1;
            foreach ($days as $v) {
                $html .= '<tr>
                        <td width="10.7%" height="75"><strong><br/>Day '.$day_counter++.'</strong></td>';

                //periods loop
                for($i=1; $i<=8; $i++) {
                    $cls_sec = '';
                    $sub_s_name = '';
                    //fetching class & subject name for specific day & period
                    foreach ($routine as $val) {
                        if ($val['day'] == $v && $val['period'] == $i) {
                            $cls_sec = $val['Class_Name'].' - '.$val['Sec_Name'];
                            $sub_s_name = $val['sub_s_name'];
                            break;
                        }
                    }

                    $html .= <<<EOD
                        <td width="10.7%">
                            $cls_sec
                            <br>
                            $sub_s_name
                        </td>
EOD;
                    if ($i == 4) {
                        $html .= <<<EOD
                        <td width="5%"><br/><br/>•</td>
EOD;
                    }
                }
                $html .= "</tr>";
            }
            $html .= <<<EOD
                    </tbody>
                </table>
EOD;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            }
            
            

            

            // Close and output PDF document
            $pdf->Output($doc_name . '.pdf', 'I');

        } else { //if form not submitted
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page'=>'admin/teacher_routine');
        }
    }
    
    public function master_routine() {
        $cls = $this->db
        ->order_by('class_order', 'ASC')
        ->get('class_sec_hdr')
        ->result_array();
       
        $data['class'] = $cls;
        
        $cls_type = $this->db->get('class_type')->result_array();
        $data['class_type'] = $cls_type;
        
        $data['form_type'] = 'master_routine';

        $data['tab_title'] = 'Master Routine';
        $data['section_heading'] = 'Master Routine <small>(Print)</small>';
        $data['menu_name'] = 'Master Routine';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }

    public function print_master_routine() {
        
        
        
        $company = $this->db->get_where('company', array('SCHOOL_TYPE'=>5))->row();
        $this->db->select('TCH_SRLNO,TCH_NAME');
        $this->db->where('dept_code !=', 3);
        if($this->input->post('class_type') !== 'all'){ 
            $cls = $this->input->post('category_class');
            $this->db->where_in('TCH_CS_SEQ',$cls);
        }
        $teachers = $this->db->get('teacher')->result_array();

        $this->db->select('tch_id,day,period,Class_Name,Sec_Name,sub_s_name');
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = routine.class_id', 'left');
        $this->db->join('subject', 'subject.sub_id = routine.sub_id', 'left');
        if($this->input->post('class_type') !== 'all'){
            $class_id = implode(",",$this->input->post('category_class'));
            $cls = $this->input->post('category_class');
            $this->db->where_in('routine.class_id',$cls);
        }
    
        $routine = $this->db->get('routine')->result_array();

        //-------------------------------------------------------------------------------------------------------

        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $doc_name = 'Master Routine';
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company->COM_NAME);
        $pdf->SetTitle($doc_name);
        $pdf->SetSubject($doc_name);
        $pdf->SetKeywords($doc_name.', smg, developed by: https://sketchmeglobal.com');

        // set default header data
        $html_header = <<<EOD
    <div style="text-align:center;">
    <span style="font-size: 15px;"><strong>$company->COM_NAME</strong></span>
    <br>
    $company->COM_ADD2 , $company->COM_CITY
    <br>
    <strong><span style="background-color: black;color: white;"> Master Routine </span></strong>
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
        $pdf->SetFont('times', '', 10, '', true);

        // Add a page
        $pdf->AddPage('L', 'A3');

        // Set some content to print
        $html = <<<EOD
            <table cellspacing="" border="1" align="Center">
                <thead>
                <tr>
                    <th width="120"><strong>Teacher's Name</strong></th>
                    <th width="258"><strong>Monday</strong></th>
                    <th width="258"><strong>Tuesday</strong></th>
                    <th width="258"><strong>Wednesday</strong></th>
                    <th width="258"><strong>Thursday</strong></th>
                    <th width="258"><strong>Friday</strong></th>
                </tr>
                <tr>
                    <th><strong></strong></th>
EOD;
        for($x=1; $x<=5; $x++){ //days loop
            for($y=1; $y<=8; $y++){ //periods loop
                $html .= '<th width="32.26"><strong>'.$y.' P</strong></th>';
            }
        }
        $html .= <<<EOD
                </tr>
                </thead>
                <tbody>
EOD;

        //teachers loop
        foreach ($teachers as $tch) {
            $html .= '<tr style="font-size: 7px">
                    <td width="120" height="40" align="left" style="font-size: 11px"><strong>'.$tch["TCH_NAME"].'</strong></td>';

            $days = array('Monday','Tuesday','Wednesday','Thursday','Friday');

            //days loop
            foreach ($days as $v) {
                //periods loop
                for($i=1; $i<=8; $i++) {
                    $cls_sec = '';
                    $sub_s_name = '';
                    //fetching class & subject name for specific day & period
                    foreach ($routine as $val) {
                        if ($val['tch_id'] == $tch["TCH_SRLNO"] && $val['day'] == $v && $val['period'] == $i) {
                            $cls_sec = $val['Class_Name'].' - '.$val['Sec_Name'];
                            $sub_s_name = $val['sub_s_name'];
                            break;
                        }
                    }

                    $html .= <<<EOD
                    <td width="32.26" style="font-size:10px;">
                        $cls_sec
                        <br>
                        $sub_s_name
                    </td>
EOD;
                }
            }

            $html .= '</tr>';
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

    public function student_list() {
        //$cls = $this->db->get('class_sec_hdr')->result_array();
        $cls = $this->db
        ->order_by('class_order', 'ASC')
        ->get('class_sec_hdr')
        ->result_array();
        $this->db->select('student_details.STD_SEQ, student_details.STD_REGNO, student_details.ST_FULL_NAME, student_details.STD_ROLLNO, class_sec_hdr.class_sec, class_sec_hdr.CS_SEQ');
        $this->db->where('student_details.STD_LEFT', 0);
        $this->db->where('student_details.STD_STATUS', 0);
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $student = $this->db->get('student_details')->result_array();

        $religion = $this->db->get('religion')->result_array();
        
        $cls_type = $this->db->get('class_type')->result_array();
        $data['class_type'] = $cls_type;

        $data['exam_terms'] = $this->db->get_where('exam_terms', array('status' => 1, 'term_year' => FINANCIAL_YEAR))->result_array();
        $data['class'] = $cls;
        $data['student'] = $student;
        $data['religion'] = $religion;
        $data['form_type'] = 'class_wise_blank_mark_sheet';

        $data['tab_title'] = 'Student List Report';
        $data['section_heading'] = 'Student List Report <small>(Print)</small>';
        $data['menu_name'] = 'Student List Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }
    
    public function std_ranklist_report() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'std_ranklist_report';

        $data['tab_title'] = 'Rank List Report';
        $data['section_heading'] = 'Rank List Report <small>(Print)</small>';
        $data['menu_name'] = 'Rank List Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }
    public function class_subject_topper_report() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'class_subject_topper_report';

        $data['tab_title'] = 'Class Wise Subject topper';
        $data['section_heading'] = 'Class Wise Subject topper <small>(Print)</small>';
        $data['menu_name'] = 'Class Wise Subject topper';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }
    
     public function std_due_undertaking_report() {
        $cls = $this->db->get('class_sec_hdr')->result_array();

        $data['class'] = $cls;
        $data['form_type'] = 'due_undertaking_report';

        $data['tab_title'] = 'Due Undertaking Report';
        $data['section_heading'] = 'Due Undertaking Report <small>(Print)</small>';
        $data['menu_name'] = 'Due Undertaking Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }


    public function print_student_list_report() {

        // echo "<pre>"; print_r($this->input->post()); die();

        $cs_seq = $this->input->post('class')[0];
        $ctype = $this->db->get_where('class_sec_hdr', array('CS_SEQ' => $cs_seq))->row()->Class_Type; // 1/2  = from NUR to IV; 3 = from V to XII

        if($ctype == 1 or $ctype == 2){
            $school_category = 'primary';
        }else{
            $school_category = 'secondary';
        }

        $class_id = $this->input->post('class[]');
        $term_seq = $this->input->post('term');
        $term_name = $this->db->get_where('exam_terms', array('et_id' => $term_seq))->row()->term_title;
        $st_id_list = $this->input->post('st_id_list[]');
        $type1 = $this->input->post('type1');
        $type2 = $this->input->post('type2');
        $single_page = $this->input->post('single_page');
        if (empty($class_id)) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'Something went wrong');
            return array('type' => 'redirect', 'page'=>'admin/student_list');
        }


        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $iter = 0;

        $report_type_hdr = '';
        switch ($type1) {
            case "summary":
                $report_type_hdr = 'Summary Report';
                break;
            case "tel_no":
                $report_type_hdr = 'Telephone Report';
                break;
            case "attendence":
                $report_type_hdr = 'Attendence Report';
                break;
            case "st_list":
                $report_type_hdr = 'Student List Report';
                break;
            case "mark_sheet_1":
                $report_type_hdr = 'Mark Sheet Type 1 Report';
                break;
            case "mark_sheet_2":
                $report_type_hdr = 'Mark Sheet Type 2 Report';
                break;
            case "mark_sheet_1_blank":
                $report_type_hdr = 'Mark Sheet Type 1 Report (Blank)';
                break;
            case "mark_sheet_2_blank":
                $report_type_hdr = 'Mark Sheet Type 2 Report (Blank)';
                break;
            default:
                $report_type_hdr = 'Report';
        }


        $company = $this->company_name((array)$class_id); 

        // set document information
        $doc_name = $report_type_hdr.'_'.date('d-m-Y_h-i-A');
        $pdf->SetCreator(PDF_CREATOR);

        $pdf->SetTitle($doc_name);
        $pdf->SetSubject('Student Strength Report');
        $pdf->SetKeywords('All Transaction Report, smg, developed by: https://sketchmeglobal.com');


        $pdf->SetAuthor($company->COM_NAME);

        // set default header data
        $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 25px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD1, $company->COM_ADD2
        <br>
        <strong><span style="background-color: black;color: white;"> $report_type_hdr </span></strong>
        <br>
        </div> 
EOD;
        $pdf->setHtmlHeader($html_header, false);

        // set header and footer fonts and size
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(30, 20, 10, true);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks

        $pdf->setPrintFooter(false);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // -----------------------------------------------------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        if ($type1 == 'summary') {

            // Set font
            $pdf->SetAutoPageBreak(TRUE, 5);
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();
            $current_year = date('Y');
            // echo "<pre>"; print_r($class_data); die();
            $head = '';
            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                //echo $key; die();
                $html .= <<<EOD
                <table style="width:100%;">
                <tr>
        <td width="80"><strong>Class & Sec</strong></td>
        <td hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></td>
        <td width="50"><strong>Subject:</strong></td>
        <td width="330"> <strong> __________________________________________________ </strong> </td>
        <td width="38" align="right"><strong>Year: </strong></td>
        <td width="50"><strong>$current_year</strong></td>
    </tr>

    <tr>
        <td colspan="6"></td>
        
    </tr>
</table>
EOD;
                $html .= '
                <table cellspacing="0" border="1" style="width:100%;">
                    <thead>
                    <tr style="border: 1px solid black;">
                        <th width="20" align="center"><strong> # </strong></th>
                        <th width="53" align="center"><strong> Regd.No </strong></th>
                        <th width="50" align="center"><strong> Roll No </strong></th>
                        <th width="190" align="left"><strong> Student Names</strong></th>
                        <th width="40" align="center"><strong> PoT</strong></th>
                        <th width="55" ><strong style="font-size:9px; !important;text-align:center !important"> REL/ VAL</strong></th>
                        <th width="55" align="center"><strong> 2nd lang</strong></th>
                        <th width="55" align="center"><strong> House</strong></th>';
                        if($class_data_val->CS_SEQ == 18 || $class_data_val->CS_SEQ == 19 || $class_data_val->CS_SEQ == 20 || $class_data_val->CS_SEQ == 21){
                            $html .= '<th width="55" align="center"><strong> 3rd lang</strong></th>';
                        }
                        $html .= '
                       
                        <th></th>
                        <th></th>
                        <th></th>
                        
                    </tr>
                    </thead>
                    <tbody>';
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where_in('STD_SEQ', $st_id_list);
                $this->db->order_by('STD_ROLLNO');
                $this->db->where('STD_LEFT', 0);
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                $pot = 0;
                $pow = 0;
                $rel = 0;
                $val = 0;
                $hindieng = 0;
                $engben = 0;
                $hindi = 0;
                $ben = 0;
                $h = 0;
                $b = 0;
                $red = 0;
                $green = 0;
                $yellow = 0;
                $blue = 0;
                foreach ($st_data as $index => $st_data_val) {
                    if($st_data_val['PoT_PoW'] == "PoT"){
                        $pot +=1;
                    }
                    if($st_data_val['PoT_PoW'] == "PoW"){
                        $pow +=1;
                    }
                    if($st_data_val['STD_RLGN'] == 3){
                        $rel +=1;
                    }
                    if($st_data_val['STD_RLGN'] == 1 || $st_data_val['STD_RLGN'] == 2 || $st_data_val['STD_RLGN'] == 4 || $st_data_val['STD_RLGN'] == 5 || $st_data_val['STD_RLGN'] == 6 || $st_data_val['STD_RLGN'] == 7){
                        $val +=1;
                    }
                    if($class_data_val->CS_SEQ == 22 || $class_data_val->CS_SEQ == 23 || $class_data_val->CS_SEQ == 24 || $class_data_val->CS_SEQ == 25){
                        if($st_data_val['STD_SECOND_LANG'] == "HINDI, ENG"){
                            $hindieng +=1;
                        }
                        if($st_data_val['STD_SECOND_LANG'] == "ENG, BENG"){
                            $engben +=1;
                        }
                    }else{
                        if($st_data_val['STD_SECOND_LANG'] == "Bengali"){
                            $ben +=1;
                        }
                        if($st_data_val['STD_SECOND_LANG'] == "Hindi"){
                            $hindi +=1;
                        }
                    }
                    if($st_data_val['STD_HOUSE'] == "Red"){
                        $red +=1;
                    }
                    if($st_data_val['STD_HOUSE'] == "Green"){
                        $green +=1;
                    }
                    if($st_data_val['STD_HOUSE'] == "Yellow"){
                        $yellow +=1;
                    }
                    if($st_data_val['STD_HOUSE'] == "Blue"){
                        $blue +=1;
                    }
                    $html .= '<tr style="line-height: 20px;"> 
                            <td width="20" align="center">'.++$iter.'</td>
                            <td width="53" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="50" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
                            <td width="190" align="left">'.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="40" align="center">'.(isset($st_data_val['PoT_PoW']) && $st_data_val['PoT_PoW'] !== 'null' ? $st_data_val['PoT_PoW'] : '').'</td>
                            <td width="55" align="center">'
                                .(isset($st_data_val['STD_RLGN']) && $st_data_val['STD_RLGN'] !== 'null'
                                    ? ($st_data_val['STD_RLGN'] === '3' ? 'REL' : 'VAL')
                                    : '')
                            .'</td>
                            <td width="55" align="center"  style="font-size:7px; !important;text-align:center !important">'.(isset($st_data_val['STD_SECOND_LANG']) && $st_data_val['STD_SECOND_LANG'] !== 'null' ? $st_data_val['STD_SECOND_LANG'] : '').'</td>
                            <td width="55" align="center"  style="font-size:7px; !important;text-align:center !important">'.(isset($st_data_val['STD_HOUSE']) && $st_data_val['STD_HOUSE'] !== 'NULL' ? $st_data_val['STD_HOUSE'] : '').'</td>';
                            if($class_data_val->CS_SEQ == 18 || $class_data_val->CS_SEQ == 19 || $class_data_val->CS_SEQ == 20 || $class_data_val->CS_SEQ == 21){
                                if($st_data_val['STD_SECOND_LANG'] === "Bengali"){
                                    $third_lang = "Hindi";
                                }else if($st_data_val['STD_SECOND_LANG'] === "Hindi"){
                                    $third_lang = "Bengali";
                                }else{
                                    $third_lang = "";
                                }
                                $html .= ' 
                                <td width="55" align="center"  style="font-size:7px; !important;text-align:center !important">'.$third_lang.'</td>';
                            }
                        $html .='
                           
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>';
                }
                 if($class_data_val->CS_SEQ == 22 || $class_data_val->CS_SEQ == 23 || $class_data_val->CS_SEQ == 24 || $class_data_val->CS_SEQ == 25){
                     $h = "<strong>HINDI, ENG:</strong>".$hindieng;
                     $b = "<strong>ENG, BENG:</strong>".$engben;
                 }else{
                     $h = "<strong>Hindi:</strong>".$hindi;
                     $b = "<strong>Bengali:</strong>".$ben;
                 }

                $html .= "
                    </tbody>
                </table>
                
                <table >
                    <thead>
                    <tr>
                        <th ></th>
                        <th></th>
                        
                    </tr>
                    <tr>
                        <th width='500' align='left'><strong>Teacher's Name :_________________________________  </strong></th>
                        <th  width='210' align='center'><strong>Signature :___________________________________ </strong></th>                        
                    </tr>
                   
                    
                    </thead>
                </table>
                <table >
                    <thead>
                     <tr>
                        <th ></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        
                    </tr>
                    <tr>
                        <th width='50' align='left'><strong>PoT:</strong>$pot</th>
                        <th width='50' align='left'><strong>PoW:</strong>$pow</th>
                        <th width='50' align='left'><strong>REL:</strong>$rel</th> 
                        <th width='50' align='left'><strong>VAL:</strong>$val</th>
                        <th width='50' align='left'>$h</th> 
                        <th width='50' align='left'>$b</th> 
                    </tr>
                    <tr>
                        <th width='50' align='left'><strong>Red:</strong>$red</th>
                        <th width='50' align='left'><strong>Green:</strong>$green</th>
                        <th width='50' align='left'><strong>Yellow:</strong>$yellow</th> 
                        <th width='50' align='left'><strong>Blue:</strong>$blue</th>
                    </tr>
                   
                    
                    </thead>
                </table>
                
                ";
// Print text using writeHTMLCell()
                if($single_page <> 'yes'){
                    if (in_array('all', $class_id)) {
                        if(count($class_data) - 1 != $key){
                            $html .= '<br pagebreak="true" />';
                        }
                    }
                }


            }

            // $pdf->writeHTMLCell(0, 0, 5, 23, $head, 0, 1, 0, true, '', true);
            $pdf->writeHTMLCell(0, 0, 5, 28, $html, 0, 1, 0, true, '', true);
            // $pdf->writeHTML($html, true, false, false, false, '');
        }
        
        if ($type1 == 'summary_blank') {

            // Set font
            $pdf->SetAutoPageBreak(TRUE, 5);
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();
            $current_year = date('Y');
            // echo "<pre>"; print_r($class_data); die();
            $head = '';
            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                //echo $key; die();
                $html .= <<<EOD
                <table style="width:100%;">
                <tr>
        <td width="80"><strong>Class & Secx</strong></td>
        <td hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></td>
        <td width="50"><strong>Subject:</strong></td>
        <td width="330"> <strong> __________________________________________________ </strong> </td>
        <td width="38" align="right"><strong>Year: </strong></td>
        <td width="50"><strong>$current_year</strong></td>
    </tr>

    <tr>
        <td colspan="6"></td>
        
    </tr>
</table>
EOD;
                $html .= '
                <table cellspacing="0" border="1" style="width:100%;">
                    <thead>
                    <tr style="border: 1px solid black;">
                        <th width="20" align="center"><strong> # </strong></th>
                        <th width="53" align="center"><strong> Regd.No </strong></th>
                        <th width="50" align="center"><strong> Roll No </strong></th>
                        <th width="190" align="left"><strong> Student Names</strong></th>
                        <th width="40" align="center"><strong> PoT</strong></th>
                        <th width="55" ><strong style="font-size:9px; !important;text-align:center !important"> REL/ VAL</strong></th>
                        <th width="55" align="center"><strong> 2nd lang</strong></th>
                        <th width="55" align="center"><strong> House</strong></th>';
                        if($class_data_val->CS_SEQ == 18 || $class_data_val->CS_SEQ == 19 || $class_data_val->CS_SEQ == 20 || $class_data_val->CS_SEQ == 21){
                            $html .= '<th width="55" align="center"><strong> 3rd lang</strong></th>';
                        }
                        $html .= '
                       
                        <th></th>
                        <th></th>
                        <th></th>
                        
                    </tr>
                    </thead>
                    <tbody>';
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where_in('STD_SEQ', $st_id_list);
                $this->db->order_by('STD_ROLLNO');
                $this->db->where('STD_LEFT', 0);
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                $pot = 0;
                $pow = 0;
                $rel = 0;
                $val = 0;
                $hindieng = 0;
                $engben = 0;
                $hindi = 0;
                $ben = 0;
                $h = 0;
                $b = 0;
                $red = 0;
                $green = 0;
                $yellow = 0;
                $blue = 0;
                foreach ($st_data as $index => $st_data_val) {
                    if($st_data_val['PoT_PoW'] == "PoT"){
                        $pot +=1;
                    }
                    if($st_data_val['PoT_PoW'] == "PoW"){
                        $pow +=1;
                    }
                    if($st_data_val['STD_RLGN'] == 3){
                        $rel +=1;
                    }
                    if($st_data_val['STD_RLGN'] == 1 || $st_data_val['STD_RLGN'] == 2 || $st_data_val['STD_RLGN'] == 4 || $st_data_val['STD_RLGN'] == 5 || $st_data_val['STD_RLGN'] == 6 || $st_data_val['STD_RLGN'] == 7){
                        $val +=1;
                    }
                    if($class_data_val->CS_SEQ == 22 || $class_data_val->CS_SEQ == 23 || $class_data_val->CS_SEQ == 24 || $class_data_val->CS_SEQ == 25){
                        if($st_data_val['STD_SECOND_LANG'] == "HINDI, ENG"){
                            $hindieng +=1;
                        }
                        if($st_data_val['STD_SECOND_LANG'] == "ENG, BENG"){
                            $engben +=1;
                        }
                    }else{
                        if($st_data_val['STD_SECOND_LANG'] == "Bengali"){
                            $ben +=1;
                        }
                        if($st_data_val['STD_SECOND_LANG'] == "Hindi"){
                            $hindi +=1;
                        }
                    }
                    if($st_data_val['STD_HOUSE'] == "Red"){
                        $red +=1;
                    }
                    if($st_data_val['STD_HOUSE'] == "Green"){
                        $green +=1;
                    }
                    if($st_data_val['STD_HOUSE'] == "Yellow"){
                        $yellow +=1;
                    }
                    if($st_data_val['STD_HOUSE'] == "Blue"){
                        $blue +=1;
                    }
                    $html .= '<tr style="line-height: 20px;"> 
                            <td width="20" align="center">'.++$iter.'</td>
                            <td width="53" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="50" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
                            <td width="190" align="left">'.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="40" align="center"></td>
                            <td width="55" align="center"></td>
                            <td width="55" align="center"  style="font-size:7px; !important;text-align:center !important"></td>
                            <td width="55" align="center"  style="font-size:7px; !important;text-align:center !important"></td>';
                            if($class_data_val->CS_SEQ == 18 || $class_data_val->CS_SEQ == 19 || $class_data_val->CS_SEQ == 20 || $class_data_val->CS_SEQ == 21){
                                if($st_data_val['STD_SECOND_LANG'] === "Bengali"){
                                    $third_lang = "Hindi";
                                }else if($st_data_val['STD_SECOND_LANG'] === "Hindi"){
                                    $third_lang = "Bengali";
                                }else{
                                    $third_lang = "";
                                }
                                $html .= ' 
                                <td width="55" align="center"  style="font-size:7px; !important;text-align:center !important">'.$third_lang.'</td>';
                            }
                        $html .='
                           
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>';
                }
                 if($class_data_val->CS_SEQ == 22 || $class_data_val->CS_SEQ == 23 || $class_data_val->CS_SEQ == 24 || $class_data_val->CS_SEQ == 25){
                     $h = "<strong>HINDI, ENG:</strong>".$hindieng;
                     $b = "<strong>ENG, BENG:</strong>".$engben;
                 }else{
                     $h = "<strong>Hindi:</strong>".$hindi;
                     $b = "<strong>Bengali:</strong>".$ben;
                 }

                $html .= "
                    </tbody>
                </table>
                
                <table >
                    <thead>
                    <tr>
                        <th ></th>
                        <th></th>
                        
                    </tr>
                    <tr>
                        <th width='500' align='left'><strong>Teacher's Name :_________________________________  </strong></th>
                        <th  width='210' align='center'><strong>Signature :___________________________________ </strong></th>                        
                    </tr>
                   
                    
                    </thead>
                </table>
                <table >
                    <thead>
                     <tr>
                        <th ></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        
                    </tr>
                    <tr>
                        <th width='50' align='left'><strong>PoT:</strong>$pot</th>
                        <th width='50' align='left'><strong>PoW:</strong>$pow</th>
                        <th width='50' align='left'><strong>REL:</strong>$rel</th> 
                        <th width='50' align='left'><strong>VAL:</strong>$val</th>
                        <th width='50' align='left'>$h</th> 
                        <th width='50' align='left'>$b</th> 
                    </tr>
                    <tr>
                        <th width='50' align='left'><strong>Red:</strong>$red</th>
                        <th width='50' align='left'><strong>Green:</strong>$green</th>
                        <th width='50' align='left'><strong>Yellow:</strong>$yellow</th> 
                        <th width='50' align='left'><strong>Blue:</strong>$blue</th>
                    </tr>
                   
                    
                    </thead>
                </table>
                
                ";
// Print text using writeHTMLCell()
                if($single_page <> 'yes'){
                    if (in_array('all', $class_id)) {
                        if(count($class_data) - 1 != $key){
                            $html .= '<br pagebreak="true" />';
                        }
                    }
                }


            }

            // $pdf->writeHTMLCell(0, 0, 5, 23, $head, 0, 1, 0, true, '', true);
            $pdf->writeHTMLCell(0, 0, 5, 28, $html, 0, 1, 0, true, '', true);
            // $pdf->writeHTML($html, true, false, false, false, '');
        }

        else if ($type1 == 'tel_no') {
            //$pdf->SetAutoPageBreak(TRUE);
            // Set font
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('P', 'A4');
            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();
            $currentYear = CURRENT_YEAR;
            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
                <table style="width:100%;">
                <tr>
                        
        <th width="80"><strong>Class & Sec</strong></th>
        <th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
        <th width="50"><strong>Subject:</strong></th>
        <th width="330"> <strong> __________________________________________________ </strong> </th>
        <th width="38" align="right"><strong>Year: </strong></th>
        <th width="50" align="right">  <strong>$currentYear</strong></th>
    </tr>
    <tr>
        <td colspan="6"></td>
        
    </tr>
</table>

EOD;

                $html .= <<<EOD
                <table  border="1">
                    <thead>
                    <tr style="border: 1px solid black;">
                        <th width="20" align="center"><strong> # </strong></th>
                        <th width="53" align="center"><strong> Regd.No </strong></th>
                        <th width="50" align="center"><strong> Roll No </strong></th>
                        <th  width="320" align="left"><strong> Student Names with Telephone Numbers</strong></th>
                        <th align="center"><strong>Phone (Residence)</strong></th>
                        <th align="center"><strong>Phone (Office)</strong></th>
                       
                    </tr>
                    </thead>
                    <tbody>
EOD;
                // $this->db->join('student_parent_details', 'student_parent_details.STD_SEQ = student_details.STD_SEQ', 'right');
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where_in('student_details.STD_SEQ', $st_id_list);
                $this->db->where('student_details.STD_LEFT', 0);
                $this->db->order_by('student_details.STD_ROLLNO');
                $st_data = $this->db->get('student_details')->result_array();

                // echo $this->db->last_query(); die();
                $iter = 0;
                foreach ($st_data as $index => $st_data_val) {
                    $STD_FTH_PHNO = '';
                    $STD_FTH_PHNO = $this->db->get_where('student_parent_details', array('STD_SEQ' => $st_data_val['STD_SEQ']))->row();
                    $html .= '<tr style="line-height: 20px;" nobr="true">
                            <td width="20" align="center">'.++$iter.'</td>
                            <td width="53" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="50" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
                            <td width="320" align="left"> '.$st_data_val['ST_FULL_NAME'].'</td>
                            <td align="center">'.$st_data_val['STD_PH_NO'].'</td>
                            <td align="center">'.$STD_FTH_PHNO->STD_FTH_PHNO.'</td>
                        </tr>';
                }

                $html .= <<<EOD
                    </tbody>
                </table>
                <br><br><br>
                <table >
                    <thead>
                     <tr>
                        <th ></th>
                        <th></th>
                        
                    </tr>
                    <tr>
                        <th width="500" align="left"><strong>Teacher's Name  </strong></th>
                        <th  width="210" align="center"><strong> Signature </strong></th>                        
                    </tr>
                    </thead>
                </table>               
EOD;
// Print text using writeHTMLCell()
                if (in_array('all', $class_id)) {
                    if(count($class_data) - 1 != $key){
                        $html .= '<br pagebreak="true" />';
                    }
                }
            }

            $pdf->writeHTMLCell(0, 0, 5, 28, $html, 0, 1, 0, true, 'C', true);
        }


        else if ($type1 == 'attendence') {

            // Set font
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('L', 'A4');

            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();
            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
                <table style="width:100%;" >
    <tr>
        <th width="80"><strong>Class & Sec</strong></th>
        <th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
        <th width="50"><strong>Exam:</strong></th>
        <th width="330"> <strong> __________________________________________________ </strong> </th>
        <th width="38" align="right"><strong>Year: </strong></th>
        <th width="50" align="right">  <strong>$company->COM_FIN_YEAR</strong></th>
    </tr>
    <tr>
        <td colspan="6"></td>
        
    </tr>
</table>
EOD;
                $html .= <<<EOD
                <table  border="1" cellpadding="2" cellspacing="0">
                    <thead>
                    <tr  nobr="true">
                        <th width="20" align="center"><strong> # </strong></th>
                        <th width="53" align="center"><strong> Regd.No </strong></th>
                        <th width="50" align="center"><strong> Roll No </strong></th>
                        <th  width="170" align="left"><strong> Student Names</strong></th>
                        <th align="center">1</th>
                        <th align="center">2</th>
                        <th align="center">3</th>
                        <th align="center">4</th>
                        <th align="center">5</th>
                        <th align="center">6</th>
                        <th align="center">7</th>
                        <th align="center">8</th>
                        <th align="center">9</th>
                        <th align="center">10</th>
                        <th align="center">11</th>
                        <th align="center">12</th>
                        <th align="center">13</th>
                        <th align="center">14</th>
                        <th align="center">15</th>
                        <th align="center">16</th>
                        <th align="center">17</th>
                        <th align="center">18</th>
                        <th align="center">19</th>
                        <th align="center">20</th>
                        <th align="center">21</th>
                        <th align="center">22</th>
                        <th align="center">23</th>
                        <th align="center">24</th>
                        <th align="center">25</th>
                        <th align="center">26</th>
                        <th align="center">27</th>
                        <th align="center">28</th>
                        <th align="center">29</th>
                        <th align="center">30</th>
                        <th align="center">31</th>
                    </tr>
                    </thead>
                    <tbody>
EOD;
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where_in('STD_SEQ', $st_id_list);
                $this->db->order_by('STD_ROLLNO');
                $this->db->where('STD_LEFT', 0);
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();

                $iter = 0;
                foreach ($st_data as $index => $st_data_val) {
                    $html .= '<tr  nobr="true">
                            <th width="20" align="center"><strong>'. ++$iter .' </strong></th>
                            <td width="53" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="50" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
                            <td width="170"  style="font-size:9px;"> &nbsp;'.$st_data_val['ST_FULL_NAME'].'</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>';
                }
                $html .= <<<EOD
                    </tbody>
                </table>
                <br><br>
                <table >
                    <thead>
                    <tr>
                        <th ></th>
                        <th></th>
                        
                    </tr>
                    <tr>
                        <th width="500" align="left"><strong>Teacher's Name  </strong></th>
                        <th  width="210" align="center"><strong> Signature </strong></th>                        
                    </tr>
                    </thead>
                </table>
               
EOD;
// Print text using writeHTMLCell()
                if (in_array('all', $class_id)) {
                    if(count($class_data) - 1 != $key){
                        $html .= '<br pagebreak="true" />';
                    }
                }
            }

            $pdf->writeHTMLCell(230, 0, 5, $pdf->GetY(), $html, 0, 1, 0, false, 'center', true);
        }


        else if ($type1 == 'st_list') {
            //$pdf->SetAutoPageBreak(TRUE);
            // Set font
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('P', 'A4');

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= <<<EOD
                <table border="1" style="width:100%;">
                    <thead>
                    <tr style="border: 1px solid black;">
                        <th width="20" align="center"><strong> # </strong></th>
                        <th width="53" align="center"><strong> Regd.No </strong></th>
                        <th  width="200" align="left"><strong> Student Names</strong></th>
                        <th  width="80" align="center"><strong> Class - Sec </strong></th>
                        <th width="50" align="center"><strong> Roll No </strong></th>
                        <th width="70"><strong> Religion </strong></th>
                        <th width="230"><strong> Signature </strong></th>
                       
                    </tr>
                    </thead>
                    <tbody>
EOD;
            /*$this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->order_by('STD_ROLLNO');
 $st_data = $this->db->get('student_details')->result_array();*/
            // Set some content to print
            // $this->db->select('class_sec_hdr.*, student_details.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->where('STD_LEFT', 0);
            $this->db->order_by('STD_ROLLNO','STD_CS_SEQ');
            $st_data = $this->db->get('student_details')->result_array();
            $iter = 0;
            foreach ($st_data as $index => $st_data_val) {
                $religion = '';
                $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
                $html .= '<tr style="line-height: 20px;">
                            <td width="20" align="center">'.++$iter.'</td>
                            <td width="53" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="200" align="left">'.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="80" align="center">'.$st_data_val['class_sec'].'</td>
                            <td width="50" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
                            <td width="70">'.$religion.'</td>
                            <td  width="230"></td>
                        </tr>';
            }

            $html .= <<<EOD
                    </tbody>
                </table>
               
EOD;
// Print text using writeHTMLCell()

            $pdf->writeHTMLCell(0, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }


        else if ($type1 == 'mark_sheet_1' and $school_category == 'primary') {
            //$pdf->SetAutoPageBreak(TRUE);
            // Set font
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('L', 'LEGAL');

            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->where('STD_LEFT', 0);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
                <table style="width:100%;">
    <tr>
        <th width="80"><strong>Class & Sec</strong></th>
        <th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
        <th width="40"><strong>Exam:</strong></th>
        <th width="620"align="left"><strong>__________________________________________________ </strong> </th>
        <th width="90" align="right"> <strong>Year: </strong> <strong>$company->COM_FIN_YEAR</strong></th>
    </tr>
    <tr>
        <td colspan="6"></td>
        
    </tr>
</table>
EOD;
                $html .= <<<EOD
                <table  border="1" style="width:100%;">
                    <thead>
                    <tr style="border: 1px solid black;">
                        <th width="20" align="center"><strong> # </strong></th>
                        <th width="53" align="center"><strong> Roll# </strong></th>
                        <th  width="160" align="left"><strong> Student Names</strong></th>
                        <th width="30"></th>
                        <th width="100" colspan="4" align="center">1st Lang Paper I</th>
                        <th width="100" colspan="4" align="center">1st Lang Paper II</th>
                        <th width="100" colspan="4" align="center">Spelling & Dictation</th>
                        <th width="100" colspan="4" align="center">Hindi / Bengali (2nd Language)</th>
                        <th width="100" colspan="4" align="center">Mathematics</th>
                        <th width="100" colspan="4" align="center">Science</th>
                        <th width="100" colspan="4" align="center">History / E.V.S</th>
                        <th width="100" colspan="4" align="center">Geography</th>
                        <th width="40">Total</th>
                        <th>%</th>
                        <th width="30">GD</th>
                        <th></th>
                    </tr>
                    <tr>
                    <td width="20"></td>
                    <td width="53"></td>
                    <td width="160"></td>
                    <td width="30"></td>
                    
                    <td width="25">10</td>
                    <td width="25">40</td>
                    <td width="25">50</td>
                    <td width="25">G</td>
                    
                    <td width="25">10</td>
                    <td width="25">40</td>
                    <td width="25">50</td>
                    <td width="25">G</td>
                    
                    <td width="25">10</td>
                    <td width="25">40</td>
                    <td width="25">50</td>
                    <td width="25">G</td>
                    
                    <td width="25">10</td>
                    <td width="25">40</td>
                    <td width="25">50</td>
                    <td width="25">G</td>
                    
                    <td width="25">10</td>
                    <td width="25">40</td>
                    <td width="25">50</td>
                    <td width="25">G</td>
                    
                    <td width="25">10</td>
                    <td width="25">40</td>
                    <td width="25">50</td>
                    <td width="25">G</td>
                    
                    <td width="25">10</td>
                    <td width="25">40</td>
                    <td width="25">50</td>
                    <td width="25">G</td>
                    
                    <td width="25">10</td>
                    <td width="25">40</td>
                    <td width="25">50</td>
                    <td width="25">G</td>


                    <td width="40"></td>

                    <td></td>

                    <td width="30"></td>

                    </thead>
                    <tbody>
                    
                </tr>
EOD;
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where_in('STD_SEQ', $st_id_list);
                $this->db->order_by('STD_ROLLNO');
                $this->db->where('STD_LEFT', 0);
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                $iter = 0;
                foreach ($st_data as $index => $st_data_val) {
                    $html .= '<tr style="line-height: 20px;" nobr="true">
                            <td width="20" align="center">'.++$iter.'</td>
                            <td width="53" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
                            <td width="160" align="left" style="font-size:9px;"> '.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="30"></td>

                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 1, $sub_seq=26) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 2, $sub_seq=26) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 3, $sub_seq=26) . '</td>
                            <td width="25"></td>

                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 1, $sub_seq=27) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 2, $sub_seq=27) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 3, $sub_seq=27) . '</td>
                            <td width="25"></td>
                            
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 1, $sub_seq=28) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 2, $sub_seq=28) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 3, $sub_seq=28) . '</td>
                            <td width="25"></td>
                            
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 1, $sub_seq=29) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 2, $sub_seq=29) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 3, $sub_seq=29) . '</td>
                            <td width="25"></td>
                            
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 1, $sub_seq=30) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 2, $sub_seq=30) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 3, $sub_seq=30) . '</td>
                            <td width="25"></td>
                            
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 1, $sub_seq=31) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 2, $sub_seq=31) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 3, $sub_seq=31) . '</td>
                            <td width="25"></td>
                            
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 1, $sub_seq=32) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 2, $sub_seq=32) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 3, $sub_seq=32) . '</td>
                            <td width="25"></td>
                            
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 1, $sub_seq=33) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 2, $sub_seq=33) . '</td>
                            <td width="25">'. $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 3, $sub_seq=33) . '</td>
                            <td width="25"></td>

                            <td width="40"></td>

                            <td></td>

                            <td width="30"></td>

                            <td></td>
                        </tr>';


                }

                $html .= <<<EOD
                    </tbody>
                </table>
                <table >
                    <thead>
                    <tr>
                        <th ></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th width="700" align="left"><strong>Teacher's Name  </strong></th>
                        <th  width="210" align="center"><strong> Signature </strong></th>                        
                    </tr>
                    </thead>
                </table>               
EOD;
// Print text using writeHTMLCell()
                if (in_array('all', $class_id)) {
                    if(count($class_data) - 1 != $key){
                        $html .= '<br pagebreak="true" />';
                    }
                }
            }

            $pdf->writeHTMLCell(220, 0, 5, 28, $html, 0, 1, 0, false, 'center', true);
        }

        else if ($type1 == 'mark_sheet_1_blank' and $school_category == 'primary') {
            //$pdf->SetAutoPageBreak(TRUE);
            // Set font
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('L', 'LEGAL');

            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->where('STD_LEFT', 0);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
        <table style="width:100%;">
<tr>
<th width="80"><strong>Class & Sec</strong></th>
<th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
<th width="40"><strong>Exam:</strong></th>
<th width="620"align="left"><strong>__________________________________________________ </strong> </th>
<th width="90" align="right"> <strong>Year: </strong> <strong>$company->COM_FIN_YEAR</strong></th>
</tr>
<tr>
<td colspan="6"></td>

</tr>
</table>
EOD;
                $html .= <<<EOD
        <table  border="1" style="width:100%;">
            <thead>
            <tr style="border: 1px solid black;">
                <th width="20" align="center"><strong> # </strong></th>
                <th width="25" align="center"><strong>Roll#</strong></th>
                <th width="110" align="left"><strong> Student Names</strong></th>
                <th width="88" colspan="4" align="center" >1st Lang Paper I</th>
                <th width="88" colspan="4" align="center" >1st Lang Paper II</th>
                <th width="88" colspan="4" align="center" >Spelling & Dictation</th>
                <th width="88" colspan="4" align="center" >Hindi / Bengali (2nd Language)</th>
                <th width="88" colspan="4" align="center" >Mathematics</th>
                <th width="88" colspan="4" align="center" >Science</th>
                <th width="88" colspan="4" align="center" >History / E.V.S</th>
                <th width="88" colspan="4" align="center" >Geography</th>
                <th width="88" colspan="4" align="center">PT</th>
                <th width="88" colspan="4" align="center">Com</th>
                <th width="88" colspan="4" align="center">MS</th>
                <th width="40" align="center">Total</th>
                <th> %% </th>
                <th width="30">GD</th>
                <th></th>
            </tr>
            <tr>
            <td width="20"></td>
            <td width="25"></td>
            <td width="110"></td>
                        
            <td width="22">10</td>
            <td width="22">40</td>
            <td width="22">50</td>
            <td width="22">G</td>
            
            <td width="22">10</td>
            <td width="22">40</td>
            <td width="22">50</td>
            <td width="22">G</td>
            
            <td width="22">10</td>
            <td width="22">40</td>
            <td width="22">50</td>
            <td width="22">G</td>
            
            <td width="22">10</td>
            <td width="22">40</td>
            <td width="22">50</td>
            <td width="22">G</td>
            
            <td width="22">10</td>
            <td width="22">40</td>
            <td width="22">50</td>
            <td width="22">G</td>
            
            <td width="22">10</td>
            <td width="22">40</td>
            <td width="22">50</td>
            <td width="22">G</td>
            
            <td width="22">10</td>
            <td width="22">40</td>
            <td width="22">50</td>
            <td width="22">G</td>
            
            <td width="22">10</td>
            <td width="22">40</td>
            <td width="22">50</td>
            <td width="22">G</td>

            <td width="22">10</td>
            <td width="22">40</td>
            <td width="22">50</td>
            <td width="22">G</td>
            
            <td width="22">10</td>
            <td width="22">40</td>
            <td width="22">50</td>
            <td width="22">G</td>
            
            <td width="22">10</td>
            <td width="22">40</td>
            <td width="22">50</td>
            <td width="22">G</td>

            <td width="40"></td>
            <td></td>
            <td width="30"></td>
            <td></td>
            </thead>
            <tbody>
            
        </tr>
EOD;
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where_in('STD_SEQ', $st_id_list);
                $this->db->order_by('STD_ROLLNO');
                $this->db->where('STD_LEFT', 0);
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                $iter = 0;
                foreach ($st_data as $index => $st_data_val) {
                    $html .= '<tr style="line-height: 20px;" nobr="true">
                    <td width="20" align="center">'.++$iter.'</td>
                    <td width="25" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
                    <td width="110" align="left" style="font-size:9px;"> '.$st_data_val['ST_FULL_NAME'].'</td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>

                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>

                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>

                    <td width="40"></td>
                    <td></td>
                    <td width="30"></td>
                    <td></td>
                    
                </tr>';


                }

                $html .= <<<EOD
            </tbody>
        </table>
        <table >
            <thead>
            <tr>
                <th ></th>
                <th></th>
            </tr>
            <tr>
                <th width="700" align="left"><strong>Teacher's Name  </strong></th>
                <th  width="210" align="center"><strong> Signature </strong></th>                        
            </tr>
            </thead>
        </table>               
EOD;
// Print text using writeHTMLCell()
                if (in_array('all', $class_id)) {
                    if(count($class_data) - 1 != $key){
                        $html .= '<br pagebreak="true" />';
                    }
                }
            }

            $pdf->writeHTMLCell(220, 0, 5, 28, $html, 0, 1, 0, false, 'center', true);
        }

        else if ($type1 == 'mark_sheet_2' and $school_category == 'primary') {
            //echo"hi";
            // $pdf->SetAutoPageBreak(TRUE);
            // Set font
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('L', 'A3');
            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();
            
             $subjects = $this->db
                ->select('class_sub_link.*,sub_name')
                ->join('subject','sub_id=CS_Sub_id','left')
                ->where('CS_SEQ', $cs_seq)
                ->order_by('Sorting', 'ASC')
                ->get('class_sub_link')->result();

            // echo "<pre>"; print_r($subjects); die();

            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
                <table style="width:100%;">
    <tr>
        <th width="80"><strong>Class & Sec</strong></th>
        <th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
        <th width="40"><strong>Exam:</strong></th>
        <th width="620"align="left"><strong><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$term_name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u> </strong> </th>
        <th width="90" align="right"> <strong>Year: </strong> <strong>$company->COM_FIN_YEAR</strong></th>
    </tr>
    <tr>
        <td colspan="6"></td>
        
    </tr>
</table>
EOD;
                $html .= '
                <table  border="1">
                    <thead>
                    <tr style="border: 1px solid black; width:atuo;">
                        <th width="20" align="center"><strong> # </strong></th>
                        <th width="20" align="center"><strong> Roll# </strong></th>
                        <th width="100" align="left"><strong> Student Name</strong></th>
                       ';
                        if(!empty($subjects)){
                            foreach($subjects as $sub){
                                $html .='<th width="72" colspan="4" align="center" >'.$sub->sub_name.'</th>';
                            }
                        }
                        
                        
                        $html .='
                        <th width="20" align="center">Total</th>
                        <th width="20">%</th>
                        <th width="20" align="center">Avg</th>
                       
                    </tr>
                    <tr>
                    <td width="20"></td>
                    <td width="20"></td>
                    <td width="100"></td>
                   ';
                    if(!empty($subjects)){
                            foreach($subjects as $sub){
                                $html .='
                                <td width="18" align="center">10</td>
                                <td width="18" align="center">90</td>
                                <td width="18" align="center" style="font-weight: bold;">100</td>
                                <td width="18" align="center">G</td>
                                ';
                            }
                        }
                        $html .='
                    
                    
                    
                   
                   

                    <td width="20"></td>

                    <td width="20"></td>

                    <td width="20"></td>

                    </thead>
                    <tbody>
                    
                </tr>
';
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where_in('STD_SEQ', $st_id_list);
                $this->db->where('STD_LEFT', 0);
                $this->db->order_by('STD_ROLLNO');
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                $iter = 0;
                foreach ($st_data as $index => $st_data_val) {
                    $html .= '<tr style="line-height: 20px;" nobr="true">
                    <td width="20" align="center">'.++$iter.'</td>    
                    <td width="20" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
                    <td width="100" align="left" style="font-size:9px;"> '.$st_data_val['ST_FULL_NAME'].'</td>
                    ';
                    if(!empty($subjects)){
                        $total = 0;
                            foreach($subjects as $sub){
                                $res1 = $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 1, $term_seq, $sub->CS_Sub_id);
                                $res2 = $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 4, $term_seq,  $sub->CS_Sub_id);
                                $grade = $this->fetch_grade(($res1+$res2),100,$cs_seq);
                                $total +=($res1 + $res2);
                                $html .='
                                <td width="18" align="center">'. $res1 . '</td>
                                <td width="18" align="center">'. $res2 . '</td>
                                <td width="18" align="center">'. ($res1+$res2) . '</td>
                                <td width="18" align="center">'.$grade.'</td>
                                ';
                            }
                        }

                     $percentage = round(($total/(count($subjects)*100))*100);
                    $average = round($total/count($subjects));

                  $html .='

                    <td width="20" align="center" style="font-weight: bold;">'. $total . '</td>

                    <td width="20" align="center">'. $percentage . '</td>

                    <td width="20" align="center">'. $this->fetch_grade(($average),100,$cs_seq) . '</td>

                   
                        </tr>';
                }

                $html .= <<<EOD
                    </tbody>
                </table>
                <table >
                    <thead>
                    <tr>
                        <th ></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th width="700" align="left"><strong>Teacher's Name  </strong></th>
                        <th  width="210" align="center"><strong> Signature </strong></th>                        
                    </tr>
                    </thead>
                </table>               
EOD;
// Print text using writeHTMLCell()
                if (in_array('all', $class_id)) {
                    if(count($class_data) - 1 != $key){
                        $html .= '<br pagebreak="true" />';
                    }
                }
            }

            $pdf->writeHTMLCell(230, 0, 5, 28, $html, 0, 1, 0, false, 'center', true);
        }

        else if ($type1 == 'mark_sheet_2_blank' and $school_category == 'primary') {
            // $pdf->SetAutoPageBreak(TRUE);
            // Set font
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('L', 'LEGAL');
            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
         <table style="width:100%;">
<tr>
 <th width="80"><strong>Class & Sec</strong></th>
 <th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
 <th width="40"><strong>Exam:</strong></th>
 <th width="620"align="left"><strong>__________________________________________________ </strong> </th>
 <th width="90" align="right"> <strong>Year: </strong> <strong>$company->COM_FIN_YEAR</strong></th>
</tr>
<tr>
 <td colspan="6"></td>
 
</tr>
</table>
EOD;
                $html .= <<<EOD
         <table  border="1">
             <thead>
             <tr style="border: 1px solid black; width:atuo;">
                 <th width="20" align="center"><strong> # </strong></th>
                 <th width="25" align="center"><strong> Roll# </strong></th>
                 <th width="110" align="left"><strong> Student Name</strong></th>
                 <th width="88" colspan="4" align="center" >1st Lang Paper I</th>
                 <th width="88" colspan="4" align="center" >1st Lang Paper II</th>
                 <th width="88" colspan="4" align="center" >Spelling & Dictation</th>
                 <th width="88" colspan="4" align="center" >Hindi / Bengali (2nd Language)</th>
                 <th width="88" colspan="4" align="center" >Mathematics</th>
                 <th width="88" colspan="4" align="center" >Science</th>
                 <th width="88" colspan="4" align="center" >History / E.V.S</th>
                 <th width="88" colspan="4" align="center" >Geography</th>
                 <th width="88" colspan="4" align="center">PT</th>
                 <th width="88" colspan="4" align="center">Com</th>
                 <th width="88" colspan="4" align="center">MS</th>
                 <th width="40" align="center">Total</th>
                 <th>%</th>
                 <th width="30" align="center">GD</th>
                 <th></th>
             </tr>
             <tr>
             <td width="20"></td>
             <td width="25"></td>
             <td width="110"></td>
             
             <td width="22" align="center">10</td>
             <td width="22" align="center">90</td>
             <td width="22" align="center">100</td>
             <td width="22" align="center">G</td>
             
             <td width="22" align="center">10</td>
             <td width="22" align="center">90</td>
             <td width="22" align="center">100</td>
             <td width="22" align="center">G</td>
             
             <td width="22" align="center">10</td>
             <td width="22" align="center">90</td>
             <td width="22" align="center">100</td>
             <td width="22" align="center">G</td>
             
             <td width="22" align="center">10</td>
             <td width="22" align="center">90</td>
             <td width="22" align="center">100</td>
             <td width="22" align="center">G</td>
             
             <td width="22" align="center">10</td>
             <td width="22" align="center">90</td>
             <td width="22" align="center">100</td>
             <td width="22" align="center">G</td>
             
             <td width="22" align="center">10</td>
             <td width="22" align="center">90</td>
             <td width="22" align="center">100</td>
             <td width="22" align="center">G</td>
             
             <td width="22" align="center">10</td>
             <td width="22" align="center">90</td>
             <td width="22" align="center">100</td>
             <td width="22" align="center">G</td>

             <td width="22" align="center">10</td>
             <td width="22" align="center">90</td>
             <td width="22" align="center">100</td>
             <td width="22" align="center">G</td>
             
             <td width="22" align="center">10</td>
             <td width="22" align="center">90</td>
             <td width="22" align="center">100</td>
             <td width="22" align="center">G</td>
             
             <td width="22" align="center">10</td>
             <td width="22" align="center">90</td>
             <td width="22" align="center">100</td>
             <td width="22" align="center">G</td>
             
             <td width="22" align="center">10</td>
             <td width="22" align="center">90</td>
             <td width="22" align="center">100</td>
             <td width="22" align="center">G</td>
            

             <td width="40"></td>

             <td></td>

             <td width="30"></td>

             </thead>
             <tbody>
             
         </tr>
EOD;
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where_in('STD_SEQ', $st_id_list);
                $this->db->where('STD_LEFT', 0);
                $this->db->order_by('STD_ROLLNO');
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                $iter = 0;
                foreach ($st_data as $index => $st_data_val) {
                    $html .= '<tr style="line-height: 20px;" nobr="true">
             <td width="20" align="center">'.++$iter.'</td>    
             <td width="25" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
             <td width="110" align="left" style="font-size:9px;"> '.$st_data_val['ST_FULL_NAME'].'</td>

            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>

            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>

            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>
            <td width="22"></td>

             <td width="40"></td>

             <td></td>

             <td width="30"></td>

             <td></td>
                 </tr>';
                }

                $html .= <<<EOD
             </tbody>
         </table>
         <table >
             <thead>
             <tr>
                 <th ></th>
                 <th></th>
             </tr>
             <tr>
                 <th width="700" align="left"><strong>Teacher's Name  </strong></th>
                 <th  width="210" align="center"><strong> Signature </strong></th>                        
             </tr>
             </thead>
         </table>               
EOD;
// Print text using writeHTMLCell()
                if (in_array('all', $class_id)) {
                    if(count($class_data) - 1 != $key){
                        $html .= '<br pagebreak="true" />';
                    }
                }
            }

            $pdf->writeHTMLCell(230, 0, 5, 28, $html, 0, 1, 0, false, 'center', true);
        }

        else if ($type1 == 'mark_sheet_1' and $school_category == 'secondary') {
            $subjects = $this->db
                ->select('class_sub_link.*,sub_name')
                ->join('subject','sub_id=CS_Sub_id','left')
                ->where('CS_SEQ', $cs_seq)
                ->order_by('Sorting', 'ASC')
                ->get('class_sub_link')->result();
            //$pdf->SetAutoPageBreak(TRUE);
            // Set font
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('L', 'LEGAL');

            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->where('STD_LEFT', 0);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
                <table style="width:100%;">
    <tr>
        <th width="80"><strong>Class & Sec</strong></th>
        <th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
        <th width="40"><strong>Exam:</strong></th>
        <th width="620"align="left"><strong><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$term_name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u> </strong> </th>
        <th width="90" align="right"> <strong>Year: </strong> <strong>$company->COM_FIN_YEAR</strong></th>
    </tr>
    <tr>
        <td colspan="6"></td>
        
    </tr>
</table>
EOD;
                $html .= '
                <table border="1" style="width:100%;">
                    <thead>
                    <tr style="border: 1px solid black;">
                        <th width="20" align="center"><strong> # </strong></th>
                        <th width="33" align="center"><strong> Roll# </strong></th>
                        <th width="150" align="left"><strong> Student Name</strong></th>
                       ';
                foreach($subjects as $sub){
                    $html .='<th width="91" colspan="4" align="center">'.$sub->sub_name.' </th>';
                }
                $html .='
                        <th width="33" style="font-weight:bold">Total</th>
                        <td width="33">%</td>
                        <th width="33">Avg.</th>
                    </tr>
                    <tr>
                    <td width="20"></td>
                    <td width="33"></td>
                    <td width="150"></td>
                   ';
                foreach($subjects as $sub){
                     if($sub->CS_Sub_id == 33 || $sub->CS_Sub_id == 61){
                         $html .='<td style="font-size:9px">30</td>
                                    <td style="font-size:9px">70</td>
                                    <td style="font-size:9px;font-weight:bold">100</td>
                                    <td style="font-size:9px">G</td>';
                     }else{
                         $html .='<td style="font-size:9px">20</td>
                                    <td style="font-size:9px">80</td>
                                    <td style="font-size:9px;font-weight:bold">100</td>
                                    <td style="font-size:9px">G</td>';
                     }
                    
                }


                $html .='
                    
                    <td width="33"></td>
                    <td width="33"></td>
                    <td width="33"></td>

                    </tr>
                    
                    </thead>
                    <tbody>
                    
                
';
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where_in('STD_SEQ', $st_id_list);
                $this->db->order_by('STD_ROLLNO');
                $this->db->where('STD_LEFT', 0);
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                $iter = 0;
                foreach ($st_data as $index => $st_data_val) {
                    $html .= '<tr style="line-height: 20px;" nobr="true">
                            <td width="20" align="center">'.++$iter.'</td>
                            <td width="33" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
                            <td width="150" align="left" style="font-size:9px;"> '.$st_data_val['ST_FULL_NAME'].'</td>
                         ';
                    $total = 0;
                    $subcount = 0;
                    foreach($subjects as $sub){
                        if($sub->CS_Sub_id == 33 || $sub->CS_Sub_id == 61){
                            $res1 = $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 5, $term_seq, $sub->CS_Sub_id);
                            $res2 = $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 6, $term_seq,  $sub->CS_Sub_id); 
                        }else{
                            $res1 = $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 2, $term_seq, $sub->CS_Sub_id);
                            $res2 = $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 3, $term_seq,  $sub->CS_Sub_id);
                        }
                        
                        if (($res1 + $res2) > 0) {
                          if($cs_seq == 26 || $cs_seq == 27 || $cs_seq == 28 || $cs_seq == 29){
                                $grade = $this->fetch_grade_secondary(($res1+$res2),100,$cs_seq); 
                            }else{
                                $grade = $this->fetch_grade(($res1+$res2),100,$cs_seq);
                            }   
                        }else{
                            $grade = "-"; 
                        }
                        
                        
                        $total +=($res1 + $res2);
                         if(($res1 + $res2) > 0){
                            $subcount +=1;
                        }
                        $html .='  <td width="22">'.$res1. '</td>
                                            <td width="22">'.$res2.'</td>
                                            <td width="22" style="font-weight:bold;">'. 
                                    (($res1 + $res2) > 0 ? ($res1 + $res2) : '-') .
                                  '</td>
                                  <td width="25">'.
                                    (!empty($grade) ? $grade : '-') .
                                  '</td>';
                    }


                    $percentage = $total > 0 ? number_format(($total / ($subcount * 100)) * 100, 2) : 0;
                    $average = $total > 0 ? number_format($total / $subcount, 2) : 0;
                    $html .='

                            <td width="33" style="font-weight:bold">'.$total.'</td>

                            <td width="33">'.$percentage.'</td>

                            <td width="33">'.$this->fetch_grade_secondary(($average),100,$cs_seq).'</td>

                        </tr>';



                }

                $html .= <<<EOD
                    </tbody>
                </table>
                <table >
                    <thead>
                    <tr>
                        <th ></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th width="700" align="left"><strong>Teacher's Name  </strong></th>
                        <th  width="210" align="center"><strong> Signature </strong></th>                        
                    </tr>
                    </thead>
                </table>               
EOD;
// Print text using writeHTMLCell()
                if (in_array('all', $class_id)) {
                    if(count($class_data) - 1 != $key){
                        $html .= '<br pagebreak="true" />';
                    }
                }
            }

            $pdf->writeHTMLCell(220, 0, 5, 28, $html, 0, 1, 0, false, 'center', true);
        }

        else if ($type1 == 'mark_sheet_1_blank' and $school_category == 'secondary') {
            //$pdf->SetAutoPageBreak(TRUE);
            // Set font
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('L', 'LEGAL');

            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->where('STD_LEFT', 0);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
        <table style="width:100%;">
<tr>
<th width="80"><strong>Class & Sec</strong></th>
<th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
<th width="40"><strong>Exam:</strong></th>
<th width="620"align="left"><strong><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$term_name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u> </strong> </th>
<th width="90" align="right"> <strong>Year: </strong> <strong>$company->COM_FIN_YEAR</strong></th>
</tr>
<tr>
<td colspan="6"></td>

</tr>
</table>
EOD;
                $html .= <<<EOD
        <table border="1" style="width:100%;">
            <thead>
            <tr style="border: 1px solid black;">
                <th width="20" align="center"><strong> # </strong></th>
                <th width="33" align="center"><strong> Roll# </strong></th>
                <th width="150" align="left"><strong> Student Name</strong></th>
                <th width="15"></th>
                <th width="88" colspan="4" align="center">1st Language Paper </th>
                <th width="88" colspan="4" align="center">2nd Language H/Ben</th>
                <th width="88" colspan="4" align="center">Mathematics</th>
                <th width="88" colspan="4" align="center">Life Science</th>
                <th width="88" colspan="4" align="center">Physical Science</th>
                <th width="88" colspan="4" align="center">History</th>
                <th width="88" colspan="4" align="center">Geography</th>
                <th width="88" colspan="4" align="center">PT</th>
                <th width="88" colspan="4" align="center">Com</th>
                <th width="88" colspan="4" align="center">MS</th>
                <th width="40">Total</th>
                <td width="20">%</td>
                <th width="33">Avg.</th>
            </tr>
            <tr>
            <td width="20"></td>
            <td width="33"></td>
            <td width="150"></td>
            <td width="15"></td>
            
            <td style="font-size:9px">20</td>
            <td style="font-size:9px">80</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>
            
            <td style="font-size:9px">20</td>
            <td style="font-size:9px">80</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>
            
            <td style="font-size:9px">20</td>
            <td style="font-size:9px">80</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>
            
            <td style="font-size:9px">20</td>
            <td style="font-size:9px">80</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>
            
            <td style="font-size:9px">20</td>
            <td style="font-size:9px">80</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>
            
            <td style="font-size:9px">20</td>
            <td style="font-size:9px">80</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>
            
            <td style="font-size:9px">20</td>
            <td style="font-size:9px">80</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>
            
            <td style="font-size:9px">20</td>
            <td style="font-size:9px">80</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>
            
            <td style="font-size:9px">20</td>
            <td style="font-size:9px">80</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>
            
            <td style="font-size:9px">20</td>
            <td style="font-size:9px">80</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>
            
            <td width="40"></td>

            <td width="20"></td>

            </tr>
            
            </thead>
            <tbody>
            
        
EOD;
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where_in('STD_SEQ', $st_id_list);
                $this->db->order_by('STD_ROLLNO');
                $this->db->where('STD_LEFT', 0);
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                $iter = 0;
                foreach ($st_data as $index => $st_data_val) {
                    $html .= '<tr style="line-height: 20px;" nobr="true">
                    <td width="20" align="center">'.++$iter.'</td>
                    <td width="33" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
                    <td width="150" align="left" style="font-size:9px;"> '.$st_data_val['ST_FULL_NAME'].'</td>
                    <td width="15"></td>

                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>

                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>

                    <td width="40"></td>

                    <td width="20"></td>

                    <td width="33"></td>

                </tr>';


                }

                $html .= <<<EOD
            </tbody>
        </table>
        <table >
            <thead>
            <tr>
                <th ></th>
                <th></th>
            </tr>
            <tr>
                <th width="700" align="left"><strong>Teacher's Name  </strong></th>
                <th  width="210" align="center"><strong> Signature </strong></th>                        
            </tr>
            </thead>
        </table>               
EOD;
// Print text using writeHTMLCell()
                if (in_array('all', $class_id)) {
                    if(count($class_data) - 1 != $key){
                        $html .= '<br pagebreak="true" />';
                    }
                }
            }

            $pdf->writeHTMLCell(220, 0, 5, 28, $html, 0, 1, 0, false, 'center', true);
        }

        else if ($type1 == 'mark_sheet_2' and $school_category == 'secondary') {
            $subjects = $this->db
                ->select('class_sub_link.*,sub_name')
                ->join('subject','sub_id=CS_Sub_id','left')
                ->where('CS_SEQ', $cs_seq)
                ->order_by('Sorting', 'ASC')
                ->get('class_sub_link')->result();
            //$pdf->SetAutoPageBreak(TRUE);
            // Set font
            $pdf->SetFont('times', '', 7, '', true);
             $pdf->AddPage('L', array(410, 250));
            //$pdf->AddPage('L', 'LEGAL');

            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->where('STD_LEFT', 0);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
                <table style="width:100%;">
    <tr>
        <th width="80"><strong>Class & Sec</strong></th>
        <th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
        <th width="40"><strong>Exam:</strong></th>
        <th width="620"align="left"><strong><u>&nbsp;&nbsp;&nbsp;$term_name&nbsp;&nbsp;&nbsp;</u> </strong> </th>
        <th width="90" align="right"> <strong>Year: </strong> <strong>$company->COM_FIN_YEAR</strong></th>
    </tr>
    <tr>
        <td colspan="6"></td>
        
    </tr>
</table>
EOD;
                $html .= '
                <table  border="1" style="width:100%;">
                    <thead>
                    <tr style="border: 1px solid black;">
                        <th width="20" align="center"><strong> # </strong></th>
                        <th width="33" align="center"><strong> Roll# </strong></th>
                        <th width="150" align="left"><strong> Student Names</strong></th>
                        ';
                foreach($subjects as $sub){
                    $html .='<th width="91" colspan="4" align="center">'.$sub->sub_name.' </th>';
                }

                $html .='
                        <th width="33">Total</th>
                        <td width="33">%</td>
                        <th width="33">Avg.</th>
                    </tr>
                    <tr>
                    <td width="53"></td>
                    <td width="150"></td>
                    ';
                foreach($subjects as $sub){
                    $html .=' <td width="22" style="font-size:9px">10</td>
                    <td width="22" style="font-size:9px">90</td>
                    <td width="22" style="font-size:9px;font-weight:bold;">100</td>
                    <td width="25" style="font-size:9px">G</td>';
                }



                $html .='

                    <td width="33"></td>

                    <td width="33"></td>
                    <td width="33"></td>

                    </tr>
                    
                    </thead>
                    <tbody>
                    
                
';
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where_in('STD_SEQ', $st_id_list);
                $this->db->order_by('STD_ROLLNO');
                $this->db->where('STD_LEFT', 0);
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                $iter = 0;
                foreach ($st_data as $index => $st_data_val) {
                    $html .= '<tr style="line-height: 20px;" nobr="true">
                            <td width="20" align="center">'.++$iter.'</td>
                            <td width="33" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
                            <td width="150" align="left" style="font-size:9px;"> '.$st_data_val['ST_FULL_NAME'].'</td>
                           ';
                    $total = 0;
                    $subcount = 0;
                    foreach($subjects as $sub){
                        $res1 = $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 1, $term_seq, $sub->CS_Sub_id);
                        $res2 = $this->fetch_marks($st_data_val['STD_SEQ'], $st_data_val['STD_CS_SEQ'], $test_seq = 4, $term_seq,  $sub->CS_Sub_id);
                        if (($res1 + $res2) > 0) {
                            if ($cs_seq == 26 || $cs_seq == 27 || $cs_seq == 28 || $cs_seq == 29) {
                                $grade = $this->fetch_grade_secondary(($res1 + $res2), 100, $cs_seq);
                            } else {
                                $grade = $this->fetch_grade(($res1 + $res2), 100, $cs_seq);
                            }
                        } else {
                            $grade = '-';
                        }
                        $total +=($res1 + $res2);
                        if(($res1 + $res2) > 0){
                            $subcount +=1;
                        }
                        $html .= '<td width="22">'.$res1.'</td>
                                  <td width="22">'.$res2.'</td>
                                  <td width="22" style="font-weight:bold;">'. 
                                    (($res1 + $res2) > 0 ? ($res1 + $res2) : '-') .
                                  '</td>
                                  <td width="25">'.
                                    (!empty($grade) ? $grade : '-') .
                                  '</td>';
                    }


                    $percentage = $total > 0 ? number_format(($total / ($subcount * 100)) * 100, 2) : 0;
                    $average = $total > 0 ? number_format($total / $subcount, 2) : 0;
                    $html .='

                            <td width="33" style="font-weight:bold;">'.$total.'</td>

                            <td width="33">'.$percentage.'</td>

                            <td width="33">'.$this->fetch_grade_secondary(($average),100,$cs_seq).'</td>

                        </tr>';


                }

                $html .= <<<EOD
                    </tbody>
                </table>
                <table >
                    <thead>
                    <tr>
                        <th ></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th width="700" align="left"><strong>Teacher's Name  </strong></th>
                        <th  width="210" align="center"><strong> Signature </strong></th>                        
                    </tr>
                    </thead>
                </table>               
EOD;
// Print text using writeHTMLCell()
                if (in_array('all', $class_id)) {
                    if(count($class_data) - 1 != $key){
                        $html .= '<br pagebreak="true" />';
                    }
                }
            }

            $pdf->writeHTMLCell(220, 0, 5, 28, $html, 0, 1, 0, false, 'center', true);
        }

        else if ($type1 == 'mark_sheet_2_blank' and $school_category == 'secondary') {
            //$pdf->SetAutoPageBreak(TRUE);
            // Set font
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('L', 'LEGAL');

            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->where('STD_LEFT', 0);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
        <table style="width:100%;">
<tr>
<th width="80"><strong>Class & Sec</strong></th>
<th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
<th width="40"><strong>Exam:</strong></th>
<th width="620"align="left"><strong><u>&nbsp;&nbsp;&nbsp;$term_name&nbsp;&nbsp;&nbsp;</u> </strong> </th>
<th width="90" align="right"> <strong>Year: </strong> <strong>$company->COM_FIN_YEAR</strong></th>
</tr>
<tr>
<td colspan="6"></td>

</tr>
</table>
EOD;
                $html .= <<<EOD
        <table  border="1" style="width:100%;">
            <thead>
            <tr style="border: 1px solid black;">
                <th width="20" align="center"><strong> # </strong></th>
                <th width="33" align="center"><strong> Roll# </strong></th>
                <th width="150" align="left"><strong> Student Names</strong></th>
                <th width="15"></th>
                <th width="88" colspan="4" align="center">1st Language Paper </th>
                <th width="88" colspan="4" align="center">2nd Language H/Ben</th>
                <th width="88" colspan="4" align="center">Mathematics</th>
                <th width="88" colspan="4" align="center">Life Science</th>
                <th width="88" colspan="4" align="center">Physical Science</th>
                <th width="88" colspan="4" align="center">History</th>
                <th width="88" colspan="4" align="center">Geography</th>
                <th width="88" colspan="4" align="center">PT</th>
                <th width="88" colspan="4" align="center">Com</th>
                <th width="88" colspan="4" align="center">MS</th>
                <th width="40">Total</th>
                <td width="20">%</td>
                <th width="33">Avg.</th>
            </tr>
            <tr>
            <td width="53"></td>
            <td width="150"></td>
            <td width="15"></td>
            
            <td width="22" style="font-size:9px">10</td>
            <td width="22" style="font-size:9px">90</td>
            <td width="22" style="font-size:9px">100</td>
            <td width="22" style="font-size:9px">G</td>

            <td style="font-size:9px">10</td>
            <td style="font-size:9px">90</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>

            <td style="font-size:9px">10</td>
            <td style="font-size:9px">90</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>

            <td style="font-size:9px">10</td>
            <td style="font-size:9px">90</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>

            <td style="font-size:9px">10</td>
            <td style="font-size:9px">90</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>

            <td style="font-size:9px">10</td>
            <td style="font-size:9px">90</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>

            <td style="font-size:9px">10</td>
            <td style="font-size:9px">90</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>

            <td style="font-size:9px">10</td>
            <td style="font-size:9px">90</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>
            
            <td style="font-size:9px">10</td>
            <td style="font-size:9px">90</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>
            
            <td style="font-size:9px">10</td>
            <td style="font-size:9px">90</td>
            <td style="font-size:9px">100</td>
            <td style="font-size:9px">G</td>

            <td width="40"></td>

            <td width="20"></td>

            </tr>
            
            </thead>
            <tbody>
            
        
EOD;
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where_in('STD_SEQ', $st_id_list);
                $this->db->order_by('STD_ROLLNO');
                $this->db->where('STD_LEFT', 0);
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                $iter = 0;
                foreach ($st_data as $index => $st_data_val) {
                    $html .= '<tr style="line-height: 20px;" nobr="true">
                    <td width="20" align="center">'.++$iter.'</td>
                    <td width="33" align="center">'.$st_data_val['STD_ROLLNO'].'</td>
                    <td width="150" align="left" style="font-size:9px;"> '.$st_data_val['ST_FULL_NAME'].'</td>
                    <td width="15"></td>

                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>

                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>
                    <td width="22"></td>

                    <td width="40"></td>

                    <td width="20"></td>

                    <td width="33"></td>

                </tr>';


                }

                $html .= <<<EOD
            </tbody>
        </table>
        <table >
            <thead>
            <tr>
                <th ></th>
                <th></th>
            </tr>
            <tr>
                <th width="700" align="left"><strong>Teacher's Name  </strong></th>
                <th  width="210" align="center"><strong> Signature </strong></th>                        
            </tr>
            </thead>
        </table>               
EOD;
// Print text using writeHTMLCell()
                if (in_array('all', $class_id)) {
                    if(count($class_data) - 1 != $key){
                        $html .= '<br pagebreak="true" />';
                    }
                }
            }

            $pdf->writeHTMLCell(220, 0, 5, 28, $html, 0, 1, 0, false, 'center', true);
        }


        // Close and output PDF document
        $pdf->Output($doc_name . '.pdf', 'I');


    }

    function fetch_marks($std_seq, $cs_seq, $test_seq, $term_seq, $sub_seq) {
        $CI = & get_instance();
        $rv = $CI->db
            ->get_where('marks_dtl',
                array(
                    'MD_CLASS_SEQ' => $cs_seq,
                    'MD_TEST_SEQ' => $test_seq,
                    'MD_SUB_SEQ' => $sub_seq,
                    'MD_TERM_SEQ' => $term_seq,
                    'MD_STD_SEQ' => $std_seq)
            )->result();
        if(count($rv) == 0){
            return '-';
        } else {
            if(($rv[0]->MD_MARKS == NULL or $rv[0]->MD_MARKS == '') and ($rv[0]->MD_GRADE == NULL or $rv[0]->MD_GRADE == '')){
                return 'Ab';
            }else{

                $mt = $CI->db->get_where('subject', array('sub_id' => $sub_seq))->row()->marks_type;
                if($mt == 'Grade'){

                    $rval=$rv[0]->MD_GRADE;

                }else{

                    if($rv[0]->MD_MARKS < 10){
                        $rval='0'.$rv[0]->MD_MARKS;
                    }else{
                        $rval=$rv[0]->MD_MARKS;
                    }
                }

                return $rval;
            }
        }
    }
    // function fetch_grade($marks_obtained, $total_marks,$class){
    //     if($class == 'IX' || $class == 'X'){
    //         if($marks_obtained >=90 && $marks_obtained<=100){
    //             return "AA";
    //         }else if($marks_obtained >=80 && $marks_obtained<=89){
    //             return "A+";
    //         }else if($marks_obtained >=60 && $marks_obtained<=79){
    //             return "A";
    //         }else if($marks_obtained >=45 && $marks_obtained<=59){
    //             return "B+";
    //         }else if($marks_obtained >=35 && $marks_obtained<=44){
    //             return "B";
    //         }else if($marks_obtained >=25 && $marks_obtained<=34){
    //             return "C";
    //         }else{
    //             return "D";
    //         }
    //     }else{
    //         $CI = & get_instance();
    //         $query = "SELECT * FROM `grades` WHERE `marks_from` >= ".$marks_obtained." AND `marks_to` <=".$marks_obtained;
    //         $grade = $CI->db->query($query)->row()->grade;

    //         return $grade;
    //     }

    // }
    function fetch_grade($marks_obtained, $total_marks,$class){
        if($class == 22 || $class == 23 || $class == 24 || $class == 25){
            if($marks_obtained >=90 && $marks_obtained<=100){
               return "AA";  
            }else if($marks_obtained >=80 && $marks_obtained<=89){
               return "A+";  
            }else if($marks_obtained >=60 && $marks_obtained<=79){
               return "A";  
            }else if($marks_obtained >=45 && $marks_obtained<=59){
               return "B+";  
            }else if($marks_obtained >=35 && $marks_obtained<=44){
               return "B";  
            }else if($marks_obtained >=25 && $marks_obtained<=34){
               return "C";  
            }else{
                return "D";
            }
        }else{
          $CI = & get_instance();
            $query = "SELECT * FROM `grades` WHERE `marks_from` >= ".$marks_obtained." AND `marks_to` <=".$marks_obtained;
            $grade = $CI->db->query($query)->row()->grade;
        
            return $grade;  
        }
        
    }
    function fetch_grade_secondary($marks_obtained, $total_marks){

    // if($total_marks != 50){
    //     $marks_obtained = $marks_obtained * 2;
    // }

    if($marks_obtained >= 90){
        $grade = 'AA';
    }else if($marks_obtained >= 75 and $marks_obtained < 90){
        $grade = 'A+';
    }else if($marks_obtained >= 60 and $marks_obtained < 75){
        $grade = 'A';
    }else if($marks_obtained >= 50 and $marks_obtained < 60){
        $grade = 'B+';
    }else if($marks_obtained >= 40 and $marks_obtained < 50){
        $grade = 'B';
    }else if($marks_obtained >= 30 and $marks_obtained < 40){
        $grade = 'C';
    }else if($marks_obtained >= 0 and $marks_obtained < 30){
        $grade = 'D';
    }else{
        $grade = '<label style="color:red">!!!</label>';
    }

    return $grade;
}

    public function print_student_category_list_report() {
        
    //       error_reporting(E_ALL);
    // ini_set('display_errors', 1);

        // echo "<pre>"; print_r($this->input->post()); die();
        $type1 = '';
        $classid =  $this->input->post('category_class');
        $category_list = $this->input->post('category_list');
        $mobile_check = $this->input->post('mobile_check');
        $multi_religion_list = $this->input->post('multi_religion_list');
        if (empty($category_list)) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'Something went wrong');
            return array('type' => 'redirect', 'page'=>'admin/student_list');
        }


        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // $this->db->select('student_details.*');

        $report_type_hdr = '';
        switch ($category_list) {
            case "compact":
                $report_type_hdr = 'Compact Report';
                break;
            case "catholic":
                $report_type_hdr = 'Catholic Report';
                break;
            case "non_catholic":
                $report_type_hdr = 'Non Catholic Report';
                break;
            case "religion":
                $report_type_hdr = ' Religion Report';
                break;
            default:
                $report_type_hdr = 'Report';
        }

        $class = array('all');
        $company = $this->company_name($class);

        // die('ok');
        // set document information
        $doc_name = $report_type_hdr.'_'.date('d-m-Y_h-i-A');
        $pdf->SetCreator(PDF_CREATOR);

        $pdf->SetTitle($doc_name);
        $pdf->SetSubject('Student Strength Report');
        $pdf->SetKeywords('All Transaction Report, smg, developed by: https://sketchmeglobal.com');


        $pdf->SetAuthor($company->COM_NAME);

        // set default header data
        $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 25px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD1, $company->COM_ADD2
        <br>
        <strong><span style="background-color: black;color: white;"> $report_type_hdr </span></strong>
        <br>
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

        $pdf->setPrintFooter(false);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // -----------------------------------------------------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        if ($category_list == 'compact') {
            //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('P', 'A4');

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= '
                <table border="1">
                    <thead>
                    <tr>
                        <th width="25" align="center"><strong> SNo </strong></th>
                        <th  width="200" align="center"><strong> Student Names</strong></th>
                        <th width="53" align="center"><strong> Regd.No </strong></th>
                        <th  width="90" align="center"><strong> Class - Sec </strong></th>
                        <th width="40" align="center"><strong> Roll No </strong></th>';
                      
                        if($mobile_check == 'on'){
                            $html .= ' <th width="90" align="center"><strong> Mobile No </strong></th>';
                        }
                       
                         $html .= '
                        <th width="60"><strong> Religion </strong></th>
                        <th width="240"><strong> Signature </strong></th>
                       
                    </tr>
                    </thead>
                    <tbody>
';

            // Set some content to print
            // $this->db->select('class_sec_hdr.*, student_details.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            // $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->order_by('STD_CS_SEQ, STD_ROLLNO asc');
            $this->db->where_in('student_details.STD_CS_SEQ',$classid);
            // $this->db->limit(100);
            $st_data = $this->db->get('student_details')->result_array();
           
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            foreach ($st_data as $index => $st_data_val) {
                $religion = '';
                $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
                $html .= '<tr>
                            <td width="25" align="center">'.++$index.'</td>
                            <td width="200" align="left"> '.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="53" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="90" align="center">'.$st_data_val['class_sec'].'</td>
                            <td width="40" align="center">'.$st_data_val['STD_ROLLNO'].'</td>';
                             if($mobile_check == 'on'){
                                $html .= ' <th width="90" align="center">'.$st_data_val['STD_PH_NO'].' </th>';
                            }
                            $html .= '
                            <td width="60" align="center">'.$religion.'</td>
                            <td  width="240"></td>
                        </tr>';
            }

            $html .= <<<EOD
                    </tbody>
                </table>
               
EOD;
// Print text using writeHTMLCell()

            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }

        else if ($category_list == 'catholic') {

            // Set font
            //$pdf->SetAutoPageBreak(TRUE, 4);
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('P', 'A4');
            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where('student_details.STD_RC', 1);
            $this->db->where_in('student_details.STD_CS_SEQ',$classid);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();

            if (empty($class_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
                <table style="width:100%;">
                <tr>
        <th width="80"><strong>Class & Sec</strong></th>
        <th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
    </tr>
     <tr>
        <td colspan="2"></td>
        
    </tr>
</table>
EOD;

                $html .= '
                <table  border="1">
                    <thead>
                    <tr style="border: 1px solid black;">
                        <th width="53" align="center"><strong> SNo </strong></th>
                        <th  width="200" align="left"><strong> Student </strong></th>
                        <th width="53" align="center"><strong> Regd.No </strong></th>
                        <th width="50" align="center"><strong> Roll No </strong></th>';
                        if($mobile_check == 'on'){
                            $html .= ' <th width="90" align="center"><strong> Mobile No </strong></th>';
                        }
                       
                         $html .= '
                        <th align="center">Religion</th>
                       
                    </tr>
                    </thead>
                    <tbody>
';
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where('STD_RC', 1);
                $this->db->order_by('STD_ROLLNO');
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                foreach ($st_data as $index => $st_data_val) {
                    $religion = '';
                    $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                    if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
                    $html .= '<tr>
                            <td width="53" align="center">'.++$index.'</td>
                            <td width="200" align="left">'.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="53" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="50" align="center">'.$st_data_val['STD_ROLLNO'].'</td>';
                             if($mobile_check == 'on'){
                                $html .= ' <th width="90" align="center">'.$st_data_val['STD_PH_NO'].' </th>';
                            }
                            $html .= '
                            <td align="center">'.$religion.'</td>
                        </tr>';
                }

                $html .= <<<EOD
                    </tbody>
                </table>
                <table >
                    <thead>
                     <tr>
                        <th ></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th width="500" align="left"><strong>Teacher's Name  </strong></th>
                        <th  width="210" align="center"><strong> Signature </strong></th>                        
                    </tr>
                    </thead>
                </table>               
EOD;
// Print text using writeHTMLCell()


            }

            $pdf->writeHTMLCell(0, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }

        else if ($category_list == 'non_catholic') {

            //$pdf->SetAutoPageBreak(TRUE);
            // Set font
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('P', 'A4');
            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->group_by('STD_CS_SEQ');
            $this->db->where('student_details.STD_RC', 0);
            $this->db->where_in('student_details.STD_CS_SEQ',$classid);
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();

            if (empty($class_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
                <table style="width:100%;">
                <tr>
        <th width="80"><strong>Class & Sec</strong></th>
        <th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
    </tr>
    <tr>
        <td colspan="2"></td>
        
    </tr>
</table>
EOD;

                $html .= '
                <table  border="1">
                    <thead>
                        <tr style="border: 1px solid black;">
                            <th width="25" align="center"><strong> SNo </strong></th>
                            <th  width="300" align="left"><strong> Student </strong></th>
                            <th width="53" align="center"><strong> Regd.No </strong></th>
                            <th width="50" align="center"><strong> Roll No </strong></th>';
                            if($mobile_check == 'on'){
                                $html .= ' <th width="90" align="center"><strong> Mobile No </strong></th>';
                            }
                       
                         $html .= '
                            <th align="center">Religion</th>
                        </tr>
                    </thead>
                    <tbody>
';
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where('STD_RC', 0);
                $this->db->order_by('STD_ROLLNO');
                // $this->db->limit(20);
                //die();
                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                foreach ($st_data as $index => $st_data_val) {
                    $religion = '';
                    $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                    if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
                    $html .= '<tr>
                            <td width="25" align="center">'.++$index.'</td>
                            <td width="300" align="left"> '.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="53" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="50" align="center">'.$st_data_val['STD_ROLLNO'].'</td>';
                             if($mobile_check == 'on'){
                                $html .= ' <th width="90" align="center">'.$st_data_val['STD_PH_NO'].' </th>';
                            }
                            $html .= '
                            <td align="center">'.$religion.'</td>
                        </tr>';
                }

                $html .= <<<EOD
                    </tbody>
                </table>
                <table >
                    <thead>
                     <tr>
                        <th ></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th width="500" align="left"><strong>Teacher's Name  </strong></th>
                        <th  width="210" align="center"><strong> Signature </strong></th>                        
                    </tr>
                    </thead>
                </table>
EOD;
// Print text using writeHTMLCell()

                /*if(count($class_data) - 1 != $key){
                    $html .= '<br pagebreak="true" />';
                }*/

            }

            $pdf->writeHTMLCell(0, 0, 5, 28, $html, 0, 1, 0, true, 'center', true);
        }


        else if ($category_list == 'religion') {
            // TEMP DEBUG
            // echo "multi_religion_list value: [" . $multi_religion_list . "]";
            // echo " | Type: " . gettype($multi_religion_list);
            // echo " | Comparison result: " . ($multi_religion_list == 345 ? "TRUE" : "FALSE");
            //die();
            
            if($multi_religion_list == 345){
    $pdf->SetFont('times', '', 9, '', true);
    $pdf->AddPage('P', 'A4');

    $this->db->select("
        class_sec_hdr.Class_Name,
        class_sec_hdr.Sec_Name,
        SUM(CASE WHEN student_details.STD_RLGN = 3 THEN 1 ELSE 0 END) as christian,
        SUM(CASE WHEN student_details.STD_RLGN = 2 THEN 1 ELSE 0 END) as muslim,
        SUM(CASE WHEN student_details.STD_RLGN NOT IN (2,3) THEN 1 ELSE 0 END) as others,
        COUNT(student_details.STD_SEQ) as total
    ");
    $this->db->from('student_details');
    $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ');
    $this->db->where_in('student_details.STD_CS_SEQ', $classid);
    $this->db->where('student_details.STD_STATUS', 0);
    $this->db->where('student_details.STD_PROMOTED', 1);
    $this->db->where('student_details.STD_LEFT', 0);
    $this->db->group_by('student_details.STD_CS_SEQ');
    $report_data = $this->db->get()->result();

    if (empty($report_data)) {
        $this->session->set_flashdata('type', 'error');
        $this->session->set_flashdata('title', 'Naa!');
        $this->session->set_flashdata('msg', 'Something went wrong');
        return array('type' => 'redirect', 'page'=>'admin/student_list');
    }

    // -------------------------------------------------------
    // GROUP & SORT LOGIC
    // -------------------------------------------------------
    // Group 1 = Pre-Primary (PRE-NUR, NUR, KG)
    // Group 2 = Primary     (I, II, III, IV)
    // Group 3 = Secondary   (V – XII)
    $class_group_map = [
        'PRE-NUR' => ['group' => 1, 'order' => 1],
        'NUR'     => ['group' => 1, 'order' => 2],
        'KG'      => ['group' => 1, 'order' => 3],
        'I'       => ['group' => 2, 'order' => 1],
        'II'      => ['group' => 2, 'order' => 2],
        'III'     => ['group' => 2, 'order' => 3],
        'IV'      => ['group' => 2, 'order' => 4],
        'V'       => ['group' => 3, 'order' => 1],
        'VI'      => ['group' => 3, 'order' => 2],
        'VII'     => ['group' => 3, 'order' => 3],
        'VIII'    => ['group' => 3, 'order' => 4],
        'IX'      => ['group' => 3, 'order' => 5],
        'X'       => ['group' => 3, 'order' => 6],
        'XI'      => ['group' => 3, 'order' => 7],
        'XII'     => ['group' => 3, 'order' => 8],
    ];

    $group_labels = [
        1 => 'PRE-NUR to KG',
        2 => 'Class I to IV',
        3 => 'Class V to XII',
    ];

    // Helper: get group info for a class name
    $getClassInfo = function($class_name) use ($class_group_map) {
        $key = strtoupper(trim($class_name));
        return isset($class_group_map[$key])
            ? $class_group_map[$key]
            : ['group' => 99, 'order' => 99];
    };

    // Sort report_data by group → then by class order
    usort($report_data, function($a, $b) use ($getClassInfo) {
        $infoA = $getClassInfo($a->Class_Name);
        $infoB = $getClassInfo($b->Class_Name);
        if ($infoA['group'] !== $infoB['group']) {
            return $infoA['group'] - $infoB['group'];
        }
        if ($infoA['order'] !== $infoB['order']) {
            return $infoA['order'] - $infoB['order'];
        }
        // Same class, different sections – sort alphabetically by section
        return strcmp($a->Sec_Name, $b->Sec_Name);
    });

    // -------------------------------------------------------
    // BUILD HTML TABLE
    // -------------------------------------------------------
    $html = '
    <table border="1" cellpadding="4">
    <thead>
    <tr>
        <th align="center"><b>SNo</b></th>
        <th colspan="3" align="center"><b>Class</b></th>
        <th align="center"><b>Section</b></th>
        <th align="center"><b>Christian</b></th>
        <th align="center"><b>Muslim</b></th>
        <th align="center"><b>Others</b></th>
        <th align="center"><b>Total</b></th>
    </tr>
    </thead>
    <tbody>';

    $grand_christian = 0;
    $grand_muslim    = 0;
    $grand_others    = 0;
    $grand_total     = 0;

    $current_group      = 0;
    $group_christian    = 0;
    $group_muslim       = 0;
    $group_others       = 0;
    $group_total        = 0;
    $sno                = 0;   // serial number resets each group

    foreach ($report_data as $row) {

        $info      = $getClassInfo($row->Class_Name);
        $row_group = $info['group'];

        // ---- New group detected: close previous group subtotal ----
        if ($current_group !== 0 && $row_group !== $current_group) {
            $html .= '
            <tr>
                <td colspan="5" align="right"><b>Total (' . $group_labels[$current_group] . ')</b></td>
                <td align="center"><b>' . $group_christian . '</b></td>
                <td align="center"><b>' . $group_muslim    . '</b></td>
                <td align="center"><b>' . $group_others    . '</b></td>
                <td align="center"><b>' . $group_total     . '</b></td>
            </tr>';

            // Reset group counters & serial
            $group_christian = 0;
            $group_muslim    = 0;
            $group_others    = 0;
            $group_total     = 0;
            $sno             = 0;
        }

        $current_group = $row_group;
        $sno++;

        $html .= '
        <tr>
            <td align="center">' . $sno . '</td>
            <td colspan="3" align="center">' . $row->Class_Name . '</td>
            <td align="center">' . $row->Sec_Name  . '</td>
            <td align="center">' . $row->christian . '</td>
            <td align="center">' . $row->muslim    . '</td>
            <td align="center">' . $row->others    . '</td>
            <td align="center">' . $row->total     . '</td>
        </tr>';

        // Accumulate group totals
        $group_christian += $row->christian;
        $group_muslim    += $row->muslim;
        $group_others    += $row->others;
        $group_total     += $row->total;

        // Accumulate grand totals
        $grand_christian += $row->christian;
        $grand_muslim    += $row->muslim;
        $grand_others    += $row->others;
        $grand_total     += $row->total;
    }

    // ---- Close the LAST group subtotal ----
    if ($current_group !== 0) {
        $html .= '
        <tr>
            <td colspan="5" align="right"><b>Sub Total (' . $group_labels[$current_group] . ')</b></td>
            <td align="center"><b>' . $group_christian . '</b></td>
            <td align="center"><b>' . $group_muslim    . '</b></td>
            <td align="center"><b>' . $group_others    . '</b></td>
            <td align="center"><b>' . $group_total     . '</b></td>
        </tr>';
    }

    // ---- Grand Total row ----
    $html .= '
    <tr>
        <td colspan="5" align="right"><b>Grand Total</b></td>
        <td align="center"><b>' . $grand_christian . '</b></td>
        <td align="center"><b>' . $grand_muslim    . '</b></td>
        <td align="center"><b>' . $grand_others    . '</b></td>
        <td align="center"><b>' . $grand_total     . '</b></td>
    </tr>
    </tbody></table>';

    $pdf->writeHTMLCell(0, 0, 5, 28, $html, 0, 1, 0, true, '', true);

}else{

            //$pdf->SetAutoPageBreak(TRUE, 3);
            // Set font
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('P', 'A4');
            // Set some content to print
            $this->db->select('class_sec_hdr.*, student_details.STD_RLGN');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where('student_details.STD_RLGN', $multi_religion_list);
            $this->db->where_in('student_details.STD_CS_SEQ',$classid);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();

            if (empty($class_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            $html = '';
            foreach ($class_data as $key => $class_data_val) {
                $html .= <<<EOD
                <table style="width:100%;">
                <tr>
        <th width="80"><strong>Class & Sec</strong></th>
        <th hwidth="10"><strong>$class_data_val->Class_Name $class_data_val->Sec_Name</strong></th>
    </tr>
     <tr>
        <td colspan="2"></td>
        
    </tr>
</table>
EOD;

                $html .= '
                <table  border="1">
                    <thead>
                    <tr >
                        <th width="25" align="center"><strong> SNo </strong></th>
                        <th  width="300" align="left"><strong> Student </strong></th>
                        <th width="53" align="center"><strong> Regd.No </strong></th>
                        <th width="50" align="center"><strong> Roll No </strong></th>';
                        if($mobile_check == 'on'){
                            $html .= ' <th width="90" align="center"><strong> Mobile No </strong></th>';
                        }
                       
                         $html .= '
                        <th align="center">Religion</th>
                       
                    </tr>
                    </thead>
                    <tbody>
';
                $this->db->where('STD_CS_SEQ', $class_data_val->CS_SEQ);
                $this->db->where('STD_RLGN', $multi_religion_list);
                $this->db->order_by('STD_ROLLNO');

                $st_data = $this->db->get('student_details')->result_array();
                // echo $class_data_val->CS_SEQ;
                // echo "<pre>"; print_r($st_data); die();
                foreach ($st_data as $index => $st_data_val) {
                    $religion = '';
                    $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                    if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
                    $html .= '<tr >
                            <td width="25" align="center">'.++$index.'</td>
                            <td width="300" align="left"> '.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="53" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="50" align="center">'.$st_data_val['STD_ROLLNO'].'</td>';
                             if($mobile_check == 'on'){
                                $html .= ' <th width="90" align="center">'.$st_data_val['STD_PH_NO'].' </th>';
                            }
                            $html .= '
                            <td align="center">'.$religion.'</td>
                        </tr>';
                }

                $html .= <<<EOD
                    </tbody>
                </table>
                <br><br><br>
                <table >
                    <thead>
                    <tr>
                        <th ></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th width="500" align="left"><strong>Teacher's Name  </strong></th>
                        <th  width="210" align="center"><strong> Signature </strong></th>                        
                    </tr>
                    </thead>
                </table>               
EOD;
// Print text using writeHTMLCell()

            }

            $pdf->writeHTMLCell(0, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
        }
        else if ($category_list == 'REL_VAL') {
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('P', 'A4');

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= '
                <table border="1">
                    <thead>
                    <tr>
                        <th width="25" align="center"><strong> SNo </strong></th>
                        <th  width="200" align="center"><strong> Class</strong></th>
                        <th width="53" align="center"><strong> REL </strong></th>
                        <th  width="90" align="center"><strong> VAL </strong></th>
                        <th width="40" align="center"><strong> Total </strong></th>';
                      
                        
                       
                         $html .= '
                        
                       
                    </tr>
                    </thead>
                    <tbody>
';

           $this->db->select('
                class_sec_hdr.class_sec AS class,
                COUNT(CASE WHEN student_details.STD_RLGN = "3" THEN 1 END) AS rel_count,
                COUNT(CASE WHEN student_details.STD_RLGN IS NOT NULL AND student_details.STD_RLGN != "null" AND student_details.STD_RLGN != "3" THEN 1 END) AS val_count
            ');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('student_details.STD_CS_SEQ', $classid);
            $this->db->where('student_details.STD_LEFT', 0);
            $this->db->where('student_details.STD_STATUS', 0);
            $this->db->group_by('student_details.STD_CS_SEQ');
            $this->db->order_by('class_sec_hdr.class_order','ASC');
            $st_data = $this->db->get('student_details')->result_array();

           
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            $valtotal = 0;
            $reltotal = 0;
            
            foreach ($st_data as $index => $st_data_val) {
                $religion = '';
                $valtotal +=$st_data_val['val_count'];
                $reltotal +=$st_data_val['rel_count'];
                $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
                $html .= '<tr>
                            <td width="25" align="center">'.++$index.'</td>
                            <td width="200" align="center"> '.$st_data_val['class'].'</td>
                            <td width="53" align="center">'.$st_data_val['rel_count'].'</td>
                            <td width="90" align="center">'.$st_data_val['val_count'].'</td>
                            <td width="40" align="center">'.($st_data_val['rel_count'] + $st_data_val['val_count']).'</td>';
                            
                            $html .= '
                           
                        </tr>';
            }

             $html .= '
             <tr>
                            <td width="25"></td>
                            <td width="200" align="center" style="font-weight:bold">Total</td>
                            <td width="53" align="center" style="font-weight:bold">'.$reltotal.'</td>
                            <td width="90" align="center" style="font-weight:bold">'.$valtotal.'</td>
                            <td width="40" align="center" style="font-weight:bold">'.($reltotal+$valtotal).'</td>
                        </tr>
                    </tbody>
                </table>
               
';
// Print text using writeHTMLCell()

            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
        else if ($category_list == 'language') {
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('L', 'A4');

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= '
                <table border="1">
                    <thead>
                    <tr>
                        <th width="100" align="center"><strong> SLNO </strong></th>
                        <th  width="100" align="center"><strong> CLASS</strong></th>
                        <th width="100" align="center"><strong> 2ND LANG HINDI </strong></th>
                        <th  width="100" align="center"><strong> 2ND LANG BENG </strong></th>
                        <th width="100" align="center"><strong> 3RD LANG HINDI </strong></th>
                        <th width="100" align="center"><strong> 3RD LANG BENG </strong></th>
                        <th width="100" align="center"><strong> HIND/ENG </strong></th>
                        <th width="100" align="center"><strong> ENG/BENG </strong></th>
                       
                        ';
                      
                        
                       
                         $html .= '
                        
                       
                    </tr>
                   
                    </thead>
                    <tbody>
';

        //   $this->db->select('
        //         class_sec_hdr.class_sec AS class,
        //         student_details.STD_CS_SEQ,
        //         COUNT(CASE WHEN student_details.STD_SECOND_LANG = "Hindi" THEN 1 END) AS SECOND_LANG_HINDI,
        //         COUNT(CASE WHEN student_details.STD_SECOND_LANG = "Bengali" THEN 1 END) AS SECOND_LANG_BENG,
                // COUNT(CASE WHEN student_details.STD_THIRD_LANG = "Hindi" THEN 1 END) AS THIRD_LANG_HINDI,
                // COUNT(CASE WHEN student_details.STD_THIRD_LANG = "Bengali" THEN 1 END) AS THIRD_LANG_BENG,
        //         COUNT(CASE WHEN student_details.STD_SECOND_LANG = "HINDI, ENG" THEN 1 END) AS HINDI_ENG,
        //         COUNT(CASE WHEN student_details.STD_SECOND_LANG = "ENG, BENG" THEN 1 END) AS ENG_BENG,
        //     ');
        //     $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        //     $this->db->where_in('student_details.STD_CS_SEQ', $classid);
        //     $this->db->where('student_details.STD_LEFT', 0);
        //     $this->db->where('student_details.STD_STATUS', 0);
        //     $this->db->group_by('student_details.STD_CS_SEQ');
        //     $this->db->order_by('class_sec_hdr.class_order','ASC');
        //     $st_data = $this->db->get('student_details')->result_array();
        
        $this->db->select('
                class_sec_hdr.class_sec AS class,
                student_details.STD_CS_SEQ,
                COUNT(CASE WHEN student_details.STD_SECOND_LANG = "Hindi" THEN 1 END) AS SECOND_LANG_HINDI,
                COUNT(CASE WHEN student_details.STD_SECOND_LANG = "Bengali" THEN 1 END) AS SECOND_LANG_BENG,
                COUNT(CASE WHEN student_details.STD_THIRD_LANG = "Hindi" THEN 1 END) AS THIRD_LANG_HINDI,
                COUNT(CASE WHEN student_details.STD_THIRD_LANG = "Bengali" THEN 1 END) AS THIRD_LANG_BENG,
                COUNT(CASE 
                        WHEN student_details.STD_CS_SEQ IN (18, 19, 20, 21) 
                        THEN 1 
                    END) - COUNT(CASE 
                                    WHEN student_details.STD_SECOND_LANG = "Hindi" 
                                    AND student_details.STD_CS_SEQ IN (18, 19, 20, 21) 
                                    THEN 1 
                                END) AS THIRD_LANG_HINDI_OTHERS,
                COUNT(CASE 
                        WHEN student_details.STD_CS_SEQ IN (18, 19, 20, 21) 
                        THEN 1 
                    END) - COUNT(CASE 
                                    WHEN student_details.STD_SECOND_LANG = "Bengali" 
                                    AND student_details.STD_CS_SEQ IN (18, 19, 20, 21) 
                                    THEN 1 
                                END) AS THIRD_LANG_BENG_OTHERS,
                COUNT(CASE WHEN student_details.STD_SECOND_LANG = "HINDI,ENG" THEN 1 END) AS HINDI_ENG,
                COUNT(CASE WHEN student_details.STD_SECOND_LANG = "ENG,BENG" THEN 1 END) AS ENG_BENG
            ');
        $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
        $this->db->where_in('student_details.STD_CS_SEQ', $classid);
        $this->db->where('student_details.STD_LEFT', 0);
        $this->db->where('student_details.STD_STATUS', 0);
        $this->db->group_by('student_details.STD_CS_SEQ');
        $this->db->order_by('class_sec_hdr.class_order','ASC');
        $st_data = $this->db->get('student_details')->result_array();


           
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            $SECOND_LANG_HINDI = 0;
            $SECOND_LANG_BENG = 0;
            $THIRD_LANG_HINDI = 0;
            $THIRD_LANG_BENG = 0;
            $HINDI_ENG = 0;
            $ENG_BENG = 0;
            foreach ($st_data as $index => $st_data_val) {
                $religion = '';
                $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
                $SECOND_LANG_HINDI +=$st_data_val['SECOND_LANG_HINDI'];
                if($st_data_val['STD_CS_SEQ'] == 18 || $st_data_val['STD_CS_SEQ'] == 19 || $st_data_val['STD_CS_SEQ'] == 20 || $st_data_val['STD_CS_SEQ'] == 21){
                   $THIRD_LANG_HINDI +=$st_data_val['THIRD_LANG_HINDI_OTHERS'];
                    $THIRD_LANG_BENG +=$st_data_val['THIRD_LANG_BENG_OTHERS'];  
                }else{
                    $THIRD_LANG_HINDI +=$st_data_val['THIRD_LANG_HINDI'];
                    $THIRD_LANG_BENG +=$st_data_val['THIRD_LANG_BENG'];
                }
                $SECOND_LANG_BENG +=$st_data_val['SECOND_LANG_BENG'];
               
                $HINDI_ENG +=$st_data_val['HINDI_ENG'];
                $ENG_BENG +=$st_data_val['ENG_BENG'];
                 
                $html .= '<tr>
                            <td width="100" align="center">'.++$index.'</td>
                            <td width="100" align="center"> '.$st_data_val['class'].'</td>
                            <td width="100" align="center">'.$st_data_val['SECOND_LANG_HINDI'].'</td>
                            <td width="100" align="center">'.$st_data_val['SECOND_LANG_BENG'].'</td>
                            ';
                            if($st_data_val['STD_CS_SEQ'] == 18 || $st_data_val['STD_CS_SEQ'] == 19 || $st_data_val['STD_CS_SEQ'] == 20 || $st_data_val['STD_CS_SEQ'] == 21){
                                $html .=' <td width="100" align="center">'.$st_data_val['THIRD_LANG_HINDI_OTHERS'].'</td>
                            <td width="100" align="center">'.$st_data_val['THIRD_LANG_BENG_OTHERS'].'</td>';
                            }else{
                                $html .=' <td width="100" align="center">'.$st_data_val['THIRD_LANG_HINDI'].'</td>
                            <td width="100" align="center">'.$st_data_val['THIRD_LANG_BENG'].'</td>';
                            }
                             $html .='
                           
                            <td width="100" align="center">'.$st_data_val['HINDI_ENG'].'</td>
                            <td width="100" align="center">'.$st_data_val['ENG_BENG'].'</td>
                           ';
                            
                            $html .= '
                           
                        </tr>
                        
                        ';
            }

            $html .= '
             <tr>
                            <td width="100"></td>
                            <td width="100" align="center" style="font-weight:bold">Total</td>
                            <td width="100" align="center" style="font-weight:bold">'.$SECOND_LANG_HINDI.'</td>
                            <td width="100" align="center" style="font-weight:bold">'.$SECOND_LANG_BENG.'</td>
                            <td width="100" align="center" style="font-weight:bold">'.$THIRD_LANG_HINDI.'</td>
                            <td width="100" align="center" style="font-weight:bold">'.$THIRD_LANG_BENG.'</td>
                            <td width="100" align="center" style="font-weight:bold">'.$HINDI_ENG.'</td>
                            <td width="100" align="center" style="font-weight:bold">'.$ENG_BENG.'</td>
                        </tr>
                    </tbody>
                </table>
               
';
// Print text using writeHTMLCell()

            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
        else if ($category_list == 'house') {
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('P', 'A4');

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= '
                <table border="1">
                    <thead>
                    <tr>
                        <th width="50" align="center"><strong> SLNO </strong></th>
                        <th  width="175" align="center"><strong> CLASS</strong></th>
                        <th width="80" align="center"><strong> YELLOW </strong></th>
                        <th  width="80" align="center"><strong> GREEN </strong></th>
                        <th width="80" align="center"><strong> RED </strong></th>
                        <th width="80" align="center"><strong> BLUE </strong></th>
                       <th width="80" align="center"><strong> TOTAL </strong></th>
                        ';
                      
                        
                       
                         $html .= '
                        
                       
                    </tr>
                   
                    </thead>
                    <tbody>
';

           $this->db->select('
                class_sec_hdr.class_sec AS class,
                COUNT(CASE WHEN student_details.STD_HOUSE = "Yellow" THEN 1 END) AS YELLOW,
                COUNT(CASE WHEN student_details.STD_HOUSE = "Green" THEN 1 END) AS GREEN,
                COUNT(CASE WHEN student_details.STD_HOUSE = "Red" THEN 1 END) AS RED,
                COUNT(CASE WHEN student_details.STD_HOUSE = "Blue" THEN 1 END) AS BLUE,
            ');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('student_details.STD_CS_SEQ', $classid);
            $this->db->where('student_details.STD_LEFT', 0);
            $this->db->where('student_details.STD_STATUS', 0);
            $this->db->group_by('student_details.STD_CS_SEQ');
             $this->db->order_by('class_sec_hdr.class_order','ASC');
            $st_data = $this->db->get('student_details')->result_array();

           
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            $yellow_total = 0;
            $green_total = 0;
            $red_total = 0;
            $blue_total = 0;
            foreach ($st_data as $index => $st_data_val) {
                $religion = '';
                $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
                $yellow_total +=$st_data_val['YELLOW'];
                $green_total +=$st_data_val['GREEN'];
                $red_total +=$st_data_val['RED'];
                $blue_total +=$st_data_val['BLUE'];
                $html .= '<tr>
                            <td width="50" align="center">'.++$index.'</td>
                            <td width="175" align="center"> '.$st_data_val['class'].'</td>
                            <td width="80" align="center">'.$st_data_val['YELLOW'].'</td>
                            <td width="80" align="center">'.$st_data_val['GREEN'].'</td>
                            <td width="80" align="center">'.$st_data_val['RED'].'</td>
                            <td width="80" align="center">'.$st_data_val['BLUE'].'</td>   
                            <td width="80" align="center">'.($st_data_val['YELLOW'] + $st_data_val['GREEN'] + $st_data_val['RED'] + $st_data_val['BLUE']).'</td>
                           ';
                            
                            $html .= '
                           
                        </tr>
                        
                        ';
            }

            $html .= '
             <tr>
                            <td width="50"></td>
                            <td width="175" align="center" style="font-weight:bold">Total</td>
                            <td width="80" align="center" style="font-weight:bold">'.$yellow_total.'</td>
                            <td width="80" align="center" style="font-weight:bold">'.$green_total.'</td>
                            <td width="80" align="center" style="font-weight:bold">'.$red_total.'</td>
                            <td width="80" align="center" style="font-weight:bold">'.$blue_total.'</td>
                            <td width="80" align="center" style="font-weight:bold">'.($yellow_total + $green_total + $red_total + $blue_total).'</td>
                        </tr>
                    </tbody>
                </table>
               
';
// Print text using writeHTMLCell()

            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
        else if ($category_list == 'house_yellow') {
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('P', 'A4');

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= '
                <table border="1">
                    <thead>
                    <tr>
                        <th width="50" align="center"><strong> SLNO </strong></th>
                        <th  width="100" align="center"><strong> CLASS</strong></th>
                        <th width="155" align="center"><strong> STUDENT </strong></th>
                        <th  width="80" align="center"><strong> REGD.NO </strong></th>
                        <th width="80" align="center"><strong> ROLL </strong></th>';
                        if($mobile_check == 'on'){
                            $html .= ' <th width="80" align="center"><strong> Mobile </strong></th>';
                        }
                         $html .= '
                        <th width="80" align="center"><strong> HOUSE </strong></th>
                      
                        ';
                      
                        
                       
                         $html .= '
                        
                       
                    </tr>
                   
                    </thead>
                    <tbody>
';

           $this->db->select('
                class_sec_hdr.class_sec AS class,
                student_details.ST_FULL_NAME,
                student_details.STD_REGNO,
                student_details.STD_ROLLNO,
                student_details.STD_PH_NO,
                student_details.STD_HOUSE,
            ');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('student_details.STD_CS_SEQ', $classid);
            $this->db->where('student_details.STD_LEFT', 0);
            $this->db->where('student_details.STD_STATUS', 0);
            $this->db->where('student_details.STD_HOUSE', "Yellow");
             $this->db->order_by('class_sec_hdr.class_order','ASC');
            $st_data = $this->db->get('student_details')->result_array();

           
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            $yellow_total = 0;
            $green_total = 0;
            $red_total = 0;
            $blue_total = 0;
            foreach ($st_data as $index => $st_data_val) {
                $religion = '';
                $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
              
                $html .= '<tr>
                            <td width="50" align="center">'.++$index.'</td>
                            <td width="100" align="center"> '.$st_data_val['class'].'</td>
                            <td width="155" align="center">'.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="80" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="80" align="center">'.$st_data_val['STD_ROLLNO'].'</td>';
                             if($mobile_check == 'on'){
                                $html .='<td width="80" align="center">'.$st_data_val['STD_PH_NO'].'</td>'; 
                             }
                            $html .='
                            <td width="80" align="center">'.$st_data_val['STD_HOUSE'].'</td>
                           ';
                            
                            $html .= '
                           
                        </tr>
                        
                        ';
            }

            $html .= '
            
                    </tbody>
                </table>
               
';
// Print text using writeHTMLCell()

            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
        else if ($category_list == 'house_green') {
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('P', 'A4');

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= '
                <table border="1">
                    <thead>
                    <tr>
                        <th width="50" align="center"><strong> SLNO </strong></th>
                        <th  width="100" align="center"><strong> CLASS</strong></th>
                        <th width="155" align="center"><strong> STUDENT </strong></th>
                        <th  width="80" align="center"><strong> REGD.NO </strong></th>
                        <th width="80" align="center"><strong> ROLL </strong></th>';
                        if($mobile_check == 'on'){
                            $html .= ' <th width="80" align="center"><strong> Mobile </strong></th>';
                        }
                         $html .= '
                        <th width="80" align="center"><strong> HOUSE </strong></th>
                      
                        ';
                      
                        
                       
                         $html .= '
                        
                       
                    </tr>
                   
                    </thead>
                    <tbody>
';

           $this->db->select('
                class_sec_hdr.class_sec AS class,
                student_details.ST_FULL_NAME,
                student_details.STD_REGNO,
                student_details.STD_ROLLNO,
                student_details.STD_PH_NO,
                student_details.STD_HOUSE,
            ');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('student_details.STD_CS_SEQ', $classid);
            $this->db->where('student_details.STD_LEFT', 0);
            $this->db->where('student_details.STD_STATUS', 0);
            $this->db->where('student_details.STD_HOUSE', "Green");
            $this->db->order_by('class_sec_hdr.class_order','ASC');
            $st_data = $this->db->get('student_details')->result_array();

           
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            $yellow_total = 0;
            $green_total = 0;
            $red_total = 0;
            $blue_total = 0;
            foreach ($st_data as $index => $st_data_val) {
                $religion = '';
                $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
              
                $html .= '<tr>
                            <td width="50" align="center">'.++$index.'</td>
                            <td width="100" align="center"> '.$st_data_val['class'].'</td>
                            <td width="155" align="center">'.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="80" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="80" align="center">'.$st_data_val['STD_ROLLNO'].'</td>';
                             if($mobile_check == 'on'){
                                $html .='<td width="80" align="center">'.$st_data_val['STD_PH_NO'].'</td>'; 
                             }
                            $html .='
                            <td width="80" align="center">'.$st_data_val['STD_HOUSE'].'</td>
                           ';
                            
                            $html .= '
                           
                        </tr>
                        
                        ';
            }

            $html .= '
            
                    </tbody>
                </table>
               
';
// Print text using writeHTMLCell()

            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
         else if ($category_list == 'house_red') {
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('P', 'A4');

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= '
                <table border="1">
                    <thead>
                    <tr>
                        <th width="50" align="center"><strong> SLNO </strong></th>
                        <th  width="100" align="center"><strong> CLASS</strong></th>
                        <th width="155" align="center"><strong> STUDENT </strong></th>
                        <th  width="80" align="center"><strong> REGD.NO </strong></th>
                        <th width="80" align="center"><strong> ROLL </strong></th>';
                        if($mobile_check == 'on'){
                            $html .= ' <th width="80" align="center"><strong> Mobile </strong></th>';
                        }
                         $html .= '
                        <th width="80" align="center"><strong> HOUSE </strong></th>
                      
                        ';
                      
                        
                       
                         $html .= '
                        
                       
                    </tr>
                   
                    </thead>
                    <tbody>
';

           $this->db->select('
                class_sec_hdr.class_sec AS class,
                student_details.ST_FULL_NAME,
                student_details.STD_REGNO,
                student_details.STD_ROLLNO,
                student_details.STD_PH_NO,
                student_details.STD_HOUSE,
            ');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('student_details.STD_CS_SEQ', $classid);
            $this->db->where('student_details.STD_LEFT', 0);
            $this->db->where('student_details.STD_STATUS', 0);
            $this->db->where('student_details.STD_HOUSE', "Red");
            $this->db->order_by('class_sec_hdr.class_order','ASC');
            $st_data = $this->db->get('student_details')->result_array();

           
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            $yellow_total = 0;
            $green_total = 0;
            $red_total = 0;
            $blue_total = 0;
            foreach ($st_data as $index => $st_data_val) {
                $religion = '';
                $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
              
                $html .= '<tr>
                            <td width="50" align="center">'.++$index.'</td>
                            <td width="100" align="center"> '.$st_data_val['class'].'</td>
                            <td width="155" align="center">'.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="80" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="80" align="center">'.$st_data_val['STD_ROLLNO'].'</td>';
                             if($mobile_check == 'on'){
                                $html .='<td width="80" align="center">'.$st_data_val['STD_PH_NO'].'</td>'; 
                             }
                            $html .='
                            <td width="80" align="center">'.$st_data_val['STD_HOUSE'].'</td>
                           ';
                            
                            $html .= '
                           
                        </tr>
                        
                        ';
            }

            $html .= '
            
                    </tbody>
                </table>
               
';
// Print text using writeHTMLCell()

            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
         else if ($category_list == 'house_blue') {
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('P', 'A4');

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= '
                <table border="1">
                    <thead>
                    <tr>
                        <th width="50" align="center"><strong> SLNO </strong></th>
                        <th  width="100" align="center"><strong> CLASS</strong></th>
                        <th width="155" align="center"><strong> STUDENT </strong></th>
                        <th  width="80" align="center"><strong> REGD.NO </strong></th>
                        <th width="80" align="center"><strong> ROLL </strong></th>';
                        if($mobile_check == 'on'){
                            $html .= ' <th width="80" align="center"><strong> Mobile </strong></th>';
                        }
                         $html .= '
                        <th width="80" align="center"><strong> HOUSE </strong></th>
                      
                        ';
                      
                        
                       
                         $html .= '
                        
                       
                    </tr>
                   
                    </thead>
                    <tbody>
';

           $this->db->select('
                class_sec_hdr.class_sec AS class,
                student_details.ST_FULL_NAME,
                student_details.STD_REGNO,
                student_details.STD_ROLLNO,
                student_details.STD_PH_NO,
                student_details.STD_HOUSE,
            ');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('student_details.STD_CS_SEQ', $classid);
            $this->db->where('student_details.STD_LEFT', 0);
            $this->db->where('student_details.STD_STATUS', 0);
            $this->db->where('student_details.STD_HOUSE', "Blue"); 
            $this->db->order_by('class_sec_hdr.class_order','ASC');
            $st_data = $this->db->get('student_details')->result_array();

           
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            $yellow_total = 0;
            $green_total = 0;
            $red_total = 0;
            $blue_total = 0;
            foreach ($st_data as $index => $st_data_val) {
                $religion = '';
                $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
              
                $html .= '<tr>
                            <td width="50" align="center">'.++$index.'</td>
                            <td width="100" align="center"> '.$st_data_val['class'].'</td>
                            <td width="155" align="center">'.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="80" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="80" align="center">'.$st_data_val['STD_ROLLNO'].'</td>';
                             if($mobile_check == 'on'){
                                $html .='<td width="80" align="center">'.$st_data_val['STD_PH_NO'].'</td>'; 
                             }
                            $html .='
                            <td width="80" align="center">'.$st_data_val['STD_HOUSE'].'</td>
                           ';
                            
                            $html .= '
                           
                        </tr>
                        
                        ';
            }

            $html .= '
            
                    </tbody>
                </table>
               
';
// Print text using writeHTMLCell()

            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
         else if ($category_list == 'aadhar_card') {
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('P', 'A4');

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= '
                <table border="1">
                    <thead>
                    <tr>
                        <th width="50" align="center"><strong> SLNO </strong></th>
                        <th  width="100" align="center"><strong> CLASS</strong></th>
                        <th width="155" align="center"><strong> STUDENT </strong></th>
                        <th  width="80" align="center"><strong> REGD.NO </strong></th>
                        <th width="80" align="center"><strong> ROLL </strong></th>';
                        if($mobile_check == 'on'){
                            $html .= ' <th width="80" align="center"><strong> Mobile </strong></th>';
                        }
                         $html .= '
                        <th width="80" align="center"><strong> Aadhar ID </strong></th>
                      
                        ';
                      
                        
                       
                         $html .= '
                        
                       
                    </tr>
                   
                    </thead>
                    <tbody>
';

           $this->db->select('
                class_sec_hdr.class_sec AS class,
                student_details.ST_FULL_NAME,
                student_details.STD_REGNO,
                student_details.STD_ROLLNO,
                student_details.STD_PH_NO,
                student_details.aadhaar_id,
            ');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('student_details.STD_CS_SEQ', $classid);
            $this->db->where('student_details.STD_LEFT', 0);
            $this->db->where('student_details.STD_STATUS', 0);
           
            $this->db->order_by('class_sec_hdr.class_order','ASC');
            $st_data = $this->db->get('student_details')->result_array();

           
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            $yellow_total = 0;
            $green_total = 0;
            $red_total = 0;
            $blue_total = 0;
            foreach ($st_data as $index => $st_data_val) {
                $religion = '';
                $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
              
                $html .= '<tr>
                            <td width="50" align="center">'.++$index.'</td>
                            <td width="100" align="center"> '.$st_data_val['class'].'</td>
                            <td width="155" align="center">'.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="80" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="80" align="center">'.$st_data_val['STD_ROLLNO'].'</td>';
                             if($mobile_check == 'on'){
                                $html .='<td width="80" align="center">'.$st_data_val['STD_PH_NO'].'</td>'; 
                             }
                            $html .='
                            <td width="80" align="center">'.$st_data_val['aadhaar_id'].'</td>
                           ';
                            
                            $html .= '
                           
                        </tr>
                        
                        ';
            }

            $html .= '
            
                    </tbody>
                </table>
               
';
// Print text using writeHTMLCell()

            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
         else if ($category_list == 'bangla_shiksha_id') {
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('P', 'A4');

            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= '
                <table border="1">
                    <thead>
                    <tr>
                        <th width="50" align="center"><strong> SLNO </strong></th>
                        <th  width="100" align="center"><strong> CLASS</strong></th>
                        <th width="155" align="center"><strong> STUDENT </strong></th>
                        <th  width="80" align="center"><strong> REGD.NO </strong></th>
                        <th width="80" align="center"><strong> ROLL </strong></th>';
                        if($mobile_check == 'on'){
                            $html .= ' <th width="80" align="center"><strong> Mobile </strong></th>';
                        }
                         $html .= '
                        <th width="80" align="center"><strong> Banglar Shiksha ID </strong></th>
                      
                        ';
                      
                        
                       
                         $html .= '
                        
                       
                    </tr>
                   
                    </thead>
                    <tbody>
';

           $this->db->select('
                class_sec_hdr.class_sec AS class,
                student_details.ST_FULL_NAME,
                student_details.STD_REGNO,
                student_details.STD_ROLLNO,
                student_details.STD_PH_NO,
                student_details.banglar_shiksha_id,
            ');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('student_details.STD_CS_SEQ', $classid);
            $this->db->where('student_details.STD_LEFT', 0);
            $this->db->where('student_details.STD_STATUS', 0);
           
            $this->db->order_by('class_sec_hdr.class_order','ASC');
            $st_data = $this->db->get('student_details')->result_array();

           
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/student_list');
            }
            $yellow_total = 0;
            $green_total = 0;
            $red_total = 0;
            $blue_total = 0;
            foreach ($st_data as $index => $st_data_val) {
                $religion = '';
                $religion = $this->db->get_where('religion', array('religion_id' => $st_data_val['STD_RLGN']))->row();
                if(!empty($religion)){ $religion = $religion->name; }else{ $religion = ''; }
              
                $html .= '<tr>
                            <td width="50" align="center">'.++$index.'</td>
                            <td width="100" align="center"> '.$st_data_val['class'].'</td>
                            <td width="155" align="center">'.$st_data_val['ST_FULL_NAME'].'</td>
                            <td width="80" align="center">'.$st_data_val['STD_REGNO'].'</td>
                            <td width="80" align="center">'.$st_data_val['STD_ROLLNO'].'</td>';
                             if($mobile_check == 'on'){
                                $html .='<td width="80" align="center">'.$st_data_val['STD_PH_NO'].'</td>'; 
                             }
                            $html .='
                            <td width="80" align="center">'.$st_data_val['banglar_shiksha_id'].'</td>
                           ';
                            
                            $html .= '
                           
                        </tr>
                        
                        ';
            }

            $html .= '
            
                    </tbody>
                </table>
               
';
// Print text using writeHTMLCell()

            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
        // Close and output PDF document
        $pdf->Output($doc_name . '.pdf', 'I');


    }

    public function ajax_fetch_classes_by_class_type() {
        $class_type_id = $this->input->post('class_type_id');
        if (!is_array($class_type_id)) {
            $class_type_id = ($class_type_id !== '' && $class_type_id !== null) ? array($class_type_id) : array();
        }
        if (!empty($class_type_id) && !in_array('all', $class_type_id)) {
            $this->db->where_in('Class_Type', $class_type_id);
        }

        $class_rs = $this->db->get('class_sec_hdr')->result();

        $html = '';
        if(!empty($class_rs)) {
            foreach ($class_rs as $val) {
                $html .= '
<option value="'.$val->CS_SEQ.'" selected>'.$val->Class_Name.' - '.$val->Sec_Name.'</option>
';
            }
        }

        $data['class_option_html'] = $html;
        return $data;
    }
    public function get_studentlist_ajax($class){
        $this->db->where('STD_CS_SEQ',$class);
        $this->db->order_by('ST_FULL_NAME', 'ASC');
        $query = $this->db->get('student_details');
        return $query->result();
    }
    public function get_subjectlist_ajax($class){
        $this->db->select('subject.sub_id, subject.sub_name');
        $this->db->from('class_sub_link');
        $this->db->join('subject', 'subject.sub_id = class_sub_link.CS_Sub_id', 'left');
        $this->db->where('class_sub_link.CS_SEQ', $class);
        $this->db->order_by('subject.sub_name', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
   
   
public function print_ranklist_report(){
    $report_type = $this->input->post('report_type');
    if($this->input->post('submit') == 'print_ranklist_report') { 
      
        $class = $this->input->post('rank_class');
        $subject = $this->input->post('rank_subject');

        $data['tab_title'] = 'Rank List Report';
        $data['section_heading'] = 'Rank List Report <small>(Print)</small>';
        $data['menu_name'] = 'Rank List Report';
        
        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $company = $this->company_name((array)$class); 

        // set document information
        $doc_name = 'Rank List_'.date('d-m-Y_h-i-A');
        $pdf->SetCreator(PDF_CREATOR);

        $pdf->SetTitle($doc_name);
        $pdf->SetSubject('Student Rank List Report');
        $pdf->SetKeywords('Student Rank List Report, smg, developed by: https://sketchmeglobal.com');

        $pdf->SetAuthor($company->COM_NAME);
        $html_header = <<<EOD
    <div style="text-align:center;">
    <span style="font-size: 25px;"><strong>$company->COM_NAME</strong></span>
    <br>
    $company->COM_ADD1, $company->COM_ADD2
    <br>
    <strong><span style="background-color: black;color: white;"> $report_type_hdr </span></strong>
    <br>
    </div> 
EOD;
        $pdf->setHtmlHeader($html_header, false);

        // set header and footer fonts and size
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(30, 20, 10, true);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->setPrintFooter(false);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
      
        // Set font
        $pdf->SetFont('times', '', 9, '', true);

        $pdf->AddPage('L', 'LEGAL');

        $this->db->where('sub_id',$subject);
        $query = $this->db->get('subject');
        $subjects = $query->row();
        
        if($subject == "All"){
            $subname = "All";
        }else{
            $subname = $subjects->sub_name;
        }
        
        $this->db->where('CS_SEQ',$class);
        $query = $this->db->get('class_sec_hdr');
        $class_data = $query->row();
        
        $this->db->where('STD_CS_SEQ',$class);
        $this->db->where('STD_LEFT ', 0);
        $this->db->order_by('ST_FULL_NAME', 'ASC');
        $query = $this->db->get('student_details');
        $student_list = $query->result();
        
        $allsubjects = $this->db
            ->select('class_sub_link.*,sub_name')
            ->join('subject','sub_id=CS_Sub_id','left')
            ->where('CS_SEQ', $class)
            ->order_by('Sorting', 'ASC')
            ->get('class_sub_link')->result();
            
        $markssubjects = $this->db
            ->select('class_sub_link.*,sub_name')
            ->join('subject','sub_id=CS_Sub_id','left')
            ->where('CS_SEQ', $class)
            ->where('subject.marks_type', 'Marks')
            ->order_by('Sorting', 'ASC')
            ->get('class_sub_link')->result();

        $html = '';
       
        $html .= <<<EOD
        <table style="width:100%;">
            <tr>
                <th width="80"><strong>Class & Sec</strong></th>
                <th hwidth="10"><strong>$class_data->Class_Name $class_data->Sec_Name</strong></th>
                <th width="80"><strong>Subject:</strong></th>
                <th width="520"align="left"><strong><u>&nbsp;&nbsp;&nbsp;$subname;&nbsp;&nbsp;&nbsp;</u> </strong> </th>
                <th width="90" align="right"> <strong>Year: </strong> <strong>$company->COM_FIN_YEAR</strong></th>
            </tr>
            <tr>
                <td colspan="6"></td>
            </tr>
        </table>
EOD;
        $html .= '
        <table  border="1" style="width:100%;">
            <thead>
            <tr style="border: 1px solid black;">
                <th width="70" align="center"><strong> # </strong></th>
                <th width="100" align="center"><strong> Regd No# </strong></th>
                <th width="100" align="center"><strong> Roll No# </strong></th>
                <th width="250" align="center"><strong> Student Names</strong></th>
                <th width="70" align="center"><strong> SA1 </strong></th>
                <th width="70" align="center"><strong> SA2 </strong></th>
                <th width="70" align="center"><strong> SA3 </strong></th>
                <th width="70" align="center"><strong> Total </strong></th>
                <th width="70" align="center"><strong> Percentage </strong></th>
                <th width="70" align="center"><strong> Rank </strong></th>
            </tr>
            </thead>
            <tbody>
        ';
        
        if(!empty($student_list)){
            $students = array(); // Initialize array
            $i=0;
            foreach($student_list as $row){
                $i++;
                if($subject == "All"){
                    $fttotal = 0;
                    $sttotal = 0;
                    $fnttotal = 0;
                    foreach($allsubjects as $sub){
                        $fa1=$this->fetch_marks($row->STD_SEQ, $class, $test_seq = 1, 1, $sub->CS_Sub_id);
                        $sa1=$this->fetch_marks($row->STD_SEQ, $class, $test_seq = 4, 1, $sub->CS_Sub_id);
                        $fa2=$this->fetch_marks($row->STD_SEQ, $class, $test_seq = 1, 2, $sub->CS_Sub_id);
                        $sa2=$this->fetch_marks($row->STD_SEQ, $class, $test_seq = 4, 2, $sub->CS_Sub_id);
                        $fa3=$this->fetch_marks($row->STD_SEQ, $class, $test_seq = 1, 3,$sub->CS_Sub_id);
                        $sa3=$this->fetch_marks($row->STD_SEQ, $class, $test_seq = 4, 3, $sub->CS_Sub_id);
                        $fttotal +=($fa1+$sa1);
                        $sttotal +=($fa2+$sa2);
                        $fnttotal +=($fa3+$sa3);
                    }
                    $students[] = array(
                        'rank' => $i,
                        'reg_no' => $row->STD_REGNO,
                        'roll_no' => $row->STD_ROLLNO,
                        'name' => $row->ST_FULL_NAME,
                        'sa1_fa1' => $fttotal,
                        'sa2_fa2' => $sttotal,
                        'sa3_fa3' => $fnttotal,
                        'total' => ($fttotal+$sttotal+$fnttotal)
                    ); 
                }else{
                    $fa1=$this->fetch_marks($row->STD_SEQ, $class, $test_seq = 1, 1, $subject);
                    $sa1=$this->fetch_marks($row->STD_SEQ, $class, $test_seq = 4, 1, $subject);
                    $fa2=$this->fetch_marks($row->STD_SEQ, $class, $test_seq = 1, 2, $subject);
                    $sa2=$this->fetch_marks($row->STD_SEQ, $class, $test_seq = 4, 2, $subject);
                    $fa3=$this->fetch_marks($row->STD_SEQ, $class, $test_seq = 1, 3, $subject);
                    $sa3=$this->fetch_marks($row->STD_SEQ, $class, $test_seq = 4, 3, $subject);
                    $total = ($sa1+$sa2+$sa3+$fa1+$fa2+$fa3);
                    $students[] = array(
                        'rank' => $i,
                        'reg_no' => $row->STD_REGNO,
                        'roll_no' => $row->STD_ROLLNO,
                        'name' => $row->ST_FULL_NAME,
                        'sa1_fa1' => $sa1 + $fa1,
                        'sa2_fa2' => $sa2 + $fa2,
                        'sa3_fa3' => $sa3 + $fa3,
                        'total' => $total
                    ); 
                }
            }
            
            // Calculate ranks based on total marks FIRST (before any sorting)
            // Create a temporary array sorted by total for rank calculation
            $temp_for_rank = $students;
            for($i = 0; $i < count($temp_for_rank); $i++) {
                for($j = 0; $j < count($temp_for_rank) - 1; $j++) {
                    if($temp_for_rank[$j]['total'] < $temp_for_rank[$j+1]['total']) {
                        $temp = $temp_for_rank[$j];
                        $temp_for_rank[$j] = $temp_for_rank[$j+1];
                        $temp_for_rank[$j+1] = $temp;
                    }
                }
            }
            
            // Assign ranks based on total marks
            $prev_total = -1;
            $actual_rank = 0;
            $rank_counter = 0;
            
            foreach ($temp_for_rank as $key => $student) {
                $rank_counter++;
                if($student['total'] != $prev_total) {
                    $actual_rank = $rank_counter;
                }
                $temp_for_rank[$key]['actual_rank'] = $actual_rank;
                $prev_total = $student['total'];
            }
            
            // Create rank mapping array
            $rank_map = array();
            foreach($temp_for_rank as $student) {
                $rank_map[$student['reg_no']] = $student['actual_rank'];
            }
            
            // Now sort the original array based on report type
            if($report_type == 'roll_wise'){
                // Sort by roll number (ascending)
                for($i = 0; $i < count($students); $i++) {
                    for($j = 0; $j < count($students) - 1; $j++) {
                        if($students[$j]['roll_no'] > $students[$j+1]['roll_no']) {
                            $temp = $students[$j];
                            $students[$j] = $students[$j+1];
                            $students[$j+1] = $temp;
                        }
                    }
                }
            } else {
                // Sort by total (descending) - use the already sorted temp_for_rank
                $students = $temp_for_rank;
            }
            
            // Assign the pre-calculated ranks to students
            foreach ($students as $key => $student) {
                $students[$key]['actual_rank'] = $rank_map[$student['reg_no']];
            }
            
            $j=0;
            foreach ($students as $index => $student) {
                $j++;
                $rank = $student['actual_rank'];
                
                if($report_type == 'rank_wise'){
                    $place = '';
                    if ($rank == 1) $place = 'First';
                    else if ($rank == 2) $place = 'Second';
                    else if ($rank == 3) $place = 'Third';
                    else $place = '';
                } else {
                    $place = $rank;
                }
                
                if($subject == "All"){
                   $total_marks = count($markssubjects) * 300; 
                   $percentage = round(($student['total'] / $total_marks)*100); 
                }else{
                    $percentage = round($student['total']/3);
                }
                
                $html .= '
                <tr>
                    <td width="70"  align="center">' . $j . '</td>
                    <td width="100"  align="center">' . $student['reg_no'] . '</td>
                    <td width="100"  align="center">' . $student['roll_no'] . '</td>
                    <td width="250"  align="left">' . $student['name'] . '</td>
                    <td width="70"  align="center">' . $student['sa1_fa1'] . '</td>
                    <td width="70"  align="center">' . $student['sa2_fa2'] . '</td>
                    <td width="70"  align="center">' . $student['sa3_fa3'] . '</td>
                    <td width="70"  align="center">' . $student['total'] . '</td>
                    <td width="70"  align="center">'.$percentage.'%</td>
                    <td width="70"  align="center">' . $place . '</td>
                </tr>
                ';
            }
        }

        $html .= <<<EOD
            </tbody>
        </table>
        <table >
            <thead>
            <tr>
                <th ></th>
                <th></th>
            </tr>
            <tr>
                <th width="700" align="left"><strong>Teacher's Name  </strong></th>
                <th  width="210" align="center"><strong> Signature </strong></th>                        
            </tr>
            </thead>
        </table>               
EOD;

        $pdf->writeHTMLCell(220, 0, 5, 28, $html, 0, 1, 0, false, 'center', true);

        $pdf->Output($doc_name . '.pdf', 'I');
    }
    }

    public function print_class_subject_topper_report() {
        if($this->input->post('submit') == 'print_class_subject_topper_report') {

            $class   = $this->input->post('rank_class');
            $subject = $this->input->post('rank_subject');

            if (empty($class)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Please select a class.');
                return array('type' => 'redirect', 'page' => 'admin/class_subject_topper');
            }

            $company    = $this->company_name((array)$class);
            $class_data = $this->db->get_where('class_sec_hdr', array('CS_SEQ' => $class))->row();

            $subname = 'All';
            if ($subject && $subject !== 'All') {
                $sub_row = $this->db->get_where('subject', array('sub_id' => $subject))->row();
                $subname = $sub_row ? $sub_row->sub_name : 'All';
            }

            $allsubjects = $this->db
                ->select('class_sub_link.*, sub_name')
                ->join('subject', 'sub_id = CS_Sub_id', 'left')
                ->where('CS_SEQ', $class)
                ->order_by('Sorting', 'ASC')
                ->get('class_sub_link')->result();

            $markssubjects = $this->db
                ->select('class_sub_link.*, sub_name')
                ->join('subject', 'sub_id = CS_Sub_id', 'left')
                ->where('CS_SEQ', $class)
                ->where('subject.marks_type', 'Marks')
                ->order_by('Sorting', 'ASC')
                ->get('class_sub_link')->result();

            $student_list = $this->db
                ->where('STD_CS_SEQ', $class)
                ->where('STD_LEFT', 0)
                ->order_by('ST_FULL_NAME', 'ASC')
                ->get('student_details')->result();

            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $doc_name = 'Class_Subject_Topper_' . date('d-m-Y_h-i-A');

            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor($company->COM_NAME);
            $pdf->SetTitle($doc_name);
            $pdf->SetSubject('Class Subject Topper Report');
            $pdf->SetKeywords('Class Subject Topper Report, smg');

            $html_header = <<<EOD
<div style="text-align:center;">
<span style="font-size:25px;"><strong>{$company->COM_NAME}</strong></span><br>
{$company->COM_ADD1}, {$company->COM_ADD2}<br>
<strong>Class Subject Topper Report</strong><br>
</div>
EOD;
            $pdf->setHtmlHeader($html_header, false);
            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', 8));
            $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(30, 20, 10, true);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->setPrintFooter(false);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->setFontSubsetting(true);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('L', 'LEGAL');

            $html  = '<table style="width:100%;">';
            $html .= '<tr>';
            $html .= '<th width="80"><strong>Class &amp; Sec</strong></th>';
            $html .= '<th width="150"><strong>' . $class_data->Class_Name . ' ' . $class_data->Sec_Name . '</strong></th>';
            $html .= '<th width="80"><strong>Subject:</strong></th>';
            $html .= '<th width="400" align="left"><strong><u>&nbsp;' . $subname . '&nbsp;</u></strong></th>';
            $html .= '<th width="90" align="right"><strong>Year: ' . $company->COM_FIN_YEAR . '</strong></th>';
            $html .= '</tr></table>';

            $html .= '<table border="1" style="width:100%;">';
            $html .= '<thead><tr>';
            $html .= '<th width="40" align="center"><strong>#</strong></th>';
            $html .= '<th width="90" align="center"><strong>Reg No</strong></th>';
            $html .= '<th width="60" align="center"><strong>Roll No</strong></th>';
            $html .= '<th width="220" align="center"><strong>Student Name</strong></th>';
            $html .= '<th width="70" align="center"><strong>Term 1</strong></th>';
            $html .= '<th width="70" align="center"><strong>Term 2</strong></th>';
            $html .= '<th width="70" align="center"><strong>Term 3</strong></th>';
            $html .= '<th width="70" align="center"><strong>Total</strong></th>';
            $html .= '<th width="70" align="center"><strong>%</strong></th>';
            $html .= '<th width="60" align="center"><strong>Rank</strong></th>';
            $html .= '</tr></thead><tbody>';

            $students = array();
            foreach ($student_list as $row) {
                if ($subject && $subject !== 'All') {
                    $t1 = (float)$this->fetch_marks($row->STD_SEQ, $class, 1, 1, $subject)
                        + (float)$this->fetch_marks($row->STD_SEQ, $class, 4, 1, $subject);
                    $t2 = (float)$this->fetch_marks($row->STD_SEQ, $class, 1, 2, $subject)
                        + (float)$this->fetch_marks($row->STD_SEQ, $class, 4, 2, $subject);
                    $t3 = (float)$this->fetch_marks($row->STD_SEQ, $class, 1, 3, $subject)
                        + (float)$this->fetch_marks($row->STD_SEQ, $class, 4, 3, $subject);
                } else {
                    $t1 = $t2 = $t3 = 0;
                    foreach ($allsubjects as $sub) {
                        $t1 += (float)$this->fetch_marks($row->STD_SEQ, $class, 1, 1, $sub->CS_Sub_id)
                             + (float)$this->fetch_marks($row->STD_SEQ, $class, 4, 1, $sub->CS_Sub_id);
                        $t2 += (float)$this->fetch_marks($row->STD_SEQ, $class, 1, 2, $sub->CS_Sub_id)
                             + (float)$this->fetch_marks($row->STD_SEQ, $class, 4, 2, $sub->CS_Sub_id);
                        $t3 += (float)$this->fetch_marks($row->STD_SEQ, $class, 1, 3, $sub->CS_Sub_id)
                             + (float)$this->fetch_marks($row->STD_SEQ, $class, 4, 3, $sub->CS_Sub_id);
                    }
                }
                $students[] = array(
                    'reg_no'  => $row->STD_REGNO,
                    'roll_no' => $row->STD_ROLLNO,
                    'name'    => $row->ST_FULL_NAME,
                    't1'      => $t1,
                    't2'      => $t2,
                    't3'      => $t3,
                    'total'   => $t1 + $t2 + $t3,
                );
            }

            // Sort descending by total for rank calculation
            usort($students, function($a, $b) { return $b['total'] <=> $a['total']; });

            // Assign ranks (shared rank for ties)
            $rank = 0; $counter = 0; $prev_total = -1;
            foreach ($students as $k => $s) {
                $counter++;
                if ($s['total'] != $prev_total) $rank = $counter;
                $students[$k]['rank'] = $rank;
                $prev_total = $s['total'];
            }

            $total_marks_per_student = ($subject && $subject !== 'All') ? 300 : count($markssubjects) * 300;

            $j = 0;
            foreach ($students as $s) {
                $j++;
                $pct = $total_marks_per_student > 0 ? round(($s['total'] / $total_marks_per_student) * 100) : 0;
                $place = $s['rank'] == 1 ? 'First' : ($s['rank'] == 2 ? 'Second' : ($s['rank'] == 3 ? 'Third' : $s['rank']));
                $html .= '<tr>'
                    . '<td width="40"  align="center">' . $j . '</td>'
                    . '<td width="90"  align="center">' . $s['reg_no'] . '</td>'
                    . '<td width="60"  align="center">' . $s['roll_no'] . '</td>'
                    . '<td width="220" align="left">'   . $s['name'] . '</td>'
                    . '<td width="70"  align="center">' . $s['t1'] . '</td>'
                    . '<td width="70"  align="center">' . $s['t2'] . '</td>'
                    . '<td width="70"  align="center">' . $s['t3'] . '</td>'
                    . '<td width="70"  align="center">' . $s['total'] . '</td>'
                    . '<td width="70"  align="center">' . $pct . '%</td>'
                    . '<td width="60"  align="center">' . $place . '</td>'
                    . '</tr>';
            }

            $html .= '</tbody></table>';

            ob_end_clean();
            $pdf->writeHTMLCell(220, 0, 5, 28, $html, 0, 1, 0, false, 'center', true);
            $pdf->Output($doc_name . '.pdf', 'I');

        } else {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Oh Snap!');
            $this->session->set_flashdata('msg', 'Something went wrong.');
            return array('type' => 'redirect', 'page' => 'admin/class_subject_topper');
        }
    }

    public function notice_report() {
        $notices = $this->db->get('notice_header')->result_array();
        $data['notices'] = $notices;
        $data['form_type'] = 'notice_report';

        $data['tab_title'] = 'Notice Report';
        $data['section_heading'] = 'Notice Report <small>(Print)</small>';
        $data['menu_name'] = 'Notice Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }
    public function add_notice_report() {
        //$cls = $this->db->get('class_sec_hdr')->result_array();
        $cls = $this->db
        ->order_by('class_order', 'ASC')
        ->get('class_sec_hdr')
        ->result_array();
        $this->db->where('TCH_SRLNO !=', 18);
        $teacher = $this->db->get('teacher')->result_array();

        $religion = $this->db->get('religion')->result_array();
        
        $dept = $this->db->get('dept')->result_array();
        $data['dept'] = $dept;

        $data['exam_terms'] = $this->db->get_where('exam_terms', array('status' => 1, 'term_year' => FINANCIAL_YEAR))->result_array();
        $data['class'] = $cls;
        $data['teacher'] = $teacher;
        $data['religion'] = $religion;
        $data['form_type'] = 'add_notice_report';

        $data['tab_title'] = 'Notice Report';
        $data['section_heading'] = 'Notice Report <small>(Add)</small>';
        $data['menu_name'] = 'Notice Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }
    
    public function submit_notice_report() {

        $dept_id = $this->input->post('department[]');
        $st_id_list = $this->input->post('st_id_list[]');
        $date = $this->input->post('date');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');

        if (empty($dept_id)) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'Something went wrong');
            return array('type' => 'redirect', 'page'=>'admin/notice_report');
        }
        
        $header_data=array(
            "notice_date"=>$date,
            "department"=>implode(",",$dept_id),
            "subject" => $subject,
            "message" => $message,
            );
        $this->db->insert('notice_header',$header_data);
        $header_id = $this->db->insert_id();
        if(!empty($st_id_list)){
            foreach($st_id_list as $std){
                $details_data=array(
                    "header_id"=>$header_id,
                    "staff_id"=>$std
                    );
                $this->db->insert("notice_details",$details_data);
            }
        }
        return array('type' => 'redirect', 'page'=>'admin/notice_report');


    }
    public function update_notice_report() {
        $id = $this->input->post('header_id');
        $dept_id = $this->input->post('department[]'); 
        $st_id_list = $this->input->post('st_id_list[]');
        $date = $this->input->post('date');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');

        if (empty($dept_id) && empty($st_id_list)) {
            $this->session->set_flashdata('type', 'error');
            $this->session->set_flashdata('title', 'Naa!');
            $this->session->set_flashdata('msg', 'Something went wrong');
            return array('type' => 'redirect', 'page'=>'admin/notice_report');
        }
        
        $header_data=array(
            "notice_date"=>$date,
            "department"=>implode(",",$dept_id),
            "subject" => $subject,
            "message" => $message,
            );
        $this->db->where('id',$id);
        $this->db->update('notice_header',$header_data);
        $this->db->where('header_id',$id);
        $this->db->delete('notice_details');
        if(!empty($st_id_list)){
            foreach($st_id_list as $std){
                $details_data=array(
                    "header_id"=>$id,
                    "staff_id"=>$std
                    );
                $this->db->insert("notice_details",$details_data);
            }
        }
        return array('type' => 'redirect', 'page'=>'admin/notice_report');


    }
    public function edit_notice_report($id) {
        //$cls = $this->db->get('class_sec_hdr')->result_array();
        $cls = $this->db
        ->order_by('class_order', 'ASC')
        ->get('class_sec_hdr')
        ->result_array();
        $this->db->where('TCH_SRLNO !=', 18);
        $teacher = $this->db->get('teacher')->result_array();

        $religion = $this->db->get('religion')->result_array();
        
        $dept = $this->db->get('dept')->result_array();
        $data['dept'] = $dept;

        $data['exam_terms'] = $this->db->get_where('exam_terms', array('status' => 1, 'term_year' => FINANCIAL_YEAR))->result_array();
        $data['class'] = $cls;
        $data['teacher'] = $teacher;
        $data['religion'] = $religion;
        $this->db->where('id', $id);
        $data['header_data'] = $this->db->get('notice_header')->row();

        $details_data =  $this->db->get_where('notice_details', array('header_id' => $id))->result_array();
        $data['staff_ids'] = array_column($details_data, 'staff_id');
        

      
        $data['form_type'] = 'edit_notice_report';

        $data['tab_title'] = 'Notice Report';
        $data['section_heading'] = 'Notice Report <small>(Edit)</small>';
        $data['menu_name'] = 'Notice Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }
    public function delete_notice_report($id) {
       $this->db->where('id',$id);
       $this->db->delete('notice_header');
       
       $this->db->where('header_id',$id);
       $this->db->delete('notice_details');
       return array('type' => 'redirect', 'page'=>'admin/notice_report');
    }
    public function print_notice_report($id) {
        $this->db->where('id', $id);
        $header_data = $this->db->get('notice_header')->row();

        //$details_data =  $this->db->get_where('notice_details', array('header_id' => $id))->result_array();
        $details_data = $this->db->select('notice_details.*, class_sec_hdr.*')
        ->from('notice_details')
        ->join('class_sec_hdr', 'class_sec_hdr.class_teacher = notice_details.staff_id', 'left') 
        ->where('notice_details.header_id', $id)
        ->order_by('ISNULL(class_sec_hdr.class_order), class_sec_hdr.class_order', 'ASC')
        ->get()
        ->result_array();

        $staff_ids = array_column($details_data, 'staff_id');
        

        $st_id_list = $staff_ids;
        $date = $header_data->notice_date;
        $subject = $header_data->subject;
        $message = $header_data->message;
        
      

        


        // create new PDF document
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $iter = 0;

        $report_type_hdr = 'Notice Report';
       


        $company = $this->company_name((array)$header_data->department); 

        // set document information
        $doc_name = $report_type_hdr.'_'.$date;
        $pdf->SetCreator(PDF_CREATOR);

        $pdf->SetTitle($doc_name);
        $pdf->SetSubject('Notice Report');
        $pdf->SetKeywords('All Notice Report, smg, developed by: https://sketchmeglobal.com');


        $pdf->SetAuthor($company->COM_NAME);

        // set default header data
        //, 2264-2667
        $html_header = <<<EOD
        <table style="">
  <tr>
    <td style="width: 17%; vertical-align: center;"> 
      <img src="https://stanthonyschooledu.org/2026-27/assets/img/reportlogo2.png" style="width: 100px;" alt="ST. Anthony's High School Logo">
    </td>
    <td style="width: 83%; vertical-align: center; text-align:center;">
      <div style="padding-top:5px;">
          <span style="font-size: 23px;"><strong>ST. ANTHONY'S HIGH SCHOOL</strong></span><br>
          <span style="font-size: 16px;">(HIGHER SECONDARY)</span><br>
          <span style="font-size: 16px;">19, MARKET STREET, KOLKATA-700 087</span><br>
          <span style="font-size: 16px;">PHONE: 2265-1530</span><br>
        
          <span style="font-size: 11px;">INDEX NO -AI-059 WEST BENGAL BOARD OF SECONDARY EDUCATION</span><br>
          <span style="font-size: 11px;">INSTITUTION CODE (H.S.)-01385</span>
      </div>
    </td>
  </tr>
</table>
EOD;
        $pdf->setHtmlHeader($html_header, false);

        // set header and footer fonts and size
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 8));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        //$pdf->SetMargins(10, 20, 10, true);
        $leftRightMargin = 20;
        $topMargin = 15;
        $pdf->SetMargins($leftRightMargin, $topMargin, $leftRightMargin, true);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks

        $pdf->setPrintFooter(false);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // -----------------------------------------------------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

      

            // Set font
            $pdf->SetAutoPageBreak(TRUE, 5);
            $pdf->SetFont('times', '', 9, '', true);

            $pdf->AddPage('P', 'A4');

            // Set some content to print
            $this->db->select('class_sec_hdr.*');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ', 'left');
            $this->db->where_in('STD_SEQ', $st_id_list);
            $this->db->group_by('STD_CS_SEQ');
            $class_data = $this->db->get('student_details')->result();

            // echo "<pre>"; print_r($class_data); die();
            //Subject:
            $head = '';
            $html = ' <br><br><br><br><div style="text-align:right;font-size:13px;margin-top:30px;">Date:'.date('d-m-Y',strtotime($date)).'</div>
            <br>
            <div style="text-align:center;font-size:15px;"><b style="text-decoration:underline;">'.$subject.'</b></div><br>
            
            <div style="font-size:15px;">'.nl2br(htmlspecialchars($message)).'</div><br>
            ';
            $i=0;
            if(!empty($st_id_list)){
                $half = ceil(count($st_id_list) / 2);
                $first_part = array_slice($st_id_list, 0, $half);
                $second_part = array_slice($st_id_list, $half);
                
                $html .= '<table  cellpadding="5" cellspacing="0" >';
                $html .= '<tr><th style="width:25px"></th><th style="width:160px"></th><th style="width:200px"></th><th style="width:25px"></th><th style="width:160px"></th><th style="width:200px"></th></tr>';
            
                $i = 0;
                $j = 0;
                $max = max(count($first_part), count($second_part));
            
                for ($k = 0; $k < $max; $k++) {
                    $html .= '<tr>';
                    
                    // First column for Part 1
                    if ($k < count($first_part)) {
                        $i++;
                        $this->db->where('TCH_SRLNO', $first_part[$k]);
                        $teacher = $this->db->get('teacher')->row();
                        $html .= '<td>' . $i . '.</td><td >' . $teacher->TCH_SALUTATION.' '.$teacher->TCH_NAME . '</td><td>.............................................</td>';
                    } else {
                        $html .= '<td></td><td></td>';
                    }
                    
                    // Second column for Part 2
                    if ($k < count($second_part)) {
                        $i++;
                        $this->db->where('TCH_SRLNO', $second_part[$k]);
                        $teacher = $this->db->get('teacher')->row();
                        $html .= '<td>' . $i . '.</td><td >' . $teacher->TCH_SALUTATION.' '.$teacher->TCH_NAME . '</td><td>.............................................</td>';
                    } else {
                        $html .= '<td></td><td></td>';
                    }
                    
                    $html .= '</tr>';
                }
                $html .= '</table>';
            }
            $html .='<br><br><br>
            <div style="font-size:15px;">Mr. Steve Menezes</div>
            <div style="font-size:15px;">Headmaster</div>
            ';
          

            // $pdf->writeHTMLCell(0, 0, 5, 23, $head, 0, 1, 0, true, '', true);
            $pdf->writeHTMLCell(0, 0, 5, 28, $html, 0, 1, 0, true, '', true);
            // $pdf->writeHTML($html, true, false, false, false, '');
        

        


        // Close and output PDF document
        $pdf->Output($doc_name . '.pdf', 'I');


    }
    
     public function teacher_related_report() {
         $cls = $this->db
        ->order_by('class_order', 'ASC')
        ->get('class_sec_hdr')
        ->result_array();
        $this->db->where('TCH_SRLNO !=', 18);
        $teacher = $this->db->get('teacher')->result_array();

        $religion = $this->db->get('religion')->result_array();
        
        $dept = $this->db->get('dept')->result_array();
        $data['dept'] = $dept;

        $data['exam_terms'] = $this->db->get_where('exam_terms', array('status' => 1, 'term_year' => FINANCIAL_YEAR))->result_array();
        $data['class'] = $cls;
        $data['teacher'] = $teacher;
        $data['religion'] = $religion;
        $data['class_type'] = $this->db->get('class_type')->result_array();
        $data['form_type'] = 'teacher_related_report';

        $data['tab_title'] = 'Teacher Related Report';
        $data['section_heading'] = 'Teacher Related Report <small>(Print)</small>';
        $data['menu_name'] = 'Teacher Related Report';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }
    public function print_teacher_related_report(){
        $report_category = $this->input->post('report_category');
        $classid =  $this->input->post('category_class');
        $department =  $this->input->post('department');
     //print_r($classid);
        $mobile_check = $this->input->post('mobile_check');
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
         $class = array('all');
        $company = $this->company_name($class);
        $report_type_hdr = 'Class Teacher Name List';
        // die('ok');
        // set document information
        $doc_name = $report_type_hdr.'_'.date('d-m-Y_h-i-A');
        $pdf->SetCreator(PDF_CREATOR);

        $pdf->SetTitle($doc_name);
        $pdf->SetSubject('Student Strength Report');
        $pdf->SetKeywords('All Transaction Report, smg, developed by: https://sketchmeglobal.com');


        $pdf->SetAuthor($company->COM_NAME);

        // set default header data
        $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 25px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD1, $company->COM_ADD2
        <br>
        <strong><span style="background-color: black;color: white;"> Class teacher Name List </span></strong>  
        <br>
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

        $pdf->setPrintFooter(false);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // -----------------------------------------------------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
        if ($report_category == 'class_teacher_name_list') {
          
             
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('L', 'A4');
           
            // echo "<pre>"; print_r($class_data); die();

            $html = '<div style="display: flex; justify-content: center; align-items: center; padding: 20px;">';
            $html .= '
                <table border="1" style="margin: 0 auto; width: 100%; max-width: 1200px;">
                    <thead>
                        <tr>
                            <th width="25" align="center"><strong> SNo </strong></th>
                            <th width="50" align="center"><strong> Class</strong></th>
                            <th width="100" align="center"><strong> Teacher Name </strong></th>
                            <th width="80" align="center"><strong> Category </strong></th>
                            <th width="180" align="center"><strong> Father`s Name / Husband Name </strong></th>
                            <th width="350" align="center"><strong> Address </strong></th>';
                            if($mobile_check == 'on'){
                                $html .='<th width="80" align="center"><strong> Mobile </strong></th>';
                            }
                            $html .='
                            <th width="80" align="center"><strong> DOB </strong></th>
                        </tr>
                    </thead>
                    <tbody>';
            
            $this->db->select('
                teacher.*, class_sec_hdr.class_sec class
            ');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.CS_SEQ = teacher.TCH_CS_SEQ');
            $this->db->where_in('class_sec_hdr.CS_SEQ', $classid);
            $this->db->where_in('teacher.DEPT_CODE', $department);
            $this->db->order_by('class_sec_hdr.class_order','ASC');
            $st_data = $this->db->get('teacher')->result_array();
            
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/teacher_related_report');
            }
            
            foreach ($st_data as $index => $st_data_val) {
                $html .= '<tr>
                    <td width="25" align="center">'.++$index.'</td>
                    <td width="50" align="center">'.$st_data_val['class'].'</td>
                    <td width="100" align="center">'.$st_data_val['TCH_NAME'].'</td>
                    <td width="80" align="center">'.$st_data_val['TCH_CAT'].'</td>
                    <td width="180" align="center">'.$st_data_val['TCH_FTH'].'</td>
                    <td width="350" align="center">'.strip_tags($st_data_val['TCH_ADDR'] ?? '').'</td>';
                    if($mobile_check == 'on'){
                        $html .= '<td width="80" align="center">'.(isset($st_data_val['TCH_PHONE']) ? $st_data_val['TCH_PHONE'] : '').'</td>';  
                    }
                    $html .= '<td width="80" align="center">'.(isset($st_data_val['TCH_DOB']) ? $st_data_val['TCH_DOB'] : '').'</td>';
                $html .= '</tr>';
            }
            
            $html .= '</tbody></table></div>';



            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
         if ($report_category == 'all_teacher_list') {
          
             
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('L', 'A4');
           
            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= '
                <table border="1">
                    <thead>
                    <tr>
                        <th width="25" align="center"><strong> SNo </strong></th>
                        <th  width="50" align="center"><strong> Class</strong></th>
                        <th  width="100" align="center"><strong> Teacher Name </strong></th>
                        <th  width="80" align="center"><strong> Category </strong></th>
                        <th width="180" align="center"><strong> Father`s Name / Husband Name </strong></th>
                        <th width="350" align="center"><strong> Address </strong></th>';
                        if($mobile_check == 'on'){
                            $html .='<th width="80" align="center"><strong> Mobile </strong></th>';
                        }
                         $html .='
                        <th width="80" align="center"><strong> DOB </strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        ';
                      
            
                       
                        

           $this->db->select('
                teacher.*,class_sec_hdr.class_sec class
            ');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.class_teacher = teacher.TCH_SRLNO','left');
            $this->db->where_in('teacher.DEPT_CODE', $department);
            $this->db->order_by('class_sec_hdr.class_order','ASC');
            $st_data = $this->db->get('teacher')->result_array();
//   echo $this->db->last_query();
//   die;
           
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/teacher_related_report');
            }
            $valtotal = 0;
            $reltotal = 0;
            
            
            foreach ($st_data as $index => $st_data_val) { 
                $html .= '<tr>
                            <td  width="25" align="center">'.++$index.'</td>
                            <td  width="50"  align="center"> '.$st_data_val['class'].'</td>
                            <td  width="100" align="center">'.$st_data_val['TCH_NAME'].'</td>
                            <td  width="80" align="center">'.$st_data_val['TCH_CAT'].'</td>
                             <td width="180" align="center">'.$st_data_val['TCH_FTH'].'</td>
                             <td width="350" align="center">'.strip_tags($st_data_val['TCH_ADDR'] ?? '').'</td>';
                             if($mobile_check == 'on'){
                                $html .='<td width="80" align="center">'.(isset($st_data_val['TCH_PHONE']) ? $st_data_val['TCH_PHONE'] : '').'</td>';
                            }
                             $html .='
                             <td width="80" align="center">'.(isset($st_data_val['TCH_DOB']) ? $st_data_val['TCH_DOB'] : '').'</td>';
                            
                            $html .= '
                           
                        </tr>';
            }
            $html .='</tbody></table>';


            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
        if ($report_category == 'pan_aadhar') {
          
             
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('L', 'A4');
           
            // echo "<pre>"; print_r($class_data); die();

            $html = '';
            $html .= '
                <table border="1">
                    <thead>
                    <tr>
                        <th width="25" align="center"><strong> SNo </strong></th>
                        <th  width="50" align="center"><strong> Class</strong></th>
                        <th  width="100" align="center"><strong> Teacher Name </strong></th>
                        <th width="180" align="center"><strong> Father`s Name / Husband Name </strong></th>
                        <th width="80" align="center"><strong> Bank Details </strong></th>
                        <th width="80" align="center"><strong> PF Details </strong></th>
                        <th width="80" align="center"><strong> Aadhar Details </strong></th>
                        <th width="80" align="center"><strong> PAN Details </strong></th>
                        ';
                        if($mobile_check == 'on'){
                            $html .='<th width="80" align="center"><strong> Mobile </strong></th>';
                        }
                         $html .='
                        <th width="80" align="center"><strong> DOB </strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        ';
                      
            
                       
                        

           $this->db->select('
                teacher.*,class_sec_hdr.class_sec class
            ');
            $this->db->join('class_sec_hdr', 'class_sec_hdr.class_teacher = teacher.TCH_SRLNO','left');
            //$this->db->where_in('class_sec_hdr.CS_SEQ', $classid); 
            $this->db->where_in('teacher.DEPT_CODE', $department);
            $this->db->order_by('class_sec_hdr.class_order','ASC');
            $st_data = $this->db->get('teacher')->result_array();
//   echo $this->db->last_query();
//   die;
           
            if (empty($st_data)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/teacher_related_report');
            }
            $valtotal = 0;
            $reltotal = 0;
            
            
            foreach ($st_data as $index => $st_data_val) {
                $html .= '<tr>
                            <td  width="25" align="center">'.++$index.'</td>
                            <td  width="50"  align="center"> '.$st_data_val['class'].'</td>
                            <td  width="100" align="center">'.$st_data_val['TCH_NAME'].'</td>
                             <td width="180" align="center">'.$st_data_val['TCH_FTH'].'</td>
                             <td  width="80" align="center">'.$st_data_val['TCH_BANK_ACCNO'].'</td>
                             <td  width="80" align="center">'.$st_data_val['TCH_PF_ACCNO'].'</td>
                             <td  width="80" align="center">'.$st_data_val['TCH_AADHAR'].'</td>
                             <td  width="80" align="center">'.$st_data_val['TCH_PAN'].'</td>
                            ';
                             if($mobile_check == 'on'){
                                $html .='<td width="80" align="center">'.(isset($st_data_val['TCH_PHONE']) ? $st_data_val['TCH_PHONE'] : '').'</td>'; 
                            }
                             $html .='
                             <td width="80" align="center">'.(isset($st_data_val['TCH_DOB']) ? $st_data_val['TCH_DOB'] : '').'</td>';
                            
                            $html .= '
                           
                        </tr>';
            }
            $html .='</tbody></table>';


            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        }
         $pdf->Output($doc_name . '.pdf', 'I');
    }
     public function print_staff_leave_report(){
        $staff = $this->input->post('staff');
        $leave_category = $this->input->post('leave_category');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        
        $this->db->select('*'); 
        $this->db->from('staff_leave'); 
        $this->db->join('teacher', 'staff_leave.staff_id = teacher.tch_srlno', 'inner');   
        if($staff !== ''){
            $this->db->where("staff_leave.staff_id",$staff);
        }
        if($leave_category !== ''){
            $this->db->where("staff_leave.leave_category",$leave_category);
        }
        if ($from_date !== '') {
            $this->db->where("staff_leave.from_date >=", $from_date);
        }
        
        if ($to_date !== '') {
            $this->db->where("staff_leave.to_date <=", $to_date);
        }
        $query = $this->db->get(); 
        
 
        $records = $query->result();
        $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
         $class = array('all');
        $company = $this->company_name($class);
        $report_type_hdr = 'Staff Leave Report';
        // die('ok');
        // set document information
        $doc_name = $report_type_hdr.'_'.date('d-m-Y_h-i-A');
        $pdf->SetCreator(PDF_CREATOR);

        $pdf->SetTitle($doc_name);
        $pdf->SetSubject('Student Strength Report');
        $pdf->SetKeywords('All Transaction Report, smg, developed by: https://sketchmeglobal.com');


        $pdf->SetAuthor($company->COM_NAME);

        // set default header data
        $html_header = <<<EOD
        <div style="text-align:center;">
        <span style="font-size: 25px;"><strong>$company->COM_NAME</strong></span>
        <br>
        $company->COM_ADD1, $company->COM_ADD2
        <br>
        <strong><span style="background-color: black;color: white;"> Staff Leave Report </span></strong>  
        <br>
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

        $pdf->setPrintFooter(false);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // -----------------------------------------------------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);
       
          
             
             //$pdf->SetAutoPageBreak(TRUE, 1);
            $pdf->SetFont('times', '', 9, '', true);
            $pdf->AddPage('p', 'A4');
           
            // echo "<pre>"; print_r($class_data); die();

            $html = '<div style="display: flex; justify-content: center; align-items: center; padding: 20px;">';
            $html .= '
                <table border="1" style="margin: 0 auto; width: 100%; max-width: 1200px;">
                    <thead>
                        <tr>
                            <th width="100" align="center"><strong> SNo </strong></th>
                            <th width="300" align="center"><strong> Staff</strong></th>
                            <th width="100" align="center"><strong> Leave Category </strong></th>
                            <th width="100" align="center"><strong> From date </strong></th>
                            <th width="100" align="center"><strong> To Date </strong></th>
                          
                        </tr>
                    </thead>
                    <tbody>';
            
           
            
            if (empty($records)) {
                $this->session->set_flashdata('type', 'error');
                $this->session->set_flashdata('title', 'Naa!');
                $this->session->set_flashdata('msg', 'Something went wrong');
                return array('type' => 'redirect', 'page'=>'admin/staff_related_report');
            }
           
            $i=0;
            foreach ($records as  $row) {
                $i++;
                $html .= '<tr>
                    <td width="100" align="center">'.$i.'</td>
                    <td width="300" align="center">'.$row->TCH_NAME.'</td>
                    <td width="100" align="center">'.$row->leave_category.'</td>
                    <td width="100" align="center">'.date('d-M-Y',strtotime($row->from_date)).'</td>
                    <td width="100" align="center">'.date('d-M-Y',strtotime($row->to_date)).'</td>
                  
                </tr>';
            }
            
            $html .= '</tbody></table></div>';



            $pdf->writeHTMLCell(135, 0, 5, 28, $html, 0, 1, 0, true, '', true);
        
        
         $pdf->Output($doc_name . '.pdf', 'I');
    }


} // /.Reports_m model