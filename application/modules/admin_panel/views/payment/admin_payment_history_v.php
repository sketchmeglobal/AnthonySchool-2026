<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
    <meta name="description" content="admin panel">
    <?php $this->load->view('components/_common_head'); ?>
    <style>
        .filter-bar{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:18px 20px;margin-bottom:20px}
        .filter-bar .form-group{margin-bottom:0}
        .filter-bar label{font-size:12px;font-weight:600;color:#5a6370;margin-bottom:3px}

        .summary-cards{display:flex;gap:14px;margin-bottom:20px;flex-wrap:wrap}
        .sum-card{background:#fff;border:1px solid #e9ecef;border-radius:10px;padding:14px 20px;flex:1;min-width:140px;text-align:center}
        .sum-card .val{font-size:24px;font-weight:800;color:#2c3e50}
        .sum-card .lbl{font-size:11px;color:#9aa3ad;margin-top:2px}
        .sum-card.green .val{color:#5fb878}
        .sum-card.red .val{color:#e74c3c}
        .sum-card.blue .val{color:#3498db}

        .pay-table{width:100%;border-collapse:collapse;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.07)}
        .pay-table thead th{background:#2c3e50;color:#fff;padding:10px 12px;font-size:11px;font-weight:600;text-align:left;white-space:nowrap}
        .pay-table tbody tr{border-bottom:1px solid #f0f0f0;transition:background .15s}
        .pay-table tbody tr:hover{background:#f8fafb}
        .pay-table tbody td{padding:10px 12px;font-size:12px;color:#2c3e50;vertical-align:middle}
        .badge-status{display:inline-block;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase}
        .badge-success{background:#eaf6ee;color:#27ae60}
        .badge-failed{background:#fef0f0;color:#e74c3c}
        .badge-cancelled{background:#fff4e6;color:#e67e22}
        .badge-pending,.badge-initiated{background:#eaf0fb;color:#3498db}

        .tfoot-total td{background:#eaf6ee;font-weight:700;font-size:13px}
        .pagination-wrap{margin-top:16px;text-align:center}
        .empty-state{text-align:center;padding:50px 20px;color:#9aa3ad}
        .empty-state i{font-size:48px;margin-bottom:12px;display:block}
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
                    <li><a href="<?=base_url('admin/fees_collection')?>">Transactions</a></li>
                    <li class="active"><?=$section_heading?></li>
                </ol>
            </div>
        </div>
        <div class="wrapper">
            <div class="row">
                <div class="col-lg-12">

                    <!-- Filters -->
                    <section class="panel">
                        <div class="panel-body">
                            <form method="get" action="" class="filter-bar">
                                <div class="row">
                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <div class="form-group">
                                            <label>From Date</label>
                                            <input type="date" name="date_from" class="form-control input-sm"
                                                   value="<?=htmlspecialchars(isset($filters['date_from']) ? $filters['date_from'] : '')?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <div class="form-group">
                                            <label>To Date</label>
                                            <input type="date" name="date_to" class="form-control input-sm"
                                                   value="<?=htmlspecialchars(isset($filters['date_to']) ? $filters['date_to'] : '')?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control input-sm">
                                                <option value="">All Statuses</option>
                                                <?php foreach (['success','failed','cancelled','pending','initiated'] as $s): ?>
                                                <option value="<?=$s?>" <?=(isset($filters['status']) && $filters['status']===$s)?'selected':''?>><?=ucfirst($s)?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <div class="form-group">
                                            <label>Plan Type</label>
                                            <select name="plan_type" class="form-control input-sm">
                                                <option value="">All Plans</option>
                                                <option value="monthly" <?=(isset($filters['plan_type']) && $filters['plan_type']==='monthly')?'selected':''?>>Monthly</option>
                                                <option value="yearly"  <?=(isset($filters['plan_type']) && $filters['plan_type']==='yearly') ?'selected':''?>>Yearly</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-xs-6">
                                        <div class="form-group">
                                            <label>Search</label>
                                            <input type="text" name="search" placeholder="Name / Reg No / Txn ID"
                                                   class="form-control input-sm"
                                                   value="<?=htmlspecialchars(isset($filters['search']) ? $filters['search'] : '')?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-4 col-xs-6" style="display:flex;align-items:flex-end;gap:6px;padding-bottom:0;">
                                        <button type="submit" class="btn btn-primary btn-sm" style="margin-top:18px;">
                                            <i class="fa fa-search"></i> Filter
                                        </button>
                                        <a href="<?=base_url('admin/online_payment_report')?>" class="btn btn-default btn-sm" style="margin-top:18px;">
                                            <i class="fa fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <!-- Summary cards -->
                            <div class="summary-cards">
                                <div class="sum-card">
                                    <div class="val"><?=$total?></div>
                                    <div class="lbl">Total Records</div>
                                </div>
                                <div class="sum-card green">
                                    <div class="val"><?=$success_count?></div>
                                    <div class="lbl">Successful</div>
                                </div>
                                <div class="sum-card green">
                                    <div class="val">&#8377;<?=number_format($total_collected, 2)?></div>
                                    <div class="lbl">Total Collected</div>
                                </div>
                            </div>

                            <!-- Bulk Download -->
                            <div style="margin-bottom:14px;display:flex;align-items:center;gap:10px;">
                                <strong style="font-size:13px;">Download Report:</strong>
                                <?php
                                $q = http_build_query(array_filter([
                                    'status'    => isset($filters['status'])    ? $filters['status']    : '',
                                    'plan_type' => isset($filters['plan_type']) ? $filters['plan_type'] : '',
                                    'date_from' => isset($filters['date_from']) ? $filters['date_from'] : '',
                                    'date_to'   => isset($filters['date_to'])   ? $filters['date_to']   : '',
                                    'search'    => isset($filters['search'])    ? $filters['search']    : '',
                                ]));
                                ?>
                                <a href="<?=base_url('admin/online_payment_report_pdf') . ($q ? '?'.$q : '')?>"
                                   class="btn btn-danger btn-sm" target="_blank">
                                    <i class="fa fa-file-pdf-o"></i> Download PDF (<?=$total?> records)
                                </a>
                            </div>

                            <!-- Table -->
                            <?php if (empty($txns)): ?>
                            <div class="empty-state">
                                <i class="fa fa-credit-card"></i>
                                <p>No transactions found for the selected filters.</p>
                            </div>
                            <?php else: ?>
                            <div style="overflow-x:auto;">
                                <table class="pay-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Transaction ID</th>
                                            <th>Student</th>
                                            <th>Reg No</th>
                                            <th>Plan</th>
                                            <th>Method</th>
                                            <th>Amount (&#8377;)</th>
                                            <th>Status</th>
                                            <th>Gateway Ref</th>
                                            <th>Receipt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $method_map = ['card' => 'Card', 'upi' => 'UPI', 'netbanking' => 'Net Banking'];
                                    $page_total = 0;
                                    $i = ($page - 1) * $limit + 1;
                                    foreach ($txns as $txn):
                                        $date = !empty($txn['completed_at'])
                                            ? date('d M Y, h:i A', strtotime($txn['completed_at']))
                                            : date('d M Y, h:i A', strtotime($txn['created_at']));
                                        $m = isset($method_map[$txn['pay_method']]) ? $method_map[$txn['pay_method']] : ucfirst((string)$txn['pay_method']);
                                        if ($txn['status'] === 'success') { $page_total += $txn['amount']; }
                                    ?>
                                    <tr>
                                        <td><?=$i++?></td>
                                        <td style="white-space:nowrap;"><?=$date?></td>
                                        <td><code style="font-size:10px;"><?=htmlspecialchars($txn['txnid'])?></code></td>
                                        <td><?=htmlspecialchars($txn['student_name'])?></td>
                                        <td><?=htmlspecialchars($txn['student_reg_no'])?></td>
                                        <td><?=ucfirst($txn['plan_type'])?></td>
                                        <td><?=$m?></td>
                                        <td style="text-align:right;font-weight:600;">
                                            <?php if ($txn['status'] === 'success'): ?>
                                                <span style="color:#27ae60;">&#8377;<?=number_format((float)$txn['amount'], 2)?></span>
                                            <?php else: ?>
                                                <span style="color:#aaa;">&#8377;<?=number_format((float)$txn['amount'], 2)?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge-status badge-<?=$txn['status']?>"><?=$txn['status']?></span></td>
                                        <td style="font-size:11px;color:#7f8c8d;"><?=htmlspecialchars((string)$txn['easepayid'])?></td>
                                        <td class="text-center">
                                            <?php if ($txn['status'] === 'success'): ?>
                                            <a href="<?=base_url('admin/payment_receipt_pdf/' . urlencode($txn['txnid']))?>"
                                               class="btn btn-xs btn-danger" title="Download PDF" target="_blank">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </a>
                                            <?php else: ?>
                                            <span style="color:#ccc;font-size:11px;">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="tfoot-total">
                                            <td colspan="7" style="text-align:right;padding:10px 12px;">Page Total (Success):</td>
                                            <td style="text-align:right;padding:10px 12px;color:#27ae60;">&#8377;<?=number_format($page_total, 2)?></td>
                                            <td colspan="3"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <?php if ($total > $limit):
                                $total_pages = ceil($total / $limit);
                                $base_q = http_build_query(array_filter([
                                    'status'    => isset($filters['status'])    ? $filters['status']    : '',
                                    'plan_type' => isset($filters['plan_type']) ? $filters['plan_type'] : '',
                                    'date_from' => isset($filters['date_from']) ? $filters['date_from'] : '',
                                    'date_to'   => isset($filters['date_to'])   ? $filters['date_to']   : '',
                                    'search'    => isset($filters['search'])    ? $filters['search']    : '',
                                ]));
                                $base_q = $base_q ? '&'.$base_q : '';
                            ?>
                            <div class="pagination-wrap">
                                <ul class="pagination pagination-sm">
                                    <?php if ($page > 1): ?>
                                    <li><a href="?page=<?=$page-1?><?=$base_q?>">&laquo;</a></li>
                                    <?php endif; ?>
                                    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                                    <li class="<?=($p===$page)?'active':''?>"><a href="?page=<?=$p?><?=$base_q?>"><?=$p?></a></li>
                                    <?php endfor; ?>
                                    <?php if ($page < $total_pages): ?>
                                    <li><a href="?page=<?=$page+1?><?=$base_q?>">&raquo;</a></li>
                                    <?php endif; ?>
                                </ul>
                                <p style="font-size:12px;color:#9aa3ad;">Showing <?=count($txns)?> of <?=$total?> records</p>
                            </div>
                            <?php endif; ?>

                            <?php endif; ?>

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
