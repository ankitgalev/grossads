<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Admin\DashboardController
    as AdminDashboardController;

use App\Http\Controllers\Publisher\DashboardController
    as PublisherDashboardController;

use App\Http\Controllers\Advertiser\DashboardController
    as AdvertiserDashboardController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\Admin\AdvertiserController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\CampaignPublisherController;
use App\Http\Controllers\Admin\CampaignAssignmentController;
use App\Http\Controllers\Admin\ApiDocsController;
use App\Http\Controllers\AdvertiserDashboardControllers;
use App\Http\Controllers\Admin\MetaIntegrationController;


/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});
Route::view('/privacy', 'privacy');
Route::view('/terms', 'terms');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Auth::routes();


/*
|--------------------------------------------------------------------------
| Common Dashboard Redirect
|--------------------------------------------------------------------------
*/

Route::get(
    '/dashboard',
    [DashboardController::class, 'index']
)->middleware('auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get(
            '/dashboard',
            [AdminDashboardController::class, 'index']
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Publishers
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'publishers',
            PublisherController::class
        );

        Route::post(
            'publishers/{publisher}/toggle-status',
            [PublisherController::class, 'toggleStatus']
        )->name('publishers.toggle-status');


        /*
        |--------------------------------------------------------------------------
        | Advertisers
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'advertisers',
            AdvertiserController::class
        );

        Route::post(
            'advertisers/{advertiser}/toggle-status',
            [AdvertiserController::class, 'toggleStatus']
        )->name('advertisers.toggle-status');
        Route::resource(
            'campaigns',
            CampaignController::class
        );
        // Route::get(
        //     '/leads',
        //     [LeadController::class, 'index']
        // )->name('leads.index');


        // Route::get(
        //     '/leads/api-docs',
        //     [LeadController::class, 'apiDocs']
        // )->name('leads.api-docs');


        // Route::get(
        //     '/leads/{id}',
        //     [LeadController::class, 'show']
        // )->name('leads.show');


        // Route::post(
        //     '/leads/{id}/status',
        //     [LeadController::class, 'updateStatus']
        // )->name('leads.update-status');

        Route::get('leads', [LeadController::class, 'index'])
            ->name('leads.index');

        Route::get('leads/{id}', [LeadController::class, 'show'])
            ->name('leads.show');

        Route::patch('leads/{id}/status', [LeadController::class, 'updateStatus'])
            ->name('leads.status');
        
        Route::get('api-docs', [ApiDocsController::class, 'index'])->name('api-docs.index');

        Route::get(
            '/campaigns/{campaign}/assign',
            [CampaignPublisherController::class, 'create']
        )->name('campaigns.assign');


        Route::post(
            '/campaigns/{campaign}/assign',
            [CampaignPublisherController::class, 'store']
        )->name('campaigns.assign.store');


        Route::delete(
            '/campaigns/{campaign}/publisher/{publisher}',
            [CampaignPublisherController::class, 'destroy']
        )->name('campaigns.publisher.remove');
        Route::get(
            'campaigns/{campaign}/assign',
            [CampaignAssignmentController::class, 'index']
        )->name('campaigns.assign');

        Route::post(
            'campaigns/{campaign}/assign',
            [CampaignAssignmentController::class, 'store']
        )->name('campaigns.assign.store');

        Route::patch(
            'campaigns/{campaign}/assignments/{assignment}/status',
            [CampaignAssignmentController::class, 'toggleStatus']
        )->name('campaigns.assignments.status');        
        
        Route::get('/meta-integrations', [
            MetaIntegrationController::class, 'index'
        ])->name('meta-integrations.index');

        Route::get('/meta-integrations/create', [
            MetaIntegrationController::class, 'create'
        ])->name('meta-integrations.create');

        Route::post('/meta-integrations', [
            MetaIntegrationController::class, 'store'
        ])->name('meta-integrations.store');

        Route::post('/meta-integrations/{id}/toggle', [
            MetaIntegrationController::class, 'toggle'
        ])->whereNumber('id')->name('meta-integrations.toggle');

        

    });
// Route::prefix('admin')
//     ->name('admin.')
//     ->middleware(['auth', 'role:admin'])
//     ->group(function () {

//         Route::get(
//             '/dashboard',
//             [AdminDashboardController::class, 'index']
//         )->name('dashboard');

//     });


/*
|--------------------------------------------------------------------------
| PUBLISHER
|--------------------------------------------------------------------------
*/

Route::prefix('publisher')
    ->name('publisher.')
    ->middleware(['auth', 'role:publisher'])
    ->group(function () {

        Route::get(
            '/dashboard',
            [PublisherDashboardController::class, 'index']
        )->name('dashboard');
        Route::get('/dashboard', [PublisherController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/campaigns', [PublisherController::class, 'campaigns'])
            ->name('campaigns');

        Route::get('/leads', [PublisherController::class, 'leads'])
            ->name('leads');
        Route::get('/api-docs', [PublisherController::class, 'apiDocs'])
            ->name('api-docs');

    });


/*
|--------------------------------------------------------------------------
| ADVERTISER
|--------------------------------------------------------------------------
*/

Route::prefix('advertiser')
    ->name('advertiser.')
    ->middleware(['auth', 'role:advertiser'])
    ->group(function () {

        Route::get(
            '/dashboard',
            [AdvertiserDashboardController::class, 'index']
        )->name('dashboard');

        Route::get('/dashboard', [
            AdvertiserDashboardControllers::class,
            'index'
        ])->name('dashboard');

        Route::get('/campaigns', [
            AdvertiserDashboardControllers::class,
            'campaigns'
        ])->name('campaigns');

        Route::get('/leads', [
            AdvertiserDashboardControllers::class,
            'leads'
        ])->name('leads');
        
        Route::get('/leads/{id}', [
            AdvertiserDashboardControllers::class,
            'leadDetails'
        ])->whereNumber('id')->name('leads.show');

    });