<?php
// ============================================================
//  DentalCare — config/routes.php
//  All application routes
// ============================================================

// ---- Auth ----
Router::get('/login',             'AuthController@showLogin');
Router::post('/login',            'AuthController@login');
Router::get('/logout',            'AuthController@logout');

// ---- Dashboard ----
Router::get('/',                  'DashboardController@index');
Router::get('/dashboard',         'DashboardController@index');

// ---- Schedule ----
Router::get('/schedule',          'ScheduleController@index');
Router::post('/schedule/store',   'ScheduleController@store');
Router::post('/schedule/update',  'ScheduleController@update');
Router::post('/schedule/delete',  'ScheduleController@delete');

// ---- Patients ----
Router::get('/patients',          'PatientsController@index');
Router::get('/patients/create',   'PatientsController@create');
Router::post('/patients/store',   'PatientsController@store');
Router::get('/patients/show',     'PatientsController@show');
Router::get('/patients/edit',     'PatientsController@edit');
Router::post('/patients/update',  'PatientsController@update');
Router::post('/patients/delete',  'PatientsController@delete');

// ---- Anamnesis ----
Router::get('/anamnesis',         'AnamnesisController@index');
Router::get('/anamnesis/create',  'AnamnesisController@create');
Router::post('/anamnesis/store',  'AnamnesisController@store');
Router::get('/anamnesis/show',    'AnamnesisController@show');
Router::get('/anamnesis/edit',    'AnamnesisController@edit');
Router::post('/anamnesis/update', 'AnamnesisController@update');

// ---- Clinical Records ----
Router::get('/records',           'RecordsController@index');
Router::get('/records/create',    'RecordsController@create');
Router::post('/records/store',    'RecordsController@store');
Router::get('/records/show',      'RecordsController@show');
Router::get('/records/edit',      'RecordsController@edit');
Router::post('/records/update',   'RecordsController@update');

// ---- Finance ----
Router::get('/finance',           'FinanceController@index');
Router::post('/finance/store',    'FinanceController@store');
Router::post('/finance/update',   'FinanceController@update');

// ---- Reports ----
Router::get('/reports',           'ReportsController@index');
Router::get('/reports/export',    'ReportsController@export');

// ---- Settings ----
Router::get('/settings',          'SettingsController@index');
Router::post('/settings/update',  'SettingsController@update');

// ---- API (JSON endpoints for AJAX) ----
Router::get('/api/dashboard',           'ApiController@dashboard');
Router::get('/api/patients/search',     'ApiController@searchPatients');
Router::get('/api/schedule/week',       'ApiController@scheduleWeek');
Router::get('/api/dentists',            'ApiController@dentists');
Router::get('/api/procedures',          'ApiController@procedures');
Router::post('/api/appointments/store', 'ApiController@storeAppointment');
