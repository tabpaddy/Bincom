<?php

use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/', 'polling-unit-result')
    ->name('polling-unit.result');

Route::livewire('/lga-result', 'lga-result')
    ->name('lga.result');

Route::livewire('/create-result', 'create-result')
    ->name('create.result');
