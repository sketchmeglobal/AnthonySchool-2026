<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
    <meta name="description" content="admin panel">
    <?php $this->load->view('components/_common_head'); ?>
    <!-- Pre-warm connection to Easebuzz payment page -->
    <link rel="preconnect" href="https://pay.easebuzz.in" crossorigin>
    <link rel="dns-prefetch" href="https://pay.easebuzz.in">
    <style>
        .pay-step-bar{display:flex;align-items:center;justify-content:center;gap:0;margin:10px 0 30px;flex-wrap:wrap}
        .pay-step{display:flex;align-items:center;gap:10px;color:#9aa3ad;font-size:13px;font-weight:500}
        .pay-step .pay-step-num{width:32px;height:32px;border-radius:50%;background:#e9ecef;color:#6c757d;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:14px}
        .pay-step.active{color:#2c3e50}.pay-step.active .pay-step-num{background:#5fb878;color:#fff}
        .pay-step.done{color:#5fb878}.pay-step.done .pay-step-num{background:#5fb878;color:#fff}
        .pay-step-line{width:60px;height:2px;background:#e9ecef;margin:0 12px}
        .pay-step-line.done{background:#5fb878}
        .summary-box{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:25px;margin-bottom:20px}
        .summary-row{display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px dashed #eee}
        .summary-row:last-child{border-bottom:none}
        .summary-row .lbl{color:#7a8590;font-size:13px}
        .summary-row .val{color:#2c3e50;font-weight:600;font-size:14px}
        .summary-total{background:#f4faf6;border:1px solid #5fb878;border-radius:8px;padding:18px 22px;display:flex;justify-content:space-between;align-items:center;margin-top:15px}
        .summary-total .tot-lbl{font-size:14px;color:#2c3e50;font-weight:600}
        .summary-total .tot-val{font-size:26px;font-weight:700;color:#5fb878}
        .terms-row{margin-top:15px;padding:12px 0;font-size:13px;color:#5a6470}
        .terms-row input[type=checkbox]{margin-right:8px}
        #payErrorBox{display:none;margin-top:15px}
    </style>
</head>
<body class="sticky-header">
<section>
    <?php $this->load->view('components/left_sidebar'); ?>
    <div class="body-content" style="min-height:1500px;">
        <?php $this->load->view('components/top_menu'); ?>
        <div class="page-head">
            <h3 class="m-b-less"><?=$menu_name;?></h3>
            <div class="state-information">
                <ol class="breadcrumb m-b-less bg-less">
                    <li><a href="<?=base_url('admin/dashboard');?>">Home</a></li>
                    <li><a href="<?=base_url('admin/pay_plan');?>">Payment</a></li>
                    <li class="active"><?=$menu_name;?></li>
                </ol>
            </div>
        </div>
        <div class="wrapper">
            <?php if ($flash = $this->session->flashdata('error')): ?>
                <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <?=$flash;?></div>
            <?php endif; ?>
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading"><?=$section_heading;?></header>
                        <div class="panel-body">
                            <div class="pay-step-bar">
                                <div class="pay-step done"><span class="pay-step-num"><i class="fa fa-check"></i></span><span>Choose Plan</span></div>
                                <div class="pay-step-line done"></div>
                                <div class="pay-step done"><span class="pay-step-num"><i class="fa fa-check"></i></span><span>Payment Method</span></div>
                                <div class="pay-step-line done"></div>
                                <div class="pay-step active"><span class="pay-step-num">3</span><span>Confirm &amp; Pay</span></div>
                            </div>

                            <div class="summary-box">
                                <h5 style="color:#2c3e50;font-weight:600;margin-top:0;margin-bottom:15px;">Order Summary</h5>
                                <div class="summary-row">
                                    <span class="lbl">Student</span>
                                    <span class="val"><?=htmlspecialchars($plan_data['student_name'])?> (<?=htmlspecialchars($plan_data['student_reg_no'])?>)</span>
                                </div>
                                <div class="summary-row">
                                    <span class="lbl">Plan</span>
                                    <span class="val"><?=htmlspecialchars($plan_data['plan_label'])?></span>
                                </div>
                                <div class="summary-row">
                                    <span class="lbl">Plan Type</span>
                                    <span class="val"><?=ucfirst($plan_data['plan_type'])?></span>
                                </div>
                                <div class="summary-row">
                                    <span class="lbl">Payment Method</span>
                                    <span class="val">
                                        <?php
                                            $m = $method_data['pay_method'];
                                            echo $m === 'card' ? 'Credit / Debit Card' : ($m === 'upi' ? 'UPI' : 'Net Banking');
                                        ?>
                                    </span>
                                </div>
                                <div class="summary-row">
                                    <span class="lbl">Email</span>
                                    <span class="val"><?=htmlspecialchars($plan_data['email'])?></span>
                                </div>
                                <div class="summary-row">
                                    <span class="lbl">Phone</span>
                                    <span class="val"><?=htmlspecialchars($plan_data['phone'])?></span>
                                </div>
                                <div class="summary-total">
                                    <span class="tot-lbl">Total Payable</span>
                                    <span class="tot-val">&#8377;<?=number_format((float)$plan_data['amount'], 2)?></span>
                                </div>
                            </div>

                            <div id="payErrorBox" class="alert alert-danger">
                                <i class="fa fa-exclamation-triangle"></i> <span id="payErrorMsg"></span>
                            </div>

                            <form id="confirmForm">
                                <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>">
                                <input type="hidden" name="confirm" value="1">
                                <input type="hidden" name="payment_nonce" id="paymentNonce" value="<?=htmlspecialchars($payment_nonce, ENT_QUOTES)?>">
                                <div class="terms-row">
                                    <label>
                                        <input type="checkbox" id="confirmChk">
                                        I have reviewed the details above and authorize this payment of &#8377;<?=number_format((float)$plan_data['amount'], 2)?>.
                                    </label>
                                </div>
                                <div class="text-right" style="margin-top:25px;">
                                    <a href="<?=base_url('admin/pay_method');?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
                                    <button type="button" id="payNowBtn" class="btn btn-success" disabled>
                                        <i class="fa fa-lock"></i> Pay &#8377;<?=number_format((float)$plan_data['amount'], 2)?> Securely
                                    </button>
                                </div>
                            </form>

                        </div>
                    </section>
                </div>
            </div>
        </div>
        <?php $this->load->view('components/footer'); ?>
    </div>
</section>
<script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
<?php $this->load->view('components/_common_js'); ?>
<script>
var INITIATE_URL = '<?=base_url("admin/pay_initiate_ajax");?>';
var EB_ENV       = '<?=htmlspecialchars($easebuzz_env, ENT_QUOTES)?>';
var PAY_BASE     = (EB_ENV === 'test')
                   ? 'https://testpay.easebuzz.in/pay/'
                   : 'https://pay.easebuzz.in/pay/';

$(document).on('change', '#confirmChk', function(){
    $('#payNowBtn').prop('disabled', !this.checked);
});

$(document).on('click', '#payNowBtn', function(){
    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Redirecting to payment...');
    $('#payErrorBox').hide();

    $.ajax({
        url: INITIATE_URL, method: 'POST',
        data: $('#confirmForm').serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.retry_nonce) $('#paymentNonce').val(res.retry_nonce);
            if (res.status !== 1) {
                showError(res.message || 'Could not start payment.');
                resetBtn($btn);
                return;
            }
            // Redirect to Easebuzz hosted payment page (works on all devices/browsers)
            window.location.href = PAY_BASE + res.access_key;
        },
        error: function() {
            showError('Network error. Please check your connection and try again.');
            resetBtn($btn);
        }
    });
});

function showError(msg) {
    $('#payErrorMsg').text(msg);
    $('#payErrorBox').show();
    $('html, body').animate({scrollTop: $('#payErrorBox').offset().top - 20}, 300);
}
function resetBtn($btn) {
    $btn.prop('disabled', false).html('<i class="fa fa-lock"></i> Pay &#8377;<?=number_format((float)$plan_data['amount'], 2)?> Securely');
}
</script>
</body>
</html>
