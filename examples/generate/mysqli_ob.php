<?php
require_once('SimPayDB.php');

$cfg = array(
	'mysql' => array(
		'host' => 'localhost',
		'username' => '3306',
		'password' => 'password',
		'database' => 'database'
	),
	'simpay' => array(
		/*
			Tryb debugowania
			Typ pola bolean true/false
			Opis Ustawienie pola na TRUE, włączna tryb debugowania, który wyświetla błędy np. w konfiguracji.
		*/
		'debugMode' => FALSE,
		/*
			ID usługi
			Typ pola int, np. 60
			Opis ID Usługi DirectCarrierBilling z panelu simpay.pl, 
		*/
		'serviceId' => 1,
		/*
			Klucz API usługi
			Typ pola string
		*/
		'apiKey' => 'lNEEDQPfPKHleZdd',
		/*
			Control
			Typ pola string
		*/
		'control' => 112,
		/*
			Adres URL do powrotu po prawidłowej transakcji
			Typ pola string
			Opis Użytkownik jest przekierowywany na ten adres po prawidłowo zakończonej transakcji.
		*/
		'completeUrl' => 'https://api.systemy.net/simpay/complete.php',
		/*
			Adres URL do powrotu po nieudanej transakcji
			Typ pola string
			Opis Użytkownik jest przekierowywany na ten adres po nieprawidłowo zakończonej transakcji.
		*/
		'failureUrl' => 'https://api.systemy.net/simpay/failure.php',
		/*
			Kwota transakcji
			Typ pola float
		*/
		'amount' => 10.00
	)
);


$mysqli = new mysqli($cfg['mysql']['host'], $cfg['mysql']['username'], $cfg['mysql']['password'], $cfg['mysql']['database']);
if ($mysqli->connect_error) {
    exit('Connection error: ' . $mysqli->connect_error);
} 

$stmt = $mysqli->prepare("INSERT INTO `dcb`(`control`, `price`, `status`) VALUES (?, ?, ?);");
$stmt->bind_param($cfg['simpay']['control'], $cfg['simpay']['price'], 'new');
$stmt->execute();


$simpayTransaction = new SimPayDBTransaction();
$simpayTransaction->setDebugMode($cfg['simpay']['debugMode']);
$simpayTransaction->setServiceID($cfg['simpay']['serviceId']);
$simpayTransaction->setApiKey($cfg['simpay']['apiKey']);
$simpayTransaction->setControl($cfg['simpay']['control']);
$simpayTransaction->setCompleteLink($cfg['simpay']['completeUrl']);
$simpayTransaction->setFailureLink($cfg['simpay']['failureUrl']);
//$simpayTransaction->setAmount(10);
//$simpayTransaction->setAmountGross(10);
$simpayTransaction->setAmountRequired($cfg['simpay']['amount']);
$simpayTransaction->generateTransaction();

if ($simpayTransaction->getResults()->status == "success") {
	/*
		Tutaj należy przekierować użytkownika używając np. header('Location: ' . $simpayTransaction->getResults()->link);
	*/
	echo $simpayTransaction->getResults()->link;
} else {
	echo 'Generowanie transakcji nie powiodło się!';
}