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

Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Route::post('/get_lessons', 'HomeController@getLessons');
Route::post('/set_lessons', 'HomeController@setLessons');
Route::post('/is_lesson_free', 'HomeController@isLessonFree');


Route::post('/check_date_is_in_borders', 'LessonsController@checkDateInBorders');
Route::post('/get_lessons', 'LessonsController@getLessons');
Route::post('/is_lesson_free', 'LessonsController@isLessonFree');
Route::post('/is_has_free_lessons', 'LessonsController@isHasFreeLessons');
Route::post('/set_lessons', 'LessonsController@setLessons');

// Admin

Route::get('week_report', 'Admin\AdminController@weekReport')->name('week_report');
Route::get('show_date/{date}', 'Admin\AdminController@showDate')->name('show_date');
Route::get('student_registration', 'Admin\AdminController@studentRegistration')->name('studentRegistration');
Route::post('student_registration', 'Admin\AdminController@registerStudent')->name('registerStudent');

