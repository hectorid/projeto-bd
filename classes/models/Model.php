<?php
namespace App\models;


use App\Database;

class Model {
    private const BASE64_PATTERN = '/^data:(?<mime_type>.+?);base64,(?<encoded_data>.+)$/';


    protected static function buildDeleteQuery(string $table_name, array $where_conditions_array) {
        if (empty($table_name) or empty($where_conditions_array))
            return false;

        [$where_sql, $params] = self::buildWhereCondition($where_conditions_array);

        $sql = "DELETE FROM {$table_name}\nWHERE ({$where_sql})";

        return [$sql, $params];
    }


    protected static function buildInsertQuery(string $table_name, array $data_array) {
        if (empty($table_name) or empty($data_array))
            return false;

        $columns = array_keys($data_array);
        $columns_str = implode(', ', $columns);

        $params = self::arrayToParams($data_array);

        $params_keys = array_keys($params);
        $params_keys_str = implode(', ', $params_keys);

        $sql = "INSERT INTO {$table_name}({$columns_str})\nVALUES({$params_keys_str})";

        return [$sql, $params];
    }


    protected static function buildSelectQuery(string $table_name, array $columns_array, array $where_conditions_array, array $order_by_columns_array = [], int $limit = 0) {
        if (empty($table_name) or empty($columns_array))
            return false;

        $columns_str = implode(",\n    ", $columns_array);

        $sql = "SELECT\n    {$columns_str}\nFROM {$table_name}";

        if (!empty($where_conditions_array)) {
            [$where_sql, $params] = self::buildWhereCondition($where_conditions_array);

            $sql .= "\nWHERE ({$where_sql})";
        }
        else
            $params = [];

        if (!empty($order_by_columns_array))
            $sql .= "\nORDER BY " . implode(', ', $order_by_columns_array);

        if (is_numeric($limit) and $limit > 0)
            $sql .= "\nLIMIT {$limit}";

        return [$sql, $params];
    }


    protected static function buildUpdateQuery(string $table_name, array $data_array, array $where_conditions_array) {
        if (empty($table_name) or empty($data_array))
            return false;

        $sets = [];
        foreach ($data_array as $column => $value) {
            // Insert the value directly into the SQL (like function calls)
            if (is_numeric($column) and is_string($value))
                $sets[] = $value;
            // Insert base64 strings as binary
            else if (preg_match(self::BASE64_PATTERN, $value))
                $sets[] = "{$column} = decode(:{$column}, 'base64')";
            else
                $sets[] = "{$column} = :{$column}";
        }
        $set_str = implode(",\n    ", $sets);

        $sql = "UPDATE {$table_name}\nSET {$set_str}";

        $params = self::arrayToParams($data_array);

        if (!empty($where_conditions_array)) {
            [$where_sql, $where_params] = self::buildWhereCondition($where_conditions_array);

            $sql .= "\nWHERE ({$where_sql})";

            $params = array_merge($params, $where_params);
        }

        return [$sql, $params];
    }


    private static function arrayToParams(array $data): array {
        $params = [];
        foreach ($data as $column => $value)
            if (preg_match(self::BASE64_PATTERN, $value, $matches))
                $params[":{$column}"] = $matches['encoded_data'];
            else if (!is_numeric($column))
                $params[":{$column}"] = $value;

        return $params;
    }


    private static function buildWhereCondition(array $conditions_array): array {
        if(array_key_exists(0, $conditions_array)) {
            $sql = $conditions_array[0];

            unset($conditions_array[0]);
            $params = $conditions_array;

            return [$sql, $params];
        }

        $conditions = [];
        foreach ($conditions_array as $column => $value) {
            // Insert the value directly into the SQL (like function calls)
            if (is_numeric($column) and is_string($value))
                $conditions[] = $value;
            else
                $conditions[] = "{$column} = :{$column}";
        }
        $sql = implode(" and ", $conditions);

        $params = self::arrayToParams($conditions_array);

        return [$sql, $params];
    }
}