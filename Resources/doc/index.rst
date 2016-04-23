EfragPaginatorBundle
====================

Installation instructions
-------------------------

For installation instructions please visit the home page of this bundle or read the README.md from the root of this
project.

Configuration
-------------

There is currently only one configuration option available for this bundle which is the default number of results per
 page.

.. code-block:: yaml

    efrag_paginator:
        perPage: 15

If the configuration value is not set then it defaults to 15 results per page.

Usage
-----

The bundle registers a service called ``efrag_paginator``. The service can be used from a controller class like:

.. code-block:: php

    $links = $this->get('efrag_paginator')
        ->setPerPage(10) // to override the default per page number of results
        ->setTotal(20) // the total number of results returned by a search
        ->setRoute('app_search') // the symfony defined named route for the search page
        ->setRouteParameters(['type' => 'foo']) // the required parameters for the proper generation of the route
        ->getLinks(1); // gets the link array assuming we are viewing the first result page
