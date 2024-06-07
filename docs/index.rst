======================================
JWT-Auth JWT Authentication Middleware
======================================

.. important::
  This package is a replacement for `tuupola/slim-jwt-auth` with the updated version of firebase/php-jwt to resolve
  `CVE-2021-46743 <https://nvd.nist.gov/vuln/detail/CVE-2021-46743>`_ in the meantime. I plan to maintain compatibility
  with v1, and then in v2 I plan to diverge, adding new features and dropping support for older PHP versions.

This middleware implements JSON Web Token Authentication. It was originally developed for Slim but can be used with any
framework using PSR-7 and PSR-15 style middleware. It has been tested with
`Slim Framework <http://www.slimframework.com/>`_ and
`Zend Expressive <https://zendframework.github.io/zend-expressive/>`_.

Heads up! You are reading the documentation for
`3.x branch <https://github.com/tuupola/slim-jwt-auth/tree/3.x>`_ which is PHP 7.4 and up only. If you are using an
older version of PHP see the `2.x branch <https://github.com/tuupola/slim-jwt-auth/tree/2.x>`_. These two branches are
not backwards compatible, see `UPGRADING <https://github.com/jimtools/jwt-auth/blob/main/UPGRADING.md>`_ for
instructions on how to upgrade.

Middleware does **not** implement an OAuth 2.0 authorization server nor does it
provide ways to generate, issue or store authentication tokens. It only parses
and authenticates a token when passed via header or cookie. This is useful for
example when you want to use
`JSON Web Tokens as API keys <https://auth0.com/blog/2014/12/02/using-json-web-tokens-as-api-keys/>`_.

For example implementation see `Slim API Skeleton <https://github.com/tuupola/slim-api-skeleton>`_.

User Guide
==========

.. toctree::
  :maxdepth: 3

  breaking
  overview
  options

