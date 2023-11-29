![logo](https://github.com/cgfeel/laravel-rotate-captcha/assets/578141/698a686e-38b5-4a7c-b0e7-5b8c6763f4fa)

[![Latest Stable Version](http://poser.pugx.org/levi/laravel-rotate-captcha/v)](https://packagist.org/packages/levi/laravel-rotate-captcha) [![Total Downloads](http://poser.pugx.org/levi/laravel-rotate-captcha/downloads)](https://packagist.org/packages/levi/laravel-rotate-captcha) [![Latest Unstable Version](http://poser.pugx.org/levi/laravel-rotate-captcha/v/unstable)](https://packagist.org/packages/levi/laravel-rotate-captcha) [![License](http://poser.pugx.org/levi/laravel-rotate-captcha/license)](https://packagist.org/packages/levi/laravel-rotate-captcha) [![PHP Version Require](http://poser.pugx.org/levi/laravel-rotate-captcha/require/php)](https://packagist.org/packages/levi/laravel-rotate-captcha)

一个开箱即用的滑动验证码Laravel扩展，基于[[isszz/rotate-captcha](https://github.com/ahsankhatri/wordpress-auth-driver-laravel/tree/master)]做的二次开发；结合了腾讯防水墙，增加安全策略，查看：[策略](#策略-policie) 和 [设计思路](#设计思路-design)

前端代码整理中，待更新...

<img width="351" alt="image" src="https://github.com/cgfeel/laravel-rotate-captcha/assets/578141/0f6d4073-2811-4c5b-807d-a95d56973848">

视频演示：

https://github.com/cgfeel/laravel-rotate-captcha/assets/578141/afa169d1-05c3-43d6-b7e7-cabaa8c5dbc5

## 安装 (Installation)

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

## 设置 (Configuration)

迁移配置文件、语言包、附件图

```
php artisan vendor:publish --provider="Levi\LaravelRotateCaptcha\CaptchaProvider"
```

添加中间件，修改`App\Http\Kernel`在`protected $middlewareAliases`中添加

```
'rotate.captcha' => \Levi\LaravelRotateCaptcha\CaptchaMiddleware::class
```

## 使用 (Usage)

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
- 参考：[策略](#策略) 和 [设计思路](#设计思路)

## 更新验证图片 (Updating)

**手动更新：** 目录位置`\storage\app\{rotate.captcha}`，其中存储引擎和位置可在配置文件中修改。

**自动更新：** 

请通过在线的图床接口，通过调度`App\Console\Kernel`定期更新，这里提供一个存储的方法，以下为参考示例：

```
<?php

$image = file_get_contents({custome_api_url});
app('rotate.captcha.file', ['path' => 'origin'])->prepend('costome_name.jpg', $image);
```

> 提示：
> 风景图安全系数 > 人物图 > 卡通图片，但不建议使用`bing`每日一图作为验证图片，因为验证的图片每天都是固定的

## 清理过期图片 (Cleanup)

请通过调度`App\Console\Kernel`定期清理，这里提供一个清理的方法，以下为参考示例：

```
<?php
app('rotate.captcha.file')->clear();   // 清理前一天
app('rotate.captcha.file')->clear(3600);   // 清理1小时前
app('rotate.captcha.file')->clear()->cost();   // 清理后返回剩余总数
```

## 跨域 (Cors)

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

## 缓存 (Cache)

用于存储验证信息，默认按照`Laravel`缓存配置`config/cache.php`默认引擎`file`

- 建议配置`cache.php`中的默认缓存
- 如果要和默认缓存不一样，修改`config/rotate.captcha.php`中的`cache`，采用的缓存需要提前在`cache.php`配置好

## 文件驱动 (Disk)

用于存储验证图片，默认按照`Laravel`文件配置`config/filesystem.php`默认引擎`local`

- 建议配置`filesystem.php`中的驱动引擎
- 如果要和默认驱动不一样，修改`config/rotate.captcha.php`中的`disk`，采用的驱动需要提前在`filesystem.php`配置好

## 多语言 (Language)

提供中文和英文，默认按照`Laravel`语言配置`config/app.php`配置为`en`

 - 建议修改`app.php`中的`locale`
 - 如果要和默认语言不一样，修改`config/rotate.captcha.php`中的`lang`
 - 如果需要默认提供之外的语言包，在根目录下的`lang/vendor/rotate.captcha`，参考语言包添加语言

## 服务对象 (Server)

具体请查看文档：[服务对象](https://github.com/cgfeel/laravel-rotate-captcha/blob/main/docs/server.md)

## 单元测试 (PHPUnit)

在根目录`phpunit.xml`中添加一组测试，如下：

```
        <testsuite name="levi/laravel-rotate-captcha">
            <directory>./vendor/levi/laravel-rotate-captcha</directory>
        </testsuite>
```

执行`./artisan test`

## 策略 (Policie)

**由两部分组成：** `policie`默认策略，`rules`策略组规则

**策略规则：**

- `limit`: 上限次数，达到峰值后重新验证，`0`不限制
- `time`: 使用期限，过期后重新验证，`0`不限制
- `routers`: 匹配要授权的路由组，`null`全匹配

这里的路由是指验证通过后，要执行操作的路由，而不是验证操作时的路由

**原理：**

- 验证通过后将颁发：`sid`、`ticket`
- 执行操作时验证不通过不予通过，通过就去和执行的`route`进行匹配
- 从而避免跨权，跨范围执行

## 设计思路 (Design)

高级用法：

- 验证流程中`ua`实际并不局限使用`User-Agent`，可以通过自定义头部加密拼接增加安全系数
- 除了头部，包括图片路径，都可以在本地通过二次加密`encryption`的方式增加安全系数

![New Board](https://github.com/cgfeel/laravel-rotate-captcha/assets/578141/27e82f87-0937-4e23-9e08-395fd9f0adda)

## 更新日志 (Changelog)

具体请查看文档：[更新日志](https://github.com/cgfeel/laravel-rotate-captcha/blob/main/docs/changelog.md)

## 物料 (Material)

即时设计的向量稿件，包含组件设计规范：[查看](https://js.design/community?category=detail&type=resource&id=6561674f12aadf8dee1b33c2)

![911700882740_ pic](https://github.com/cgfeel/laravel-rotate-captcha/assets/578141/ea1532fa-17e1-4d08-b005-5089f705388c)

## 相关产品 (Product)

### react前端组件

整理中，待开放...

### isszz/rotate-captcha

第三方仓库，提供了react和laravel之外的生态扩展 [[点击打开](https://github.com/ahsankhatri/wordpress-auth-driver-laravel/tree/master)]

包含：

- 前端：vue、uni-app
- 后端：php、ThinkPHP

