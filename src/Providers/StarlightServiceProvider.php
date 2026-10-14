<?php

namespace ProjectSaturnStudios\Themes\Starlight\Providers;

use ProjectSaturnStudios\Themes\Starlight\Console\Commands\UpdateAssets;
use ProjectSaturnStudios\Themes\Starlight\Markdown\Renderer;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class StarlightServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('starlight')
            ->hasConfigFile('starlight')
            ->hasViews('starlight')
            ->hasCommand(UpdateAssets::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Renderer::class, function (): Renderer {
            $hosts = (array) config('starlight.markdown.internal_hosts', []);
            if ($host = parse_url((string) config('app.url'), PHP_URL_HOST)) {
                $hosts[] = $host;
            }

            return new Renderer(
                array_values(array_unique($hosts)),
                (string) config('starlight.markdown.themes.light', 'github-light'),
                (string) config('starlight.markdown.themes.dark', 'github-dark'),
                (string) config('starlight.markdown.html_input', 'allow'),
            );
        });
    }
}
