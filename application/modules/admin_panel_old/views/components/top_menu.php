<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 09-07-17
 * Time: xx:xx
 */
?>

<div class="header-section">

    <!--logo and logo icon start-->
    <div class="logo theme-logo-bg hidden-xs hidden-sm">
        <a href="<?=base_url();?>" target="_blank">
<!--            <img src="--><?//=base_url();?><!--assets/admin_panel/img/logo-icon.png" alt="">-->
            <i class="fa fa-home"></i>
            <span class="brand-name"><strong><?=WEBSITE_NAME_SHORT;?></strong></span>
        </a>
    </div>

    <div class="icon-logo theme-logo-bg hidden-xs hidden-sm">
        <a href="<?=base_url();?>" target="_blank">
<!--            <img src="--><?//=base_url();?><!--assets/admin_panel/img/logo-icon.png" alt="">-->
            <i class="fa fa-home"></i>
        </a>
    </div>
    <!--logo and logo icon end-->

    <!--toggle button start-->
    <a class="toggle-btn"><i class="fa fa-outdent"></i></a>
    <!--toggle button end-->

    <div class="notification-wrap" style="display: flex; justify-content: space-between;">

        <?php
        if($this->session->usertype == 1) {
            ?>
                <div class="dropdown" style="width: fit-content;display: flex; align-items: center; justify-content: center;     margin-left: 14px;">
                  <button class=" bg-primary dropdown-toggle text-light" style="outline: none; border: none; padding: 10px 10px; border-radius: 5px; margin-bottom:0px;" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Goto Portal(<?php echo FINANCIAL_YEAR?>)
                    <span style="padding-left: 25px;" class=" fa fa-angle-down"></span>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                       <a href="https://stanthonyschooledu.org/2023-24" target="_blank" class=" dropdown-item badge text-dark" style="color:#337ab7; background-color: transparent; font-size: 20px;margin-top: 5px;"><i class="bi bi-box-arrow-up-right"></i> &nbsp; 2023-24</a>
                       <a href="https://stanthonyschooledu.org/2024-25" target="_blank" class=" dropdown-item badge text-dark" style="color:#337ab7; background-color: transparent; font-size: 20px;margin-top: 10px;"><i class="bi bi-box-arrow-up-right"></i> &nbsp; 2024-25</a>
                       <a href="https://stanthonyschooledu.org/2025-26" target="_blank" class=" dropdown-item badge text-dark" style="color:#337ab7; background-color: transparent; font-size: 20px;margin-top: 10px;"><i class="bi bi-box-arrow-up-right"></i> &nbsp; 2025-26</a>
                       <a href="https://stanthonyschooledu.org/2026-27" target="_blank" class=" dropdown-item badge text-dark" style="color:#337ab7; background-color: transparent; font-size: 20px;margin-top: 10px;"><i class="bi bi-box-arrow-up-right"></i> &nbsp; 2026-27</a>
                  </div>
                </div>
            <?php
        }
        ?>

        <!--right notification start-->
        <div class="right-notification">
            <ul class="notification-menu">
                <li>
                    <a href="javascript:;" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <?php
                        if($this->session->usertype == 1) {
                            $user_row = $this->db->get_where('user_details', array('user_id' => $this->session->user_id))->row();
                            $profile_img = isset($user_row->img) ? $user_row->img : 'default.png';
                        } else {
                            $profile_img = 'default.png';
                        }
                        ?>
                        <img class="profile_img" src="<?=base_url();?>assets/admin_panel/img/profile_img/<?=$profile_img;?>" />
                        <span class="lastname" style="font-weight: bold;color: black;"><?=$this->session->name; //user lastname?></span>
                        <span class=" fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu purple pull-right">
                            <li><a href="<?= base_url(); ?>admin/profile"><i class="fa fa-vcard-o pull-right"></i>Profile</a></li>
                        <li><a href="<?=base_url();?>logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <!--right notification end-->
    </div>

</div>