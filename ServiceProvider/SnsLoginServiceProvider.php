<?php

/*
 * This file is part of the SnsLogin
 *
 * Copyright (C) 2018 StringTech Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SnsLogin\ServiceProvider;

use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Plugin\SnsLogin\Form\Type\SnsLoginConfigType;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class SnsLoginServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        // admin
        $app->match('/'.$app['config']['admin_route'].'/plugin/SnsLogin/config', 'Plugin\SnsLogin\Controller\ConfigController::index')->bind('plugin_SnsLogin_config');

        // front
        $app->match('/mypage/login/wechat', 'Plugin\SnsLogin\Controller\SnsLoginController::wechat')->bind('wechat');
        $app->match('/mypage/login', 'Plugin\SnsLogin\Controller\SnsLoginController::login')->bind('mypage_login');

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new SnsLoginConfigType();
            return $types;
        }));

        // Repository
        $app['eccube.plugin.repository.sns_login_config'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\SnsLogin\Entity\SnsLoginConfig');
        });
        $app['eccube.plugin.repository.sns_login_customer'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\SnsLogin\Entity\SnsLoginCustomer');
        });

        // Service
        $app['eccube.plugin.service.sns_login_wechat'] = $app->share(function () use ($app) {
            return new \Plugin\SnsLogin\Service\WechatService($app);
        });

        // Config
        $app['config'] = $app->share($app->extend('config', function ($config) {
            $addNavi['id'] = 'admin_wechat_config';
            $addNavi['name'] = 'Wechat oauth2';
            $addNavi['url'] = 'plugin_SnsLogin_config';
            $nav = $config['nav'];
            foreach ($nav as $key => $val) {
                if ('setting' == $val['id']) {
                    $nav[$key]['child'][] = $addNavi;
                }
            }
            $config['nav'] = $nav;

            return $config;
        }));

        // ログファイル設定
        $app['monolog.logger.snslogin'] = $app->share(function ($app) {

            $logger = new $app['monolog.logger.class']('snslogin');

            $filename = $app['config']['root_dir'].'/app/log/snslogin.log';
            $RotateHandler = new RotatingFileHandler($filename, $app['config']['log']['max_files'], Logger::INFO);
            $RotateHandler->setFilenameFormat(
                'snslogin_{date}',
                'Y-m-d'
            );

            $logger->pushHandler(
                new FingersCrossedHandler(
                    $RotateHandler,
                    new ErrorLevelActivationStrategy(Logger::ERROR),
                    0,
                    true,
                    true,
                    Logger::INFO
                )
            );

            return $logger;
        });

    }

    public function boot(BaseApplication $app)
    {
    }
}
