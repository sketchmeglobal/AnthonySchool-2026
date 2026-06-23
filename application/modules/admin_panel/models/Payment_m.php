<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_m extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // -------------------------------------------------------------------------
    // Step 1 view data
    // -------------------------------------------------------------------------
    public function pay_plan()
    {
        $student     = $this->_get_student_info();
        $fees        = $this->_get_student_fees($student['std_seq'], $student['cs_seq']);
        $paid_months = $this->_get_paid_months($student['std_seq']);

        $fy_parts = explode('-', FINANCIAL_YEAR);
        $fy_start = (int)$fy_parts[0]; // e.g. 2026

        $months = [];

        // Class_Type 4 = Higher Secondary (11 & 12) → April to March (financial year)
        // All others (Nursery, Primary, Secondary)   → January to December (calendar year)
        if ($student['class_type'] === 4) {
            $month_order = [4,5,6,7,8,9,10,11,12,1,2,3];
            foreach ($month_order as $m) {
                $y   = ($m >= 4) ? $fy_start : ($fy_start + 1);
                $key = $y . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
                $months[] = [
                    'key'   => $key,
                    'month' => $m,
                    'year'  => $y,
                    'label' => date('F Y', mktime(0, 0, 0, $m, 1, $y)),
                    'short' => date('M', mktime(0, 0, 0, $m, 1, $y)),
                    'paid'  => isset($paid_months[$key]),
                ];
            }
        } else {
            $month_order = [1,2,3,4,5,6,7,8,9,10,11,12];
            foreach ($month_order as $m) {
                $y   = $fy_start; // all 12 months in same calendar year
                $key = $y . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
                $months[] = [
                    'key'   => $key,
                    'month' => $m,
                    'year'  => $y,
                    'label' => date('F Y', mktime(0, 0, 0, $m, 1, $y)),
                    'short' => date('M', mktime(0, 0, 0, $m, 1, $y)),
                    'paid'  => isset($paid_months[$key]),
                ];
            }
        }

        $data = [
            'tab_title'          => 'Choose Plan',
            'section_heading'    => 'Select a Payment Plan',
            'menu_name'          => 'Payment',
            'student'            => $student,
            'monthly_amount'     => $fees['monthly_amount'],
            'yearly_amount'      => $fees['yearly_amount'],
            'monthly_standard'   => $fees['monthly_standard'],
            'yearly_standard'    => $fees['yearly_standard'],
            'monthly_concession' => $fees['monthly_concession'],
            'yearly_concession'  => $fees['yearly_concession'],
            'months'             => $months,
            'paid_months'        => $paid_months,
            'current_month'      => date('F Y'),
            'month_paid'         => false, // kept for backward compat; individual months tracked via $months
        ];

        return ['page' => 'payment/payment_plan_v', 'data' => $data];
    }

    // -------------------------------------------------------------------------
    // FIX 1 — public fee lookup used by controller to avoid trusting session amount
    public function get_fee_amount($std_seq, $cs_seq, $plan_type)
    {
        $fees = $this->_get_student_fees($std_seq, $cs_seq);
        return ($plan_type === 'yearly') ? $fees['yearly_amount'] : $fees['monthly_amount'];
    }

    // Step 2 view data
    // -------------------------------------------------------------------------
    public function pay_method()
    {
        $student  = $this->_get_student_info();
        $fees     = $this->_get_student_fees($student['std_seq'], $student['cs_seq']);
        $plan_type = $this->session->userdata('pay_plan_type');

        $amount = ($plan_type === 'yearly') ? $fees['yearly_amount'] : $fees['monthly_amount'];
        $period = ($plan_type === 'yearly') ? 'Annual ' . FINANCIAL_YEAR : date('F Y');

        $data = [
            'tab_title'       => 'Payment Method',
            'section_heading' => 'Select Payment Method',
            'menu_name'       => 'Payment',
            'student'         => $student,
            'plan_type'       => $plan_type,
            'plan_label'      => ($plan_type === 'yearly') ? 'Yearly Fee' : 'Monthly Fee',
            'period'          => $period,
            'amount'          => $amount,
        ];

        return ['page' => 'payment/payment_method_v', 'data' => $data];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function _get_student_info()
    {
        $tbl_id = (int)$this->session->userdata('tbl_id');
        $row    = $this->db->query("
            SELECT sd.*, csh.Class_Type
            FROM student_details sd
            LEFT JOIN class_sec_hdr csh ON csh.CS_SEQ = sd.STD_CS_SEQ
            WHERE sd.STD_SEQ = ?
        ", [$tbl_id])->row_array();

        return [
            'std_seq'    => $tbl_id,
            'cs_seq'     => !empty($row['STD_CS_SEQ']) ? (int)$row['STD_CS_SEQ'] : 0,
            'class_type' => !empty($row['Class_Type']) ? (int)$row['Class_Type'] : 1,
            'id'         => $tbl_id,
            'name'       => $this->session->userdata('name') ?: 'Student',
            'reg_no'     => $this->session->userdata('username') ?: '',
            'class'      => '',
            'section'    => '',
        ];
    }

    private function _get_student_fees($std_seq, $cs_seq)
    {
        // --- Student-specific concession fees ---
        // Must join class_sec_dtl to get CS_FEES_TYPE (0=monthly,1=yearly).
        // acc_master.Fees_Type is an FK to fees_type table, not 0/1 values.
        $conc_rows = $this->db->query("
            SELECT csd.CS_FEES_TYPE AS Fees_Type, fc.Fees
            FROM fees_concession fc
            JOIN acc_master am ON am.ACC_MASTER_CODE = fc.ACC_MASTER_CODE
            JOIN class_sec_dtl csd ON csd.ACC_MASTER_CODE = am.ACC_MASTER_CODE AND csd.CS_SEQ = ?
            WHERE fc.std_id = ? AND fc.class_id = ? AND csd.CS_FEES_TYPE IN (0, 1) AND am.STATUS = 1
        ", [$cs_seq, $std_seq, $cs_seq])->result_array();

        // --- Class-level standard fees from class_sec_dtl ---
        $std_rows = $this->db->query("
            SELECT csd.CS_FEES_TYPE AS Fees_Type, csd.Fees
            FROM class_sec_dtl csd
            JOIN acc_master am ON am.ACC_MASTER_CODE = csd.ACC_MASTER_CODE
            WHERE csd.CS_SEQ = ? AND csd.CS_FEES_TYPE IN (0, 1) AND am.STATUS = 1
        ", [$cs_seq])->result_array();

        $monthly_std = 0.0;
        $yearly_std  = 0.0;
        foreach ($std_rows as $r) {
            if ((int)$r['Fees_Type'] === 0) $monthly_std += (float)$r['Fees'];
            if ((int)$r['Fees_Type'] === 1) $yearly_std  += (float)$r['Fees'];
        }

        $monthly_actual = 0.0;
        $yearly_actual  = 0.0;
        $has_conc       = !empty($conc_rows);

        if ($has_conc) {
            foreach ($conc_rows as $r) {
                if ((int)$r['Fees_Type'] === 0) $monthly_actual += (float)$r['Fees'];
                if ((int)$r['Fees_Type'] === 1) $yearly_actual  += (float)$r['Fees'];
            }
        } else {
            // No concession record set up — use class standard
            $monthly_actual = $monthly_std;
            $yearly_actual  = $yearly_std;
        }

        return [
            'monthly_amount'     => $monthly_actual,
            'yearly_amount'      => $yearly_actual,
            'monthly_standard'   => $monthly_std,
            'yearly_standard'    => $yearly_std,
            'monthly_concession' => round(max(0, $monthly_std - $monthly_actual), 2),
            'yearly_concession'  => round(max(0, $yearly_std  - $yearly_actual),  2),
        ];
    }

    /**
     * Returns a map of already-paid month keys for a student.
     * Key format: "2025-04", "2026-01", etc.
     * Checks both online (via allocations) and manual/cash payments.
     */
    private function _get_paid_months($std_seq)
    {
        $paid = [];

        // Online payments: check allocations (covers single and multi-month)
        $rows = $this->db->query("
            SELECT a.period_month AS m, a.period_year AS y
            FROM tbl_payment_allocation a
            JOIN tbl_payment_transaction t ON t.id = a.transaction_id
            WHERE a.student_id = ? AND t.plan_type = 'monthly' AND t.status = 'success'
        ", [$std_seq])->result_array();
        foreach ($rows as $r) {
            $paid[$r['y'] . '-' . str_pad($r['m'], 2, '0', STR_PAD_LEFT)] = true;
        }

        // Manual/cash payments
        $rows2 = $this->db->query("
            SELECT YEAR(FM_HDR_COL_DATE) AS y, MONTH(FM_HDR_COL_DATE) AS m
            FROM fees_monthly_hdr
            WHERE FM_HDR_STD_SEQ = ?
        ", [$std_seq])->result_array();
        foreach ($rows2 as $r) {
            $paid[$r['y'] . '-' . str_pad($r['m'], 2, '0', STR_PAD_LEFT)] = true;
        }

        return $paid;
    }

    private function _is_month_paid($std_seq)
    {
        // Check manual/cash payments
        $r = $this->db->query("
            SELECT COUNT(*) AS cnt FROM fees_monthly_hdr
            WHERE FM_HDR_STD_SEQ = ?
              AND YEAR(FM_HDR_COL_DATE)  = YEAR(CURDATE())
              AND MONTH(FM_HDR_COL_DATE) = MONTH(CURDATE())
        ", [$std_seq])->row_array();
        if (!empty($r['cnt']) && (int)$r['cnt'] > 0) return true;

        // Check online payments via gateway
        $r2 = $this->db->query("
            SELECT COUNT(*) AS cnt FROM tbl_payment_transaction
            WHERE student_id = ? AND plan_type = 'monthly' AND status = 'success'
              AND YEAR(completed_at)  = YEAR(CURDATE())
              AND MONTH(completed_at) = MONTH(CURDATE())
        ", [$std_seq])->row_array();
        return !empty($r2['cnt']) && (int)$r2['cnt'] > 0;
    }
}
