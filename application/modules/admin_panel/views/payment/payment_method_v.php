<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
    <meta name="description" content="admin panel">
    <?php $this->load->view('components/_common_head'); ?>
    <style>
        .pay-step-bar{display:flex;align-items:center;justify-content:center;gap:0;margin:10px 0 30px;flex-wrap:wrap}
        .pay-step{display:flex;align-items:center;gap:10px;color:#9aa3ad;font-size:13px;font-weight:500}
        .pay-step .pay-step-num{width:32px;height:32px;border-radius:50%;background:#e9ecef;color:#6c757d;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:14px}
        .pay-step.active{color:#2c3e50}.pay-step.active .pay-step-num{background:#5fb878;color:#fff}
        .pay-step.done{color:#5fb878}.pay-step.done .pay-step-num{background:#5fb878;color:#fff}
        .pay-step-line{width:60px;height:2px;background:#e9ecef;margin:0 12px}
        .pay-step-line.done{background:#5fb878}
        .order-summary{background:#f8f9fa;border:1px solid #e9ecef;border-radius:8px;padding:18px 22px;margin-bottom:25px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px}
        .order-summary .os-label{font-size:12px;color:#7a8590;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
        .order-summary .os-value{font-size:16px;font-weight:600;color:#2c3e50}
        .order-summary .os-amount{font-size:24px;font-weight:700;color:#5fb878}
        .method-list{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:15px}
        .method-card{background:#fff;border:2px solid #e9ecef;border-radius:8px;padding:20px;cursor:pointer;transition:all .2s ease;display:flex;align-items:center;gap:15px}
        .method-card:hover{border-color:#5fb878;background:#fafdfb}
        .method-card.selected{border-color:#5fb878;background:#f4faf6}
        .method-card .method-icon{width:50px;height:50px;border-radius:8px;background:#eaf6ee;color:#5fb878;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
        .method-card.selected .method-icon{background:#5fb878;color:#fff}
        .method-card .method-info{flex:1}
        .method-card .method-name{font-size:15px;font-weight:600;color:#2c3e50;margin-bottom:3px}
        .method-card .method-desc{font-size:12px;color:#7a8590}
        .method-card .method-radio{width:20px;height:20px;border:2px solid #ccc;border-radius:50%;display:flex;align-items:center;justify-content:center}
        .method-card.selected .method-radio{border-color:#5fb878}
        .method-card.selected .method-radio:after{content:'';width:10px;height:10px;background:#5fb878;border-radius:50%}
        .method-logos{display:flex;gap:6px;margin-top:6px;flex-wrap:wrap}
        .method-logos span{font-size:10px;background:#fff;border:1px solid #e0e0e0;color:#666;padding:2px 6px;border-radius:3px;font-weight:600}
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
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading"><?=$section_heading;?></header>
                        <div class="panel-body">
                            <div class="pay-step-bar">
                                <div class="pay-step done"><span class="pay-step-num"><i class="fa fa-check"></i></span><span>Choose Plan</span></div>
                                <div class="pay-step-line done"></div>
                                <div class="pay-step active"><span class="pay-step-num">2</span><span>Payment Method</span></div>
                                <div class="pay-step-line"></div>
                                <div class="pay-step"><span class="pay-step-num">3</span><span>Confirm &amp; Pay</span></div>
                            </div>

                            <div class="order-summary">
                                <div>
                                    <div class="os-label">Selected Plan</div>
                                    <div class="os-value"><?=htmlspecialchars($plan_label)?> &mdash; <?=htmlspecialchars($period)?></div>
                                </div>
                                <div>
                                    <div class="os-label">Student</div>
                                    <div class="os-value"><?=htmlspecialchars($student['name'])?> (<?=htmlspecialchars($student['reg_no'])?>)</div>
                                </div>
                                <div>
                                    <div class="os-label">Amount Payable</div>
                                    <div class="os-amount">&#8377;<?=number_format($amount, 2)?></div>
                                </div>
                            </div>

                            <h5 style="color:#2c3e50;font-weight:600;margin-bottom:15px;">Select Payment Method</h5>

                            <form id="methodForm" method="post" action="<?=base_url('admin/pay_form');?>">
                                <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>">
                                <div class="method-list">

                                    <label class="method-card" data-method="card">
                                        <input type="radio" name="pay_method" value="card" style="display:none" required>
                                        <div class="method-icon"><i class="fa fa-credit-card"></i></div>
                                        <div class="method-info">
                                            <div class="method-name">Credit / Debit Card</div>
                                            <div class="method-desc">Visa, MasterCard, RuPay, Amex</div>
                                            <div class="method-logos"><span>VISA</span><span>MC</span><span>RuPay</span><span>AMEX</span></div>
                                        </div>
                                        <div class="method-radio"></div>
                                    </label>

                                    <label class="method-card" data-method="upi">
                                        <input type="radio" name="pay_method" value="upi" style="display:none">
                                        <div class="method-icon"><i class="fa fa-mobile"></i></div>
                                        <div class="method-info">
                                            <div class="method-name">UPI</div>
                                            <div class="method-desc">Pay via any UPI app</div>
                                            <div class="method-logos"><span>GPay</span><span>PhonePe</span><span>Paytm</span><span>BHIM</span></div>
                                        </div>
                                        <div class="method-radio"></div>
                                    </label>

                                    <label class="method-card" data-method="netbanking">
                                        <input type="radio" name="pay_method" value="netbanking" style="display:none">
                                        <div class="method-icon"><i class="fa fa-university"></i></div>
                                        <div class="method-info">
                                            <div class="method-name">Net Banking</div>
                                            <div class="method-desc">All major banks supported</div>
                                            <div class="method-logos"><span>SBI</span><span>HDFC</span><span>ICICI</span><span>+50 more</span></div>
                                        </div>
                                        <div class="method-radio"></div>
                                    </label>

                                </div>
                                <div class="text-right" style="margin-top:25px;">
                                    <a href="<?=base_url('admin/pay_plan');?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
                                    <button type="submit" class="btn btn-success" id="methodNextBtn" disabled>
                                        Continue <i class="fa fa-arrow-right"></i>
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
    $(document).on('click', '.method-card', function(){
        $('.method-card').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input[type=radio]').prop('checked', true);
        $('#methodNextBtn').prop('disabled', false);
    });
</script>
</body>
</html>
