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
use Plugin\SnsLogin\Entity\SnsLoginConfig;
use Symfony\Component\HttpFoundation\Request;

class ConfigController
{
    /**
     * SnsLogin用設定画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $Config = $app['eccube.plugin.repository.sns_login_config']->find(1);
        if (!$Config) {
            $Config = new SnsLoginConfig();
        }

        $form = $app['form.factory']->createBuilder('snslogin_config', $Config)->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Plugin\SnsLogin\Entity\SnsLoginConfig $Config */
            $Config = $form->getData();
            $Config->setId(1);
            $app['orm.em']->persist($Config);
            $app['orm.em']->flush();
        }

        return $app->render('SnsLogin/Resource/template/admin/config.twig', array(
            'form' => $form->createView(),
        ));
    }

}
