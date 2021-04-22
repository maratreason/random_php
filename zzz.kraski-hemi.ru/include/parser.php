<?php

$arr = array();
$arrPrice = array();

// $products = $sql->fetchAllTables();

$xml = "";
$xmlPrice = "";
$path = '../-406935381.xml';
$path2 = '../413004476.xml';

if (file_exists($path)) {
    $xml = new SimpleXMLElement($path, null, true);
    $xmlPrice = new SimpleXMLElement($path2, null, true);
}

$query = "";

foreach($xmlPrice->{'ПакетПредложений'}->{'Предложения'}->{'Предложение'} as $productPrice) {
    $arrPrice['id'] = (string) $productPrice->{'Ид'};
    $arrPrice['price_float'] = $productPrice->{'Цены'}->{'Цена'}[0]->{'ЦенаЗаЕдиницу'};

    if ($productPrice->{'Артикул'}[0]) {
        $arrPrice['artikul'] = $productPrice->{'Артикул'}[0];
    } else $arrPrice['artikul'] = 0;

    if ($productPrice->{'Склад'}[0]) {
        $arrPrice['sklad1'] = $productPrice->{'Склад'}[0]->attributes()['ИдСклада'];
        $arrPrice['sklad1_qt'] = $productPrice->{'Склад'}[0]->attributes()['КоличествоНаСкладе'];
    } else { $arrPrice['sklad1'] = 0; $arrPrice['sklad1_qt'] = 0; }

    if ($productPrice->{'Склад'}[1]) {
        $arrPrice['sklad2'] = $productPrice->{'Склад'}[1]->attributes()['ИдСклада'];
        $arrPrice['sklad2_qt'] = $productPrice->{'Склад'}[1]->attributes()['КоличествоНаСкладе'];
    } else { $arrPrice['sklad2'] = 0; $arrPrice['sklad2_qt'] = 0; }

    if ($productPrice->{'Склад'}[2]) {
        $arrPrice['sklad3_qt'] = $productPrice->{'Склад'}[2]->attributes()['ИдСклада'];
        $arrPrice['sklad3_qt'] = $productPrice->{'Склад'}[2]->attributes()['КоличествоНаСкладе'];
    } else { $arrPrice['sklad3'] = 0; $arrPrice['sklad3_qt'] = 0; }

    $query .= "UPDATE pages SET price_float =" . $arrPrice['price_float'] . ", sklad1 =" . $arrPrice['sklad1_qt'] . ", sklad2 =" . $arrPrice['sklad2_qt'] . ", sklad3 =" . $arrPrice['sklad3_qt'] . " WHERE artikul =" . $arrPrice['artikul'] . ";";
}

// echo $query;

const MYSQL_SERVER = 'localhost';
const MYSQL_DB = 'kraski_hemi';
const MYSQL_USER = 'root';
const MYSQL_PASSWORD = 'root';

$db = new PDO('mysql:host=' . MYSQL_SERVER . ';dbname=' . MYSQL_DB, MYSQL_USER, MYSQL_PASSWORD);
$db->exec('SET NAMES UTF8');
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$smtp = $db->prepare($query);
$smtp->execute();

?>