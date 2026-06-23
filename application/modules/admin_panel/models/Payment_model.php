<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Payment_model
 * All DB writes related to Easebuzz payment.
 * Uses DB transactions to make success finalization ATOMIC.
 */
class Payment_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->_ensure_tables();
    }

    private function _ensure_tables()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `tbl_payment_transaction` (
              `id`              int(11)          NOT NULL AUTO_INCREMENT,
              `txnid`           varchar(50)      NOT NULL,
              `student_id`      int(11)          NOT NULL,
              `student_reg_no`  varchar(50)      DEFAULT NULL,
              `student_name`    varchar(255)     DEFAULT NULL,
              `email`           varchar(191)     NOT NULL,
              `phone`           varchar(15)      NOT NULL,
              `plan_type`       enum('monthly','yearly') NOT NULL,
              `plan_label`      varchar(255)     DEFAULT NULL,
              `pay_method`      enum('card','upi','netbanking') DEFAULT NULL,
              `amount`          decimal(10,2)    NOT NULL,
              `currency`        varchar(5)       NOT NULL DEFAULT 'INR',
              `productinfo`     varchar(500)     DEFAULT NULL,
              `status`          enum('pending','initiated','success','failed','cancelled') NOT NULL DEFAULT 'pending',
              `hash_verified`   tinyint(1)       DEFAULT '0',
              `easepayid`       varchar(100)     DEFAULT NULL,
              `gateway_status`  varchar(50)      DEFAULT NULL,
              `gateway_mode`    varchar(50)      DEFAULT NULL,
              `bank_ref_num`    varchar(100)     DEFAULT NULL,
              `bankcode`        varchar(50)      DEFAULT NULL,
              `error_code`      varchar(100)     DEFAULT NULL,
              `error_message`   varchar(500)     DEFAULT NULL,
              `cardnum`         varchar(20)      DEFAULT NULL,
              `name_on_card`    varchar(100)     DEFAULT NULL,
              `issuing_bank`    varchar(100)     DEFAULT NULL,
              `raw_response`    mediumtext       DEFAULT NULL,
              `ip_address`      varchar(45)      DEFAULT NULL,
              `user_agent`      varchar(500)     DEFAULT NULL,
              `created_by`      int(11)          DEFAULT NULL,
              `created_at`      datetime         NOT NULL,
              `initiated_at`    datetime         DEFAULT NULL,
              `completed_at`    datetime         DEFAULT NULL,
              `updated_at`      datetime         DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `uq_txnid` (`txnid`),
              KEY `idx_student_id` (`student_id`),
              KEY `idx_status` (`status`),
              KEY `idx_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `tbl_payment_allocation` (
              `id`             int(11)       NOT NULL AUTO_INCREMENT,
              `transaction_id` int(11)       NOT NULL,
              `txnid`          varchar(50)   NOT NULL,
              `student_id`     int(11)       NOT NULL,
              `fee_head`       varchar(255)  NOT NULL,
              `period_label`   varchar(100)  DEFAULT NULL,
              `period_month`   tinyint(2)    DEFAULT NULL,
              `period_year`    smallint(4)   DEFAULT NULL,
              `amount`         decimal(10,2) NOT NULL,
              `created_at`     datetime      NOT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_transaction_id` (`transaction_id`),
              KEY `idx_student_id` (`student_id`),
              CONSTRAINT `fk_alloc_txn` FOREIGN KEY (`transaction_id`) REFERENCES `tbl_payment_transaction` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Add payment_meta column if not yet present (safe to re-run)
        $col = $this->db->query("SHOW COLUMNS FROM `tbl_payment_transaction` LIKE 'payment_meta'")->row_array();
        if (empty($col)) {
            $this->db->query("ALTER TABLE `tbl_payment_transaction` ADD COLUMN `payment_meta` TEXT DEFAULT NULL");
        }

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `tbl_payment_webhook_log` (
              `id`              int(11)       NOT NULL AUTO_INCREMENT,
              `txnid`           varchar(50)   DEFAULT NULL,
              `easepayid`       varchar(100)  DEFAULT NULL,
              `gateway_status`  varchar(50)   DEFAULT NULL,
              `hash_verified`   tinyint(1)    DEFAULT '0',
              `processed`       tinyint(1)    DEFAULT '0',
              `process_message` varchar(500)  DEFAULT NULL,
              `ip_address`      varchar(45)   DEFAULT NULL,
              `payload`         mediumtext    DEFAULT NULL,
              `received_at`     datetime      NOT NULL,
              PRIMARY KEY (`id`),
              KEY `idx_txnid` (`txnid`),
              KEY `idx_received_at` (`received_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function create_pending($data)
    {
        $row = [
            'txnid'          => $data['txnid'],
            'student_id'     => (int)$data['student_id'],
            'student_reg_no' => isset($data['student_reg_no']) ? $data['student_reg_no'] : null,
            'student_name'   => isset($data['student_name'])   ? $data['student_name']   : null,
            'email'          => $data['email'],
            'phone'          => $data['phone'],
            'plan_type'      => $data['plan_type'],
            'plan_label'     => isset($data['plan_label']) ? $data['plan_label'] : null,
            'pay_method'     => $data['pay_method'],
            'amount'         => number_format((float)$data['amount'], 2, '.', ''),
            'currency'       => 'INR',
            'productinfo'    => $data['productinfo'],
            'status'         => 'pending',
            'ip_address'     => $this->input->ip_address(),
            'user_agent'     => substr((string)$this->input->user_agent(), 0, 500),
            'created_by'     => isset($data['created_by']) ? (int)$data['created_by'] : null,
            'payment_meta'   => isset($data['payment_meta']) ? $data['payment_meta'] : null,
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        $ok = $this->db->insert('tbl_payment_transaction', $row);
        if (!$ok) {
            log_message('error', 'Payment_model::create_pending failed: ' . print_r($this->db->error(), true));
            return false;
        }
        return (int)$this->db->insert_id();
    }

    // FIX 2 — double-charge guard
    public function has_paid($student_id, $plan_type)
    {
        $student_id = (int)$student_id;

        if ($plan_type === 'monthly') {
            // Online payments this calendar month
            $r = $this->db->query("
                SELECT id FROM tbl_payment_transaction
                WHERE student_id = ? AND plan_type = 'monthly' AND status = 'success'
                  AND YEAR(completed_at) = YEAR(CURDATE())
                  AND MONTH(completed_at) = MONTH(CURDATE())
                LIMIT 1
            ", [$student_id])->row_array();
            if (!empty($r)) return true;

            // Cash/manual payments this month
            $r2 = $this->db->query("
                SELECT FM_HDR_SRLNO FROM fees_monthly_hdr
                WHERE FM_HDR_STD_SEQ = ?
                  AND YEAR(FM_HDR_COL_DATE) = YEAR(CURDATE())
                  AND MONTH(FM_HDR_COL_DATE) = MONTH(CURDATE())
                LIMIT 1
            ", [$student_id])->row_array();
            return !empty($r2);
        }

        // Yearly — check current financial year
        $fin_year = (string)date('Y');
        $r = $this->db->query("
            SELECT id FROM tbl_payment_transaction
            WHERE student_id = ? AND plan_type = 'yearly' AND status = 'success'
              AND YEAR(completed_at) = ?
            LIMIT 1
        ", [$student_id, $fin_year])->row_array();
        if (!empty($r)) return true;

        $r2 = $this->db->query("
            SELECT FM_HDR_SRLNO FROM fees_yearly_hdr
            WHERE FM_HDR_STD_SEQ = ? AND FM_HDR_FIN_YEAR = ?
            LIMIT 1
        ", [$student_id, $fin_year])->row_array();
        return !empty($r2);
    }

    /**
     * For multi-month: returns the label of the first already-paid month, or false if all clear.
     * Checks tbl_payment_allocation (covers both single and multi-month online payments)
     * and fees_monthly_hdr (manual/cash payments).
     */
    public function has_paid_months($student_id, $selected_months)
    {
        $student_id = (int)$student_id;
        foreach ($selected_months as $m) {
            $year  = (int)$m['year'];
            $month = (int)$m['month'];

            // Online payment allocations
            $r = $this->db->query("
                SELECT a.id FROM tbl_payment_allocation a
                JOIN tbl_payment_transaction t ON t.id = a.transaction_id
                WHERE a.student_id = ? AND t.plan_type = 'monthly' AND t.status = 'success'
                  AND a.period_year = ? AND a.period_month = ?
                LIMIT 1
            ", [$student_id, $year, $month])->row_array();
            if (!empty($r)) return $m['label'];

            // Manual/cash payments
            $r2 = $this->db->query("
                SELECT FM_HDR_SRLNO FROM fees_monthly_hdr
                WHERE FM_HDR_STD_SEQ = ?
                  AND YEAR(FM_HDR_COL_DATE) = ? AND MONTH(FM_HDR_COL_DATE) = ?
                LIMIT 1
            ", [$student_id, $year, $month])->row_array();
            if (!empty($r2)) return $m['label'];
        }
        return false;
    }

    public function mark_initiated($txnid)
    {
        $this->db->where('txnid', $txnid);
        $this->db->update('tbl_payment_transaction', [
            'status'       => 'initiated',
            'initiated_at' => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);
        return $this->db->affected_rows() > 0;
    }

    public function get_by_txnid($txnid)
    {
        return $this->db
            ->where('txnid', $txnid)
            ->get('tbl_payment_transaction')
            ->row_array();
    }

    /**
     * Atomic finalization on success.
     * 1. Lock row, 2. Update status, 3. Insert allocations.
     * Rollback everything on any failure.
     */
    public function finalize_success($txnid, $gateway_fields, $allocations = [])
    {
        $this->db->trans_begin();

        try {
            $row = $this->db->query(
                "SELECT * FROM tbl_payment_transaction WHERE txnid = ? FOR UPDATE",
                [$txnid]
            )->row_array();

            if (!$row) {
                throw new Exception("Row not found: txnid={$txnid}");
            }

            if ($row['status'] === 'success') {
                $this->db->trans_commit();
                return true;
            }

            if (!in_array($row['status'], ['pending', 'initiated'], true)) {
                throw new Exception("Cannot finalize: txnid={$txnid} in status '{$row['status']}'");
            }

            $update = array_merge([
                'status'        => 'success',
                'hash_verified' => 1,
                'completed_at'  => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ], $this->_pick_gateway_fields($gateway_fields));

            $this->db->where('txnid', $txnid);
            $this->db->update('tbl_payment_transaction', $update);

            if (!empty($allocations) && is_array($allocations)) {
                foreach ($allocations as $a) {
                    $this->db->insert('tbl_payment_allocation', [
                        'transaction_id' => $row['id'],
                        'txnid'          => $txnid,
                        'student_id'     => $row['student_id'],
                        'fee_head'       => $a['fee_head'],
                        'period_label'   => isset($a['period_label']) ? $a['period_label'] : null,
                        'period_month'   => isset($a['period_month']) ? $a['period_month'] : null,
                        'period_year'    => isset($a['period_year'])  ? $a['period_year']  : null,
                        'amount'         => number_format((float)$a['amount'], 2, '.', ''),
                        'created_at'     => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            if ($this->db->trans_status() === FALSE) {
                throw new Exception("DB trans_status FALSE during finalize");
            }

            $this->db->trans_commit();
            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Payment_model::finalize_success rollback. txnid=' . $txnid
                . ' err=' . $e->getMessage());
            return false;
        }
    }

    public function finalize_failure($txnid, $gateway_fields, $status = 'failed')
    {
        if (!in_array($status, ['failed', 'cancelled'], true)) {
            $status = 'failed';
        }

        $row = $this->get_by_txnid($txnid);
        if (!$row) {
            log_message('error', "finalize_failure: row not found txnid={$txnid}");
            return false;
        }
        if ($row['status'] === 'success') {
            return true;
        }

        $update = array_merge([
            'status'        => $status,
            'hash_verified' => isset($gateway_fields['hash_verified']) ? (int)$gateway_fields['hash_verified'] : 0,
            'completed_at'  => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ], $this->_pick_gateway_fields($gateway_fields));

        $this->db->where('txnid', $txnid);
        $this->db->update('tbl_payment_transaction', $update);
        return $this->db->affected_rows() >= 0;
    }

    public function log_webhook($txnid, $easepayid, $gateway_status, $hash_verified, $processed, $process_message, $payload)
    {
        $this->db->insert('tbl_payment_webhook_log', [
            'txnid'           => $txnid,
            'easepayid'       => $easepayid,
            'gateway_status'  => $gateway_status,
            'hash_verified'   => $hash_verified ? 1 : 0,
            'processed'       => $processed ? 1 : 0,
            'process_message' => substr((string)$process_message, 0, 500),
            'ip_address'      => $this->input->ip_address(),
            'payload'         => is_string($payload) ? $payload : json_encode($payload),
            'received_at'     => date('Y-m-d H:i:s'),
        ]);
        return (int)$this->db->insert_id();
    }

    // =========================================================================
    // HISTORY QUERIES
    // =========================================================================

    /**
     * Fetch transactions for a single student (student self-service).
     */
    public function get_student_transactions($student_id, $limit = 50, $offset = 0)
    {
        return $this->db
            ->where('student_id', (int)$student_id)
            ->order_by('created_at', 'DESC')
            ->limit((int)$limit, (int)$offset)
            ->get('tbl_payment_transaction')
            ->result_array();
    }

    public function count_student_transactions($student_id)
    {
        return (int)$this->db
            ->where('student_id', (int)$student_id)
            ->count_all_results('tbl_payment_transaction');
    }

    /**
     * Returns per-status totals for a student: [status => [count, amount_sum]]
     */
    public function get_student_payment_stats($student_id)
    {
        $rows = $this->db->query(
            "SELECT status, COUNT(*) AS cnt, COALESCE(SUM(amount),0) AS total
             FROM tbl_payment_transaction
             WHERE student_id = ?
             GROUP BY status",
            [(int)$student_id]
        )->result_array();

        $stats = [];
        foreach ($rows as $r) {
            $stats[$r['status']] = ['count' => (int)$r['cnt'], 'amount' => (float)$r['total']];
        }
        return $stats;
    }

    /**
     * Fetch all transactions for admin with optional filters.
     * $filters keys: status, plan_type, date_from, date_to, search (student name/reg/txnid)
     */
    public function get_all_transactions($filters = [], $limit = 0, $offset = 0)
    {
        $this->_apply_admin_filters($filters);
        $this->db->order_by('created_at', 'DESC');
        if ($limit > 0) {
            $this->db->limit((int)$limit, (int)$offset);
        }
        return $this->db->get('tbl_payment_transaction')->result_array();
    }

    public function count_all_transactions($filters = [])
    {
        $this->_apply_admin_filters($filters);
        return (int)$this->db->count_all_results('tbl_payment_transaction');
    }

    private function _apply_admin_filters($filters)
    {
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        if (!empty($filters['plan_type'])) {
            $this->db->where('plan_type', $filters['plan_type']);
        }
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(created_at) >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(created_at) <=', $filters['date_to']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search']; // ->like() escapes internally
            $this->db->group_start();
            $this->db->like('student_name', $s, 'both');
            $this->db->or_like('student_reg_no', $s, 'both');
            $this->db->or_like('txnid', $s, 'both');
            $this->db->group_end();
        }
    }

    // =========================================================================
    // Offline (cash/cheque) payment history for a student
    // Returns one row per receipt with a comma-separated list of months paid.
    // =========================================================================
    public function get_student_offline_payments($student_id, $fin_year = null)
    {
        $student_id = (int)$student_id;
        $month_names = [
            1=>'January',2=>'February',3=>'March',4=>'April',
            5=>'May',6=>'June',7=>'July',8=>'August',
            9=>'September',10=>'October',11=>'November',12=>'December',
        ];

        $sql = "
            SELECT h.FM_HDR_SRLNO, h.FM_HDR_RCPT_NO, h.FM_HDR_COL_DATE,
                   h.FM_HDR_P_TYP, h.FM_HDR_B_NAME, h.FM_HDR_TOT_FEES, h.FM_HDR_FIN_YEAR,
                   GROUP_CONCAT(DISTINCT d.FEES_DTL_MONTH ORDER BY d.FEES_DTL_MONTH ASC) AS months_csv
            FROM fees_monthly_hdr h
            LEFT JOIN fees_monthly_dtl d ON d.FEES_DTL_HDR_SRLNO = h.FM_HDR_SRLNO
            WHERE h.FM_HDR_STD_SEQ = ?
        ";
        $params = [$student_id];
        if ($fin_year) {
            $sql .= " AND h.FM_HDR_FIN_YEAR = ?";
            $params[] = $fin_year;
        }
        $sql .= " GROUP BY h.FM_HDR_SRLNO ORDER BY h.FM_HDR_COL_DATE DESC";

        $rows = $this->db->query($sql, $params)->result_array();

        // Convert month numbers to names for display
        foreach ($rows as &$row) {
            $label = '';
            if (!empty($row['months_csv'])) {
                $nums  = explode(',', $row['months_csv']);
                $names = array_map(function($n) use ($month_names) {
                    return isset($month_names[(int)$n]) ? $month_names[(int)$n] : $n;
                }, $nums);
                $label = implode(', ', $names);
            }
            $row['months_label'] = $label;
        }
        unset($row);
        return $rows;
    }

    // =========================================================================

    private function _pick_gateway_fields($g)
    {
        $map = [
            'easepayid'      => isset($g['easepayid'])      ? $g['easepayid']      : null,
            'gateway_status' => isset($g['gateway_status']) ? $g['gateway_status'] : null,
            'gateway_mode'   => isset($g['gateway_mode'])   ? $g['gateway_mode']   : null,
            'bank_ref_num'   => isset($g['bank_ref_num'])   ? $g['bank_ref_num']   : null,
            'bankcode'       => isset($g['bankcode'])       ? $g['bankcode']       : null,
            'error_code'     => isset($g['error_code'])     ? $g['error_code']     : null,
            'error_message'  => isset($g['error_message'])  ? substr((string)$g['error_message'], 0, 500) : null,
            'cardnum'        => isset($g['cardnum'])        ? $g['cardnum']        : null,
            'name_on_card'   => isset($g['name_on_card'])   ? $g['name_on_card']   : null,
            'issuing_bank'   => isset($g['issuing_bank'])   ? $g['issuing_bank']   : null,
            'raw_response'   => isset($g['raw_response'])   ? $g['raw_response']   : null,
        ];
        return array_filter($map, function($v){ return $v !== null; });
    }
}
