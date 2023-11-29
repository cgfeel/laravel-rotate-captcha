<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 缓存模式
    |--------------------------------------------------------------------------
    |
    | 默认为空，按照`cache.default`的配置，填写当前设置
    | 将覆盖默认配置，可用缓存配置将通过`cache.stores`进
    | 行配置
    |
    */

    'cache' => '',

    /*
    |--------------------------------------------------------------------------
    | 文件系统
    |--------------------------------------------------------------------------
    |
    | 默认为空，按照`filesystem.default`中默认的配置，填
    | 写当前设置将覆盖默认配置，可用存储配置将通过
    | `filesystem.disks`进行配置
    |
    */

    'disk' => '',

    /*
    |--------------------------------------------------------------------------
    | 容错率
    |--------------------------------------------------------------------------
    |
    | 举例：验证图片旋转40°，容错率10，那么验证通过的范围就是
    | 35-45，容错率越高安全级别越低，请结合试错率一起看
    |
    */

    'earea' => 10,

    /*
    |--------------------------------------------------------------------------
    | 生成验证有效期
    |--------------------------------------------------------------------------
    |
    | 以秒为单位计算，举例：300秒即获取验证信息到提交验证5分钟内
    | 都有效，超时需要重新验证
    |
    */

    'expire' => 300,

    /*
    |--------------------------------------------------------------------------
    | 验证图片处理方式
    |--------------------------------------------------------------------------
    |
    | 支持gd和imagick
    | https://medium.com/@vlreshet/the-difference-between-php-imagick-and-php-gd-19b84dc064b4
    |
    */

    'handle' => 'gd',

    /*
    |--------------------------------------------------------------------------
    | 验证图片压缩率和背景色
    |--------------------------------------------------------------------------
    |
    | `quality`的值是0-100，使用GD来处理图片时`bgcolor`为
    | `hex`类型，使用imagick处理图片时，可以是`rgb`也可以是
    | `hex`
    |
    */

    'img' => [
        'quality' => 80,
        'bgcolor' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | 语言包
    |--------------------------------------------------------------------------
    |
    | 默认为空，按照`app.locale`的配置，默认只包含`zh_CN`和`en`
    | 两个语言包，若需要额外语言包，请参考
    | `laravel-rotate-captcha/lang`下的语言文件，在项目的
    | `lang`目录添加相应文件
    |
    */

    'lang' => '',

    /*
    |--------------------------------------------------------------------------
    | 试错次数
    |--------------------------------------------------------------------------
    |
    | 举例：假定容错率10，目前提供验证的范围是：270 - 30 = 240
    | ，那么最大命中次数：240 / 10 = 24；假定试错数是：2，那么
    | 命中率就是：1 / (24 / 2) = 1/12；依次类推，试错数是：3，
    | 命中率就是：1/8；试错数是：4，命中率就是：1/6；试错率越
    | 大，命中几率就越大，安全级别就越低。
    | 注意：limit必须和客户端设置保持一致
    |
    */

    'limit' => 2,

    /*
    |--------------------------------------------------------------------------
    | 中间件
    |--------------------------------------------------------------------------
    |
    | 默认的路由对应的控制器下使用的中间件，如需要添加中间件可添加
    | 数组中中间件的名称，将按照数组元素顺序执行
    |
    */

    'middleware' => ['rotate.captcha'],

    /*
    |--------------------------------------------------------------------------
    | 验证图片格式
    |--------------------------------------------------------------------------
    |
    | 验证图片转码后保存的格式，仅支持：`jpg`、`png`、`webp`，推
    | 荐使用`webp`
    |
    */

    'outputType' => 'webp',

    /*
    |--------------------------------------------------------------------------
    | 默认使用的策略
    |--------------------------------------------------------------------------
    |
    | 当鉴权验证时没有提供策略时，默认提供的策略
    |
    */

    'policie' => 'default',

    /*
    |--------------------------------------------------------------------------
    | 鉴权路由
    |--------------------------------------------------------------------------
    |
    | 提供5组路由，见：
    | - `levi/laravel-rotate-captcha/routes/web.php`
    | 规则：
    | - 不写部署在所有环境
    | - 指定环境只在特定环境提供路由
    | - 设置为`null`所有环境都屏蔽
    | - 以上规则必须开启中间件`rotate.captcha`
    |
    */

    'route'  => [
        'rotate.captcha.test' => 'local'
    ],

    /*
    |--------------------------------------------------------------------------
    | 策略规则
    |--------------------------------------------------------------------------
    |
    | 规则说明：
    | - num: 鉴权成功后使用次数，上限后需重新验证，小于0将不可使用，
    | 等于0将不限制使用
    | - time: 鉴权成功后有效期，超时后需重新验证，以秒为单位，小于0
    | 将不可以使用，等于0将不限期使用
    | - routers: 可用的路由范围，在权限可用下，只有在范围内的路由名
    | 才能使用，设置null将不对路由做限制
    |
    */

    'rules' => [
        'default' => [
            'num' => 0,
            'time' => 1800,
            'routers' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 验证图大小
    |--------------------------------------------------------------------------
    |
    | 验证图片转码后的直径
    |
    */

    'size' => 350,

    /*
    |--------------------------------------------------------------------------
    | 验证图存放目录
    |--------------------------------------------------------------------------
    |
    | 目录下将包含两个有效子目录
    | - origin: 原始图片
    | - transform: 转换角度后的验证图片
    |
    */

    'storePath' => 'rotate.captcha',
];
