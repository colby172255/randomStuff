<?php

$strSendID = 'send_id';
$strRecID = 'recipient_id';
$strEmail = 'email';
$strTime = 'time';
$strType = 'type';
$strUrl = 'url_clicked';
$strErr = 'error_reason';

$columnHeaders = array($strSendID, $strRecID, $strEmail, $strTime, $strType, $strUrl, $strErr);

// **** Index positions ********* //
$deliveredIndexes = array($strSendID => 26, $strRecID => 26, $strEmail =>4, $strTime =>11, $strType => 'delivered',
	$strUrl => null, $strErr => null);

$openIndexes = array($strSendID => 8, $strRecID => 8, $strEmail =>2, $strTime =>7, $strType => 'open',
	$strUrl => null, $strErr => null);

$clickIndexes = array($strSendID => 10, $strRecID => 10, $strEmail =>2, $strTime =>8, $strType => 'click',
	$strUrl => 11, $strErr => null);

$bounceIndexes = array($strSendID => 23, $strRecID => 23, $strEmail =>2, $strTime =>10, $strType => 'bounce',
	$strUrl => null, $strErr => 13);

$deferredIndexes = array($strSendID => 30, $strRecID => 30, $strEmail =>5, $strTime =>13, $strType => 'deferred',
	$strUrl => null, $strErr => null);

$processIndexes = array($strSendID => 27, $strRecID => 27, $strEmail =>3, $strTime =>15, $strType => 'processed',
	$strUrl => null, $strErr => null);

$dropIndexes = array($strSendID => 26, $strRecID => 26, $strEmail =>3, $strTime =>14, $strType => 'dropped',
	$strUrl => null, $strErr => 15);

$unsubscribeIndex = array($strSendID => 12, $strRecID => 12, $strEmail =>1, $strTime =>11, $strType => 'unsubscribe',
	$strUrl => null, $strErr => null);

$spamIndex = array($strSendID => 7, $strRecID => 7, $strEmail =>2, $strTime =>5, $strType => 'spamreport',
	$strUrl => null, $strErr => null);

// ******* CHANGE BASED OFF FILE *************
$startPosition = 0; // set non 0 row number if starting mid file.
$num = 2;
$readFile = fopen("/home/ccollins/Documents/temp/sendgrid/SG$num/unsubscribeevent.csv", "r");
$fileName = "unsub$num.csv";
$writeFile = fopen("/home/ccollins/Documents/temp/sendgrid/combinedResults/$fileName", 'w');
$currentIndex = $unsubscribeIndex;
$addHeaders = false;
// ******************************************

//write headers to writeFile
$count = 1;
if($addHeaders) {
	fputcsv($writeFile, $columnHeaders);
}

//start mid file
if($startPosition > 0){
	while($count != $startPosition) {
		$t = fgetcsv($readFile);
		$count++;
	}
}

//get rid of the readFile headers
$header = fgetcsv($readFile);

$type = $currentIndex[$strType];

while($row = fgetcsv($readFile)){
	try {
		//start with null values
		$valueRow = null;
		$sendID = null;
		$recipientID= null;
		$time = null;
		$url_clicked = null;
		$error_reason = null;

		$sendIDAndRecVal = $row[$currentIndex[$strSendID]];

		if($count == 425){
			$temp = 1;
		}
		//if value isn't long enough
		if (strlen($sendIDAndRecVal) < 15){
			continue;
		}
		$sendID = getValue($sendIDAndRecVal, $strSendID);

		//if value isn't numeric skip
		if(!is_numeric($sendID)){
			continue;
		}
		$recipientID = getValue($sendIDAndRecVal, $strRecID);
		$email = $row[$currentIndex[$strEmail]];
		$time = gmdate("Y-m-d H:i:s.0", $row[$currentIndex[$strTime]]);
		$time = str_replace('"', "", $time);
		$url_clicked = null;
		$error_reason = null;

		if (!is_null($currentIndex[$strUrl])) {
			$url_clicked = substr($row[$currentIndex[$strUrl]], 0, 1024);
		}

		if (!is_null($currentIndex[$strErr])) {
			$error_reason = str_replace('"', " ", substr($row[$currentIndex[$strErr]], 0, 1024));
			$error_reason = str_replace("\n", "", $error_reason);
			$error_reason = str_replace("\r", "", $error_reason);
		}

		$valueRow = array($sendID, $recipientID, $email, $time, $type, $url_clicked, $error_reason);

		fputcsv($writeFile, $valueRow, "|");
	} catch (Exception $e){
		echo ("Exception occurred on line <$count>. Consider re-running");
		exit;
	}
	$count++;
	if($count % 1000 == 0){
		echo $count . "\n";
	}
}
fclose($readFile);
fclose($writeFile);

function getValue($colValue, $key)
{
	if($key == 'send_id' || $key == 'recipient_id'){
		return getSendOrRecipient($key, $colValue);
	}
	return -1;
}

function getSendOrRecipient($key, $value)
{
	//get rid of first and last character {}
	$value = substr($value, 1, -1);
	$valueArray = explode(',', $value);

	if($key == 'recipient_id'){
		$recArray = explode('=', $valueArray[0]);
		return trim($recArray[1]);
	}

	if($key == 'send_id'){
		$sendArray = explode('=', $valueArray[1]);
		return trim($sendArray[1]);
	}
	return -1;
}
