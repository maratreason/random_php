<?
//класс полей обработчиков для модуля админ панели
class  Area{
	/*
	Шаблон: любой
	Поле: любое [TEXT]
	Описание: создаёт текстовое поле для редактирования
	*/
	function template_linetext($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
		if(!isset($param['class'])) $param['class'] = "";
		$out = "<label class='{$param['class']}'>{$param['caption']}<input name='{$type}[{$param['name']}]' type='text' value='{$param['value']}' class='input' {$param['attr']}/></label>";
		return $out;
	}
	/*
	Шаблон: любой
	Поле: любое [INTEGER]
	Описание: создаёт текстовое поле для редактирования
	*/
	function template_integer($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
		$out = "<label class='{$param['class']}'>{$param['caption']}<input name='{$type}[{$param['name']}]' type='text' value='{$param['value']}' class='input number-area' {$param['attr']}/></label>";
		return $out;
	}

	/*
	Шаблон: любой
	Поле: любое [TEXT]
	Описание: создаёт текстовое поле для редактирования
	*/
	function template_password($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
		$out = "<label class='{$param['class']}'>{$param['caption']}<input name='{$type}[{$param['name']}]' type='password' value='{$param['value']}' class='input' {$param['attr']}/></label>";
		return $out;
	}

	/*
	Шаблон: любой
	Поле: любое [TEXT]
	Описание: создаёт текстовое поле для редактирования
	*/
	function template_datetime($param,$type="param")
	{
		if(!$param['value']){
			$value = date("d/m/Y H:i:s",mktime());
		} else {
			$value = date("d/m/Y H:i:s",strtotime($param['value']));
		}
		if(!isset($param['attr'])) $param['attr'] = "";
		$out = "<label class='{$param['class']}'>{$param['caption']}<input name='{$type}[{$param['name']}]' type='text' value='{$value}' class='input datetime-picker' {$param['attr']}/></label>";
		return $out;
	}

	/*
	Шаблон: любой
	Поле: любое [HIDDEN]
	Описание: создаёт скрытое поле
	*/
	function template_hidden($param,$type="param")
	{
		$out = "<input name='{$type}[{$param['name']}]' type='hidden' value='{$param['value']}'/>";
		return $out;
	}
	/*
	Шаблон: любой
	Поле: любое [TEXTAREA]
	Описание: создаёт многострочное текстовое поле для редактирования
	*/
	function template_text($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
        if(!isset($param['style'])) $param['style'] = "";
		$out = "<label class='{$param['class']}'>{$param['caption']}<textarea name='{$type}[{$param['name']}]' class='input' {$param['attr']} style='{$param['style']}'>{$param['value']}</textarea></label>";
		return $out;
	}

	function template_tiny($param,$type="param")
	{
		if(!isset($param['style'])) $param['style'] = "";
        $out = "<label class='{$param['class']}'>{$param['caption']}<textarea name='{$type}[{$param['name']}]' class='input text-editor' style='{$param['style']}' >{$param['value']}</textarea></label>";
		return $out;
	}



		/*
	Шаблон: любой
	Поле: любое [IMAGE]
	Описание: создаёт текстовое поле для редактирования
	*/
	function template_image($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
		$out = "<label class='{$param['class']}'>{$param['caption']}";

        $out .="<input name='{$type}[{$param['name']}]' type='text' value='{$param['value']}' class='input image-input' id='area-{$param['name']}' {$param['attr']}/>";
		//$out .= "<a href='javascript:;' onclick=\"moxman.browse({fields: 'area-{$param['name']}',no_host: true})\" class='button'>Выбрать</a>";
		$out .= mod_admin::createButton("","javascript:;","icon-browse.png","browse-image","onclick=\"moxman.browse({fields: 'area-{$param['name']}',no_host: true})\"");
        $out .="</label>";
		return $out;
	}

	/*
	Шаблон: любой
	Поле: любое [TEXT]
	Описание: создаёт текстовое поле для url
	*/
	function template_cirilicurl($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
		if(!isset($param['class'])) $param['class'] = "";
		$out = "<label class='{$param['class']}'>{$param['caption']}";
        $out .= "<input name='{$type}[{$param['name']}]' type='text' value='{$param['value']}' class='input url-input' {$param['attr']}/>";
        $out .= mod_admin::createButton("","javascript:;","icon-generate.png","cirilic-url","");
        $out .="</label>";
		return $out;
	}

