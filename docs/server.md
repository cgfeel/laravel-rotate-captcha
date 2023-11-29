# 服务对象和方法

## rotate.captcha

验证信息、图片生成、校验、鉴权

**参数:** 无

**返回:**

- `Captcha`对象

以下是服务对象提供的方法

#### create

**参数:**

- `$ua`：`string`，User-Agent，高级用法见设计思路
- `$ip`：`string`，请求的IP
- `$size`：`int`，生成验证图片尺寸
- `$output`：`string|null`，图片生成格式

**返回:**

- `Captcha`对象

#### get

获取验证信息

**返回:**

- `['str' => '图片路径', 'token' => '口令']`

#### output

路径换图片

**参数:**

- `$str`：`string`，图片路径

**返回:**

- `['mime', 'image content']`

#### replaceTicket

`token`换`ticket`

**参数:**

- `$token`：`string`，之前`get`时拿到的`token`
- `$policie`：`string`，策略
- `$params`：`array`，生成ticket自定义的信息

**返回:**

- `['sid' => '编码', 'ticket' => '票据']`

#### verifyTicket

票据校验

**参数:**

- `$sid`：`string`，编码
- `$ticket`：`string`，票据
- `$route_name`：`string`，当前路由名

---

## rotate.captcha.file

文件存储管理

**参数:** 

- `['path' => 'origin|transform']`

不提供，默认为`transform`

**返回:**

- `File`对象

#### clear

清理过期验证图片

**参数:**

- `$time`：`int`，过期时间
- `$ext`：`boolean`，匹配规定格式的图片文件

**返回:**

- `File`对象

#### cost

获取目录下文件总数

**参数:**

- `$ext`：`boolean`，匹配规定格式的图片文件

**返回:**

- `number`

#### list

获取目录下所有文件

**参数:**

- `$ext`：`boolean`，匹配规定格式的图片文件

**返回:**

- `[{file_path}...]`

#### prepend

添加文件

**参数:**

- `$name`：`string`，文件名
- `$context`：`string`，文件内容

**返回:**

- `boolean`

#### rand

从目录中随机获取一个或一组文件

**参数:**

- `$limit`：`int`，获取数量，默认为1

**返回:**

- `string|array`

---

## rotate.captcha.store

缓存管理

**参数:** 无

**返回:**

- `Store`对象

#### forget

删除缓存

**参数:**

- `$token`：`string`，口令
- `$ticket`：`boolean`，默认`false`删除验证信息，否则删除票据信息

**返回:**

- `Store`对象


#### get

获取验证信息

**参数:**

- `$token`：`string`，口令
- `$consume`：`boolean`，默认`true`每次查询都消耗一次，直至上限

**返回:**

- `array`验证信息

#### put

添加验证信息

**参数:**

- `$info`：`array`，验证信息
- `$limit`：`int`，上限次数，默认为2
- `$ttl`：`int`，过期时间，默认30秒

**返回:**

- `string`返回`token`

#### ticket

添加、查询票据信息

**参数:**

- `$sid`：`string`，编号
- `$policie`：`string|null`，策略，默认`null`作为查询，否则将添加票据
- `$used`：`int`，使用次数，默认0
- `$expire`：`int`，过期时间单位秒，默认30秒

**返回:**

- `array`返回票据信息
