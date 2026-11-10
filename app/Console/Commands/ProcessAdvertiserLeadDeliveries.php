<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdvertiserLeadDelivery;
use App\Services\AdvertiserLeadDeliveryService;
use Throwable;

class ProcessAdvertiserLeadDeliveries extends Command
{
    protected $signature = 'advertiser:process-deliveries';

    protected $description = 'Process pending advertiser lead deliveries and retries';

    public function handle(AdvertiserLeadDeliveryService $service)
    {
        $this->info('Starting advertiser lead delivery processing...');

        $processed = 0;

        AdvertiserLeadDelivery::where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('next_attempt_at')
                    ->orWhere('next_attempt_at', '<=', now());
            })
            ->orderBy('id')
            ->chunkById(100, function ($deliveries) use ($service, &$processed) {

                foreach ($deliveries as $delivery) {
                    try {
                        $service->sendDelivery($delivery);
                        $processed++;
                    } catch (Throwable $e) {
                        report($e);

                        $this->error(
                            'Delivery ID ' . $delivery->id . ': ' . $e->getMessage()
                        );
                    }
                }
            });

        $this->info('Processed deliveries: ' . $processed);

        return 0;
    }
}