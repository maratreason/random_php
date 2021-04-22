<?php
class Func {

	// создает уменьшенную картинку задданной ширины
	private function imageConvector($img, $width, $height, $scale = 0){

		if(file_exists(Engine::$root.$img)&&$img!=""){

			$dir = dirname($img);
			$name = basename($img);
			if($width>0){
				$new_dir = "w{$width}";
			} elseif($height>0){
				$new_dir = "h{$height}";
			} elseif($width<=0&&$height<=0&&$scale>0){
				$new_dir = "s{$scale}";
			}

			$new_image = "{$dir}/preview-{$new_dir}/{$name}";
			if(!file_exists(Engine::$root.$new_image)){
				// создадим отдельную папку для картинок
				if(!file_exists(Engine::$root."{$dir}/preview-{$new_dir}")) mkdir(Engine::$root."{$dir}/preview-{$new_dir}", 0700);
				$image = new SimpleImage();
				$image->load(Engine::$root.$img);
				if($width>0&&$height>0){
					$image->resize($width,$height);
				} elseif($width>0){
					$image->resizeToWidth($width);
				} elseif($height>0){
					$image->resizeToHeight($height);
				} elseif($scale>0){
					$image->scale($scale);
				}
				$image->save(Engine::$root.$new_image);
			}
        } else {
			$new_image = "";
		}
		return $new_image;
	}

	public function imageToWidth($img,$width){
		return Func::imageConvector($img,$width,0);
	}

	public function imageToHeight($img,$height){
		return Func::imageConvector($img,0, $height);
	}

	public function imageScale($img,$scale){
		return Func::imageConvector($img,0,0,$scale);
	}

    // перевод в транслит для чпу
    public function translit($s) {
      $s = (string) $s; // преобразуем в строковое значение
      $s = strip_tags($s); // убираем HTML-теги
      $s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
      $s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
      $s = trim($s); // убираем пробелы в начале и конце строки
      $s = function_exists('mb_strtolower') ? mb_strtolower($s,'utf-8') : strtolower($s); // переводим строку в нижний регистр (иногда надо задать локаль)
        //$s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
        // транслит под Яндекс
        // a-b-v-g-d-e-yo-zh-z-i-y-k-l-m-n-o-p-r-s-t-u-f-h-c-ch-sh-shch--y--e-yu-ya
      $s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo','ж'=>'zh','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
      $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
      $s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
      return $s; // возвращаем результат
    }

