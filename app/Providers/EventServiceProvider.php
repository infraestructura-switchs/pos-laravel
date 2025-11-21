<?php

namespace App\Providers;

use App\Models\Bill;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use App\Observers\BillObserver;
use App\Observers\ProductObserver;
use App\Observers\CustomerObserver;
use App\Observers\CategoryObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function boot() {
        Product::observe(ProductObserver::class);
        Bill::observe(BillObserver::class);
        Customer::observe(CustomerObserver::class);
        Category::observe(CategoryObserver::class);
    }

    public function shouldDiscoverEvents() {
        return false;
    }
}
