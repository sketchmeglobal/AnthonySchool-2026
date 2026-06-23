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
        .pay-receipt .rrow.err .v{color:#e74c3c}
        .help-note{background:#e8f4fd;border:1px solid #b6dffb;border-radius:6px;padding:15px;font-size:13px;color:#1a5490;text-align:left;margin-top:20px}
        .help-note strong{display:block;margin-bottom:5px}
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
                                <div class="pay-result-title" style="color:#e74c3c;">
                                    <?php
                                        if (!empty($txn) && $txn['status'] === 'cancelled') echo 'Payment Cancelled';
                                        else echo 'Payment Failed';
                                    ?>
                                </div>
                                <div class="pay-result-sub">
                                    <?php
                                        if (!empty($txn) && $txn['status'] === 'cancelled')
                                            echo 'You cancelled the payment before completion. No charges have been made.';
                                        else
                                            echo 'We could not complete your payment. No charges have been made. You may try again.';
                                    ?>
                                </div>

                                <?php if ($flash = $this->session->flashdata('payment_msg')): ?>
                                <div class="alert alert-warning"><?=$flash;?></div>
                                <?php endif; ?>

                                <?php if (!empty($txn)): ?>
                                <div class="pay-receipt">
                                    <h5>Transaction Details</h5>
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
                                        <span class="l">Amount</span>
                                        <span class="v">&#8377;<?=number_format((float)$txn['amount'], 2)?></span>
                                    </div>
                                    <div class="rrow">
                                        <span class="l">Status</span>
                                        <span class="v" style="text-transform:uppercase;color:#e74c3c;"><?=htmlspecialchars($txn['status'])?></span>
                                    </div>
                                    <div class="rrow">
                                        <span class="l">Attempted At</span>
                                        <span class="v"><?=date('d M Y, h:i A', strtotime($txn['completed_at'] ?: $txn['created_at']))?></span>
                                    </div>
                                    <?php if (!empty($txn['error_message'])): ?>
                                    <div class="rrow err">
                                        <span class="l">Reason</span>
                                        <span class="v"><?=htmlspecialchars($txn['error_message'])?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <div class="help-note">
                                    <strong><i class="fa fa-info-circle"></i> If money was deducted from your account:</strong>
                                    It will be auto-refunded by your bank within 5-7 business days. If not, please contact us with the Transaction ID above.
                                </div>

                                <div style="margin-top:25px;">
                                    <a href="<?=base_url('admin/pay_plan');?>" class="btn btn-success"><i class="fa fa-refresh"></i> Try Again</a>
                                    <a href="<?=base_url('admin/dashboard');?>" class="btn btn-default"><i class="fa fa-home"></i> Back to Dashboard</a>
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
