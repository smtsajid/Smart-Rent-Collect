<?php

function authenticate_user($conn, $username, $password)
{
    $sql = "SELECT username, pass, role, name
            FROM users";

    $result = pg_query($conn, $sql);

    if (!$result) {
        return false;
    }

    while ($user = pg_fetch_assoc($result)) {
        if ($user['username'] === $username && $user['pass'] === $password) {
            return $user;
        }
    }

    return false;
}
