<?php

return [
    [
        'key' => 'call_history',
        'label' => 'My Call History',
        'icon' => 'heroicon-o-phone',
        'route' => 'portal.calls.index',
        'permission' => 'view_any_calls',
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
        'permission' => 'view_any_faqs',
    ],
];
