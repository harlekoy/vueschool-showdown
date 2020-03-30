<?php

namespace App\Jobs;

use App\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class BulkUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    public $ids;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = User::whereIn('id', $this->ids)->get();

        $response = Http::post($this->apiUrl(), $this->formParams($users));

        $response->throw();
    }

    /**
     * Get the third-party API URL.
     *
     * @return string
     */
    public function apiUrl()
    {
        return env('THIRD_PARTY_API_URL', route('mock.bulk'));
    }

    /**
     * Get the list of updated users in the past.
     *
     * @param \Illuminate\Database\Eloquent\Collection $users
     * @return array
     */
    public function formParams($users)
    {
        $batches = [[
            'subscribers' => $users->map(function ($user) {
                return array_filter([
                    'email'     => $user->email,
                    'name'      => $user->name,
                    'time_zone' => $user->timezone,
                ]);
            })->all()
        ]];

        return compact('batches');
    }
}
