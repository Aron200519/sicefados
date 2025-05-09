<?php

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

use Illuminate\Support\Facades\Route;
use Modules\FABRICASOFT\Http\Controllers\FABRICASOFTController;

Route::prefix('fabricasoft')->group(function() {
    Route::get('/index', 'FABRICASOFTController@index')->name('cefa.fabricasoft.index');
    Route::get('/admin/welcome', 'FABRICASOFTController@admin')->name('fabricasoft.admin.welcome');


});
