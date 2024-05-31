========
Overview
========

Requirments
===========

#. PHP 8.1 or greater

.. _installation:

Installation
============

The recommended way to install JWT-Auth is with composer
`Composer <https://getcomposer.org>`_. Composer is a dependency management tool
for PHP that allows you to declare the dependencies your project needs and
installs them into your project.

You can add JWT-Auth as a dependency using Composer:

.. code-block:: bash

  composer require jimtools/jwt-auth

.. note::

  If using Apache add the following to the ``.htaccess`` file. Otherwise, PHP won't
  have access to the ``Authorization`` header.

.. code-block::

  RewriteRule .* - [env=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

Upgrading
=========

The git repository contains an `upgrade guide`__ that details what changed
between the major versions.

__ https://github.com/jimtools/jwt-auth/blob/master/UPGRADING.md

License
=======

Licensed using the `MIT license <https://opensource.org/licenses/MIT>`_.

    Copyright (c) 2024 James Read <https://github.com/jimtools>

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.


Contributing
============

First off, thanks for taking the time to contribute!

Guidelines
----------

1. Use php-cs-fixer and phpstan
2. All pull requests must include unit tests to ensure the change works as
   expected and to prevent regressions.

Running the tests
-----------------

.. code-block:: bash

    make test

Reporting a security vulnerability
==================================

After a security vulnerability has been corrected, a security hotfix release will
be deployed as soon as possible.
