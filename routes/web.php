<?php

use App\Http\Controllers\EventController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




Route::get('/Contact', function () {
    return inertia('Contact');

});

Route::get('/About', function () {
    return inertia('About');

});


// CRUD
Route::get('/', [EventController::class, 'EventsList'])->name('events.list');
Route::get('/event/{event}', [EventController::class, 'getEvent'])->name('event.get');
Route::post('/add-event', [EventController::class, 'store'])->name('event.store');
Route::delete('/delete-event/{event}', [EventController::class, 'deleteEvent'])->name('event.delete');
Route::post('/update-event/{event}', [EventController::class, 'updateEvent'])->name('event.update');
Route::post('/events-search', [EventController::class, 'compareDates'])->name('events.search');



