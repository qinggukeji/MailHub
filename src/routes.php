<?php

Route::group([
    'prefix' => 'webhook', 
    'as' => 'MailHub::', 
    'namespace' => 'MrVokia\MailHub\Controllers'
], function()
{
    Route::get('/sendcloud_callback', [
        'as' => 'sendcloud',
        'uses' => 'CallbackController@sendcloud'
    ]);

    // ... more callback
});