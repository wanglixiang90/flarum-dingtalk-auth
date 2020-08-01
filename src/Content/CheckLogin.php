<?php

namespace Dingtalk\Auth\Content;

use Dingtalk\Auth\Flarum\Forum\Auth\NResponseFactory;
use Flarum\Frontend\Document;
use Flarum\Settings\SettingsRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class CheckLogin
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
     * @param  SettingsRepositoryInterface  $settings
     * @param  NResponseFactory  $response
     */
    public function __construct(SettingsRepositoryInterface $settings,NResponseFactory $response)
    {
        $this->response = $response;
        $this->settings = $settings;
    }

    public function __invoke(Document $document, ServerRequestInterface $request)
    {
        $actor = $request->getAttribute('actor');

        if (empty($actor->id)) {
            $corp_id = $this->settings->get('dingtalk-auth.corp_id');
            $document->foot[] = <<<DINGTALK
                <script src="https://g.alicdn.com/dingding/dingtalk-jsapi/2.10.3/dingtalk.open.js"></script>
                <script>
                    let platform = dd.env.platform;
                    if (platform === 'notInDingTalk'){
                        alert('请在钉钉中打开');
                    }else{
                        dd.ready(function () {
                            dd.runtime.permission.requestAuthCode({
                                corpId: "{$corp_id}",
                                onSuccess: function (result) {
                                    console.log(result.code);
                                    window.location.href="/dingtalk/login?code=" + result.code;
                                },
                                onFail: function (err) {
                                    console.log(err);
                                    alert(JSON.stringify(err))
                                }
                            });
                        });
                    }
                </script>
            DINGTALK;
        }
    }
}
