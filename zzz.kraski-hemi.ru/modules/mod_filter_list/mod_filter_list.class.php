<?
/*
	Выводит список статей в категории
*/
class mod_filter_list extends Module{

	public function main($param){
		
		$this->Init();
        $run = true;
		$cid = Engine::$page['id'];
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
		if($param['sort'] === "auto")
		{			
			if($_SESSION["articles_sort"] == 1){
				$sort = "popular DESC"; // сортируем по популярности
				$_SESSION["articles_sort"] = 1;
			} else {
				$_SESSION["articles_sort"] = 0;
				$sort = "content_date DESC"; // сортируем по дате
			}
		} else { // сортируем жестко, согласно настройкам модуля
			$sort = $param['sort']." DESC";
		}
		
		// проверяем есть ли текущая страница для работы модуля
		if(!is_numeric($cur_page)||$cur_page<0||(Engine::$url!=Engine::$urlAlias&&Engine::$urlAlias&&!isset($this->GET['page']))) {
			Engine::set404(); //страница не существует
            $run = false;
		} else
		{
	
			$value = array(":parent"=>$cid);
			if($param['category_list']==1&&(Engine::$page['unit']!="catalog")){
				// статьи внутри раздела
                $sid = $cid;
				$pages->sql("SELECT * FROM pages WHERE (parent = :parent OR content_tags LIKE '%,$sid,%') AND status = 1 AND unit = '{$unit_name}' ORDER BY {$sort} LIMIT {$cur_page}, {$param['count']}",$value);
				$count = $count->sql("SELECT * FROM pages WHERE (parent = :parent OR content_tags LIKE '%,$sid,%') AND status = 1 AND unit = '{$unit_name}'",$value)->rowCount();
				$pagi = Engine::$page['url'];
			} else {
				// все статьи
				$pages->sql("SELECT * FROM pages WHERE status = 1 AND unit = '{$unit_name}' ORDER BY {$sort} LIMIT {$cur_page}, {$param['count']}");
				$count = $count->sql("SELECT * FROM pages WHERE status = 1 AND unit = '{$unit_name}'")->rowCount();
				// если задан свой алиас для генерации страниц пагинации, то ставим его
				if(trim($param['alias_page'])!="") {
					$pagi = trim($param['alias_page']);
				} else {
					$pagi = Engine::$page['url'];
				}
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
			$template = Engine::$root.Config::$front_template."/{$param['template_list']}.html";
			if(!file_exists($template)){
				$out = "<p>Шаблон вывода не найден</p>";
			} else {
                $block_data['_block'] = array();
                while($data = $pages->next()){
				    //$out .= Engine::templater($template,$data);
                    $block_data['_block'][] = $data;
                }
                if(count($block_data['_block'])>0) {
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
        if(Engine::$urlAlias&&$run){
            Engine::$page['title'] .= " / Страница ".$this->GET['page'];
            Engine::$page['description'] .= " / Страница ".$this->GET['page'];
        }
		
        
		return $out;
		
	}
}


?>