<?php

/*
|--------------------------------------------------------------------------
| Installation Web Routes
|--------------------------------------------------------------------------
|
| Routes related to installation of the software
|
*/

Route::get('/install-start', 'IController@index')->name('install.index');
Route::get('/install/details', 'IController@details')->name('install.details');
Route::post('/install/post-details', 'IController@postDetails')->name('install.postDetails');
Route::post('/install/install-alternate', 'IController@installAlternate')->name('install.installAlternate');
Route::get('/install/success', 'IController@successfull')->name('install.success');
Route::get('/install/update', 'IController@updateConfirmation')->name('install.updateConfirmation');
Route::post('/install/update', 'IController@update')->name('install.update');