<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkRequest;
use App\Http\Requests\IndividualRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MockApiController extends Controller
{
    /**
     * Mocked third-party individual update API endpoint.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function individual(IndividualRequest $request, User $user)
    {
        $this->log($user);
    }

    /**
     * Mocked third-party bulk update API endpoint.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulk(BulkRequest $request)
    {
        $this->subscribers($request)
            ->each(function ($user) {
                $this->log($user);
            });

        return response()->noContent();
    }

    /**
     * Get the list of subscribers.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     */
    public function subscribers($request)
    {
        $emails = collect($request->get('batches'))
            ->flatten(2)
            ->pluck('email')
            ->all();

        return User::whereIn('email', $emails)->get();
    }

    /**
     * Log user.
     *
     * @param  \App\User $user
     * @return void
     */
    public function log($user)
    {
        Log::info("[{$user->id}]".trim($this->output($user), ','));
    }

    /**
     * Stringify user's attribute.
     *
     * @param  \App\User $user
     * @return \Illuminate\Support\Collection
     */
    public function output($user)
    {
        return collect(['name', 'email', 'timezone'])
            ->reduce(function ($output, $attribute) use ($user) {
                if ($user->$attribute) {
                    return $output.', '.$attribute.':'. $user->$attribute;
                }

                return $output;
            }, '');
    }
}
