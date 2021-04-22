<?php
    header("Content-Type: text/plain");
    DataBase::connect(Config::$base_type,Config::$base_host,Config::$base_name,Config::$base_user,Config::$base_pass);
    $robots = str_replace("{{domain}}", $_SERVER['HTTP_HOST'], Engine::constant('robots'));
    echo $robots;
?>
