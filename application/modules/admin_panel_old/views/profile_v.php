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
    <title>Profile | <?=WEBSITE_NAME;?></title>
    <meta name="keyword" content="profile">
    <meta name="description" content="update profile">

    <!--Form Wizard-->
    <link href="<?=base_url();?>assets/admin_panel/css/jquery.steps.css" rel="stylesheet" type="text/css" />

    <!--iCheck-->
    <link href="<?=base_url();?>assets/admin_panel/js/icheck/skins/all.css" rel="stylesheet">

    <!--bootstrap picker-->
    <link href="<?=base_url();?>assets/admin_panel/js/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />

    <!--Select2-->
    <link href="<?=base_url();?>assets/admin_panel/css/select2.css" rel="stylesheet">
    <link href="<?=base_url();?>assets/admin_panel/css/select2-bootstrap.css" rel="stylesheet">

    <!-- common head -->
    <?php $this->load->view('components/_common_head'); //left side menu ?>
    <!-- /common head -->
</head>

<body class="sticky-header">

<noscript>
    <meta http-equiv="refresh" content="0; URL=<?=base_url();?>js_disabled">
</noscript>

<section>
    <!-- sidebar left start (Menu)-->
    <?php $this->load->view('components/left_sidebar'); //left side menu ?>
    <!-- sidebar left end (Menu)-->

    <!-- body content start-->
    <div class="body-content" style="min-height: 1500px;">

        <!-- header section start-->
        <?php $this->load->view('components/top_menu'); ?>
        <!-- header section end-->

        <!-- profile head start-->
        <?php
        if($user_type == 1) { //only admin
            ?>
        <div class="profile-hero">
            <div class="profile-intro">
                <?php
                $profile_img = isset($user_details[0]['img']) ? $user_details[0]['img'] : 'default.png';
                ?>
                <img class="profile_img" src="<?=base_url();?>assets/admin_panel/img/profile_img/<?=$profile_img;?>" />
                <div class="clearfix"></div>
                <h1>
                    <strong class="fullname"><?=$user_details[0]['firstname'].' '.$user_details[0]['lastname'];?></strong>
                </h1>
                <div class="s-n">
                    <?php
                    if($user_details[0]['fb_profile']) {
                        ?>
                        <a href="<?=$user_details[0]['fb_profile'];?>" target="_blank"> <i class="fa fa-facebook-square"></i></a>
                        <?php
                    }
                    ?>
                    <?php
                    if($user_details[0]['tw_profile']) {
                        ?>
                        <a href="<?=$user_details[0]['tw_profile'];?>" target="_blank"> <i class="fa fa-twitter"></i></a>
                        <?php
                    }
                    ?>
                    <?php
                    if($user_details[0]['gp_profile']) {
                        ?>
                        <a href="<?=$user_details[0]['gp_profile'];?>" target="_blank"> <i class="fa fa-google-plus-official"></i></a>
                        <?php
                    }
                    ?>
                    <?php
                    if($user_details[0]['website']) {
                        ?>
                        <a href="<?=$user_details[0]['website'];?>" target="_blank"> <i class="fa fa-globe"></i></a>
                        <?php
                    }
                    ?>
                </div>
            </div>

            <div class="profile-value-info">
                <div class="info">
                    <span><?=date("d M'y", strtotime($user[0]['registration_date']));?></span>
                    Registered Since
                </div>
            </div>
        </div>
            <?php
        }
        ?>
        <!-- profile head end-->

        <!-- page head start-->
        <div class="page-head">
            <h3 class="m-b-less">
                Profile
            </h3>
            <div class="state-information">
                <ol class="breadcrumb m-b-less bg-less">
                    <li><a href="<?=base_url('admin/dashboard');?>">Home</a></li>
                    <li class="active"> Profile </li>
                </ol>
            </div>
        </div>
        <!-- page head end-->

        <!--body wrapper start-->
        <div class="wrapper">

            <!--Basic Profile Information section-->
            <?php
            if($user_type == 1) { //only admin
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">
                                Basic Profile Information
                                <span class="tools pull-right">
                                <a class="t-collapse fa fa-chevron-down" href="javascript:;"></a>
                            </span>
                            </header>
                            <div class="panel-body">
                                <div class="form">
                                    <form class="cmxform form-horizontal tasi-form" id="basic_info_form" method="post"
                                          action="<?= base_url(); ?>admin/form_basic_info">
                                        <div class="form-group ">
                                            <label for="firstname" class="control-label col-lg-2">Firstname *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-user-o"></i>
                                                <input value="<?= $user_details[0]['firstname']; ?>"
                                                       class="form-control round-input" id="firstname" name="firstname"
                                                       type="text" placeholder="Type your firstname"/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="lastname" class="control-label col-lg-2">Lastname *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-user-o"></i>
                                                <input value="<?= $user_details[0]['lastname']; ?>"
                                                       class="form-control round-input" id="lastname" name="lastname"
                                                       type="text" placeholder="Type your lastname"/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label class="control-label col-lg-2">Gender *</label>
                                            <div class="col-lg-10">
                                                <input <?= $user_details[0]['gender'] == 'male' ? 'checked' : ''; ?>
                                                        type="radio" value="male" name="gender" id="male"
                                                        class="iCheck-square-blue">
                                                <label for="male" class="control-label">Male</label>
                                                &nbsp;&nbsp;&nbsp;
                                                <input <?= $user_details[0]['gender'] == 'female' ? 'checked' : ''; ?>
                                                        type="radio" value="female" name="gender" id="female"
                                                        class="iCheck-square-orange">
                                                <label for="female" class="control-label">Female</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="dob" class="control-label col-lg-2">Birthday</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-birthday-cake"></i>
                                                <input value="<?= date("d F, Y", strtotime($user_details[0]['dob'])); ?>"
                                                       class="form-control round-input date dp_dob" id="dob" readonly
                                                       name="dob" size="30" type="text"
                                                       placeholder="Select your date of birth">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="phone" class="control-label col-lg-2">Contact No.</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-phone"></i>
                                                <input value="<?= $user_details[0]['contact']; ?>"
                                                       class="form-control round-input" id="phone" name="phone"
                                                       type="tel" minlength="7" maxlength="20"
                                                       placeholder="Type your contact number"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="country" class="control-label col-lg-2">Country</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-globe"></i>
                                                <select class="form-control round-input" id="country" name="country">
                                                    <option value="">Select your country</option>
                                                    <option value="AF">Afghanistan</option>
                                                    <option value="AX">Åland Islands</option>
                                                    <option value="AL">Albania</option>
                                                    <option value="DZ">Algeria</option>
                                                    <option value="AS">American Samoa</option>
                                                    <option value="AD">Andorra</option>
                                                    <option value="AO">Angola</option>
                                                    <option value="AI">Anguilla</option>
                                                    <option value="AQ">Antarctica</option>
                                                    <option value="AG">Antigua and Barbuda</option>
                                                    <option value="AR">Argentina</option>
                                                    <option value="AM">Armenia</option>
                                                    <option value="AW">Aruba</option>
                                                    <option value="AU">Australia</option>
                                                    <option value="AT">Austria</option>
                                                    <option value="AZ">Azerbaijan</option>
                                                    <option value="BS">Bahamas</option>
                                                    <option value="BH">Bahrain</option>
                                                    <option value="BD">Bangladesh</option>
                                                    <option value="BB">Barbados</option>
                                                    <option value="BY">Belarus</option>
                                                    <option value="BE">Belgium</option>
                                                    <option value="BZ">Belize</option>
                                                    <option value="BJ">Benin</option>
                                                    <option value="BM">Bermuda</option>
                                                    <option value="BT">Bhutan</option>
                                                    <option value="BO">Bolivia, Plurinational State of</option>
                                                    <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
                                                    <option value="BA">Bosnia and Herzegovina</option>
                                                    <option value="BW">Botswana</option>
                                                    <option value="BV">Bouvet Island</option>
                                                    <option value="BR">Brazil</option>
                                                    <option value="IO">British Indian Ocean Territory</option>
                                                    <option value="BN">Brunei Darussalam</option>
                                                    <option value="BG">Bulgaria</option>
                                                    <option value="BF">Burkina Faso</option>
                                                    <option value="BI">Burundi</option>
                                                    <option value="KH">Cambodia</option>
                                                    <option value="CM">Cameroon</option>
                                                    <option value="CA">Canada</option>
                                                    <option value="CV">Cape Verde</option>
                                                    <option value="KY">Cayman Islands</option>
                                                    <option value="CF">Central African Republic</option>
                                                    <option value="TD">Chad</option>
                                                    <option value="CL">Chile</option>
                                                    <option value="CN">China</option>
                                                    <option value="CX">Christmas Island</option>
                                                    <option value="CC">Cocos (Keeling) Islands</option>
                                                    <option value="CO">Colombia</option>
                                                    <option value="KM">Comoros</option>
                                                    <option value="CG">Congo</option>
                                                    <option value="CD">Congo, the Democratic Republic of the</option>
                                                    <option value="CK">Cook Islands</option>
                                                    <option value="CR">Costa Rica</option>
                                                    <option value="CI">Côte d'Ivoire</option>
                                                    <option value="HR">Croatia</option>
                                                    <option value="CU">Cuba</option>
                                                    <option value="CW">Curaçao</option>
                                                    <option value="CY">Cyprus</option>
                                                    <option value="CZ">Czech Republic</option>
                                                    <option value="DK">Denmark</option>
                                                    <option value="DJ">Djibouti</option>
                                                    <option value="DM">Dominica</option>
                                                    <option value="DO">Dominican Republic</option>
                                                    <option value="EC">Ecuador</option>
                                                    <option value="EG">Egypt</option>
                                                    <option value="SV">El Salvador</option>
                                                    <option value="GQ">Equatorial Guinea</option>
                                                    <option value="ER">Eritrea</option>
                                                    <option value="EE">Estonia</option>
                                                    <option value="ET">Ethiopia</option>
                                                    <option value="FK">Falkland Islands (Malvinas)</option>
                                                    <option value="FO">Faroe Islands</option>
                                                    <option value="FJ">Fiji</option>
                                                    <option value="FI">Finland</option>
                                                    <option value="FR">France</option>
                                                    <option value="GF">French Guiana</option>
                                                    <option value="PF">French Polynesia</option>
                                                    <option value="TF">French Southern Territories</option>
                                                    <option value="GA">Gabon</option>
                                                    <option value="GM">Gambia</option>
                                                    <option value="GE">Georgia</option>
                                                    <option value="DE">Germany</option>
                                                    <option value="GH">Ghana</option>
                                                    <option value="GI">Gibraltar</option>
                                                    <option value="GR">Greece</option>
                                                    <option value="GL">Greenland</option>
                                                    <option value="GD">Grenada</option>
                                                    <option value="GP">Guadeloupe</option>
                                                    <option value="GU">Guam</option>
                                                    <option value="GT">Guatemala</option>
                                                    <option value="GG">Guernsey</option>
                                                    <option value="GN">Guinea</option>
                                                    <option value="GW">Guinea-Bissau</option>
                                                    <option value="GY">Guyana</option>
                                                    <option value="HT">Haiti</option>
                                                    <option value="HM">Heard Island and McDonald Islands</option>
                                                    <option value="VA">Holy See (Vatican City State)</option>
                                                    <option value="HN">Honduras</option>
                                                    <option value="HK">Hong Kong</option>
                                                    <option value="HU">Hungary</option>
                                                    <option value="IS">Iceland</option>
                                                    <option value="IN">India</option>
                                                    <option value="ID">Indonesia</option>
                                                    <option value="IR">Iran, Islamic Republic of</option>
                                                    <option value="IQ">Iraq</option>
                                                    <option value="IE">Ireland</option>
                                                    <option value="IM">Isle of Man</option>
                                                    <option value="IL">Israel</option>
                                                    <option value="IT">Italy</option>
                                                    <option value="JM">Jamaica</option>
                                                    <option value="JP">Japan</option>
                                                    <option value="JE">Jersey</option>
                                                    <option value="JO">Jordan</option>
                                                    <option value="KZ">Kazakhstan</option>
                                                    <option value="KE">Kenya</option>
                                                    <option value="KI">Kiribati</option>
                                                    <option value="KP">Korea, Democratic People's Republic of</option>
                                                    <option value="KR">Korea, Republic of</option>
                                                    <option value="KW">Kuwait</option>
                                                    <option value="KG">Kyrgyzstan</option>
                                                    <option value="LA">Lao People's Democratic Republic</option>
                                                    <option value="LV">Latvia</option>
                                                    <option value="LB">Lebanon</option>
                                                    <option value="LS">Lesotho</option>
                                                    <option value="LR">Liberia</option>
                                                    <option value="LY">Libya</option>
                                                    <option value="LI">Liechtenstein</option>
                                                    <option value="LT">Lithuania</option>
                                                    <option value="LU">Luxembourg</option>
                                                    <option value="MO">Macao</option>
                                                    <option value="MK">Macedonia, the former Yugoslav Republic of
                                                    </option>
                                                    <option value="MG">Madagascar</option>
                                                    <option value="MW">Malawi</option>
                                                    <option value="MY">Malaysia</option>
                                                    <option value="MV">Maldives</option>
                                                    <option value="ML">Mali</option>
                                                    <option value="MT">Malta</option>
                                                    <option value="MH">Marshall Islands</option>
                                                    <option value="MQ">Martinique</option>
                                                    <option value="MR">Mauritania</option>
                                                    <option value="MU">Mauritius</option>
                                                    <option value="YT">Mayotte</option>
                                                    <option value="MX">Mexico</option>
                                                    <option value="FM">Micronesia, Federated States of</option>
                                                    <option value="MD">Moldova, Republic of</option>
                                                    <option value="MC">Monaco</option>
                                                    <option value="MN">Mongolia</option>
                                                    <option value="ME">Montenegro</option>
                                                    <option value="MS">Montserrat</option>
                                                    <option value="MA">Morocco</option>
                                                    <option value="MZ">Mozambique</option>
                                                    <option value="MM">Myanmar</option>
                                                    <option value="NA">Namibia</option>
                                                    <option value="NR">Nauru</option>
                                                    <option value="NP">Nepal</option>
                                                    <option value="NL">Netherlands</option>
                                                    <option value="NC">New Caledonia</option>
                                                    <option value="NZ">New Zealand</option>
                                                    <option value="NI">Nicaragua</option>
                                                    <option value="NE">Niger</option>
                                                    <option value="NG">Nigeria</option>
                                                    <option value="NU">Niue</option>
                                                    <option value="NF">Norfolk Island</option>
                                                    <option value="MP">Northern Mariana Islands</option>
                                                    <option value="NO">Norway</option>
                                                    <option value="OM">Oman</option>
                                                    <option value="PK">Pakistan</option>
                                                    <option value="PW">Palau</option>
                                                    <option value="PS">Palestinian Territory, Occupied</option>
                                                    <option value="PA">Panama</option>
                                                    <option value="PG">Papua New Guinea</option>
                                                    <option value="PY">Paraguay</option>
                                                    <option value="PE">Peru</option>
                                                    <option value="PH">Philippines</option>
                                                    <option value="PN">Pitcairn</option>
                                                    <option value="PL">Poland</option>
                                                    <option value="PT">Portugal</option>
                                                    <option value="PR">Puerto Rico</option>
                                                    <option value="QA">Qatar</option>
                                                    <option value="RE">Réunion</option>
                                                    <option value="RO">Romania</option>
                                                    <option value="RU">Russian Federation</option>
                                                    <option value="RW">Rwanda</option>
                                                    <option value="BL">Saint Barthélemy</option>
                                                    <option value="SH">Saint Helena, Ascension and Tristan da Cunha
                                                    </option>
                                                    <option value="KN">Saint Kitts and Nevis</option>
                                                    <option value="LC">Saint Lucia</option>
                                                    <option value="MF">Saint Martin (French part)</option>
                                                    <option value="PM">Saint Pierre and Miquelon</option>
                                                    <option value="VC">Saint Vincent and the Grenadines</option>
                                                    <option value="WS">Samoa</option>
                                                    <option value="SM">San Marino</option>
                                                    <option value="ST">Sao Tome and Principe</option>
                                                    <option value="SA">Saudi Arabia</option>
                                                    <option value="SN">Senegal</option>
                                                    <option value="RS">Serbia</option>
                                                    <option value="SC">Seychelles</option>
                                                    <option value="SL">Sierra Leone</option>
                                                    <option value="SG">Singapore</option>
                                                    <option value="SX">Sint Maarten (Dutch part)</option>
                                                    <option value="SK">Slovakia</option>
                                                    <option value="SI">Slovenia</option>
                                                    <option value="SB">Solomon Islands</option>
                                                    <option value="SO">Somalia</option>
                                                    <option value="ZA">South Africa</option>
                                                    <option value="GS">South Georgia and the South Sandwich Islands
                                                    </option>
                                                    <option value="SS">South Sudan</option>
                                                    <option value="ES">Spain</option>
                                                    <option value="LK">Sri Lanka</option>
                                                    <option value="SD">Sudan</option>
                                                    <option value="SR">Suriname</option>
                                                    <option value="SJ">Svalbard and Jan Mayen</option>
                                                    <option value="SZ">Swaziland</option>
                                                    <option value="SE">Sweden</option>
                                                    <option value="CH">Switzerland</option>
                                                    <option value="SY">Syrian Arab Republic</option>
                                                    <option value="TW">Taiwan, Province of China</option>
                                                    <option value="TJ">Tajikistan</option>
                                                    <option value="TZ">Tanzania, United Republic of</option>
                                                    <option value="TH">Thailand</option>
                                                    <option value="TL">Timor-Leste</option>
                                                    <option value="TG">Togo</option>
                                                    <option value="TK">Tokelau</option>
                                                    <option value="TO">Tonga</option>
                                                    <option value="TT">Trinidad and Tobago</option>
                                                    <option value="TN">Tunisia</option>
                                                    <option value="TR">Turkey</option>
                                                    <option value="TM">Turkmenistan</option>
                                                    <option value="TC">Turks and Caicos Islands</option>
                                                    <option value="TV">Tuvalu</option>
                                                    <option value="UG">Uganda</option>
                                                    <option value="UA">Ukraine</option>
                                                    <option value="AE">United Arab Emirates</option>
                                                    <option value="GB">United Kingdom</option>
                                                    <option value="US">United States</option>
                                                    <option value="UM">United States Minor Outlying Islands</option>
                                                    <option value="UY">Uruguay</option>
                                                    <option value="UZ">Uzbekistan</option>
                                                    <option value="VU">Vanuatu</option>
                                                    <option value="VE">Venezuela, Bolivarian Republic of</option>
                                                    <option value="VN">Viet Nam</option>
                                                    <option value="VG">Virgin Islands, British</option>
                                                    <option value="VI">Virgin Islands, U.S.</option>
                                                    <option value="WF">Wallis and Futuna</option>
                                                    <option value="EH">Western Sahara</option>
                                                    <option value="YE">Yemen</option>
                                                    <option value="ZM">Zambia</option>
                                                    <option value="ZW">Zimbabwe</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="address" class="control-label col-lg-2">Address</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-map-marker"></i>
                                                <textarea class="form-control round-input" id="address" name="address"
                                                          placeholder="Type your full address"><?= $user_details[0]['address']; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="fb_link" class="control-label col-lg-2">Facebook</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-facebook-f"></i>
                                                <input value="<?= $user_details[0]['fb_profile']; ?>"
                                                       class="form-control round-input" id="fb_link" name="fb_link"
                                                       type="url" placeholder="Type your facebook profile link"/>
                                                <p class="help-block">e.g. : https://facebook.com/pran93</p>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="tw_link" class="control-label col-lg-2">Twitter</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-twitter"></i>
                                                <input value="<?= $user_details[0]['tw_profile']; ?>"
                                                       class="form-control round-input" id="tw_link" name="tw_link"
                                                       type="url" placeholder="Type your twitter profile link"/>
                                                <p class="help-block">e.g. : https://twitter.com/prankrishna3</p>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="gp_link" class="control-label col-lg-2">Google+</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-google-plus"></i>
                                                <input value="<?= $user_details[0]['gp_profile']; ?>"
                                                       class="form-control round-input" id="gp_link" name="gp_link"
                                                       type="url" placeholder="Type your google plus profile link"/>
                                                <p class="help-block">e.g. : https://plus.google.com/+PranKrishnaDas</p>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="website_link" class="control-label col-lg-2">Website</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-globe"></i>
                                                <input value="<?= $user_details[0]['website']; ?>"
                                                       class="form-control round-input" id="website_link"
                                                       name="website_link" type="url"
                                                       placeholder="Type your website link"/>
                                                <p class="help-block">e.g. : http://www.google.com</p>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="file" class="control-label col-lg-2">Profile Image</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-file-image-o"></i>
                                                <input class="form-control round-input" id="file" name="file"
                                                       type="file" accept="image/jpeg,image/png,image/gif,image/bmp"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-lg-offset-2 col-lg-10">
                                                <button class="btn btn-success" type="submit" name="submit"
                                                        value="submit_basic_info">Update <i class="fa fa-refresh"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <?php
            }
            ?>

            <!--Change Password section-->
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Change Password
                            <span class="tools pull-right">
                                <a class="t-collapse fa fa-chevron-down" href="javascript:;"></a>
                            </span>
                        </header>
                        <div class="panel-body">
                            <form id="change_pass_form" method="post" action="<?=base_url();?>admin/form_change_pass">
                                <div>
                                    <h3>Step 1</h3>
                                    <section>
                                        <div class="form-group clearfix">
                                            <label for="current_pass" class="col-lg-2 control-label">Current Password *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-unlock-alt"></i>
                                                <input id="current_pass" name="current_pass" type="password" placeholder="Enter your current password" class="form-control round-input">
                                            </div>
                                        </div>
                                    </section>
                                    <h3>Step 2</h3>
                                    <section>
                                        <div class="form-group clearfix">
                                            <label for="new_pass" class="col-lg-2 control-label">New Password *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-key"></i>
                                                <input id="new_pass" name="new_pass" type="password" placeholder="Enter new password" class="form-control round-input">
                                            </div>
                                        </div>
                                        <div class="form-group clearfix">
                                            <label for="confirm_pass" class="col-lg-2 control-label">Confirm Password *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-key"></i>
                                                <input id="confirm_pass" name="confirm_pass" type="password" placeholder="Enter new password again" class="form-control round-input">
                                            </div>
                                        </div>
                                    </section>
                                </div>
                                <input type="hidden" name="submit" value="submit_change_pass">
                            </form>
                        </div>
                    </section>
                </div>
            </div>

            <!--Change Email Address section-->
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Change Email Address
                            <span class="tools pull-right">
                                <a class="t-collapse fa fa-chevron-down" href="javascript:;"></a>
                            </span>
                        </header>
                        <div class="panel-body">
                            <div class="form">
                                <form class="cmxform form-horizontal tasi-form" id="change_email_form" method="post" action="<?=base_url();?>admin/form_change_email">
                                    <div class="form-group ">
                                        <label for="new_email" class="control-label col-lg-2">Email *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <i class="fa fa-envelope-o"></i>
                                            <input class="form-control round-input" id="new_email" name="new_email" type="email" placeholder="Type new email address" />
                                            <p class="help-block">e.g. : pro@me.in</p>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit" value="submit_change_email">Update <i class="fa fa-refresh"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!--Change Username section-->
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Change Username
                            <span class="tools pull-right">
                                <a class="t-collapse fa fa-chevron-down" href="javascript:;"></a>
                            </span>
                        </header>
                        <div class="panel-body">
                            <div class="form">
                                <form class="cmxform form-horizontal tasi-form" id="change_username_form" method="post" action="<?=base_url();?>admin/form_change_username">
                                    <div class="form-group ">
                                        <label for="new_username" class="control-label col-lg-2">Username *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <i class="fa fa-user-circle"></i>
                                            <input value="<?=$user[0]['username'];?>" class="form-control round-input" id="new_username" name="new_username" type="text" placeholder="Type new username" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit" value="submit_change_username">Update <i class="fa fa-refresh"></i></button>
                                        </div>
                                    </div>
                                </form>
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
<script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
<script src="<?=base_url();?>assets/admin_panel/js/jquery-migrate.js"></script>

