<?
// функции пост обработки значений
class Post {
	// обработка контента
	// первый параметр строка из таблицы, вторая поле которое необходимо обработать
	function post_content($_row,$_area){
		return $_row["url_name"]."<br/>".$_row[$_area]."/ ЗНАЧЕНИЕ С ПОСТ ОБРАБОТКОЙ";
	}




	function post_clearBorderLine($_row, $_area){
		$s = array("<hr />","<p>&nbsp;</p>");
		$_row['content'] = str_replace($s,"",$_row['content']);
		return $_row['content'];
	}
	// обработка вывода статьи
	function post_clearContent(&$_row, $_area){

		$content = self::post_clearBorderLine($_row, $_area);
		$_row['new-content'] = $content;
		return ""; // очищаем поле контента, дабы не выодить контент стандартным методом системы
	}
	// обработка вывода статьи
	function post_clearContentProduct(&$_row, $_area){

		$_row['new-content'] = $_row['content'];
		return ""; // очищаем поле контента, дабы не выодить контент стандартным методом системы
	}

	// обработка вывода статьи
	function post_clearButtonCode($_row, $_area){

		$btn = $_row[$_area];
		if($btn){
			$s = strpos($btn, "href=")+6;
			$e = strpos($btn,'"',$s);
			$btn = substr($btn,$s,$e-$s);
			$btn = "<a href='{$btn}' class='button buy' target='blank_'>Купить</a>";
		}
		return $btn; // очищаем поле контента, дабы не выодить контент стандартным методом системы
	}

	function post_popcounter($_row,$_area){
		//return $_row['popular'];
	}


}



?>