	/*
	Шаблон: любой
	Поле: любое [IMAGE]
	Описание: создаёт текстовые поля для загрузки нескольких изображений
	*/
	function template_multiimage($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
		$out = "<label class='{$param['class']} multiImages'>{$param['caption']}<input name='{$type}[{$param['name']}]' type='hidden' value='{$param['value']}' class='input multiimage-input image-input' id='area-{$param['name']}' {$param['attr']}/><div id='multiimages-{$param['name']}' class='multiimages-block'></div></label>";
		return $out;
	}

	/*
	Шаблон: любой
	Поле: любое [multiselect]
	Описание: поле с выбором нескольких значений
	*/

	function template_multiselect($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
		$values = explode(",", $param['value']);
        $values = array_diff($values, array(''));
        $out = "<label  class='{$param['class']} multiselect'>{$param['caption']}</label>";
        $out .= "<input name='{$type}[{$param['name']}]' type='hidden' value='{$param['value']}' class='input' {$param['attr']}/>";
		/*Создаем столбцы если вариантов очень много*/
		$columns = array();
		$columns[] = $param['select'];
		if(count($param['select'])/8>1) $columns = array_chunk($param['select'],6,true);
		$out .= "<div class='select-table'>";
		foreach($columns as $column){
			$out .= "<div class='select-column'>";
			foreach($column as $value=>$caption){
				if(in_array($value, $values)) $checked = "checked='checked'"; else $checked = "";
				$out .= "<label class='checkbox'><input type='checkbox' value='{$value}' $checked/> <div></div><span>{$caption}</span></label>";
			}
			$out .= "</div>";
		}
		$out .= "</div>";
		//$out .= "</label>";

		return $out;
	}

    /*
    Шаблон: любой
    Поле: любое [multiselect]
    Описание: поле с выбором нескольких значений
    */

    function template_multiselectgroup($param,$type="param")
    {
        if(!isset($param['attr'])) $param['attr'] = "";
        $values = explode(",", $param['value']);
        $values = array_diff($values, array(''));
        $out = "<label  class='{$param['class']} multiselect'>{$param['caption']}";
        $out .= "<input name='{$type}[{$param['name']}]' type='hidden' value='{$param['value']}' class='input' {$param['attr']}/>";
        // найдем все группы
        $groups = array();
        foreach($param['groups'] as $name=>$fields) {

            $keys = explode("-",$fields['group']);
            $addFlag = false;
            foreach($param['select'] as $key=>$caption){
                // начинаем добавлять в группу элементы
                if($key===$keys[0]&&$addFlag==false) $addFlag = true;
                if($addFlag){
                    $groups[$name]['caption'] = $fields['caption'];
                    $groups[$name]['list'][$key] = $caption;
                }
                // дошли до последнего элемента, перестаем добавлять
                if($key===$keys[1]&&$addFlag==true) {$addFlag = false; break;}
            }
        }
        $out .= "<div class='list-533'>";
        foreach ($groups as $name=>$fields){
            $out .= "<div class='item'>";
            $out .= "<h2>{$name}</h2><br>";
            foreach($fields['list'] as $value=>$caption){
                if(in_array($value, $values)) $checked = "checked='checked'"; else $checked = "";
                $out .= "<label class='checkbox'><input type='checkbox' value='{$value}' $checked/> {$caption}</label>";
                //$out .= "{$key}<br>";
            }
            $out .= "</div>";
        }
        $out .= "</div>";
        /*
        foreach($param['select'] as $value=>$caption){
            if(in_array($value, $values)) $checked = "checked='checked'"; else $checked = "";
            $out .= "<label class='checkbox'><input type='checkbox' value='{$value}' $checked/> {$caption}</label>";
        }*/
        $out .= "</label>";
        return $out;
    }

