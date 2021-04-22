<?

if(!isset($_SESSION['login'])) $_SESSION['login'] = false;
//$_SESSION['login'] = false;
//$_SESSION['user_type'] = "superadmin";

class mod_admin extends Module{

	// url страницы админки, для безопасности можно установить любой урл админ панели
	// так же необходимо поменять урл и алиас для админки в БД
	static  $admin_url = "/unit";
	private $edit_domain;

	function __construct(){
		if(Engine::$url==mod_admin::$admin_url){
			Engine::hardSet404();
		}
		// страница не будет существовать без авторизации / поможет защитить от лишних подборов
		if($_SESSION['login']==false) {
			Engine::set404();
		} else {
			Engine::set404(0); // дадим команду на существования всех страниц в системе
		}
		//header("Location:".mod_admin::$admin_url."/login");
		if(!isset($_SESSION['edit_domain'])) $_SESSION['edit_domain'] = "*";
		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_admin"; // получим класс
		$this->edit_domain = &$_SESSION['edit_domain']; // привязываем к сессии редактируемый домен
		// прикрепляем необходимые файлы модуля в шаблон

        // дополнительные файлы
        $this->setFile("js/sortable.min.js");
        $this->setFile("js/multiimages/multiimages.js");
        $this->setFile("js/multiimages/multiimages.css");
		$this->setFile("js/mod_admin.js"); // подключим js файл модуля
		// стандартная инициализация модуля
		$this->Init();
	}

	// обработчик ajax событий
	public function ajax(){
		$this->main();
	}

	// создаёт ссылку с иконкой
	public function createButton($_name,$_link,$_icon="",$_bonus="",$_superbonus=""){
		if(strpos($_link,"::")>0){
			$type = explode("::",$_link);
			$_link = $type[1];
			$type = $type[0];
		} else {
			$type = "link";
		}
		if(strpos("-".Engine::$fullUrl,$_link)>0) $active = "button-active"; else $active = "";
		if($_icon != ""){
			if($_name!="") $img_class = "class='plus-text'"; else $img_class = "";
			$img = "<img src='".Config::$back_template."/img/icons/{$_icon}' width='24' height='24' {$img_class}/>";
		}
		// создать ссылку
		if($type==="link"){
			$out = "<a href='{$_link}' class='button {$active} {$_bonus}' {$_superbonus}>{$img}{$_name}</a>";
		}
		// создать кнопку с типом submit
		if($type==="submit"){
			$out = "<input type='submit' value='{$_name}' class='button {$active} {$_bonus}'>";
		}
		return $out;
	}
	// выводит панель редактирования страницы
	public function page_panel(){
		$out = "";
		if($_SESSION['login']){
			$this->setFile("panel.css");
			$id = Engine::$page['id'];
			$out = "<div class='admin_page_panel'>";
			$out .= $this->createButton("","".mod_admin::$admin_url."/pages-edit/id-{$id}","icon-edit.png","","target='blank_'");
			if(Engine::$page['type']==1){
				$out .= "<br>".$this->createButton("","".mod_admin::$admin_url."/pages-edit/create-page/parent-{$id}","icon-plus-page.png","","target='blank_'");
			}
			$out .="</div>";
		}
		return $out;
	}
	// выводит меню
	public function menu(){
        //echo $this->createButton("Сайты","".mod_admin::$admin_url."/domains","icon-domain.png","menu-link");
		//echo $this->createButton("Разделы","".mod_admin::$admin_url."/groups","page-type-1.png","menu-link");
		echo $this->createButton("Все страницы","".mod_admin::$admin_url."/pages","page-type-0.png","menu-link");
		//echo $this->createButton("Статьи","".mod_admin::$admin_url."/pages-show/id-53","page-type-0.png","menu-link");

		$cats = new Page();
		$cats = $cats->sql("SELECT id, url_name, user_cfg FROM pages WHERE type = 1 ORDER BY prior")->all();
		echo "<div class='catalog-menu'>";
		foreach ($cats as $cat) {
			$cfg = unserialize($cat['user_cfg']);
			if(@$cfg[$_SESSION['user']]['hittab']==0) continue;
			if(@$this->GET['id']==$cat['id']) $active = "active"; else $active = "";
			$url = "".mod_admin::$admin_url."/pages-show/id-{$cat['id']}";
			echo "<a href='{$url}' class='catalog-link {$active}'>{$cat['url_name']}</a>";
			//echo $this->createButton("{$cat['url_name']}","".mod_admin::$admin_url."/pages-show/id-{$cat['id']}","page-type-0.png","menu-link");
		}
		echo "</div>";
		/*
		echo $this->createButton("Шкафы","".mod_admin::$admin_url."/pages-show/id-706","page-type-0.png","menu-link");
		echo $this->createButton("Стеллажи","".mod_admin::$admin_url."/pages-show/id-912","page-type-0.png","menu-link");
		echo $this->createButton("Комплектующие стел.","".mod_admin::$admin_url."/pages-show/id-995","page-type-0.png","menu-link");
		echo $this->createButton("Сейфы","".mod_admin::$admin_url."/pages-show/id-994","page-type-0.png","menu-link");
		echo $this->createButton("Сейфы new","".mod_admin::$admin_url."/pages-show/id-996","page-type-0.png","menu-link");

		echo $this->createButton("Верстаки","".mod_admin::$admin_url."/pages-show/id-1313","page-type-0.png","menu-link");
		echo $this->createButton("Элементы верстаков","".mod_admin::$admin_url."/pages-show/id-1314","page-type-0.png","menu-link");
		echo $this->createButton("Оружейные сейфы","".mod_admin::$admin_url."/pages-show/id-1491","page-type-0.png","menu-link");
		echo $this->createButton("Входные двери","".mod_admin::$admin_url."/pages-show/id-1811","page-type-0.png","menu-link");
		echo $this->createButton("Противопож. двери","".mod_admin::$admin_url."/pages-show/id-1812","page-type-0.png","menu-link");
		echo $this->createButton("Технические двери","".mod_admin::$admin_url."/pages-show/id-1813","page-type-0.png","menu-link");
		echo $this->createButton("Разделочные столы","".mod_admin::$admin_url."/pages-show/id-2024","page-type-0.png","menu-link");
		echo $this->createButton("Авторская мебель","".mod_admin::$admin_url."/pages-show/id-2110","page-type-0.png","menu-link");
*/
		//echo $this->createButton("Шаблоны","".mod_admin::$admin_url."/templates","icon-page.png","menu-link");
		echo $this->createButton("Модули","".mod_admin::$admin_url."/modules","icon-module.png","menu-link");
		echo $this->createButton("Данные","".mod_admin::$admin_url."/base","icon-var.png","menu-link");
		echo $this->createButton("Units","".mod_admin::$admin_url."/units","icon-unit.png","menu-link");
		echo $this->createButton("Выход","".mod_admin::$admin_url."/exit","icon-exit.png","menu-link");
	}

