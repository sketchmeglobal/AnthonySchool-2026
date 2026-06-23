<?php
// echo '<pre>',print_r($student_details),'<pre>';

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

function fetch_grade($marks_obtained, $total_marks,$class){
    if($class == 'IX' || $class == 'X'){
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

function fetch_attendance($term_seq, $std_seq){
    $CI = & get_instance();
    $rv = $CI->db
        ->get_where('progress_report_entry', array('EXAM_TERM' => $term_seq,'STUDENT' => $std_seq))
        ->result();
    if(count($rv) == 0){
        return '-';
    } else {
        $total_working_days = $CI->db->get_where('exam_terms',array('et_id' => $term_seq))->row()->total_working_days;
        return $rv[0]->TOTAL_ATTENDANCE . '/' . $total_working_days;
    }
}

function fetch_general_remarks($term_seq, $std_seq){
    $CI = & get_instance();
    $rv = $CI->db
        ->get_where('progress_report_entry', array('EXAM_TERM' => $term_seq,'STUDENT' => $std_seq))
        ->result();
    if(count($rv) == 0){
        return '-';
    } else {
        return $rv[0]->GENERAL_REMARKS;
    }
}

function fetch_charcter_grade($term_seq, $std_seq, $title){
    $CI = & get_instance();
    $rv = $CI->db
        ->get_where('progress_report_entry', array('EXAM_TERM' => $term_seq,'STUDENT' => $std_seq))
        ->result();
    if(count($rv) == 0){
        return '-';
    } else {
        $field = 'GRADE_'.$title;
        return $rv[0]->$field;
    }
}

function fetch_marks_total($std_seq, $test_seq, $term_seq){
    $CI = & get_instance();
    $rv = $CI->db
        ->select('SUM(MD_MARKS) AS total_marks')
        ->group_by('MD_TERM_SEQ,MD_TEST_SEQ,MD_STD_SEQ')
        ->get_where('marks_dtl',
            array(
                'MD_TEST_SEQ' => $test_seq,
                'MD_TERM_SEQ' => $term_seq,
                'MD_STD_SEQ' => $std_seq)
        )
        ->row();
        //echo $CI->db->last_query();
    if(count($rv) == 0){
        return '-';
    } else{
        return ($rv->total_marks < 10) ? '0'. $rv->total_marks : $rv->total_marks;
    }
}

function fetch_sig_status($std_seq, $test_seq, $term_seq){
    $CI = & get_instance();
    $rv = $CI->db
        ->where_in('MD_TEST_SEQ', $test_seq)
        ->get_where('marks_dtl',
            array(
                'MD_TERM_SEQ' => $term_seq,
                'MD_STD_SEQ' => $std_seq
            )
        )->result();
    if(count($rv) == 0){
        return false;
    } else{
        return true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
    <meta name="description" content="Paper Css reports">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400&display=swap" rel="stylesheet">
    <style>
        input[type="text"],input[type="password"],textarea {width: 100%;border: none;outline: none;border-bottom: 2px dashed #b21414;font-size: 14px;font-weight: bold;padding-left: 5px;color: #b21414;text-align: center;}
        section{margin:auto!important}
        label{color: #b21414;}
        table.table { font-family: 'Oswald', sans-serif;border-collapse: collapse;font-size: 12px;}

        .table td, .table th { border: 1px solid #000; text-align: left; padding: 0.2px;}
        .table th{font-size: 11px;text-align: center;font-weight: lighter}
        .table td{font-family: 'Open Sans', sans-serif;text-align: center;}
        .table tr td:first-child{font-weight:bold;text-align: left;padding-left: 5px}

        .left_table{width:128mm; float:left}
        .left_table tr th:first-child{width:125px}
        .right_table{width:128mm; float:right}
        .right_table tr td:first-child{padding: 0px 5px}
        .right_table tr td strong{font-size: 11px;}

        .padding-5mm{padding:5mm}
        .gradation{font-weight: bold;text-align: center!important;font-size:11px}
        .text-center {text-align: center;}
        .text-justify {text-align: justify;}
        .heading-text-style {color: #b21414;}
        .border-bottom {border-bottom: 2px solid;}
        .para-style {color: #f83030d4;font-size: 1.1rem;line-height: 1.25rem;}
        .main-div {border: 2px solid;border-radius: 10px;}
        .parent-p {padding: 0px 10px;}
        .font-weight-bld {font-weight: bolder;font-size: 1.1rem;}
        .img-fluid {max-width: 100%;display: block;}
        .d-flex {display: flex;justify-content: center;}
        .k2-style {color: #aeae80;font-size: 1.4rem;padding: 0px;margin: 0px;}
        .parent-form {border: 3px solid #b21414;padding: 0px 5px;}
        .signature{width: 100%;border: none;outline: none;border: 2px solid #b21414 !important;}
        .flex-display{display: flex;}
        .m-0{margin: 0}
        .my-3{margin: 12px 0px;}
        .mt-3{margin-top: 20px}
        .mb-3{margin-bottom: 20px}
        .mb-2{margin-bottom: 10px}
        .form-side-padding{padding: 0px 5px;margin-bottom: 11px;}
    </style>
</head>

    <body class="A4 landscape">

    

        <section class="sheet padding-15mm">
            <div class="body-content">
               
                <div class="table right_table">
                    <div class="main-div">
                        <div class="">
                            <h3 class="text-center heading-text-style" style="margin-bottom:0;font-size:20px;letter-spacing: 1px;">ST. ANTHONY'S HIGH SCHOOL</h3>
                            <h4 class="text-center heading-text-style font-weight-bld" style="margin-top:0;font-size: 0.95rem;">
                                (HIGHER SECONDARY)
                                <br>
                                19, MARKET STREET, KOLKATA-700 087
                            </h4>
                        </div>
                        <div class="d-flex" style="margin: 20px 0;">
                            <img style="height:132px" class="img-fluid" src="<?=base_url()?>/assets/img/favicon.ico" alt=""><!--width="100"-->
                        </div>
                        <div>
                            <h3 class="text-center m-0 heading-text-style">PROGRESS REPORT</h3>
                            <h3 class="text-center k2-style"><?=CURRENT_YEAR?></h3>
                            <h3 class="text-center m-0 heading-text-style">CLASS <?=$class . ' - ' . $sec?></h3>
                        </div>
                        <div class="form-side-padding">
                            <div class="parent-form">
                                <form action="#" method="post">
                                    <div class="flex-display my-3">
                                        <label for="name1">Name</label>
                                        <input type="text" id="name1" value="<?=$sd['STD_FNAME'] . ' ' . $sd['STD_MNAME'] . ' ' . $sd['STD_LNAME']?>">
                                    </div>
                                    <div class="flex-display my-3">

                                        <label for="name2">Class</label>
                                        <input type="text" id="name2" value="<?=$class?>">
                                        <label for="name3">Sec</label>
                                        <input type="text" id="name3" value="<?=$sec?>">
                                    </div>
                                    <div class="flex-display my-3">
                                        <label for="name4">Roll</label>
                                        <input type="text" id="name4" value="<?=$sd['STD_ROLLNO']?>">
                                        <label for="name5">Reg</label>
                                        <input type="text" id="name5" value="<?=$sd['STD_REGNO']?>">
                                    </div>
                                    <div class="flex-display my-3">
                                        <label for="name6">Parent/Guardian's Name</label>
                                        <input type="text" id="name6" value="<?=$sd['STD_FTH_NAME']?>">
                                    </div>
                                    <!-- <div class="flex-display my-3">
                                        <label for="name7">Address</label>
                                        <input type="text" id="name7">
                                    </div> -->
                                    <div class="flex-display my-3">
                                        <label for="name8">Phone</label>
                                        <input type="text" id="name8" value="<?=$sd['STD_PH_NO']?>">
                                    </div>
                                    <div class="flex-display my-3">
                                        <label for="name9">Specimen Signature of Parent/Guardian</label>
                                        <input style="height: 35px" class="signature" type="text" id="name9">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="main-div" style="margin-top: 8px;text-align: center;">
                        <label>Class Teacher For <?=$class . '-' . $sec?></label><br>
                        <div style="height: 40px;"></div>
                        <label class="text-center"><b><?= $class_teacher ?></b></label>
                    </div>
                </div>
            </div>
        </section>

        

    <!-- Placed js at the end of the document so the pages load faster -->
    <script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
    <script></script>
    </body>
   
    


</html>