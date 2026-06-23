<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 30-09-2020
 * Time: 11:53
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="Pran Krishna Das">
    <link href="<?=base_url();?>assets/img/favicon.ico" rel="shortcut icon" type="image/png">

    <title><?=$tab_title.' | '.WEBSITE_NAME;?></title>
    <!-- Normalize or reset CSS with your favorite library -->
    <link href="<?=base_url();?>assets/admin_panel/css/normalize.min.css" rel="stylesheet">
    <!-- Load paper.css for happy printing -->
    <link href="<?=base_url();?>assets/admin_panel/css/paper.css" rel="stylesheet">
    <link href="<?=base_url();?>assets/admin_panel/css/fonts.googleapis.css" rel="stylesheet">

    <link href="https://fonts.cdnfonts.com/css/coronet" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/monotype-corsiva" rel="stylesheet">
    <?php $this->load->view('components/_common_head'); //left side menu ?>
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Lato:wght@300&family=Pacifico&display=swap');
        @font-face {
            font-family: 'Mistral';
            font-style: normal;
            font-weight: normal;
            src: local('Mistral'), url(<?=FCPATH?>'assets/admin_panel/fonts/MISTRAL.woff') format('woff');
        }
    </style>

    <!-- common head -->
    <!-- /common head -->
    <style>
        .input_ruler {
            border-bottom: 1px solid darkgrey;
            padding-left: 0px;
        }
        .custom-col-5{width: 20%;width: 20%;float: left;padding-left: 15px;padding-right: 15px;}
        hr {
    margin-top: 20px;
    margin-bottom: 20px;
    border: 0;
    border-top: 1px solid #262222;
}
        .mb-1{margin-bottom: 1rem;}

        @media print{
            .col-sm-1 { width: 8.33333333%!important; float:left!important;}
            .col-sm-2 { width: 16.66666667%!important; float:left!important; }
            .col-sm-3 { width: 25%!important; float:left;!important; }
            .col-sm-4 { width: 33.33333333%%;!important; float:left;!important; }
            .col-sm-5 { width: 41.66666667%!important; float:left;!important; }
            .col-sm-6 { width: 50%!important; float:left!important; }
            .col-sm-7 { width: 58.33333333%!important; float:left;!important; }
            .col-sm-8 { width: 66.66666667%;!important; float:left;!important; }
            .col-sm-9 { width: 75%!important; float:left;!important; }
            .col-sm-10 { width: 83.33333333%;!important; float:left;!important; }
            .col-sm-11 { width: 91.66666667%;!important; float:left;!important; }
            .col-sm-12 { width: 100%!important; float:left;!important; }

            .col-sm-offset-2 { margin-left: 16.66666667%;!important; }
            .col-sm-offset-8 { margin-left: 66.66666667%;!important; }
            .custom-col-5{width: 20%;width: 20%;float: left;padding-left: 15px;padding-right: 15px;}
            .mb-1{margin-bottom: 1rem;}
            .text-red {
            color: red!important;
        }
#red_td {
    background-color: #fbb6b6!important;
    color: red!important;
        }
        }
    </style>

    <!--table to excel-->
    <style type="text/css">
        @media print{
            .print-me{display: none}
        }
    </style>
</head>


<?php
//if $print_section not set
if(!isset($print_section)) {
    ?>
    <body>
    <div class="col-sm-12">
        <div class="row">
            <h1 class="text-warning text-center" style="padding-top: 50px;">
                <strong>No Data!</strong>
            </h1>
        </div>
    </div>
    </body>
    <?php
}

