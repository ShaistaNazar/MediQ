<?php

return [
    'user' => [
        'user_not_active' => 0,
        'user_Active' => 1,
        'email_verify'=>1,
    ],
    'roles' => [
        'admin' => 1,
        'employee' => 2,
        'user'=>3,
    ],
    'orders' => [
        'pending_status' => 1,
        'complete_status' => 2,
        'cancelled_status'=>3,
        'in_process'=>4,
    ],
    'notifications' => [
        'read' => 1,
        'unread' => 2,
        'seen'=>3,
    ],
    'response' => [
        'ResponseCode_fail' => 201,
        'ResponseCode_success' => 200,
        'ResponseCode_non_authoritative_information' => 203,
        'ResponseCode_no_content' => 204,
        'ResponseCode_created' => 200,
        'ResponseCode_not_authenticated' => 401,
        'ResponseCode_bad_request' => 400,
        'ResponseCode_not_found' => 404,
        'ResponseCode_precondition_required'=>428,
    ],
    'payment_method' => [
        'mobile_account' => 1,
        'voucher_payment' => 2,
        'credit_debit_card'=>3,
    ],
];