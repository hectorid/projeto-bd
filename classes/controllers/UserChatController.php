<?php
namespace App\controllers;

use App\models\UserChatModel;


class UserChatController extends AbstractController {
    public static function get(array $params): array {
        $user_id = (int)$params['user_id'] ?? 0;

        return UserChatModel::read($user_id);
    }
}