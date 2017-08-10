<?php

Route::get('/', 'IndexController@index')->name('index');
Route::get('/category/{cateSlug}', 'CategoriesController@show')->name('category');
Route::get('/category/{cateSlug}/post/{post}', 'PostsController@show')->name('post');

Route::group(
    ['middleware' => 'auth'], function () {
        Route::post('ajax_upload_picture', 'PicturesController@upload');
    }
);