	// устанавливаем домен для редактирования
	private function setDomain($_key){
		$this->edit_domain = $_key;
	}
	//----------------------------------------------------------------------------------
	// главная функция модуля
	//----------------------------------------------------------------------------------
	public function main(){
		// распеределяем задачи
		// оринетируемся по параметрам в урл что надо сделать
		if(isset($this->GET["domains"])) $this->mainDomains();
		if(isset($this->GET["groups"])) $this->mainGroups();
		if(isset($this->GET["pages"])) $this->mainPages();
		if(isset($this->GET["modules"])) $this->mainModules();
		if(isset($this->GET["units"])) $this->mainUnits();
		if(isset($this->GET["base"])) $this->mainBases();
		if(isset($this->GET["exit"])) {$_SESSION['login']=false; Engine::setRedirect(mod_admin::$admin_url."/login");}
	}

	private function checkField(){

	}
	//----------------------------------------------------------------------------------
	// логика страницы domains
	//----------------------------------------------------------------------------------
	function mainDomains(){
		// список доменов
		if($this->GET["domains"]===""&&count($this->GET)==1){
			foreach(Config::$domains as $key=>$value){
				if($key==="*") $class = "superadmin"; else $class = "";
				echo $this->createButton($value." [{$key}]","".mod_admin::$admin_url."/domains-set/edit-{$key}","icon-domain.png","inline-button {$class}");
			}
		}
		if($this->GET["domains"]==="set"){
			$this->setDomain($this->GET["edit"]);
			echo "Редактируемый сайт: ".Config::$domains[$this->GET["edit"]];
		}
	}

	//----------------------------------------------------------------------------------
	// логика страницы groups
	//----------------------------------------------------------------------------------
	function mainGroups(){
		echo "groups main";
	}
	// создаёт массив дерева вывода страниц
	function pageTree($_parent,$_level=0,$parent_list=""){

		$out = array();
		$base = new Page();
		$pages = new Page();
		if($this->edit_domain!="*"){
			$pages->sql("SELECT id,status,type,unit,domain,user_cfg,url,url_name,status,parent,prior FROM pages WHERE parent = :parent AND (domain = :domain1 OR domain = :domain2) ORDER BY prior", array("parent"=>$_parent, "domain1"=>$this->edit_domain, "domain2"=>"*"));
		} else {
			$pages->get(array("*"=>"id","parent"=>$_parent));
		}
		$count_cur = 0;
		$count_all = $pages->count();
		while($row = $pages->assoc()){
			// находим первый и последний эелемент
			// последний эелемент закрывает div группы в шаблоне
			// скрываем все системные страницы
			if($row['type']==1000) continue;
			$cfg = unserialize($row['user_cfg']);
			// прочитаем параметры unit файла
			$yaml = Yaml::YAMLLoad(Engine::$root."/units/page/{$row['unit']}.yaml");
			$row['_style'] = "";
			if(isset($yaml['style'])) {
				$row['_style'] = (string)$yaml['style'];
			}
			unset($yaml);
			if(!isset($row['_buttons_unit'])) $row['_buttons_unit'] = "";
			if(!isset($row['_buttons'])) $row['_buttons'] = "";
			$row['_buttons_unit'] .= $this->createButton("","".mod_admin::$admin_url."/units-edit/file-{$row['unit']}","icon-unit-edit.png","icon-button page-icon-type");
			$row['_buttons'] = $this->createButton("","".mod_admin::$admin_url."/pages-edit/id-{$row['id']}","icon-edit.png","icon-button");

			// уберем кнопку удаления у системных стрнаиц и у главной страницы
            $row['_buttons_delete'] = "";
            if($row['type']!=1000&&$row['unit']!="main") $row['_buttons_delete'] = $this->createButton("","javaScript:{deletePage({$row['id']})}","icon-delete.png","icon-button");
			$row['_buttons_status'] = $this->createButton("","javaScript:{statusPage({$row['id']})}","icon-status.png","icon-button btn-status");
			if($row['status']==0) $row['_buttons_status'] = $this->createButton("","javaScript:{statusPage({$row['id']})}","icon-status-.png","icon-button btn-status");
			$row['_buttons_hittab'] = "";
			$row['_url_hittab'] = "";
			if($row['type']==1&&$row['unit']!='main')$row['_buttons_hittab'] = $this->createButton("","javaScript:{}","icon-hittab.png","icon-button btn-hittab");
			if(@$cfg[$_SESSION['user']]['hittab']==1){
				// страница является избранной
				$row['_url_hittab'] = mod_admin::$admin_url."/pages-show/id-".$row['id'];
				$row['_buttons_hittab'] = $this->createButton("","javaScript:{}","icon-hittab_.png","icon-button btn-hittab active");
			}
			// для раздела переопределим дизайн кнопок
			if($row['type']==1) {
				$row['_buttons'] .= $this->createButton("","".mod_admin::$admin_url."/pages-edit/create-page/parent-{$row['id']}","icon-plus-page.png","icon-button");

			}
			// Отступы
			if(!isset($row['_padding'])) $row['_padding'] = "";
			$row['_level'] = $_level;
			$margin = ($_level)*22;
			$margin2 = ($_level)*22-1;
			$margin3 = ($_level-1)*22+5;
			$row['_padding'] .="<div class='line-sub' style='width:{$margin2}px'></div>";
			if($_level>0) $row['_padding'] .="<div class='line-first' style='left:{$margin3}px'></div>";
			$row['_padding_text'] ="padding-left:{$margin}px";
			if($row['domain']==="*") $row['_class'] = "superadmin"; else $row['_class'] = "";
            $row['parent_list'] = $parent_list." parent-".$_parent;

			// если не закрыта достаем дочерние страницы
			$count = 0;
			$count = $base->get(array("*"=>"id","parent"=>$row['id']))->count();
			$row['_url_hittab_count'] = $count;
			//$count = $row['type'];
			$child = array();

			if((@$cfg[$_SESSION['user']]['showhide']==0&&@$cfg[$_SESSION['user']]['hittab']==0)){
				$row['_close'] = "";
				$child = $this->pageTree($row['id'],$_level+1, $row['parent_list']);
			} else {
				//$count = 0;
				$row['_close'] = "close";
			}
            // если содержит в себе ещё страницы то поставим флаг

            if($count>0) {
				$row['iscat'] = 1;
			} else {
				$row['iscat'] = 0;
				//$row['_last'] = 1;
			}
			// прикрепляем иконки к названию
			if(@$row['market_yandex']=="yamarket") $row['url_name'] .= "<img src='".Config::$back_template."/img/icons/icon-yandex-market.png' style='margin-left:5px;margin-bottom:-2px;width:16px;height:16px;'/>";
			if(@$row['market_google']=="gmarket") $row['url_name'] .= "<img src='".Config::$back_template."/img/icons/icon-google-market.png' style='margin-left:5px;margin-bottom:-2px;width:16px;height:16px;'/>";
			$out[] = $row;
			$out[count($out)-1]['_child'] = $child;
            //$out = array_merge($out,$child);
		}
		return $out;
	}
	// создаёт массив дерева вывода модулей
	function moduleTree($_parent,$_level=0){
		$out = array();
		$mod = new Module();
		//$mod->get(array("parent"=>$_parent));
        $mod->select("WHERE parent = :parent  ORDER BY status DESC", array("parent"=>$_parent));
		while($row = $mod->assoc()){
			if($row['type']==1000) continue;

            // прочитаем параметры unit файла
            $yaml = Yaml::YAMLLoad(Engine::$root."/units/module/{$row['unit']}.yaml");
            $row['_style'] = "";
            if(isset($yaml['style'])) $row['_style'] = (string)$yaml['style'];

			$row['_buttons'] = $this->createButton("","".mod_admin::$admin_url."/modules-edit/id-{$row['id']}","icon-edit-module.png","icon-button");
			if($row['type']!=1000) $row['_buttons_delete'] = $this->createButton("","javaScript:{deleteModule({$row['id']})}","icon-delete-module.png","icon-button");
			// для раздела переопределим дизайн кнопок
			if($row['type']==1) {
				//$row['_buttons'] = $this->createButton("","".mod_admin::$admin_url."/modules-edit/id-{$row['id']}","icon-edit-group.png","icon-button");
				$row['_buttons'] .= $this->createButton("","".mod_admin::$admin_url."/modules-edit/create-module/parent-{$row['id']}","icon-plus-module.png","icon-button");
				//$row['_buttons_delete'] = $this->createButton("","javaScript:{deleteModule({$row['id']},\"Удалить группу?\")}","icon-delete-group.png","icon-button");
			}
			$margin = ($_level-1)*16;
			if(!isset($row['_padding'])) $row['_padding'] = "";
			$margin = ($_level)*22;
			$margin2 = ($_level)*22-1;
			$margin3 = ($_level-1)*22+5;
			$row['_padding'] .="<div class='line-sub' style='width:{$margin2}px'></div>";
			$row['_padding'] .="<div class='line-first' style='left:{$margin3}px'></div>";
			$row['_padding_text'] ="padding-left:{$margin}px";
			if($row['type']==1000) $row['_class'] = "superadmin"; else $row['_class'] = "";
			if($row['type']==1) $row['iscat'] = 1; else $row['iscat'] = 0;
			$out[] = $row;
			$out = array_merge($out,$this->moduleTree($row['id'],$_level+1));
		}
		return $out;
	}

