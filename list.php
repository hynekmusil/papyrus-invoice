<?php //encoding=utf-8
	header('Content-Type: text/xml; charset=utf-8');
	
	if(isset($_REQUEST['save'])) save();
	elseif(isset($_REQUEST['list'])) listDoc();
	elseif(isset($_REQUEST['open']) and isset($_REQUEST['id'])) open($_REQUEST['id']);
	elseif(isset($_REQUEST['new'])) newInv();
	
	function save(){
		$id = getId();
		if(!file_exists('archive/'.$id)){
			mkdir('archive/'.$id);
		}
		copy('data/comodityList.xml', 'archive/'.$id.'/comodityList.xml');
		copy('data/paymentProcess.xml', 'archive/'.$id.'/paymentProcess.xml');
		copy('data/relatedDocs.xml', 'archive/'.$id.'/relatedDocs.xml');
		copy('data/subject_acceptor.xml', 'archive/'.$id.'/subject_acceptor.xml');
		copy('data/subject_customer.xml', 'archive/'.$id.'/subject_customer.xml');
		copy('data/view.xml', 'archive/'.$id.'/view.xml');
	}
	function newInv(){
		save();
		copy('data/comodityList_new.xml', 'data/comodityList.xml');
		copy('data/relatedDocs_new.xml', 'data/relatedDocs.xml');
		copy('data/view_new.xml', 'data/view.xml');
	}
	function listDoc(){
		$id = getId();
        $xml = "";
		$xml  .= "<div class=\"listInv\" xmlns=\"http://www.w3.org/1999/xhtml\">";
		$xml .= "<ul>";
		$xml .= "<li class=\"command\"><input type=\"text\"  name=\"searchText\" id=\"searchText\" style=\"margin-right: 2em;\"/><a href=\"#\" onclick=\"doSearch(); return false;\">vyhledej</a></li>";
		$xml .= "</ul><div id=\"searchResult\"></div><hr style=\"clear: both\"/>";
		$xml .= "<ul>";
		$xml .= "<li class=\"command\"><a href=\"\" onclick=\"newInv(); return false;\">nový</a></li>";
		$xml .= "<li class=\"command\"><a href=\"\" onclick=\"saveInv(); return false;\">uložit</a></li>";
		$xml .= "<li class=\"command\"><a href=\"javascript:window.print()\">vytisknout</a></li>";
		$xml .= "</ul><ul id=\"invoiceList\">";
		$isLast = false;
		if ($handle = opendir('archive')) {
			$i = 0;
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					$xml .= "<li>"; 
					if($file == $id) $xml .= "<span>$file</span>";
					else $xml .= "<a href=\"\" onclick=\"openInv('$file'); return false;\">$file</a>";
					$xml .= "</li>";
				}
			}
			closedir($handle);
		}
		$xml .= "</ul></div>";
		$doc = new DOMDocument();
		$doc->loadXML($xml);
		$xslt = new XSLTProcessor();
		$xslDoc = new DOMDocument();
		$xslDoc->load("listSort.xsl");
		$xslt->importStylesheet($xslDoc); 
		echo $xslt->transformToXML($doc);
	}
	function open($aId){
		copy( 'archive/'.$aId.'/comodityList.xml','data/comodityList.xml');
		copy('archive/'.$aId.'/paymentProcess.xml','data/paymentProcess.xml');
		copy('archive/'.$aId.'/relatedDocs.xml','data/relatedDocs.xml');
		copy('archive/'.$aId.'/subject_acceptor.xml','data/subject_acceptor.xml');
		copy('archive/'.$aId.'/subject_customer.xml','data/subject_customer.xml');
		copy('archive/'.$aId.'/view.xml','data/view.xml');
	}
	
	function getId(){
		$doc = new DOMDocument();
		$doc->load('data/relatedDocs.xml');
		$xpath = new DOMXPath($doc);
		$xpath->registerNamespace('r','http://formax.cz/impresso/invoice');
		return $xpath->query("//r:evidence")->item(0)->getAttribute('id');
	}

?>