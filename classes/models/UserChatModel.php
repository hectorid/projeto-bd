<?php
namespace App\models;

use App\Database;


class UserChatModel extends Model {
    private const USER_CHATS_VIEW = 'data.user_chats';


    public static function read(int $user_id = 0): array {
        $db = new Database();

        if ($user_id > 0)
            $where_conditions_array = ['user_id' => $user_id];

        [$sql, $params] = self::buildSelectQuery(
            self::USER_CHATS_VIEW,
            ['*'],
            $where_conditions_array ?? []
        );

        $db->query($sql, $params);

        return $db->fetchAll();
    }
}