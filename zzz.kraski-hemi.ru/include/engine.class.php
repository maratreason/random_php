<?
// для мультидоменов
//ini_set("session.cookie_domain", ".zzz.unit.ru");
session_start();

// автоматическая подгрузка классов
function __autoload($name) {
    include_once($_SERVER['DOCUMENT_ROOT']."/include/".mb_strtolower($name).".class.php");
	 /*echo "add Class {$name}";
	 eval("class {$name} extends DataBase{}");*/
}


// основной класс системы
class Engine{
	// конфигурация путей и запросов
	public static $root; // корень сайта
	public static $domain; // домен сайта
	public static $fullUrl; // полный урл со всеми параметрами
	public static $url; // урл текущей страницы
	public static $urlAlias; // урл алиаса
	public static $page; // массив значений текущей страницы
  public static $pageAlias; // массив значений текущей страницы
	public static $modules;
	public static $header;
	public static $error404 = 1;
	public static $redirect;
	public static $modMeta; // дополнительные файлы подключаемые модулями
	public static $admin = false;
  public static $path;
  public static $console;
  public static $timer;

  public function getPageData($_url){
    $data = array();
    $type = "page";
    $page = new Page();
		//$page->get(array("url"=>$_url,"domain"=>array_search(Engine::$domain,Config::$domains)."|*","status"=>1))->assoc();
    $page->sql("SELECT * FROM pages WHERE url=:url AND (domain = :domain OR domain = 'site01' OR domain = '*') AND status = 1 ORDER BY id DESC",array("url"=>$_url,"domain"=>array_search(Engine::$domain,Config::$domains)))->assoc();

    //если страницы нету в таблице
		// проверяем является ли страница алиасом
		if(!$page->row){
      $url = Engine::$url;
			$alias = "";
			while($alias!="/"&&!$page->row){
        $alias = substr($url,0,strrpos($url,"/",1));
				$url = substr($url,0,strrpos($url,"/",1));

        if($alias==="") $alias="/";
				// Биндим значения
				$values = array(":alias"=>"".$alias."/%",":domain1"=>array_search(Engine::$domain,Config::$domains),":domain2"=>"site01");
				// создаём запрос
				$page->select("WHERE alias LIKE :alias AND (domain = :domain1 OR domain = :domain2 OR domain = '*') AND status = 1", $values)->assoc();
			}
			if($page->row) {
        $type = "alias";
      }
		}

    if($page->row){
      $data = $page->row;
    } else {
      $type = "none";
    }
    return array("data"=>$data, "type"=>$type);
  }

	// поиск страницы
	public function getPage($_url){
		$page = new Page();
		//$page->get(array("url"=>$_url,"domain"=>array_search(Engine::$domain,Config::$domains)."|*","status"=>1))->assoc();
    $page->sql("SELECT * FROM pages WHERE url=:url AND (domain = :domain OR domain = 'site01' OR domain = '*') AND status = 1 ORDER BY id DESC",array("url"=>$_url,"domain"=>array_search(Engine::$domain,Config::$domains)))->assoc();

    //если страницы нету в таблице
		// проверяем является ли страница алиасом
		if(!$page->row){
			$url = Engine::$url;
			$alias = "";
			while($alias!="/"&&!$page->row){
        $alias = substr($url,0,strrpos($url,"/",1));
				$url = substr($url,0,strrpos($url,"/",1));

        if($alias==="") $alias="/";
				// Биндим значения
				$values = array(":alias"=>"".$alias."/%",":domain1"=>array_search(Engine::$domain,Config::$domains),":domain2"=>"site01");
				// создаём запрос
        // СТАТУС = 1 алиасы работают при активном урл, = 0 страница недоступна но алиасы работают
				$page->select("WHERE alias LIKE :alias AND (domain = :domain1 OR domain = :domain2 OR domain = '*') AND status = 1", $values)->assoc();
			}
			if($page->row) {
        Engine::$urlAlias = $page->row['url']; // получаем url алиаса
        Engine::$pageAlias = $page->row;
      }
		} else {
        Engine::set404(0);
		}

		// если нашли страницу
		if($page->row){

      // проверим является ли страница копией
      // ищем копию и заменяем все поля которые можно поменять
      if($page->row['url_link']!=""){
        $cur_page = $page->row;
        $page_link = Engine::getPageData($cur_page['url_link']);
        $page->get(@$page_link['data']['id']);
        //$page_link = $page_link['data'];
        $yaml = unserialize($cur_page['unit_str']);
        foreach($yaml['areas'] as $name=>$area){
          if(isset($area['replace']))
          if($area['replace']==true){
            $page->row[$name] = $cur_page[$name];
          }
        }
        Engine::$url = $cur_page['url_link'];
      }

      Engine::$page = &$page->row;
      Engine::$path = Config::$front_template;
      if($page->row['type']==1000) {
          Engine::$admin = true;
          Engine::$path = Config::$back_template;
      }
			// для полей с пост обработкой выполним их функции обработки
			$page->setPostFunctions();
			// найдём все модули
			$modules = explode(",",($page->row['modules'].",".$page->row['unic_modules']));
			Engine::$modules = Engine::findModules($modules);
            // ответ серверва
			echo header(Engine::$header);
      $out =  $page->template();
      // дополнительные файлы и мета теги
      if(Engine::$page["search_index"]==0&&Engine::$urlAlias=="") Engine::$modMeta[] = '<meta name="robots" content="noindex, nofollow">';
      if(is_array(Engine::$modMeta)) $out = str_replace("</head>",implode(Engine::$modMeta)."</head>",$out); // добавим метатеги от модулей
      return $out;
		} else {
			Engine::set404();
		}

	}

