<?php
require_once 'defines.php';


function check_is_user_logged(): bool {
    if (session_status() === PHP_SESSION_NONE)
        session_start();

    DEFINE('USER_ID', $_SESSION['user_id'] ?? 0);

    return (USER_ID > 0);
}


function getParamsFromResource($path, $resource): array {
    // Get all the parameters names from the path (:param)
    preg_match_all('/:(?<params>[^\/?]+)/', $path, $matches);
    $params_names = $matches['params'];

    // Use the RegEx generated from the path to match the corresponding values
    preg_match_all(pathToRegex($path), $resource, $matches);

    return array_map(function ($captures) {return $captures[0];}, // Get the first match for each capture group
        array_filter(
            $matches,
            function ($capture_group) {return !is_numeric($capture_group);}, // Select only the named capture groups
            ARRAY_FILTER_USE_KEY
        )
    );
}


function pathToRegex(string $path): string {
    $patterns = [
        '/\//',          // Match all bars '/'
        '/:([^\/\\\?]+)/', // Match the ":params" (yes, that backslash is double escaped)
    ];
    $replacements = [
        '\\/',       // Escape the bars '/'
        '(?<$1>[^\/?]+)', // Replace with a named capture group that match anything until the next '/' or '?'
    ];

    $pattern = preg_replace($patterns, $replacements, $path);

    return "/^{$pattern}$/";
}


function redirect_to(string $url): void {
    $refresh_time = 0; // seconds
    header("Refresh: {$refresh_time}; url={$url}");
    die;
}