	// преобразование в русскую даьу
	public function rusDate() {
	// Перевод
	 $translate = array(
		 "am" => "дп",
		 "pm" => "пп",
		 "AM" => "ДП",
		 "PM" => "ПП",
		 "Monday" => "Понедельник",
		 "Mon" => "Пн",
		 "Tuesday" => "Вторник",
		 "Tue" => "Вт",
		 "Wednesday" => "Среда",
		 "Wed" => "Ср",
		 "Thursday" => "Четверг",
		 "Thu" => "Чт",
		 "Friday" => "Пятница",
		 "Fri" => "Пт",
		 "Saturday" => "Суббота",
		 "Sat" => "Сб",
		 "Sunday" => "Воскресенье",
		 "Sun" => "Вс",
		 "January" => "Января",
		 "Jan" => "Янв",
		 "February" => "Февраля",
		 "Feb" => "Фев",
		 "March" => "Марта",
		 "Mar" => "Мар",
		 "April" => "Апреля",
		 "Apr" => "Апр",
		 "May" => "Мая",
		 "May" => "Мая",
		 "June" => "Июня",
		 "Jun" => "Июн",
		 "July" => "Июля",
		 "Jul" => "Июл",
		 "August" => "Августа",
		 "Aug" => "Авг",
		 "September" => "Сентября",
		 "Sep" => "Сен",
		 "October" => "Октября",
		 "Oct" => "Окт",
		 "November" => "Ноября",
		 "Nov" => "Ноя",
		 "December" => "Декабря",
		 "Dec" => "Дек",
		 "st" => "ое",
		 "nd" => "ое",
		 "rd" => "е",
		 "th" => "ое"
		 );
		 // если передали дату, то переводим ее
		 if (func_num_args() > 1) {
		 $timestamp = func_get_arg(1);
		 return strtr(date(func_get_arg(0), $timestamp), $translate);
		 } else {
		// иначе текущую дату
		 return strtr(date(func_get_arg(0)), $translate);
		 }
	}
		// преобразует массив из Ajax запроса в вид который понимает модуль фиьтра
		public function getFilterUrl($_data){
			$url = "";
			$action = $_data['action'];
			$filter = $_data['filter'];
			ksort($filter);
			$columns = array();
			// загружаем файл фильтра
			$page = new Page();
			$apage = $page->get(array("url"=>$action))->assoc();
			$yaml = Engine::$root."/modules/mod_multifilter/filters/".$apage["multifilter"].".yaml";
			if(!file_exists($yaml)) $yaml = Engine::$root."/modules/mod_multifilter/filters/main.yaml"; // фильтр по умолчанию если не найден указанный
			$yaml = Yaml::YAMLLoad($yaml);

			// добавим по NAME поле из базы
			foreach ($yaml['filters'] as $key => $value) {
				if(array_key_exists($value['name'], $filter)){
					$columns[$value['name']] = $key;
				}
			}

			foreach ($filter as $key => $value) {
				$add = "";
				$column = $columns[$key];
				if($yaml['filters'][$column]['type']=="range"){
					// находим мин и максимум
					$parent = $page->get(array("url"=>$action))->assoc();
					$min = $page->sql("SELECT {$column} FROM pages WHERE parent = {$parent['id']} ORDER BY {$column} ASC LIMIT 1")->assoc();
					$max = $page->sql("SELECT {$column} FROM pages WHERE parent = {$parent['id']} ORDER BY {$column} DESC LIMIT 1")->assoc();
					$min = $min[$column];
					$max = $max[$column];

					if($value[0]>$min||$value[1]<$max) $add = $key."-".implode(",", $value);
				} else {
					$add = $key."-".implode(",", $value);
				}
				if($add){
					if($url!="") $url .= "+";
					$url .= $add;
				}
			}

			return $action."/only-".$url;
		}

    // разбирает урл по параметрам, переданный мультифильтром
    public function getUrlFilterParams($prefix, $url=""){
        $filter = array();
        $prefix_len = strlen($prefix);
        if($url==""){
            $url = Engine::$url;
        }
        $pos = strpos($url, $prefix);
        if($pos>0) $url_action = substr($url,0,$pos); else $url_action = $url;
        // получаем строку фильтра
        if($pos>0){
        $url_params = substr($url, $pos+$prefix_len);
        $url_params = explode("+", $url_params);
        // создаем массив фильтра
        foreach($url_params as $p){
            if($p!=""){
                $pos = strpos($p, "-");
                $key = trim(substr($p,0,$pos));
                $val = substr($p,$pos+1);
                $val = explode(",",$val);
                $filter[$key] = $val;
            }
        }
        }
        return array("filter"=>$filter, "action"=>$url_action);
    }

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
    // получает все поля юнита включая наследуемые юниты
    public function getUnitAreas($file){
        $yaml = Yaml::YAMLLoad($file);
        $un = $yaml;
		if(isset($yaml['extends'])){
			$ext = $yaml['extends'];
			$ext = explode(",",$ext);
			$ext_areas = array();
			foreach($ext as $unit){
				$unit = trim($unit);
				$yaml = Yaml::YAMLLoad(Engine::$root."/units/page/{$unit}.yaml");
				foreach($yaml['areas'] as $col=>$area){
					if(!isset($area['systemtype'])){
						$ext_areas[$col] = $area;
					}
				}

			}

			$areas = array_merge($ext_areas,$un['areas']);
			$areas = array_merge($un,array("areas"=>$areas));
		} else {
			$areas = $un; // для ЮНИТОВ которые не содержат расширений
		}
        return $areas;
    }

    // получает названия всех полей
    public function getUnitNames($unit){
        $names = array();
        if($unit){
            $str = unserialize($unit['unit_str']);
            foreach($str['areas'] as $col=>$param){
                $names[$col] = $param['caption'];
            }
        }
        return $names;
    }


