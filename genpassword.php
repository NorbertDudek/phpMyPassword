<?php

function generatePasswordFromSyllables($length, $digitsCount = 1, $specialCount = 1) {

    $syllables = file('syllables.dat');
    $special = ['!', '@', '#', '$', '%', '&', '*'];

    $password =  ucwords(trim($syllables[array_rand($syllables)]));
    $password .= trim($syllables[array_rand($syllables)]);

	
    for ($i = 0; $i < $digitsCount; $i++) {
		$password .= rand(0, 9);
		}
    for ($i = 0; $i < $specialCount; $i++) {
		$password .= $special[array_rand($special)];
		}

    $password .= ucwords(trim($syllables[array_rand($syllables)]));

    while (strlen($password) < $length) {
        $password .= trim($syllables[array_rand($syllables)]);
    }
    return substr($password, 0, $length); // przycinamy do żądanej długości
}

$length = isset($_GET['length']) ? (int)$_GET['length'] : 12;
$digits = isset($_GET['digits']) ? (int)$_GET['digits'] : 1;
$special = isset($_GET['special']) ? (int)$_GET['special'] : 1;

if ($length < 1) $length = 1;
if ($length > 256) $length = 256;
if ($digits < 0) $digits = 0;
if ($special < 0) $special = 0;

echo generatePasswordFromSyllables($length, $digits, $special);

?>
