<?php

namespace Levi\LaravelRotateCaptcha\Tests;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Levi\LaravelRotateCaptcha\Captcha;
use Levi\LaravelRotateCaptcha\CaptchaMiddleware;
use Levi\LaravelRotateCaptcha\Support\Store;
use Tests\TestCase;

class CaptchaTest extends TestCase
{
    public static function createProvider()
    {
        return [
            'custom ua'  => ['192.168.1.2', 'laravel test ua'],
            'chrome osx' => ['192.168.31.212', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36'],
        ];
    }

    /**
     * @dataProvider createProvider
     */
    public function testCreate($ip, $ua)
    {
        (new Captcha)->create($ua, $ip);
        $this->assertTrue(true);
    }

    /**
     * @dataProvider createProvider
     */
    public function testGet($ip, $ua)
    {
        $info = (new Captcha)->create($ua, $ip)->get();
        $this->assertEquals(2, count($info));
    }

    /**
     * @dataProvider createProvider
     */
    public function testOutput($ip, $ua)
    {
        $captcha = (new Captcha)->create($ua, $ip);

        $info   = $captcha->get();
        [$mime] = $captcha->output($info['str']);

        $this->assertEquals('image/webp', $mime);
    }

    /**
     * @dataProvider createProvider
     */
    public function testVerify($ip, $ua)
    {
        $captcha = (new Captcha)->create($ua, $ip);
        $token = $captcha->get()['token'] ?? '';

        $this->assertNotEquals('', $token);

        $info = (new Store)->get($token, false);
        $ds   = $this->_getEarea($info['ds']);

        $url     = route('rotate.captcha.verify', ['angle' => $ds]);
        $request = $this->_getRequest($url, $ip, $ua, $token);

        $this->_setRoute($url, $request, $ds);

        $middleware = new CaptchaMiddleware;
        $middleware->handle($request, function (Request $req) {
            $this->assertEquals(true, $req->attributes->get('success', false));
        });
    }

    /**
     * @dataProvider createProvider
     */
    public function testTicket($ip, $ua)
    {
        $captcha = (new Captcha)->create($ua, $ip);
        $token = $captcha->get()['token'] ?? '';

        $this->assertNotEquals('', $token);

        $info  = (new Store)->get($token, false);
        $angle = $this->_getEarea($info['ds']);

        $params  = [
            'ds'    => $angle,
            'ip'    => $ip,
            'token' => $token,
            'ttl'   => now()->timestamp,
            'ua'    => crc32($ua),
        ];

        $data = $captcha->replaceTicket($token, '', $params);
        $this->assertEquals(2, count($data));

        $success = $captcha->verifyTicket($data['sid'], $data['ticket']);
        $this->assertEquals(true, $success);
    }

    /**
     * 随机角度，第二个参数true返回正确角度，否则错误角度
     */
    private function _getEarea(int $ds, bool $expect = true): int|float
    {
        $earea = max((int)config('rotate.captcha.earea'), 0);
        $num = $earea * 100 / 2;

        return $ds + mt_rand(-$num, $num) / 100 + ($expect ? 0 : 1);
    }

    private function _getRequest(string $url, string $ip, string $ua, string $token): Request
    {
        $request = Request::create(
            $url, 'GET', server: ['REMOTE_ADDR' => $ip]
        );

        $request->headers->set('User-Agent', $ua);
        $request->headers->set('X-Captchatoken', $token);

        return $request;
    }

    private function _setRoute(string $url, Request $request, string $ds)
    {
        $route = new Route('GET', $url, []);

        $route->name('rotate.captcha.verify');
        $route->bind($request);

        $route->setParameter('angle', (string)$ds);
        $request->setRouteResolver(fn () => $route);
    }
}
