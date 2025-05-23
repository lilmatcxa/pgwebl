<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\PolygonsController;
use App\Http\Controllers\PolylinesController;


Route::get('/', [PointsController::class, 'index'])->name('map');
Route::get('/', [PolylinesController::class, 'index'])->name('map');
Route::get('/', [PolygonsController::class, 'index'])->name('map');

Route::get('/table', [TableController::class, 'index'])->name('table');

Route::post('/point-store', [PointsController::class, 'store'])->name('point.store');

Route::post('/polyline-store', [PolylinesController::class, 'store'])->name('polyline.store');

Route::post('/polygon-store', [PolygonsController::class, 'store'])->name('polygon.store');

Route::resource('points', PointsController::class);
Route::resource('polylines', PolylinesController::class);
Route::resource('polygons', PolygonsController::class);

