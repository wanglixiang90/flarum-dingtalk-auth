<?php
/*
 * This file is part of Flarum.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Dingtalk\Auth\Flarum\Forum\Auth;

use Flarum\Forum\Auth\Registration;
use Flarum\Http\Rememberer;
use Flarum\User\LoginProvider;
use Flarum\User\RegistrationToken;
use Flarum\User\User;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * Class NResponseFactory - temporary fix
 *
 * @package Dingtalk\Auth\Flarum\Forum\Auth
 */
class NResponseFactory
{
    /**
     * @var Rememberer
     */
    protected $rememberer;

    /**
     * @param  Rememberer  $rememberer
     */
    public function __construct(Rememberer $rememberer)
    {
        $this->rememberer = $rememberer;
    }

    public function make(string $provider, string $identifier, callable $configureRegistration): ResponseInterface
    {
        $configureRegistration($registration = new Registration);
        $provided = $registration->getPayload();
        if ($user = LoginProvider::logIn($provider, $identifier)) {
            $user->avatar_url = $provided['avatar'];
            $user->save();
            return $this->makeLoggedInResponse($user);
        }

        // 检查用户
        $user = User::query()->where(array_only($provided, 'email'))->first();
        if (empty($user)) {
            $user = User::register($provided['name'], $provided['email'], $identifier);
            $user->avatar_url = $provided['avatar'];
            $user->is_email_confirmed = 1;
            $user->save();
        }

        if ($user) {
            $user->loginProviders()->create(compact('provider', 'identifier'));
            return $this->makeLoggedInResponse($user);
        }

        $token = RegistrationToken::generate($provider, $identifier, $provided, $registration->getPayload());
        $token->save();

        return $this->makeResponse(array_merge($provided, $registration->getSuggested(), [
                'token'    => $token->token,
                'provided' => array_keys($provided),
            ]));
    }

    private function makeResponse(array $payload): HtmlResponse
    {
        $content = sprintf('<script>window.location.href="/";window.opener.app.authenticationComplete(%s);</script>', json_encode($payload));

        return new HtmlResponse($content);
    }

    private function makeLoggedInResponse(User $user)
    {
        $response = $this->makeResponse(['loggedIn' => true]);

        return $this->rememberer->rememberUser($response, $user->id);
    }
}
