<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
    <meta name="description" content="admin panel">
    <?php $this->load->view('components/_common_head'); ?>
    <style>
        /* ── Step bar ── */
        .pay-step-bar{display:flex;align-items:center;justify-content:center;gap:0;margin:10px 0 30px;flex-wrap:wrap}
        .pay-step{display:flex;align-items:center;gap:10px;color:#9aa3ad;font-size:13px;font-weight:500}
        .pay-step .pay-step-num{width:32px;height:32px;border-radius:50%;background:#e9ecef;color:#6c757d;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:14px}
        .pay-step.active{color:#2c3e50}.pay-step.active .pay-step-num{background:#5fb878;color:#fff}
        .pay-step.done .pay-step-num{background:#5fb878;color:#fff}
        .pay-step-line{width:60px;height:2px;background:#e9ecef;margin:0 12px}

        /* ── Plan grid ── */
        .plan-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:24px;margin-top:10px}
        .plan-card{position:relative;background:#fff;border:2px solid #e9ecef;border-radius:12px;padding:24px 28px;transition:border-color .2s ease;cursor:pointer}
        .plan-card.selected{border-color:#5fb878;background:#f4faf6}
        .plan-card.yearly-card:hover{border-color:#5fb878}
        .plan-badge{position:absolute;top:-10px;right:15px;background:#e74c3c;color:#fff;font-size:11px;padding:3px 10px;border-radius:12px;font-weight:600}
        .plan-icon{width:54px;height:54px;border-radius:50%;background:#eaf6ee;color:#5fb878;display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 12px}
        .plan-title{font-size:17px;font-weight:700;color:#2c3e50;margin-bottom:2px;text-align:center}
        .plan-period-label{font-size:12px;color:#5fb878;font-weight:600;margin-bottom:14px;text-align:center}
        .fee-amount{font-size:28px;font-weight:800;color:#2c3e50;text-align:center;line-height:1;margin-bottom:4px}
        .fee-amount sup{font-size:15px;vertical-align:super;font-weight:600}
        .fee-amount small{font-size:12px;color:#9aa3ad;font-weight:400}
        .concession-wrap{background:#fff8e1;border:1px solid #ffe082;border-radius:8px;padding:9px 13px;margin:10px 0;font-size:12px}
        .concession-wrap .c-row{display:flex;justify-content:space-between;padding:2px 0;color:#5a6470}
        .concession-wrap .c-row.c-save{color:#2e7d32;font-weight:700;border-top:1px dashed #ffe082;margin-top:4px;padding-top:5px}
        .plan-features{list-style:none;padding:0;margin:10px 0 18px;text-align:left}
        .plan-features li{padding:4px 0;font-size:13px;color:#5a6470;border-bottom:1px dashed #f0f0f0}
        .plan-features li:last-child{border-bottom:none}
        .plan-features li i{color:#5fb878;margin-right:8px;width:14px;text-align:center}
        .plan-select-btn{display:inline-block;padding:9px 20px;background:#5fb878;color:#fff;border-radius:6px;font-size:13px;font-weight:600;border:none;width:100%;cursor:pointer}
        .plan-card.selected .plan-select-btn{background:#2c3e50}

        /* ── Month picker (inside Monthly card) ── */
        .month-picker-wrap{margin:12px 0 16px}
        .month-picker-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
        .month-picker-header span{font-size:12px;font-weight:600;color:#5a6470}
        .month-picker-header a{font-size:12px;color:#5fb878;cursor:pointer;text-decoration:none;font-weight:600}
        .month-picker-header a:hover{text-decoration:underline}
        .month-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:7px}
        .month-cell{position:relative;cursor:pointer;user-select:none}
        .month-cell input[type=checkbox]{position:absolute;opacity:0;width:0;height:0}
        .month-label{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:8px 4px 6px;border:1.5px solid #e9ecef;border-radius:8px;transition:all .15s ease;background:#fff;min-height:58px}
        .month-label .mo-name{font-size:12px;font-weight:700;color:#2c3e50;line-height:1.1}
        .month-label .mo-year{font-size:10px;color:#9aa3ad;margin-top:1px}
        .month-label .mo-tag{font-size:9px;font-weight:700;margin-top:4px;padding:1px 6px;border-radius:10px}
        .month-tag-paid{background:#d4edda;color:#155724}
        .month-tag-sel{background:#5fb878;color:#fff}
        /* checked state */
        .month-cell input:checked + .month-label{border-color:#5fb878;background:#eaf6ee}
        .month-cell input:checked + .month-label .mo-name{color:#1a7a40}
        /* disabled (paid) state */
        .month-cell.is-paid{cursor:default}
        .month-cell.is-paid .month-label{border-color:#c3e6cb;background:#f4fbf6;opacity:.75;cursor:default}
        .month-cell.is-paid .month-label .mo-name{color:#4a7c5a}

        /* ── Total bar ── */
        .monthly-total-bar{display:flex;justify-content:space-between;align-items:center;background:#eaf6ee;border:1px solid #b2dfdb;border-radius:8px;padding:10px 16px;margin-top:12px}
        .monthly-total-bar .tot-label{font-size:12px;color:#2e7d32;font-weight:600}
        .monthly-total-bar .tot-val{font-size:18px;font-weight:800;color:#1a7a40}
        .monthly-total-bar .tot-meta{font-size:11px;color:#5fb878;text-align:right}
    </style>
</head>
<body class="sticky-header">
<section>
    <?php $this->load->view('components/left_sidebar'); ?>
    <div class="body-content" style="min-height:1500px;">
        <?php $this->load->view('components/top_menu'); ?>
        <div class="page-head">
            <h3 class="m-b-less"><?=$menu_name?></h3>
            <div class="state-information">
                <ol class="breadcrumb m-b-less bg-less">
                    <li><a href="<?=base_url('admin/dashboard')?>">Home</a></li>
                    <li class="active"><?=$menu_name?></li>
                </ol>
            </div>
        </div>
        <div class="wrapper">
            <?php if ($flash = $this->session->flashdata('error')): ?>
                <div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> <?=htmlspecialchars($flash)?></div>
            <?php endif; ?>
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading"><?=$section_heading?></header>
                        <div class="panel-body">

                            <div class="pay-step-bar">
                                <div class="pay-step active"><span class="pay-step-num">1</span><span>Choose Plan</span></div>
                                <div class="pay-step-line"></div>
                                <div class="pay-step"><span class="pay-step-num">2</span><span>Payment Method</span></div>
                                <div class="pay-step-line"></div>
                                <div class="pay-step"><span class="pay-step-num">3</span><span>Confirm &amp; Pay</span></div>
                            </div>

                            <?php if ($monthly_amount <= 0 && $yearly_amount <= 0): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                Your fee structure has not been set up yet. Please contact the school office.
                            </div>
                            <?php else: ?>

                            <form id="planForm" method="post" action="<?=base_url('admin/pay_method')?>">
                                <input type="hidden" name="<?=$this->security->get_csrf_token_name()?>" value="<?=$this->security->get_csrf_hash()?>">
                                <?php
                                // Count unpaid months for JS
                                $unpaid_count = 0;
                                foreach ($months as $mo) { if (!$mo['paid']) $unpaid_count++; }
                                ?>

                                <div class="plan-grid">

                                    <!-- ═══════════ MONTHLY CARD ═══════════ -->
                                    <?php if ($monthly_amount > 0): ?>
                                    <div class="plan-card" id="monthlyCard" data-plan="monthly">
                                        <!-- Hidden radio — set when any month is ticked -->
                                        <input type="radio" name="plan_type" value="monthly" id="planMonthly" style="display:none">

                                        <div class="plan-icon"><i class="fa fa-calendar"></i></div>
                                        <div class="plan-title">Monthly Fee</div>
                                        <div class="plan-period-label">Select months to pay</div>

                                        <div class="fee-amount">
                                            <sup>&#8377;</sup><?=number_format($monthly_amount, 2)?> <small>/ month</small>
                                        </div>


                                        <!-- Month picker -->
                                        <div class="month-picker-wrap">
                                            <div class="month-picker-header">
                                                <span><?= ($student['class_type'] === 4) ? 'Financial Year ' . FINANCIAL_YEAR : 'Year ' . explode('-', FINANCIAL_YEAR)[0] ?></span>
                                                <?php if ($unpaid_count > 0): ?>
                                                <a id="selectAllMonths">Select All Unpaid</a>
                                                <?php endif; ?>
                                            </div>

                                            <div class="month-grid">
                                                <?php foreach ($months as $mo): ?>
                                                <div class="month-cell <?=$mo['paid'] ? 'is-paid' : ''?>"
                                                     title="<?=$mo['label']?><?=$mo['paid'] ? ' — Already Paid' : ''?>">
                                                    <input type="checkbox"
                                                           id="mo_<?=$mo['key']?>"
                                                           name="selected_months[]"
                                                           value="<?=$mo['key']?>"
                                                           class="month-chk"
                                                           <?=$mo['paid'] ? 'checked disabled' : ''?>>
                                                    <label class="month-label" for="mo_<?=$mo['key']?>">
                                                        <span class="mo-name"><?=$mo['short']?></span>
                                                        <span class="mo-year"><?=$mo['year']?></span>
                                                        <?php if ($mo['paid']): ?>
                                                        <span class="mo-tag month-tag-paid"><i class="fa fa-check"></i> Paid</span>
                                                        <?php endif; ?>
                                                    </label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>

                                            <!-- Live total -->
                                            <div class="monthly-total-bar" id="monthlyTotalBar" style="<?=$unpaid_count ? '' : 'display:none'?>">
                                                <div>
                                                    <div class="tot-label">Total Payable</div>
                                                    <div class="tot-val">&#8377;<span id="monthlyTotalAmt">0.00</span></div>
                                                </div>
                                                <div class="tot-meta">
                                                    <span id="monthlySelCount">0</span> month(s) selected<br>
                                                    <span style="color:#5a6470;">&#8377;<?=number_format($monthly_amount, 2)?> &times; months</span>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($unpaid_count === 0): ?>
                                        <div class="alert alert-success" style="margin-bottom:0;font-size:13px;text-align:center;">
                                            <i class="fa fa-check-circle"></i> All months for this financial year have been paid.
                                        </div>
                                        <?php else: ?>
                                        <button type="button" class="plan-select-btn" id="monthlySelectBtn">
                                            <i class="fa fa-calendar-check-o"></i> Select &amp; Continue
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>

                                    <!-- ═══════════ YEARLY CARD ═══════════ -->
                                    <?php if ($yearly_amount > 0): ?>
                                    <label class="plan-card yearly-card" data-plan="yearly" id="yearlyCard">
                                        <input type="radio" name="plan_type" value="yearly" id="planYearly" style="display:none">

                                        <div class="plan-icon"><i class="fa fa-graduation-cap"></i></div>
                                        <div class="plan-title">Yearly Fee</div>
                                        <div class="plan-period-label">Academic Year <?=FINANCIAL_YEAR?></div>

                                        <div class="fee-amount">
                                            <sup>&#8377;</sup><?=number_format($yearly_amount, 2)?> <small>/ year</small>
                                        </div>


                                        <ul class="plan-features">
                                            <li><i class="fa fa-check"></i> Development Fees</li>
                                            <li><i class="fa fa-check"></i> Electricity &amp; Building Fund</li>
                                            <li><i class="fa fa-check"></i> Co-Curricular &amp; Exam Fees</li>
                                            <li><i class="fa fa-check"></i> Annual Receipt</li>
                                        </ul>

                                        <button type="button" class="plan-select-btn">Select Plan</button>
                                    </label>
                                    <?php endif; ?>

                                </div><!-- /.plan-grid -->

                                <div class="text-right" style="margin-top:25px;">
                                    <a href="<?=base_url('admin/dashboard')?>" class="btn btn-default">Cancel</a>
                                    <button type="submit" class="btn btn-success" id="planNextBtn" disabled>
                                        Continue <i class="fa fa-arrow-right"></i>
                                    </button>
                                </div>
                            </form>

                            <?php endif; ?>

                        </div>
                    </section>
                </div>
            </div>
        </div>
        <?php $this->load->view('components/footer'); ?>
    </div>
</section>
<script src="<?=base_url()?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
<?php $this->load->view('components/_common_js'); ?>
<script>
var PER_MONTH = <?=json_encode((float)$monthly_amount)?>;

/* ── Helpers ── */
function fmtINR(n) {
    return n.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

/* ── Update monthly live total and button state ── */
function updateMonthlyTotal() {
    var $checked = $('.month-chk:not(:disabled):checked');
    var count    = $checked.length;
    var total    = count * PER_MONTH;

    $('#monthlySelCount').text(count);
    $('#monthlyTotalAmt').text(fmtINR(total));
    $('#monthlyTotalBar').toggle(count > 0);

    if (count > 0) {
        $('#planMonthly').prop('checked', true);
        // deselect yearly visually
        $('#yearlyCard').removeClass('selected');
        $('#planYearly').prop('checked', false);
        $('#monthlyCard').addClass('selected');
        $('#planNextBtn').prop('disabled', false);
    } else {
        $('#planMonthly').prop('checked', false);
        $('#monthlyCard').removeClass('selected');
        // re-check if yearly is still selected
        if (!$('#planYearly').prop('checked')) {
            $('#planNextBtn').prop('disabled', true);
        }
    }
}

/* ── Month checkbox change ── */
$(document).on('change', '.month-chk', function () {
    updateMonthlyTotal();
});

/* ── Select All Unpaid ── */
$('#selectAllMonths').on('click', function (e) {
    e.preventDefault();
    $('.month-chk:not(:disabled)').prop('checked', true);
    updateMonthlyTotal();
});

/* ── "Select & Continue" button on Monthly card ── */
$('#monthlySelectBtn').on('click', function () {
    var count = $('.month-chk:not(:disabled):checked').length;
    if (count === 0) {
        alert('Please select at least one month to pay.');
        return;
    }
    $('#planForm').submit();
});

/* ── Yearly card click ── */
$(document).on('click', '.yearly-card', function () {
    $('#planYearly').prop('checked', true);
    $('.yearly-card').addClass('selected');
    $('#monthlyCard').removeClass('selected');
    // uncheck monthly months selection (keep paid ones checked)
    $('.month-chk:not(:disabled)').prop('checked', false);
    updateMonthlyTotal(); // will disable monthly radio + reset total
    $('#planNextBtn').prop('disabled', false);
});

/* ── Prevent clicking disabled month cells ── */
$(document).on('click', '.month-cell.is-paid label', function (e) {
    e.preventDefault();
});

/* ── On load: restore any previously selected state ── */
updateMonthlyTotal();
</script>
</body>
</html>
