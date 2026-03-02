<?php

namespace App\Providers;

use App\Models\CallLog;
use App\Models\Customer;
use App\Models\Faq;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\CallLogPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\FaqPolicy;
use App\Policies\OrderPolicy;
use App\Policies\TicketPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(CallLog::class, CallLogPolicy::class);
        Gate::policy(Faq::class, FaqPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