	function init() {
    $loading = Engine::timer();
		// среда окружения
		Engine::$root = $_SERVER['DOCUMENT_ROOT'];
		//echo  Engine::$root."**";
		Engine::$domain = $_SERVER['HTTP_HOST'];
    // проеверям www и делаем редирект без www
    if(strpos(Engine::$domain,"www.")===0){
      Engine::$redirect = "http://".str_replace("www.","",Engine::$domain);
      header("Location:".Engine::$redirect, true, 301);
      exit;
    }
		Engine::$fullUrl = $_SERVER['REQUEST_URI']; // полный путь с параметрами
		Engine::$url = explode("?", $_SERVER['REQUEST_URI']); Engine::$url = Engine::$url[0];// путь без параметров
        if(Engine::$url=="") Engine::$url = "/";
        // Раскомментировать для русскох доменов
        // Engine::$url = urldecode(Engine::$url);
        // Если домен русскоязычный
        if (Config::$isRus) {
            Engine::$url = urldecode(Engine::$url);
        }
		// соединение с базой
		DataBase::connect(Config::$base_type,Config::$base_host,Config::$base_name,Config::$base_user,Config::$base_pass);
    // проверим существует ли у нас данный поддомен
    $out = "";
    $subdomen = Func::getCityParams();
    // проверяем есть ли данный домен и поддомен
    if(isset($subdomen['id'])||array_search(Engine::$domain,Config::$domains)){
      $out = Engine::getPage(Engine::$url);
    } else {
      Engine::setHeader("HTTP/1.x 404 Not Found");
      echo Engine::getPage("/error");
    }

		//**************************************************************
		if(!Engine::$error404&&!Engine::$redirect){
			Engine::timer($loading,"LOADING ");
      echo $out.Engine::$console;
		} else {
		    //страницы не существует
			if(Engine::$error404){
				Engine::setHeader("HTTP/1.x 404 Not Found");
				echo Engine::getPage("/404");
			}
			if(Engine::$redirect){
				header("Location:".Engine::$redirect, true, 301);
				//echo Engine::getPage(Engine::$redirect);
			}
		}
	}

    // рекурсивный обход поиска модулей
    function modulesSearch($mods){
        $mod = new Module();
        $modules = array();
        foreach($mods as $mname){
            $mname = trim($mname);
            if($mname!=""){
                $mod->get(array("name"=>$mname,"status"=>1))->assoc();
                if(@$mod->row['type']!=1){
                    $pos = @$mod->row['position'];
                    //$name = $mod->row['unit'];
                    if($pos!="") $modules[$pos][] = $mod->row;
                } else {
                    $gmod = new Module();
                    $gmod->get(array("parent"=>$mod->row['id']));
                    $child = array();
                    while($gr = $gmod->assoc()){
                        $child[] = $gr['name'];
                    }
                    $modules = array_merge_recursive($modules,Engine::modulesSearch($child));
                }
            }
        }
        return $modules;
    }
	// поиск всех модулей
	function findModules($mods){

        // составим список модулей которые необходимо отключить на данной странице

		$clear_mods = array();
		foreach($mods as $key=>$mod){
			$mod = trim($mod);
			if($mod!="")
			if($mod[0]=="-"){
				$mod_name = substr($mod,1);
				$clear_mods[] = $mod_name;
				$mods[$key] = $mod_name;
			}
		}


		$modules = Engine::modulesSearch($mods);

        // выполнение каждого модуля
        $out = array();
        foreach($modules as $posname=>$pos){
            //print_r($pos);
            foreach($pos as $params){

                $mname = mb_strtolower($params['unit']);
                if($mname!=""&&!in_array($params['name'],$clear_mods)){
					include_once(Engine::$root."/modules/{$mname}/{$mname}.class.php");
					$mod = new $mname();
					if(!isset($out[$posname])) $out[$posname] = "";
					$out[$posname] .= $mod->main($params);
                }

            }
        }
        return $out;
	}

	// вывод модулей в позиции
	function position($name){
		$out = "";
		/*if(isset(Engine::$modules[$name]))
		foreach(Engine::$modules[$name] as $params){
			$mname = $params['unit'];
			include_once(Engine::$root."/modules/{$mname}/".mb_strtolower($mname).".class.php");
			$mod = new $mname();
			$out .= $mod->main($params);
		}*/
		if(!isset(Engine::$modules[$name])) Engine::$modules[$name] = "";
		return Engine::$modules[$name];
	}


