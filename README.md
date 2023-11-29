![logo](https://github.com/cgfeel/laravel-rotate-captcha/assets/578141/698a686e-38b5-4a7c-b0e7-5b8c6763f4fa)

[![Latest Stable Version](http://poser.pugx.org/levi/laravel-rotate-captcha/v)](https://packagist.org/packages/levi/laravel-rotate-captcha) [![Total Downloads](http://poser.pugx.org/levi/laravel-rotate-captcha/downloads)](https://packagist.org/packages/levi/laravel-rotate-captcha) [![Latest Unstable Version](http://poser.pugx.org/levi/laravel-rotate-captcha/v/unstable)](https://packagist.org/packages/levi/laravel-rotate-captcha) [![License](http://poser.pugx.org/levi/laravel-rotate-captcha/license)](https://packagist.org/packages/levi/laravel-rotate-captcha) [![PHP Version Require](http://poser.pugx.org/levi/laravel-rotate-captcha/require/php)](https://packagist.org/packages/levi/laravel-rotate-captcha)

一个开箱即用的滑动验证码Laravel扩展，基于[[isszz/rotate-captcha](https://github.com/ahsankhatri/wordpress-auth-driver-laravel/tree/master)]做的二次开发；结合了腾讯防水墙，增加安全策略（[设计思路](#设计思路)）

## 安装

安装此包你需要：

- Laravel 9.0及以上
- PHP 8.0及以上
- 安装有php-gd或php-imagick扩展

通过composer命令行进行安装：

```
composer require levi/laravel-rotate-captcha
```

或者将下面的配置添加到`composer.json`并执行`composer update`

```
"require": {
    "levi/laravel-rotate-captcha": "^1.0",
}
```

## 设置

迁移配置文件、语言包、附件图

```
php artisan vendor:publish --provider="Levi\LaravelRotateCaptcha\CaptchaProvider"
```

添加中间件，修改`App\Http\Kernel`在`protected $middlewareAliases`中添加

```
'rotate.captcha' => \Levi\LaravelRotateCaptcha\CaptchaMiddleware::class
```

## 使用

### 默认开箱即用

默认提供了5个路由、1个中间件、1个控制器

| url | router name | method | usage |
| ----- | ----- | ----- | ----- |
| `/rotate.captcha` | `rotate.captcha.get` | `GET` | 获取验证信息 |
| `/rotate.captcha/{id}` | `rotate.captcha.load` | `GET` | 获取验证图片 |
| `/rotate.captcha/verify/{angle}/{token?}` | `rotate.captcha.verify` | `GET` | 验证信息 |
| `/rotate.captcha/verify/{angle}/{token?}` | `rotate.captcha.verify` | `OPTIONS` | 跨域提交验证信息 |
| `/rotate.captcha` | `rotate.captcha.store` | `POST` | 获取验证信息，见注1 |
| `/rotate.captcha/test/action` | `rotate.captcha.test` | `any` | 权限测试，默认仅供本次测试 |

- 注1: `rotate.captcha.store`和`rotate.captcha.get`一样，不同在于接受2个可选请求参数:
  - `size`：默认生成的验证图片尺寸是`350px`，可根据用户设备尺寸不同定制
  - `output`：默认按照配置文件输出验证图片格式（如`webp`），对于一些老设备的用户可单独配置

客户端请求和接受的数据，见`react-rotate-captcha`（整理中...）

### 手动设置

**仅修改中间件：** 通过中间件自定义处理，修改配置配置`config/rotate.captcha.php`中`middleware`项

**完全自定义：** 关闭默认提供的路由和控制器

- 配置`config/rotate.captcha.php`中的`routers`项，关闭对应的路由
- 参考文件`CaptchaController.php`和`CaptchaMiddleware.php`
- 参考：[设计思路](#设计思路)

## 跨域

根据情况设置，以下仅供参考，修改`config/cors.php`：

```
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'rotate.captcha*'],

    'allowed_methods' => ['GET, POST, PATCH, PUT, OPTIONS'],

    // 下面是本地调试的URL，根据生产环境修改
    'allowed_origins' => ['http://localhost:8686', 'http://192.168.31.204:8686'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['X-Captchatoken', 'X-Captchapolicie', 'X-Captchasid', 'X-Captchaticket'],

    'exposed_headers' => ['X-Captchatoken'],

    'max_age' => 0,

    'supports_credentials' => true,
```

## 缓存

用于存储验证信息，默认按照`Laravel`缓存配置`config/cache.php`默认引擎`file`

- 建议配置`cache.php`中的默认缓存
- 如果要和默认缓存不一样，修改`config/rotate.captcha.php`中的`cache`，采用的缓存需要提前在`cache.php`配置好

## 文件驱动

用于存储验证图片，默认按照`Laravel`文件配置`config/filesystem.php`默认引擎`local`

- 建议配置`filesystem.php`中的驱动引擎
- 如果要和默认驱动不一样，修改`config/rotate.captcha.php`中的`disk`，采用的驱动需要提前在`filesystem.php`配置好

## 多语言

提供中文和英文，默认按照`Laravel`语言配置`config/app.php`配置为`en`

 - 建议修改`app.php`中的`locale`
 - 如果要和默认语言不一样，修改`config/rotate.captcha.php`中的`lang`
 - 如果需要默认提供之外的语言包，在根目录下的`lang/vendor/rotate.captcha`，参考语言包添加语言

## 设计思路

![New Board](https://github.com/cgfeel/laravel-rotate-captcha/assets/578141/27e82f87-0937-4e23-9e08-395fd9f0adda)