	/*
	Шаблон: любой
	Поле: любое [SELECT]
	Описание: поле с выбором значений
	*/
	private function create_selectoptions($_options,$_default){
		$out = "";
		foreach($_options as $value=>$caption) {
			if($_default == trim($value)) $selected = "selected"; else $selected = "";
			$out .= "<option value='{$value}' {$selected} >{$caption}</option>";
		}
		return $out;
	}
	function template_select($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
		$out = "<label  class='{$param['class']}'>{$param['caption']}<select name='{$type}[{$param['name']}]' class='input' {$param['attr']}>";
		$out .= self::create_selectoptions($param["select"],$param["value"]);
		$out .= "</select></label>";
		return $out;
	}

	/*
	Шаблон: любой
	Поле: любое [TEXT]
	Описание: выбор цвета
	*/
	function template_color($param,$type="param")
	{
		// получаем цвета из стилей config.scss
		if(file_exists(Engine::$root."/templates/front/css/jedcoder/_config.scss")){
			$css = file_get_contents(Engine::$root."/templates/front/css/jedcoder/_config.scss");
			$css = explode("\n",$css);
			$colors = array();
			foreach($css as $line){
				if(strpos($line,"endcolors")) break;
				if(strpos($line,'$color')!==false){
					$mas = explode(":",$line);
					$colors[trim($mas[0])] = trim(str_replace(";","",$mas[1]));
				}
			}


			$out = "";
			if(!isset($param['attr'])) $param['attr'] = "";
			$out .= "<label class='{$param['class']} selectcolor'>{$param['caption']}";
			$out .= "<div class='colorpicker'></div><input name='{$type}[{$param['name']}]' type='text' value='{$param['value']}' class='input color' {$param['attr']}/>";
			$out .= "<div class='styleColorList'>";
			foreach ($colors as $key => $value) {
				$out .= "<div class='styleColor' style='background-color:{$value}' data-key='{$key}' data-color='{$value}'></div>";
			}
			$out .= "</div>";
			$out .= "<div class='preview'></div></label>";
			return $out;
		} else {
			return '';
		}
	}

	/*
	Шаблон: любой
	Поле: любое [SELECTDOMAIN]
	Описание: поле с выбором домена
	*/
	function template_selectdomain($param,$type="param"){
		$out = "<label  class='{$param['class']}'>{$param['caption']}<select name='{$type}[{$param['name']}]' class='input'>";
		foreach(Config::$domains as $value=>$option) {
			if($param['value'] == trim($value)) $selected = "selected"; else $selected = "";
			$out .= "<option value='{$value}' {$selected}>{$option}</option>";
		}
		$out .= "</select></label>";
		return $out;
	}
	/*
	Шаблон: любой
	Поле: любое [SELECTCITY]
	Описание: Выбор города из списка доменов добавленных в системе
	*/

	function template_selectcity($param,$type="param"){
		$out = "<label  class='{$param['class']}'>{$param['caption']}<select name='{$type}[{$param['name']}]' class='input'>";
		$base = new DataBase();
		$base->setTable("vars");
		$cities = $base->get()->all();
		foreach($cities as $city) {
			$value = $city['city'];
			$option = "{$city['city']} ({$city['domen']})";
			if($param['value'] == trim($value)) $selected = "selected"; else $selected = "";
			$out .= "<option value='{$value}' {$selected}>{$option}</option>";
		}
		$out .= "</select></label>";
		return $out;
	}
	/*
	Шаблон: любой
	Поле: любое [SELECTUNIT]
	Описание: поле с выбором шаблона полей
	в param необходимо передать param['object'] это массив страницы, чтобы определить список доступных странице шаблонов
	*/
	function template_selectunit($param,$type="param"){

		$page = new Page();
		if(!isset($param['object']['parent'])) $param['object']['parent'] = "";
		$page = $page->get($param['object']['parent'],"id")->assoc();
		$page = explode(",",trim($page["child_units"]));
		$page = array_diff($page,array(''));// вычтем пустые
		$units = array();
		// если есть шаблоны юнитов то выведем их иначе все юниты системы
		if(count($page)!=0){
			foreach($page as $p){
				$p = trim($p);
				//$xml =  simplexml_load_file(Engine::$root."/units/page/{$p}.xml");
				$yaml = Yaml::YAMLLoad(Engine::$root."/units/page/{$p}.yaml");
				$name = basename($p,".yaml");
				//if($xml->TYPE!="HIDDEN"&&$xml->TYPE!="ADMIN")
				if(!isset($units[$name])) $units[$name] = "";
				$units[$name] .= @$yaml['title']." [{$name}.yaml]";
			}
		} else {
			$mask = Engine::$root."/units/page/*.yaml";
			foreach (glob($mask) as $filename) {
				$yaml = Yaml::YAMLLoad($filename);
				$name = basename($filename,".yaml");
				if(!isset($yaml['type'])) $yaml['type'] = "";
				if($yaml['type']!="hidden"){
					if(!isset($units[$name])) $units[$name] = "";
					$units[$name] .= $yaml['title']." [{$name}.yaml]";
				}
			}
		}
		$out = "<label  class='{$param['class']}'>{$param['caption']}<select name='{$type}[{$param['name']}]' class='input' id='unit-select'>";
		foreach($units as $value=>$option) {
			if($param["value"] == trim($value)) $selected = "selected"; else $selected = "";
			$out .= "<option value='{$value}' {$selected}>{$option}</option>";
		}
		$out .= "</select></label>";
		return $out;
	}

