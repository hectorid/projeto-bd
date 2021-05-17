<?php
namespace App;

define("DIR_ROOT", dirname(__FILE__));

const DIR_CLASSES = DIR_ROOT . '/classes';

const DATABASE_CONNECTION_FILE = DIR_ROOT . '/database_connection.ini';

// Request methods
const GET    = 'get';
const POST   = 'post';
const PUT    = 'put';
const DELETE = 'delete';

// The order of declaration is important!
const API_ROUTES = [
    ['path' => '/messages/:message_id',                     'controller' => 'Message',     'methods' => [GET, PUT, DELETE]],
    ['path' => '/posts/:post_id',                           'controller' => 'Post',        'methods' => [GET, PUT, DELETE]],
    ['path' => '/users',                                    'controller' => 'User',        'methods' => [GET, POST]],
    ['path' => '/users/profiles',                           'controller' => 'UserProfile', 'methods' => [GET]],
    ['path' => '/users/:user_id',                           'controller' => 'User',        'methods' => [GET, PUT, DELETE]],
    ['path' => '/users/:user_id/chats',                     'controller' => 'UserChat',    'methods' => [GET]],
    ['path' => '/users/:follower_id/follows/:followed_id',  'controller' => 'UserFollow',  'methods' => [GET, POST, DELETE]],
    ['path' => '/users/:sent_by/messages/:sent_to',         'controller' => 'Message',     'methods' => [GET, POST]],
    ['path' => '/users/:user_id/posts',                     'controller' => 'Post',        'methods' => [GET, POST]],
    ['path' => '/users/:user_id/profile',                   'controller' => 'UserProfile', 'methods' => [GET]],
];

// TODO: Remember to disable it
const DEBUG_MODE = true;