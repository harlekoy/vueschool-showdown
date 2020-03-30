<?php

namespace Tests\Feature;

use App\Jobs\BulkUpdate;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use TiMacDonald\Log\LogFake;

class ThirdpartyCommandTaskSchedulerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_run_the_bulk_update_command()
    {
        factory(User::class, 20)->create();

        Bus::fake();

        Artisan::call('bulk:update');

        Bus::assertDispatched(BulkUpdate::class);
    }

    /** @test */
    public function it_should_not_update_again_when_bulk_update_command_was_run_twice()
    {
        factory(User::class, 20)->create();

        Bus::fake();

        Artisan::call('bulk:update');
        Artisan::call('bulk:update');

        Bus::assertDispatched(BulkUpdate::class);
    }

    /** @test */
    public function it_should_chunk_when_user_bulk_update_is_greater_than_1000()
    {
        factory(User::class, 1100)->create();

        Bus::fake();

        Artisan::call('bulk:update');

        Bus::assertDispatched(BulkUpdate::class, 2);
    }
}
