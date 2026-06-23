<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 09-07-17
 * Time: xx:xx
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard | <?=WEBSITE_NAME;?></title>
    <meta name="keyword" content="user dashboard">
    <meta name="description" content="account statistic">

    <!-- common head -->
    <?php $this->load->view('components/_common_head'); //left side menu ?>
    <!-- /common head -->

    <!-- Start grocerycrud JS & STYLES -->
    <?php
    if(!empty($output)){
    foreach($css_files as $file):
        ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
    <?php
    endforeach;
    foreach($js_files as $file):
    ?>
        <script src="<?php echo $file; ?>"></script>
    <?php
    endforeach;
    }
    ?>
    <!--  End grocerycrud JS & STYLES  -->
</head>

<body class="sticky-header">

<section>
    <!-- sidebar left start (Menu)-->
    <?php $this->load->view('components/left_sidebar'); //left side menu ?>
    <!-- sidebar left end (Menu)-->

    <!-- body content start-->
    <div class="body-content" style="min-height: 1500px;">

        <!-- header section start-->
        <?php $this->load->view('components/top_menu'); ?>
        <!-- header section end-->

        <!-- page head start-->
        <div class="page-head">
            <h3>Dashboard</h3>
            <span class="sub-title">Welcome to <?=WEBSITE_NAME;?> dashboard</span>
        </div>
        <!-- page head end-->

        <!--body wrapper start-->
        <div class="wrapper">

            <?php
            //Student dashboard
            if($this->session->usertype == 4) {
                //show due alert (if any) after 20th of each month
                if(date('d') >= DUE_ALERT_DATE && ($due_month_names != '' || $yearly_fee_status == 'due')) {
                    ?>
                    <div class="alert alert-danger text-center" role="alert" style="font-size: 24px;">
                        <i class="fa fa-exclamation-triangle"></i>
                        <?php
                        //if both monthly and yearly fees are due
                        if ($due_month_names != '' && $yearly_fee_status == 'due') {
                            echo "Fees for the month(s) of <strong><u>$due_month_names</u></strong> is due, <strong><u>Annual fees</u></strong> are also due. Please pay the fee as soon as possible. Thank you.";
                        }
                        //if only monthly fees are due
                        elseif ($due_month_names != '') {
                            echo "Fees for the month(s) of <strong><u>$due_month_names</u></strong> is due. Please pay the fee as soon as possible. Thank you.";
                        }
                        //if only yearly fees are due
                        elseif ($yearly_fee_status == 'due') {
                            echo "<strong><u>Annual fees</u></strong> are due. Please pay the fee as soon as possible. Thank you.";
                        }
                        ?>
                    </div>
                    <?php
                }
            }
            ?>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            <?=$section_heading;?>
                        </header>
                        <div class="panel-body">
                            <?php echo $add_button; ?>
                            <?php echo $output; ?>
                        </div>
                    </section>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading text-center" style="font-size: 25px;">
                            <strong><u>Notice Board</u></strong>
                        </header>
                        <div class="panel-body">
                            <?php
                            $notice = $this->db->get('notices')->row()->notice;
                            echo $notice;
                            ?>
                        </div>
                    </section>
                </div>
            </div>
            <?php
            $date = date('Y-m-d');
            $this->db->where('TCH_DOB',$date);
            $teacher = $this->db->get('teacher')->row();
            if(!empty($teacher)){
                $teacher_name = $teacher->TCH_NAME;
                $saluation = $teacher->TCH_SALUTATION; 
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                           
                            <div class="panel-body">
                                
                                 
                                
                                  <div style="background-color: #fbfbf1; padding: 10px; border-radius: 5px; font-size: 18px; font-weight: bold; color: #2c3e50;text-align:center;">
                                    Today <?php echo $saluation?> <?php echo $teacher_name?>'s Birthday. <span style="color: #e74c3c;">Happy Birthday!</span>
                                </div>
                                  
                               
                               
                                
                            </div>
                        </section>
                    </div>
                </div>
                <?php
            }
            ?>
             

        </div>
        <!--body wrapper end-->


        <!--footer section start-->
        <?php $this->load->view('components/footer'); ?>
        <!--footer section end-->

    </div>
    <!-- body content end-->
</section>

<!-- Placed js at the end of the document so the pages load faster -->
<?php
if($output == '') {
    ?>
    <script src="<?= base_url(); ?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
    <?php
}
?>
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery-migrate.js"></script>-->

<!-- common js -->
<?php $this->load->view('components/_common_js'); //left side menu ?>
<!-- /common js -->

</body>
</html>