	// составляет редактор шаблона
	// в $_data передаётся строка из таблицы данных в ввиде ассоц массива
	// в $_data должен быть элемент $_data['unit'], это имя базовго шаблона из по которому создаётся редактор полей
	// СЕДЛАТЬ ПО РАСПРЕДЕЛЕНИЮ ПРАВ ПОЛЬЗОВАТЕЛЕЙ
	function createUnitEditor($_data,$_dir){
		$_unit = $_data['unit'];
		// получим родительский шаблон, если текущий шаблон является расширением <EXT>
		if($_dir==="module"&&file_exists(Engine::$root."/modules/{$_unit}/{$_unit}.yaml")){
			$yaml = Yaml::YAMLLoad(Engine::$root."/modules/{$_unit}/{$_unit}.yaml");
		} else $yaml = Yaml::YAMLLoad(Engine::$root."/units/{$_dir}/{$_unit}.yaml");

		//echo Engine::$root."/modules/{$_unit}/{$_unit}.yaml";
		if(count($yaml)==1) return "Неправильный формат файла {$_unit}";
		if(!isset($yaml['extends'])) $yaml['extends'] = "";
		$units = explode(",",$yaml['extends']);
		array_push($units,$_unit);// добавляем текущий шаблон к массиву всех шаблонов unit'ов
		$units = array_diff($units,array(''));// вычтем пустые
		// сохранение всех полей для выявления повторного определения
		$fields = array();
		//$all_areas = array();
		$area = array();
		$area_unit = array();
		$out = "";
		// проходимся по всем шаблонам
		foreach($units as $unit){
			$unit = trim($unit);
			if($_dir==="module"&&file_exists(Engine::$root."/modules/{$unit}/{$unit}.yaml")){
				$yaml = Yaml::YAMLLoad(Engine::$root."/modules/{$unit}/{$unit}.yaml");
				//Engine::console("{$unit} as modules");
			} else {
				$yaml = Yaml::YAMLLoad(Engine::$root."/units/{$_dir}/{$unit}.yaml");
				//Engine::console("{$unit} no modules");
			}
			foreach($yaml['areas'] as $name=>$param){
					// занесем название поля в массив
					$param['name'] = trim($name);
					// установим значения по умолчанию, если их не определили в конфиге страницы
					// все неопределённые системные типы полей будут являтся для каждой строки уникальными / значение native
					if(!isset($param['systemtype'])) $param['systemtype'] = "native";
					// непоределённый тип будет по умолчанию text
					if(!isset($param['type'])) $param['type'] = "text";
					// не определённый редактор textarea
					if(!isset($param['editor'])) $param['editor'] = "text";
					// получени описания поля
					if(isset($param['desc'])){
						$param['caption'] = "<span class='desc-button'>{$param['caption']}</span><div class='description'>{$param['desc']}</div>";
					}
					$system_type = strtolower($param['systemtype']);
					$type = new Page();
					$base_type = strtolower($type->getFieldType($name));
					$param_type = strtolower($param["type"]);
					// проверим является или тип поля в шаблоне аналогичному типу поля в базе
					// да, если поля совпадают или если поля не существует
					if($base_type === $param_type||$base_type === ""){
						if(isset($_data[$name])) $value = $_data[$name]; else $value = "";
						$param['class'] = "";
						// проверим иммется ли значение <SYSTEMTYPE>
						if($system_type!="native"){

							// если поле для супер админа
							if($system_type === "superadmin"){
								$param['class'] = "superadmin";
							}
							// если поле статичное
							if($system_type === "static"){
								$param['class'] = "static superadmin";
								$param['attr'] = "readonly=\"readonly\"";
								$value = $param['value'];
							}
						}
						//для супер админа покажем системный тип поля
						$param['caption'] .= " <span class=\"superadmin area-type\">/ <strong>{$name}</strong> / <strong>{$base_type}</strong> / <a href='/unit/units-edit/file-{$unit}' target='blank_'>{$unit}.yaml</a></span>";
						// если значение не задано установим сзначение по умолчанию
						if($value==""){
							if(!isset($param['value'])) $param['value'] = "";
							$value = $param['value'];
						}

						$func = "template_".mb_strtolower(trim($param['editor']));
						$param['value'] = $value;
						$param['object'] = $_data; // передаём объект-строку для которого создаётся поле

						if(isset($fields[$name])){
							if($fields[$name]==="static"&&$param['systemtype']!='static'){
								// разрешаем переопределение статичных полей только статичным полем
								$area[$unit][$name] = "<div class='error'><strong>{$unit}</strong>: Поле <strong>{$name}</strong> можно переопределить только статичным полем</div>";
								continue;
							} else {
								//$param['caption'] .= "<span class='superadmin label-comment'>/ {$unit}.xml</span>";
								// удаляем данное поле в других шаблонах
								foreach($area as $u=>$a){
									unset($area[$u][$name]);
								}
							}
						}
						$area[$unit][$name] = Area::$func($param,"param");
                        $area_unit[$name] = Area::$func($param,"param");
						// создаём скрытое поле с типом
						$param['value'] = $param['type'];
						$area[$unit][$name] .= Area::template_hidden($param,"type");
                        $area_unit[$name] .= Area::template_hidden($param,"type");

						// Помечаем что данное поле уже определялось, в качестве метки ставим системный тип поля
						$fields[$name] = $param['systemtype'];

					} else {
						$area[$unit][$name] = "<div class='error'><strong>{$unit}</strong>: Для параметра <strong>{$name}</strong> уже определен тип <strong>{$base_type}</strong>, но не <strong>{$param['type']}</strong></div>";
					}
			}
		}
		$out = "";
		// шаблонизируем поля
		//foreach($units as $unit){
		$unit = $_data['unit'];
        $base_fields = @$area["module"];
        $sec_fields = $area[$unit];
        $area[$unit] = $area_unit;
        $free = array();
		$freeAll = array();
		$free_name = array();
            // если у данного шаблона есть шаблон вывода, то выводим через него, иначе выводим способом по умолчанию
			if($_dir==="modules"&&file_exists(Engine::$root."/modules/{$unit}/{$unit}.html")){
				$out .= Engine::templater(Engine::$root."/modules/{$unit}/{$unit}.html",$area[$unit]);
			}elseif(file_exists(Engine::$root."/units/{$_dir}/view/{$unit}.html")){
				$out .= Engine::templater(Engine::$root."/units/{$_dir}/view/{$unit}.html",$area[$unit]);
			}elseif(file_exists(Engine::$root."/units/{$_dir}/view/".@$yaml['template'].".html")) {
                $out .= Engine::templater(Engine::$root."/units/{$_dir}/view/{$yaml['template']}.html",$area[$unit]);
            } else {
				// если unit содержит шаблон по выводу групп
				if(isset($yaml['unit-template'])){
					//$out .= Engine::templater(Engine::$root."/units/{$_dir}/{$yaml['unit-template']}.html",array("_base" => $base_fields, "_fields"=>implode($sec_fields)));
                    // проверим какие поля есть в шаблоне, а какие пропущены
                    $tmp = file_get_contents(Engine::$root."/units/{$_dir}/view/{$yaml['unit-template']}.html");
                    foreach($area_unit as $name=>$txt){
                        $find = strpos($tmp,"<?=\$DATA['{$name}']?>");
                        if(!$find) $find = strpos($tmp,"<?=@\$DATA['{$name}']?>");
                        // поле не используется в шаблоне, добавим его как свободное, чтобы потом списком высести все эти поля в шаблоне
                        if(!$find) {

                            // проверим к какой группе принадлежит запись
                            if (isset($yaml['areas'][$name]['group'])) {
                                $gr = trim($yaml['areas'][$name]['group']);
                            } else {
                                $gr = '0';
                            }
                            if (!isset($free[$gr])) $free[$gr] = "";
                            // свободные поля только начинающиеся с opt_
                            if (strpos(" ".$name, "opt_")>0&&$_dir!="modules") {
                                //Engine::console("-".$name);
                                $free[$gr] .= $txt;
                            } else {$free[$gr] .= $txt;}

							if(!isset($free_name[$gr])) $free_name[$gr] = "";
							if(isset($yaml['groups'][$gr])) $free_name[$gr] = $yaml['groups'][$gr]['caption'];
							//$free .= $txt;
                        }
                    }

					// Назовём каждую группу
					foreach($free as $n=>$group){
						$free[$n] = "<h2>".$free_name[$n]."</h2>".$free[$n];
					}
                    $data = $area_unit;

                    $data['_free'] = implode($free);
					$data['_freeAll'] = implode($free);
                    $out .= Engine::templater(Engine::$root."/units/{$_dir}/view/{$yaml['unit-template']}.html",$data);
				} else {
					$out .= implode($area[$unit]);
				}
			}
		//}
		// создадим скрытое поле с id редактируемого объекта
		if(!isset($_data["id"])) $_data["id"] = "";
		$out .= Area::template_hidden(array("name"=>"id","value"=>$_data["id"]));
		return $out;
	}

