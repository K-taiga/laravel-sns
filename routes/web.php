<?php

Auth::routes();
Route::get('/','ArticleController@index')->name('articles.index');
// resource(create,store,destroy,edit,update)にauthをかける
Route::resource('/articles','ArticleController')->except(['index','show'])->middleware('auth');
// resouceの中でshowだけmiddlewareなしでアクセス可
Route::resource('/articles','ArticleController')->only(['show']);