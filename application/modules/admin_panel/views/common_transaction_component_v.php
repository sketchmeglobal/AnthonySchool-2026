<?php  ?>

<?php
/**
 * Coded by: Pritam Khan
 * CI: 3.0.6
 * Date: 23-09-2022
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
    <meta name="description" content="admin panel">

    <!-- common head -->
    <?php $this->load->view('components/_common_head'); //left side menu ?>
    <!-- /common head -->

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
            <h3 class="m-b-less">
                <?=$menu_name;?>
            </h3>
            <!--<span class="sub-title">Welcome to Static Table</span>-->
            <div class="state-information">
                <ol class="breadcrumb m-b-less bg-less">
                    <li><a href="<?=base_url('admin/dashboard');?>">Home</a></li>
                    <li class="active"> <?=$menu_name;?> </li>
                </ol>
            </div>
        </div>
        <!-- page head end-->

        <!--body wrapper start-->
        <div class="wrapper">

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            <?=$section_heading;?>
                        </header>
                        <div class="panel-body">
                            <div class="row">
                                <?php
                                $user_type = $this->session->usertype;
                                //fetch user access permission
                                $this->db->where('user_id', $this->session->user_id);
                                $rs_user_prm = $this->db->get('user_permissions')->result_array();
                                $prm_arr = array_column($rs_user_prm, 'permission', 'menu_id');

                                # Admission Fee
                                if ($user_type == 1 || $prm_arr[63] == 1) {
                                    ?>
                                    <div class="col-lg-2">
                                        <a href="<?= base_url(); ?>admin/new_admission_fees_report">
                                            <section class="panel bg-info text-center" style="padding: 20px;">
                                                <div class="symbol">
                                                    <i class="fa fa-money" style="font-size: 38px;"></i>
                                                </div>
                                                <div class="value white">
                                                    <h3 class="timer" style="font-size: 12px;color: black;">Admission
                                                        Fees</h3>
                                                    <p></p>
                                                </div>
                                            </section>
                                        </a>
                                    </div>
                                    <?php
                                }

                                # Monthly Fee
                                if ($user_type == 1 || $prm_arr[61] == 1) {
                                    ?>
                                    <div class="col-lg-2">
                                        <a href="<?= base_url(); ?>admin/monthly_fees_report">
                                            <section class="panel bg-primary text-center" style="padding: 20px;">
                                                <div class="symbol">
                                                    <i class="fa fa-money" style="font-size: 38px;"></i>
                                                </div>
                                                <div class="value white">
                                                    <h3 class="timer" style="font-size: 12px;color: black;">Monthly
                                                        Fees </h3>
                                                    <p></p>
                                                </div>
                                            </section>
                                        </a>
                                    </div>
                                    <?php
                                }

                                # Yearly Fee
                                if ($user_type == 1 || $prm_arr[62] == 1) {
                                    ?>
                                    <div class="col-lg-2">
                                        <a href="<?= base_url(); ?>admin/yearly_fees_report">
                                            <section class="panel bg-success text-center" style="padding: 20px;">
                                                <div class="symbol">
                                                    <i class="fa fa-money" style="font-size: 38px;"></i>
                                                </div>
                                                <div class="value white">
                                                    <h3 class="timer" style="font-size: 12px;color: black;">Yearly
                                                        Fees </h3>
                                                    <p></p>
                                                </div>
                                            </section>
                                        </a>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>	
                            
                        </div>
                    </section>
                </div>
            </div>

        </div>
        <!--body wrapper end-->


        <!--footer section start-->
        <?php $this->load->view('components/footer'); ?>
        <!--footer section end-->

    </div>
    <!-- body content end-->
</section>

<!-- Placed js at the end of the document so the pages load faster -->
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery-1.10.2.min.js"></script>-->
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery-migrate.js"></script>-->
<?php
//open print page
if(isset($print)){
    ?>
    <script>window.open("<?=base_url($print);?>");</script>
    <?php
}
?>
<!-- common js -->
<script src="https://code.jquery.com/jquery-3.6.1.min.js"></script>
<?php $this->load->view('components/_common_js'); //left side menu ?>
<!-- /common js -->


<script>
    
</script>

</body>
</html>