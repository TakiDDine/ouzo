<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\CookiesSetter;

class MockCookiesSetter extends CookiesSetter
{
    private $_cookies = [];

    public function setCookies($cookies)
    {
        $this->_cookies = $cookies;
    }

    public function getCookies()
    {
        return $this->_cookies;
    }
}
