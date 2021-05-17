<?php
namespace App\controllers;


class AbstractController {
    public static function filterEditableData(array $input_data, array $editable_data): array {
        foreach ($input_data as $key => $value)
            if (!in_array($key, $editable_data))
                unset($input_data[$key]);

        return $input_data;
    }


    // At least the GET method should be implemented
    public static function get(array $params) {}
}