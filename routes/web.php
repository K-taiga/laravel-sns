<?php

Auth::routes();
Route::get('/','ArticleController@index')->name('articles.index');
// resource(create,store,destroy,edit,update)にauthをかける
Route::resource('/articles','ArticleController')->except(['index','show'])->middleware('auth');
// resouceの中でshowだけmiddlewareなしでアクセス可
Route::resource('/articles','ArticleController')->only(['show']);
Route::prefix('articles')->name('articles.')->group(function () {
    Route::put('/{article}/like', 'ArticleController@like')->name('like')->middleware('auth');
    Route::delete('/{article}/like', 'ArticleController@unlike')->name('unlike')->middleware('auth');
});
Route::get('/tag/{name}','TagController@show')->name('tags.show');