<?php
namespace App\controllers;

use \App\models\UserProfileModel;


class UserProfileController extends AbstractController {
    public static function get(array $params): array {
        $user_id = (int)$params['user_id'] ?? 0;

        return UserProfileModel::read($user_id);
    }
}