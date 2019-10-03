<?php


Route::get('/', function () {
    return view('arvore');
});

Route::post('submit', 'Base@CarragaXml');
