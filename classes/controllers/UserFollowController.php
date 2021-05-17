<?php
namespace App\controllers;

use App\models\UserFollowModel;


class UserFollowController extends AbstractController {
    public static function get(array $params): bool {
        $follower_id = (int)$params['follower_id'] ?? 0;
        $followed_id = (int)$params['followed_id'] ?? 0;

        if (($follower_id <= 0) or ($followed_id <= 0))
            return false;

        return !empty(UserFollowModel::read($follower_id, $followed_id));
    }


    public static function delete(array $params): bool {
        $follower_id = (int)$params['follower_id'] ?? 0;
        $followed_id = (int)$params['followed_id'] ?? 0;

        if (($follower_id <= 0) or ($followed_id <= 0))
            return false;

        return UserFollowModel::delete($follower_id, $followed_id);
    }


    public static function post(array $params, array $input_data): bool {
        $follower_id = (int)$params['follower_id'] ?? 0;
        $followed_id = (int)$params['followed_id'] ?? 0;

        if (($follower_id <= 0) or ($followed_id <= 0))
            return false;

        return UserFollowModel::create($follower_id, $followed_id);
    }
}