<?
class mod_explode_field extends Module{

	function __construct(){
		
		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_faq_block"; // получим класс
        //скрипты фронтальной части
        if(!Engine::$admin){

        }

        // стандартная инициализация модуля
        $this->Init();
	
	}
	
    public function main($param){
		$out = "";
        $template = Engine::$root.Config::$front_template."/{$param['template_list']}.html";
		$f1 = $param['find_f1']; // строка начала поиска
		$f2 = $param['find_f2']; // строка конца поиска
		$len_f1 = strlen($f1);
		$len_f2 = strlen($f2);
        
		// получаем контент
		if($param['page_id']!=0){
            $page = new Page();
            $page = $page->get($param['page_id'])->assoc();

        } else {
            $page = Engine::$page;
            
        }
		
        $content = str_repeat("-",$len_f1).$page[$param['page_field']];
		$p = 0;
		$flag = 0;
		$block = array();
		$count = 0;
        
		while($flag == 0){
			$p = strpos($content, $f1,$p+$len_f1);
			$p2 = strpos($content,$f2,$p+$len_f1);
			
			if($p2 <=0) {$p2 = strlen($content); $flag=1;}
			$block[] = substr($content, $p, $p2-$p);
			$count++;
			if($count>=$param['block_count']) $flag = 1;
		}
        
		//$data = $param;
		$data = $page;
		$data['_block'] = $block;
		$out = Engine::templater($template, $data);
		
        return $out;
	}
}


?>