<?php

/*
 * This file is part of dingtalk/login.
 *
 * Copyright (c) 2020 FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dingtalk\Auth;

use Dingtalk\Auth\Http\Controllers\DingtalkLoginController;
use Flarum\Extend;
use Illuminate\Contracts\View\Factory;

return [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/resources/less/admin.less'),
    new Extend\Locales(__DIR__ . '/resources/locale'),

    (new Extend\Frontend('forum'))
        ->content(Content\CheckLogin::class),

    (new Extend\Routes('forum'))
        ->get('/dingtalk/login', 'dingtalk.login', DingtalkLoginController::class),

    function (Factory $view) {
        $view->addNamespace('dingtalk', __DIR__.'/resources/views');
    }
];
