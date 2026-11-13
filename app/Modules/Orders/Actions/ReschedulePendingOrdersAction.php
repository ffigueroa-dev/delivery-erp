<?php

namespace App\Modules\Orders\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class ReschedulePendingOrdersAction
{
    /**
     * Reschedule expired pending orders to the target date.
     */
    public function rescheduleExpired(CarbonImmutable $targetDate): int
    {
        $now = CarbonImmutable::now(config('app.timezone'));

        return $this->updateOrders(
            $targetDate,
            fn ($query) => $query
                ->whereNotNull('scheduled_until')
                ->where('scheduled_until', '<=', $now->utc())
        );
    }

    /**
     * Force rescheduling of all pending orders to the target date.
     */
    public function rescheduleAll(CarbonImmutable $targetDate): int
    {
        return $this->updateOrders($targetDate);
    }

    /**
     * Update pending orders with the target day's schedule.
     */
    private function updateOrders(
        CarbonImmutable $targetDate,
        ?callable $additionalFilter = null
    ): int {
        $timezone = config('app.timezone');

        $startOfDay = $targetDate
            ->setTimezone($timezone)
            ->startOfDay()
            ->utc();

        $endOfDay = $targetDate
            ->setTimezone($timezone)
            ->endOfDay()
            ->utc();

        $query = DB::table('orders')
            ->where('status', 'pending');

        if ($additionalFilter !== null) {
            $additionalFilter($query);
        }

        return $query->update([
            'scheduled_at' => $startOfDay,
            'scheduled_until' => $endOfDay,
            'updated_at' => now(),
        ]);
    }
}
