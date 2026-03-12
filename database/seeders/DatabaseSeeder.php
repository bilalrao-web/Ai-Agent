<?php

namespace Database\Seeders;

use App\Models\CallLog;
use App\Models\ConversationMessage;
use App\Models\Customer;
use App\Models\Faq;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);

        $this->call(RoleAndPermissionSeeder::class);

        for ($i = 1; $i <= 3; $i++) {
            $user = User::firstOrCreate(
                ['email' => "admin{$i}@example.com"],
                [
                    'name' => "Admin User {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                ]
            );
            if (! $user->hasRole('admin')) {
                $user->assignRole('admin');
            }
        }

        $orderStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $ticketStatuses = ['open', 'in_progress', 'resolved', 'closed'];
        $issueTypes = ['Delivery delay', 'Damaged product', 'Wrong item', 'Refund request', 'General inquiry'];

        for ($i = 1; $i <= 5; $i++) {
            $user = User::firstOrCreate(
                ['email' => "customer{$i}@example.com"],
                [
                    'name' => "Customer {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                ]
            );
            if (! $user->hasRole('customer')) {
                $user->assignRole('customer');
            }

            $customer = Customer::firstOrCreate(
                ['email' => "customer{$i}@example.com"],
                [
                    'user_id' => $user->id,
                    'name' => "Customer {$i}",
                    'phone' => '+1555000' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                ]
            );
            if ($customer->user_id !== $user->id) {
                $customer->update(['user_id' => $user->id]);
            }

            $numOrders = rand(2, 3);
            for ($o = 1; $o <= $numOrders; $o++) {
                $orderNumber = 'ORD-C' . $customer->id . '-' . $o;
                Order::firstOrCreate(
                    [
                        'order_number' => $orderNumber,
                    ],
                    [
                        'customer_id' => $customer->id,
                        'status' => $orderStatuses[array_rand($orderStatuses)],
                        'delivery_date' => now()->addDays(rand(1, 14)),
                        'amount' => rand(500, 5000) / 10,
                    ]
                );
            }

            for ($t = 0; $t < rand(1, 2); $t++) {
                Ticket::firstOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'issue_type' => $issueTypes[array_rand($issueTypes)],
                        'description' => 'Sample issue description for ticket ' . ($t + 1),
                    ],
                    ['status' => $ticketStatuses[array_rand($ticketStatuses)]]
                );
            }
        }

        $faqs = [
            ['question' => 'What are your working hours?', 'answer' => 'We are available 24/7 for customer support.'],
            ['question' => 'How can I track my order?', 'answer' => 'Use the order number on our website or portal to track delivery status.'],
            ['question' => 'What is your return policy?', 'answer' => 'Returns are accepted within 30 days with original receipt.'],
            ['question' => 'How do I create a support ticket?', 'answer' => 'Log in to the customer portal and go to My Tickets to create a new ticket.'],
            ['question' => 'What are your customer support working hours?', 'answer' => 'Our AI voice agent and support team are available 24/7.'],
        ];
        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                ['answer' => $faq['answer'], 'is_active' => true]
            );
        }

        $customers = Customer::with('user')->has('user')->limit(2)->get();
        $queries = ['What is the status of my latest order?', 'I have an issue with my delivered product.', 'What are your working hours?'];
        foreach ($customers as $customer) {
            foreach (array_slice($queries, 0, rand(1, 2)) as $q) {
                $log = CallLog::firstOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'simulated_query' => $q,
                    ],
                    ['duration' => rand(30, 180), 'status' => 'completed']
                );
                if ($log->conversationMessages()->count() === 0) {
                    ConversationMessage::create(['call_log_id' => $log->id, 'role' => 'user', 'content' => $q]);
                    ConversationMessage::create(['call_log_id' => $log->id, 'role' => 'assistant', 'content' => 'Thank you for your query. This is a simulated response.']);
                }
            }
        }
    }
}
