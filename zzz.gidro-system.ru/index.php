<?php
// загрузим основные классы сразу, ускоряет работу скрипта
include_once("include/database.class.php");
include_once("include/engine.class.php");
include_once("include/page.class.php");
include_once("include/redirect.class.php");

// robots.txt
if($_SERVER['REQUEST_URI']==="/robots.txt"){
    include "robots.php";
}else if($_SERVER['REQUEST_URI']==="/sitemap.xml"){
    include "sitemap.php";
} else {
    // редиректы и коды
    Redirect::setRedirects();
    // загрузка и инициализация конфига и переменных окружения
    Engine::init();
}

include_once("include/cart.php");
?>