	/*
	Шаблон: любой
	Поле: любое [CODE]
	Описание: поле с подсветкой кода
	*/
	function template_codeyaml($param,$type="param"){
		if(!isset($param['height'])) $height = "200px"; else $height = $param['height'];
		if(!isset($param['class'])) $param['class'] = "";
		$out = "<label class='{$param['class']}'>{$param['caption']}<textarea name='{$type}[{$param['name']}]' id='code-{$param['name']}' class='code-editor' type='yaml'>{$param['value']}</textarea>";
		$out .= "<div id='code-{$param['name']}-area' class='input-code' style='height:{$height}'></div></label>";
		return $out;
	}

	/*
	Шаблон: любой
	Поле: любое [codehtml]
	Описание: поле с подсветкой кода
	*/
	function template_codehtml($param,$type="param"){
		if(!isset($param['height'])) $height = "200px"; else $height = $param['height'];
		$out = "<label class='{$param['class']}'>{$param['caption']}<textarea name='{$type}[{$param['name']}]' id='code-{$param['name']}' class='code-editor' type='html'>{$param['value']}</textarea>";
		$out .= "<div id='code-{$param['name']}-area' class='input-code' style='height:{$height}'></div></label>";
		return $out;
	}

		/*
	Шаблон: любой
	Поле: любое [SELECTMODULE]
	Описание: поле с выбором шаблона полей
	в param необходимо передать param['object'] это массив страницы, чтобы определить список доступных странице шаблонов
	*/
	function template_selectmodule($param,$type="param"){

		$page = new Module();
		$mask = Engine::$root."/units/module/*.yaml";
		foreach (glob($mask) as $filename) {
			$yaml = Yaml::YAMLLoad($filename);
			$name = basename($filename,".yaml");
			if(!isset($yaml['type'])) $yaml['type']="";
			if($yaml['type']!="hidden"){
				if(!isset($units[$name])) $units[$name] = "";
				$units[$name] .= $yaml['title']." [{$name}.yaml]";
			}
		}
		// проходим по папкам модулей
		$mask = Engine::$root."/modules/mod_*";
		foreach (glob($mask) as $filename) {
			$dir = basename($filename);
			$file = $filename."/{$dir}.yaml";
			if(file_exists($file)){
				$yaml = Yaml::YAMLLoad($file);
				$name = basename($filename,".yaml");
				if(!isset($yaml['type'])) $yaml['type']="";
				if($yaml['type']!="hidden"){
					if(!isset($units[$name])) $units[$name] = "";
					$units[$name] .= $yaml['title']." [{$name}.yaml]";
				}
			}
		}
		$out = "<label  class='{$param['class']}'>{$param['caption']}<select name='{$type}[{$param['name']}]' class='input' id='module-select'>";
		foreach($units as $value=>$option) {
			if($param["value"] == trim($value)) $selected = "selected"; else $selected = "";
			$out .= "<option value='{$value}' {$selected}>{$option}</option>";
		}
		$out .= "</select></label>";
		return $out;
	}

