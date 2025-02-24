<?php

namespace Plank\LaravelModelResolver;

use Plank\LaravelModelResolver\Contracts\ResolvesModels;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelModelResolverServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-model-resolver')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->startWith(function (InstallCommand $command) {
                    $command->info("Laravel Model Resolver Installer... \n");

                    if ($command->confirm('Would you like to publish the config file?')) {
                        $command->publishConfigFile();
                    }
                });

                $command->endWith(function (InstallCommand $command) {
                    $command->info('✅ Installation complete.');

                    $command->askToStarRepoOnGitHub('plank/laravel-model-resolver');
                });
            });
    }

    public function bootingPackage()
    {
        $this->app->scopedIf(ResolvesModels::class, function () {
            $repository = config('model-resolver.repository');

            return new $repository();
        });
    }
}
