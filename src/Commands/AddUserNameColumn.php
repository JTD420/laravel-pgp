<?php

namespace JTD420\PGP\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class AddUserNameColumn extends Command
{
    protected $signature = 'add-username-column';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will add the username column to the users table if it does not exist';

    public $hidden = true;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! Schema::hasColumn('users', 'username')) {
            file_put_contents(base_path('database/migrations/add_username_to_users_table.php'), file_get_contents(__DIR__.'/../../stubs/default/database/migrations/add_username_to_users_table.php.stub'));
//            $this->call('php artisan migrate --path=database/migrations/add_username_to_users_table.php');
        }
    }
}
