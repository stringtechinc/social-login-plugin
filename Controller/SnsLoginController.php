<?php

/*
 * This file is part of the SnsLogin
 *
 * Copyright (C) 2018 StringTech Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SnsLogin\Controller;

use Eccube\Application;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;
use Guzzle\Log\MessageFormatter;
use Guzzle\Log\PsrLogAdapter;
use Guzzle\Plugin\Log\LogPlugin;
use Plugin\SnsLogin\Entity\SnsLoginCustomer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SnsLoginController
{
    /**
     * ログイン画面.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function login(Application $app, Request $request)
    {
        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $app->redirect($app->url('mypage'));
        }

        $builder = $app['form.factory']->createNamedBuilder('', 'customer_login');

        if ($app->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $Customer = $app->user();
            if ($Customer) {
                $builder->get('login_email')->setData($Customer->getEmail());
            }
        }

        $event = new EventArgs(array('builder' => $builder,), $request);

        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_MYPAGE_MYPAGE_LOGIN_INITIALIZE, $event);

        $form = $builder->getForm();

        $Config = $app['eccube.plugin.repository.sns_login_config']->find(1);
        if ($Config) {
            $Url = $app['eccube.plugin.service.sns_login_wechat']->qrconnect($Config);
        }

        return $app->render('SnsLogin/Resource/template/login.twig', array(
            'error' => $app['security.last_error']($request),
            'wechat' => $Url,
            'form' => $form->createView(),
        ));
    }

    /**
     * wechat
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function wechat(Application $app, Request $request)
    {
        if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $app->redirect($app->url('mypage'));
        }

        $Code = $request->query->get('code');
        if (!$Code) {
            return $app->redirect($app->url('mypage_login'));
        }
        $Config = $app['eccube.plugin.repository.sns_login_config']->find(1);
        if (!$Config) {
            return $app->redirect($app->url('mypage_login'));
        }

        $User = $app['eccube.plugin.service.sns_login_wechat']->oauth2($Code, $Config);
        if (empty($User)) {
            return $app->redirect($app->url('mypage_login'));
        }
        
        $SnsLoginCustomer = $app['eccube.plugin.repository.sns_login_customer']->findOneBy(array('union_id' => $User['unionid']));
        if ($SnsLoginCustomer) {
            $SnsLoginCustomer->setInfo(json_encode($User));
            $app['orm.em']->persist($SnsLoginCustomer);
            $app['orm.em']->flush();
        } else {
            $CustomerStatus = $app['eccube.repository.customer_status']->find(CustomerStatus::ACTIVE);
            $Customer = $app['eccube.repository.customer']->newCustomer();
            $Customer
                ->setStatus($CustomerStatus)
                ->setName01('User')
                ->setName02($User['nickname'])
                ->setEmail($User['unionid'] . '@wechat.com')
                ->setSalt(
                    $app['eccube.repository.customer']->createSalt(5)
                )
                ->setPassword(
                    $app['eccube.repository.customer']->encryptPassword($app, $Customer)
                )
                ->setSecretKey(
                    $app['eccube.repository.customer']->getUniqueSecretKey($app)
                );
            $app['orm.em']->persist($Customer);
            $app['orm.em']->flush();

            $SnsLoginCustomer = new SnsLoginCustomer();
            $SnsLoginCustomer
                ->setUnionId($User['unionid'])
                ->setInfo(json_encode($User))
                ->setCustomerId($Customer->getId());
            $app['orm.em']->persist($SnsLoginCustomer);
            $app['orm.em']->flush();

            $token = new UsernamePasswordToken($Customer, null, 'customer', array('ROLE_USER'));
            $app['security.token_storage']->setToken($token);
        }
        return $app->redirect($app->url('top'));
    }
}
