<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::any('browsersync/{event?}/{path}', '\ABetterToolkitController@handle')->where('path','.*');
Route::any('image/{style?}/{path}', '\ABetterToolkitController@handle')->where('path','.*');