<!--bootstrap picker-->
<script src="<?=base_url();?>assets/admin_panel/js/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
<!--picker initialization-->
<script src="<?=base_url();?>assets/admin_panel/js/picker-init.js"></script>

<!--form validation-->
<script src="<?=base_url();?>assets/admin_panel/js/jquery.validate.min.js" type="text/javascript"></script>
<!--form validation init-->
<script src="<?=base_url();?>assets/admin_panel/js/form-validation-init.js"></script>

<!--Form Wizard-->
<script src="<?=base_url();?>assets/admin_panel/js/jquery.steps.min.js" type="text/javascript"></script>
<script src="<?=base_url();?>assets/admin_panel/js/jquery.validate.min.js" type="text/javascript"></script>
<!--wizard initialization-->
<script src="<?=base_url();?>assets/admin_panel/js/wizard-init.js" type="text/javascript"></script>

<!--Icheck-->
<script src="<?=base_url();?>assets/admin_panel/js/icheck/skins/icheck.min.js"></script>
<!--icheck init-->
<script src="<?=base_url();?>assets/admin_panel/js/icheck-init.js"></script>

<!--select2-->
<script src="<?=base_url();?>assets/admin_panel/js/select2.js"></script>
<!--select2 init-->
<script src="<?=base_url();?>assets/admin_panel/js/select2-init.js"></script>

<!--ajax form submit-->
<script src="<?=base_url();?>assets/admin_panel/js/jquery.form.min.js"></script>
<!--ajax form submit init-->
<script src="<?=base_url();?>assets/admin_panel/js/jquery.form-init.js"></script>

<!-- common js -->
<?php $this->load->view('components/_common_js'); //left side menu ?>
<!-- /common js -->

<script>
    //select country
    <?php
    if($user_type == 1) { //only admin
    ?>
    $('#country').val("<?=$user_details[0]['country'];?>").change();
    <?php
    }
    ?>
</script>

</body>
</html>