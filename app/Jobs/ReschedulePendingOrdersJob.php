<?php

namespace App\Jobs;

use App\Modules\Orders\Actions\ReschedulePendingOrdersAction;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ReschedulePendingOrdersJob implements ShouldQueue
{
    use Queueable;

    public function handle(ReschedulePendingOrdersAction $action): void
    {
        $timezone = config('app.timezone');

        $targetDate = CarbonImmutable::tomorrow($timezone);

        $affected = $action->rescheduleExpired($targetDate);

        Log::info('Pending orders rescheduled.', [
            'target_date' => $targetDate->toDateString(),
            'affected_orders' => $affected,
        ]);
    }
}