	//----------------------------------------------------------------------------------
	// логика страницы pages
	//----------------------------------------------------------------------------------
	function mainPages(){

		// показать скрыть разделы
		if(isset($this->GET["showhide"])){
			$user = $_SESSION['user'];
			$pages = new Page();
			$cat = $pages->get(array("*"=>"id,user_cfg", "id"=>$this->GET["showhide"]))->assoc();
			$cfg = unserialize($cat['user_cfg']);
			if(!isset($cfg[$user])) $cfg[$user] = array();
			if(@$cfg[$user]['showhide']==0){
				// закрыть
				$cfg[$user]['showhide'] = 1;
				echo "";
			} else {
				// открыть
				$cfg[$user]['showhide'] = 0;
				$parent_list =  func::getAllParents($cat['id']);
				$parent_class = "";
				foreach ($parent_list as $list) {
					$parent_class .= " parent-".$list;
				}
				$rows =  self::pageTree($cat['id'], $this->GET["level"]+1,$parent_class);
				echo Engine::templater($this->mod_path."/page-list-row.html",$rows);
			}
			//print_r($cfg);
			//echo "END";
			$pages->set($cat['id'], array("user_cfg"=>serialize($cfg)));
		}
		// сортировка
		if(isset($this->GET["sorting"])){
			$list = explode(",", $this->GET['sorting']);
			$page = new Page();
			foreach ($list as $i=>$id) {
				$page->set($id,array("prior"=>$i));
			}

		}
		// Добавить раздел в избранное
		if(isset($this->GET["hittab"])){
			$user = $_SESSION['user'];
			$pages = new Page();
			$cat = $pages->get(array("*"=>"id,user_cfg", "id"=>$this->GET["hittab"]))->assoc();
			$cfg = unserialize($cat['user_cfg']);
			if(!isset($cfg[$user])) $cfg[$user] = array();
			if(@$cfg[$user]['hittab']==0){
				// закрыть
				$cfg[$user]['hittab'] = 1;
			} else {
				// открыть
				$cfg[$user]['hittab'] = 0;
			}
			$pages->set($cat['id'], array("user_cfg"=>serialize($cfg)));
		}
		// Установить статус страницы
		if(isset($this->GET["status"])){
			$id = $this->GET["status"];
			$page = new Page();
			$p = $page->get(array("*"=>"id, status","id"=>$id))->assoc();
			if($p['status']) $status = 0; else $status = 1;
			$page->set($id,array("status"=>$status));
			echo $status;
		}

		// показать все страницы, если в запросе был только один параметр
		if($this->GET["pages"]===""&&count($this->GET)==1){
			$data['_pages'] = $this->pageTree(0);
			$data['_domain'] = Config::$domains[$this->edit_domain];
			$mpage = new Page();
			$data['current'] = $mpage->get(10)->assoc();
			//$data['_buttons'] = $this->createButton("Новая страница","".mod_admin::$admin_url."/pages-edit/create-page","icon-plus-page.png","inline-button");
			//$data['_buttons'] .= $this->createButton("Новый раздел","".mod_admin::$admin_url."/pages-edit/create-category","icon-plus-group.png","inline-button");
			$data['_buttons'] = $this->createButton("Новая страница","".mod_admin::$admin_url."/pages-edit/create-page/parent-10","icon-plus-page.png","inline-button");
			$data['_panel_title'] = "Все страницы";
            echo Engine::templater($this->mod_path."/page-list.html",$data);
		}

		if($this->GET["pages"]==="show"&&isset($this->GET['id'])){
			$id = $this->GET['id'];
			$data['_pages'] = $this->pageTree($id);
			$data['_domain'] = Config::$domains[$this->edit_domain];
			if(!isset($data['_buttons'])) $data['_buttons'] = "";
			$data['_buttons'] .= $this->createButton("Новая страница","".mod_admin::$admin_url."/pages-edit/create-page/parent-{$id}","icon-plus-page.png","inline-button");
			//$data['_buttons'] .= $this->createButton("Новый раздел","".mod_admin::$admin_url."/pages-edit/create-category","icon-plus-group.png","inline-button");
			$mpage = new Page();
			$data['current'] = $mpage->get($id)->assoc();
			$data['_panel_title'] = $data['current']['url_name'];
			$data['_hitId'] = $id;
			echo Engine::templater($this->mod_path."/page-list.html",$data);
		}
		// окно редактирования страницы
		if($this->GET["pages"]==="edit"){
			if(isset($this->GET['id'])){
				$page = new Page();
				$page = $page->get($this->GET['id'])->assoc();
			}

			// если страница не существует или не установлен ни один шаблон
			// значит мы пытаемся создать новую страницу
			// поставим значения по умолчанию
			if(isset($this->GET['create'])){
				// создаём страницу
				if($this->GET['create']==="page"){
					$page['unit'] = "page";
					$page['domain'] = $this->edit_domain;
				}
				//создаём раздел
				if($this->GET['create']==="category"){
					$page['unit'] = "category";
					$page['domain'] = $this->edit_domain;
				}
				// если указан родитель
				if(isset($this->GET['parent'])){
					$parent = new Page();
					$parent = $parent->get($this->GET['parent'])->assoc();
					$page['parent'] = $parent['id'];
					$page['url'] = $parent['url']."/";
					// получим первый юнит, который разрешен для добавления в данный раздел
					$u = explode(",",$parent['child_units']);
					$page['unit'] = $u[0];
				}
			}
			// если я вно указали каким юнитом хотим показать страницу
			if(@$this->GET['unit']){
				$page['unit'] = $this->GET['unit'];
			}

			$data = array();
			$data['_content'] = $this->createUnitEditor($page,"page");
			// поределяем какой домен выбрать при открывании страницы по кнопке "Перейти на срнаицу"
			if(isset($page['id'])){
				$domain = $page["domain"];
				if($domain==="*"){$domain = Engine::$domain;} else {$domain = Config::$domains[$page['domain']];}
			}
			if(isset($page['url_name'])) {
				$data['_title'] = "Редактирование: ".$page['url_name'];
			} else {
				$data['_title'] = "Создание новой страниц";
			}
			$data['_buttons'] = $this->createButton("Назад","".mod_admin::$admin_url."/pages","icon-left.png","inline-button");
			$data['_buttons'] .= $this->createButton("Сохранить","javaScript:{sendForm(\"page-form\");}","icon-save.png","inline-button");
			if(isset($page['id'])) $data['_buttons'] .= $this->createButton("На страницу","http://".$domain.$page['url'],"icon-domain.png","inline-button","target='blank_'");
			echo Engine::templater($this->mod_path."/page-edit.html",$data);
		}

		// сохраняем страницу
		if($this->GET["pages"]=="save"){
			$page = new Page();
			// получаем поля таблицы
			$field = $page->getFields();
			// проверим все записи, есть ли колонка для них в таблице
			foreach($_POST['param'] as $param=>$value){
				// если нету создаём колонку
				if(!isset($field[trim($param)])) {
                    $type = $_POST['type'][$param];
					$page->createField($param,$type);
				}
			}

			// предзаписывающие функции
			$yaml = Yaml::YAMLLoad(Engine::$root."/units/page/{$_POST['param']['unit']}.yaml");
			//echo Engine::$root."/units/page/{$_POST['param']['unit']}.yaml";

			if(isset($yaml['areas']['pre_func'])){
				$line = explode("\n",$yaml['areas']['pre_func']['value']);
				foreach($line as $kv){
					if(trim($kv)=="") continue;
					$kv = explode("=",$kv);
					$param = trim($kv[0]);
					$func = "pre_".trim($kv[1]);
					$_POST['param'][$param] = pre::$func($_POST['param'], $param);
				}
			}
			// для всех таких полей установим значние в пусто
			foreach($field as $area=>$value){
				if(!isset($_POST['param'][$area])) {
					//$_POST['param'][$area] = ""; - отключено / будет отдельный скрипт мусоросборщик
				}
			}
			// предобработка поля перед записью
            // записываем дерево юнита в виде массива
            //$unit = $page->get($_POST['param']['id'])->assoc();
            //Engine::console($unit['unit']);
            $yaml = Func::getUnitAreas(Engine::$root."/units/page/{$_POST['param']['unit']}.yaml");

            $_POST['param']['unit_str'] = serialize($yaml);
			$flag = $page->set($_POST['param']['id'], $_POST['param']);
			// если создали новую страницу
			if($flag>0) Engine::jsRedirect("".mod_admin::$admin_url."/pages-edit/id-{$flag}");
		}

		// удаляем страницу
		if($this->GET["pages"]=="delete"){
			$id = $this->GET["id"];
			$page = new Page();
			$child = new Page();
			// получим парент удаляемой страницы
			$parent = $page->get($id)->assoc();
			$parent = $page->row['parent'];
			// для всех дочерних страниц переопределим родителя
			$page->get(array("parent"=>$id));
			while($row = $page->assoc()){
				$child->set($row['id'], array("parent"=>$parent));
			}
			$page->delete($id);
			echo $this->GET["id"];
		}

	}
	//----------------------------------------------------------------------------------
	// логика страницы modules // Моудули сайта
	//----------------------------------------------------------------------------------
	function mainModules(){
		// показать все страницы, если в запросе был только один параметр
		if($this->GET["modules"]===""&&count($this->GET)==1){
			$data['_modules'] = $this->moduleTree(0);
			/*$data['_domain'] = Config::$domains[$this->edit_domain];*/
			$data['_buttons'] = $this->createButton("Новый модуль","".mod_admin::$admin_url."/modules-edit/create-module","icon-plus-module.png","inline-button");
			$data['_buttons'] .= $this->createButton("Новая группа","".mod_admin::$admin_url."/modules-edit/create-group","icon-plus-group.png","inline-button");
			echo Engine::templater($this->mod_path."/module-list.html",$data);
		}

		// окно редактирования страницы
		if($this->GET["modules"]==="edit"){
			$data['_content'] = "";
			if(isset($this->GET['id'])){
				$mod = new Module();
				$mod = $mod->get($this->GET['id'])->assoc();
			}

			if(isset($this->GET['create'])){
				// создаём модуль
				if($this->GET['create']==="module"){
					$mod['unit'] = "module";
				}
				// создаём группу
				if($this->GET['create']==="group"){
					$mod['unit'] = "group";
					$mod['parent'] = 0;
				}
				// если указан родитель
				if(isset($this->GET['parent'])){
					$parent = new Module();
					$parent = $parent->get($this->GET['parent'])->assoc();
					$mod['parent'] = $parent['id'];
				}
			}
			// если явно указали каким юнитом хотим показать страницу
			if(!isset($this->GET['unit'])) $this->GET['unit'] = "";
			if($this->GET['unit']){
				$mod['unit'] = $this->GET['unit'];
			}

			//$units = $unit;
			$data['_content'] .= $this->createUnitEditor($mod,"module");
			if(isset($mod['type'])){
				if($mod['type']==0) $data['_title'] = "Редактирование модуля";
				if($mod['type']==1) $data['_title'] = "Редактирование группы модулей";
			} else {
				$data['_title'] = "Новый модуль";
			}
			$data['_buttons'] = $this->createButton("Назад","".mod_admin::$admin_url."/modules","icon-left.png","inline-button");
			$data['_buttons'] .= $this->createButton("Сохранить","javaScript:{sendForm(\"module-form\");}","icon-save.png","inline-button");
			// запускаем админскую часть модуля
			if(file_exists(Engine::$root."/modules/{$mod['unit']}/{$mod['unit']}.class.php")){
				include_once(Engine::$root."/modules/{$mod['unit']}/{$mod['unit']}.class.php");
				// запускаем модуль для админской части
				$modRun = new $mod['unit'];
				if(method_exists($modRun,"admin")) $data['_admin'] = $modRun->admin($mod);
			}
			echo Engine::templater($this->mod_path."/module-edit.html",$data);
		}
		// сохраняем страницу
		if($this->GET["modules"]==="save"){

			$mod = new Module();
			// получаем поля таблицы
			$field = $mod->getFields();
			// проверим все записи, есть ли колонка для них в таблице
			foreach($_POST['param'] as $param=>$value){
				// если нету создаём колонку
				if(!isset($field[$param])) {
					$type = $_POST['type'][$param];
					// создаем новую колонку
					$mod->createField($param,$type);
				}
			}
			// почистим все поля, которые больше не нужны
			// для всех таких полей установим сзначние в пусто
			foreach($field as $area=>$value){
				if(!isset($_POST['param'][$area])) {
					$_POST['param'][$area] = "";
				}
			}
			$flag = $mod->set($_POST['param']['id'], $_POST['param']);
			// если создали новую страницу
			if($flag>0) Engine::jsRedirect("".mod_admin::$admin_url."/modules-edit/id-{$flag}");
		}

		// удаляем модуль
		if($this->GET["modules"]=="delete"){
			$id = $this->GET["id"];
			$mod = new Module();
			$child = new Module();
			// получим парент удаляемой страницы
			$parent = $mod->get($id)->assoc();
			$parent = $mod->row['parent'];
			// для всех дочерних модулей переопределим родителя
			$mod->get(array("parent"=>$id));
			while($row = $mod->assoc()){
				$child->set($row['id'], array("parent"=>$parent));
			}
			$mod->delete($id);
			echo $this->GET["id"];
		}
	}
	//----------------------------------------------------------------------------------
	// логика страницы UNITS // шаблоны данных
	//----------------------------------------------------------------------------------
	function mainUnits(){
		// показать все units, если в запросе был только один параметр
		if($this->GET["units"]===""&&count($this->GET)==1){
			$page = new Page();
			$mask = Engine::$root."/units/page/*.yaml"; # Выводит все yaml файлы
			foreach (glob($mask) as $filename) {
				if(basename($filename)==="tmp.xml") continue;
				$unitname = basename($filename,".yaml");

				$yaml = Yaml::YAMLLoad($filename);
				$unit = array();
				$unit["file"] = basename($filename,".yaml");
				$unit["caption"] = (string)$yaml['title'];
				if(!isset($yaml['extends'])) $yaml['extends']="";
				$unit["extends"] = (string)$yaml['extends'];
				//$unit["adesc"] = (string)$yaml['admin-description'];
				$unit['_pages'] = $page->get($unitname,"unit")->rowCount();
				foreach($yaml['areas'] as $name=>$param){
					if(@$param['systemtype']==="static"){
						if(!isset($unit['_static'])) $unit['_static'] = "";
						$unit['_static'].="<div class='static-params'><div>{$name} [{$param['caption']}]: </div> <span> {$param['value']}</span></div>";
					}
				}
				$unit['_buttons'] = $this->createButton("","".mod_admin::$admin_url."/units-edit/file-{$unitname}","icon-unit-edit.png","icon-button");
				// разрешим удаление юнитов с 0-м количеством страниц
				if($unit['_pages']==0) $unit['_buttons_delete'] = $this->createButton("","javaScript:{deleteUnit(\"{$unitname}\");}","icon-unit-delete.png","icon-button");
				$data['_units'][] = $unit;
				unset($unit);
				unset($yaml);
			}
			$data['_buttons'] = $this->createButton("Создать Unit","".mod_admin::$admin_url."/units-edit/create-unit","icon-plus-unit.png","inline-button");
			echo Engine::templater($this->mod_path."/unit-list.html",$data);
		}
		// редактирование
		if($this->GET["units"]==="edit"){

			if(!isset($this->GET['create'])){
				$unit = $this->GET['file'];
				$content = file_get_contents(Engine::$root."/units/page/{$unit}.yaml");
			} else {
				// создаём новый файл
				$unit = "new";
				$content = file_get_contents(Engine::$root."/units/page/tmp.yaml");
			}
			$lines = explode("\n",$content);
			$lines = (count($lines)+15)*17;

			// создадим поле с обозначением редактируемого файла
			$param['caption'] = "Unit";
			$param['name'] = "unit";
			$param['value'] = $unit;
			$data['_content'] = Area::template_linetext($param);
			// создадим поле редактора
			$param['name'] = "code";
			$param['value'] = $content;
			$param['caption'] = "Редактор";
			$param['height'] = $lines."px";
			$data['_content'] .= Area::template_codeyaml($param);
			$page = new Page();
			$count = $page->get(basename($unit,".yaml"),"unit")->rowCount();
			$data['_title'] = "Редактирование Unit: {$unit} ({$count} страниц)";

			$data['_buttons'] = $this->createButton("Назад","".mod_admin::$admin_url."/units","icon-left.png","inline-button");
			$data['_buttons'] .= $this->createButton("Сохранить","javaScript:{sendForm(\"unit-form\");}","icon-save.png","inline-button");

			echo Engine::templater($this->mod_path."/unit-edit.html",$data);
		}
		// сохранение
		if($this->GET["units"]==="save"){
			$unit = $_POST['param']['unit'];
			$file = fopen(Engine::$root."/units/page/{$unit}.yaml","w+");
			$text = $_POST['param']['code'];
			if (!$file)
			{
				echo("Ошибка открытия файла!");
			}
			else
			{
				fputs ($file, $text);
			}
			fclose ($file);
			// перезапишем статичные данные для всех страниц

            $yaml = Func::getUnitAreas(Engine::$root."/units/page/{$unit}.yaml");

			$page = new Page();
			// достаём одну запись запись , пусть будет запись с id=1. id=1 должна быть всегда в таблице
			$field = $page->getFields();
			$data = array();
			foreach($yaml['areas'] as $name=>$param){
				//$param = (array)$param;
				if(!isset($param['systemtype'])) $param['systemtype'] = "";
				if($param['systemtype']==="static"){
					$data['param'][$name] = (string)$param['value'];
					$data['type'][$name] = (string)$param['type'];
				}
			}

            $data['param']['unit_str'] = serialize($yaml);
            $data['type']['unit_str'] = "text";
			// проверим все записи, есть ли колонка для них в таблице
			foreach($data['param'] as $name=>$value){
				// если нету создаём колонку
				if(!isset($field[$name])) {
					$type = $data['type'][$name];
					$page->createField($name,$type);
				}
			}
			$pages = new Page();
			$pages->get($unit,"unit");
			$count = 0;
			while($row = $pages->assoc())
			{
				$page->set($row['id'], $data['param']);
				$count++;
			}
			echo "Перезаписано {$count} страниц";
		}
		// удаление
		if($this->GET["units"]==="delete"){
			$unit = $this->GET['file'];
			$page = new Page();
			$count = $page->get($unit,"unit")->rowCount();
			// удаляем только неиспользуемые юниты, остальное игнорируем
			if($count==0){
				unlink(Engine::$root."/units/page/{$unit}.yaml");
			}
		}
	}

