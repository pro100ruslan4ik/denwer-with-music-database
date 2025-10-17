<?php
    $dblocation = "localhost";
    $dbname = "music";
    $dbuser = "root";
    $dbpasswd = "";

    $id = @mysqli_connect($dblocation,$dbuser,$dbpasswd,$dbname);

    if (!$id)
    {
        exit("<p>Error: ".mysqli_connect_error()."</p>");
    }

    mysqli_set_charset($id, "utf8");
