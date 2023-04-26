<?php

declare(strict_types=1);

/*

Copyright (c) 2013-2020 Mika Tuupola

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/

/**
 * @see       https://github.com/tuupola/slim-basic-auth
 * @license   https://www.opensource.org/licenses/mit-license.php
 */

namespace App\Application\Middleware\JwtAuthentication;

use App\Application\Middleware\JwtAuthentication\Token;

final class PdoGetUserToken
 implements AuthenticatorInterface
{

    /**
     * Stores all the options passed to the authenticator.
     * @var mixed[]
     */
    private $options;

    /**
     * @param mixed[] $options
     */
    public function __construct(array $options = [])
    {

        /* Default options. */
        $this->options = [
            "utable" => "users",
            "user" => "user",
            "ttable" => "tokens",
            "uiid" => "uiid",
        ];

        if ($options) {
            $this->options = array_merge($this->options, $options);
        }
    }

    /**
     * @param string[] $arguments
     */
    public function __invoke(array $arguments): Token
    {

       
        $user = $arguments["user"];

        $usql = $this->userRequest();

        $statement = $this->options["pdo"]->prepare($usql);

        $statement->execute([$user]);

        $uiid = null;

        if ($user = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $uiid = $user["id"];
        }else{
            return null;
        }

        $tsql = $this->tokenRequest();

        $statement = $this->options["pdo"]->prepare($tsql);
        $statement->execute([$uiid]);

        if ($token = $statement->fetch(\PDO::FETCH_ASSOC)) {

            if($token["expiration"])
            $tokenObj = new Token($token["id"], $token["uiid"], $token["token"], $token["created"], $token["expiration"]);
            return $tokenObj ;
        }

        return null;
    }

    public function userRequest(): string
    {
        $driver = $this->options["pdo"]->getAttribute(\PDO::ATTR_DRIVER_NAME);

        /* Workaround to test without sqlsrv with Travis */
        if (defined("__PHPUNIT_ATTR_DRIVER_NAME__")) {
            $driver = __PHPUNIT_ATTR_DRIVER_NAME__;
        }

        if ("sqlsrv" === $driver) {
            $sql =
                "SELECT TOP 1 *
                 FROM {$this->options['utable']}
                 WHERE {$this->options['user']} = ?";
        } else {
            $sql =
                "SELECT *
                 FROM {$this->options['utable']}
                 WHERE {$this->options['user']} = ?
                 LIMIT 1";
        }

        return (string) preg_replace("!\s+!", " ", $sql);
    }

    public function tokenRequest(): string
    {
        $driver = $this->options["pdo"]->getAttribute(\PDO::ATTR_DRIVER_NAME);

        /* Workaround to test without sqlsrv with Travis */
        if (defined("__PHPUNIT_ATTR_DRIVER_NAME__")) {
            $driver = __PHPUNIT_ATTR_DRIVER_NAME__;
        }

        if ("sqlsrv" === $driver) {
            $sql =
                "SELECT TOP 1 *
                 FROM {$this->options['ttable']}
                 WHERE {$this->options['uiid']} = ?";
        } else {
            $sql =
                "SELECT *
                 FROM {$this->options['ttable']}
                 WHERE {$this->options['uiid']} = ?
                 LIMIT 1";
        }

        return (string) preg_replace("!\s+!", " ", $sql);
    }

}
