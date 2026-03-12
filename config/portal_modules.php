<?php

// Every module that CAN appear in the customer portal
// Super admin controls visibility by giving/removing 'view_any_*' permission to customer role

return [
    [
        'key' => 'call_history',
        'label' => 'My Call History',
        'icon' => 'heroicon-o-phone',
        'route' => 'portal.calls.index',
        'permission' => 'view_any_calls',  // must have this to see in nav
    ],
    [
        'key' => 'orders',
        'label' => 'My Orders',
        'icon' => 'heroicon-o-shopping-bag',
        'route' => 'portal.orders.index',
        'permission' => 'view_any_orders',
    ],
    [
        'key' => 'tickets',
        'label' => 'My Tickets',
        'icon' => 'heroicon-o-ticket',
        'route' => 'portal.tickets.index',
        'permission' => 'view_any_tickets',
    ],
    [
        'key' => 'faqs',
        'label' => 'FAQs',
        'icon' => 'heroicon-o-question-mark-circle',
        'route' => 'portal.faqs.index',
        'permission' => 'view_any_faqs',  // super admin toggles this ON/OFF
    ],
    // Add more portal modules here in future
];
