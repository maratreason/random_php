<?
class mod_feedback extends Module{
	function __construct(){

		$this->mod_path = dirname(__FILE__);
		$this->mod_name = "mod_feedback"; // получим класс
		if(!Engine::$admin){

			// стандартная инициализация модуля
			$this->Init();
		} else {
			// скрипты админской части

		}
	}

	// обработчик ajax событий
	public function ajax(){
		if(isset($data['url_before'])){
			 $this->send();
		} /*else {
			$this->main();
		}*/
	}
	function send(){

		$data = $_POST;
		$module = new Module(); // получаем параметры модуля
		$mod = $module->get(array("name"=>$data['mod']))->assoc();
		$fields = "";
		$sms_fields = "";
		if($mod['mail_template']) // если есть собственный шаблон письма, то формируем письмо по нему
		{
			$replace = array();
			foreach($data['value'] as $key=>$input)
			{
				$params['label'] = $data['label'][$key];
				if($input) $params['value'] = $input; else $params['value'] = "&nbsp;";
				$replace["[#{$key}#]"] = $input;
			}
			$fields = sys_templaterKeys($mod['mail_template'],$replace);
		}
		else // формирование письма без шаблона
		{
			foreach($data['value'] as $key=>$input)
			{
				$params['label'] = $data['label'][$key];
				if($input) $params['value'] = $input; else $params['value'] = "&nbsp;";
				$fields.=Engine::templater(Engine::$root."/modules/mod_feedback/email_line.html",$params);
				$sms_fields .= " ".$params['value'];
			}
		}
		$url['label'] = "Страница отправки";
		$url['value'] = "<a href='http://{$_SERVER['SERVER_NAME']}{$data['url']}' target='blank_' style='color:#0090ff'>{$data['url']}</a>";
		$params['rows'] = Engine::templater(Engine::$root."/modules/mod_feedback/email_line.html",$url);

		// дополнительная информация
		$info['label'] = "Информация";
		$info['value'] = $_POST['info'];
		$params['rows'] .= Engine::templater(Engine::$root."/modules/mod_feedback/email_line.html",$info);

		$params['rows'] .= $fields;
		$mail_msg = Engine::templater(Engine::$root."/modules/mod_feedback/email_user.html",$params);


		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers  .= "Content-type: text/html; charset=utf-8 \r\n";
		$headers .= "From: {$_SERVER['SERVER_NAME']}\r\n";
        // добавим к списку емейлов общий емейл отправки
        $mainEmail = Engine::constant("email");

		if(trim($mainEmail)!="") $mod['emails'] = $mainEmail.",".$mod['emails'];
		// проверяем на спам
		if($data['more']==""&&$data['url_before']==""){
			mail($mod['emails'], $mod['mail_title'], $mail_msg, $headers);
		}
		// отправляем письмо отправителю, тут стоит допилить через админку и поля в ней
		/*if(@$data['value']['email']&&$data['more']==""&&$data['url_before']==""){
			mail($data['value']['email'], "Вы оставили сообщение на сайте {$_SERVER['SERVER_NAME']}", $mail_msg."<br/>Спасибо, в ближайшее время наш менеджер свяжется с Вами!", $headers);
		}*/
		echo $mod['send_ok'];
		// отправим СМС оповещение
		/*
		$ch = curl_init("http://sms.ru/sms/send");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(

			"api_id"		=>	"",
			"to"			=>	"79099670969, 9067013971",
			"text"		=>	"".$sms_fields

		));

		$body = curl_exec($ch);
		curl_close($ch);
		*/
	}

	public function getForm(){
		
	}
	public function main($param){

		//скрипты фронтальной части
		$this->setFile("jquery.maskedinput.min.js"); // подключим js файл модуля
		$this->setFile("feedback.js"); // подключим js файл модуля
		$this->setFile("feedback.css"); // подключим css файл модуля

		// читаем конфигурацию и выводим нужные поля
		$inputs = trim($param['options']);
		$inputs = json_decode($inputs);
		if(!is_array($inputs)) {return "Ошибка в параметрах модуля.";}

		$fields = array();
		foreach($inputs as $input)
		{
			$type = $input[1];
      		$input['class_'] = $param['input_class'];
			$fields[$input[0]] = Engine::templater(Engine::$root."/modules/mod_feedback/input_{$type}.html",$input);

		}
		//$fields['button'] = "<span class='feedback_button {$param['button_class']}''>{$param['button_name']}</span>";
		$param['']['fields'] = implode('',$fields);
		$out = Engine::templater(Engine::$root."/modules/mod_feedback/form_template.html",$param);

		if(isset($param['template_list']))if(trim($param['template_list'])!=""){
			$data = $param;
			$data['_form'] = $out;
			$data['_field'] = $fields;
			$tmp = Engine::$root."/".Config::$front_template."/views/".$param['template_list'].".html";

            if(file_exists($tmp)){
                $out = Engine::templater($tmp, $data);
            } elseif(file_exists(Engine::$root."/".Config::$front_template."/blocks/".$param['template_list']."/view.html")) {
                $out = Engine::templater(Engine::$root."/".Config::$front_template."/blocks/".$param['template_list']."/view.html", $data);
            } else {
                Engine::errorMod($param,"шаблон вывода не найден");

            }

		}

		return $out;
	}
}


?>
