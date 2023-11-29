<?php

use Illuminate\Support\Facades\Route;
use Levi\LaravelRotateCaptcha\CaptchaController;

Route::get('/rotate.captcha', [CaptchaController::class, 'create'])
    ->name('rotate.captcha.get');

Route::get('/rotate.captcha/{id}', [CaptchaController::class, 'get'])
    ->name('rotate.captcha.load');

// options为跨域时传递头部使用
Route::match(['get', 'options'], '/rotate.captcha/verify/{angle}/{token?}', [CaptchaController::class, 'verify'])
    ->name('rotate.captcha.verify');

// 为了图片兼容格式留一个口子
Route::post('/rotate.captcha', [CaptchaController::class, 'create'])
    ->name('rotate.captcha.get');

// 鉴权测试
Route::any('/rotate.captcha/test/action', [CaptchaController::class, 'test'])
    ->name('rotate.captcha.test');
