<?php

namespace JTD420\PGP\Commands;

use Illuminate\Console\Command;

class AddUserModelChanges extends Command
{
    protected $signature = 'add-user-model-changes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add necessary changes to the User model for PGP to work with Laravel';

    public $hidden = true;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * This method updates the User model with necessary changes.
         *
         * @return void
         */
        $modelPath = app_path('Models/User.php');

        // Check if the User model file exists
        if (! file_exists($modelPath)) {
            $this->error('User model file not found.');

            return;
        }

        // Read the contents of the User model file
        $modelContents = file_get_contents($modelPath);

        if (! str_contains($modelContents, "'username'")) {
            $fillableRegex = '/protected\s+\$fillable\s*=\s*\[[^\]]+\]/m';
            if (preg_match($fillableRegex, $modelContents, $matches)) {
                $fillableArray = $matches[0];
                $fillableArray = str_replace(']', "    'username',\n    ]", $fillableArray);
                $modelContents = preg_replace($fillableRegex, $fillableArray, $modelContents);
                file_put_contents($modelPath, $modelContents);
                $this->info("Added 'username' to User model.");
            }
        }

        if (strpos($modelContents, "use JTD420\PGP\Events\UserCreatedEvent;") === false) {
            $modelContentsNew = str_replace("use Laravel\Sanctum\HasApiTokens;\n", "use Laravel\Sanctum\HasApiTokens;\nuse JTD420\PGP\Events\UserCreatedEvent;\n", $modelContents);
            $eventsProperty = "\n    /**\n     * The event map for the model.\n     *\n     * @var array\n     */\n    protected \$events = [\n        'created' => UserCreatedEvent::class,\n    ];\n  \n";
            $modelContentsNew = str_replace("class User extends Authenticatable\n{\n", "class User extends Authenticatable\n{\n".$eventsProperty, $modelContentsNew);
            $modelContents = $modelContentsNew;
            file_put_contents($modelPath, $modelContents);
            $this->info('Changes to User model successfully added.');
        } else {
            $this->warn('The presence of the use statement for UserCreatedEvent was detected in your User Model. To avoid duplicates, no modifications were made to it. If this is unexpected, try removing the statement and re-running the install command. If the issue persists, please open a Github issue for assistance.');
        }
    }
}