	/*
	Шаблон: любой
	Поле: любое [CODE]
	Описание: выбор родителя
	*/
	// получаем все каталоги в который можно вложить данную страницу
	function recursFindParents($_parent,$_cur,$_level=0){
		$out = array();
        $unit = ",".trim($_cur["unit"]).",";
        if(isset($_cur["id"])) $_cur_id = $_cur["id"]; else $_cur_id = 0;


		$page = new Page();
		$page->get(array("domain"=>array_search(Engine::$domain,Config::$domains), "parent"=>$_parent, "type"=>1, "id"=>"!{$_cur_id}"));
		while($row = $page->assoc()){
			// для функции strpos добавим доплнительные символы для точного поиска
            $u = "-,".$row["child_units"].",";
            $u = str_replace(" ","",$u);
            //echo "- $unit = {$row['unit']} : $u <br>";
            if(strpos($u,$unit)>0){
                $row['_level'] = $_level;
                $out[] = $row;
            }
			// для всех дочерних элементов которые нахуодятся в текущем каталоге отменяем рекурсию
			if($row['id']!=$_cur_id){

				$out = array_merge($out,self::recursFindParents($row["id"],$_cur,$_level+1));
			}
		}
		return $out;
	}

	function template_selectparent($param,$type="param"){

		$out = "<label class='{$param['class']}'>{$param['caption']}<select name='{$type}[{$param['name']}]' class='input'>";
		// не иметь раздел может только главная страница сайта
        if($param["object"]["unit"]=="main") $out .= "<option value='0'>Нет</option>";
		$category = self::recursFindParents(0,@$param['object']);
		foreach($category as $row){
			$padding = str_repeat("-",$row['_level']);
			if($param["value"] == trim($row['id'])) $selected = "selected"; else $selected = "";
			$out .= "<option value='{$row['id']}' {$selected} >{$padding} {$row['url_name']} ({$row['url']})</option>";
		}
		$out.= "</select></label>";
		return $out;
	}

	function recursFindAllParents($_parent,$_cur,$_level=0){
		$out = array();
        $unit = ",".trim($_cur["unit"]).",";
        if(isset($_cur["id"])) $_cur_id = $_cur["id"]; else $_cur_id = 0;


		$page = new Page();
		$page->get(array("domain"=>array_search(Engine::$domain,Config::$domains), "parent"=>$_parent, "type"=>1, "id"=>"!{$_cur_id}"));
		while($row = $page->assoc()){
			// для функции strpos добавим доплнительные символы для точного поиска
            //$u = "-,".$row["child_units"].",";
            //$u = str_replace(" ","",$u);
            //echo "- $unit = {$row['unit']} : $u <br>";
            //if(strpos($u,$unit)>0){
                $row['_level'] = $_level;
                $out[] = $row;
            //}
			// для всех дочерних элементов которые нахуодятся в текущем каталоге отменяем рекурсию
			if($row['id']!=$_cur_id){

				$out = array_merge($out,self::recursFindAllParents($row["id"],$_cur,$_level+1));
			}
		}
		return $out;
	}

	function template_selectallparent($param,$type="param"){

		$out = "<label class='{$param['class']}'>{$param['caption']}<select name='{$type}[{$param['name']}]' class='input'>";
		// не иметь раздел может только главная страница сайта
        $out .= "<option value='0'>Нет</option>";
		$category = self::recursFindAllParents(0,@$param['object']);
		foreach($category as $row){
			$padding = str_repeat("-",$row['_level']);
			if($param["value"] == trim($row['id'])) $selected = "selected"; else $selected = "";
			$out .= "<option value='{$row['id']}' {$selected} >{$padding} {$row['url_name']} ({$row['url']})</option>";
		}
		$out.= "</select></label>";
		return $out;
	}


