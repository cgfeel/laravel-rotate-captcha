<?php

namespace Levi\LaravelRotateCaptcha;

use App\Http\Controllers\Controller;

class CaptchaController extends Controller
{
    public function __construct()
    {
        $middleware = (array)config('rotate.captcha.middleware', []);
        $middleware !== [] && $this->middleware($middleware);
    }

    /**
     * 生成验证码图片和相关信息
     */
    public function create()
    {
        $ua = request()->header('User-Agent');
        $ip = request()->ip();

        $meta   = $this->_getMetadata();
        $size   = $meta['size'] ?? 350;
        $output = $meta['output'] ?? null;

        try
        {
            $info = app('rotate.captcha')->create($ua, $ip, $size, $output)->get();
            $msg  = $info === [] ? Captcha::lang('Failed to create image.') : 'success';
        }
        catch (CaptchaException $e)
        {
            $info = [];
            $msg  = $e->getMessage();
        }

        $success = $info !== [];
        $data = [
            'code' => $success ? 0 : 1,
            'data' => ['str' => $info['str'] ?? ''],
            'msg' => $msg,
        ];

        return response()->json($data, 200, !$success ? [] : [
            'X-Captchatoken' => $info['token'] ?? '',
        ]);
    }

    /**
     * 通过前端传递str字段给后端叫唤图片显示到前端
     */
    public function get(string $id)
    {
        [$mime, $image] = app('rotate.captcha')->output($id);
        return $mime === null ? response('', 404) : response($image, 200, [
            'Cache-Control'  => 'private, no-cache, no-store, must-revalidate',
            'Content-Length' => strlen($image),
            'Content-Type'   => $mime,
        ]);
    }

    public function test()
    {
        $sid    = request()->header('X-Captchasid');
        $ticket = request()->header('X-Captchaticket');

        if (null === $sid || null === $ticket)
        {
            $res = ['code' => 1, 'msg' => Captcha::lang('Lack of necessary information.')];
            return response()->json($res, 403);
        }

        if (!app('rotate.captcha')->verifyTicket($sid, $ticket))
        {
            $res = ['code' => 1, 'msg' => Captcha::lang('Invalid verification.')];
            return response()->json($res, 403);
        }

        return response()->json(['code' => 0, 'msg' => 'success']);
    }

    public function verify(string $angle)
    {
        $success = request()->attributes->get('success', false);
        $data    = [];

        if ($success)
        {
            $token   = request()->attributes->get('token', '');
            $policie = request()->header('X-Captchapolicie', '');

            $params  = [
                'ds'    => $angle,
                'ip'    => request()->ip(),
                'token' => $token,
                'ttl'   => now()->timestamp,
                'ua'    => crc32(request()->header('User-Agent')),
            ];

            $data = app('rotate.captcha')->replaceTicket($token, $policie, $params);
        }

        return response()->json([
            'code' => $success ? 0 : 1,
            'data' => $data,
            'msg'  => $success ? 'success' : Captcha::lang('Invalid verification.'),
        ]);
    }

    private function _getMetadata()
    {
        $data = request()->all() ?: request()->getContent() ?: [];
        return is_string($data) ? json_decode($data, true) : $data;
    }
}
