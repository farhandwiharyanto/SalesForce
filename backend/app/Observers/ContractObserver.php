<?php

namespace App\Observers;

use App\Models\Contract;
use App\Models\Customer;

class ContractObserver
{
    /**
     * Handle the Contract "saved" event.
     */
    public function saved(Contract $contract): void
    {
        $this->syncCustomerStatus($contract->customer_id);
    }

    /**
     * Handle the Contract "deleted" event.
     */
    public function deleted(Contract $contract): void
    {
        $this->syncCustomerStatus($contract->customer_id);
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
