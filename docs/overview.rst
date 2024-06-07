========
Overview
========

Install
=======

Install the latest version using `composer <https://getcomposer.org/>`_.

.. code-block:: bash

  composer require jimtools/jwt-auth

.. note::
    If using Apache add the following to the ``.htaccess`` file. Otherwise, PHP won't
    have access to the ``Authorization: Bearer`` header.

.. code-block::

  RewriteRule .* - [env=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

Usage
=====

Configuration options are passed as an array. The only mandatory parameter is ``secret`` which is used for verifying the
token signature. Note again that ``secret`` is not the token. It is the secret you use to sign the token.

For simplicity's sake examples show ``secret`` hardcoded in code. In real life, you should store it somewhere else. A
good option is the environment variables. You can use `dotenv <https://github.com/vlucas/phpdotenv>`_ or something
similar for development. Examples assume you are using Slim Framework.

.. code-block:: php

  $app = new Slim\App;

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "secret" => "supersecretkeyyoushouldnotcommittogithub"
  ]));

An example where your secret is stored as an environment variable:

.. code-block:: php

  $app = new Slim\App;

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "secret" => getenv("JWT_SECRET")
  ]));

When a request is made, the middleware tries to validate and decode the token. If a token is not found or there is an
error when validating and decoding it, the server will respond with ``401 Unauthorized``.

Validation errors are triggered when the token has been tampered with or the token has expired. For all possible
validation errors, see `JWT library <https://github.com/firebase/php-jwt/blob/master/src/JWT.php#L60-L138>`_ source.

Rules
=====

The optional ``rules`` parameter allows you to pass in rules which define whether the request should be authenticated or
not. A rule is a callable which receives the request as a parameter. If any of the rules returns boolean ``false`` the
request will not be authenticated.

By default, the middleware configuration looks like this. All paths are authenticated with all request methods except
``OPTIONS``.

.. code-block:: php

  $app = new Slim\App;

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "rules" => [
          new Tuupola\Middleware\JwtAuthentication\RequestPathRule([
              "path" => "/",
              "ignore" => []
          ]),
          new Tuupola\Middleware\JwtAuthentication\RequestMethodRule([
              "ignore" => ["OPTIONS"]
          ])
      ]
  ]));

RequestPathRule contains both a ``path`` parameter and a ``ignore`` parameter. Later contains paths which should not be
authenticated. RequestMethodRule contains the ``ignore`` parameter of request methods which also should not be
authenticated. Think of ``ignore`` as a whitelist.

In 99% of the cases, you do not need to use the ``rules`` parameter. It is only provided for special cases when defaults
do not suffice.

Security
========

JSON Web Tokens are essentially passwords. You should treat them as such and you should always use HTTPS. If the
middleware detects insecure usage over HTTP it will throw a ``RuntimeException``. By default, this rule is relaxed for
requests to the server running on ``localhost``. To allow insecure usage you must enable it manually by setting
``secure`` to ``false``.

.. code-block:: php

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "secure" => false,
      "secret" => "supersecretkeyyoushouldnotcommittogithub"
  ]));

Alternatively, you could list multiple development servers to have relaxed security. With the below settings both
``localhost`` and ``dev.example.com`` allow incoming unencrypted requests.

.. code-block:: php

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "secure" => true,
      "relaxed" => ["localhost", "dev.example.com"],
      "secret" => "supersecretkeyyoushouldnotcommittogithub"
  ]));


Authorization
=============

By default middleware only authenticates. This is not very interesting. The beauty of JWT is you can pass extra data in
the token. This data can include for example scope which can be used for authorization.

**It is up to you to implement how token data is stored or possible authorization implemented.**

Let's assume you have a token which includes data for scope. By default, middleware saves the contents of the token to
the ``token`` attribute of the request.

.. code-block:: php

  [
      "iat" => "1428819941",
      "exp" => "1744352741",
      "scope" => ["read", "write", "delete"]
  ]

.. code-block:: php

  $app = new Slim\App;

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "secret" => "supersecretkeyyoushouldnotcommittogithub"
  ]));

  $app->delete("/item/{id}", function ($request, $response, $arguments) {
      $token = $request->getAttribute("token");
      if (in_array("delete", $token["scope"])) {
          /* Code for deleting item */
      } else {
          /* No scope so respond with 401 Unauthorized */
          return $response->withStatus(401);
      }
  });

Testing
=======

You can run tests either manually or automatically on every code change. Automatic tests require
`entr <http://entrproject.org/>`_ to work.

.. code-block:: bash

  make test

.. code-block:: bash

  brew install entr
  make watch

Contributing
============

Please see `CONTRIBUTING <https://github.com/jimtools/jwt-auth/blob/main/CONTRIBUTING.md>`_ for details.

Security Issues
===============

If you discover any security-related issues, please email james.read.18@gmail.com instead of using the issue tracker.

License
=======

The MIT License (MIT). Please see `License File <https://github.com/jimtools/jwt-auth/blob/main/LICENSE.md>`_ for more
information.
