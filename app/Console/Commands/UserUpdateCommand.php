<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class UserUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:update {user} {--fields=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updated user record';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($user = $this->user()) {
            $data = $this->fields();
            $user->fill($data);

            if ($user->isDirty()) {
                $user->update($data);
                $this->info("User {$user->id} updated");
            } else {
                $this->comment("Nothing to update");
            }
        }
    }

    /**
     * Get user.
     *
     * @return \App\User
     */
    public function user()
    {
        return User::findOrFail($this->argument('user'));
    }

    /**
     * Get fields.
     *
     * @return array
     */
    public function fields()
    {
        return collect(explode(',', $this->option('fields')))
            ->filter()
            ->mapWithKeys(function ($value) {
                $pair = explode(':', $value);

                return [$pair[0] => $pair[1] ?? null];
            })
            ->all();
    }
}
