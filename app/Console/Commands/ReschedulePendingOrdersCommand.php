<?php

namespace App\Console\Commands;

use App\Modules\Orders\Actions\ReschedulePendingOrdersAction;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use InvalidArgumentException;

class ReschedulePendingOrdersCommand extends Command
{
    protected $signature = 'orders:reschedule-pending
                            {date? : Target date (today or YYYY-MM-DD)}';

    protected $description = 'Reschedule pending orders';

    public function handle(ReschedulePendingOrdersAction $action): int
    {
        try {
            $timezone = config('app.timezone');
            $date = $this->argument('date');

            // No argument: same behavior as the scheduled Job.
            if ($date === null) {
                $targetDate = CarbonImmutable::tomorrow($timezone);

                $affected = $action->rescheduleExpired($targetDate);

                $this->info(
                    "{$affected} expired pending orders rescheduled for tomorrow."
                );

                return self::SUCCESS;
            }

            // Explicit date: force rescheduling of all pending orders.
            $targetDate = $date === 'today'
                ? CarbonImmutable::today($timezone)
                : $this->parseDate($date, $timezone);

            $affected = $action->rescheduleAll($targetDate);

            $this->info(
                "{$affected} pending orders rescheduled for {$targetDate->toDateString()}."
            );

            return self::SUCCESS;
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } catch (\Throwable $exception) {
            report($exception);

            $this->error('Could not reschedule pending orders.');

            return self::FAILURE;
        }
    }

    private function parseDate(
        string $date,
        string $timezone
    ): CarbonImmutable {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new InvalidArgumentException(
                'Invalid date. Use today or YYYY-MM-DD.'
            );
        }

        $parsedDate = CarbonImmutable::createFromFormat(
            '!Y-m-d',
            $date,
            $timezone
        );

        if (! $parsedDate || $parsedDate->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException(
                'Invalid date. Use today or YYYY-MM-DD.'
            );
        }

        return $parsedDate;
    }
}