	// Работа с произвольными базами и параметрами
	function mainBases(){


		// создаем таблицу, если её не существует
		if(isset($this->GET["base"])&&isset($this->GET["create"])){
			if($this->GET["base"]){
				$table = new DataBase();
				$table->createTable($this->GET["base"]);
			}
		}
		// вывод всех дополнительных баз
		if(isset($this->GET["base"])&&count($this->GET)==1&&$this->GET["base"]==""){
			$mask = Engine::$root."/units/base/*.yaml"; # Выводит все yaml файлы
			foreach (glob($mask) as $filename) {
				$unitname = basename($filename,".yaml");
				$yaml =  Yaml::YAMLLoad(Engine::$root."/units/base/{$unitname}.yaml");
				$base[$unitname]['title'] = $yaml['title'];
				$base[$unitname]['description'] = $yaml['admin-description'];
				$base[$unitname]['icon'] = Config::$back_template."/img/icons/{$yaml['icon']}";
			}
			$data["_bases"] = $base;
			echo Engine::templater($this->mod_path."/base-list.html",$data);
		}

		// если база указана то работаем с ней и выводим все поля
		if(isset($this->GET["base"])&&$this->GET["base"]!=""&&count($this->GET)==1){
			$base_name = $this->GET["base"];

			$row["id"] = "ID";
			$row["_edit"] = "";
			$yaml = Yaml::YAMLLoad(Engine::$root."/units/base/{$base_name}.yaml");
			foreach($yaml['areas'] as $name=>$param){
				if(@$param['table'] === "show" ) {
					$row[$name] = $param['caption'];
				}
			}
			$row["_delete"] = "";
			$data['_title'] = $yaml['title'];
			// помечаем какие поля брать из таблицы
			/*$fields = implode(", ",array_flip($row));

			// добавим промежуточный столбец для кнопок редактирования
			array_splice($row,1,0,"");
			array_splice($row,count($row),0,"");*/
			$data['_rows'][] = $row;

			$table = new DataBase();
			$table->setTable($base_name);
			$table->sql("SELECT * FROM {$base_name}");
			//$count = 0;
			while($r = $table->next()){
				$r = array_intersect_key($r,$row);

				// добавим кнопку редактирования
				$button = $this->createButton("","".mod_admin::$admin_url."/base-{$base_name}/edit-{$r['id']}","icon-edit.png","icon-button");
				array_splice($r,1,0,$button);
				// добавим кнопку удаления
				$button = $this->createButton("","javascript:deleteRecord({$r['id']},\"{$base_name}\")","icon-delete.png","icon-button");
				array_splice($r,count($r),0,$button);
				foreach($r as $key=>$n){
					if(isset($yaml['areas'][$key]['code'])) $r[$key]=str_replace("\n","<br>",htmlspecialchars($n));
				}
				$data['_rows'][] = $r;
				$count++;
			}

			$data['_buttons'] = $this->createButton("Назад","".mod_admin::$admin_url."/base","icon-left.png","inline-button");
			$data['_buttons'] .= $this->createButton("Новая запись","".mod_admin::$admin_url."/base-{$base_name}/edit-add","icon-plus.png","inline-button");

			// формируем вывод полей базы
			echo Engine::templater($this->mod_path."/base-item-list.html",$data);
		}

		// окно редактирования записи
		if(isset($this->GET["base"])&&isset($this->GET["edit"])){
			$base_name = $this->GET["base"];
			$data = array();
			$base = new DataBase();
			$base->setTable($this->GET["base"]);
			$base = $base->get($this->GET["edit"])->assoc();
			$base["unit"] = $this->GET["base"];

			$data['_content'] = $this->createUnitEditor($base,"base");
			// поределяем какой домен выбрать при открывании страницы по кнопке "Перейти на срнаицу"
			/*
			$domain = $base["domain"];
			if($domain==="*"){$domain = Engine::$domain;} else {$domain = Config::$domains[$base['domain']];}
			*/
			$data['_base'] = $this->GET["base"];

			$data['_title'] = "Редактирование страницы";
			$data['_buttons'] = $this->createButton("Сохранить","javaScript:{sendForm(\"page-form\");}","icon-save.png","inline-button");
			$data['_buttons'] .= $this->createButton("Закрыть","".mod_admin::$admin_url."/base-{$base_name}","icon-close.png","inline-button");
			echo Engine::templater($this->mod_path."/base-edit.html",$data);
		}

		// сохраняем
		if(isset($this->GET["base"])&&isset($this->GET["save"])){
			$base_name = $this->GET["base"];
			$base = new DataBase();
			$base->setTable($this->GET["base"]);
			// получаем все поля в таблице
			$field = $base->getFields();
			// проверим все записи, есть ли колонка для них в таблице
			foreach($_POST['param'] as $param=>$value){
				// если нету создаём колонку
				if(!isset($field[$param])) {
					$type = $_POST['type'][$param];
					echo "'{$param}'='{$type}'\n";
					$base->createField($param,$type);
				}
			}
			// предзаписывающие функции
			$yaml = Yaml::YAMLLoad(Engine::$root."/units/base/{$base_name}.yaml");
			if(isset($yaml['pre_func'])){
				$line = explode("\n",$yaml['pre_func']);
				foreach($line as $kv){
					$kv = explode("=",$kv);
					$param = trim($kv[0]);
					$func = "pre_".trim($kv[1]);
					$_POST['param'][$param] = pre::$func($_POST['param'], $param);
				}
			}
			// предобработка поля перед записью
			$flag = $base->set($_POST['param']['id'], $_POST['param']);
			// если создали новую страницу
			if($flag>0) Engine::jsRedirect("".mod_admin::$admin_url."/base-".$this->GET["base"]."/edit-{$flag}");
		}

		// удаляем запись
		if(isset($this->GET["base"])&&isset($this->GET["delete"])){
			$base_name = $this->GET["base"];
			$id = $this->GET["delete"];

			$base = new DataBase();
			$base->setTable($base_name);
			$base->delete($id);
		}
	
	}

}

?>