	// присваивает переменной значение по порядку пока не встретим не пустое значение
    public function getStrDefault($unit){
		$out = "";
		$values = func_get_args();
		foreach($values as $value){
			if(trim($value)!=""){$out = $value;break;}
		}
		return $out;
	}

    // переводит все значения в базе с названиями указанными в юните
    // второй параметр задает условие поиска, типа поле=значение - будет выбирать только те поля у которых совпадает данное условие
    // вернет массив
    // $a[название колонки][_val] переведенные значения поля
    // $a[название колонки][_name] переведенное название поля
	// $a[название колонки][_group] переведенное название поля
    public function unitParamName($row,$find="none=none"){
        $find = explode("=",$find);
        $yaml = unserialize($row['unit_str']);
        $item = array();
		$group = array(); // группы
		$gr_name = "";
        // заменим в мультиселекте значение на названия
        foreach($yaml['areas'] as $col=>$area){
            if(@$area[$find[0]]==trim($find[1])||($find[0]=="none"&&$find[1]=="none")){

			if(!isset($area['group'])) $area['group'] = '0';
			$gr_name = $area['group'];
			//$item[$col] = $area;
            if($area['editor']==="multiselect"||$area['editor']==="select"||$area['editor']==="multiselectgroup"){
                $vals = explode(",",@$row[$col]);
                $vals = array_diff($vals, array(''));
                $cap = $area['select'];
                $new = array();
                foreach($vals as $val){
                    // если имеется такое значение то ставим его иначе оставляем поле пустым
                    if(isset($cap[trim($val)])) $new[] = $cap[trim($val)]; else $new[] = "";
                }
                //$item['_val'][$col] = implode(", ",$new);
//				$item[$col]['_val'] = implode(", ",$new);
				$area['_val'] = implode(", ",$new);
            } else{
                // для всех других полей
                // если имеется такое значение то ставим его
                //if(isset($row[$col])) $item['_val'][$col] = $row[$col];
				//if(isset($row[$col])) $item[$col]['_val'] = $row[$col];
				if(isset($row[$col])) $area['_val'] = $row[$col];
            }
			$group[$gr_name]['_items'][$col] = $area;
			$group[$gr_name]['_group'] = "";
			if(isset($yaml['groups'][$gr_name])) $group[$gr_name]['_group'] = $yaml['groups'][$gr_name];
            /*$item['_name'][$col] = $area['caption'];
			if(!isset($area['group'])) $area['group'] = '0';
			$item['_group'][$col] = $area['group'];*/
            }
        }
        return $group;
    }

    // достаёт картинку по индексу
    function indexMultiimage($str, $index){
        $image = json_decode($str);
        if(isset($image[$index][0])) return $image[$index][0]; else return '';
    }

	public function setItemCount($url, $count){
		$base = new DataBase();
		$base->setTable('itemcount');
		$base->set(array("url"=>$url,"count"=>$count,"status"=>"1","date"=>date("Y-m-d H:i:s"),"md5"=>md5($url)), "WHERE md5='".md5($url)."'");
	}


	// color to RGB
    public function hextorgb($hex, $alpha = false) {
        $hex = str_replace('#', '', $hex);
        if ( strlen($hex) == 6 ) {
            $rgb['r'] = hexdec(substr($hex, 0, 2));
            $rgb['g'] = hexdec(substr($hex, 2, 2));
            $rgb['b'] = hexdec(substr($hex, 4, 2));
        }
        else if ( strlen($hex) == 3 ) {
            $rgb['r'] = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $rgb['g'] = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $rgb['b'] = hexdec(str_repeat(substr($hex, 2, 1), 2));
        }
        else {
            $rgb['r'] = '0';
            $rgb['g'] = '0';
            $rgb['b'] = '0';
        }
        if ( $alpha ) {
            $rgb['a'] = $alpha;
        }
        return $rgb;
    }

		public function getCity($_pre="",$_pad="") {
			$var = Func::getCityParams();
			if($_pad==""){
				// отдать массив города
				return $var;
			} else {
				// отдать город с предлогом
				if(isset($var[$_pad]))	return $_pre." ".$var[$_pad]; else return "";
			}
		}


