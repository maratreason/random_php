<?
class Config {
    // ключ google для расчета расстояния маршрута
    // ссылка: https://developers.google.com/maps/documentation/directions/intro?hl=ru
    public static $googleKey = "AIzaSyAF534i7l1BTNCqRwR3sJiVM-F7jaJAyCM";
    // популярность маршрута после чего он начинает индексироваться
    public static $indexCount = 4;
    // минимальный процент стоимости маршрута default 70% (0.7)
    public static $minPercent = 0.7;


	public static $debug = true;
	// конфигурация базы данных
	public static $base_type = "mysql";
	public static $base_host = "127.0.0.1";
	public static $base_name = "jed_alyans";
	public static $base_user = "root";
	public static $base_pass = "";

	// конфигурация шаблонов
	public static $back_template = "/templates/admin";
	public static $front_template = "/templates/front";
	// сайты
	public static $domains = array("*"=>"*",
						 "site01"=>"alyans-tf.ru"
						);
}

?>
