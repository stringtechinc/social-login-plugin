<?php

/*
 * This file is part of the SnsLogin
 *
 * Copyright (C) 2018 StringTech Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SnsLogin;

use Eccube\Application;

class SnsLoginEvent
{
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
