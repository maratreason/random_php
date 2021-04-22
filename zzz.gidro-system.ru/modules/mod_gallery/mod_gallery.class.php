<?
class mod_gallery extends Module{

	function __construct(){
		
		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_gallery"; // получим класс
        //скрипты фронтальной части
        if(!Engine::$admin){
            $this->setFile("engine.js"); // подключим js файл модуля
            $this->setFile("grid/jquery.colorbox.js"); // подключим css файл модуля
            $this->setFile("grid/colorbox.css"); 
        }

        // стандартная инициализация модуля
        $this->Init();
	
	}
    public function main($param){
        $out = "";
		$images = "";
		//$block_template = Engine::$root."/modules/mod_gallery/gallery_block.html";
        $template = dirname(__FILE__)."/{$param['template_list']}.html";
        //Engine::console($template);
        // получаем список картинок
        if($param['page_id']==0){
		  $images = Engine::$page[$param['page_field']];    
		} else {
		    $page = new Page();
            $page = $page->get(trim($param['page_id']))->assoc();
            $images = $page[$param['page_field']];
		}
        $images = json_decode($images);
		//print_r($images);
		
        $image = array();
        $count = 0;
        foreach($images as $img){
            $img_url = trim($img[0]);
			$image[$count]['image_full'] = $img_url;  
			$image[$count]['image_preview'] = func::imageToWidth($img_url,620);
			$image[$count]['image_caption'] = $img[1];
			$count++;            
        }
        $data = $param;
        $data['_images'] = $image;
        if(file_exists($template)){
            $out = Engine::templater($template,$data);
        }
        return $out;
	}
}


?>