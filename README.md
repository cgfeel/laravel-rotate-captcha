![logo](https://github.com/cgfeel/laravel-rotate-captcha/assets/578141/698a686e-38b5-4a7c-b0e7-5b8c6763f4fa)

[![Latest Stable Version](http://poser.pugx.org/levi/laravel-rotate-captcha/v)](https://packagist.org/packages/levi/laravel-rotate-captcha) [![Total Downloads](http://poser.pugx.org/levi/laravel-rotate-captcha/downloads)](https://packagist.org/packages/levi/laravel-rotate-captcha) [![Latest Unstable Version](http://poser.pugx.org/levi/laravel-rotate-captcha/v/unstable)](https://packagist.org/packages/levi/laravel-rotate-captcha) [![License](http://poser.pugx.org/levi/laravel-rotate-captcha/license)](https://packagist.org/packages/levi/laravel-rotate-captcha) [![PHP Version Require](http://poser.pugx.org/levi/laravel-rotate-captcha/require/php)](https://packagist.org/packages/levi/laravel-rotate-captcha)

一个开箱即用的滑动验证码Laravel扩展，基于[[isszz/rotate-captcha](https://github.com/isszz/rotate-captcha)]做的二次开发；结合了腾讯防水墙，增加安全策略，查看：[策略](#-策略-policie) 和 [设计思路](#-设计思路-design)；提供了React前端开源组件

前端推荐使用React组件库：`cgfeel/react-rotate-captcha`，[[安装](https://github.com/cgfeel/react-rotate-captcha#-%E5%AE%89%E8%A3%85-installing)]、[[使用](https://github.com/cgfeel/react-rotate-captcha#-%E4%BD%BF%E7%94%A8-usage)]、[[接口](https://github.com/cgfeel/react-rotate-captcha#-%E6%8E%A5%E5%8F%A3-api)]，更多资源见底部相关产品

<img width="351" alt="image" src="https://github.com/cgfeel/laravel-rotate-captcha/assets/578141/0f6d4073-2811-4c5b-807d-a95d56973848">

## 🎙️ 演示 (Demo)

以下演示全部都一样，分别展示了多主题，多语言、多个唤起方式；在不同的环境下的演示，可根据自己的情况选择一个环境查看演示和演示的代码

- CodeSondbox：Webpack+TS+React [[查看](https://codesandbox.io/p/devbox/tske-yong-v5-d5kgjr?layout=%257B%2522sidebarPanel%2522%253A%2522EXPLORER%2522%252C%2522rootPanelGroup%2522%253A%257B%2522direction%2522%253A%2522horizontal%2522%252C%2522contentType%2522%253A%2522UNKNOWN%2522%252C%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522id%2522%253A%2522ROOT_LAYOUT%2522%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522contentType%2522%253A%2522UNKNOWN%2522%252C%2522direction%2522%253A%2522vertical%2522%252C%2522id%2522%253A%2522clpqci2l100073b6lcf1o65qk%2522%252C%2522sizes%2522%253A%255B70%252C30%255D%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522contentType%2522%253A%2522EDITOR%2522%252C%2522direction%2522%253A%2522horizontal%2522%252C%2522id%2522%253A%2522EDITOR%2522%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL%2522%252C%2522contentType%2522%253A%2522EDITOR%2522%252C%2522id%2522%253A%2522clpqci2l000023b6lh9c8vv90%2522%257D%255D%257D%252C%257B%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522contentType%2522%253A%2522SHELLS%2522%252C%2522direction%2522%253A%2522horizontal%2522%252C%2522id%2522%253A%2522SHELLS%2522%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL%2522%252C%2522contentType%2522%253A%2522SHELLS%2522%252C%2522id%2522%253A%2522clpqci2l000043b6ld2blf0sx%2522%257D%255D%252C%2522sizes%2522%253A%255B100%255D%257D%255D%257D%252C%257B%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522contentType%2522%253A%2522DEVTOOLS%2522%252C%2522direction%2522%253A%2522vertical%2522%252C%2522id%2522%253A%2522DEVTOOLS%2522%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL%2522%252C%2522contentType%2522%253A%2522DEVTOOLS%2522%252C%2522id%2522%253A%2522clpqci2l000063b6lewuwosa5%2522%257D%255D%252C%2522sizes%2522%253A%255B100%255D%257D%255D%252C%2522sizes%2522%253A%255B60%252C40%255D%257D%252C%2522tabbedPanels%2522%253A%257B%2522clpqci2l000023b6lh9c8vv90%2522%253A%257B%2522id%2522%253A%2522clpqci2l000023b6lh9c8vv90%2522%252C%2522tabs%2522%253A%255B%255D%257D%252C%2522clpqci2l000063b6lewuwosa5%2522%253A%257B%2522id%2522%253A%2522clpqci2l000063b6lewuwosa5%2522%252C%2522tabs%2522%253A%255B%257B%2522id%2522%253A%2522clpqci2l000053b6lwhnjyn5s%2522%252C%2522mode%2522%253A%2522permanent%2522%252C%2522type%2522%253A%2522TASK_PORT%2522%252C%2522taskId%2522%253A%2522start%2522%252C%2522port%2522%253A3000%252C%2522path%2522%253A%2522%252Fzh-CN%2522%257D%255D%252C%2522activeTabId%2522%253A%2522clpqci2l000053b6lwhnjyn5s%2522%257D%252C%2522clpqci2l000043b6ld2blf0sx%2522%253A%257B%2522id%2522%253A%2522clpqci2l000043b6ld2blf0sx%2522%252C%2522tabs%2522%253A%255B%257B%2522id%2522%253A%2522clpqci2l000033b6l2vrwme3j%2522%252C%2522mode%2522%253A%2522permanent%2522%252C%2522type%2522%253A%2522TASK_LOG%2522%252C%2522taskId%2522%253A%2522start%2522%257D%255D%252C%2522activeTabId%2522%253A%2522clpqci2l000033b6l2vrwme3j%2522%257D%257D%252C%2522showDevtools%2522%253Atrue%252C%2522showShells%2522%253Atrue%252C%2522showSidebar%2522%253Atrue%252C%2522sidebarPanelSize%2522%253A15%257D)]
- CodeSondbox：Webpack+JS+React [[查看](https://codesandbox.io/p/devbox/react-rotate-captcha-js-react-webpack-ngm77w?layout=%257B%2522sidebarPanel%2522%253A%2522EXPLORER%2522%252C%2522rootPanelGroup%2522%253A%257B%2522direction%2522%253A%2522horizontal%2522%252C%2522contentType%2522%253A%2522UNKNOWN%2522%252C%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522id%2522%253A%2522ROOT_LAYOUT%2522%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522contentType%2522%253A%2522UNKNOWN%2522%252C%2522direction%2522%253A%2522vertical%2522%252C%2522id%2522%253A%2522clpp92lgn00083b6lcztfa1s7%2522%252C%2522sizes%2522%253A%255B70%252C30%255D%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522contentType%2522%253A%2522EDITOR%2522%252C%2522direction%2522%253A%2522horizontal%2522%252C%2522id%2522%253A%2522EDITOR%2522%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL%2522%252C%2522contentType%2522%253A%2522EDITOR%2522%252C%2522id%2522%253A%2522clpp92lgm00023b6lox4wiual%2522%257D%255D%257D%252C%257B%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522contentType%2522%253A%2522SHELLS%2522%252C%2522direction%2522%253A%2522horizontal%2522%252C%2522id%2522%253A%2522SHELLS%2522%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL%2522%252C%2522contentType%2522%253A%2522SHELLS%2522%252C%2522id%2522%253A%2522clpp92lgm00053b6lk2sv6sks%2522%257D%255D%252C%2522sizes%2522%253A%255B100%255D%257D%255D%257D%252C%257B%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522contentType%2522%253A%2522DEVTOOLS%2522%252C%2522direction%2522%253A%2522vertical%2522%252C%2522id%2522%253A%2522DEVTOOLS%2522%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL%2522%252C%2522contentType%2522%253A%2522DEVTOOLS%2522%252C%2522id%2522%253A%2522clpp92lgn00073b6lcb020nkl%2522%257D%255D%252C%2522sizes%2522%253A%255B100%255D%257D%255D%252C%2522sizes%2522%253A%255B50%252C50%255D%257D%252C%2522tabbedPanels%2522%253A%257B%2522clpp92lgm00023b6lox4wiual%2522%253A%257B%2522id%2522%253A%2522clpp92lgm00023b6lox4wiual%2522%252C%2522tabs%2522%253A%255B%255D%257D%252C%2522clpp92lgn00073b6lcb020nkl%2522%253A%257B%2522id%2522%253A%2522clpp92lgn00073b6lcb020nkl%2522%252C%2522tabs%2522%253A%255B%257B%2522id%2522%253A%2522clpp92lgn00063b6lt7dh3gg9%2522%252C%2522mode%2522%253A%2522permanent%2522%252C%2522type%2522%253A%2522TASK_PORT%2522%252C%2522taskId%2522%253A%2522start%2522%252C%2522port%2522%253A3000%252C%2522path%2522%253A%2522%252Fen-US%2522%257D%255D%252C%2522activeTabId%2522%253A%2522clpp92lgn00063b6lt7dh3gg9%2522%257D%252C%2522clpp92lgm00053b6lk2sv6sks%2522%253A%257B%2522id%2522%253A%2522clpp92lgm00053b6lk2sv6sks%2522%252C%2522tabs%2522%253A%255B%257B%2522id%2522%253A%2522clpp92lgm00033b6l4gn4biw7%2522%252C%2522mode%2522%253A%2522permanent%2522%252C%2522type%2522%253A%2522TASK_LOG%2522%252C%2522taskId%2522%253A%2522start%2522%257D%252C%257B%2522id%2522%253A%2522clpp92lgm00043b6lprj2oc6z%2522%252C%2522mode%2522%253A%2522permanent%2522%252C%2522type%2522%253A%2522TASK_LOG%2522%252C%2522taskId%2522%253A%2522build%2522%257D%252C%257B%2522type%2522%253A%2522TASK_LOG%2522%252C%2522taskId%2522%253A%2522yarn%2520add%2520react-rotate-captcha%2540latest%2522%252C%2522id%2522%253A%2522clpt7p4yv005j3b6ld1k4bd3z%2522%252C%2522mode%2522%253A%2522permanent%2522%257D%255D%252C%2522activeTabId%2522%253A%2522clpp92lgm00033b6l4gn4biw7%2522%257D%257D%252C%2522showDevtools%2522%253Atrue%252C%2522showShells%2522%253Atrue%252C%2522showSidebar%2522%253Atrue%252C%2522sidebarPanelSize%2522%253A15%257D)]
- CodeSondbox：Vite+TS+React [[查看](https://codesandbox.io/p/devbox/react-rotate-captcha-ts-react-vite-t23lcq?layout=%257B%2522sidebarPanel%2522%253A%2522EXPLORER%2522%252C%2522rootPanelGroup%2522%253A%257B%2522direction%2522%253A%2522horizontal%2522%252C%2522contentType%2522%253A%2522UNKNOWN%2522%252C%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522id%2522%253A%2522ROOT_LAYOUT%2522%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522contentType%2522%253A%2522UNKNOWN%2522%252C%2522direction%2522%253A%2522vertical%2522%252C%2522id%2522%253A%2522clpqf4taw00073b6lf9ixqjs6%2522%252C%2522sizes%2522%253A%255B70%252C30%255D%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522contentType%2522%253A%2522EDITOR%2522%252C%2522direction%2522%253A%2522horizontal%2522%252C%2522id%2522%253A%2522EDITOR%2522%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL%2522%252C%2522contentType%2522%253A%2522EDITOR%2522%252C%2522id%2522%253A%2522clpqf4tav00023b6l8tmq733p%2522%257D%255D%257D%252C%257B%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522contentType%2522%253A%2522SHELLS%2522%252C%2522direction%2522%253A%2522horizontal%2522%252C%2522id%2522%253A%2522SHELLS%2522%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL%2522%252C%2522contentType%2522%253A%2522SHELLS%2522%252C%2522id%2522%253A%2522clpqf4taw00043b6lpn1xeejf%2522%257D%255D%252C%2522sizes%2522%253A%255B100%255D%257D%255D%257D%252C%257B%2522type%2522%253A%2522PANEL_GROUP%2522%252C%2522contentType%2522%253A%2522DEVTOOLS%2522%252C%2522direction%2522%253A%2522vertical%2522%252C%2522id%2522%253A%2522DEVTOOLS%2522%252C%2522panels%2522%253A%255B%257B%2522type%2522%253A%2522PANEL%2522%252C%2522contentType%2522%253A%2522DEVTOOLS%2522%252C%2522id%2522%253A%2522clpqf4taw00063b6lzext9al2%2522%257D%255D%252C%2522sizes%2522%253A%255B100%255D%257D%255D%252C%2522sizes%2522%253A%255B50%252C50%255D%257D%252C%2522tabbedPanels%2522%253A%257B%2522clpqf4tav00023b6l8tmq733p%2522%253A%257B%2522id%2522%253A%2522clpqf4tav00023b6l8tmq733p%2522%252C%2522tabs%2522%253A%255B%255D%257D%252C%2522clpqf4taw00063b6lzext9al2%2522%253A%257B%2522id%2522%253A%2522clpqf4taw00063b6lzext9al2%2522%252C%2522tabs%2522%253A%255B%257B%2522id%2522%253A%2522clpqf4taw00053b6lvewjnad1%2522%252C%2522mode%2522%253A%2522permanent%2522%252C%2522type%2522%253A%2522TASK_PORT%2522%252C%2522taskId%2522%253A%2522dev%2522%252C%2522port%2522%253A5173%252C%2522path%2522%253A%2522%252Fzh-CN%2522%257D%255D%252C%2522activeTabId%2522%253A%2522clpqf4taw00053b6lvewjnad1%2522%257D%252C%2522clpqf4taw00043b6lpn1xeejf%2522%253A%257B%2522id%2522%253A%2522clpqf4taw00043b6lpn1xeejf%2522%252C%2522tabs%2522%253A%255B%257B%2522id%2522%253A%2522clpqf4taw00033b6lpioiumrk%2522%252C%2522mode%2522%253A%2522permanent%2522%252C%2522type%2522%253A%2522TASK_LOG%2522%252C%2522taskId%2522%253A%2522dev%2522%257D%255D%252C%2522activeTabId%2522%253A%2522clpqf4taw00033b6lpioiumrk%2522%257D%257D%252C%2522showDevtools%2522%253Atrue%252C%2522showShells%2522%253Atrue%252C%2522showSidebar%2522%253Atrue%252C%2522sidebarPanelSize%2522%253A15%257D)]
- Stackblitz：ts+react [[查看](https://stackblitz.com/edit/stackblitz-starters-paesfm?file=src%2FApp.tsx)]

视频演示：

https://github.com/cgfeel/laravel-rotate-captcha/assets/578141/afa169d1-05c3-43d6-b7e7-cabaa8c5dbc5

## 📦 安装 (Installation)

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

## 🎛️ 设置 (Configuration)

迁移配置文件、语言包、附件图

```
php artisan vendor:publish --provider="Levi\LaravelRotateCaptcha\CaptchaProvider"
```

添加中间件，修改`App\Http\Kernel`在`protected $middlewareAliases`中添加

```
'rotate.captcha' => \Levi\LaravelRotateCaptcha\CaptchaMiddleware::class
```

## 🔨 使用 (Usage)

### 默认开箱即用

提供了5个路由、1个中间件、1个控制器

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

客户端请求和接受的数据，见`react-rotate-captcha` [[查看](https://github.com/cgfeel/react-rotate-captcha#-%E6%8E%A5%E5%8F%A3-api)]

### 手动设置

手动设置不是必须的，仅针对有定制需求的用户

**仅修改中间件：** 增加或修改中间件处理请求和响应的数据，修改配置配置`config/rotate.captcha.php`中`middleware`项

**完全自定义：** 关闭默认提供的路由和控制器

- 配置`config/rotate.captcha.php`中的`routers`项，关闭对应的路由
- 参考文件`CaptchaController.php`和`CaptchaMiddleware.php`
- 参考：[服务对象](https://github.com/cgfeel/laravel-rotate-captcha/blob/main/docs/server.md)、[策略](#-策略-policie)、[设计思路](#-设计思路-design)

## 🏞️ 更新验证图片 (Updating)

**手动更新：** 目录位置`\storage\app\{rotate.captcha}`，其中存储引擎和位置可在配置文件中修改。

**自动更新：** 

请通过在线的图床接口，在Laravel调度`App\Console\Kernel`中`schedule`方法里添加定时抓取，这里提供一个存储的方法作为参考：

```php
// 每天0点抓取一张
$schedule->call(function () {
    $image = file_get_contents({custome_api_url});
    app('rotate.captcha.file', ['path' => 'origin'])->prepend('costome_name.jpg', $image);
})
->daily();
```

> 安全系数：风景图 > 人物图 > 卡通图片，但不建议使用`bing`每日一图作为验证图片，因为验证的图片每天都是固定的，拿来比对就能得出结果

## 🗑️ 清理过期图片 (Cleanup)

请在Laravel调度`App\Console\Kernel`中`schedule`方法里添加定期清理，以下为参考示例：

```php
// 清理前一天
$schedule->call(fn () => app('rotate.captcha.file')->clear())->daily();

// 清理1小时前
$schedule->call(fn () => app('rotate.captcha.file')->clear(3600))->hourly();

// 清理后返回剩余总数，请在诸如`Controller`或`artisan`中调用
app('rotate.captcha.file')->clear()->cost();
```

## 🕸️ 跨域 (Cors)

根据情况设置，以下仅供参考，修改`config/cors.php`：

```php
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

## 🗃️ 缓存 (Cache)

用于存储验证信息，默认按照`Laravel`缓存配置`config/cache.php`默认引擎`file`

- 建议配置`cache.php`中的默认缓存
- 如果要和默认缓存不一样，修改`config/rotate.captcha.php`中的`cache`，采用的缓存需要提前在`cache.php`配置好

## 🗄️ 文件驱动 (Disk)

用于存储验证图片，默认按照`Laravel`文件配置`config/filesystem.php`默认引擎`local`

- 建议配置`filesystem.php`中的驱动引擎
- 如果要和默认驱动不一样，修改`config/rotate.captcha.php`中的`disk`，采用的驱动需要提前在`filesystem.php`配置好

## 👩‍🎤 多语言 (Language)

提供中文和英文，默认按照`Laravel`语言配置`config/app.php`配置为`en`

 - 建议修改`app.php`中的`locale`
 - 如果要和默认语言不一样，修改`config/rotate.captcha.php`中的`lang`
 - 如果需要默认提供外的语言包，在根目录下的`lang/vendor/rotate.captcha`，参考语言包添加语言

## 🚀 服务对象 (Server)

具体请查看文档：[服务对象](https://github.com/cgfeel/laravel-rotate-captcha/blob/main/docs/server.md)

## 🧪 单元测试 (PHPUnit)

在根目录`phpunit.xml`中添加一组测试，如下：

```xml
        <testsuite name="levi/laravel-rotate-captcha">
            <directory>./vendor/levi/laravel-rotate-captcha</directory>
        </testsuite>
```

执行`./artisan test`

## 🛃 策略 (Policie)

**由两部分组成：** `policie`默认策略，`rules`策略组规则

**策略规则：**

- `limit`: 上限次数，达到峰值后重新验证，`0`不限制
- `time`: 使用期限，过期后重新验证，`0`不限制
- `routers`: 匹配要授权的路由组，`null`全匹配

这里的路由是指验证通过后，要执行操作的路由，而不是验证操作时的路由

**原理：**

- 验证通过后将颁发：`sid`、`ticket`
- 执行操作时验证决定是否通过，通过就去和执行的`route`进行匹配
- 从而避免跨权，跨范围执行
  
**优点：**

- 针对不同应用场景提供验证
- 例如登录验证有效期30分钟，后台操作按次数进行身份验证

## 🛟 设计思路 (Design)

高级用法：

- 验证流程中`ua`实际并不局限使用`User-Agent`，可以通过自定义头部加密拼接增加安全系数
- 除了头部，包括图片路径，都可以在本地通过二次加密`encryption`的方式增加安全系数

![New Board](https://github.com/cgfeel/laravel-rotate-captcha/assets/578141/27e82f87-0937-4e23-9e08-395fd9f0adda)

## ✂️ 物料 (Material)

即时设计的向量稿件，包含组件设计规范：[查看](https://js.design/community?category=detail&type=resource&id=6561674f12aadf8dee1b33c2)

![911700882740_ pic](https://github.com/cgfeel/laravel-rotate-captcha/assets/578141/ea1532fa-17e1-4d08-b005-5089f705388c)

## 🗓️ 更新日志 (Changelog)

具体请查看文档：[更新日志](https://github.com/cgfeel/laravel-rotate-captcha/blob/main/docs/changelog.md)

## 🔗 相关产品 (Product)

### react前端组件

推荐使用：`cgfeel/react-rotate-captcha`，[[安装](https://github.com/cgfeel/react-rotate-captcha#-%E5%AE%89%E8%A3%85-installing)]、[[使用](https://github.com/cgfeel/react-rotate-captcha#-%E4%BD%BF%E7%94%A8-usage)]、[[接口](https://github.com/cgfeel/react-rotate-captcha#-%E6%8E%A5%E5%8F%A3-api)]

### isszz/rotate-captcha

第三方仓库，提供了react和laravel之外的生态扩展 [[点击打开](https://github.com/isszz/rotate-captcha)]

包含：

- 前端：vue、uni-app
- 后端：php、ThinkPHP

