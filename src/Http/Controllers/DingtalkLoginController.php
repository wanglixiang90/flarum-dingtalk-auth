<?php

namespace Dingtalk\Auth\Http\Controllers;

use Dingtalk\Auth\Flarum\Forum\Auth\NResponseFactory;
use EasyDingTalk\Application;
use Exception;
use Flarum\Forum\Auth\Registration;
use Flarum\Http\UrlGenerator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DingtalkLoginController implements RequestHandlerInterface
{
    /**
     * @var NResponseFactory
     */
    protected $response;
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;
    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @param  NResponseFactory  $response
     * @param  SettingsRepositoryInterface  $settings
     * @param  UrlGenerator  $url
     */
    public function __construct(NResponseFactory $response, SettingsRepositoryInterface $settings, UrlGenerator $url)
    {
        $this->response = $response;
        $this->settings = $settings;
        $this->url      = $url;
    }

    /**
     * @param  Request  $request
     * @return ResponseInterface
     * @throws Exception
     */
    public function handle(Request $request): ResponseInterface
    {
        $code = Arr::get($request->getQueryParams(), 'code');

        if (empty($code)) {
            throw new Exception('Invalid code', 422);
        }

        $config = [
            'corp_id'    => $this->settings->get('dingtalk-auth.corp_id'),
            'app_key'    => $this->settings->get('dingtalk-auth.app_key'),
            'app_secret' => $this->settings->get('dingtalk-auth.app_secret'),
        ];

        $app  = new Application($config);
        $user = $app->user->getUserByCode($code);

        if ($user['errcode'] > 0) {
            throw new Exception($user['errmsg'] . '，请联系管理员~', 422);
        }
        $user = $app->user->get($user['userid'], $lang = null);

        if ($user['errcode'] > 0) {
            throw new Exception($user['errmsg'] . '，请联系管理员~', 422);
        }

        $user['email'] = $user['userid'] . '@halobear.com';

        return $this->response->make('dingtalk', $user['userid'], function (Registration $registration) use ($user) {
            $registration->suggestUsername($user['name'])->setPayload($user);
        });
    }
}
