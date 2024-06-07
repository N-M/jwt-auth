===================
Optional parameters
===================

Path
----

The optional ``path`` parameter allows you to specify the protected part of your website. It can be either a string or
an array. You do not need to specify each URL. Instead, think of ``path`` setting as a folder. In the example below
everything starting with ``/api`` will be authenticated. If you do not define ``path`` all routes will be protected.

.. code-block:: php

  $app = new Slim\App;

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "path" => "/api", /* or ["/api", "/admin"] */
      "secret" => "supersecretkeyyoushouldnotcommittogithub"
  ]));

Ignore
------

With the optional ``ignore`` parameter you can make exceptions to ``path`` parameter. In the example below everything
starting with ``/api`` and ``/admin`` will be authenticated except ``/api/token`` and ``/admin/ping`` which will not be
authenticated.

.. code-block:: php

  $app = new Slim\App;

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "path" => ["/api", "/admin"],
      "ignore" => ["/api/token", "/admin/ping"],
      "secret" => "supersecretkeyyoushouldnotcommittogithub"
  ]));

Header
------

By default, middleware tries to find the token from the ``Authorization`` header. You can change the header name using
the ``header`` parameter.

.. code-block:: php

  $app = new Slim\App;

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "header" => "X-Token",
      "secret" => "supersecretkeyyoushouldnotcommittogithub"
  ]));

Regexp
------

By default, the middleware assumes the value of the header is in ``Bearer <token>`` format. You can change this
behaviour with the ``regexp`` parameter. For example, if you have a custom header such as ``X-Token: <token>`` you
should pass both header and regexp parameters.

.. code-block:: php

  $app = new Slim\App;

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "header" => "X-Token",
      "regexp" => "/(.*)/",
      "secret" => "supersecretkeyyoushouldnotcommittogithub"
  ]));

Cookie
------

If the token is not found from the header, the middleware tries to find it via a cookie named ``token``. You can change
the cookie name using the ``cookie`` parameter.

.. code-block:: php

  $app = new Slim\App;

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "cookie" => "nekot",
      "secret" => "supersecretkeyyoushouldnotcommittogithub"
  ]));


Algorithm
---------

You can set supported algorithms via the ``algorithm`` parameter. This can be either a string or an array of strings.
The default value is ``["HS256"]``. Supported algorithms are ``HS256``, ``HS384``, ``HS512`` and ``RS256``. Note that
enabling both ``HS256`` and ``RS256`` is a
`security risk <https://auth0.com/blog/critical-vulnerabilities-in-json-web-token-libraries/>`_.

When passing multiple algorithms it must be a key array, with the key matching the ``kid`` of the JWT.

.. code-block:: php

  $app = new Slim\App;

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "secret" => [
          "acme" => "supersecretkeyyoushouldnotcommittogithub",
          "beta" => "supersecretkeyyoushouldnotcommittogithub",
      "algorithm" => [
          "amce" => "HS256",
          "beta" => "HS384"
      ]
  ]));
.. warning::
  Because of changes in ``firebase/php-jwt`` the ``kid`` is now checked when multiple algorithms are set, if you do not
  specify a key the algorithm will be used as the key. this also means the ``kid`` will now need to be present in the
  JWT header as well.

Attribute
---------

When the token is decoded successfully and authentication succeeds the contents of the decoded token are saved as a
``token`` attribute to the ``$request`` object. You can change this with. ``attribute`` parameter. Set to ``null`` or
``false`` to disable this behaviour

.. code-block:: php

  $app = new Slim\App;

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "attribute" => "jwt",
      "secret" => "supersecretkeyyoushouldnotcommittogithub"
  ]));

  /* ... */

  $decoded = $request->getAttribute("jwt");

Logger
------

The optional ``logger`` parameter allows you to pass in a PSR-3 compatible logger to help with debugging or other
application logging needs.

.. code-block:: php

  use Monolog\Logger;
  use Monolog\Handler\RotatingFileHandler;

  $app = new Slim\App;

  $logger = new Logger("slim");
  $rotating = new RotatingFileHandler(__DIR__ . "/logs/slim.log", 0, Logger::DEBUG);
  $logger->pushHandler($rotating);

  $app->add(new Tuupola\Middleware\JwtAuthentication([
      "path" => "/api",
      "logger" => $logger,
      "secret" => "supersecretkeyyoushouldnotcommittogithub"
  ]));

Before
------

The before function is called only when authentication succeeds but before the next incoming middleware is called. You
can use this to alter the request before passing it to the next incoming middleware in the stack. If it returns anything
else than ``Psr\Http\Message\ServerRequestInterface`` the return value will be ignored.
