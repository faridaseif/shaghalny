<?php
// config/routes.php
// Central route table for the app (used by public/index.php dispatcher)
// Format: return [ 'GET' => [ '/path' => 'Controller@action', ... ], 'POST' => [...] ];

return [
    'GET' => [
        '/' => 'HomeController@index',
        '/feed' => 'HomeController@index',
        '/social-feed' => 'HomeController@index',
        '/support-center' => 'SupportController@index',
        '/support' => 'SupportController@index',
        '/support/dashboard' => 'SupportController@dashboard',
        '/safety-tips' => 'SupportController@safetyTips',
        '/jobs/map' => 'JobsController@map',
        '/messages' => 'MessageController@inbox',
        '/messages/fetch' => 'MessageController@fetch',
        '/messages/conversations' => 'MessageController@conversations',
        '/posts/comments' => 'PostController@getComments',
        '/support/reports' => 'SupportController@getReports',
        '/support/report' => 'SupportController@getReport',
        // add more GET routes here
    ],
    'POST' => [
        '/login' => 'AuthController@login',
        '/register' => 'AuthController@register',
        '/messages/send' => 'MessageController@send',
        '/posts/create' => 'PostController@create',
        '/posts/update' => 'PostController@update',
        '/posts/like' => 'PostController@toggleLike',
        '/posts/comment' => 'PostController@addComment',
        '/posts/delete' => 'PostController@delete',
        '/support/report' => 'SupportController@createReport',
        '/support/message' => 'SupportController@createMessage',
        // add more POST routes here
    ],
];
