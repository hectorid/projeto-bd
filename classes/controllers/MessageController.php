<?php
namespace App\controllers;

use App\models\MessageModel;


class MessageController extends \App\controllers\AbstractController {
    public static function get(array $params): array {
        $user_id = (int)$params['user_id'] ?? 0;
        $sent_by = (int)$params['sent_by'] ?? 0;
        $sent_to = (int)$params['sent_to'] ?? 0;

        return MessageModel::read($user_id, $sent_by, $sent_to);
    }


    public static function delete(array $params): bool {
        $message_id = (int)$params['message_id'] ?? 0;

        if (!is_numeric($message_id) or ($message_id <= 0))
            return false;

        return MessageModel::delete($message_id);
    }


    public static function post(array $params, array $input_data) {
        $sent_by = (int)$params['sent_by'] ?? 0;
        $sent_to = (int)$params['sent_to'] ?? 0;

        $text = (String)$input_data['text'] ?? '';

        if (($sent_by <= 0) or ($sent_to <= 0) or ($text === ''))
            return -1;

        $message_data = [
            'sent_by' => $sent_by,
            'sent_to' => $sent_to,
            'text'    => $text,
        ];

        return MessageModel::create($message_data);
    }


    public static function put(array $params , array $input_data) {
        $message_id = (int)$params['message_id'] ?? 0;

        $text = (String)$input_data['text'] ?? '';

        if (($message_id <= 0) or ($text === ''))
            return -1;

        $message_data = [
            'text' => $text,
            'last_edited_at = now()'
        ];

        return MessageModel::update($message_id, $message_data);
    }
}