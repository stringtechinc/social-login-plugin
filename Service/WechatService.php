<?php

namespace Plugin\SnsLogin\Service;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Util\Str;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;
use Guzzle\Log\MessageFormatter;
use Guzzle\Log\PsrLogAdapter;
use Guzzle\Plugin\Log\LogPlugin;
use Plugin\SnsLogin\Entity\SnsLoginConfig;

class WechatService
{
    /** @var \Eccube\Application */
    public $app;

    const SCOPE = 'snsapi_login';

    const RESPONSE_TYPE = 'code';

    const GRANT_TYPE = 'authorization_code';

    const QR_URI = 'https://open.weixin.qq.com/connect/qrconnect?';

    const TOKEN_URI = 'https://api.weixin.qq.com/sns/oauth2/access_token?';

    const USER_URI = 'https://api.weixin.qq.com/sns/userinfo?';

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * qrconnect
     */
    public function qrconnect(SnsLoginConfig $Config)
    {
        $redirectUrl = urlencode('https://dev.haoweidao.jp/mypage/login/wechat');
//        $redirectUrl = 'https%3A%2F%2Fdev.haoweidao.jp%2Flogin%2Fwechat';

        $state = $this->makeRandStr(49);
        $qrconnectUrl = self::QR_URI
            . 'appid=' . $Config->getPublicKey()
            . '&'
            . 'redirect_uri=' . $redirectUrl
            . '&'
            . 'response_type=' . self::RESPONSE_TYPE
            . '&'
            . 'scope=' . self::SCOPE
            . '&'
            . 'state=' .$state
            . '#wechat_redirect';
        return $qrconnectUrl;
    }

    /**
     * oauth2
     */
    public function oauth2($Code, SnsLoginConfig $Config)
    {
        log_info('////////////////wechat service start ////////////////');
        $result = array();
        $result['code'] = $Code; //step1 qrconnect

        $tokenUrl = self::TOKEN_URI
            . 'appid='. $Config->getPublicKey()
            . '&'
            . 'secret=' . $Config->getSecretKey()
            . '&'
            . 'code='. $Code
            . '&'
            . 'grant_type=' . self::GRANT_TYPE;
        log_info('////////////////get access token ////////////////');
        $result = array_merge($result, $this->WechatApi($tokenUrl));

        log_info('////////////////get user info ////////////////');
        $userUrl = self::USER_URI
            . 'access_token=' . $result['access_token']
            . '&'
            . 'openid=' . $result['openid'];;
        $result = array_merge($result, $this->WechatApi($userUrl));
        log_info('/////////////// wechat service end ////////////////');
        return $result;
    }

    private function WechatApi($url)
    {
        $client = new Client();
        $request = $client->get($url);
        $response = $request->send();
        $array = (array) json_decode($response->getBody());
        return $array;
    }

    private function makeRandStr($length = 6) {
        static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; ++$i) {
            $str .= $chars[mt_rand(0, 61)];
        }
        return $str;
    }
}
