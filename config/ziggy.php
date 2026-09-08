<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route groups
    |--------------------------------------------------------------------------
    |
    | Anonymous visitors only receive the "public" group, so admin route
    | names and URIs are not embedded in every public page. The owner
    | receives the full list.
    |
    */

    'groups' => [
        'public' => [
            'home', 'about', 'portfolio', 'portfolio.*', 'contact', 'contact.*', 'hobby', 'more',
            'resume', 'note', 'feeds', 'sitemap', 'api.*',
            'login', 'logout', 'register', 'password.*', 'verification.*', 'profile.*', 'appearance',
        ],
    ],
];