	// сбор указанного шаблона из файла
	// кеширует уже используемые шаблоны, чтобы не подгружать из файла каждый раз
	public function templater($file_path,$DATA=0)
	{
		// все запрашиваемые шаблоны кешируются в массив
		static $cache = array();

		$id = md5($file_path);
		if(!isset($cache[$id]))	{
			if(file_exists($file_path)){
                $template = file_get_contents($file_path);
                $cache[$id] = $template;
            } else {
                $template = "";
                Engine::console("Файл не найден: ".$file_path);
            }
		} else {
			$template = $cache[$id];
		}

		ob_start();
		eval("?>".$template."<?");
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	// сбор шаблона из строки
	public function templaterString($template,$DATA=0)
	{
		ob_start();
		eval("?>".$template."<?");
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}

	public function templaterArrayKeys($template,$data=0)
	{
		$keys = array();
		$values = array();
		foreach($data as $key=>$value)
		{
			$keys[]="[#{$key}#]";
			$values[]=$value;
		}
		return str_replace($keys,$values,$template);
	}

	// сбор шаблона ключ=значние
	function templaterKeys($template,$data)
	{
		$keys = array();
		$values = array();
		foreach($data as $key=>$value)
		{
			$keys[]=$key;
			$values[]=$value;
		}
		return str_replace($keys,$values,$template);
	}

	// установка заголовков
	public function setHeader($_header){
		self::$header = $_header;
	}

	function constant($key){
		$base = new DataBase();
		$base->setTable("params");
		$row = $base->get(Array("name"=>$key))->assoc();
		return @$row['value'];
	}

	// установка заголовков 404
    /*
        Если, хотябы один модуль или страница дайст flag=false, то значит данная страница может сгенерироваться и все другие попытки установить
        значение в true будут игнорироваться

    */
	public function set404($flag = true){
		//Engine::console("Soft 404");
        if(Engine::$error404==true) Engine::$error404 = $flag;
	}

	public function hardSet404($flag = true){
		//Engine::console("Hard 404");
        Engine::$error404 = $flag;
	}

	public function addMeta($_meta){
		Engine::$modMeta[$_meta] = $_meta;
	}

	public function setRedirect($url){
		Engine::$redirect = $url;
	}

	public function jsRedirect($_url){
		echo "<script>document.location = '{$_url}'</script>";
	}

	// прямой вывод модуля
	public function module($_mod){
		include_once(Engine::$root."/modules/{$_mod}/".mb_strtolower($_mod).".class.php");
		return new $_mod();
	}

	// прямой вывод модуля по индификатору
	public function moduleByName($_modname){

		$modules = Engine::findModules(array(0=>$_modname));
		$out = $modules[key($modules)];
		return $out;
	}
	// преобразует входной урл в массив параметров
	public function urlToParams($_url){
		$get = array();
		$params = explode("/",$_url);
		foreach($params as $param){
			$kv = explode("-",$param);
            $key = $kv[0];
            unset($kv[0]);
			if($param)$get[$key] = urldecode(@implode("-",$kv));
		}
		return $get;
	}


	// счётчик времени выполнения
	function timer($tstart=0,$caption="")
	{
		if(strpos(" ".Engine::$url,"/ajax/")>0) return 0;
		static $top;
		if	($tstart==0)
		 {
			//Считываем текущее время
			$mtime = microtime();
			//Разделяем секунды и миллисекунды
			$mtime = explode(" ",$mtime);
			//Составляем одно число из секунд и миллисекунд
			$mtime = $mtime[1] + $mtime[0];
			//Записываем стартовое время в переменную
			return  $mtime;
		 }
		else
		 {
			$top +=20;
			$mtime = microtime();
			$mtime = explode(" ",$mtime);
			$mtime = $mtime[1] + $mtime[0];
			//Записываем время окончания в другую переменную
			$tend = $mtime;
			//Вычисляем разницу
			$totaltime = round($tend - $tstart,3);
			//Выводим не экран
			//Engine::$timer .= "<div style='font-size:11px;background-color:#000;font-family:small;position:fixed;right:10px;top:{$top}px;color:#FFF;padding:5px;z-index:10000'>{$caption} {$totaltime}</div>";
            Engine::console($caption.$totaltime);
		 }
	}
	// вывод ошибок в консоль
	function console($text){
		if(Config::$debug) Engine::$console .= "<script>console.log('".$text."')</script>";
	}

	// Ошибка модуля
	function errorMod($param,$text){
		Engine::console("Error({$param['unit']}:{$param['name']}) ".$text);
	}

	function block($file, $params=array()){
        $out = "";
        if(count($params)==0) $out = "<div class='jeddemo'>{$file}</div>";
        $out .= Engine::templater(Engine::$root.Engine::$path."/blocks/".$file."/view.html",$params);
        return $out;
    }
}
?>
