<?php

namespace Levi\LaravelRotateCaptcha;

use Closure;
use Illuminate\Http\Request;
use Levi\LaravelRotateCaptcha\Support\Store;

class CaptchaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->_canVisit($request))
        {
            return response()->json(['code' => 1, 'msg' => 'Not provided'], 404);
        }

        $method = strtolower($request->method());
        if ($method === 'options') return response();

        try
        {
            if ($request->route()->getName() === 'rotate.captcha.verify')
            {
                $this->_captchaVerify($request);
            }

            return $next($request);
        }
        catch (CaptchaException $e)
        {
            $code     = $e->getCode() === 2 ? 2 : 1;
            $response = response()->json(['code' => $code, 'msg' => $e->getMessage()]);

            return $response;
        }
    }

    private function _canVisit(Request $request): bool
    {
        $name  = $request->route()->getName();
        $route = config('rotate.captcha.route');

        if (!array_key_exists($name, $route)) return true;

        $env = config('app.env');
        return $route[$name] !== null && $route[$name] === $env;
    }

    private function _captchaVerify(Request $request)
    {
        $angle = $request->route('angle');
        $token = $request->route('token', '');

        $captchatoken = $request->header('X-Captchatoken', $token);

        $ua = $request->header('User-Agent', '');
        $ip = $request->ip() ?? '';

        if (!$this->_check($captchatoken, $ua, $ip, $angle))
        {
            throw new CaptchaException(Captcha::lang('Lack of necessary information.'), 2);
        }

        $request->attributes->add([
            'success' => true,
            'token'   => $captchatoken,
        ]);
    }

    private function _check(
        string $token, string $ua, string $ip, int|float|string|null $angle = null): bool
    {
        if (empty($token) || empty($angle)) return false;
        if ([] === ($payload = (new Store)->get($token))) return false;

        if (now()->timestamp > ($payload['ttl'] ?? 0))
        {
            throw new CaptchaException(Captcha::lang('Verification timed out.'), 2);
        }

        if ($ip !== ($payload['ip'] ?? null))
        {
            throw new CaptchaException(Captcha::lang('Invalid verification.'));
        }

        if (crc32($ua) !== ($payload['ua'] ?? null))
        {
            throw new CaptchaException(Captcha::lang('Invalid verification.'));
        }

        $ds = ((int)$payload['ds'] ?? 0) * -1;
        if (-30 < $ds || -270 > $ds)
        {
            throw new CaptchaException(Captcha::lang('Validation error.'));
        }

        // 误差最小是0
        $earea = max((int)config('rotate.captcha.earea'), 0);
        $angle = (float)$angle;

        $diff = abs($angle + $ds);
        if ($diff < 0 || $diff > $earea / 2)
        {
            throw new CaptchaException(Captcha::lang('Invalid verification.'));
        }

        return true;
    }
}
