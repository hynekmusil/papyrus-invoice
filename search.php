<?php 
if(isset($_GET['debug'])):
?>
<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			</head>
			<body>
<?php
endif;
$text = addslashes(trim($_REQUEST['text']));
if($text != '')
{
$table = array(' ' => '[[:blank:]]'
	,'a' => '[aá]' ,'á' => '[aá]'
	,'c' => '[cč]','č' => '[cč]'
	,'d' => '[dď]','ď' => '[dď]'
	,'e' => '[eěé]','é' => '[eěé]' , 'ě' => '[eěé]'
	,'i' => '[ií]','í' => '[ií]'
	,'n' => '[nň]','ň' => '[nň]'
	,'o' => '[oó]','ó' => '[oó]'
	,'r' => '[rř]' ,'ř' => '[rř]'
	,'s' => '[sš]','š' => '[sš]'
	,'t' => '[tť]','ť' => '[tť]'
	,'u' => '[uúů]','ů' => '[uúů]','ú' => '[uúů]'
	,'y' => '[yý]','ý' => '[yý]'
	,'z' => '[zž]' ,'ž' => '[zž]');
$text = strtr($text, $table);
$cmd = "LANG=cs_CZ.UTF-8 grep -il \"<name>[^<]*".$text."[^<]*</name>\" /usr/local/apache2/htdocs/invoice/archive/*/comodityList.xml";
exec($cmd, $lines);
$xml = "<ul id=\"searchResult\" xmlns=\"http://www.w3.org/1999/xhtml\">";
foreach($lines as $line)
{
	if($line !=="")
	{
		$id = substr($line,42);
		$id = substr($id, 0, strpos($id,'/'));
		$xml .= "<li><a onclick=\"openInv('$id'); return false;\" href=\"\">$id</a></li>";
	}
}
	$xml .= "</ul>";
	if(isset($_GET['debug'])){
		echo $cmd;
		echo "<pre>";
		var_dump($lines);
		echo "</pre>".$xml;
	}
	$doc = new DOMDocument();
	$doc->loadXML($xml);
	$xslt = new XSLTProcessor();
	$xslDoc = new DOMDocument();
	$xslDoc->load("listSort.xsl");
	$xslt->importStylesheet($xslDoc); 
	echo $xslt->transformToXML($doc);
}
if(isset($_GET['debug'])):
?>
</body>
</html>
<?php
endif;