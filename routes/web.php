<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Redirect::to('https://planco.ch', 302);
});