	// получаем все группы в который можно вложить данный модуль
	function recursFindGroups($_parent,$_cur,$_level=0){
		$out = array();
		$page = new Module();
		$page->get(array("parent"=>$_parent, "type"=>1, "id"=>"!{$_cur}"));
		while($row = $page->assoc()){
			$row['_level'] = $_level;
			$out[] = $row;
			// для всех дочерних элементов которые нахуодятся в текущем каталоге отменяем рекурсию
			if($row['id']!=$_cur){
				$out = array_merge($out,self::recursFindGroups($row['id'],$_cur,$_level+1));
			}
		}
		return $out;
	}
	function template_selectgroup($param,$type="param"){
		$out = "<label class='{$param['class']}'>{$param['caption']}<select name='{$type}[{$param['name']}]' class='input'>";
		$out .= "<option value='0'>Нет</option>";
		if(!isset($param['object']['id'])) $param['object']['id'] = "";
		$category = self::recursFindGroups(0,$param['object']['id']);
		foreach($category as $row){
			$padding = str_repeat("-",$row['_level']);
			if($param["value"] == trim($row['id'])) $selected = "selected"; else $selected = "";
			$out .= "<option value='{$row['id']}' {$selected}>{$padding} {$row['caption']} ({$row['name']})</option>";
		}
		$out.= "</select></label>";
		return $out;
	}

	function template_changeauthor($param, $type="param"){
		$base = new DataBase();
		$base->setTable("users");
		$base->get(array("type"=>2,"status"=>1));
		while($user = $base->next()){
			$select["select"][$user['name']] = $user['fio'];
		}
		if(!isset($param['attr'])) $param['attr'] = "";
		$out = "<label  class='{$param['class']}'>{$param['caption']}<select name='{$type}[{$param['name']}]' class='input' {$param['attr']}>";
		$out .= self::create_selectoptions($select["select"],$param["value"]);
		$out .= "</select></label>";
		return 	$out;
	}

	//	редактирование тегов акций для продуктов
	function template_discounttags($param, $type="param"){
		$page = $param['object'];
		if(!isset($page['item_discounts'])) $page['item_discounts'] = "";
		$tags = explode(",",$page['item_discounts']);
		foreach($tags as $key=>$tag){
			$tags[$key] = trim($tag);
		}
		$base = new DataBase();
		$base->setTable("discounts");
		$base->sql("SELECT * FROM discounts");
		$out = "<label class='{$param['class']}'>{$param['caption']}</label><div class='label-area'>";
		while($row = $base->next()){
			if(in_array($row['id'],$tags)) $checked = "checked='checked'"; else $checked = "";
			$out .= "<label  class='checkbox'><input type='checkbox' {$checked} tid='{$row['id']}' class='tags-check'/><div></div><span> {$row['name']}</span></label>";

		}
		if(!isset($param['attr'])) $param['attr'] = "";
		$out .= "<input name='{$type}[{$param['name']}]' type='hidden' value='{$param['value']}' class='input' {$param['attr']} id='tags-list'/></div>";
		//$out .= "<label class='{$param['class']}'>{$param['caption']}<input name='{$type}[{$param['name']}]' type='text' value='{$param['value']}' class='input' {$param['attr']}/></label>";
		return $out;
	}


	//	редактирование тегов для статьи
	function template_tagseditor($param, $type="param"){
		$page = $param['object'];
		if(!isset($page['content_tags'])) $page['content_tags'] = "";
		$tags = explode(",",$page['content_tags']);
		foreach($tags as $key=>$tag){
			$tags[$key] = trim($tag);
		}
		$base = new DataBase();
		$base->setTable("tags");
		$base->sql("SELECT * FROM tags");
		$out = "<label class='{$param['class']}'>{$param['caption']}</label><div class='label-area'>";
		while($row = $base->next()){
			if(in_array($row['id'],$tags)) $checked = "checked='checked'"; else $checked = "";
			$out .= "<input type='checkbox' {$checked} tid='{$row['id']}' class='tags-check'/> {$row['name']}<br/>";
		}
		if(!isset($param['attr'])) $param['attr'] = "";
		$out .= "<input name='{$type}[{$param['name']}]' type='hidden' value='{$param['value']}' class='input' {$param['attr']} id='tags-list'/></div>";
		//$out .= "<label class='{$param['class']}'>{$param['caption']}<input name='{$type}[{$param['name']}]' type='text' value='{$param['value']}' class='input' {$param['attr']}/></label>";
		return $out;
	}

