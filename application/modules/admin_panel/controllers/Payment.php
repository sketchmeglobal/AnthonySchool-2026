<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends My_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->user_type = $this->session->has_userdata('user_id')
                         ? (int)$this->session->usertype : 0;
        $this->load->helper(['url', 'form']);
        $this->load->model('Payment_m');
        $this->load->model('Payment_model');
        $this->load->config('easebuzz');
    }

    // =========================================================================
    // STEP 1 — Choose Plan
    // =========================================================================
    public function pay_plan()
    {
        $this->_require_student();
        $this->session->unset_userdata(['pay_plan_data', 'pay_method_data']);
        $data = $this->Payment_m->pay_plan();
        $this->load->view($data['page'], $data['data']);
    }

    // =========================================================================
    // STEP 2 — Choose Payment Method
    // =========================================================================
    public function pay_method()
    {
        $this->_require_student();

        $plan_type = $this->input->post('plan_type', true);
        if (!in_array($plan_type, ['monthly', 'yearly'], true)) {
            $this->session->set_flashdata('error', 'Please choose a valid plan.');
            redirect('admin/pay_plan');
        }

        $student   = $this->_get_logged_in_student();
        $view_data = $this->Payment_m->pay_method();
        $per_unit  = (float)$view_data['data']['amount']; // per-month or yearly amount from DB

        if ($plan_type === 'monthly') {
            // ── Multi-month selection ────────────────────────────────────────
            $raw = $this->input->post('selected_months', true); // array of "YYYY-MM" keys
            if (empty($raw) || !is_array($raw)) {
                $this->session->set_flashdata('error', 'Please select at least one month.');
                redirect('admin/pay_plan');
            }

            $selected_months = [];
            foreach ($raw as $key) {
                if (preg_match('/^(\d{4})-(\d{2})$/', $key, $match)) {
                    $yr = (int)$match[1];
                    $mo = (int)$match[2];
                    if ($yr >= 2020 && $yr <= 2099 && $mo >= 1 && $mo <= 12) {
                        $selected_months[] = [
                            'key'   => $key,
                            'year'  => $yr,
                            'month' => $mo,
                            'label' => date('F Y', mktime(0, 0, 0, $mo, 1, $yr)),
                        ];
                    }
                }
            }

            if (empty($selected_months)) {
                $this->session->set_flashdata('error', 'Invalid month selection. Please try again.');
                redirect('admin/pay_plan');
            }

            $months_count  = count($selected_months);
            $total_amount  = round($per_unit * $months_count, 2);
            $month_labels  = array_column($selected_months, 'label');

            // Compact label: full names ≤3 months, abbreviated range for more
            if ($months_count <= 3) {
                $label = 'Monthly Fee - ' . implode(', ', $month_labels);
            } else {
                $first = reset($month_labels);
                $last  = end($month_labels);
                $label = "Monthly Fee - {$first} to {$last} ({$months_count} months)";
            }

            $view_data['data']['amount']  = $total_amount;
            $view_data['data']['period']  = $months_count === 1 ? $month_labels[0] : implode(', ', $month_labels);

            $this->session->set_userdata('pay_plan_data', [
                'plan_type'        => 'monthly',
                'plan_label'       => $label,
                'amount'           => $total_amount,
                'amount_per_month' => $per_unit,
                'months_count'     => $months_count,
                'selected_months'  => $selected_months,
                'student_id'       => $student['id'],
                'student_reg_no'   => $student['reg_no'],
                'student_name'     => $student['name'],
                'email'            => $student['email'],
                'phone'            => $student['phone'],
            ]);
        } else {
            // ── Yearly (unchanged) ───────────────────────────────────────────
            $label = 'Yearly Fee - FY ' . FINANCIAL_YEAR;
            $this->session->set_userdata('pay_plan_data', [
                'plan_type'      => 'yearly',
                'plan_label'     => $label,
                'amount'         => $per_unit,
                'student_id'     => $student['id'],
                'student_reg_no' => $student['reg_no'],
                'student_name'   => $student['name'],
                'email'          => $student['email'],
                'phone'          => $student['phone'],
            ]);
        }

        $this->session->set_userdata('pay_plan_type', $plan_type);
        $this->load->view($view_data['page'], $view_data['data']);
    }

    // =========================================================================
    // STEP 3 — Confirm & Pay
    // =========================================================================
    public function pay_form()
    {
        $this->_require_student();

        $plan_data = $this->session->userdata('pay_plan_data');
        if (empty($plan_data)) { redirect('admin/pay_plan'); }

        $pay_method_post = $this->input->post('pay_method', true);
        if ($pay_method_post !== null) {
            if (!in_array($pay_method_post, ['card', 'upi', 'netbanking'], true)) {
                $this->session->set_flashdata('error', 'Please choose a valid payment method.');
                redirect('admin/pay_method');
            }
            $this->session->set_userdata('pay_method_data', ['pay_method' => $pay_method_post]);
            $this->session->set_userdata('pay_method', $pay_method_post);
        }

        if (empty($this->session->userdata('pay_method_data'))) {
            $this->session->set_flashdata('error', 'Please choose a payment method first.');
            redirect('admin/pay_method');
        }

        // FIX 4: generate one-time cryptographic nonce for this form load
        $nonce = bin2hex(random_bytes(16));
        $this->session->set_userdata('payment_nonce', $nonce);

        $data = [
            'tab_title'       => 'Confirm & Pay',
            'menu_name'       => 'Payment',
            'section_heading' => 'Confirm Your Payment',
            'plan_data'       => $plan_data,
            'method_data'     => $this->session->userdata('pay_method_data'),
            'merchant_key'    => $this->config->item('easebuzz_live_key'),
            'easebuzz_env'    => 'prod',
            'payment_nonce'   => $nonce,
        ];
        $this->load->view('payment/payment_form_v', $data);
    }

    // =========================================================================
    // AJAX — returns access_key for Easebuzz JS popup
    // =========================================================================
    public function pay_initiate_ajax()
    {
        $this->output->set_content_type('application/json');

        if ($this->user_type !== 4) {
            echo json_encode(['status' => 0, 'message' => 'Unauthorized']); return;
        }

        // ── FIX 4: CSRF — verify one-time nonce ──────────────────────────────
        $nonce        = $this->input->post('payment_nonce', true);
        $stored_nonce = $this->session->userdata('payment_nonce');
        $this->session->unset_userdata('payment_nonce'); // destroy immediately — one use only

        if (empty($nonce) || empty($stored_nonce) || !hash_equals($stored_nonce, $nonce)) {
            log_message('error', 'pay_initiate_ajax: nonce mismatch ip=' . $this->input->ip_address());
            echo json_encode(['status' => 0, 'message' => 'Invalid request token. Please refresh the page and try again.']); return;
        }
        // ─────────────────────────────────────────────────────────────────────

        $plan_data   = $this->session->userdata('pay_plan_data');
        $method_data = $this->session->userdata('pay_method_data');

        if (empty($plan_data) || empty($method_data)) {
            echo json_encode(['status' => 0, 'message' => 'Session expired. Please start again.']); return;
        }
        if (!$this->input->post('confirm')) {
            echo json_encode(['status' => 0, 'message' => 'Please tick the confirmation checkbox.']); return;
        }

        $student_id      = (int)$plan_data['student_id'];
        $plan_type       = $plan_data['plan_type'];
        $selected_months = !empty($plan_data['selected_months']) ? $plan_data['selected_months'] : null;

        // ── Double-charge guard ───────────────────────────────────────────────
        if ($plan_type === 'monthly' && !empty($selected_months)) {
            $already_paid = $this->Payment_model->has_paid_months($student_id, $selected_months);
            if ($already_paid) {
                log_message('info', "pay_initiate_ajax: month already paid. student={$student_id}, month={$already_paid}");
                echo json_encode(['status' => 0, 'message' => "{$already_paid} has already been paid. Please remove it from your selection and try again."]); return;
            }
        } else {
            if ($this->Payment_model->has_paid($student_id, $plan_type)) {
                log_message('info', "pay_initiate_ajax: already paid. student={$student_id}, plan={$plan_type}");
                echo json_encode(['status' => 0, 'message' => 'Payment for this period has already been completed. Please check your payment history.']); return;
            }
        }
        // ─────────────────────────────────────────────────────────────────────

        // ── Amount re-validation — never trust session amount ─────────────────
        $std_row      = $this->db->get_where('student_details', ['STD_SEQ' => $student_id])->row_array();
        $cs_seq       = !empty($std_row['STD_CS_SEQ']) ? (int)$std_row['STD_CS_SEQ'] : 0;
        $per_unit_fee = (float)$this->Payment_m->get_fee_amount($student_id, $cs_seq, 'monthly');

        if ($plan_type === 'monthly' && !empty($selected_months)) {
            // Re-compute total from DB per-month rate × month count (never trust session amount)
            $months_count = count($selected_months);
            $db_fee       = round($per_unit_fee * $months_count, 2);
        } else {
            $db_fee = (float)$this->Payment_m->get_fee_amount($student_id, $cs_seq, $plan_type);
        }

        if ($db_fee <= 0) {
            log_message('error', "pay_initiate_ajax: zero fee. student={$student_id}, cs_seq={$cs_seq}");
            echo json_encode(['status' => 0, 'message' => 'Fee amount could not be determined. Please contact the school office.']); return;
        }
        $amount = number_format($db_fee, 2, '.', '');
        // ─────────────────────────────────────────────────────────────────────

        $productinfo = $this->_ascii_safe($plan_data['plan_label']);
        $raw_name    = $plan_data['student_name'];
        $name_part   = strpos($raw_name, '|') !== false ? strstr($raw_name, '|', true) : $raw_name;
        $firstname   = trim(preg_replace('/\s+/', ' ', $this->_ascii_safe($name_part)));
        $phone       = $this->_clean_phone($plan_data['phone']);
        $email       = trim((string)$plan_data['email']);

        if (empty($email)) {
            $email = $this->config->item('easebuzz_fallback_email');
        }
        if (strlen($phone) !== 10) {
            echo json_encode(['status' => 0, 'message' => 'A valid 10-digit mobile number is missing. Please contact the school office.']); return;
        }

        // Store selected months as JSON so _build_allocations can use it even from the webhook
        $payment_meta = (!empty($selected_months))
            ? json_encode(['selected_months' => $selected_months])
            : null;

        $txnid  = $this->_eb_txnid();
        $row_id = $this->Payment_model->create_pending([
            'txnid'          => $txnid,
            'student_id'     => $student_id,
            'student_reg_no' => $plan_data['student_reg_no'],
            'student_name'   => $plan_data['student_name'],
            'email'          => $email,
            'phone'          => $phone,
            'plan_type'      => $plan_type,
            'plan_label'     => $plan_data['plan_label'],
            'pay_method'     => $method_data['pay_method'],
            'amount'         => $amount,
            'productinfo'    => $productinfo,
            'payment_meta'   => $payment_meta,
            'created_by'     => $this->session->userdata('user_id'),
        ]);

        if (!$row_id) {
            echo json_encode(['status' => 0, 'message' => 'Could not create transaction record. Please try again.']); return;
        }

        $params = [
            'txnid'       => $txnid,
            'amount'      => $amount,
            'firstname'   => $firstname,
            'email'       => $email,
            'phone'       => $phone,
            'productinfo' => $productinfo,
            'surl'        => base_url('admin/payment_response'),
            'furl'        => base_url('admin/payment_response'),
            'udf1'        => (string)$student_id,
            'udf2'        => $plan_type,
            'udf3'        => $method_data['pay_method'],
            'udf4'        => '', 'udf5' => '',
        ];

        $result = $this->_eb_initiate($params);

        if (empty($result['status']) || (int)$result['status'] !== 1) {
            $detail = isset($result['error_desc']) ? ' (' . $result['error_desc'] . ')' : '';
            $msg    = (isset($result['data']) ? $result['data'] : 'Gateway error.') . $detail;
            log_message('error', "Easebuzz init failed: txnid={$txnid}, msg={$msg}");
            $this->Payment_model->finalize_failure($txnid, ['error_message' => $msg]);
            // Issue a fresh nonce so the student can retry without reloading the page
            $retry_nonce = bin2hex(random_bytes(16));
            $this->session->set_userdata('payment_nonce', $retry_nonce);
            echo json_encode(['status' => 0, 'message' => $msg, 'retry_nonce' => $retry_nonce]); return;
        }

        $this->Payment_model->mark_initiated($txnid);
        $this->session->set_userdata('current_txnid', $txnid);

        // Issue a retry nonce in case the popup is cancelled — student can try again
        $retry_nonce = bin2hex(random_bytes(16));
        $this->session->set_userdata('payment_nonce', $retry_nonce);

        echo json_encode(['status' => 1, 'access_key' => $result['data'], 'txnid' => $txnid, 'retry_nonce' => $retry_nonce]);
    }

    // =========================================================================
    // GATEWAY RESPONSE HANDLER — surl and furl both point here
    // =========================================================================
    public function payment_response()
    {
        $post = $this->input->post(NULL, true);

        if (empty($post) || empty($post['txnid'])) {
            log_message('error', 'payment_response: empty POST');
            redirect('admin/pay_failed');
        }

        // ── FIX 3: Fake response guard ────────────────────────────────────────
        // Verify the merchant key in the POST matches ours before anything else
        if (!isset($post['key']) || $post['key'] !== $this->config->item('easebuzz_live_key')) {
            log_message('error', 'payment_response: merchant key mismatch. got=' . (isset($post['key']) ? $post['key'] : 'null'));
            redirect('admin/pay_failed');
        }
        // Verify txnid belongs to our prefix (rules out cross-merchant replay)
        $prefix = $this->config->item('easebuzz_txnid_prefix') ?: 'SAS';
        if (strpos($post['txnid'], $prefix) !== 0) {
            log_message('error', 'payment_response: txnid prefix mismatch. txnid=' . $post['txnid']);
            redirect('admin/pay_failed');
        }
        // ─────────────────────────────────────────────────────────────────────

        $txnid = $post['txnid'];
        $row   = $this->Payment_model->get_by_txnid($txnid);

        if (!$row) {
            log_message('error', "payment_response: unknown txnid={$txnid}");
            redirect('admin/pay_failed');
        }

        if (in_array($row['status'], ['success', 'failed', 'cancelled'], true)) {
            log_message('info', "Duplicate callback ignored: txnid={$txnid}, status={$row['status']}");
            redirect($row['status'] === 'success'
                ? 'admin/pay_success/' . urlencode($txnid)
                : 'admin/pay_failed/'  . urlencode($txnid));
        }

        if (!$this->_eb_verify_hash($post)) {
            $this->Payment_model->finalize_failure($txnid, [
                'gateway_status' => isset($post['status'])    ? $post['status']    : null,
                'easepayid'      => isset($post['easepayid']) ? $post['easepayid'] : null,
                'error_message'  => 'Hash verification failed',
                'hash_verified'  => 0,
                'raw_response'   => json_encode($post),
            ]);
            $this->session->set_flashdata('payment_msg',
                'Payment verification failed. Contact support with txn ID: ' . $txnid);
            redirect('admin/pay_failed/' . urlencode($txnid));
        }

        $db_amount      = number_format((float)$row['amount'], 2, '.', '');
        $gateway_amount = number_format((float)(isset($post['amount']) ? $post['amount'] : 0), 2, '.', '');
        if ($db_amount !== $gateway_amount) {
            log_message('error',
                "AMOUNT TAMPERING DETECTED: txnid={$txnid}, db={$db_amount}, gateway={$gateway_amount}");
            $this->Payment_model->finalize_failure($txnid, [
                'error_message' => "Amount mismatch: expected {$db_amount}, got {$gateway_amount}",
                'hash_verified' => 1,
                'raw_response'  => json_encode($post),
            ]);
            redirect('admin/pay_failed/' . urlencode($txnid));
        }

        $api_resp       = $this->_eb_verify_api($txnid, $row['amount'], $row['email'], $row['phone']);
        log_message('info', "Easebuzz API verify: txnid={$txnid} resp=" . json_encode($api_resp));
        $gateway_status = strtolower((string)(isset($post['status']) ? $post['status'] : ''));
        $is_success     = ($gateway_status === 'success');

        $gateway_fields = [
            'easepayid'      => isset($post['easepayid'])     ? $post['easepayid']     : null,
            'gateway_status' => isset($post['status'])        ? $post['status']        : null,
            'gateway_mode'   => isset($post['mode'])          ? $post['mode']          : null,
            'bank_ref_num'   => isset($post['bank_ref_num'])  ? $post['bank_ref_num']  : null,
            'bankcode'       => isset($post['bankcode'])      ? $post['bankcode']      : null,
            'error_code'     => isset($post['error'])         ? $post['error']         : null,
            'error_message'  => isset($post['error_Message']) ? $post['error_Message'] : (isset($post['error_message']) ? $post['error_message'] : null),
            'cardnum'        => isset($post['cardnum'])       ? $post['cardnum']       : null,
            'name_on_card'   => isset($post['name_on_card'])  ? $post['name_on_card']  : null,
            'issuing_bank'   => isset($post['issuing_bank'])  ? $post['issuing_bank']  : null,
            'raw_response'   => json_encode($post),
        ];

        if ($is_success) {
            $allocations = $this->_build_allocations($row);
            $ok          = $this->Payment_model->finalize_success($txnid, $gateway_fields, $allocations);

            if (!$ok) {
                $this->Payment_model->finalize_failure($txnid, array_merge($gateway_fields, [
                    'error_message' => 'Internal finalize failure - rolled back',
                ]));
                $this->session->set_flashdata('payment_msg',
                    'Payment received but could not be recorded. Contact support. Txn ID: ' . $txnid);
                redirect('admin/pay_failed/' . urlencode($txnid));
            }

            log_message('info', "Payment SUCCESS: txnid={$txnid}, amount={$db_amount}");
            $this->session->unset_userdata(['pay_plan_data','pay_method_data','pay_plan_type','pay_method','current_txnid']);
            redirect('admin/pay_success/' . urlencode($txnid));
        } else {
            $final_status = ($gateway_status === 'usercancelled') ? 'cancelled' : 'failed';
            $this->Payment_model->finalize_failure($txnid,
                array_merge($gateway_fields, ['hash_verified' => 1]), $final_status);
            redirect('admin/pay_failed/' . urlencode($txnid));
        }
    }

    // =========================================================================
    // SUCCESS / FAILURE pages
    // =========================================================================
    public function pay_success($txnid = null)
    {
        $this->_require_student();
        $txnid = $txnid ? urldecode($txnid) : null;
        $this->load->view('payment/payment_success_v', [
            'tab_title'       => 'Payment Successful',
            'menu_name'       => 'Payment',
            'section_heading' => 'Payment Successful',
            'txn'             => $txnid ? $this->Payment_model->get_by_txnid($txnid) : null,
        ]);
    }

    public function pay_failed($txnid = null)
    {
        $this->_require_student();
        $txnid = $txnid ? urldecode($txnid) : null;
        $this->load->view('payment/payment_failed_v', [
            'tab_title'       => 'Payment Failed',
            'menu_name'       => 'Payment',
            'section_heading' => 'Payment Failed',
            'txn'             => $txnid ? $this->Payment_model->get_by_txnid($txnid) : null,
        ]);
    }

    // =========================================================================
    // WEBHOOK — server-to-server callback from Easebuzz
    // No student session required. Always respond 200 quickly.
    // =========================================================================
    public function webhook()
    {
        $post = $this->input->post(NULL, true);
        if (empty($post)) {
            $raw  = file_get_contents('php://input');
            $json = json_decode($raw, true);
            if (is_array($json)) { $post = $json; }
        }

        if (empty($post) || empty($post['txnid'])) {
            $this->Payment_model->log_webhook(null, null, null, 0, 0, 'Empty or missing txnid', $post);
            http_response_code(200); echo 'OK'; return;
        }

        $txnid          = $post['txnid'];
        $easepayid      = isset($post['easepayid']) ? $post['easepayid'] : null;
        $gateway_status = isset($post['status'])    ? $post['status']    : null;

        $hash_ok = $this->_eb_verify_hash($post);
        if (!$hash_ok) {
            $this->Payment_model->log_webhook($txnid, $easepayid, $gateway_status, 0, 0, 'Hash mismatch', $post);
            http_response_code(200); echo 'OK'; return;
        }

        $row = $this->Payment_model->get_by_txnid($txnid);
        if (!$row) {
            $this->Payment_model->log_webhook($txnid, $easepayid, $gateway_status, 1, 0, 'No matching DB row', $post);
            http_response_code(200); echo 'OK'; return;
        }

        if ($row['status'] === 'success' || $row['status'] === 'failed' || $row['status'] === 'cancelled') {
            $this->Payment_model->log_webhook($txnid, $easepayid, $gateway_status, 1, 1,
                "Already terminal '{$row['status']}', no action", $post);
            http_response_code(200); echo 'OK'; return;
        }

        $api_resp    = $this->_eb_verify_api($txnid, $row['amount'], $row['email'], $row['phone']);
        $api_success = $this->_api_says_success($api_resp);

        $gw_status_lc = strtolower((string)$gateway_status);
        $is_success   = ($gw_status_lc === 'success') && $api_success;

        $gw_fields = [
            'easepayid'      => $easepayid,
            'gateway_status' => $gateway_status,
            'gateway_mode'   => isset($post['mode'])          ? $post['mode']          : null,
            'bank_ref_num'   => isset($post['bank_ref_num'])  ? $post['bank_ref_num']  : null,
            'bankcode'       => isset($post['bankcode'])      ? $post['bankcode']      : null,
            'error_code'     => isset($post['error'])         ? $post['error']         : null,
            'error_message'  => isset($post['error_Message']) ? $post['error_Message'] : (isset($post['error_message']) ? $post['error_message'] : null),
            'raw_response'   => json_encode($post),
        ];

        if ($is_success) {
            $allocations = $this->_build_allocations($row);
            $ok = $this->Payment_model->finalize_success($txnid, $gw_fields, $allocations);
            $this->Payment_model->log_webhook($txnid, $easepayid, $gateway_status, 1, $ok ? 1 : 0,
                $ok ? 'Finalized success via webhook' : 'finalize_success rolled back', $post);
        } else {
            $final = ($gw_status_lc === 'usercancelled') ? 'cancelled' : 'failed';
            $gw_fields['hash_verified'] = 1;
            $this->Payment_model->finalize_failure($txnid, $gw_fields, $final);
            $this->Payment_model->log_webhook($txnid, $easepayid, $gateway_status, 1, 1,
                'Marked as ' . $final . ' via webhook', $post);
        }

        http_response_code(200); echo 'OK';
    }

    // =========================================================================
    // STUDENT — Online Payment History
    // =========================================================================
    public function my_payment_history()
    {
        $this->_require_student();
        $student_id = (int)$this->session->userdata('tbl_id');
        $fin_year   = $this->db->get('company')->row()->COM_FIN_YEAR;

        $page    = max(1, (int)$this->input->get('page'));
        $limit   = 20;
        $offset  = ($page - 1) * $limit;
        $total   = $this->Payment_model->count_student_transactions($student_id);
        $txns    = $this->Payment_model->get_student_transactions($student_id, $limit, $offset);
        $stats   = $this->Payment_model->get_student_payment_stats($student_id);
        $offline = $this->Payment_model->get_student_offline_payments($student_id, $fin_year);

        $this->load->view('payment/payment_history_v', [
            'tab_title'       => 'My Payment History',
            'menu_name'       => 'Payment History',
            'section_heading' => 'My Payment History',
            'txns'            => $txns,
            'total'           => $total,
            'page'            => $page,
            'limit'           => $limit,
            'stats'           => $stats,
            'offline_txns'    => $offline,
        ]);
    }

    // Download a single payment receipt as PDF.
    // Students: allowed only for their own transactions.
    // Admin / operator (user_type 1, 6): allowed for any transaction.
    public function payment_receipt_pdf($txnid = null)
    {
        $user_type = $this->user_type;
        if ($user_type !== 4 && $user_type !== 1 && $user_type !== 6) {
            $this->session->set_flashdata('msg', 'Access denied.');
            redirect('admin/dashboard');
        }

        $txnid = $txnid ? urldecode($txnid) : null;
        if (!$txnid) { show_404(); }

        $txn = $this->Payment_model->get_by_txnid($txnid);
        if (!$txn) { show_404(); }

        // Students can only download their own receipts
        if ($user_type === 4) {
            $student_id = (int)$this->session->userdata('tbl_id');
            if ((int)$txn['student_id'] !== $student_id) {
                show_404();
            }
        }

        $this->load->library('tcpdf/Pdf');
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('St. Anthony School');
        $pdf->SetAuthor('St. Anthony School');
        $pdf->SetTitle('Payment Receipt - ' . $txnid);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(20, 20, 20);
        $pdf->AddPage();

        $html = $this->_receipt_html($txn);
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Receipt_' . $txnid . '.pdf', 'D');
    }

    // =========================================================================
    // ADMIN — Online Payment Transactions Report
    // =========================================================================
    public function online_payment_report()
    {
        if ($this->user_type !== 1 && $this->user_type !== 6) {
            $this->session->set_flashdata('msg', 'Access denied.');
            redirect('admin/dashboard');
        }

        $filters = [
            'status'    => $this->input->get('status', true),
            'plan_type' => $this->input->get('plan_type', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to'   => $this->input->get('date_to', true),
            'search'    => $this->input->get('search', true),
        ];
        $filters = array_filter($filters); // remove empty

        $page   = max(1, (int)$this->input->get('page'));
        $limit  = 25;
        $offset = ($page - 1) * $limit;
        $total  = $this->Payment_model->count_all_transactions($filters);
        $txns   = $this->Payment_model->get_all_transactions($filters, $limit, $offset);

        // Summary totals
        $success_txns = $this->Payment_model->get_all_transactions(array_merge($filters, ['status' => 'success']), 0, 0);
        $total_collected = array_sum(array_column($success_txns, 'amount'));

        $this->load->view('payment/admin_payment_history_v', [
            'tab_title'        => 'Online Payment Report',
            'menu_name'        => 'Transactions',
            'section_heading'  => 'Online Payment Transactions',
            'txns'             => $txns,
            'total'            => $total,
            'page'             => $page,
            'limit'            => $limit,
            'filters'          => $filters,
            'total_collected'  => $total_collected,
            'success_count'    => count($success_txns),
        ]);
    }

    // Admin — bulk PDF report
    public function online_payment_report_pdf()
    {
        if ($this->user_type !== 1 && $this->user_type !== 6) {
            show_404();
        }

        $filters = [
            'status'    => $this->input->get('status', true),
            'plan_type' => $this->input->get('plan_type', true),
            'date_from' => $this->input->get('date_from', true),
            'date_to'   => $this->input->get('date_to', true),
            'search'    => $this->input->get('search', true),
        ];
        $filters = array_filter($filters);

        $txns = $this->Payment_model->get_all_transactions($filters, 0, 0);
        $total_collected = array_sum(array_column(
            array_filter($txns, function($t){ return $t['status'] === 'success'; }),
            'amount'
        ));

        $this->load->library('tcpdf/Pdf');
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('St. Anthony School');
        $pdf->SetAuthor('St. Anthony School');
        $pdf->SetTitle('Online Payment Report');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        $html = $this->_admin_report_html($txns, $filters, $total_collected);
        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'Online_Payment_Report_' . date('Ymd_His') . '.pdf';
        $pdf->Output($filename, 'D');
    }

    // =========================================================================
    // PDF HELPERS
    // =========================================================================
    private function _receipt_html($txn)
    {
        $method_map = ['card' => 'Credit / Debit Card', 'upi' => 'UPI', 'netbanking' => 'Net Banking'];
        $method = isset($method_map[$txn['pay_method']]) ? $method_map[$txn['pay_method']] : ucfirst($txn['pay_method']);
        if (!empty($txn['gateway_mode'])) { $method .= ' (' . htmlspecialchars($txn['gateway_mode']) . ')'; }
        $status_color = $txn['status'] === 'success' ? '#27ae60' : ($txn['status'] === 'pending' || $txn['status'] === 'initiated' ? '#e67e22' : '#e74c3c');
        $date = !empty($txn['completed_at']) ? date('d M Y, h:i A', strtotime($txn['completed_at'])) : date('d M Y, h:i A', strtotime($txn['created_at']));

        $h  = '<table width="100%" cellpadding="0" cellspacing="0" style="font-family:helvetica;">';
        $h .= '<tr><td align="center" style="padding-bottom:6px;font-size:20px;font-weight:bold;color:#2c3e50;">' . htmlspecialchars(WEBSITE_NAME) . '</td></tr>';
        $h .= '<tr><td align="center" style="font-size:12px;color:#7f8c8d;padding-bottom:16px;">Online Payment Receipt</td></tr>';
        $h .= '</table>';
        $h .= '<hr style="border:1px solid #bdc3c7;"/>';

        $row = function($label, $value) {
            return '<tr><td style="padding:6px 4px;font-size:11px;color:#7f8c8d;width:45%;">' . $label . '</td>'
                 . '<td style="padding:6px 4px;font-size:11px;color:#2c3e50;font-weight:bold;">' . $value . '</td></tr>';
        };

        $h .= '<table width="100%" cellpadding="0" cellspacing="0" style="font-family:helvetica;">';
        $h .= $row('Transaction ID', htmlspecialchars($txn['txnid']));
        if (!empty($txn['easepayid'])) { $h .= $row('Gateway Ref', htmlspecialchars($txn['easepayid'])); }
        $h .= $row('Student', htmlspecialchars($txn['student_name']) . ' (' . htmlspecialchars($txn['student_reg_no']) . ')');
        $h .= $row('Plan', htmlspecialchars($txn['plan_label']));
        $h .= $row('Payment Method', $method);
        if (!empty($txn['bank_ref_num'])) { $h .= $row('Bank Reference', htmlspecialchars($txn['bank_ref_num'])); }
        $h .= $row('Date & Time', $date);
        $h .= $row('Status', '<span style="color:' . $status_color . ';">' . strtoupper($txn['status']) . '</span>');
        $h .= '</table>';
        $h .= '<hr style="border:1px solid #bdc3c7;"/>';
        $h .= '<table width="100%" cellpadding="0" cellspacing="0" style="font-family:helvetica;">';
        $h .= '<tr><td style="padding:8px 4px;font-size:14px;font-weight:bold;color:#2c3e50;">Amount Paid</td>';
        $h .= '<td style="padding:8px 4px;font-size:16px;font-weight:bold;color:#27ae60;text-align:right;">&#8377;' . number_format((float)$txn['amount'], 2) . '</td></tr>';
        $h .= '</table>';
        $h .= '<p style="font-size:9px;color:#95a5a6;text-align:center;margin-top:20px;">This is a computer-generated receipt. ' . htmlspecialchars(WEBSITE_NAME) . '</p>';
        return $h;
    }

    private function _admin_report_html($txns, $filters, $total_collected)
    {
        $method_map = ['card' => 'Card', 'upi' => 'UPI', 'netbanking' => 'Net Banking'];
        $status_colors = ['success' => '#27ae60', 'failed' => '#e74c3c', 'cancelled' => '#e67e22', 'pending' => '#f39c12', 'initiated' => '#3498db'];

        // Build filter description
        $filter_parts = [];
        if (!empty($filters['date_from'])) { $filter_parts[] = 'From: ' . $filters['date_from']; }
        if (!empty($filters['date_to']))   { $filter_parts[] = 'To: ' . $filters['date_to']; }
        if (!empty($filters['status']))    { $filter_parts[] = 'Status: ' . ucfirst($filters['status']); }
        if (!empty($filters['plan_type'])) { $filter_parts[] = 'Plan: ' . ucfirst($filters['plan_type']); }
        if (!empty($filters['search']))    { $filter_parts[] = 'Search: ' . htmlspecialchars($filters['search']); }
        $filter_str = !empty($filter_parts) ? implode(' | ', $filter_parts) : 'All Transactions';

        $h  = '<table width="100%" cellpadding="0" cellspacing="0" style="font-family:helvetica;">';
        $h .= '<tr><td align="center" style="font-size:18px;font-weight:bold;color:#2c3e50;">' . htmlspecialchars(WEBSITE_NAME) . '</td></tr>';
        $h .= '<tr><td align="center" style="font-size:13px;color:#7f8c8d;padding-bottom:4px;">Online Payment Transactions Report</td></tr>';
        $h .= '<tr><td align="center" style="font-size:10px;color:#95a5a6;padding-bottom:4px;">' . $filter_str . '</td></tr>';
        $h .= '<tr><td align="center" style="font-size:10px;color:#95a5a6;">Generated: ' . date('d M Y, h:i A') . '</td></tr>';
        $h .= '</table>';
        $h .= '<br/>';

        $th = 'style="background-color:#2c3e50;color:#fff;font-size:10px;padding:6px 4px;text-align:left;"';
        $h .= '<table width="100%" cellpadding="2" cellspacing="0" border="1" style="font-family:helvetica;font-size:10px;border-collapse:collapse;border-color:#dee2e6;">';
        $h .= '<tr>';
        $h .= '<th ' . $th . '>#</th>';
        $h .= '<th ' . $th . '>Date</th>';
        $h .= '<th ' . $th . '>Transaction ID</th>';
        $h .= '<th ' . $th . '>Student</th>';
        $h .= '<th ' . $th . '>Reg No</th>';
        $h .= '<th ' . $th . '>Plan</th>';
        $h .= '<th ' . $th . '>Method</th>';
        $h .= '<th ' . $th . '>Amount (&#8377;)</th>';
        $h .= '<th ' . $th . '>Status</th>';
        $h .= '<th ' . $th . '>Gateway Ref</th>';
        $h .= '</tr>';

        $i = 1;
        foreach ($txns as $txn) {
            $bg = ($i % 2 === 0) ? '#f8f9fa' : '#ffffff';
            $td = 'style="padding:5px 4px;font-size:9px;background-color:' . $bg . ';"';
            $sc = isset($status_colors[$txn['status']]) ? $status_colors[$txn['status']] : '#333';
            $date = !empty($txn['completed_at']) ? date('d/m/y H:i', strtotime($txn['completed_at'])) : date('d/m/y H:i', strtotime($txn['created_at']));
            $m = isset($method_map[$txn['pay_method']]) ? $method_map[$txn['pay_method']] : ucfirst((string)$txn['pay_method']);
            $h .= '<tr>';
            $h .= '<td ' . $td . '>' . $i . '</td>';
            $h .= '<td ' . $td . '>' . $date . '</td>';
            $h .= '<td ' . $td . '>' . htmlspecialchars($txn['txnid']) . '</td>';
            $h .= '<td ' . $td . '>' . htmlspecialchars($txn['student_name']) . '</td>';
            $h .= '<td ' . $td . '>' . htmlspecialchars($txn['student_reg_no']) . '</td>';
            $h .= '<td ' . $td . '>' . ucfirst($txn['plan_type']) . '</td>';
            $h .= '<td ' . $td . '>' . $m . '</td>';
            $h .= '<td style="padding:5px 4px;font-size:9px;background-color:' . $bg . ';text-align:right;">' . number_format((float)$txn['amount'], 2) . '</td>';
            $h .= '<td ' . $td . '><span style="color:' . $sc . ';font-weight:bold;">' . strtoupper($txn['status']) . '</span></td>';
            $h .= '<td ' . $td . '>' . htmlspecialchars((string)$txn['easepayid']) . '</td>';
            $h .= '</tr>';
            $i++;
        }

        $h .= '<tr>';
        $h .= '<td colspan="7" style="padding:6px 4px;font-size:10px;font-weight:bold;text-align:right;background-color:#eaf6ee;">Total Collected (Success)</td>';
        $h .= '<td style="padding:6px 4px;font-size:11px;font-weight:bold;text-align:right;background-color:#eaf6ee;color:#27ae60;">&#8377;' . number_format($total_collected, 2) . '</td>';
        $h .= '<td colspan="2" style="padding:6px 4px;background-color:#eaf6ee;"></td>';
        $h .= '</tr>';
        $h .= '</table>';
        $h .= '<p style="font-size:8px;color:#95a5a6;text-align:center;">Total ' . count($txns) . ' record(s) | ' . htmlspecialchars(WEBSITE_NAME) . '</p>';
        return $h;
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    private function _require_student()
    {
        if ($this->user_type !== 4) {
            $this->session->set_flashdata('title', 'Log-in!');
            $this->session->set_flashdata('msg', 'Kindly log-in to access that page.');
            redirect(base_url('admin'));
            exit;
        }
    }

    private function _get_logged_in_student()
    {
        $tbl_id = (int)$this->session->userdata('tbl_id');
        $row    = $this->db->get_where('student_details',        ['STD_SEQ' => $tbl_id])->row_array();
        $parent = $this->db->get_where('student_parent_details', ['STD_SEQ' => $tbl_id])->row_array();

        $sess_email = $this->session->userdata('email');
        $email = !empty($row['STD_EMAIL']) ? $row['STD_EMAIL']
               : ((!empty($sess_email) && $sess_email !== 'N/A') ? $sess_email
               : (string)$this->config->item('easebuzz_fallback_email'));

        $raw_phone = !empty($row['STD_PH_NO']) ? $row['STD_PH_NO']
                   : (!empty($parent['STD_FTH_MOB']) ? $parent['STD_FTH_MOB'] : '');
        $phone = $this->_clean_phone($raw_phone);

        return [
            'id'     => $tbl_id,
            'reg_no' => !empty($row['STD_REGNO']) ? $row['STD_REGNO'] : $this->session->userdata('username'),
            'name'   => $this->session->userdata('name'),
            'email'  => $email,
            'phone'  => $phone,
        ];
    }

    private function _build_allocations($txn_row)
    {
        if ($txn_row['plan_type'] === 'monthly') {
            // Multi-month: read selected months from payment_meta stored in the DB row
            $meta   = !empty($txn_row['payment_meta']) ? json_decode($txn_row['payment_meta'], true) : [];
            $months = !empty($meta['selected_months']) ? $meta['selected_months'] : null;

            if (!empty($months) && is_array($months)) {
                $count  = count($months);
                $total  = (float)$txn_row['amount'];
                $base   = round($total / $count, 2);
                $last   = round($total - ($base * ($count - 1)), 2); // absorb rounding remainder
                $allocs = [];
                foreach ($months as $i => $m) {
                    $allocs[] = [
                        'fee_head'     => 'Tuition + Activity',
                        'period_label' => $m['label'],
                        'period_month' => (int)$m['month'],
                        'period_year'  => (int)$m['year'],
                        'amount'       => ($i === $count - 1) ? $last : $base,
                    ];
                }
                return $allocs;
            }

            // Legacy single-month fallback (old transactions without payment_meta)
            return [[
                'fee_head'     => 'Tuition + Activity',
                'period_label' => $txn_row['plan_label'],
                'period_month' => (int)date('m'),
                'period_year'  => (int)date('Y'),
                'amount'       => $txn_row['amount'],
            ]];
        }

        // Yearly (unchanged)
        $total  = (float)$txn_row['amount'];
        $year   = (int)date('Y');
        $base   = floor(($total / 12) * 100) / 100;
        $last   = round($total - ($base * 11), 2);
        $allocs = [];
        for ($m = 1; $m <= 12; $m++) {
            $allocs[] = [
                'fee_head'     => 'Tuition + Activity (Yearly)',
                'period_label' => date('F', mktime(0, 0, 0, $m, 1, $year)) . ' ' . $year,
                'period_month' => $m,
                'period_year'  => $year,
                'amount'       => ($m === 12) ? $last : $base,
            ];
        }
        return $allocs;
    }

    private function _ascii_safe($str)
    {
        // Strip non-ASCII
        $str = preg_replace('/[^\x20-\x7E]/', '', $str);
        // Strip chars Easebuzz productinfo rejects (commas, parentheses, pipe, etc.)
        $str = str_replace(['|', ',', '(', ')', '&', '#', '@', '!', ';', ':'], '', $str);
        // Collapse any double-spaces left behind
        return trim(preg_replace('/\s{2,}/', ' ', $str));
    }

    private function _clean_phone($phone)
    {
        return substr(preg_replace('/\D/', '', (string)$phone), -10);
    }

    // =========================================================================
    // EASEBUZZ — Production only. No third-party SDK. Pure cURL.
    // =========================================================================
    private function _eb_txnid()
    {
        $prefix = $this->config->item('easebuzz_txnid_prefix') ?: 'SAS';
        return $prefix . date('YmdHis') . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    }

    private function _eb_hash(array $p)
    {
        $salt = $this->config->item('easebuzz_live_salt');
        $str  = '';
        foreach (['key','txnid','amount','productinfo','firstname','email','udf1','udf2','udf3','udf4','udf5'] as $k) {
            $str .= (isset($p[$k]) ? $p[$k] : '') . '|';
        }
        $str .= '|||||' . $salt;
        return strtolower(hash('sha512', $str));
    }

    private function _eb_post($url, $body)
    {
        $cainfo = APPPATH . 'config/cacert.pem';
        $ca_ok  = file_exists($cainfo);

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            $opts = [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => 1,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; StAnthonySchool/1.0)',
                CURLOPT_TIMEOUT        => 30,
            ];
            if ($ca_ok) $opts[CURLOPT_CAINFO] = $cainfo;
            curl_setopt_array($ch, $opts);
            $raw = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            if (!$err && $raw !== false && $raw !== '') return $raw;
            log_message('error', 'Easebuzz cURL: ' . $err);
        }

        $context = stream_context_create([
            'ssl'  => ['verify_peer' => true, 'verify_peer_name' => true, 'cafile' => $ca_ok ? $cainfo : null],
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\nUser-Agent: Mozilla/5.0\r\n",
                'content' => $body,
                'timeout' => 30,
            ],
        ]);
        return @file_get_contents($url, false, $context);
    }

    private function _eb_initiate(array $params)
    {
        $params['key']  = $this->config->item('easebuzz_live_key');

        // Build hash string exactly as Easebuzz expects for logging
        $salt      = $this->config->item('easebuzz_live_salt');
        $hash_str  = '';
        foreach (['key','txnid','amount','productinfo','firstname','email','udf1','udf2','udf3','udf4','udf5'] as $k) {
            $hash_str .= (isset($params[$k]) ? $params[$k] : '') . '|';
        }
        $hash_str .= '|||||' . $salt;

        $params['hash'] = strtolower(hash('sha512', $hash_str));

        // Debug log — check application/logs/log-YYYY-MM-DD.php after a failed attempt
        log_message('debug', 'EB_INITIATE_PARAMS: ' . json_encode(array_diff_key($params, ['hash' => 1])));
        log_message('debug', 'EB_HASH_STRING: ' . $hash_str);
        log_message('debug', 'EB_HASH: ' . $params['hash']);

        $raw = $this->_eb_post('https://pay.easebuzz.in/payment/initiateLink', http_build_query($params));

        log_message('debug', 'EB_RAW_RESPONSE: ' . $raw);

        if (!$raw) return ['status' => 0, 'data' => 'Could not connect to payment gateway.'];
        $result = json_decode($raw, true);
        if (!is_array($result)) {
            log_message('error', 'Easebuzz bad response: ' . $raw);
            return ['status' => 0, 'data' => 'Invalid gateway response.'];
        }
        return $result;
    }

    private function _eb_verify_hash(array $post)
    {
        foreach (['key','txnid','amount','productinfo','firstname','email','status','hash'] as $f) {
            if (!isset($post[$f])) return false;
        }
        $salt = $this->config->item('easebuzz_live_salt');
        $g    = function($k) use ($post) { return isset($post[$k]) ? $post[$k] : ''; };
        $str  = $salt . '|' . $post['status'] . '|'
              . $g('udf10').'|'.$g('udf9').'|'.$g('udf8').'|'.$g('udf7').'|'.$g('udf6').'|'
              . $g('udf5').'|'.$g('udf4').'|'.$g('udf3').'|'.$g('udf2').'|'.$g('udf1').'|'
              . $post['email'].'|'.$post['firstname'].'|'.$post['productinfo'].'|'
              . $post['amount'].'|'.$post['txnid'].'|'.$post['key'];
        return hash_equals(strtolower(hash('sha512', $str)), strtolower($post['hash']));
    }

    private function _eb_verify_api($txnid, $amount, $email, $phone)
    {
        $key  = $this->config->item('easebuzz_live_key');
        $salt = $this->config->item('easebuzz_live_salt');
        $hash = strtolower(hash('sha512', $key . '|' . $txnid . '|' . $salt));
        $raw  = $this->_eb_post('https://pay.easebuzz.in/transaction/v1/retrieve',
            http_build_query([
                'merchant_key' => $key,
                'txnid'        => $txnid,
                'amount'       => number_format((float)$amount, 2, '.', ''),
                'email'        => $email,
                'phone'        => $phone,
                'hash'         => $hash,
            ]));
        $result = json_decode($raw, true);
        return is_array($result) ? $result : null;
    }

    private function _api_says_success($api_resp)
    {
        if ($api_resp === null) return null;
        if (!is_array($api_resp)) return false;
        $status = isset($api_resp['status']) ? (int)$api_resp['status'] : 0;
        if ($status !== 1) return false;
        if (!isset($api_resp['msg'])) return null;
        $msg = $api_resp['msg'];
        if (is_string($msg)) return null;
        if (is_array($msg)) {
            $first = reset($msg);
            if (is_array($first) && isset($first['status'])) {
                return strtolower($first['status']) === 'success';
            }
            if (isset($msg['status'])) {
                return strtolower($msg['status']) === 'success';
            }
        }
        return null;
    }
}
