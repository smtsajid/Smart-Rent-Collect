<?php
// Model/res_model.php

function register_user($conn, $data)
{

    $sql = "INSERT INTO users (username, pass, name, phone, nid, dob, address, role, admin_user, ismod) 
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)";

    $params = array(
        $data['username'], // $1
        $data['pass'],     // $2
        $data['name'],     // $3
        $data['phone'],    // $4
        $data['nid'],      // $5
        $data['dob'],      // $6
        $data['address'],  // $7
        'admin',           // $8 - Role hardcoded to admin
        null,              // $9 - admin_user is always null for new registrations
        null               // $10 - ismod is always null as requested
    );


    $result = pg_query_params($conn, $sql, $params);

    return $result ? true : false;
}
?>