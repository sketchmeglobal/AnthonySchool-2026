<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
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
        $data['book_master'] = $this->db->get_where('book_master')->result_array();
        $data['class_sec_hdr'] = $this->db->get_where('class_sec_hdr')->result_array();
        $data['student_details'] = $this->db->get_where('student_details')->result_array();
        $data['tab_title'] = 'Reports';
        $data['section_heading'] = 'Reports <small>(Add)</small>';
        $data['menu_name'] = 'Reports';

        return array('type' => 'load_view', 'page' => 'Reports_v', 'data' => $data);
    }


    public function generate_details_print_format() {
        $filter_book_id = $this->input->post('filter_book_id');
        $filter_class_id = $this->input->post('filter_class_id');
        $filter_student_id = $this->input->post('filter_student_id');
        $returned = $this->input->post('returned');
        $with_fine = $this->input->post('with_fine');
        $this->db->join('book_master','book_master.BOOK_SEQ = book_issued.BOOK_SEQ','left');
        $this->db->join('student_details','student_details.STD_SEQ = book_issued.STD_SEQ','left');
        $this->db->join('class_sec_hdr','class_sec_hdr.CS_SEQ = student_details.STD_CS_SEQ','left');
        if($filter_book_id != '') {
        $this->db->where('book_issued.BOOK_SEQ', $filter_book_id);
        }
        if($filter_class_id != '') {
        $this->db->where('student_details.STD_CS_SEQ', $filter_class_id);
        }
        if($filter_student_id != '') {
        $this->db->where('student_details.STD_SEQ', $filter_student_id);
        }
        if($returned == 1) {
        $this->db->where('returned_or_not', 1);    
        } elseif($returned == 0) {
        $this->db->where('returned_or_not', 0);    
        }
        $rs = $this->db->get_where('book_issued', array('book_issued.status' => 1))->result();
        $data['fine'] = $with_fine;
        $data['result'] = $rs;
        $data['print_section'] = 'details_report';
        return array('type' => 'load_view', 'page' => 'common_print_v', 'data' => $data);
    }


} // /.Reports_m model