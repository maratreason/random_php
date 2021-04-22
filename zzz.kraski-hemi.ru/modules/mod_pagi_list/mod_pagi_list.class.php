<?
/*
	Выводит список статей в категории
*/
class mod_pagi_list extends Module{

	public function main($param){

        $this->Init();
        $run = true;
		$cid = Engine::$page['id'];
        if(trim($param['sql_text'])){
            $sql_text = " AND ".trim($param['sql_text']);
        } else {
            $sql_text = "";
        }
		// если вывести надо специальный раздел то выводим его
		if($param['menu_category']!=0) $cid = $param['menu_category'];
		$pages = new Page();
		$count = new Page();
		$unit_name = $param['page_type'];
		
		if(isset($this->GET["page"])){			
			$cur_page = ($this->GET["page"]-1)*$param['count'];
		} else {
			$cur_page = 0;
		}
		if(!isset($_SESSION["articles_sort"])) $_SESSION["articles_sort"] = 0;
		if(isset($_GET['sort'])) {$_SESSION["articles_sort"] = $_GET['sort'];Engine::setRedirect(Engine::$url);}
		
		// проверям сортировку
		
		if(trim($param['sort'])==="") {
			$sort = "id {$param['sort_type']}"; 
		} else {
			$sort = "{$param['sort']} {$param['sort_type']}";
		}
		
	
		$value = array(":parent"=>$cid);
		if($param['category_list']==1){
			// статьи внутри раздела
			if($unit_name) $unit_str = "AND unit = '{$unit_name}'"; else $unit_str = "";
			$pages->sql("SELECT * FROM pages WHERE parent = :parent AND status = 1 {$unit_str} {$sql_text} ORDER BY {$sort} LIMIT {$cur_page}, {$param['count']}",$value);
			$count = $count->sql("SELECT * FROM pages WHERE parent = :parent AND status = 1 {$unit_str} {$sql_text}",$value)->rowCount();
			$pagi = Engine::$page['url'];
		} elseif($param['category_list']==2) {
			// взять список страниц из поля родителя
			$ids = Engine::$page[$param['page_list']];
			$ids = explode(",",$ids);
			$ids = implode("|",$ids);
			if($unit_name) $unit_str = $unit_name; else $unit_str = "*";
			//$pages->sql("SELECT * FROM pages WHERE status = 1 ORDER BY {$sort} LIMIT {$cur_page}, {$param['count']}");
			$pages->get(array("*"=>"*","id"=>$ids,"status"=>1,"unit"=>$unit_str,"sql"=>"{$sql_text} ORDER BY {$sort} LIMIT {$cur_page}, {$param['count']}"));
			$count = $count->sql("SELECT * FROM pages WHERE status = 1 {$sql_text}")->rowCount();

		} elseif($param['category_list']==3){
			if($unit_name) $unit_str = "AND unit = '{$unit_name}'"; else $unit_str = "";
			$pages->sql("SELECT * FROM pages WHERE  status = 1 {$unit_str} {$sql_text} ORDER BY {$sort} LIMIT {$cur_page}, {$param['count']}",$value);
			$count = $count->sql("SELECT * FROM pages WHERE  status = 1 {$unit_str} {$sql_text}",$value)->rowCount();
			$pagi = Engine::$page['url'];
		} 
		// количество страниц в разделе
		$pagi_count = ceil($count/$param['count']);
		// если данные запроса не корректны то 404	
		if(isset($this->GET['page']))
		if($this->GET['page']>$pagi_count){
			Engine::set404();  
			$run = false;
		} 
		
		$out = "";
		$template = Engine::$root.Config::$front_template."/views/{$param['template_list']}.html";
		if(!file_exists($template)){
			$template = Engine::$root.Config::$front_template."/blocks/{$param['template_list']}/view.html";
		}
		if(!file_exists($template)){
			$out = "Файл шаблона не найден";
		}
		else {
			$block_data['_block'] = array();
			$block_data['_mod'] = $param;
			while($data = $pages->next()){
				//$out .= Engine::templater($template,$data);
				$block_data['_block'][] = $data;
			}
			if(count($block_data['_block'])>=0) {
				// добавим к данным раздел с которого берем страницы
				$ppage = new Page();
				$ppage = $ppage->get($cid)->assoc();
				$block_data['_parent'] = $ppage;
				$out .= Engine::templater($template,$block_data); 
				Engine::set404(0);
			} else {
				$out = "";
			}
		}
		
		//пагинация
		if($param['page_flag']){ // если пагинация включена
			if($pagi_count>1){
				$out .= "<div class='pagination'>";
				// кнопка предыдущей страницы
				if(!isset($this->GET['page'])) $this->GET['page'] = 1;
				if($this->GET['page']>1){
					$pri = $this->GET['page']-1;
					/*if($pri==1 &&$pagi=="/articles") $pagi_url = Engine::$page['url']; else */$pagi_url = $pagi;
					if($pri<=1) $out .= "<a href='{$pagi_url}' class='pri-next'><</a>";	else $out .= "<a href='{$pagi_url}/page-{$pri}' class='pri-next'><</a>";
				} else {
					$out .= "<span class='deactive pri-next'><</span>";
				}
				// страницы
				for($i=1;$i<=$pagi_count;$i++){
					if($i == $this->GET['page']) {
						//$class = "class='active'"; else $class = "";
						$out .= "<span class='active'>{$i}</span>";
					} else {
						/*if($pri==1&&$pagi=="/articles") $pagi_url = "/"; else */$pagi_url = $pagi;
						if($i==1) $out .= "<a href='{$pagi_url}'>{$i}</a>"; else $out .= "<a href='{$pagi_url}/page-{$i}'>{$i}</a>";
					}
				}
				// кнопка следующей страницы
				if($this->GET['page']<$count/$param['count']){
					$pri = $this->GET['page']+1;
					$out .= "<a href='{$pagi}/page-{$pri}' class='pri-next'>></a>";
				} else {
					$out .= "<span class='deactive pri-next'>></span>";
				}
				$out .="</div>";
			}
		}
		

		// выведем вид сортировки статей, если установлено значение такой сортировки в модуле
		$sort = "";
		if($param['sort'] === "auto"){
			$sort = '<div class="speed-sort">';
			
			if($_SESSION["articles_sort"] == 0) $class = "class='active'"; else $class = "";
			$sort .= "<a href='?sort=0' {$class} >Последние</a>";
			if($_SESSION["articles_sort"] == 1) $class = "class='active'"; else $class = "";
			$sort .= "<a href='?sort=1' {$class}>Популярные</a>";
			$sort .= '</div>';
		}
		$out = $sort.$out;
		// если это первая страница в пагинации, то выведем текст
		// на внутренних страницах запретим вывод текста, чтобы не писать везде canonical
		/*if(isset($page['new-content']))*/	
        if($param['content_flag']==1&&Engine::$urlAlias&&$run){
            //if(!Engine::$urlAlias&&@Engine::$page['new-content']) $out = "<div class='content'>".Engine::$page['new-content']."</div>".$out;
            Engine::$page['content'] = "";
            
        }
        if(Engine::$urlAlias&&$run&&$param['page_flag']){
            @Engine::$page['title'] .= " / Страница ".$this->GET['page'];
            @Engine::$page['description'] .= " / Страница ".$this->GET['page'];
        }
		
        
		return $out;
		
	}
}


?>