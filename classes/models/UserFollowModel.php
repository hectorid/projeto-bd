<?php
namespace App\models;

use App\Database;


class UserFollowModel extends Model {
    private const USER_FOLLOWS_TABLE = 'data.user_follows';


    public static function create(int $follower_id, int $followed_id): bool {
        $db = new Database();

        [$sql, $params] = self::buildInsertQuery(
            self::USER_FOLLOWS_TABLE,
            [
                'follower' => $follower_id,
                'followed' => $followed_id,
            ]
        );

        return $db->query($sql, $params);
    }


    public static function read(int $follower_id, int $followed_id): array {
        $db = new Database();

        [$sql, $params] = self::buildSelectQuery(
            self::USER_FOLLOWS_TABLE,
            ['1'],
            [
                'follower' => $follower_id,
                'followed' => $followed_id,
            ],
            [],
            1
        );

        $db->query($sql, $params);

        return $db->fetchRow();
    }


    public static function delete(int $follower_id, int $followed_id): bool {
        $db = new Database();

        [$sql, $params] = self::buildDeleteQuery(
            self::USER_FOLLOWS_TABLE,
            [
                'follower' => $follower_id,
                'followed' => $followed_id,
            ]
        );

        return $db->query($sql, $params);
    }
}