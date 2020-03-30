<?php

namespace App\Console\Commands;

use App\Jobs\BulkUpdate;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ThirdPartyBulkUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk update user attributes in the third-party provider.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $updated = $this->lastBulkUpdate();
        $this->setNowBulkUpdate();

        User::when($updated, function ($query) use ($updated) {
                $query->where('updated_at', '>', $updated);
            })
            ->tap(function ($query) {
                if ($count = $query->count()) {
                    $this->info('Total updated users '.$count);
                } else {
                    $this->warn('Nothing to update');
                }
            })
            ->chunk(1000, function ($users) {
                $ids = $users->pluck('id')->all();

                dispatch(new BulkUpdate($ids));
            });

    }

    /**
     * Get the last bulk updated at.
     *
     * @return \Illuminate\Support\Carbon
     */
    public function lastBulkUpdate()
    {
        return Cache::get('third-party:last_bulk_updated_at');
    }

    /**
     * Set the bulk updated at timestamp to now.
     *
     * @return boolean
     */
    public function setNowBulkUpdate()
    {
        return Cache::forever('third-party:last_bulk_updated_at', now());
    }
}
