<?php

use Livewire\Volt\Volt;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsAdminOrRedac;

Volt::route('/', 'index');
Volt::route('/category/{slug}', 'index');
Volt::route('/posts/{slug}', 'posts.show')->name('posts.show');
Volt::route('/search/{param}', 'index')->name('posts.search');
Volt::route('/pages/{page:slug}', 'pages.show')->name('pages.show');
Volt::route('/contact', 'contact-form')->name('contact');
Volt::route('/calendar', 'calendar')->name('calendar');
Volt::route('/about', 'about')->name('about');

Route::middleware('guest')->group(function () {
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/register', 'auth.register');
    Volt::route('/forgot-password', 'auth.forgot-password');
    Volt::route('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
});
Route::middleware('auth')->group(function () {
    Volt::route('/profile', 'auth.profile')->name('profile');
    Volt::route('/favorites', 'index')->name('posts.favorites');
    Route::middleware(IsAdminOrRedac::class)->prefix('admin')->group(function () {
        Volt::route('/dashboard', 'admin.index')->name('admin');
        Volt::route('/posts/index', 'admin.posts.index')->name('posts.index');
        Volt::route('/posts/create', 'admin.posts.create')->name('posts.create');
        Volt::route('/posts/{post:slug}/edit', 'admin.posts.edit')->name('posts.edit');
        Route::middleware(IsAdmin::class)->group(function () {
            Volt::route('/categories/index', 'admin.categories.index')->name('categories.index');
            Volt::route('/categories/{category}/edit', 'admin.categories.edit')->name('categories.edit');
            Volt::route('/pages/index', 'admin.pages.index')->name('pages.index');
            Volt::route('/pages/create', 'admin.pages.create')->name('pages.create');
            Volt::route('/pages/{page:slug}/edit', 'admin.pages.edit')->name('pages.edit');
            Volt::route('/users/index', 'admin.users.index')->name('users.index');
            Volt::route('/users/{user}/edit', 'admin.users.edit')->name('users.edit');
            Volt::route('/comments/index', 'admin.comments.index')->name('comments.index');
            Volt::route('/comments/{comment}/edit', 'admin.comments.edit')->name('comments.edit');
            Volt::route('/menus/index', 'admin.menus.index')->name('menus.index');
            Volt::route('/menus/{menu}/edit', 'admin.menus.edit')->name('menus.edit');
            Volt::route('/submenus/{submenu}/edit', 'admin.menus.editsub')->name('submenus.edit');
            Volt::route('/footers/index', 'admin.menus.footers')->name('menus.footers');
            Volt::route('/footers/{footer}/edit', 'admin.menus.editfooter')->name('footers.edit');
            Volt::route('/images/index', 'admin.images.index')->name('images.index');
            Volt::route('/images/{year}/{month}/{id}/edit', 'admin.images.edit')->name('images.edit');
            Volt::route('/settings', 'admin.settings')->name('settings');
            Volt::route('/contacts', 'admin.contact-list')->name('contact-list');
        });
    });
});
