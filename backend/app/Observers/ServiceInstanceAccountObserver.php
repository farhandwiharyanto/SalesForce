<?php

namespace App\Observers;

use App\Models\ServiceInstanceAccount;
use App\Models\Customer;

class ServiceInstanceAccountObserver
{
    /**
     * Handle the ServiceInstanceAccount "saved" event.
     */
    public function saved(ServiceInstanceAccount $sia): void
    {
        $this->syncCustomerStatus($sia->customer_id);
    }

    /**
     * Handle the ServiceInstanceAccount "deleted" event.
     */
    public function deleted(ServiceInstanceAccount $sia): void
    {
        $this->syncCustomerStatus($sia->customer_id);
    }

    private function syncCustomerStatus($customerId)
    {
        if (!$customerId) return;
        
        $customer = Customer::find((int)$customerId);
        if ($customer) {
            $customer->syncStatus();
        }
    }
}
