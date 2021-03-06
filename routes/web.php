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

Route::get('/language/{locale}', 'LanguageController@index')->name('change_lang');

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
Route::post('delete_lesson', 'LessonsController@deleteLesson');

// Admin

Route::get('settings', 'Admin\AdminController@siteSettings')->name('siteSettings');
Route::get('week_report', 'Admin\AdminController@weekReport')->name('week_report');
Route::get('choose_student', 'Admin\AdminController@chooseStudent')->name('choose_student');
Route::get('student_home', 'Admin\AdminController@studentHome')->name('student_home');
Route::get('show_date/{date}', 'Admin\AdminController@showDate')->name('show_date');
Route::get('student_registration', 'Admin\AdminController@studentRegistration')->name('studentRegistration');
Route::post('student_registration', 'Admin\AdminController@registerStudent')->name('registerStudent');
Route::post('remove_student_from_lesson', 'Admin\AdminController@removeStudent');
Route::post('add_student_to_lesson', 'Admin\AdminController@addStudent');
Route::get('delete_student', 'Admin\AdminController@deleteStudentView')->name('deleteStudent');
Route::post('delete_student', 'Admin\AdminController@deleteStudent')->name('delete_student');
Route::post('updateStudentSettings', 'Admin\AdminController@updateStudentSettings')->name('updateStudentSettings');
Route::get('show_report', 'Admin\AdminController@showReportView')->name('show_report');
Route::get('student_report', 'Admin\AdminController@studentReport')->name('student_report');
Route::get('download_pdf', 'Admin\AdminController@downloadPDF')->name('download_pdf');
Route::get('download_csv', 'Admin\AdminController@downloadCSV')->name('download_csv');
Route::get('show_update', 'Admin\AdminController@showUpdateView')->name('show_update');
Route::get('student_update', 'Admin\AdminController@studentUpdate')->name('student_update');
