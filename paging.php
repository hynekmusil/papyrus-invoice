<?php //encoding=utf-8
	require_once 'system/server/componentComposer.php';

	$appDir = './';
	$viewFileName = $appDir.'data/view.xml';
	$viewDoc = new DOMDocument;
	$viewDoc->load($viewFileName);
	$pageList = getPageNodeList();
	$currSumPage = getSumPage($pageList);
	$sumRecord = getSumRecord();
	$typeRgList = array();
	$typeRgList[1] = getPgRange(1);
	$typeRgList[2] = getPgRange(2);
	$typeRgList[3] = getPgRange(3);
	$typeRgList[4] = getPgRange(4);
	$result = 0;
	
	$customerFile = 'data/subject_customer.xml';
	$doc = new DOMDocument();
	$doc->load($customerFile);
	$xpath = new DOMXPath($doc);
	$ic = $xpath->query("//*[@ic]")->item(0)->getAttribute('ic');
	if($ic == '26131820'){
		$templateList = array('ComodityList' => '_letov');
	}
	file_put_contents('data/subject_acceptor.xml',file_get_contents('data/subject_'.$ic.'.xml'));
	
	function addPage(){
		global $viewDoc, $currSumPage, $pageList;
		if($currSumPage == 1){
			$pageList->item(0)->setAttributeNS('http://formax.cz/impresso/system','id','n2');
			$newPage = $viewDoc->createElement('component');
			$newPage = $pageList->item(0)->parentNode->appendChild($newPage);
			$newPage->setAttribute('type','Page');
			$newPage->setAttributeNS('http://formax.cz/impresso/system','id','n3');
		}
		if($currSumPage > 1){
			$newPage = $viewDoc->createElement('component');
			$newPage = $pageList->item(0)->parentNode->insertBefore($newPage,$pageList->item($currSumPage - 1));
			$newPage->setAttribute('type','Page');
			$newPage->setAttributeNS('http://formax.cz/impresso/system','id','n4');
		}
	}
	
	function getDiff4add($aSum){
		if($aSumPage == 1) return  getPgRUL(2) + getPgRUL(3) - getPgRUL(1);
		elseif($aSumPage > 1) return getPgRUL(4);
	}
	
	function removePage(){
		global $viewDoc, $currSumPage, $pageList;
		if($currSumPage == 2){
			$pageList->item(0)->setAttributeNS('http://formax.cz/impresso/system','id','n1');
			$pageList->item(0)->parentNode->removeChild($pageList->item(1));
		}
		if($currSumPage > 2){
			$pageList->item(0)->parentNode->removeChild($pageList->item(1));
		}
	}
	
	function getDiff4remove($aSumPage){
		if($aSumPage == 1) return getPgRUL(1);
		if($aSumPage == 2) return  getPgRUL(2) + getPgRUL(3) - getPgRUL(1);
		elseif($aSumPage > 2) return getPgRUL(4);
	}
	
	function getSumRecord(){
		global $appDir;
		$comodityListFileName = $appDir.'data/comodityList.xml';
		$comodityListDoc = new DOMDocument;
		$comodityListDoc->load($comodityListFileName);
		$xpath = new DOMXPath($comodityListDoc);
		return $xpath->query("//*[local-name() = 'comodity']")->length;
	}
	
	function getSumDisplayedRecord($aSumPage = 0){
		global $currSumPage;
		if($aSumPage > 0) $sumPage = $aSumPage;
		else $sumPage = $currSumPage;
		if($sumPage ==1) return getPgRUL(1);
		elseif($sumPage > 1) return getPgRUL(2) + ($sumPage - 2) * getPgRUL(4) + getPgRUL(3);
	}
	
	function checkPageCount($aSumPage){
		global $sumRecord, $result;
		$sumDisplayed = getSumDisplayedRecord($aSumPage);
		if($sumRecord <= $sumDisplayed){
			if($sumRecord <= ($sumDisplayed - getDiff4remove($aSumPage))){
				checkPageCount($aSumPage - 1);
			}
			else{
				$result = $aSumPage;
			}
		}
		else checkPageCount($aSumPage + 1);
		return $result;
	}
	
	function refreshPageCount(){
		global $currSumPage;
		$sumPage = checkPageCount($currSumPage);
		if($currSumPage < $sumPage){
			$diff = $sumPage - $currSumPage;
			for($i = 0; $i < $diff; $i++){
				addPage();
				$currSumPage++;
			}
			if($diff == 1){
				if($currSumPage == 2) return getPgRUL(2) - getPgRUL(1);
				elseif($currSumPage > 2) return getPgRUL(4) - getPgRUL(3);
			}
			return true;
		}
		elseif($currSumPage > $sumPage){
			$diff = $currSumPage - $sumPage;
			for($i = 0; $i < $diff; $i++){
				removePage();
				$currSumPage--;
			}
			return true;
		}
		return false;
	}
	
	function getPageNodeList(){
		global $appDir, $viewDoc;
		$xpath = new DOMXPath($viewDoc);
		return $xpath->query("//*[local-name() = 'component'][@type = 'Page']");
	}
	
	function getSumPage(&$aNodeList){
		$result = 0;
		foreach ($aNodeList as $node) $result++;
		return $result;
	}
	
	function getPgRange($aType){
		global $appDir;
		$pageDoc = new DOMDocument;
		$pageFileName = $appDir.'data/page';
		$pageDoc->load($pageFileName.'_n'.$aType.'.xml');
		$xpath = new DOMXPath($pageDoc);
		$nl = $xpath->query("//*[local-name() = 'component'][@type = 'ComodityList']");
		return $nl->item(0)->getAttributeNS('http://formax.cz/impresso/system','pgRange');
	}
	
	function getPgRUL($aType){
		global $typeRgList;
		$t = explode('..',$typeRgList[$aType]);
		return $t[1] - $t[0] + 1;
	}
	
	function getPgRangeList(){
		global $appDir, $typeRgList;
		$pageList = getPageNodeList();
		$sumPage = getSumPage($pageList);
		$result = array();
		if($sumPage == 1){
			$result[] = $typeRgList[1] ;
		}
		elseif($sumPage == 2){
			$result[] = $typeRgList[2] ;
			$result[] = $typeRgList[3] ;
		}
		elseif($sumPage > 2){
			$result[] = $typeRgList[2] ;
			$result[] = $typeRgList[4] ;
			$t4PgRange = explode('..',$result[1]);
			$lowerLimit = $t4PgRange[1] + 1;
			$t4Records = $t4PgRange[1] - $t4PgRange[0];
			for($i = 2; $i < $sumPage; $i++){
				if($i == ($sumPage - 1)) $upperLimit = '*';
				else $upperLimit = $lowerLimit + $t4Records;
				$result[] = $lowerLimit.'..'.$upperLimit;
				$lowerLimit = $upperLimit + 1;
			}
		}
		return $result;
	}
	
	function show(){
		global $appDir, $templateList;
		refreshPageCount();
		$component = new Component($appDir);
		$component->setCType('View');
		$component->getDataDoc();
		$component->setAccess(true);
		$componentComposer = new ComponentComposer($appDir, $component);
		$accessList = new AccessList();
		$accessList->addAccess('ComodityList');
		$accessList->addAccess('RelatedDocs');
		$accessList->addAccess('PaymentProcess');
		$accessList->addAccess('Subject','customer');
		$componentComposer->setPgRangeList(getPgRangeList());
		$componentComposer->setTemplateList($templateList);
		$componentComposer->compose($accessList);
		$componentComposer->show();
	}
	
	
	?>