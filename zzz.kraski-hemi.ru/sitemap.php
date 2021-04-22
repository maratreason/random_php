<?php
    header("Content-Type: application/xml");
    DataBase::connect(Config::$base_type,Config::$base_host,Config::$base_name,Config::$base_user,Config::$base_pass);
    $domain = $_SERVER['HTTP_HOST'];

    function getAllPages($id){
        $pages = new Page();
        $page = $pages->get($id)->assoc();
        if($page['search_index']==1&&$page['status']==1){
            if(trim($page['url'])!=""&&strpos($page['url'],"http")===false&&trim($page['url'])!="/404"&&trim($page['url'])!="/error"){
                echo "<url>\n";
                echo "\t<loc>https://{$_SERVER['HTTP_HOST']}{$page['url']}</loc>\n";
                echo "\t<lastmod>".date('Y-m-d',strtotime($page['date']))."</lastmod>\n";
                echo "</url>\n";
            }
            if($page['type']==1){
                $pages->get(array("parent"=>$page['id']));
                while($child = $pages->assoc()){
                    getAllPages($child['id']);
                }
            }
        }
    }
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    echo getAllPages(10);
    echo "</urlset>";
?>
