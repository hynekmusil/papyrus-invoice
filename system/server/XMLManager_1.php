<?php //encoding=utf-8
	class Element{
		public $name;
		public $position;
		public $parent;
		public $previous;
		public $following = 0;
		public $children = array();
		public $attributeList = array();
		//public $namespace;
		public $text;
		public $type;
		private $contextMenu;
		
		function __construct($aName, $aPosition = 1, $aParent=0, $aPrevious=0, $aFollowing=0, $aNamespace=''){
			$this->name = $aName;
			$this->position = $aPosition;
			$this->parent = $aParent;
			$this->previous = $aPrevious;
			$this->following = $aFollowing;
			//$this->namespace = $aNamespace;
		}
		function addAttribute(&$aAttribute){
			$this->attributeList[$aAttribute->name] = $aAttribute;
		}
		function setElementType(&$aElementType){
			$this->type = $aElementType;
		}
	}
	class Attribute{
		public $name;
		public $value;
		
		function __construct($aName, $aValue = ''){
			$this->name = $aName;
			$this->value = $aValue;
		}
	}
	class ElementType{
		public $name;
		public $position;
		public $maxOcc = 1;
		public $minOcc = 1;
		public $model = null;
		public $attributeType = array();
		public $optionalAttributeType = array();
		public $elementOption = null;
		public $buildCallback = '';
		
		function __construct($aName){
			$this->name = $aName;
		}
		function setModel(&$aModel){
			$this->model = $aModel;
		}
		function addAttributeType(&$aAttributeType){
			if($aAttributeType->isRequired) $this->attributeType[] = $aAttributeType;
			else $this->optionalAttributeType[] = $aAttributeType;
		}
		function setOptionList(&$aOption){
			$this->elementOption = $aOption;
		}
	}
	class RootElementType extends ElementType{
		public $schemaLocation = array();
		public $xmlns = array();
		
		function addSchemaLocation($aSchemaURL, $aPrefix = ''){
			$this->schemaLocation[$aPrefix] = $aSchemaURL;
		}
		function addXMLNS($aXMLNS, $aPrefix = ''){
			$this->xmlns[$aPrefix] = $aXMLNS;
		}
	}	
	class AttributeType{
		public $name;
		public $isRequired = true;
		public $defaultValue = '';
		
		function __construct($aName, $aIsRequired = true, $aDefaultValue = ''){
			$this->name = $aName;
			$this->isRequired = $aIsRequired;
			$this->defaultValue = $aDefaultValue;
		}
	}
	class ElementOption{
		public $previous = array();
		public $following = array();
		public $children = array();
		public $wrapper = array();
	}
	class Model{
		public $type = 'sequence';
		public $maxOcc;
		public $minOcc;
		public $elementType = array();
		public $optionalElementType = array();
		private $i = 0;
		private $j = 0;
		
		function __construct($aType){
			$this->type = $aType;
		}
		function addElementType(&$aElementType){
			if($this->type == 'sequence'){
				if($aElementType->minOcc == 0) {
					$this->optionalElementType[$this->i - 1][$this->j] = $aElementType;
					$this->j++;
				} 
				else { 
					if($aElementType->maxOcc == 'unbouded'){
						$this->optionalElementType[$this->i - 1][$this->j] = $aElementType;
						$this->optionalElementType[$this->i ][$this->j] = $aElementType;
						$this->j++;
					} else $this->j = 0;
					$this->elementType[$this->i] = $aElementType;
					$this->i++;
				}
			}
			elseif($this->type == 'choice'){
				if(($this->j == 0) && ($aElementType->minOcc != 0)){
					$this->elementType[0] = $aElementType;
					$this->j++;
				}
				else $this->optionalElementType[] = $aElementType;
			}
			elseif($this->type == 'all'){
				if($aElementType->minOcc == 0) $this->optionalElementType[$this->i] = $aElementType;
				else $this->elementType[$this->i] = $aElementType;
				$this->i++;
			}
		}
		function printOptionalElementType(){
			$result=array();
			foreach($this->optionalElementType as $i=>$typeList){
				foreach($typeList as $j=>$type){
					$result[$i][$j] = $type->name;
				}
			}
			print_r($result);
		}
		function setOptionList(){
			if($this->type == 'sequence'){
				$isBeginPrevious = false;
				if(array_key_exists(-1,$this->optionalElementType)) $isBeginPrevious = true;
				foreach($this->optionalElementType as $key=>$typeList){
					foreach($typeList as $type){
						$o[$type->name] = $type;
					}
					if($key == -1){
						$eo = new ElementOption;
						$eo->previous = $o;
					}elseif(($key == 0) && $isBeginPrevious){
						$eo = new ElementOption;
						$eo->following = $o;
						$this->elementType[$key]->setOptionList($eo);
					}else{
						$eo->following = $o;
						$this->elementType[$key]->setOptionList($eo);
						$eo = new ElementOption;
						$eo->previous = $o;
					}
				}
			} 
			elseif($this->type == 'choice') {
			
			}
		}
		function sumElementType(){
			return count($this->elementType);
		}
	}
	class ElementManager{
		public $elementList = array();
		private $schemaURL = '';
		private $elementType = null;
		private $parentList = array();
		private $following;
		private $i = 0;
		private $el = array();
		
		function __construct(&$aElementType){
			$this->elementType = $aElementType;
			$this->elementList = $this->build($aElementType);
		}
		public function build(&$aElementType){
			$et = $aElementType;
			$elementList = array();
			$ml = array();
			$i = 0;
			$value = '';
			do{
				if($et->model != null) $et->model->setOptionList();
				$elementList[$i] = new Element($et->name);
				$elementList[$i]->setElementType($et);
				foreach($et->attributeType as $attributeType) {
					$att = new Attribute($attributeType->name, $attributeType->defaultValue);
					$elementList[$i]->addAttribute($att);
				}
				if(get_class($et) == 'RootElementType'){
					if(count($et->schemaLocation) > 0){
						$att = new Attribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
						$elementList[$i]->addAttribute($att);
						foreach($et->schemaLocation as $prefix=>$schemaLocation){
							$value .= $et->xmlns[$prefix]." ".$schemaLocation." ";
						}
						$att = new Attribute('xsi:schemaLocation', $value);
						$elementList[$i]->addAttribute($att);
					}
					foreach ($et->xmlns as $prefix=>$xmlns){
						if($prefix != '') $att = new Attribute("xmlns:$prefix", $xmlns);
						else $att = new Attribute("xmlns", $xmlns);
						$elementList[$i]->addAttribute($att);
					}
				}
				$elementList[$i]->position = $i +1;
				$elementList[$i]->previous = 0;
				$elementList[$i]->following = 0;
				if(count($ml) != 0) {
					$elementList[$i]->parent = $ml[0]['parent'];
					$pp = $ml[0]['parent'] - 1;
					if(count($elementList[$pp]->children) > 0) {
						$ch = end($elementList[$pp]->children);
						$elementList[$i]->previous = $ch;
						$elementList[$ch - 1]->following = $i + 1;
					}
					$elementList[$pp]->children[] = $i + 1;
				}
				else $elementList[$i]->parent = 0;
				if($et->model == null) {
					if(count($ml) == 0) break;
					while($ml[0]['model']->sumElementType() <= ($ml[0]['ti'] +1)){
						array_shift($ml);
						if(count($ml) == 0) break 2;
					}
					$ml[0]['ti']++;
				}
				else{
					$model['parent'] = $i + 1;
					$model['ti'] = 0;
					$model['model'] = $et->model;
					array_unshift($ml,$model);
				}
				$et = $ml[0]['model']->elementType[$ml[0]['ti']];
				$i++;
			} while(count($ml) > 0);
			if($aElementType->buildCallback != '') {
				$elementList = call_user_func($aElementType->buildCallback, $elementList);
				echo "<!--\n";
				print_r($this->getArray($elementList));
				echo "\n-->\n";
			}
			
			return $elementList;
		}
		private function openElement($aParser, $aName, $aAttr){
			//*
			if(!(count($this->elementList) > $this->i)) {
				$last = end($this->elementList[$this->parentList[0]]->children);
				$this->addFollowing($last, $aName);
			}
			foreach($aAttr as $name=>$value){
				$att = new Attribute($name, $value);
				$this->elementList[$this->i]->addAttribute($att);
			}
			array_unshift($this->parentList, $this->i);
			$this->i++;
		}
		private function closeElement($aParser, $aName){
			array_shift($this->parentList);
		}
		private function textElement($aParser,$aText){
            $text = trim($aText);
            if($text) $text= preg_replace('/  */',' ',$aText);
            $this->elementList[$this->i - 1]->text .= $text;
        }
		public function fill($aFromURI){
			if($fp = fopen ($aFromURI, "r")){
				$parser = xml_parser_create("utf-8");
				xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
				xml_set_object($parser,$this);
				xml_set_element_handler($parser,"openElement","closeElement");
				xml_set_character_data_handler($parser, "textElement");
				//$this->elementList = array();
				$this->elementList = $this->build($this->elementType);
				while($data = fread($fp, 4096)) xml_parse($parser,$data);
				xml_parser_free($parser);
				fclose($fp);
			}
			return $this->elementList;
		}
		function getFollowing($aPosition, $aDebug = false){
			$element= $this->elementList[$aPosition - 1];
			if($aDebug) echo $element->name."(following = ".$element->following.") -> ";
			if($element->following > 0) $this->following = $element->following;
			else{
				if($aPosition > 1){
					$this->getFollowing($element->parent, $aDebug);
				}
				else $this->following = 0;
			}
			return $this->following;
		}
		function increaceRelations($aFrom, $aIncrement, &$aElementList, &$aParent = null){
			$increment = $aIncrement;
			foreach($aElementList as $key=>$element){
				if($element->position > $aFrom) $element->position += $increment;
				if($element->parent > $aFrom) $element->parent += $increment;
				if($element->previous > $aFrom) {
					$element->previous += $increment;
				}
				if($element->following > $aFrom) $element->following += $increment;
				for($i = 0; $i < count($element->children); $i++){
					if($element->children[$i]  > $aFrom) {
						$element->children[$i] += $aIncrement;
					}
				}
			}
			if($aParent){
				foreach($aElementList as $element){
					if($element->parent == 0) $element->parent = $aParent->position;
				}
			}
		}
		function prepareElementList(&$aElementList){
			$i = 0;
			$e = 0;
			foreach($aElementList as $element){
				if($element->parent == 0){
					//$element->parent = $parent->position;
					$element->previous = $e + 1;
					$aElementList[$e]->following = $i + 1;
					$result[] = $aContextual + $i + 1;
					$e = $i;
				}
				$i++;
			}
			return $result;
		}
		function mergeElementList($aContextual, &$aElementList){
			$elementList1 = array_slice($this->elementList, 0 ,$aContextual);
			$elementList2 = array_slice($this->elementList, $aContextual);
			$this->elementList = array_merge($elementList1,$aElementList,$elementList2);
		}
		function addFirstChild($aContextual, $aElementList){
			$parent = $this->elementList[$aContextual - 1];
			if(count($parent->children) > 0){
				$following = $parent->children[0] + count($aElementList);
			} else {
				$following = 0;
			}
			$force = $this->prepareElementList($aElementList,0,$following);
			$this->increaceRelations(0, $aContextual, $aElementList, $parent);
			$this->increaceRelations($aContextual, count($aElementList), $this->elementList);
			$parent->children = array_merge($force,$parent->children);
			$this->mergeElementList($aContextual, $aElementList);
		}
		function addLastChild($aContextual, $aElementList){
		
		}
		function addPrevious($aContextual, $aElementList){
			$contextual = $this->elementList[$aContextual - 1];
			$previous = $contextual->previous;
			$following = $aContextual + count($aElementList);
			$parent = $this->elementList[$contextual->parent - 1];
			$force = $this->prepareElementList($aElementList,$previous,$following);
			$this->increaceRelations(0, $aContextual, $aElementList, $parent);
			$this->increaceRelations($following, count($aElementList), $this->elementList);
			$parent->children = array_merge($force,$parent->children);
			$this->mergeElementList($aContextual - 1, $aElementList);
		}
		function addFollowing($aContextual, $aTypeName, &$aPostAdd = array()){
			$contextual = $this->elementList[$aContextual -1];
			// Muzeme pridavat? Zjistime podle toho zda nam to typ kontextoveho uzlu dovoli.
			if(array_key_exists($aTypeName,$contextual->type->elementOption->following)) {
				// Zjistime typ pridavaneho elementu a dle toho sestavime element ze vsech jeho povinnych slozek.
				$el = $this->build($contextual->type->elementOption->following[$aTypeName]);
				//Zjistime pozici nasledujiciho nedetskeho elementu ke kontextovemu
				if(($following = $this->getFollowing($aContextual))>0){
					// Jestlize nasledujici nedetsky element ke kontextovemu existuje, 
					//pak zvysujeme vsechny vazby u elementu s pozici vetsi jak pozice nasledujiciho nedetskeho elementu - 1 o pocet elementu v pridavanem elementu.
					$this->increaceRelations($following - 1, count($el), $this->elementList);
				} else $following = count($this->elementList) + 1;
				//Zvysime vsechny vazby u pridavaneho elementu o pozicici nasledujiciho nedetskeho elementu - 1. 
				//Upravime vazbu vzhledem na rodice pridavaneho elementu
				$parent = $this->elementList[$contextual->parent - 1];
				$this->increaceRelations(0, $following - 1, $el, $parent);
				//Upravime vazbu k predchozimu a nasledujicimu elementu pridavaneho elementu
				$el[0]->previous = $contextual->position;
				$el[0]->following = $contextual-> following;
				//Upravime vazbu na nasledujici kontextoveho elementu tak aby ukazoval na pridavany element
				$contextual->following = $following;
				//Pripojime pridavany element k stavajicim elementum.
				if($following == (count($this->elementList) + 1)) $this->elementList = array_merge($this->elementList, $el);
				else $this->mergeElementList($following -1, $el);
				//Upravime vazby rodice od kontekteveho elementu na vsechny jeho deti. 
				$children = $parent->children;
				foreach($children as $i=>$child){
					if($child > $aContextual) $parent->children[$i+1] = $children[$i];
					if($child == $aContextual) $parent->children[$i+1] = $el[0]->position;
					//Upravime vazby deti na predchazejici elementy
					if($child > $aContextual) {
						if($this->elementList[$child - 1]->previous > 0) $this->elementList[$child - 1]->previous = $this->elementList[$parent->children[$i] - 1]->position ;
					}
				}
			}
			$i = $this->insertAddition($aPostAdd , $el[0]->position);
			$el = array_slice($this->elementList, $el[0]->position -1, count($el) + $i);
			return $el;
		}
		function remove($aContextual){
			//Zjistime pozici nasledujiciho nedetskeho elementu ke kontextovemu
			if(($following = $this->getFollowing($aContextual))>0){
				//pocet elementu v odstranovanem elementu
				$removeCount = $aContextual - $following;
				// Jestlize nasledujici nedetsky element ke kontextovemu existuje, 
				//pak snizujeme vsechny vazby u elementu s pozici vetsi jak pozice nasledujiciho nedetskeho elementu - 1 o pocet elementu v odstranovanem elementu.
				$this->increaceRelations($following - 1, $removeCount, $this->elementList);
			}
			$r = array();
			foreach($this->elementList as $k => $e){
				$r[$k] = $e->name;
			}
			array_splice($this->elementList,$aContextual - 1, -1*$removeCount);
		}
		function addInto($aContextual, $aTypeName, &$aPostAdd = array()){

		}
		function getXML(&$aElementList = array()){
			$result = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
			$parentList = array();
			if(count($aElementList) == 0) $el = $this->elementList;
			else {
				$el = $aElementList;
				if(get_class($this->elementType) == 'RootElementType'){
					$att = new Attribute('xmlns',$this->elementType->xmlns['']);
					reset($el)->addAttribute($att);
				}
			}
			foreach($el as $element){
				$result .= "<{$element->name}";
				if(is_array($element->attributeList)) foreach($element->attributeList as $attribute) $result .= ' '.$attribute->name.'="'.$attribute->value.'"';
				if(count($element->children) > 0){
					$info['name'] = $element->name;
					$info['following'] = $element->following;
					array_unshift($parentList,$info);
					$result .= ">".$element->text;
				} 
				else {
					if($element->text == '') $result .= "/>";
					else $result .= ">".$element->text."</{$element->name}>";
					if($element->following == 0){
						$parent= reset($parentList);
						$result .= "</{$parent['name']}>";
						while($parent['following'] == 0){
							array_shift($parentList);
							if(count($parentList) == 0) break;
							$parent= reset($parentList);
							$result .= "</{$parent['name']}>";
						}
						array_shift($parentList);
					}
				}
			}
			return $result;
		}
		function getArray(&$aElementList = array()){
			$result = array();
			$i = 0;
			if(count($aElementList) == 0) $el = $this->elementList;
			else $el = $aElementList;
			foreach($el as $element){
				$result[$i]['position'] = $element->position;
				$result[$i]['name'] = $element->name;
				$result[$i]['parent'] = $element->parent;
				$result[$i]['previous'] = $element->previous;
				$result[$i]['following'] = $element->following;
				$result[$i]['children'] = $element->children;
				if(is_array($elementList->attributeList)) foreach($elementList->attributeList as $attribute) $result[$i]['attribute'][$attribute->name] = $attribute->value;
				$i++;
			}
			return $result;
		}
		function getElementWithAllChild($aPosition){
			$r = $this->elementList[$aPosition - 1];
			$this->el[$aPosition - 1] = $r;
			//echo print_r($this->getarray($r));
			if(count($r->children)>0) foreach($r->children as $child) $this->getElementWithAllChild($child);
			return $this->el;
		}
		function insertAddition(&$aAddition, $aPosition){
			$i = 0;
			if(isset($aAddition['following'])){
				if(count($aAddition['following']) > 0){
					foreach($aAddition['following'] as $position => $typeName){
						$this->addFollowing($aPosition + $position - 1, $typeName);
						$i++;
					}
				}
			}
			if(isset($aAddition['attribute'])){
				if(count($aAddition['attribute']) > 0){
					foreach($aAddition['attribute'] as $position => $name){
						$att = new Attribute($name, 0);
						$this->elementList[$aPosition + $position - 2]->addAttribute($att);
					}
				}
			}
			return $i;
		}
	}
?>