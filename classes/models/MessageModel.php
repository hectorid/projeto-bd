<?php
namespace App\models;

use App\Database;


class MessageModel extends Model {
    private const MESSAGES_TABLE = 'data.messages';


    public static function create(array $message_data) {
        $db = new Database();

        [$sql, $params] = self::buildInsertQuery(
            self::MESSAGES_TABLE,
            $message_data
        );

        $sql .= "\nRETURNING id, created_at";

        if ($db->query($sql, $params) === false)
            return false;

        return $db->fetchRow();
    }


    public static function read(int $message_id = 0, int $sent_by = 0, int $sent_to = 0): array {
        $db = new Database();

        if ($message_id > 0)
            $where_conditions_array = ['id' => $message_id];
        else if (($sent_by > 0) and ($sent_to > 0))
            $where_conditions_array = [
                '(sent_by in (:sent_by, :sent_to)) and (sent_to in(:sent_by, :sent_to))',
                ':sent_by' => $sent_by,
                ':sent_to' => $sent_to,
            ];

        [$sql, $params] = self::buildSelectQuery(
            self::MESSAGES_TABLE,
            ['*'],
            $where_conditions_array ?? [],
            [
                'created_at desc',
                'id desc'
            ]
        );

        $db->query($sql, $params);

        return $db->fetchAll();
    }


    public static function update(int $message_id, array $message_data) {
        $db = new Database();

        [$sql, $params] = self::buildUpdateQuery(
            self::MESSAGES_TABLE,
            $message_data,
            ['id' => $message_id]
        );

        $sql .= "\nRETURNING last_edited_at";

        if ($db->query($sql, $params) === false)
            return false;

        return $db->fetchRow();
    }


    public static function delete(int $message_id): bool {
        $db = new Database();

        [$sql, $params] = self::buildDeleteQuery(
            self::MESSAGES_TABLE,
            ['id' => $message_id]
        );

        return $db->query($sql, $params);
    }
}