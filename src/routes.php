<?php
Route::get('yiche/ocr/idcard', 'Yiche\Ocr\Http\Controllers\OcrController@idcard');
Route::post('yiche/ocr/idcard', 'Yiche\Ocr\Http\Controllers\OcrController@idcard');
Route::get('yiche/ocr/businessLicense', 'Yiche\Ocr\Http\Controllers\OcrController@businessLicense');
Route::post('yiche/ocr/businessLicense', 'Yiche\Ocr\Http\Controllers\OcrController@businessLicense');
Route::get('yiche/ocr/vehicleLicense', 'Yiche\Ocr\Http\Controllers\OcrController@vehicleLicense');
Route::post('yiche/ocr/vehicleLicense', 'Yiche\Ocr\Http\Controllers\OcrController@vehicleLicense');
Route::get('yiche/ocr/bankcard', 'Yiche\Ocr\Http\Controllers\OcrController@bankcard');
Route::post('yiche/ocr/bankcard', 'Yiche\Ocr\Http\Controllers\OcrController@bankcard');
