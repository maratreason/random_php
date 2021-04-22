<?
class mod_multifilter extends Module{
	function __construct(){

		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_multifilter"; // получим класс
		if(!Engine::$admin){

			// стандартная инициализация модуля
			$this->Init();
		} else {
			// скрипты админской части

		}
	}

	// возвращает количество элементов удовлетворяющий фильтру
	public function getItemsCount($url){
		$prefix = "/only-";
		$filter_full = func::getUrlFilterParams($prefix,$url);
        $action = $filter_full['action']; // страница фильтров
        $filter = $filter_full['filter']; // фильтры в урл

		$apage = new Page();
        $apage = $apage->get(array("url"=>$action))->assoc();
		$parent = $apage['id'];
		$base_filter = array();
		$yaml_file = Engine::$root."/modules/mod_multifilter/filters/{$apage['multifilter']}.yaml";
        if(!file_exists($filter_name)){
            // фильтр по умолчанию
            $filter_name = Engine::$root."/modules/mod_multifilter/filters/main.yaml";
        }
        $yaml = Yaml::YAMLLoad($yaml_file);
        foreach($yaml['filters'] as $col_name=>$params){
            $name = $params['name'];
            if(isset($filter[$name])){
                $base_filter[$col_name]["values"] = $filter[$name]; // сопостави ключ с полем в базе данных
                $base_filter[$col_name]["type"] = $params['etype']; // полуим тип поля фильтра
			}
		}

		// составим запрос
		$sql_count = "SELECT id FROM pages WHERE status = '1' AND parent = '{$parent}' ";
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
                    $values[$col.'0'] = $f['values'][0];
                    $values[$col.'1'] = $f['values'][1];
            }

