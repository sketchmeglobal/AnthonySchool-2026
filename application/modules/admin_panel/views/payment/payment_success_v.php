<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
    <meta name="description" content="admin panel">
    <?php $this->load->view('components/_common_head'); ?>
    <style>
        .pay-result-wrap{max-width:640px;margin:30px auto;text-align:center}
        .pay-result-title{font-size:26px;font-weight:700;color:#2c3e50;margin-bottom:8px}
        .pay-result-sub{font-size:15px;color:#7a8590;margin-bottom:30px}
        .pay-receipt{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:25px;text-align:left;margin-bottom:25px}
        .pay-receipt h5{color:#2c3e50;font-weight:600;margin-top:0;margin-bottom:15px;padding-bottom:10px;border-bottom:2px solid #f0f0f0}
        .pay-receipt .rrow{display:flex;justify-content:space-between;padding:9px 0;font-size:13px}
        .pay-receipt .rrow .l{color:#7a8590}
        .pay-receipt .rrow .v{color:#2c3e50;font-weight:600}
        .pay-receipt .rrow.tot{margin-top:8px;padding-top:14px;border-top:2px dashed #eee;font-size:16px}
        .pay-receipt .rrow.tot .v{color:#5fb878;font-size:20px}
        .pay-result-actions .btn{margin:0 5px}
        @media print{.no-print{display:none !important}body{background:#fff !important}}
    </style>
</head>
<body class="sticky-header">
<section>
    <?php $this->load->view('components/left_sidebar'); ?>
    <div class="body-content" style="min-height:1500px;">
        <?php $this->load->view('components/top_menu'); ?>
        <div class="page-head no-print">
            <h3 class="m-b-less"><?=$menu_name;?></h3>
            <div class="state-information">
                <ol class="breadcrumb m-b-less bg-less">
                    <li><a href="<?=base_url('admin/dashboard');?>">Home</a></li>
                    <li class="active"><?=$menu_name;?></li>
                </ol>
            </div>
        </div>
        <div class="wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <div class="panel-body">
                            <div class="pay-result-wrap">
                                <div class="pay-result-title" style="color:#5fb878;">&#10003; Payment Successful!</div>
                                <div class="pay-result-sub">Your payment has been received and recorded. A confirmation will be sent to your email.</div>

                                <?php if (!empty($txn)): ?>
                                <div class="pay-receipt">
                                    <h5>Payment Receipt</h5>
                                    <div class="rrow">
                                        <span class="l">Transaction ID</span>
                                        <span class="v"><?=htmlspecialchars($txn['txnid'])?></span>
                                    </div>
                                    <?php if (!empty($txn['easepayid'])): ?>
                                    <div class="rrow">
                                        <span class="l">Gateway Ref</span>
                                        <span class="v"><?=htmlspecialchars($txn['easepayid'])?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="rrow">
                                        <span class="l">Student</span>
                                        <span class="v"><?=htmlspecialchars($txn['student_name'])?> (<?=htmlspecialchars($txn['student_reg_no'])?>)</span>
                                    </div>
                                    <div class="rrow">
                                        <span class="l">Plan</span>
                                        <span class="v"><?=htmlspecialchars($txn['plan_label'])?></span>
                                    </div>
                                    <div class="rrow">
                                        <span class="l">Payment Method</span>
                                        <span class="v">
                                            <?php
                                                $m = $txn['pay_method'];
                                                echo $m === 'card' ? 'Credit / Debit Card' : ($m === 'upi' ? 'UPI' : 'Net Banking');
                                                if (!empty($txn['gateway_mode'])) echo ' (' . htmlspecialchars($txn['gateway_mode']) . ')';
                                            ?>
                                        </span>
                                    </div>
                                    <?php if (!empty($txn['bank_ref_num'])): ?>
                                    <div class="rrow">
                                        <span class="l">Bank Reference</span>
                                        <span class="v"><?=htmlspecialchars($txn['bank_ref_num'])?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="rrow">
                                        <span class="l">Date &amp; Time</span>
                                        <span class="v"><?=date('d M Y, h:i A', strtotime($txn['completed_at'] ?: $txn['created_at']))?></span>
                                    </div>
                                    <div class="rrow tot">
                                        <span class="l">Amount Paid</span>
                                        <span class="v">&#8377;<?=number_format((float)$txn['amount'], 2)?></span>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="pay-result-actions no-print">
                                    <button onclick="window.print()" class="btn btn-default"><i class="fa fa-print"></i> Print Receipt</button>
                                    <a href="<?=base_url('admin/dashboard');?>" class="btn btn-success"><i class="fa fa-home"></i> Back to Dashboard</a>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <?php $this->load->view('components/footer'); ?>
    </div>
</section>
<?php $this->load->view('components/_common_js'); ?>
</body>
</html>
