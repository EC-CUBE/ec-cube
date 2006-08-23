<?

$arrRet = array("1", "2", "3", "4", "5", "0");

if(array_search("2", $arrRet)) {
	echo '2:find';
}

if(array_search("1", $arrRet)) {
	echo '1:find';
}

if(array_search("3", $arrRet)) {
	echo '3:find';
}

if(array_search("4", $arrRet)) {
	echo '4:find';
}

if(array_search("0", $arrRet)) {
	echo '0:find';
}

print(array_search("1", $arrRet));
print(array_search("2", $arrRet));

?>