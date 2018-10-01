<?php //encoding=UTF-8
class Study{
	public static $i = 0;
	private $str = '<?xml version="1.0" encoding="UTF-8"?>
<table xmlns="http://www.w3.org/1999/xhtml" xmlns:o="http://formax.cz/impresso/invoice" onclick="doFocus(\'paymentProcess\',\'0\', this);" class="PaymentProcess ~ 0 5" cellspacing="0">
  <tr   xmlns="http://www.w3.org/1999/xhtml" xmlns:o="http://formax.cz/impresso/invoice">
    <th>Den splatnosti</th>
    <td>26.3.2008</td>
  </tr>
  <tr   xmlns="http://www.w3.org/1999/xhtml">
    <th>Den odeslání</th>
    <td>13.3.2008</td>
  </tr>
  <tr   xmlns="http://www.w3.org/1999/xhtml" xmlns:o="http://formax.cz/impresso/invoice">
    <th>Forma úhrady</th>
    <td>pøevod</td>
  </tr>
  <tr   xmlns="http://www.w3.org/1999/xhtml">
    <th>Den splnìní</th>
    <td>6.3.2008</td>
  </tr>
</table>';

	public static function clearNamespace($aMatches){
		Study::$i++;
		echo Study::$i." {$aMatches[1]} \n";
		if (Study::$i == 1) {
			if(strpos($aMatches[1],'xmlns=') !== false) return $aMatches[1];
			else return '';
		} else return '';
	}
	
	function show(){
		echo preg_replace_callback("|(xmlns(:[a-z]+)*=\"[^\"]+\")|", "Study::clearNamespace", $this->str);
	}
}

 $study = new Study();
 $study->show();

?>

