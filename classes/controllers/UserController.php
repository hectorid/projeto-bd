<?php
namespace App\controllers;

use App\models\UserModel;


class UserController extends AbstractController {
    private const EDITABLE_DATA = [
        'username',
        'visible_name',
        'email',
        'birthdate',
        'profile_picture',
        'password_hash',
    ];


    public static function get(array $params): array {
        $user_id = (int)$params['user_id'] ?? 0;

        $user_data = UserModel::read($user_id);

        // Hide the password hash from public view
        foreach ($user_data as &$user)
            unset($user['password_hash']);

        return $user_data;
    }


    public static function delete(array $params): bool {
        $user_id = (int)$params['message_id'] ?? 0;

        if (!is_numeric($user_id) or ($user_id <= 0))
            return false;

        return UserModel::delete($user_id);
    }


    public static function post(array $params, array $input_data): int {
        if (!self::validate_input_data($input_data))
            return false;

        // Create a password hash to store on the database
        $input_data['password_hash'] = password_hash($input_data['password'], PASSWORD_DEFAULT);

        $input_data = self::filterEditableData($input_data, self::EDITABLE_DATA);

        return UserModel::create($input_data);
    }


    public static function put(array $params , array $input_data): bool {
        if (!isset($params['user_id']))
            return false;

        if (!self::validate_input_data($input_data))
            return false;

        $password = $input_data['password'] ?? null;
        if ($password) // Create a password hash to store on the database
            $input_data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);

        $input_data = self::filterEditableData($input_data, self::EDITABLE_DATA);
        if(empty($input_data))
            return false;

        return UserModel::update($params['user_id'], $input_data);
    }


    public static function validate_login(String $user, String $password): int {
        if (empty($user) or empty($password))
            return 0;

        $users_data = UserModel::read(0, $user);

        foreach ($users_data as $user_data)
            if (password_verify($password, $user_data['password_hash']))
                return $user_data['id'];

        return 0;
    }


    private static function validate_input_data(array $input_data): bool {
        $username     = (!isset($input_data['username'])     or !empty($input_data['username']));
        $visible_name = (!isset($input_data['visible_name']) or !empty($input_data['visible_name']));
        $email        = (!isset($input_data['email'])        or !empty($input_data['email']));
        $birthdate    = (!isset($input_data['birthdate'])    or !empty($input_data['birthdate']));
        $password     = (!isset($input_data['password'])     or !empty($input_data['password']));

        return $username
            and $visible_name
            and $email
            and $birthdate
            and $password;
    }
}