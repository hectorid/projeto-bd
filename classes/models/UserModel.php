<?php
namespace App\models;

use App\Database;


class UserModel extends Model {
    private const USERS_TABLE = 'data.users';


    public static function create(array $user_data): int {
        $db = new Database();

        [$sql, $params] = self::buildInsertQuery(
            self::USERS_TABLE,
            $user_data
        );

        $sql .= "\nRETURNING id";

        if ($db->query($sql, $params) === false)
            return -1;

        $result = $db->fetchRow();

        return $result['id'];
    }


    public static function read(int $user_id = 0, String $user = ''): array {
        $db = new Database();

        if ($user_id > 0)
            $where_conditions_array = ['id' => $user_id];

        [$sql, $params] = self::buildSelectQuery(
            self::USERS_TABLE,
            [
                'id',
                'username',
                'visible_name',
                'email',
                'birthdate',
                'encode(profile_picture, \'base64\') as profile_picture',
                'password_hash',
                'created_at',
            ],
            $where_conditions_array ?? []
        );

        if ($user_id <= 0 and $user !== '') {
            $sql .= ' WHERE username = :user or email = :user';
            $params[':user'] = $user;
        }

        $db->query($sql, $params);

        return $db->fetchAll();
    }


    public static function update(int $user_id, array $user_data): bool {
        $db = new Database();

        [$sql, $params] = self::buildUpdateQuery(
            self::USERS_TABLE,
            $user_data,
            ['id' => $user_id]
        );

        return $db->query($sql, $params);
    }


    public static function delete(int $user_id): bool {
        $db = new Database();

        [$sql, $params] = self::buildDeleteQuery(
            self::USERS_TABLE,
            ['id' => $user_id]
        );

        return $db->query($sql, $params);
    }
}