		public function getCityParams() {
			$base = new DataBase;
			$base->setTable("vars");
			$data = $base->get(array("domen"=>$_SERVER['HTTP_HOST']))->assoc();

			$params = new DataBase;
			$params->setTable("params");
			$params = $params->get()->all();

			foreach ($params as $param) {
				if(!@$data[$param['name']]) $data[$param['name']] = $param['value'];
			}

			if(!@$data['domen']) $data['domen'] = "";
			// добавим записи информации о городе
			$city = array();
			if(isset($data['city'])){
				$cities = new DataBase;
				$cities->setTable("cities");
				$city = $cities->get(array("i"=>$data['city']))->assoc();
			}
			if(!is_array($city)) $city = array();
			return array_merge($data, $city);
		}

		// заменяет переменные переменными текущего города
		public function templateCityVars($_str){
			$params = Func::getCityParams();
			$data = array();
			foreach ($params as $key => $value) {
				$data["{{".$key."}}"] = $value;
			}
			$out = Engine::templaterKeys($_str,$data);
			return $out;
		}

		// получить родителя для построения меню
		public function getParentService($_cur, $unit="main"){
		    $out = "";
		    if($_cur['unit']!=$unit){
		        $page = new Page();
		        $page = $page->get(array('id'=>$_cur['parent']))->assoc();
		        if($page['unit']!=$unit) $out = func::getParentService($page,$unit); else $out = $page;
		    } else {
		        $out = $_cur;
		    }
		    return $out;
		}

		/*// получить всех родителей текущей страницы
		public function getAllParent($_cur){
		    $out = array($_cur['id']);
	        $page = new Page();
	        $page = $page->sql("SELECT id, unit, parent FROM pages WHERE id = :id", array('id'=>$_cur['parent']))->assoc();
		    if($page['unit']!="main"&&$_cur['unit']!="main") {
				$out = array_merge(array($page['id']),$out,func::getAllParent($page));
			}
		    return $out;
		}*/
		// получения дерева наследования в виде ID страниц
		public function getAllParents($parent,$unit="main",$all=false){
			$arr = array(); 
			$pages = new Page();
			$page = $pages->get(array("*"=>"id, parent,unit,file_load","id"=>$parent))->assoc();
			if($page['unit']=="main") return array(); // родитель не найден дошли до главной страницы
			if($page['unit']!=$unit){
				if(!$all){
					$arr[] = $page['id'];
				} else {
					$arr[] = $page;
				}
				$arr = array_merge(self::getAllParents($page['parent'],$unit,$all),$arr);
			} elseif($page['unit']==$unit){
				if(!$all){
					$arr[] = $page['id'];
				} else {
					$arr[] = $page;
				}
			}
			return $arr;
		}
		/*
		public function getAllParents($parent,$unit="main"){
			$arr = array(); 
			$pages = new Page();
			$page = $pages->get(array("*"=>"id, parent,unit","id"=>$parent))->assoc();
			if($page['unit']!=$unit){
				$arr[] = $page['id'];
				$arr = array_merge(self::getAllParents($page['parent'],$unit),$arr);
			} elseif($page['unit']==$unit){
				$arr[] = $page['id'];
			}
			return $arr;
		}	
*/
		// получить цену города
		public function getCityPrice($prices){
			static $city;
			if($city=="") $city  = func::getCityParams();
			$prices = json_decode($prices, true);
			return $prices[$city['i']];
		}

		public function getColor($color){
			static $colors = array();
			if(!$colors){
				// получаем цвета из стилей config.scss
				$css = file_get_contents(Engine::$root."/templates/front/css/jedcoder/_config.scss");
				$css = explode("\n",$css);
				//$colors = array();
				foreach($css as $line){
					if(strpos($line,"endcolors")) break;
					if(strpos($line,'$color')!==false){
						$mas = explode(":",$line);
						$colors[trim($mas[0])] = trim(str_replace(";","",$mas[1]));
					}
				}
			}
			if(strpos($color,'$')!==false){
				return $colors[$color];
			} else {
				return $color;
			}
		}

}

?>
