<?php

$x = false;
$jackpot =array(1, 2, 3, 4, 5, 6);
$count = 0;

while($x == false){
	$quickPick = getQuickPick();
	$count++;
	$diff = array_diff($jackpot, $quickPick);
	$diffCount = count($diff);
	$display = number_format($count);
	if($diffCount == 0){
		print ("WINNER WINNER chicken dinner, number of tickets: $display \n");
		$x = true;
	} elseif($diffCount == 1) {
		print("HOLY HELL. 1 off on TRY: $display \n");
	} else {
		if($count % 100000 == 0) {
			print("incorrect trying again: Count: number: $display \n");
		}
	}
}
$result = 1;

function getQuickPick()
{
	$list = array (
		$num1 = rand(1, 69),
		$num2 = rand(1, 69),
		$num3 = rand(1, 69),
		$num4 = rand(1, 69),
		$num5 = rand(1, 69),
	);
	checkUnique($list);
	$powerBall = rand(1, 26);
	$list[] = $powerBall;
	return $list;
}

function checkUnique($list)
{
	$unique = array_unique($list);
	if(count($unique) != 5){
		$unique[] = rand(1, 69);
		checkUnique($unique);
	} else {
		return $unique;
	}
}
