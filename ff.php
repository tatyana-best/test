<?php
/*Ќаходит файлы содержащие текст
Ќа крупных CMS  +  слабый хостинг, искать с корн¤ не получаетс¤, так как работает долго  и не укладываетс¤ в timeout сервера
*/
function find_files($path, $text, $replace=0) {
  $handle = opendir($path);
  while ( false !== ($file = readdir($handle)) ) {
    if ( ($file !== "..") ) {
	  if(is_file($path."/".$file) && ($file !== ".") && ($file!=='ff.php')){
		$arr = pathinfo($path."/".$file);		
		if(in_array($arr['extension'],array('php','js')))//—писок расширений файлов которые будут провер¤тьс¤
		{	
			$fpath = realpath($path."/".$file);
			$fcont=file_get_contents($fpath);			
			if(stripos($fcont, $text)!==false) { // найден искомый  текст				
				echo $path."/".$file . "<br>";
				//замена искомого текста на пробел
				if($replace) file_put_contents($fpath, str_ireplace($text,' ',$fcont));
			}
		}
	  }
      if (!is_file($path."/".$file) && ($file !== ".") )
      find_files($path . "/" . $file, $text, $replace);
    }
  }
  closedir($handle);
}
//замен¤ть найденный текст или нет ? false - не замен¤ть, true - замен¤ть
$replace=false;
//ќткуда искать
$path = $_SERVER["QUERY_STRING"];
//что ищем ?
$text= 'ADDITIONAL_RESULT'; 
//ѕо умолчанию будем  искать от корн¤ сайта
if ( $path{0} != "/" )  $path = $_SERVER["DOCUMENT_ROOT"] . "/" . $path;

find_files($path, $text, $replace);
?>