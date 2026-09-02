<?php

const EVENT_STATUSES = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
const PAYMENT_METHODS = ['Bank Transfer', 'Credit Card'];
const PAYMENT_STATUSES = ['Unpaid', 'Pending Approval', 'Paid'];

function statusClass(string $status): string
{
    return match ($status) {
        'Confirmed', 'Pending Approval' => 'status-confirmed',
        'Completed', 'Paid' => 'status-success',
        'Cancelled', 'Unpaid' => 'status-danger',
        default => 'status-pending',
    };
}