	/*
	Шаблон: для карточки товара ТАРКО
	Поле: любое [multiselect]
	Описание: поле с выбором нескольких значений
	*/

	function template_setstok($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
		$values = explode(",", $param['value']);
        $values = array_diff($values, array(''));
        $out = "<label  class='{$param['class']} multiselect'>{$param['caption']}";
        $out .= "<input name='{$type}[{$param['name']}]' type='hidden' value='{$param['value']}' class='input' {$param['attr']}/>";
		$base = new DataBase();
		$base->setTable("discounts");
		$stoks = $base->sql("SELECT * FROM stoks")->all();
		foreach($stoks as $stok ){
		    if(in_array($stok['city'], $values)) $checked = "checked='checked'"; else $checked = "";
            $out .= "<label class='checkbox'><input type='checkbox' value='{$stok['city']}' $checked/><div></div><span> {$stok['city']} ({$stok['made']})</span></label>";
		}
		$out .= "</label>";
		return $out;
	}

	/*
	Шаблон:
	Поле: любое [multiselect]
	Описание: Выбор нескольких сотрудников
	*/

	function template_users($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
		$values = explode(",", $param['value']);
        $values = array_diff($values, array(''));
        $out = "<label  class='{$param['class']} multiselect'>{$param['caption']}</label>";
        $out .= "<input name='{$type}[{$param['name']}]' type='hidden' value='{$param['value']}' class='input' {$param['attr']}/>";
		$out .="<div class='select-table'>";
		$base = new DataBase();
		$base->setTable("managers");
		$users = $base->get()->all();
		foreach($users as $user ){
		    if(in_array($user['id'], $values)) $checked = "checked='checked'"; else $checked = "";
            $out .= "<label class='checkbox'><input type='checkbox' value='{$user['id']}' $checked/><div></div><span><strong>{$user['name']}</strong><br>{$user['name_type']}</span></label>";
		}
		$out .= "</div>";

		return $out;
	}

	/*
	Шаблон:
	Поле: любое [multiselect]
	Описание: Выбор нескольких объектов для показа
	*/

	function template_objects($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
		if(!isset($param['units'])) $param['units'] = "!#";
		$values = explode(",", $param['value']);
        $values = array_diff($values, array(''));
        $out = "<label  class='{$param['class']} multiselect'>{$param['caption']}</label>";
        $out .= "<input name='{$type}[{$param['name']}]' type='hidden' value='{$param['value']}' class='input' {$param['attr']}/>";
		$out .="<div class='select-table'>";
		$base = new DataBase();
		$base->setTable("pages");
		$users = $base->get(array("parent"=>@$param['id'],"unit"=>$param['units']))->all();
		foreach($users as $user ){
		    if(in_array($user['id'], $values)) $checked = "checked='checked'"; else $checked = "";
            $out .= "<label class='checkbox'><input type='checkbox' value='{$user['id']}' $checked/><div></div><span>{$user['url_name']}</span></label>";
		}
		$out .= "</div>";

		return $out;
	}

	//	редактирование тегов для продукта - продукт может относится к нескольким разделам каталога
    //  уникальный / заточен под определённый проект!
	function template_catalogtags($param, $type="param"){
		$page = $param['object'];
		if(!isset($page['content_tags'])) $page['content_tags'] = "";
		$tags = explode(",",$page['content_tags']);
		foreach($tags as $key=>$tag){
			$tags[$key] = trim($tag);
		}
		$base = new DataBase();
		$base->setTable("tags");
		$base->sql("SELECT * FROM pages WHERE unit='catalogcat' AND parent != 67 AND type=1");
		$out = "<label class='{$param['class']}'>{$param['caption']}</label><div class='label-area'>";
		while($row = $base->next()){
            if($page['parent']!=$row['id']){
			 if(in_array($row['id'],$tags)) $checked = "checked='checked'"; else $checked = "";
			 $out .= "<input type='checkbox' {$checked} tid='{$row['id']},{$row['parent']},67' class='tags-check'/> {$row['url_name']}<br/>";
            }
		}
		if(!isset($param['attr'])) $param['attr'] = "";
		$out .= "<input name='{$type}[{$param['name']}]' type='hidden' value='{$param['value']}' class='input' {$param['attr']} id='tags-list'/></div>";
		//$out .= "<label class='{$param['class']}'>{$param['caption']}<input name='{$type}[{$param['name']}]' type='text' value='{$param['value']}' class='input' {$param['attr']}/></label>";
		return $out;
	}

