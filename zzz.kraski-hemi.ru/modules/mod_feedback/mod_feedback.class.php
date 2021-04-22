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
		if(isset($_POST['url'])){
			 $this->send();
		}
		if(isset($_POST['getform'])){
			$this->getForm();
		}
	}

	function send(){
		// библиотека форматирования полей
		include ($this->mod_path."/feedback_format.class.php");
		$error = 0;
		$error_msg = array();
		$city = func::getCityParams();
		$data = $_POST;
		$module = new Module(); // получаем параметры модуля
		$mod = $module->get(array("name"=>$data['mod']))->assoc();
		$inputs = trim($mod['options']);
		$inputs = json_decode($inputs);
		// формируем текст сообщения
		$nomer = 0;
		$text = "<table cellspacing='0' cellpadding='10' border='0'  style='border-top:1px solid #CCC;'>";
		$text_label = array();
		foreach($data['value'] as $key=>$input)
		{
			$row['label'] = $data['label'][$key];
			if($input) $row['value'] = $input; else $row['value'] = "&nbsp;";
			// проверка поля на обработчик
			$func = explode(":", $inputs[$nomer][1]);
			if(isset($func[1])) $func = $func[1]; else $func = "";
			// если есть обрабтчик отдаём значение на обработку
			if($func) $row['value'] = feedback_format::$func($input,$data['value']);

			$text.=Engine::templater(Engine::$root."/modules/mod_feedback/email_line.html",$row);
			// поле для пользователя
			$text_label[$key] = $row['value'];
			// валидации
			if($data['type'][$key]=="input_text"){
				if(strpos($input,"www.")!==false) $error = 1;
				if(strpos($input,"http:")!==false) $error = 1;
				if(strpos($input,"https:")!==false) $error = 1;
				$error_msg[] = "Текст содержит ссылки на сайты!";
			}
			$nomer++;
		}

		// дополнительная информация
		$city = func::getCityParams();
		$row['label'] = "Город";
		$row['value'] = $city['i'];
		$text .= Engine::templater(Engine::$root."/modules/mod_feedback/email_line.html",$row);

		// дополнительная информация
		$row['label'] = "Сайт";
		$row['value'] = $_SERVER['SERVER_NAME'];
		$text .= Engine::templater(Engine::$root."/modules/mod_feedback/email_line.html",$row);

		// страница отправки
		$row['label'] = "Страница отправки";
		$row['value'] = "<a href='http://{$_SERVER['SERVER_NAME']}{$data['url']}' target='blank_' style='color:#0090ff'>{$data['url']}</a>";
		$text .= Engine::templater(Engine::$root."/modules/mod_feedback/email_line.html",$row);

		// дополнительная информация
		$row['label'] = "Информация";
		$row['value'] = $_POST['info'];
		$text .= Engine::templater(Engine::$root."/modules/mod_feedback/email_line.html",$row);

		$text.="</table>";

		$smtp = Engine::constant("smtpmail");
		$smtp = explode("\n",$smtp);

		require dirname(__FILE__).'/PHPMailer/src/Exception.php';
		require dirname(__FILE__).'/PHPMailer/src/PHPMailer.php';
		require dirname(__FILE__).'/PHPMailer/src/SMTP.php';

		$mail = new PHPMailer\PHPMailer\PHPMailer();
		$mail->CharSet = 'UTF-8';

		// Настройки SMTP
		$mail->isSMTP();
		$mail->SMTPAuth = true;
		$mail->SMTPDebug = 0;

		$mail->Host = "ssl://smtp.yandex.ru";
		$mail->Port = 465;
		$mail->Username = trim($smtp[0]);
		$mail->Password = trim($smtp[1]);

		$mail->setFrom(trim($smtp[0]), '');
		// Кому
		$emailList = array();
		$emailList = explode(",", $mod['emails']);
		$emailList[] = $city['email'];
		foreach ($emailList as $value) {
			$mail->addAddress(trim($value), '');
		}

		// прикрепляем файлы
		if(isset($_FILES['value'])){
			foreach ($_FILES['value']['name'] as $key => $value) {
				// прикрепляем файл
					$mail->AddAttachment($_FILES['value']['tmp_name'][$key], $_FILES['value']['name'][$key]);
			}
		}

		// Тема письма
		$mail->Subject = $mod['mail_title'];
		// Тело письма
		$body = $text;
		$mail->msgHTML($body);

		if($error) {
			echo implode("<br>", $error_msg);
		} elseif(!$mail->Send()){
		  echo "Ошибка отправки письма: " . $mail->ErrorInfo;
		} else {
		  echo $mod['send_ok'];
		}
		//echo $body;

	  // сообщение для отправителя
	  if($mod['send_user']){
				$mod['mail_text_user'] = str_replace("\n","<br>",$mod['mail_text_user']);
				$text_user = func::templateCityVars(Engine::templaterArrayKeys($mod['mail_text_user'],$text_label));

				$mail = new PHPMailer\PHPMailer\PHPMailer();
				$mail->CharSet = 'UTF-8';

				// Настройки SMTP
				$mail->isSMTP();
				$mail->SMTPAuth = true;
				$mail->SMTPDebug = 0;

				$mail->Host = "ssl://smtp.yandex.ru";
				$mail->Port = 465;
				$mail->Username = trim($smtp[0]);
				$mail->Password = trim($smtp[1]);

				$mail->setFrom(trim($smtp[0]), '');
				// Кому
				$mail->addAddress(trim($text_label['email']), '');

				// прикрепляем файлы если есть
				if($mod['mail_file_user']){
					$file = "";
					$parents = func::getAllparents($_POST['page_id'], "main",true);
					foreach($parents as $parent){
						if($parent['file_load']!="") $file = $parent['file_load'];
					}
					$file =Engine::$root.$file;
					$name = basename($file);
					$mail->AddAttachment($file, $name);
				}

				// Тема письма
				$mail->Subject = $mod['mail_title_user'];
				// Тело письма
				$mail->msgHTML($text_user);

				if(!$error) {
					$mail->Send();
				}

	  }

	}

	// получение формы
	public function getForm(){

		$module = new Module(); // получаем параметры модуля
		$param = $module->get(array("name"=>$_POST['getform']))->assoc();

		// читаем конфигурацию и выводим нужные поля
		$inputs = trim($param['options']);
		$inputs = json_decode($inputs);
		if(!is_array($inputs)) {return "Ошибка в параметрах модуля.";}

		$fields = array();
		foreach($inputs as $input)
		{
			$type = explode(":",$input[1]);
			$type = $type[0];
      		$input['class_'] = $param['input_class'];
			$fields[$input[0]] = Engine::templater(Engine::$root."/modules/mod_feedback/input_{$type}.html",$input);

		}
		//$fields['button'] = "<span class='feedback_button {$param['button_class']}''>{$param['button_name']}</span>";
		
		$param['_page_id'] = $_POST['pageid'];
		if(!$param['template_main']){
			$param['']['fields'] = implode('',$fields);
			$out = Engine::templater(Engine::$root."/modules/mod_feedback/form_template.html",$param);
		} else {
			$param['']['fields'] = $fields;
			$out = Engine::templater(Engine::$root."/modules/mod_feedback/{$param['template_main']}.html",$param);
		}
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
		echo $out;
	}
	public function main($param){
		//скрипты фронтальной части
		// $this->setFile("magnific/jquery.magnific-popup.min.js"); // подключим css файл модуля
		// $this->setFile("magnific/magnific-popup.css"); // подключим css файл модуля
		$this->setFile("jquery.maskedinput.min.js"); // подключим js файл модуля
		$this->setFile("feedback.js"); // подключим js файл модуля
		$this->setFile("feedback.css"); // подключим css файл модуля
		$this->setFile("datetimepicker/jquery.datetimepicker.js"); // подключим css файл модуля
		$this->setFile("datetimepicker/jquery.datetimepicker.css"); // подключим css файл модуля

		return "<div class='formid disable' data-id='{$param['name']}' data-page-id='".Engine::$page['id']."'></div>";
	}
}


?>