            //$sql = $sql.$str;
            $sql_count = $sql_count.$str;
        }

		$rescount = new Page();
        $rescount = $rescount->sql($sql_count,$values)->rowCount();
		return $rescount;
	}
    // разбирает урл по параметрам, переданный мультифильтром
    public function getUrlFilterParams($prefix){
        $filter = array();
        $prefix_len = strlen($prefix);
        $pos = strpos(Engine::$url, $prefix);
        //Engine::console(Engine::$url);
        if($pos>0) $url_action = substr(Engine::$url,0,$pos); else $url_action = Engine::$url;
        // получаем строку фильтра
        $url_params = substr(Engine::$url, $pos+$prefix_len);
        $url_params = explode("+", $url_params);
        // создаем массив фильтра
        foreach($url_params as $p){
            $pos = strpos($p, "-");
            $key = trim(substr($p,0,$pos));
            $val = substr($p,$pos+1);
            $val = explode(",",$val);
            $filter[$key] = $val;
        }
        return array("filter"=>$filter, "action"=>$url_action);
    }

	public function main($param){
        $prefix = "/only-";
        $this->setFile("multifilter.js"); // подключим js файл модуля
        $this->setFile("js/icheck.min.js"); //
        $this->setFile("js/skins/all.css"); //
        $this->setFile("js/nouislider.min.js"); //
        $this->setFile("js/nouislider.css"); //
        $out = "";

        // получим параметры фильтра из текущего урл
        $filter = func::getUrlFilterParams($prefix);
        $filter_url = $filter;
        $action = $filter['action'];
        $apage = new Page();
        $apage = $apage->get(array("url"=>$action))->assoc();

        $filter = $filter['filter'];

        $filter_name = Engine::$root."/modules/mod_multifilter/filters/".$apage["multifilter"].".yaml";
        if(!file_exists($filter_name)){
            // фильтр по умолчанию
            $filter_name = Engine::$root."/modules/mod_multifilter/filters/main.yaml";
        }
        $data = array();
        if(file_exists($filter_name)){
            $key = md5($filter_name);
            $cache = Engine::$root."/modules/mod_multifilter/cache/{$key}.html";
            /*if(file_exists($cache))*/{
                $yaml = Yaml::YAMLLoad($filter_name);
                $areas = $yaml['filters'];
                $filter_full = array();
                foreach($areas as $n=>$p){
                    if($p['type']=="multiselect"){
                    $filter_full[] = $p['name'];
                    }
                }
                foreach($areas as $name=>$params){
                    $data = $params;

                    // множественный выбор
                    if($params['type'] == "multiselect"){
                        if(isset($filter[$params['name']])){
                            foreach($filter[$params['name']] as $val){
                                $data['_checked'][$val] = "checked";
                            }
                        }


                        foreach($params['select'] as $v=>$n){
                            // связываем поля в фильтре
                            // для определённых значений будем показывать свой выбор
                            $data['select'][$v]['key'] = "keyname-".$name."-".$v;
                            if(isset($n['key_name'])){
                                $data['select'][$v]['key'] .= " hide keyparent-".$n['key_name']."-".$n['key_value'];
                            }

                            $url = $filter;
                            //уберем из фильтра пагинацию
                            if(isset($url['page'])) unset($url['page']);
                            //уберем из фильтра сортировку
                            $sort = "";
                            if(isset($url['sort'])) {
                                $sort = "sort-".$url['sort'][0];
                                unset($url['sort']);
                            }
                            // если фильтр содержит текущее значение, то уберём его
														$seturl = true;
                            if(!isset($url[$params['name']])) $url[$params['name']] = array(); else $seturl = false;
                            $key = array_search($v, $url[$params['name']]);
                            if($key===false){
                                $url[$params['name']][] = $v;
                            } else {
                                unset($url[$params['name']][$key]);
                            }

                            $url[$params['name']] = array_unique($url[$params['name']]);
                            ksort($url);
                            sort($url[$params['name']]);
                            // удалим пустой фильтр
                            if(count($url[$params['name']])<=0) unset($url[$params['name']]);
                            $furl = "";

                            foreach($url as $key=>$values){
                                $furl .= $key."-".implode(",",$values)."+";
                            }
                            $furl = substr($furl,0,-1);
                            // добавим сортировку если есть
                            if($sort) $furl .="+".$sort;
                            // добавим урл
							$filter_full = func::getUrlFilterParams($prefix);

							$action = $filter_full['action']; // страница фильтров
							$filter = $filter_full['filter']; // фильтры в урл
							// получаем из фильтра текущую страницу
							if(isset($filter['page'][0])) $page_nomer = $filter['page'][0];
							// получим урл параметров фильтра

							//$filter_url = func::clearFilterUrl($filter_full,"page",$prefix);

							// раздел фильтра
							/*$_POST['url'] = Engine::$page['url'];
							include(Engine::$root."/modules/mod_multifilter_list/mod_multifilter_list.class.php");
							$count = mod_multifilter_list::countItems();*/
                            //$count = mod_multifilter_list::ajax();

							$furl = $action.$prefix.$furl;

							//$count = 1;


							if($seturl===false){
								$data['_url'][$v] = "";
							} else {

								$data['_url'][$v] = "";
								if(count($url)<=0)
								{
									$data['_url'][$v] = $furl;
									// проверим кешированы ли данные в базе
									/*$base = new DataBase();
									$base->setTable('itemcount');
									$bitem = $base->get(array("md5"=>md5($furl)))->assoc();
									// проверим истекло ли время кеширования данных
									$flag = true;
									if(isset($bitem['id'])){
										$time = time()-strtotime($bitem['date']);
										//Engine::console($time);
										if($time<60*60*24*7) $flag = false;
									}
									if($flag){
										$count = self::getItemsCount($furl);
										// закешируем результат в базу если элементы есть по данному урл
										if($count>0) func::setItemCount($furl,$count);
									} else {$count = $bitem['count'];}

									if($count>0){
										$data['_url'][$v] = $furl;
										$data['select'][$v]['caption'] = $n['caption']." ({$count})";
									} else {
										$data['_url'][$v] = "";
									}*/

								}

							}

							// показывает количество элеметнов
							// очень долгая загрузка
							//$count = self::getItemsCount($furl);
							//$data['select'][$v]['caption'] = $n['caption']." ({$count})";
                        }
                    }


                    // диапазон
                    if($params['type'] == "range"){
                        // найдём максимальное числовое значение
                        $r = new Page();
  											$parent_page = func::getUrlFilterParams($prefix);
												// раздел фильтра
								        $apage = new Page();
								        $apage = $apage->get(array("url"=>$parent_page['action']))->assoc();
												$parent_id = $apage['id'];


                        $min = $r->sql("SELECT {$name} FROM pages WHERE parent = {$parent_id} ORDER BY {$name} ASC LIMIT 1")->all();
                        $max = $r->sql("SELECT {$name} FROM pages WHERE parent = {$parent_id} ORDER BY {$name} DESC LIMIT 1")->all();

                        $data['_value'][0] = 0;
                        $data['_value'][1] = 100000; // максимально значение для рендж
                        if(isset($min[0][$name])&&$min[0][$name]>=0) $data['_value'][0] = $min[0][$name];
                        if(isset($max[0][$name])&&$max[0][$name]>0) $data['_value'][1] = $max[0][$name];
                        //$data['_value'] = explode("-",$params['minmax']);
                        $data['_min'] = $data['_value'][0];
                        $data['_max'] = $data['_value'][1];
                         if(isset($filter[$params['name']])){
                             $v1 = $filter[$params['name']][0];
                             $v2 = $filter[$params['name']][1];
                             // так как стоит сортировка значений, то диапазоны могу поменяться местами
                             // проверим это и сделаем корректировку
                             if($v1<$v2) {
                                $data["_value"] = array($v1,$v2);
                             } else {
                                $data["_value"] = array($v2,$v1);
                             }
                         }
                    }

                    $out .= Engine::templater(Engine::$root."/modules/mod_multifilter/{$params['type']}.html", $data);
                }
                $data['filter'] = $out;
                $data['action'] = $action;
                // собираем фильтр
                $out = Engine::templater(Engine::$root."/modules/mod_multifilter/filter.html", $data);
                // добавим фильтр в кеш
                $fp = fopen($cache, "w");
                fwrite($fp, $out);
                fclose($fp);
            } /*else {
                // возьмем из кеша
                $out = file_get_contents($cache);
            }*/
        }
        else {
            Engine::errorMod($param, "Файл фильтра не найден");
        }
		return $out;
	}
}


?>
