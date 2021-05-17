<?php
namespace App\models;

use \App\Database;


class UserFeedModel extends Model {
    private const USER_FEED_VIEW = 'data.user_feed';


    public static function read(int $id = 0): array {
        $db = new Database();

        [$sql, $params] = self::buildSelectQuery(self::USER_FEED_VIEW, ['*'], ['id' => $id]);

        $db->query($sql, $params);

        return $db->fetchAll();
    }
}