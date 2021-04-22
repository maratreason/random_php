<?
/*
	Выводит товаров по фильтру
    Используется совместно с модулем mod_multifilter
*/
class mod_multifilter_list extends Module{
	static $ajax = false;
	static $filterArr = array(); // массив параметров товара для скрывания значений в самом фильтре
	static $filterCount = 0;
    // убирает из фильтра ненужные параметра фильтра
    function clearFilterUrl($filter,$params,$prefix){
        $params = array_flip(explode(",",$params));
        $nofirst = 0;
        $clear = "";
        foreach($filter['filter'] as $key=>$val){ 
            if(!isset($params[$key])){ // не учитываем в фильтре пагинацию
                $val = implode(",",$val);
                if($nofirst==1) $clear .="+";
                $clear .= "{$key}-{$val}";
                $nofirst = 1;
            }
        }

        if($clear==""){
            $clear = $filter['action'];
        } else {
            $clear = $filter['action'].$prefix.$clear;
        }
        return $clear;
    }

    // обработчик ajax событий
	public function ajax(){
		self::$ajax = true;
        $this->main();
	}
	public function countItems(){
		self::$ajax = true;
        return $this->main();
	}
    public function main($param=0){
        $prefix = "/only-";
        $max_items = $param['count']; // количество товаров на странице
        $page_nomer = 1; // текущая страница
        $page_count = 0; // сколько всего страниц
        $filter_url = ""; //урл фильтра
        $seo_title = array();
        $seo_desc = array();
		$seo_h = array();
		$labels = array();
        $empty_filter = false;
        $this->Init();
        Engine::set404(false);

        $out = "";
        if(!self::$ajax){
            $filter_full = func::getUrlFilterParams($prefix);
        } else {
            // для ajax запроса урл возьмем с POST
			$ajax_url = func::getFilterUrl($_POST);
			//echo $ajax_url;
            $filter_full = func::getUrlFilterParams($prefix, $ajax_url);
        }

        $action = $filter_full['action']; // страница фильтров
        $filter = $filter_full['filter']; // фильтры в урл
        // получаем из фильтра текущую страницу
        if(isset($filter['page'][0])) $page_nomer = $filter['page'][0];
        // получим урл параметров фильтра

        $filter_url = self::clearFilterUrl($filter_full,"page",$prefix);

        $item_template = Engine::$root.Config::$front_template."/views/{$param['item_template']}.html";

        // раздел фильтра
        $apage = new DataBase('pages');
        $apage = $apage->get(array("*"=>"id,multifilter","url"=>$action))->assoc();
        
        $parent = $apage['id'];
        // если указан точный родитель, то выберем его
        if($param['category']!=0){
            $parent = $param['category'];
        }
        //
        $items = array();

        $base_filter = array();
		if(trim($apage['multifilter'])!=""){
        	$yaml_file = Engine::$root."/modules/mod_multifilter/filters/{$apage['multifilter']}.yaml";
		} else {
			$yaml_file = Engine::$root."/modules/mod_multifilter/filters/filter_".trim(@$apage['child_units']).".yaml";
		}

        if(!file_exists($yaml_file)){
            // фильтр по умолчанию
            $yaml_file = Engine::$root."/modules/mod_multifilter/filters/main.yaml";
        }
        $yaml = Yaml::YAMLLoad($yaml_file);
        $filtercode = $yaml;
        if(file_exists($yaml_file)){
        foreach($yaml['filters'] as $col_name=>$params){
            $name = $params['name'];
            if(isset($filter[$name])){
                $base_filter[$col_name]["values"] = $filter[$name]; // сопостави ключ с полем в базе данных
                $base_filter[$col_name]["type"] = $params['etype']; // полуим тип поля фильтра
                // Соберем все ключевые слова по которым осуществляется фильтр
                // описания для множественого выбора
                if($params['type']=="multiselect"){
                    $title = array();
                    $desc = array();
					$h = array();
                    foreach($filter[$name] as $val){
                        $txt = $params['select'][$val]['title'];
                        //if(!$txt) $txt = $params['select'][$val]['caption'];
                        $title[] = $txt;
                        $txt = $params['select'][$val]['desc'];
                        //if(!$txt) $txt = $params['select'][$val]['caption'];
                        $desc[] = $txt;
						// переменные берём из поля title
                        $txt = $params['select'][$val]['h1'];
                        //if(!$txt) $txt = $params['select'][$val]['h1'];
                        $h[] = $txt;
                    }
                    // формируем title
                    if(count($title)>0){
                        if($params['title_start']!="") $params['title_start'].=" ";
                        if($params['title_end']!="") $params['title_end'] = " ".$params['title_end'];
                        $out_title = "";
                        // если указывают два параметра поставим и, так красивый
                        if(count($title)==2) $out_title = trim(implode(" и ",$title)); else $out_title = trim(implode(", ",$title));
                        $seo_title[] = $params['title_start'].$out_title.$params['title_end'];
                    }
                    
                    // формируем description
                    if(count($desc)>0){
                        if($params['desc_start']!="") $params['desc_start'].=" ";
                        if($params['desc_end']!="") $params['desc_end'] = " ".$params['desc_end'];
                        $out_desc = "";
                        // если указывают два параметра поставим и, так красивый
                        if(count($desc)==2) $out_desc = trim(implode(" и ",$desc)); else $out_desc = trim(implode(", ",$desc));
                        $seo_desc[] = $params['desc_start'].$out_desc.$params['desc_end'];
                    }
					// формируем заголовок h1
                    if(count($h)>0){
                        if($params['h1_start']!="") $params['h1_start'].=" ";
                        if($params['h1_end']!="") $params['h1_end'] = " ".$params['h1_end'];
                        $out_h = "";
                        // если указывают два параметра поставим и, так красивый
                        if(count($h)==2) $out_h = trim(implode(", ",$h)); else $out_h = trim(implode(", ",$h));
                        if($params['h1_show']) $seo_h[] = $params['h1_start'].$out_h.$params['h1_end'];
                    }
                }
                // описание для диапазона
                if($params['type']=="range"){
                        $title = array();
                        $v1 = $filter[$name][0]; // от
                        $v2 = $filter[$name][1]; // до
                        // так как значения все сортируются то определим какой из значений от и какое до
                        if($v1<$v2) {$val1 = $v1; $val2 = $v2;} else {$val1 = $v2; $val2 = $v1;}
                        $max = explode("-",$params['minmax']);
                        $max = @$max[1];
                        if(!isset($params['title_start'])) $params['title_start']="" ; else $params['title_start'].=" ";
                        if(!isset($params['title_end'])) $params['title_end']=""; else $params['title_end'] = " ".$params['title_end'];
                        if(!isset($params['desc_start'])) $params['desc_start']="" ; else $params['desc_start'].=" ";
                        if(!isset($params['desc_end'])) $params['desc_end']=""; else $params['desc_end'] = " ".$params['desc_end'];
						if(!isset($params['h1_start'])) $params['h1_start']="" ; else $params['h1_start'].=" ";
                        if(!isset($params['h1_end'])) $params['h1_end']=""; else $params['h1_end'] = " ".$params['h1_end'];
                        if($val1<=0||$val1==""){
                            $seo_title[] = $params['title_start']."до ".$val2.$params['title_end'];
							$seo_desc[] = $params['desc_start']."до ".$val2.$params['desc_end'];
							if($params['h1_show'])	$seo_h[] = $params['h1_start']."до ".$val2.$params['h1_end'];
                        } elseif($val2>=$max||$val2==""){
                            $seo_title[] = $params['title_start']."от ".$val1.$params['title_end'];
							$seo_desc[] = $params['desc_start']."от ".$val1.$params['desc_end'];
							if($params['h1_show'])	$seo_h[] = $params['h1_start']."от ".$val1.$params['h1_end'];
                        } else {
                            $seo_title[] = $params['title_start']."от ".$val1." до ".$val2.$params['title_end'];
							$seo_desc[] = $params['desc_start']."от ".$val1." до ".$val2.$params['desc_end'];
							if($params['h1_show'])	$seo_h[] = $params['h1_start']."от ".$val1." до ".$val2.$params['h1_end'];
                        }
                }

            }
        }
        } else {
            Engine::errorMod($param,"Файл фильтра не найден");
        }

        // найдём все разделы, чтобы в дальнешйем искать карточки в этих разделах
        // необходим для составления фильтра для всех товаров в категориях
	/*
        $sql = "SELECT id FROM pages WHERE status = '1' AND type = 1 AND parent = '{$parent}'";
        $cat = new Page();
        $cat = $cat->sql($sql)->all();
        $cat_id_sql = "";
        if (count($cat) > 0) {
            $cat_id_sql = "(";
            foreach ($cat as $i => $c) {
                if ($i > 0) $cat_id_sql .= " OR ";
                $cat_id_sql .= "parent = {$c['id']}";
            }
            $cat_id_sql .= ")";
        } else {
            $cat_id_sql = "parent = '{$parent}'";
        }

        $sql = "SELECT * FROM pages WHERE status = '1' AND type = 0 AND {$cat_id_sql} ";
        $sql_count = "SELECT id FROM pages WHERE status = '1' AND {$cat_id_sql} ";
*/
		// получим только определённые поля, чтобы ускорить выборку
        if(trim($param['item_fields'])!="") $fields = $param['item_fields']; else $fields = "*";
    
		// multi_images, price_float, price_float_unic, url_name, unit, opt_nagruzka, url, opt_mass, url_desc, opt_height, opt_width, opt_depth,item_discounts

        // заберем один элемент чтобы знать какие поля нужно вытащить
		$tPage = new Page();
        $tPage = $tPage->sql("SELECT unit_str FROM pages WHERE parent = :parent LIMIT 1", array("parent"=>$parent))->assoc();
        
		$tYaml = unserialize($tPage['unit_str']);
        $yamlFields = array();
        if(isset($tYaml['areas']))
		foreach ($tYaml['areas'] as $key => $value) {
			$yamlFields[] = $key;
        }
        // id
        $yamlFields[] = "id";
		$fields = implode(",", $yamlFields);

		$sql = "SELECT {$fields} FROM pages WHERE status = '1' AND type = 0 AND parent = {$parent}";
        $sql_count = "SELECT id FROM pages WHERE status = '1' AND parent = {$parent}";

        $values = array();
        foreach($base_filter as $col=>$f){

            $str = "";

            // одно значение в базе
            if($f['type']=="select"){
                $str .= " AND ";
                $str .= "(";
                foreach($f['values'] as $i=>$val){
                    if($i>0) $str.=" OR ";
                    $str = $str."{$col} = :{$col}{$i}";
                    $values[$col.$i] = $val;
                }
                $str .= ")";
            }

            // несколько значений в базе
            if($f['type']=="multiselect"){
                $str .= " AND ";
                $str .= "(";
                foreach($f['values'] as $i=>$val){
                    if($i>0) $str.=" OR ";
                    $str = $str."{$col} LIKE :{$col}{$i}";
                    $values[$col.$i] = "%,{$val},%";
                }
                $str .= ")";
            }

            // числовое значение в базе
            if($f['type']=="integer"){
                    $str .= " AND ";
                    $str .= "(";
                    $str = $str."{$col} >= :{$col}0 AND {$col} <= :{$col}1";
                    $str .= ")";
                    if($f['values'][0]<$f['values'][1]){
                        $values[$col.'0'] = $f['values'][0];
                        $values[$col.'1'] = $f['values'][1];
                    } else {
                        $values[$col.'0'] = $f['values'][1];
                        $values[$col.'1'] = $f['values'][0];
                    }

            }

            $sql = $sql.$str;
            $sql_count = $sql_count.$str;
        }

        // получим количество записей
        $rescount = new Page();
        $rescount = $rescount->sql($sql_count,$values)->rowCount();
		self::$filterCount = $rescount;
        // если ajax запрос то покажем ток число найденых элементов
        if(mod_multifilter_list::$ajax){
			// проверим есть ли копия страницы по такому урл
			$copy = new Page();
			$copy = $copy->get(array("*"=>"url",'url_link'=>$ajax_url))->assoc();
			if($copy){
				$ajax_url = $copy['url'];
			}
            echo "{\"count\":{$rescount},\"url\":\"{$ajax_url}\"}";
			//print_r($filter);
			//echo $ajax_url;
			return 0;
        }

        // работаем с сортировкой
        $sort="";
        $sort_sql = "";
        $clear_url = self::clearFilterUrl($filter_full,"sort,page", $prefix);
        if($rescount>0){

        if(trim($param['item_sort'])!=""){

            $sort = "<div class='item-sort'>";
            $sortjs = json_decode($param['item_sort']);
            foreach($sortjs as $i=>$line){
                // выделим текущую сортировку
                // таковой является если она указана в урл, если не указана выделяем первый вид сортировки и используем по умолчанию её
                if(@$filter['sort'][0]==$line[0]||($i==0&&!isset($filter['sort']))){
                    $sort .= "<span class='button small select'>{$line[2]}</span>";
                    // указываем запрос в базу для сортировки
                    $sort_sql = " ORDER BY {$line[1]}";
                } else {
                    // для фильтра по умолчанию не будем выводить в урл сортировку
                    if($i!=0){
                        if($clear_url==$action){
                            $sort .= "<a class='button small' href='{$clear_url}{$prefix}sort-{$line[0]}'>{$line[2]}</a>";
                        }
                        else {
                            $sort .= "<a class='button small' href='{$clear_url}+sort-{$line[0]}'>{$line[2]}</a>";
                        }
                    } else {
                        // фильтр по умолчанию не вставляет в урл параметр сортировка,
                        // система сама понимает что сортировать надо по первому указанному полю
                        $sort .= "<a class='button small' href='{$clear_url}'>{$line[2]}</a>";
                    }
                }
            }
            $sort .="</div>";
        }
        }
        $page_count = ceil($rescount/$max_items);
        // получим только записи текущей страницы
        $start_item = ($page_nomer-1)*$max_items;
		$sqlAll = $sql.$sort_sql."";
		$sql = $sql.$sort_sql." LIMIT {$start_item}, {$max_items}";

		//Engine::console(addslashes($sqlAll));
		//echo "<script>console.log('".quotemeta($sql)."');</script>";
        $childs = new Page();
		//$data = $childs->sql($sqlAll,$values)->all();
        $data = $childs->sql($sql,$values)->all();
        $dataAll = $childs->sql($sqlAll,$values)->all();
        //$data = array();
        $nom = 0;
        $filter_name = Engine::$root."/modules/mod_multifilter/filters/".$apage["multifilter"].".yaml";
        $areas = array();
        if(file_exists($filter_name)){
            $yaml = Yaml::YAMLLoad($filter_name);
            $areas = $yaml['filters'];
        }
//		self::$filterArr = $data[0];
		// составим массив всех параметров всстречающихся в карточках
		foreach($dataAll as $item){
            //Engine::console("[{$item['url_name']}]");
            foreach($item as $name=>$area){
                if(($area!=''||$area!=",")&&strpos($name,"opt_")!==false){
                    if(!isset(self::$filterArr[$name])) self::$filterArr[$name] = '';
                    self::$filterArr[$name].=$area;
                    //Engine::console($name."=".$area);
                }
            }
            //Engine::console("-------------");
        }
        // собираем шаблон
        $out = $sort.Engine::templater($item_template, $data);

        //пагинация
        $param['page_flag'] = 1;
        // если пагинация включена
        if($param['page_flag']){
            // если товаров больше чем может уместится на одной странице
            if($rescount>$max_items){
                $out .= "<div class='pagination'>";
                // кнопка предыдущей страницы
                //if(!isset($this->GET['page'])) $this->GET['page'] = 1;
                if($page_nomer>1){
                   $pri = $page_nomer-1;
                    $url = "{$filter_url}+page-{$pri}";
                    if($filter_url==$action) $url = "{$filter_url}{$prefix}page-{$pri}";
                    if($pri<=1) $out .= "<a href='{$filter_url}' class='page-prev'><</a>"; else $out .= "<a href='{$url}' class='page-prev'><</a>";
                } else {
                    $out .= "<span class='deactive page-prev'><</span>";
                }
                // страницы
                $out .= "<div class='pages-nomer'>";
                for($i=1;$i<=$page_count;$i++){
                    if($i == $page_nomer) {
                        //$class = "class='active'"; else $class = "";
                        $out .= "<span class='active'>{$i}</span>";
                    } else {
                        /*if($pri==1&&$pagi=="/articles") $pagi_url = "/"; else $pagi_url = $pagi;*/
                        $url = "{$filter_url}+page-{$i}";
                        if($filter_url==$action) $url = "{$filter_url}{$prefix}page-{$i}";
                        if($i==1) $out .= "<a href='{$filter_url}'>{$i}</a>"; else $out .= "<a href='{$url}'>{$i}</a>";
                    }
                }
                $out .= "</div>";
                // кнопка следующей страницы
                if($page_nomer<$page_count){
                    $next = $page_nomer+1;
                    $url = "{$filter_url}+page-{$next}";
                    if($filter_url==$action) $url = "{$filter_url}{$prefix}page-{$next}";
                    $out .= "<a href='{$url}' class='page-next'>></a>";
                } else {
                    $out .= "<span class='deactive page-next'>></span>";
                }
                $out .="</div>";
            }
        }


        // доводим SEO
        // если страница фильтра существует в системе а не является виртуальной, то попробуем взять описания со страницы
        if(Engine::$urlAlias!=""){
            /*if(Engine::$page['title']=="")*/ if($filter_url!=$action) Engine::$page['title'] 			= preg_replace("/\s{2,}/",' ',$filtercode['title_start']." ".implode(" ",$seo_title)." ".$filtercode['title_end']);
            /*if(Engine::$page['description']=="")*/   if($filter_url!=$action) Engine::$page['description'] 	= preg_replace("/\s{2,}/",' ',$filtercode['desc_start']." ".implode(" ",$seo_desc)." ".$filtercode['desc_end']);
            /*if(Engine::$page['content_title']=="") */if($filter_url!=$action) Engine::$page['content_title'] 	= preg_replace("/\s{2,}/",' ',$filtercode['h1_start']." ".implode(", ",$seo_h)." ".$filtercode['h1_end']);
			
			/*if(@Engine::$page['filter_labels']=="")*/ if($filter_url!=$action) Engine::$page['filter_labels'] = $seo_h;
            
        }
        if($page_nomer>1) {
            // добавим тег каноникл на основную страницу, избавляемся от лишних дублей в выдаче
            Engine::addMeta("<link rel='canonical' href='{$clear_url}'/>");
            // добавим в титлы номер страницы
            Engine::$page['title'] .= " / Страница {$page_nomer}";
        }
        //if(isset($filter['sort'])){
			// проверим есть ли копия этой страницы для фильтра
			$page = new Page();
			$page = $page->get(array("url_link"=>$clear_url,"id"=>"!".Engine::$page['id']))->assoc();
			if(isset($page['id'])){
    	        $clear_url = $page['url'];
	            Engine::addMeta("<link rel='canonical' href='{$clear_url}'/>");
            }
        //}
		// запретим индексацию страницы если она не возвращает элементы
        if($rescount<=0){
			Engine::addMeta('<meta name="robots" content="noindex, nofollow">');
		} else {
			// добавим в базу страницу с количеством записей для кеширования
			//func::setItemCount(Engine::$fullUrl,$rescount);
		}
		return $out;

	}
}


?>
