<?php

namespace IbnuJa\JetstreamQuasar\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jetstream-quasar:install {--teams : Indicates if team support should be installed}
                                           {--pest : Indicates if Pest should be installed}
                                           {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the JetstreamQuasar components and resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Install Inertia...
        $this->requireComposerPackages('inertiajs/inertia-laravel:^0.5.2', 'laravel/jetstream:^2.6', 'tightenco/ziggy:^1.0', 'innocenzi/laravel-vite:0.2.*');
        // $this->requireComposerPackages('inertiajs/inertia-laravel:^0.5.2', 'tightenco/ziggy:^1.0', 'innocenzi/laravel-vite:0.2.*');
        // Publish...
        $this->callSilent('vendor:publish', ['--tag' => 'jetstream-config', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'jetstream-migrations', '--force' => true]);

        $this->callSilent('vendor:publish', ['--tag' => 'fortify-config', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'fortify-support', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'fortify-migrations', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'vite-config', '--force' => true]);

        // "Home" Route...
        $this->replaceInFile('/home', '/dashboard', app_path('Providers/RouteServiceProvider.php'));

        // Fortify Provider...
        copy(base_path('vendor/laravel/fortify/stubs/FortifyServiceProvider.php'), app_path('Providers/FortifyServiceProvider.php'));
        $this->installServiceProviderAfter('RouteServiceProvider', 'FortifyServiceProvider');

        // Configure Session...
        $this->configureSession();

        // AuthenticateSession Middleware...
        $this->replaceInFile(
            '// \Illuminate\Session\Middleware\AuthenticateSession::class',
            '\Laravel\Jetstream\Http\Middleware\AuthenticateSession::class',
            app_path('Http/Kernel.php')
        );

        // Install Stack...
        $this->installInertiaStack();

        // Tests...
        $stubs = $this->getTestStubsPath();

        if ($this->option('pest')) {
            $this->requireComposerPackages('pestphp/pest:^1.16', 'pestphp/pest-plugin-laravel:^1.1');

            copy($stubs . '/Pest.php', base_path('tests/Pest.php'));
            copy($stubs . '/ExampleTest.php', base_path('tests/Feature/ExampleTest.php'));
            copy($stubs . '/ExampleUnitTest.php', base_path('tests/Unit/ExampleTest.php'));
        }

        copy($stubs . '/AuthenticationTest.php', base_path('tests/Feature/AuthenticationTest.php'));
        copy($stubs . '/EmailVerificationTest.php', base_path('tests/Feature/EmailVerificationTest.php'));
        copy($stubs . '/PasswordConfirmationTest.php', base_path('tests/Feature/PasswordConfirmationTest.php'));
        copy($stubs . '/PasswordResetTest.php', base_path('tests/Feature/PasswordResetTest.php'));
        copy($stubs . '/RegistrationTest.php', base_path('tests/Feature/RegistrationTest.php'));
    }

    /**
     * Configure the session driver for Jetstream.
     *
     * @return void
     */
    protected function configureSession()
    {
        if (!class_exists('CreateSessionsTable')) {
            try {
                $this->call('session:table');
            } catch (Exception $e) {
                //
            }
        }

        $this->replaceInFile("'SESSION_DRIVER', 'file'", "'SESSION_DRIVER', 'database'", config_path('session.php'));
        $this->replaceInFile('SESSION_DRIVER=file', 'SESSION_DRIVER=database', base_path('.env'));
        $this->replaceInFile('SESSION_DRIVER=file', 'SESSION_DRIVER=database', base_path('.env.example'));
    }

    /**
     * Install the Inertia stack into the application.
     *
     * @return void
     */
    protected function installInertiaStack()
    {
        // Install NPM packages...
        $this->updateNodePackages(function () {
            return [
                '@inertiajs/inertia' => '^0.10.0',
                '@inertiajs/inertia-vue3' => '^0.5.1',
                '@quasar/extras' => '^1.13.3',
                '@quasar/vite-plugin' => '^1.0.9',
                '@types/lodash-es' => '^4.17.6',
                '@types/prismjs' => '^1.26.0',
                '@typescript-eslint/eslint-plugin' => '^5.16.0',
                '@typescript-eslint/parser' => '^5.16.0',
                '@vitejs/plugin-vue' => '^2.2.4',
                '@vue/compiler-sfc' => '^3.0.5',
                'axios' => '^0.25',
                'eslint' => '^7.12.1',
                'eslint-config-standard' => '^16.0.3',
                'eslint-plugin-import' => '^2.22.1',
                'eslint-plugin-node' => '^11.1.0',
                'eslint-plugin-promise' => '^4.2.1',
                'eslint-plugin-vue' => '^8.5.0',
                'lodash-es' => '^4.17.21',
                'prismjs' => '^1.27.0',
                'quasar' => '^2.6.1',
                'sass' => '1.32.0',
                'typescript' => '^4.6.3',
                'vite' => '^2.8.6',
                'vite-plugin-laravel' => '^0.2.0-beta.9',
                'vue' => '^3.0.5',
                'vue-loader' => '^16.1.2',
            ];
        });

        //dep
        $this->updateNodeScripts(function () {
            return  [
                "dev" => "vite",
                "build" => "vite build"
            ];
        });

        // Sanctum...
        (new Process([$this->phpBinary(), 'artisan', 'vendor:publish', '--provider=Laravel\Sanctum\SanctumServiceProvider', '--force'], base_path()))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });

        // // Tailwind Configuration...
        // // copy(__DIR__.'/../../stubs/inertia/tailwind.config.js', base_path('tailwind.config.js'));
        // // copy(__DIR__.'/../../stubs/inertia/webpack.mix.js', base_path('webpack.mix.js'));
        // // copy(__DIR__.'/../../stubs/inertia/webpack.config.js', base_path('webpack.config.js'));

        // Directories...
        (new Filesystem)->ensureDirectoryExists(app_path('Actions/Fortify'));
        (new Filesystem)->ensureDirectoryExists(app_path('Actions/Jetstream'));
        (new Filesystem)->ensureDirectoryExists(public_path('css'));
        // (new Filesystem)->ensureDirectoryExists(resource_path('css'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/components'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/layouts'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/pages/Jetstream'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/pages/Layouts'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/pages/Pages'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/pages/Pages/API'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/pages/Pages/Auth'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/pages/Pages/Profile'));
        (new Filesystem)->ensureDirectoryExists(resource_path('scripts'));
        (new Filesystem)->ensureDirectoryExists(resource_path('scripts/plugins'));
        (new Filesystem)->ensureDirectoryExists(resource_path('scripts/vite'));
        (new Filesystem)->ensureDirectoryExists(resource_path('markdown'));

        (new Filesystem)->deleteDirectory(resource_path('sass'));

        // Terms Of Service / Privacy Policy...
        copy(base_path('vendor/laravel/jetstream/stubs/resources/markdown/terms.md'), resource_path('markdown/terms.md'));
        copy(base_path('vendor/laravel/jetstream/stubs/resources/markdown/policy.md'), resource_path('markdown/policy.md'));

        // Service Providers...
        copy(base_path('vendor/laravel/jetstream/stubs/app/Providers/JetstreamServiceProvider.php'), app_path('Providers/JetstreamServiceProvider.php'));

        $this->installServiceProviderAfter('FortifyServiceProvider', 'JetstreamServiceProvider');

        // Middleware...
        (new Process([$this->phpBinary(), 'artisan', 'inertia:middleware', 'HandleInertiaRequests', '--force'], base_path()))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });

        $this->installMiddlewareAfter('SubstituteBindings::class', '\App\Http\Middleware\HandleInertiaRequests::class');

        // Models...
        copy(base_path('vendor/laravel/jetstream/stubs/app/Models/User.php'), app_path('Models/User.php'));

        // Factories...
        copy(base_path('vendor/laravel/jetstream/database/factories/UserFactory.php'), base_path('database/factories/UserFactory.php'));

        // Actions...
        copy(base_path('vendor/laravel/jetstream/stubs/app/Actions/Fortify/CreateNewUser.php'), app_path('Actions/Fortify/CreateNewUser.php'));
        copy(base_path('vendor/laravel/jetstream/stubs/app/Actions/Fortify/UpdateUserProfileInformation.php'), app_path('Actions/Fortify/UpdateUserProfileInformation.php'));
        copy(base_path('vendor/laravel/jetstream/stubs/app/Actions/Jetstream/DeleteUser.php'), app_path('Actions/Jetstream/DeleteUser.php'));

        // Blade Views...
        copy(__DIR__ . '/../../stubs/inertia/resources/views/app.blade.php', resource_path('views/app.blade.php'));

        if (file_exists(resource_path('views/welcome.blade.php'))) {
            unlink(resource_path('views/welcome.blade.php'));
        }

        // // Inertia Pages...
        // copy(base_path('vendor/laravel/jetstream/stubs/inertia/resources/js/Pages/Dashboard.vue', resource_path('js/Pages/Dashboard.vue'));
        // copy(base_path('vendor/laravel/jetstream/stubs/inertia/resources/js/Pages/PrivacyPolicy.vue', resource_path('js/Pages/PrivacyPolicy.vue'));
        // copy(base_path('vendor/laravel/jetstream/stubs/inertia/resources/js/Pages/TermsOfService.vue', resource_path('js/Pages/TermsOfService.vue'));
        // copy(base_path('vendor/laravel/jetstream/stubs/inertia/resources/js/Pages/Welcome.vue', resource_path('js/Pages/Welcome.vue'));

        copy(__DIR__ . '/../../stubs/inertia/resources/views/pages/Dashboard.vue', resource_path('views/pages/Dashboard.vue'));
        copy(__DIR__ . '/../../stubs/inertia/resources/views/pages/PrivacyPolicy.vue', resource_path('views/pages/PrivacyPolicy.vue'));
        copy(__DIR__ . '/../../stubs/inertia/resources/views/pages/TermsOfService.vue', resource_path('views/pages/TermsOfService.vue'));
        copy(__DIR__ . '/../../stubs/inertia/resources/views/pages/Welcome.vue', resource_path('views/pages/Welcome.vue'));

        // (new Filesystem)->copyDirectory(__DIR__.'/../../stubs/inertia/resources/js/Jetstream', resource_path('js/Jetstream'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/inertia/resources/views/layouts', resource_path('views/layouts'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/inertia/resources/views/pages/API', resource_path('views/pages/API'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/inertia/resources/views/pages/Auth', resource_path('views/pages/Auth'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/inertia/resources/views/pages/Profile', resource_path('views/pages/Profile'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/inertia/resources/views/layouts', resource_path('views/layouts'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/inertia/resources/views/components', resource_path('views/components'));

        // (new Filesystem)->copyDirectory(base_path('vendor/laravel/jetstream/stubs/inertia/resources/js/Jetstream', resource_path('js/Jetstream'));
        // (new Filesystem)->copyDirectory(base_path('vendor/laravel/jetstream/stubs/inertia/resources/js/Layouts', resource_path('js/Layouts'));
        // (new Filesystem)->copyDirectory(base_path('vendor/laravel/jetstream/stubs/inertia/resources/js/Pages/API', resource_path('js/Pages/API'));
        // (new Filesystem)->copyDirectory(base_path('vendor/laravel/jetstream/stubs/inertia/resources/js/Pages/Auth', resource_path('js/Pages/Auth'));
        // (new Filesystem)->copyDirectory(base_path('vendor/laravel/jetstream/stubs/inertia/resources/js/Pages/Profile', resource_path('js/Pages/Profile'));

        // Routes...
        $this->replaceInFile('auth:api', 'auth:sanctum', base_path('routes/api.php'));

        copy(base_path('vendor/laravel/jetstream/stubs/inertia/routes/web.php'), base_path('routes/web.php'));

        // Assets...
        // copy(base_path('vendor/laravel/jetstream/stubs/public/css/app.css', public_path('css/app.css'));
        // copy(base_path('vendor/laravel/jetstream/stubs/resources/css/app.css', resource_path('css/app.css'));
        // copy(base_path('vendor/laravel/jetstream/stubs/inertia/resources/js/app.js', resource_path('js/app.js'));


        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/inertia/resources/scripts', resource_path('scripts'));

        // Flush node_modules...
        // static::flushNodeModules();

        // Eslint & vite config
        copy(__DIR__ . '/../../stubs/vite.config.ts', base_path('vite.config.ts'));
        copy(__DIR__ . '/../../stubs/.eslintrc.js', base_path('.eslintrc.js'));
        copy(__DIR__ . '/../../stubs/.eslintignore', base_path('.eslintignore'));

        // Tests...
        $stubs = $this->getTestStubsPath();

        copy($stubs . '/inertia/ApiTokenPermissionsTest.php', base_path('tests/Feature/ApiTokenPermissionsTest.php'));
        copy($stubs . '/inertia/BrowserSessionsTest.php', base_path('tests/Feature/BrowserSessionsTest.php'));
        copy($stubs . '/inertia/CreateApiTokenTest.php', base_path('tests/Feature/CreateApiTokenTest.php'));
        copy($stubs . '/inertia/DeleteAccountTest.php', base_path('tests/Feature/DeleteAccountTest.php'));
        copy($stubs . '/inertia/DeleteApiTokenTest.php', base_path('tests/Feature/DeleteApiTokenTest.php'));
        copy($stubs . '/inertia/ProfileInformationTest.php', base_path('tests/Feature/ProfileInformationTest.php'));
        copy($stubs . '/inertia/TwoFactorAuthenticationSettingsTest.php', base_path('tests/Feature/TwoFactorAuthenticationSettingsTest.php'));
        copy($stubs . '/inertia/UpdatePasswordTest.php', base_path('tests/Feature/UpdatePasswordTest.php'));

        // Teams...
        if ($this->option('teams')) {
            $this->installInertiaTeamStack();
        }

        $this->line('');
        $this->info('Inertia scaffolding installed successfully.');
        $this->comment('Please execute "npm install && npm run dev" to build your assets.');
        // $this->comment(__DIR__ . '/../../stubs/inertia/resources/views/pages/Profile');
    }

    /**
     * Install the Inertia team stack into the application.
     *
     * @return void
     */
    protected function installInertiaTeamStack()
    {
        // Directories...
        (new Filesystem)->ensureDirectoryExists(resource_path('js/Pages/Profile'));

        // Pages...
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/inertia/resources/views/pages/Teams', resource_path('views/pages/Teams'));

        // Tests...
        $stubs = $this->getTestStubsPath();

        copy($stubs . '/inertia/CreateTeamTest.php', base_path('tests/Feature/CreateTeamTest.php'));
        copy($stubs . '/inertia/DeleteTeamTest.php', base_path('tests/Feature/DeleteTeamTest.php'));
        copy($stubs . '/inertia/InviteTeamMemberTest.php', base_path('tests/Feature/InviteTeamMemberTest.php'));
        copy($stubs . '/inertia/LeaveTeamTest.php', base_path('tests/Feature/LeaveTeamTest.php'));
        copy($stubs . '/inertia/RemoveTeamMemberTest.php', base_path('tests/Feature/RemoveTeamMemberTest.php'));
        copy($stubs . '/inertia/UpdateTeamMemberRoleTest.php', base_path('tests/Feature/UpdateTeamMemberRoleTest.php'));
        copy($stubs . '/inertia/UpdateTeamNameTest.php', base_path('tests/Feature/UpdateTeamNameTest.php'));

        $this->ensureApplicationIsTeamCompatible();
    }

    /**
     * Ensure the installed user model is ready for team usage.
     *
     * @return void
     */
    protected function ensureApplicationIsTeamCompatible()
    {
        // Publish Team Migrations...
        $this->callSilent('vendor:publish', ['--tag' => 'jetstream-team-migrations', '--force' => true]);

        // Configuration...
        $this->replaceInFile('// Features::teams([\'invitations\' => true])', 'Features::teams([\'invitations\' => true])', config_path('jetstream.php'));

        // Directories...
        (new Filesystem)->ensureDirectoryExists(app_path('Actions/Jetstream'));
        (new Filesystem)->ensureDirectoryExists(app_path('Events'));
        (new Filesystem)->ensureDirectoryExists(app_path('Policies'));

        // Service Providers...
        copy(base_path('vendor/laravel/jetstream/stubs/app/Providers/AuthServiceProvider.php'), app_path('Providers/AuthServiceProvider.php'));
        copy(base_path('vendor/laravel/jetstream/stubs/app/Providers/JetstreamWithTeamsServiceProvider.php'), app_path('Providers/JetstreamServiceProvider.php'));

        // Models...
        copy(base_path('vendor/laravel/jetstream/stubs/app/Models/Membership.php'), app_path('Models/Membership.php'));
        copy(base_path('vendor/laravel/jetstream/stubs/app/Models/Team.php'), app_path('Models/Team.php'));
        copy(base_path('vendor/laravel/jetstream/stubs/app/Models/TeamInvitation.php'), app_path('Models/TeamInvitation.php'));
        copy(base_path('vendor/laravel/jetstream/stubs/app/Models/UserWithTeams.php'), app_path('Models/User.php'));

        // Actions...
        copy(base_path('vendor/laravel/jetstream/stubs/app/Actions/Jetstream/AddTeamMember.php'), app_path('Actions/Jetstream/AddTeamMember.php'));
        copy(base_path('vendor/laravel/jetstream/stubs/app/Actions/Jetstream/CreateTeam.php'), app_path('Actions/Jetstream/CreateTeam.php'));
        copy(base_path('vendor/laravel/jetstream/stubs/app/Actions/Jetstream/DeleteTeam.php'), app_path('Actions/Jetstream/DeleteTeam.php'));
        copy(base_path('vendor/laravel/jetstream/stubs/app/Actions/Jetstream/DeleteUserWithTeams.php'), app_path('Actions/Jetstream/DeleteUser.php'));
        copy(base_path('vendor/laravel/jetstream/stubs/app/Actions/Jetstream/InviteTeamMember.php'), app_path('Actions/Jetstream/InviteTeamMember.php'));
        copy(base_path('vendor/laravel/jetstream/stubs/app/Actions/Jetstream/RemoveTeamMember.php'), app_path('Actions/Jetstream/RemoveTeamMember.php'));
        copy(base_path('vendor/laravel/jetstream/stubs/app/Actions/Jetstream/UpdateTeamName.php'), app_path('Actions/Jetstream/UpdateTeamName.php'));

        copy(base_path('vendor/laravel/jetstream/stubs/app/Actions/Fortify/CreateNewUserWithTeams.php'), app_path('Actions/Fortify/CreateNewUser.php'));

        // Policies...
        (new Filesystem)->copyDirectory(base_path('vendor/laravel/jetstream/stubs/app/Policies'), app_path('Policies'));

        // Factories...
        copy(base_path('vendor/laravel/jetstream/database/factories/UserFactory.php'), base_path('database/factories/UserFactory.php'));
        copy(base_path('vendor/laravel/jetstream/database/factories/TeamFactory.php'), base_path('database/factories/TeamFactory.php'));
    }

    /**
     * Install the service provider in the application configuration file.
     *
     * @param  string  $after
     * @param  string  $name
     * @return void
     */
    protected function installServiceProviderAfter($after, $name)
    {
        if (!Str::contains($appConfig = file_get_contents(config_path('app.php')), 'App\\Providers\\' . $name . '::class')) {
            file_put_contents(config_path('app.php'), str_replace(
                'App\\Providers\\' . $after . '::class,',
                'App\\Providers\\' . $after . '::class,' . PHP_EOL . '        App\\Providers\\' . $name . '::class,',
                $appConfig
            ));
        }
    }

    /**
     * Install the middleware to a group in the application Http Kernel.
     *
     * @param  string  $after
     * @param  string  $name
     * @param  string  $group
     * @return void
     */
    protected function installMiddlewareAfter($after, $name, $group = 'web')
    {
        $httpKernel = file_get_contents(app_path('Http/Kernel.php'));

        $middlewareGroups = Str::before(Str::after($httpKernel, '$middlewareGroups = ['), '];');
        $middlewareGroup = Str::before(Str::after($middlewareGroups, "'$group' => ["), '],');

        if (!Str::contains($middlewareGroup, $name)) {
            $modifiedMiddlewareGroup = str_replace(
                $after . ',',
                $after . ',' . PHP_EOL . '            ' . $name . ',',
                $middlewareGroup,
            );

            file_put_contents(app_path('Http/Kernel.php'), str_replace(
                $middlewareGroups,
                str_replace($middlewareGroup, $modifiedMiddlewareGroup, $middlewareGroups),
                $httpKernel
            ));
        }
    }

    /**
     * Returns the path to the correct test stubs.
     *
     * @return string
     */
    protected function getTestStubsPath()
    {
        return $this->option('pest')
            ? base_path('vendor/laravel/jetstream/stubs/pest-tests')
            : base_path('vendor/laravel/jetstream/stubs/tests');
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param  mixed  $packages
     * @return void
     */
    protected function requireComposerPackages($packages)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = [$this->phpBinary(), $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param  bool  $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (!file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param  bool  $dev
     * @return void
     */
    protected static function updateNodeScripts(callable $callback)
    {
        if (!file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = 'scripts';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    /**
     * Delete the "node_modules" directory and remove the associated lock files.
     *
     * @return void
     */
    protected static function flushNodeModules()
    {
        tap(new Filesystem, function ($files) {
            $files->deleteDirectory(base_path('node_modules'));

            $files->delete(base_path('yarn.lock'));
            $files->delete(base_path('package-lock.json'));
        });
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Get the path to the appropriate PHP binary.
     *
     * @return string
     */
    protected function phpBinary()
    {
        return (new PhpExecutableFinder())->find(false) ?: 'php';
    }
}