	// редактор для слайдера
	function template_json_slider($param, $type="param"){
		$sliders = json_decode($param['value'],true);
		$param['class'] = "slide-array";
		$out = Area::template_text($param);
		foreach($sliders as $key=>$slide){
			$out .= "<div class='block-50' id='slide-{$key}'><div class='block-content'><h2>Слайдер №".($key+1)."</h2>";

			// картинка
			$p['caption'] = "Фон";
			$p['name'] = "image".$key;
			$p['value'] = $slide['image'];
			$p['class'] = 'slide-image';
			//$p['attr'] = "id='image-{$key}'";
			$out .= Area::template_image($p,"slide");
			// заголовок
			$p['caption'] = "Заголовок";
			$p['name'] = "title".$key;
			$p['value'] = $slide['title'];
			$p['class'] = 'slide-title';
			//$p['attr'] = "id='title-{$key}'";
			$out .= Area::template_linetext($p,"slide");
			// text
			$p['caption'] = "Текст";
			$p['name'] = "text".$key;
			$p['value'] = $slide['text'];
			$p['class'] = 'slide-text';
			//$p['attr'] = "id='text-{$key}'";
			$out .= Area::template_text($p,"slide");
			// url
			$p['caption'] = "Ссылка на страницу";
			$p['name'] = "link".$key;
			$p['value'] = $slide['link'];
			$p['class'] = 'slide-link';
			//$p['attr'] = "id='link-{$key}'";
			$out .= Area::template_linetext($p,"slide");
			$out .= "</div></div>";
		}
		return $out;
	}


	/*
	Шаблон: любой
	Поле: любое [TEXTAREA]
	Описание: создаёт многострочное текстовое поле для редактирования
	*/
	function template_priceCity($param,$type="param")
	{
		$values = json_decode($param['value'], true);
		$base = new DataBase("vars");
		$base = $base->get()->all();
		if(!isset($param['attr'])) $param['attr'] = "";
        if(!isset($param['style'])) $param['style'] = "";
		$out = "<label class='{$param['class']} multiprices'>{$param['caption']}";
		$out .= "<input type='hidden' name='{$type}[{$param['name']}]' class='input mainvalue' {$param['attr']} style='{$param['style']}' value='{$param['value']}'>";
		$out .= "<div class='values'>";
		foreach($base as $city){
			$out .= "{$city['city']}";
			$val = $values[$city['city']];
			$out .= "<input type='number' value= '{$val}' data-key='{$city['city']}' class='input' style='{$param['style']}'>";
		}
		$out .= "</div>";
		$out .= "</label>";
		return $out;
	}


	/*
	Шаблон:
	Поле: любое [multiselect]
	Описание: Выбор нескольких объектов для показа
	*/

	function template_marketcat($param,$type="param")
	{
		if(!isset($param['attr'])) $param['attr'] = "";
		if(!isset($param['units'])) $param['units'] = "!#";
		$values = explode(",", $param['value']);
        $values = array_diff($values, array(''));
        $out = "<label  class='{$param['class']} multiselect'>{$param['caption']}</label>";
        $out .= "<input name='{$type}[{$param['name']}]' type='hidden' value='{$param['value']}' class='input' {$param['attr']}/>";
		$out .="<div class='select-table'>";
		$base = new DataBase();
		$base->setTable("pages");
		$users = $base->get(array("unit"=>"market_category","parent"=>"!451"))->all();
		foreach($users as $user ){
		    if(in_array($user['id'], $values)) $checked = "checked='checked'"; else $checked = "";
            $out .= "<label class='checkbox'><input type='checkbox' value='{$user['id']}' $checked/><div></div><span>{$user['url_name']}</span></label>";
		}
		$out .= "</div>";

		return $out;
	}

}


?>
