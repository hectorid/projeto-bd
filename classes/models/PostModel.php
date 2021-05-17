<?php
namespace App\models;

use App\Database;


class PostModel extends Model {
    private const POSTS_TABLE = 'data.posts';
    private const POSTS_VIEW = 'data.user_posts';


    public static function create(array $post_data): int {
        $db = new Database();

        [$sql, $params] = self::buildInsertQuery(
            self::POSTS_TABLE,
            $post_data
        );

        $sql .= "\nRETURNING id";

        if ($db->query($sql, $params) === false)
            return -1;

        $result = $db->fetchRow();

        return $result['id'];
    }


    public static function read(int $post_id = 0, $user_id = 0): array {
        $db = new Database();

        if ($post_id > 0)
            $where_conditions_array = ['post_id' => $post_id];
        else if ($user_id > 0)
            $where_conditions_array = ['user_id' => $user_id];

        [$sql, $params] = self::buildSelectQuery(
            self::POSTS_VIEW,
            ['*'],
            $where_conditions_array ?? []
        );

        $db->query($sql, $params);

        return $db->fetchAll();
    }


    public static function update(int $post_id, array $post_data): bool {
        $db = new Database();

        [$sql, $params] = self::buildUpdateQuery(
            self::POSTS_TABLE,
            $post_data,
            ['id' => $post_id]
        );

        return $db->query($sql, $params);
    }


    public static function delete(int $post_id): bool {
        $db = new Database();

        [$sql, $params] = self::buildDeleteQuery(
            self::POSTS_TABLE,
            ['id' => $post_id]
        );

        return $db->query($sql, $params);
    }
}