<?php
namespace App\models;

use App\Database;


class UserProfileModel extends Model {
    private const USER_PROFILES_VIEW = 'data.user_profiles';


    public static function read(int $user_id = 0): array {
        $db = new Database();

        if ($user_id > 0)
            $where_conditions_array = ['id' => $user_id];

        [$sql, $params] = self::buildSelectQuery(
            self::USER_PROFILES_VIEW,
            ['*'],
            $where_conditions_array ?? []
        );

        $db->query($sql, $params);

        return $db->fetchAll();
    }
}