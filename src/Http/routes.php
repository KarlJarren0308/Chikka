<?php

Route::group(['middleware' => 'web'], function() {
    Route::get('chikka/read/incoming/{id?}', ['as' => 'chikka.incoming', 'uses' => 'KarlMacz\Chikka\Http\Controllers\ChikkaController@readIncomingSms']);
    Route::get('chikka/read/outgoing/{id?}', ['as' => 'chikka.outgoing', 'uses' => 'KarlMacz\Chikka\Http\Controllers\ChikkaController@readOutgoingSms']);

    Route::post('chikka/receive', ['as' => 'chikka.receive', 'uses' => 'KarlMacz\Chikka\Http\Controllers\ChikkaController@receive']);
    Route::post('chikka/send', ['as' => 'chikka.send', 'uses' => 'KarlMacz\Chikka\Http\Controllers\ChikkaController@send']);
});
