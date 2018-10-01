<?php //encoding=utf-8
	class Component{
		private $dataFileName;
		private $templateFileName;
		private $templateId = '';
		private $type;
		private $id;
		private $pgRange = '';
		private $appDir;
		private $cLinkList;
		private $cBaseName;
		private $dataDoc;
		private $access = false;
		private $lang = '';
		public static $i = 0;
		
		function Component($aAppDir){
			$this->appDir = $aAppDir;
		}
		public function setCId($aId){
			$this->id= $aId;
		}
		public function setCPgRange($aPgRange){
			$this->pgRange= $aPgRange;
		}
		public function setAccess($aRight){
			$this->access = $aRight;
		}
		public function setCType($aType){
			$this->type= $aType;
			$this->cBaseName = strtolower($this->type[0]).substr($this->type,1);
		}
		public function getCType(){
			return $this->type;
		}
		public function setLang($aLang){
			$this->lang = $aLang;
		}
		public function getDataDoc(){
			$dataFileName = $this->appDir.'data/'.$this->getId().'.xml';
			$this->dataDoc = new DOMDocument;
			$this->dataDoc->load($dataFileName);
			return $this->dataDoc;
		}
		public function getNamespaces(){
		}
		public function show(){
			if($this->type != ''){
				$templateFileName = $this->appDir.'template/'.$this->cBaseName.$this->templateId;
				if($this->access) $templateFileName .= '_e'; 
				$templateFileName .= '.xslt';
				$templateD = new DOMDocument;
				$templateD->load($templateFileName);
				$proc = new XSLTProcessor;
				$proc->importStyleSheet($templateD);
				//$proc->setParameter('', 'pe', $this->focus);
				$proc->setParameter('', 'id', $this->getId());
				if($this->pgRange != '') $proc->setParameter('', 'pgRange', $this->pgRange);
				//if($aFocused == 'true') $proc->setParameter('', 'focused', 'true');
				$outputD = @$proc->transformToDoc($this->dataDoc);
				return $outputD;
			}
		}
		public function initializeHistory(){
			$templateFileName = $this->appDir.'/system/template/doHistoryJS.xslt';
			$templateD = new DOMDocument;
			$templateD->load($templateFileName);
			$proc = new XSLTProcessor;
			$proc->importStyleSheet($templateD);
			$proc->setParameter('', 'id', $this->getId());
			$outputS = @$proc->transformToXML($this->dataDoc);
			$historyFileName = $this->appDir.'client/history.js';
			$fh = fopen($historyFileName, 'a') or die("can't open file");
			fwrite($fh, $outputS);
		}
		public function showEdit($aEditPosition){
			if($this->type != ''){
				$templateFileName = $this->appDir.'template/'.$this->cBaseName.$this->templateId.'_e.xslt';
				$templateD = new DOMDocument;
				$templateD->load($templateFileName);
				$proc = new XSLTProcessor;
				$proc->importStyleSheet($templateD);
				$proc->setParameter('', 'pe', $aEditPosition);
				$proc->setParameter('', 'id', $this->getId());
				$proc->setParameter('', 'onlyEditor', 'true');
				$output = @$proc->transformToXML($this->dataDoc);
				Component::$i = 0;
				return preg_replace_callback("|(xmlns(:[a-z]+)*=\"[^\"]+\")|", "Component::clearNamespace", $output);
			}
		}
		public function getId(){
			$result = $this->cBaseName;
			if($this->id != '') $result .= '_'.$this->id;
			if($this->lang != '') $result .= '_'.$this->lang;
			return $result;
		}
		public function showFragment($aXML, $aPosition){
			$templateFileName = $this->appDir.'template/'.$this->cBaseName.$this->templateId.'_e.xslt';
			$templateD = new DOMDocument;
			$templateD->load($templateFileName);
			$dataD = new DOMDocument;
			$dataD->loadXML($aXML);
			$proc = new XSLTProcessor;
			$proc->importStyleSheet($templateD);
			$proc->setParameter('', 'dataPosition', $aPosition - 1);
			$proc->setParameter('', 'id', $this->getId());
			$output = $proc->transformToXML($dataD);
			Component::$i = 0;
			return preg_replace_callback("|(xmlns(:[a-z]+)*=\"[^\"]+\")|", "Component::clearNamespace", $output);
		}
		public static function clearNamespace($aMatches){
			Component::$i++;
			if (Component::$i == 1) {
				if(strpos($aMatches[1],'xmlns=') !== false) return $aMatches[1];
				else return '';
			} else return '';
		}
		public function setTemplateId($aId){
			$this->templateId = $aId;
		}
	}
	class AccessList{
		private $access  = array();
		
		function addAccess($aName, $aId = ''){
			$this->access["{$aName}_{$aId}"] = true;
		}
		function hasAccess($aName, $aId = ''){
			$k = "{$aName}_{$aId}";
			return isset($this->access["{$aName}_{$aId}"]);
		}
	}
	class ComponentComposer{
		private $component;
		private $appDir;
		private $currentNode = null;
		private $doc = null;
		private $xpath;
		private $composedComponents = array();
		private $pg = 0;
		private $pgRangeList = array();
		private $templateList = array();
		function setPgRangeList(&$aPgRangeList){
			$this->pgRangeList = $aPgRangeList;
		}
		function setTemplateList($aTemplateList){
			$this->templateList = $aTemplateList;
		}
		function ComponentComposer($aAppDir, &$aComponent){
			$this->appDir = $aAppDir;
			$this->component = $aComponent;
			file_put_contents($aAppDir.'client/history.js',"// encoding=utf-8 \n viewHistory = new Array(); \n");
		}
		public function compose(&$aAccessList = null){
			//$this->component->getNamespaces();
			if($aAccessList){
				$focused = 'true';
				//echo $this->component->getId()." \n";
				$componentId = $this->component->getId();
				if(array_search($componentId,$this->composedComponents) === false){
					$this->composedComponents[] = $componentId;
					$this->component->initializeHistory();
				}
			}
			if($this->component->getCType() == 'Page') $this->pg++;
			//else $focused = 'false';
			$doc = $this->component->show();
			if($this->doc){
				if($this->currentNode->parentNode){
					$newNode = $doc->documentElement->cloneNode(true);
					$importNode = $this->doc->importNode($newNode,true);
					$newNode = $this->currentNode->parentNode->insertBefore($importNode, $this->currentNode);
					$this->currentNode->parentNode->removeChild($this->currentNode);
				}
			}else{
				$this->doc = $doc;
			}
			$xpath = new DOMXPath($doc);
			$cLinkList = $xpath->query("//*[local-name() = 'component']");
			foreach ($cLinkList as $currentNode) {
				$type = $currentNode->getAttribute('type');
				$id = $currentNode->getAttributeNS('http://formax.cz/impresso/system','id');
				$pgRange = $currentNode->getAttributeNS('http://formax.cz/impresso/system','pgRange');
				$this->component = new Component($this->appDir);
				$this->component->setCType($type);
				if(isset($this->templateList[$type])) $this->component->setTemplateId($this->templateList[$type]);
				$query = "//*[local-name() = 'component'][@type = '$type']";
				if($id != '') {
					$this->component->setCId($id);
					$query .= "[@s:id = '$id']";
				}
				if($pgRange != '') {
					$this->component->setCPgRange($this->pgRangeList[$this->pg - 1]);
				}
				if($aAccessList != null){
					if($aAccessList->hasAccess($type,$id)){
						$this->component->setAccess(true);
					}
				}
				$this->component->getDataDoc();
				$dxpath = new DOMXPath($this->doc);
				$dxpath->registerNamespace("s", "http://formax.cz/impresso/system");
				$this->currentNode = $dxpath->query($query)->item(0);
				$this->compose($aAccessList);
			}
		}
		public function show(){
			Component::$i = 0;
			echo preg_replace_callback("|(xmlns(:[a-z]+)*=\"[^\"]+\")|", "Component::clearNamespace", $this->doc->saveXML());
		}
	}
?>