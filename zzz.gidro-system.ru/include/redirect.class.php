<?
class Redirect {
    function setRedirects(){
        DataBase::connect(Config::$base_type,Config::$base_host,Config::$base_name,Config::$base_user,Config::$base_pass);
        $cur = $_SERVER['REQUEST_URI'];
        $base = new DataBase();
        $base->setTable('redirect');
        $urls = $base->get()->all();
        foreach($urls as $url){
            if($url['url_from']===$cur){
                //301
                if($url['code']==301){
                    echo $url['url_to']."  =  ".$cur;
                    header("Location:".$url['url_to'], true, 301);
                    exit;
                }
                //404
                if($url['code']==404){
                    Engine::setHeader("HTTP/1.x 404 Not Found");
                    echo Engine::getPage("/error");
                }
            }
        }
    }
}
?>
