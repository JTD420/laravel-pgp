<?php

namespace JTD420\PGP\Console;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

trait InstallsPGPStack
{
    /**
     * Install the Default Blade PGP stack.
     *
     * @return void
     */
    protected function installPGPStack()
    {
        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                '@tailwindcss/forms' => '^0.5.2',
                'autoprefixer' => '^10.4.2',
                'postcss' => '^8.4.6',
                'tailwindcss' => '^3.1.0',
            ] + $packages;
        });
        // Controllers...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Http/Controllers', app_path('Http/Controllers'));

        // Models...
        (new Filesystem)->ensureDirectoryExists(app_path('Models'));
        (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Models/PGP', app_path('Models/PGP'));

        // Requests...
        //(new Filesystem)->ensureDirectoryExists(app_path('Http/Requests'));
        //(new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/Http/Requests', app_path('Http/Requests'));

        // Views...
        //(new Filesystem)->ensureDirectoryExists(resource_path('views'));
        //(new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/resources/views', resource_path('views'));

        if (!$this->option('dark')) {
            $this->removeDarkClasses((new Finder)
                ->in(resource_path('views'))
                ->name('*.blade.php')
                ->notName('welcome.blade.php')
            );
        }

        /**
         * Adds the necessary classmap to applications composer.json for PGP to work with Laravel.
         *
         * @return void
         */

        $composerJsonPath = base_path('composer.json');
        $composerJsonFile = json_decode(file_get_contents($composerJsonPath), true);
        $composerJsonFile['autoload']['classmap'] = ['vendor/singpolyma/openpgp-php/lib/'];
        file_put_contents($composerJsonPath, json_encode($composerJsonFile, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Components...
        //(new Filesystem)->ensureDirectoryExists(app_path('View/Components'));
        //(new Filesystem)->copyDirectory(__DIR__.'/../../stubs/default/app/View/Components', app_path('View/Components'));

        // Tests...
        //$this->installTests();

        // Append Include of PGP Routes to Existing routes/web.php File...
        $existingRoutes = file_get_contents(base_path('routes/web.php')); // Read the contents of the existing routes/web.php file into a string
        if (! str_contains($existingRoutes, "require __DIR__.'/pgp.php'")) {
            $stubRoutes = file_get_contents(__DIR__.'/../../stubs/default/routes/web.php'); // Read the contents of your stub file into a string
            $combinedRoutes = $existingRoutes.PHP_EOL.$stubRoutes; // Concatenate the contents of the two files
            file_put_contents(base_path('routes/web.php'), $combinedRoutes); // Write the combined contents back to the routes/web.php file
        }

        // Routes...
        if (! file_exists(base_path('routes/pgp.php'))) {
            copy(__DIR__.'/../../stubs/default/routes/pgp.php', base_path('routes/pgp.php'));
        } else {
            if ($this->confirm('Overwrite routes/PGP directory?')) {
                copy(__DIR__.'/../../stubs/default/routes/pgp.php', base_path('routes/pgp.php'));
            }
        }

        //copy(__DIR__.'/../../stubs/default/routes/auth.php', base_path('routes/auth.php'));

        // "Dashboard" Route...
        //$this->replaceInFile('/home', '/dashboard', resource_path('views/welcome.blade.php'));
        //$this->replaceInFile('Home', 'Dashboard', resource_path('views/welcome.blade.php'));
        //$this->replaceInFile('/home', '/dashboard', app_path('Providers/RouteServiceProvider.php'));

        // Tailwind / Vite...
        copy(__DIR__.'/../../stubs/default/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__.'/../../stubs/default/postcss.config.js', base_path('postcss.config.js'));
        copy(__DIR__.'/../../stubs/default/vite.config.js', base_path('vite.config.js'));
        copy(__DIR__.'/../../stubs/default/resources/css/app.css', resource_path('css/app.css'));
//        copy(__DIR__.'/../../stubs/default/resources/js/app.js', resource_path('js/app.js'));

        $this->components->info('Installing dependencies.');

        if (file_exists(base_path('pnpm-lock.yaml'))) {
            $this->runCommands(['pnpm install', 'pnpm run build']);
        } elseif (file_exists(base_path('yarn.lock'))) {
            $this->runCommands(['yarn install', 'yarn run build']);
        } else {
            $this->runCommands(['npm install', 'npm run build']);
        }

        $overwriteCss = $this->components->confirm('Overwrite existing css/app.css file?', 'false');
        if ($overwriteCss) {
            copy(__DIR__.'/../../stubs/default/resources/css/app.css', base_path('resources/css/app.css'));
        } else {
            $this->components->warn('Skipped writing app.css file! Please ensure you have correctly imported tailwindcss.');
        }

        $this->line('');
        $this->components->info('PGP scaffolding installed successfully.');
    }
}
