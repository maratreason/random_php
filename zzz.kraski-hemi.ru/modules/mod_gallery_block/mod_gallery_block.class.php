<?
class mod_gallery_block extends Module{

	function __construct(){
		
		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_gallery_block"; // получим класс
        //скрипты фронтальной части
        if(!Engine::$admin){
            $this->setFile("grid/jquery.photoset-grid.js"); // подключим js файл модуля
            //$this->setFile("engine.js"); // подключим js файл модуля
            $this->setFile("grid/jquery.colorbox.js"); // подключим css файл модуля
            $this->setFile("grid/colorbox.css");
			
           // $this->setFile("just/jquery.justifiedGallery.min.js"); // подключим js файл модуля
		//	$this->setFile("just/justifiedGallery.min.css"); // подключим js файл модуля
            $this->setFile("engine.js"); // подключим js файл модуля
	
        }

        // стандартная инициализация модуля
        $this->Init();
	
	}
    public function main($param){

		$images = "";
		$block_template = Engine::$root."/modules/mod_gallery_block/photo_list.html";
        $template = Engine::$root.Config::$front_template."/{$param['template_list']}.html";
        $flag = "cat";
        if($param['page_id']==0){
            if($param['cur_page_id']==0){
                $p = Engine::$page['id'];
                $flag = "cur_page";
            } else {
                $p = $param['cur_page_id'];
                $flag = "cur_page";
            }            
        } else {
            $p = $param['page_id'];
        }

        
        $cat = new Page();
        $cat->get($p)->assoc();
        $param['_all_work'] = $cat->row['url'];
        $param['_all_work_caption'] = $cat->row['url_name'];
        
        if($flag=="cur_page"){
            // переходим на указаную страницу и берем фото из неё
            $fotos = $cat->row[$param['page_field']];
            $foto = json_decode($fotos);
        } else {
            // проходимся по разделу фотогалереи и ищем на страницах случайные фото
            $fotos = Array();
            $pages = new Page();
            $value = array(":parent"=>$param['page_id']);
            $pages->select("WHERE parent=:parent ORDER BY rand() LIMIT 3",$value);
            while($page = $pages->next()){
                $ft = json_decode($page[$param['page_field']]);
                if(is_array($ft)) $fotos = array_merge($fotos,$ft);
                //$fotos .= $page[$param['page_field']];
            }
            $foto = $fotos;
        }
        
        $out = "";


        /*if(is_array($foto)) */
        //$foto = array_diff($foto, array(''));

        if(count($foto)>=$param['image_count']){
            $keys = array_rand($foto, trim($param['image_count']));
            if(!is_array($keys)) $keys= array(0=>$keys);
            $image = array();
            $count = 0;
            foreach($keys as $key){
                $img_url = trim($foto[$key][0]);
				$image[$count]['image_full'] = $img_url;  
				$image[$count]['image_preview'] = func::imageToWidth($img_url,$param['image_width']);
				$image[$count]['image_caption'] = $foto[$key][1];  
				$count++;
            }

            $data = $param;
            $data['_images'] = $image;
            $block = Engine::templater($block_template,$data);
			$data['_mod'] = $block;
            if(file_exists($template)){
                $out = Engine::templater($template,$data);
            } else {
                $out = $block;
            }
        }
        return $out;
	}
}


?>