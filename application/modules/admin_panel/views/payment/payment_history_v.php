<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
    <meta name="description" content="admin panel">
    <?php $this->load->view('components/_common_head'); ?>
    <style>
        .hist-wrap{max-width:960px;margin:0 auto}
        .hist-summary{display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap}
        .hist-stat{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:16px 22px;flex:1;min-width:150px;text-align:center}
        .hist-stat .val{font-size:26px;font-weight:800;color:#2c3e50}
        .hist-stat .lbl{font-size:12px;color:#9aa3ad;margin-top:2px}
        .hist-stat.green .val{color:#5fb878}
        .hist-stat.red .val{color:#e74c3c}
        .hist-stat.orange .val{color:#e67e22}

        .pay-table{width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.07)}
        .pay-table thead th{background:#2c3e50;color:#fff;padding:11px 14px;font-size:12px;font-weight:600;text-align:left}
        .pay-table tbody tr{border-bottom:1px solid #f0f0f0;transition:background .15s}
        .pay-table tbody tr:hover{background:#f8fafb}
        .pay-table tbody td{padding:11px 14px;font-size:13px;color:#2c3e50;vertical-align:middle}
        .pay-table .badge-status{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;text-transform:uppercase}
        .badge-success{background:#eaf6ee;color:#27ae60}
        .badge-failed{background:#fef0f0;color:#e74c3c}
        .badge-cancelled{background:#fff4e6;color:#e67e22}
        .badge-pending,.badge-initiated{background:#eaf0fb;color:#3498db}

        .pagination-wrap{margin-top:18px;text-align:center}
        .empty-state{text-align:center;padding:50px 20px;color:#9aa3ad}
        .empty-state i{font-size:48px;margin-bottom:12px;display:block}
        @media(max-width:600px){.pay-table thead th:nth-child(4),.pay-table tbody td:nth-child(4){display:none}.hist-summary{gap:8px}}
        .section-label{font-size:14px;font-weight:700;color:#2c3e50;margin:28px 0 10px;padding-bottom:6px;border-bottom:2px solid #e9ecef}
        .section-label i{margin-right:6px;color:#5fb878}
        .badge-offline{background:#f0f4ff;color:#2c5fa8}
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
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <div class="panel-body">
                            <div class="hist-wrap">

                                <?php
                                // Stats are from ALL records (not just this page)
                                $sum_success     = isset($stats['success'])   ? $stats['success']['amount'] : 0;
                                $count_success   = isset($stats['success'])   ? $stats['success']['count']  : 0;
                                $count_failed    = isset($stats['failed'])    ? $stats['failed']['count']   : 0;
                                $count_cancelled = isset($stats['cancelled']) ? $stats['cancelled']['count']: 0;
                                ?>

                                <div class="hist-summary">
                                    <div class="hist-stat green">
                                        <div class="val">&#8377;<?=number_format($sum_success, 2)?></div>
                                        <div class="lbl">Total Paid</div>
                                    </div>
                                    <div class="hist-stat green">
                                        <div class="val"><?=$count_success?></div>
                                        <div class="lbl">Successful</div>
                                    </div>
                                    <div class="hist-stat red">
                                        <div class="val"><?=$count_failed?></div>
                                        <div class="lbl">Failed</div>
                                    </div>
                                    <div class="hist-stat orange">
                                        <div class="val"><?=$count_cancelled?></div>
                                        <div class="lbl">Cancelled</div>
                                    </div>
                                </div>

                                <div class="section-label">
                                    <i class="fa fa-credit-card"></i> Online Payments (Easebuzz)
                                </div>

                                <?php if (empty($txns)): ?>
                                <div class="empty-state">
                                    <i class="fa fa-credit-card"></i>
                                    <p>No online payment transactions found.</p>
                                    <a href="<?=base_url('admin/pay_plan')?>" class="btn btn-success">Pay Fees Online</a>
                                </div>
                                <?php else: ?>

                                <table class="pay-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Transaction ID</th>
                                            <th>Plan</th>
                                            <th>Method</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th class="text-center">Receipt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $method_map = ['card' => 'Card', 'upi' => 'UPI', 'netbanking' => 'Net Banking'];
                                    $i = ($page - 1) * $limit + 1;
                                    foreach ($txns as $txn):
                                        $date = !empty($txn['completed_at'])
                                            ? date('d M Y, h:i A', strtotime($txn['completed_at']))
                                            : date('d M Y, h:i A', strtotime($txn['created_at']));
                                        $m = isset($method_map[$txn['pay_method']]) ? $method_map[$txn['pay_method']] : ucfirst((string)$txn['pay_method']);
                                    ?>
                                    <tr>
                                        <td><?=$i++?></td>
                                        <td><?=$date?></td>
                                        <td><code style="font-size:11px;"><?=htmlspecialchars($txn['txnid'])?></code></td>
                                        <td><?=htmlspecialchars($txn['plan_label'])?></td>
                                        <td><?=$m?></td>
                                        <td><strong>&#8377;<?=number_format((float)$txn['amount'], 2)?></strong></td>
                                        <td><span class="badge-status badge-<?=$txn['status']?>"><?=$txn['status']?></span></td>
                                        <td class="text-center">
                                            <?php if ($txn['status'] === 'success'): ?>
                                            <a href="<?=base_url('admin/payment_receipt_pdf/' . urlencode($txn['txnid']))?>"
                                               class="btn btn-xs btn-danger" title="Download PDF Receipt" target="_blank">
                                                <i class="fa fa-file-pdf-o"></i> PDF
                                            </a>
                                            <?php else: ?>
                                            <span style="color:#ccc;font-size:11px;">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <?php if ($total > $limit):
                                    $total_pages = ceil($total / $limit);
                                ?>
                                <div class="pagination-wrap">
                                    <ul class="pagination pagination-sm">
                                        <?php if ($page > 1): ?>
                                        <li><a href="?page=<?=$page-1?>">&laquo;</a></li>
                                        <?php endif; ?>
                                        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                                        <li class="<?=($p === $page) ? 'active' : ''?>"><a href="?page=<?=$p?>"><?=$p?></a></li>
                                        <?php endfor; ?>
                                        <?php if ($page < $total_pages): ?>
                                        <li><a href="?page=<?=$page+1?>">&raquo;</a></li>
                                        <?php endif; ?>
                                    </ul>
                                    <p style="font-size:12px;color:#9aa3ad;">Showing <?=count($txns)?> of <?=$total?> records</p>
                                </div>
                                <?php endif; ?>
                                <?php endif; ?>

                                <!-- ═══ OFFLINE / CASH PAYMENTS ═══ -->
                                <div class="section-label">
                                    <i class="fa fa-money"></i> Cash / Cheque Payments
                                </div>

                                <?php if (empty($offline_txns)): ?>
                                <div class="empty-state" style="padding:20px;">
                                    <i class="fa fa-inbox" style="font-size:28px;"></i>
                                    <p style="margin:6px 0 0;">No offline payment records found.</p>
                                </div>
                                <?php else: ?>
                                <table class="pay-table" style="margin-top:0;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Receipt No.</th>
                                            <th>Months Paid</th>
                                            <th>Mode</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php $oi = 1; foreach ($offline_txns as $ot): ?>
                                    <tr>
                                        <td><?=$oi++?></td>
                                        <td><?=date('d M Y', strtotime($ot['FM_HDR_COL_DATE']))?></td>
                                        <td><code style="font-size:11px;"><?=htmlspecialchars($ot['FM_HDR_RCPT_NO'])?></code></td>
                                        <td><?=htmlspecialchars($ot['months_label'] ?: '—')?></td>
                                        <td><?=htmlspecialchars($ot['FM_HDR_P_TYP'] ?: '—')?></td>
                                        <td><strong>&#8377;<?=number_format((float)$ot['FM_HDR_TOT_FEES'], 2)?></strong></td>
                                        <td><span class="badge-status badge-offline">Offline</span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php endif; ?>

                                <div style="margin-top:20px;">
                                    <a href="<?=base_url('admin/pay_plan')?>" class="btn btn-success btn-sm">
                                        <i class="fa fa-credit-card"></i> Pay Fees Online
                                    </a>
                                    <a href="<?=base_url('admin/dashboard')?>" class="btn btn-default btn-sm">
                                        <i class="fa fa-home"></i> Dashboard
                                    </a>
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