elseif($print_section == 'leaving_certificate') {
    ?>
    <body class="A4" onload="window.print()">
    <style>
        .height40{height:40px}
        .border-all{border: 1px solid #000}.border-bottom{border-bottom: 1px solid #000}
        .m-0{margin:0}

        @media print {
            @page {
            size: 210mm 297mm;
            }
        }
        u{text-underline-offset: 12px;}

    </style>

    <?php
    foreach($std_row as $std) {
        ?>
        <!-- Each sheet element should have the class "sheet" -->
        <section class="sheet padding-10mm">
            <div class="row">
                <div class="col-sm-12 text-left">
                    <b>No. 
                        <u> 
                        <?php 
                        if($class_type == 1){
                            echo PREFIX_NUR_KG . $certificate_no;
                        } else if($class_type == 2){
                            echo PREFIX_PRIMARY . $certificate_no;
                        } else if($class_type == 3){
                            echo PREFIX_HIGH . $certificate_no;
                        }else{
                            echo PREFIX_HIGHER . $certificate_no;
                        }
                        ?> 
                        </u>
                    </b>
                </div>
                <br><br>
                <div class="col-sm-2 text-left">
                    <?php
                    if($classes[0]->Class_Type == 1){
                        $imgsrc = base_url('assets/img/SCHOOL_LOGO_NUR.jpg'); 
                    }else{
                        $imgsrc = base_url('assets/img/SCHOOL_LOGO.jpg'); 
                    }
                    ?>
                    <img src="<?=$imgsrc?>" class="" alt="Logo" width="100px" height="100px" >
                </div>

                <div class="col-sm-8 text-center">
                    <p style="font-size: 31px"><strong><?=$company->COM_NAME;?></strong></p>
                    <?php
                        if($classes[0]->Class_Type == 4){
                            ?>
                            <p style="margin: 5px;font-size: 20px;"><strong>(Higher Secondary)</strong></p>
                            <p style="margin: 5px;font-size: 20px;"><strong>19, Market Street, Kolkata - 700 087</strong></p>
                            <p style="margin: 5px;font-size: 20px;"><strong>Institution Code (H.S.): 101385</strong></p>
                            <?php
                        }else{
                            ?>
                            <p class="text-center" style="font-size: 15px">
                                <strong>19, MARKET STREET, KOLKATA - 700 087</strong><br/>
                                <?php if($classes[0]->Class_Type != 1){
                                    echo "Recognition continues with Boards Letter No. and <br/>
                                        date 31.12.70. No. 1370G/092-110 dated 3.2.69 <br/>";
                                }else{
                                    echo "<span style='margin-top:9px;display:block'></span>";
                                } 
                                ?>
                                
                                <!-- (West Bengal Board of Sec. Education. Index No. 170-110) -->
                            </p>        
                            <?php
                        }
                    ?>
                    
                </div>
            </div>
            <br/>

            <div class="row" style="font-family: 'Lato', sans-serif;">
                <div class="col-sm-12">
                    <p class="text-center" style="font-family: 'Monotype Corsiva', sans-serif;font-size: 40px"><strong><u>Transfer Certificate</u></strong></p>
                    <br/><br/>
                    <p class="text-justify" style="font-size: 20px; clear:both">
                        <span style="float:left;">This is to certify that</span> 
                        <span style="float:right;width: 140mm;border-bottom: 2px dotted;"><strong><?= $std->STD_FNAME.' '.$std->STD_MNAME.' '.$std->STD_LNAME ?></strong></span>
                    </p>
                    <p class="text-justify" style="font-size: 20px; clear:both">    
                        <br/>
                        <span style="float:left;">son of </span> 
                        <span style="float:right;width: 174mm;border-bottom: 2px dotted;"><strong><?= $std->STD_FTH_NAME ?></strong></span>
                    </p>
                    <p class="text-justify" style="font-size: 20px; clear:both">        
                        <br/>
                        <span style="float:left;">an inhabitant of </span> 
                        <span style="float:right;width: 152mm;border-bottom: 2px dotted;"><strong><?= $std->STD_ADDR_0 ?></strong></span>
                    </p>
                    <p class="text-justify" style="font-size: 20px; clear:both">        
                        <br/>
                        <span style="float:left;">in the District of</span> 
                        <?php if(isset($form_data)){
                            ?>
                            <span style="float:right;width: 151mm;border-bottom: 2px dotted;"><strong><?=$form_data['district']?></strong></span>
                            <?php
                        } else {
                            ?>
                            <span style="float:right;width: 151mm;border-bottom: 2px dotted;"><strong><?=$district?></strong></span>
                            <?php
                        } ?>
                        
                    </p>
                    <p class="text-justify" style="font-size: 20px; clear:both">        
                        <br/>
                        <span style="float:left;">was in Class</span> 
                        <span style="float:right;width: 160mm;border-bottom: 2px dotted;"><strong><?= $classes[0]->Class_Name.' '.$classes[0]->Sec_Name ?></strong></span>
                    </p>
                    <p class="text-justify" style="font-size: 20px; clear:both">        
                        <br/>
                        <span style="float:left;">up to</span> 
                        <?php if(isset($form_data)){
                            ?>
                        <span style="float:right;width: 175mm;border-bottom: 2px dotted;"><strong><?=date('d-m-Y', strtotime($form_data['upto']))?></strong></span>
                            <?php
                        } else {
                            ?>
                            <span style="float:right;width: 175mm;border-bottom: 2px dotted;"><strong><?=date('d-m-Y', strtotime($upto))?></strong></span>
                            <?php
                        } ?>
                        
                    </p>
                    <p class="text-justify" style="font-size: 20px; clear:both">        
                        <br/>
                        <span style="float:left;">His date of birth, according to the Admission Register, is</span> 
                        <span style="float:right;width: 63mm;border-bottom: 2px dotted;"><strong><?= date ('d-m-Y', strtotime($std->STD_DOB)); ?></strong></span>
                    </p>
                    <p class="text-justify" style="font-size: 20px; clear:both">        
                        <br/>
                        All sums due to the School on his account have been paid.
                    </p>
                    <p class="text-justify" style="font-size: 20px; clear:both">        
                        <br/>
                        <span style="float:left;">Reason for leaving</span> 
                        <?php if(isset($form_data)){
                            ?>
                            <span style="float:right;width: 145mm;border-bottom: 2px dotted;"><strong><?=$form_data['reason_for_leaving']?></strong></span>
                            <?php
                        } else {
                            ?>
                            <span style="float:right;width: 145mm;border-bottom: 2px dotted;"><strong><?=$reason_for_leaving?></strong></span>
                            <?php
                        } ?>                        
                    </p>
                    <p class="text-justify" style="font-size: 20px; clear:both">        
                        <br/>
                        <span style="float:left;">Remarks as to Character and Conduct</span> 
                        <?php if(isset($form_data)){
                            ?>
                            <span style="float:right;width: 385px;border-bottom: 2px dotted"><strong><?=$form_data['certificate_remarks']?></strong></span>
                            <span style="float:right;width: 100%;border-bottom: 2px dotted;margin-top:30px"><strong></strong></span>
                            <?php
                        } else {
                            ?>
                            <span style="float:right;width: 385px;border-bottom: 2px dotted"><strong><?=$certificate_remarks?></strong></span>
                            <span style="float:right;width: 100%;border-bottom: 2px dotted;margin-top:30px"><strong></strong></span>
                            <?php
                        } ?>
                    </p>
                    <p class="text-justify" style="font-size: 20px; clear:both">        
                        <br/>
                        <span style="float:left;">Promotion has been</span> 
                        <?php if(isset($form_data)){
                            ?>
                            <span style="float:right;width: 142mm;border-bottom: 2px dotted;"><strong><?=$form_data['promotion_has_been']?></strong></span>
                            <?php
                        } else {
                            ?>
                            <span style="float:right;width: 142mm;border-bottom: 2px dotted;"><strong><?=$promotion_has_been?></strong></span>
                            <?php
                        } ?>
                    </p>
                </div>
            </div>

            <br/><br/><br/><br/><br/>

            <div class="row" style="font-family: 'Lato', sans-serif;font-size: 20px">
                <div class="col-sm-5">
                    Date 
                    <?php if(isset($form_data)){
                        ?>
                        <u><strong><?=date('d-m-Y', strtotime($form_data['certificate_date']))?></strong></u><br/>
                        <?php
                    } else {
                        ?>
                        <u><strong><?=date('d-m-Y', strtotime($certificate_date))?></strong></u><br/>
                        <?php
                    } ?>
                </div>
                <div class="col-sm-7 text-center">
                    <span style="text-decoration: overline;">Headmaster</span>
                </div>
            </div>
        </section>
        <?php
    }
    ?> 
    </body>
    <?php
}
elseif($print_section == 'character_certificate') {
    ?>
    <body class="A4" onload="window.print()">
    <style>
        .height40{height:40px}
        .border-all{border: 1px solid #000}.border-bottom{border-bottom: 1px solid #000}
        .m-0{margin:0}

        @media print {
            @page {
            size: 210mm 297mm;
            }
        }
        u{text-underline-offset: 12px;}

    </style>

    <?php
    foreach($std_row as $std) {
        ?>
        <!-- Each sheet element should have the class "sheet" -->
        <section class="sheet padding-10mm">
            <div class="row">
                <div class="col-sm-12 text-left">
                    <b>No. 
                        <u>
                        <?php 
                        if($class_type == 1){
                            echo PREFIX_NUR_KG . $certificate_no;
                        } else if($class_type == 2){
                            echo PREFIX_PRIMARY . $certificate_no;
                        } else if($class_type == 3){
                            echo PREFIX_HIGH . $certificate_no;
                        }else{
                            echo PREFIX_HIGHER . $certificate_no;
                        }
                        ?> 
                        </u>
                    </b>
                </div>
                <br><br>
                
                <div class="col-sm-2 text-left">
                    <?php
                    if($classes[0]->Class_Type == 1){
                        $imgsrc = base_url('assets/img/SCHOOL_LOGO_NUR.jpg'); 
                    }else{
                        $imgsrc = base_url('assets/img/SCHOOL_LOGO.jpg'); 
                    }
                    ?>
                    <img src="<?=$imgsrc?>" class="" alt="Logo" width="100px" height="100px" >
                </div>

                <div class="col-sm-8 text-center">
                    <p style="font-size: 31px"><strong><?=$company->COM_NAME;?></strong></p>
                    <?php
                    if($classes[0]->Class_Type == 4){
                            ?>
                            <p style="margin: 5px;font-size: 20px;"><strong>(Higher Secondary)</strong></p>
                            <p style="margin: 5px;font-size: 20px;"><strong>19, Market Street, Kolkata - 700 087</strong></p>
                            <p style="margin: 5px;font-size: 20px;"><strong>Institution Code (H.S.): 101385</strong></p>
                            <?php
                        }else{
                            ?>
                            <p class="text-center" style="font-size: 15px">
                                <strong>19, MARKET STREET, KOLKATA - 700 087</strong><br/>
                                <?php if($classes[0]->Class_Type != 1){
                                    echo "Recognition continues with Boards Letter No. and <br/>
                                        date 31.12.70. No. 1370G/092-110 dated 3.2.69 <br/>";
                                }else{
                                    echo "<span style='margin-top:9px;display:block'></span>";
                                } 
                                ?>
                                <!-- (West Bengal Board of Sec. Education. Index No. 170-110) -->
                            </p>
                    <?php } ?>
                </div>
            </div>
            <br/>

            <div class="row" style="font-family: 'Lato', sans-serif;">
                
                
                <div class="col-sm-12">
                        <p class="text-center" style="font-family: 'Monotype Corsiva', sans-serif;font-size: 40px"><strong><u>Character Certificate</u></strong></p>
                        <br/>
                        <p class="text-justify" style="font-size: 20px; clear:both">
                            <span style="float:left;">This is to certify that</span> 
                            <span style="float:right;width: 140mm;border-bottom: 2px dotted;"><strong><?= $std->STD_FNAME.' '.$std->STD_MNAME.' '.$std->STD_LNAME ?></strong></span>
                        </p>
                        <p class="text-justify" style="font-size: 20px; clear:both">    
                            <br/>
                            <span style="float:left;">son of </span> 
                            <span style="float:right;width: 174mm;border-bottom: 2px dotted;"><strong><?= $std->STD_FTH_NAME ?></strong></span>
                        </p>
                        <p class="text-justify" style="font-size: 20px; clear:both">        
                            <br/>
                            <span style="float:left;">an inhabitant of </span> 
                            <span style="float:right;width: 152mm;border-bottom: 2px dotted;"><strong><?= $std->STD_ADDR_0 ?></strong></span>
                        </p>
                        <p class="text-justify" style="font-size: 20px; clear:both">        
                            <br/>
                            <span style="float:left;">in the District of</span> 
                            <?php if(isset($form_data)){
                                ?>
                                <span style="float:right;width: 151mm;border-bottom: 2px dotted;"><strong><?=$form_data['district']?></strong></span>
                                <?php
                            } else {
                                ?>
                                <span style="float:right;width: 151mm;border-bottom: 2px dotted;"><strong><?=$district?></strong></span>
                                <?php
                            } ?>
                        </p>
                        <p class="text-justify" style="font-size: 20px; clear:both">        
                            <br/>
                            <span style="float:left;">was in Class</span> 
                            <span style="float:right;width: 160mm;border-bottom: 2px dotted;"><strong><?= $classes[0]->Class_Name.' '.$classes[0]->Sec_Name ?></strong></span>
                        </p>
                        <p class="text-justify" style="font-size: 20px; clear:both">        
                            <br/>
                            <span style="float:left;">up to</span> 
                            <?php if(isset($form_data)){ ?>
                                <span style="float:right;width: 175mm;border-bottom: 2px dotted;"><strong><?=date('d-m-Y', strtotime($form_data['upto']))?></strong></span>
                            <?php } else{ ?>
                                <span style="float:right;width: 175mm;border-bottom: 2px dotted;"><strong><?=date('d-m-Y', strtotime($upto))?></strong></span>
                            <?php } ?>

                        </p>
                        <p class="text-justify" style="font-size: 20px; clear:both">        
                            <br/>
                            <span style="float:left;">His date of birth, according to the Admission Register, is</span> 
                            <span style="float:right;width: 63mm;border-bottom: 2px dotted;"><strong><?= date ('d-m-Y', strtotime($std->STD_DOB)); ?></strong></span>
                        </p>
                        <p class="text-justify" style="font-size: 20px; clear:both">        
                            <br/>
                            All sums due to the School on his account have been paid.
                        </p>
                        <p class="text-justify" style="font-size: 20px; clear:both">        
                            <br/>
                            <span style="float:left;">Remarks as to Character and Conduct</span> 
                            <?php if(isset($form_data)){ ?>
                                <span style="float:right;width: 385px;border-bottom: 2px dotted"><strong><?=$form_data['certificate_remarks']?></strong></span>
                                <span style="float:right;width: 100%;border-bottom: 2px dotted;margin-top:30px"><strong></strong></span>
                            <?php } else{ ?>
                                <span style="float:right;width: 385px;border-bottom: 2px dotted"><strong><?=$certificate_remarks?></strong></span>
                                <span style="float:right;width: 100%;border-bottom: 2px dotted;margin-top:30px"><strong></strong></span>
                            <?php } ?>
                            
                        </p>
                        <p class="text-justify" style="font-size: 20px; clear:both">        
                            <br/>
                            <span style="float:left;">Promotion has been</span> 
                            <?php if(isset($form_data)){ ?>
                                <span style="float:right;width: 142mm;border-bottom: 2px dotted;"><strong><?=$form_data['promotion_has_been']?></strong></span>
                            <?php } else{ ?>
                                <span style="float:right;width: 142mm;border-bottom: 2px dotted;"><strong><?=$promotion_has_been?></strong></span>
                            <?php } ?>                            
                        </p>
                    </div>
                
            </div>

            <br/><br/><br/><br/><br/>

            <div class="row" style="font-family: 'Lato', sans-serif;font-size: 20px">
                <div class="col-sm-5">
                    Date 
                    
                    <?php if(isset($form_data)){ ?>
                        <u><strong><?=date('d-m-Y', strtotime($form_data['certificate_date']))?></strong></u><br/>
                    <?php } else{ ?>
                        <u><strong><?=date('d-m-Y', strtotime($certificate_date))?></strong></u><br/>
                    <?php } ?>
                </div>
                <div class="col-sm-7 text-center">
                    <span style="text-decoration: overline;">Headmaster</span>
                </div>
            </div>
        </section>
        <?php
    }
    ?> 
    </body>
    <?php
}
elseif($print_section == 'general_letter') {
    ?>
    <body class="A4" onload="window.print()">
    <style>
        .height40{height:40px}
        .border-all{border: 1px solid #000}.border-bottom{border-bottom: 1px solid #000}
        .m-0{margin:0}

        @media print {
            @page {
            size: 210mm 297mm;
            }
        }
        u{text-underline-offset: 12px;}

    </style>

    <?php
    foreach($std_row as $std) {
        ?>
        <!-- Each sheet element should have the class "sheet" -->
        <section class="sheet padding-10mm" style="padding: 10mm 7mm;">
            <div class="row">
                <div class="col-sm-12 text-left">
                    <b>No. 
                        <u>
                        <?php 
                        if($class_type == 1){
                            echo PREFIX_NUR_KG . $certificate_no;
                        } else if($class_type == 2){
                            echo PREFIX_PRIMARY . $certificate_no;
                        } else if($class_type == 3){
                            echo PREFIX_HIGH . $certificate_no;
                        }else{
                            echo PREFIX_HIGHER . $certificate_no;
                        }
                        ?> 
                        </u>
                    </b>
                </div>
                <br><br>
                <div class="col-sm-2 text-left">
                    <?php
                    if($classes[0]->Class_Type == 1){
                        $imgsrc = base_url('assets/img/SCHOOL_LOGO_NUR.jpg'); 
                    }else{
                        $imgsrc = base_url('assets/img/SCHOOL_LOGO.jpg'); 
                    }
                    ?>
                    <img src="<?=$imgsrc?>" class="" alt="Logo" width="100px" height="100px" >
                </div>
<!--< ?php print_r($company) ?>-->
                <div class="col-sm-8 text-center">
                    <!--< ?php print_r($company->COM_NAME) ?>-->
                    <!--< ?=$company->COM_NAME;?>-->
                    <p style="font-size: 31px"><strong>ST. ANTHONY'S HIGH SCHOOL</strong></p>
                    <p class="text-center" style="font-size: 15px">
                        <strong>19, MARKET STREET, KOLKATA - 700 087</strong><br/>
                        <?php if($classes[0]->Class_Type != 1){
                            echo "Recognition continues with Boards Letter No. and <br/>
                                date 31.12.70. No. 1370G/092-110 dated 3.2.69 <br/>";
                        }else{
                            echo "<span style='margin-top:9px;display:block'></span>";
                        } 
                        ?>
                        
                        <!-- (West Bengal Board of Sec. Education. Index No. 170-110) -->
                    </p>
                </div>
            </div>
            <br/>

            <div class="row" style="font-family: 'Lato', sans-serif; font-size: 20px;line-height: 30px;">
                <div class="col-sm-12">
                    <p class="text-center"><u><strong>TO WHOMSOEVER IT MAY CONCERN</strong></u></p>
                    <br/>
                    <!--< ?=$company->COM_NAME;?>-->
                    <p class="text-justify">
                        This is to certify that <?= $std->STD_FNAME.' '.$std->STD_MNAME.' '.$std->STD_LNAME ?>
                        of class <?= $classes[0]->Class_Name.' '.$classes[0]->Sec_Name ?>,
                        son of <?= $std->STD_FTH_NAME ?>
                        of <?= $std->STD_ADDR_0 ?>
                        is a student of ST. ANTHONY'S HIGH SCHOOL, 19, Market Street, Kolkata - 700 087.
                        As per our school record his admission date <?= date ('d-m-Y', strtotime($std->STD_DOA)); ?>. 
                        His school registration no. <?= $std->STD_REGNO; ?>.
                        <br/><br/>
                        As per our school record his date of birth is <?= date ('d-m-Y', strtotime($std->STD_DOB)); ?>.
                        <br/><br/>
                        His/Her photograph affixed below is duly attested by me.
                    </p>
                    <br/><br/>
                    <div class="col-sm-6 text-right">
                        Attested
                        <br/>
                        <?=HEADMASTER?>
                        <br/>
                        Headmaster
                        <br/>
                       ST. ANTHONY'S HIGH SCHOOL
                    </div>
                    <div class="col-sm-6">
                        <!--<img src="< ?=base_url('assets/img/students')?>/< ?=$std->STD_IMAGE_PATH?>" class="img-rounded" width="112px" height="112px" >-->
                        <span style="display:block;height:130px;width:130px;border: 1px solid"></span>
                    </div>
                </div>
            </div>

            <br/><br/><br/><br/><br/>

            <div class="row" style="font-family: 'Lato', sans-serif;font-size: 20px">
                <div class="col-sm-5">
                    Date 
                    <?php if(isset($form_data)){ ?>
                        <u><strong><?=date('d-m-Y', strtotime($form_data['certificate_date']))?></strong></u><br/>
                    <?php } else{ ?>
                        <u><strong><?=date('d-m-Y', strtotime($certificate_date))?></strong></u><br/>
                    <?php } ?>
                </div>
                <div class="col-sm-7 text-center">
                    Headmaster
                </div>
            </div>
        </section>
        <?php
    }
    ?> 
    </body>
    <?php
}

?>

<script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
<script src="<?=base_url();?>assets/admin_panel/js/bootstrap.min.js"></script>
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery-migrate.js"></script>-->

<!--form validation-->
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery.validate.min.js" type="text/javascript"></script>-->
<!--form validation init-->
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/form-validation-init.js"></script>-->

<!-- common js -->
<?php $this->load->view('components/_common_js'); //left side menu ?>

<script type="text/javascript">
    //table to excel (single table)
    var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,'
            , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
            , base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
            , format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
        return function (table, name, filename) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }

            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = filename;
            document.getElementById("dlink").click();

        }
    })();


    //table to excel (multiple table)
    var array1 = new Array();
    var n = <?php if(isset($table_no)){echo $table_no;}else{echo 0;} ?>; //Total table
    for ( var x=1; x<=n; x++ ) {
        array1[x-1] = 'export_table_to_excel' + x;
    }
    var tablesToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,'
            , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets>'
            , templateend = '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>'
            , body = '<body>'
            , tablevar = '<table>{table'
            , tablevarend = '}</table>'
            , bodyend = '</body></html>'
            , worksheet = '<x:ExcelWorksheet><x:Name>'
            , worksheetend = '</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>'
            , worksheetvar = '{worksheet'
            , worksheetvarend = '}'
            , base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
            , format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
            , wstemplate = ''
            , tabletemplate = '';

        return function (table, name, filename) {
            var tables = table;
            var wstemplate = '';
            var tabletemplate = '';

            wstemplate = worksheet + worksheetvar + '0' + worksheetvarend + worksheetend;
            for (var i = 0; i < tables.length; ++i) {
                tabletemplate += tablevar + i + tablevarend;
            }

            var allTemplate = template + wstemplate + templateend;
            var allWorksheet = body + tabletemplate + bodyend;
            var allOfIt = allTemplate + allWorksheet;

            var ctx = {};
            ctx['worksheet0'] = name;
            for (var k = 0; k < tables.length; ++k) {
                var exceltable;
                if (!tables[k].nodeType) exceltable = document.getElementById(tables[k]);
                ctx['table' + k] = exceltable.innerHTML;
            }

            // window.location.href = uri + base64(format(allOfIt, ctx));

            document.getElementById("dlink").href = uri + base64(format(allOfIt, ctx));;
            document.getElementById("dlink").download = filename;
            document.getElementById("dlink").click();
        }
    })();
</script>

</html>
