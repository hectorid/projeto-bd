<?php
namespace App\controllers;

use App\models\PostModel;


class PostController extends AbstractController {
    public static function get(array $params): array {
        $post_id = (int)$params['post_id'] ?? 0;
        $user_id = (int)$params['user_id'] ?? 0;

        return PostModel::read($post_id, $user_id);
    }
}