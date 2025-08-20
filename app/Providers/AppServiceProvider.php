<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Setting;
use Illuminate\View\View;
use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use App\Policies\ManageNewsletterPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (!$this->app->runningInConsole()) {
            $settings = Setting::all();
            foreach ($settings as $setting) {
                config(['app.' . $setting->key => $setting->value]);
            }
        }
        Facades\View::composer(['components.layouts.app'], function (View $view) {
            $view->with(
                'menus',
                Menu::with(['submenus' => function ($query) {
                    $query->orderBy('order');
                }])->orderBy('order')->get()
            );
        });

          \Illuminate\Support\Facades\Gate::define('manage-newsletters', [ManageNewsletterPolicy::class, 'manage']);
    }
}
