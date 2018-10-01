<?php
	header('Content-Type: text/xml; charset=utf-8');
	
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
			if($aElementType->minOcc == 0) {
				$this->optionalElementType[$this->i - 1][$this->j] = $aElementType;
				$this->j++;
			} else { 
				if($aElementType->maxOcc == 'unbouded'){
					$this->optionalElementType[$this->i - 1][$this->j] = $aElementType;
					$this->optionalElementType[$this->i ][$this->j] = $aElementType;
					$this->j++;
				} else $this->j = 0;
				$this->elementType[$this->i] = $aElementType;
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
			$op = array();
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
		
		function __construct(&$aElementType){
			$this->elementType = $aElementType;
			$this->elementList = $this->build($aElementType);
		}
		public function build(&$aElementType){
			$et = $aElementType;
			$elementList = array();
			$ml = array();
			$i = 0;
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
			//*/
			/*
			$this->elementList[$this->i] = new Element($aName);
			foreach($aAttr as $name=>$value){
				$att = new Attribute($name, $value);
				$this->elementList[$this->i]->addAttribute($att);
			}
			$this->elementList[$this->i]->position = $this->i + 1;
			$this->elementList[$this->i]->parent = 0;
			$this->elementList[$this->i]->previous = 0;
			$this->elementList[$this->i]->following = 0;
			if($this->i > 0) {
				$this->elementList[$this->i]->parent = $this->parentList[0];
				if($this->parentList[0] == ($this->i - 1)){
					$this->elementList[$this->i]->previous = 0;
				}else{
					$last = end($this->elementList[$this->parentList[0]]->children);
					$this->elementList[$this->i]->previous = $last;
					$this->elementList[$last - 1]->following = $this->i + 1;
				}
				$this->elementList[$this->parentList[0]]->children[] = $this->i + 1;
			}
			array_unshift($this->parentList, $this->i);
			$this->i++; */
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
		function addFollowing($aContextual, $aTypeName){
			$contextual = $this->elementList[$aContextual -1];
			// Muzeme pridavat? Zjistime podle toho zda nam to typ kontextoveho uzlu dovoli.
			if(array_key_exists($aTypeName,$contextual->type->elementOption->following)) {
				// Zjistime typ pridavaneho elementu a dle toho sestavime element ze vsech jeho povinnych slozek.
				$el = $this->build($contextual->type->elementOption->following[$aTypeName]);
				//Zjistime pozici nasledujiciho nedetskeho elementu ke ke kontextovemu
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
		}
		function getXML(&$aElementList = array()){
			echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
			$parentList = array();
			if(count($aElementList) == 0) $el = $this->elementList;
			else $el = $aElementList;
			foreach($el as $element){
				echo "<{$element->name}";
				foreach($element->attributeList as $attribute) echo ' '.$attribute->name.'="'.$attribute->value.'"';
				if(count($element->children) > 0){
					$info['name'] = $element->name;
					$info['following'] = $element->following;
					array_unshift($parentList,$info);
					echo ">".$element->text;
				} 
				else {
					if($element->text == '') echo "/>";
					else echo ">".$element->text."</{$element->name}>";
					if($element->following == 0){
						$parent= reset($parentList);
						echo "</{$parent['name']}>";
						while($parent['following'] == 0){
							array_shift($parentList);
							if(count($parentList) == 0) break;
							$parent= reset($parentList);
							echo "</{$parent['name']}>";
						}
						array_shift($parentList);
					}
				}
			}
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
				$i++;
			}
			return $result;
		}
	}
	
	$root = new RootElementType('comodityList');
	$root->addXMLNS('http://formax.cz/impresso/invoice');
	$root->addXMLNS('http://formax.cz/impresso/system','s');
	$root->addSchemaLocation('../schema/invoice.xsd');
	$att = new AttributeType('discount',false);
	$root->addAttributeType($att);
	$att = new AttributeType('currencyCode',false);
	$root->addAttributeType($att);
	$att = new AttributeType('xml:lang',true,'cs');
	$root->addAttributeType($att);
	$m0 = new Model('sequence');
	$root->setModel($m0);
	
	$et = new ElementType('comodity');
	$et->maxOcc = 'unbouded';
	$att = new AttributeType('id',false);
	$et->addAttributeType($att);
	$att = new AttributeType('quantity',false);
	$et->addAttributeType($att);
	$att = new AttributeType('unit',false);
	$et->addAttributeType($att);
	$m0->addElementType($et);
	$m1 = new Model('sequence');
	$et->setModel($m1);
	
	$et = new ElementType('name');
	$m1->addElementType($et);
	
	$et = new ElementType('price');
	$et->maxOcc = 'unbouded';
	$att = new AttributeType('currencyCode',false);
	$et->addAttributeType($att);
	$att = new AttributeType('VAT',false);
	$et->addAttributeType($att);
	$att = new AttributeType('discount',false);
	$et->addAttributeType($att);
	$m1->addElementType($et);
	$m2 = new Model('sequence');
	$et->setModel($m2);
	
	$et = new ElementType('one');
	$att = new AttributeType('nett',true);
	$et->addAttributeType($att);
	$att = new AttributeType('gross',false);
	$et->addAttributeType($att);
	$m2->addElementType($et);
	
	$et = new ElementType('sum');
	$et->minOcc = 0;
	$att = new AttributeType('nett',true);
	$et->addAttributeType($att);
	$att = new AttributeType('gross',false);
	$et->addAttributeType($att);
	$m2->addElementType($et);
	
	$et = new ElementType('summary');
	$et->minOcc = 0;
	$m0->addElementType($et);
	$m1= new Model('sequence');
	$et->setModel($m1);
	
	$et = new ElementType('one');
	$att = new AttributeType('nett',true);
	$et->addAttributeType($att);
	$att = new AttributeType('gross',false);
	$et->addAttributeType($att);
	$m1->addElementType($et);
	
	$et = new ElementType('sum');
	$att = new AttributeType('nett',true);
	$et->addAttributeType($att);
	$att = new AttributeType('gross',false);
	$et->addAttributeType($att);
	$m1->addElementType($et);
	
	$em = new ElementManager($root);

	$em->addFollowing(5, 'sum');
	$em->addFollowing(2,'summary');
	$em->addFollowing(2,'comodity');
	$em->addFollowing(10,'sum');
	$em->addFollowing(7, 'comodity');
	
	
	
	$base = Array();
	$base[0] = Array('position' => 1,'name' => 'comodityList', 'parent' => 0,  'previous' => 0, 'following' => 0, 'children' => Array(0 => 2,1 => 7,2 => 12, 3 => 16));
	$base[1] = Array('position' => 2, 'name' => 'comodity', 'parent' => 1, 'previous' => 0, 'following' => 7,'children' => Array(0 => 3,1 => 4));
	$base[2] = Array('position' => 3, 'name' => 'name', 'parent' => 2, 'previous' => 0, 'following' => 4, 'children' => Array());
	$base[3] = Array('position' => 4, 'name' => 'price', 'parent' => 2, 'previous' => 3, 'following' => 0, 'children' => Array(0 => 5, 1 => 6));
	$base[4] = Array('position' => 5, 'name' => 'one', 'parent' => 4, 'previous' => 0, 'following' => 6, 'children' => Array ());
	$base[5] = Array('position' => 6, 'name' => 'sum', 'parent' => 4, 'previous' => 5, 'following' => 0, 'children' => Array());
	$base[6] = Array('position' => 7, 'name' => 'comodity','parent' => 1,'previous' => 2, 'following' => 12, 'children' => Array(0 => 8,1 => 9));
	$base[7] = Array('position' => 8, 'name' => 'name','parent' => 7, 'previous' => 0, 'following' => 9,'children' => Array());
	$base[8] = Array('position' => 9,'name' => 'price', 'parent' => 7, 'previous' => 8, 'following' => 0,'children' => Array(0 => 10, 1=>11));
	$base[9] = Array('position' => 10, 'name' => 'one', 'parent' => 9, 'previous' => 0, 'following' => 11,'children' => Array());
	$base[10] = Array('position' => 11, 'name' => 'sum', 'parent' => 9, 'previous' => 10, 'following' => 0,'children' => Array());
	$base[11] = Array('position' => 12, 'name' => 'comodity','parent' => 1,'previous' => 7, 'following' => 16, 'children' => Array(0 => 13,1 => 14));
	$base[12] = Array('position' => 13, 'name' => 'name','parent' => 12, 'previous' => 0, 'following' => 14,'children' => Array());
	$base[13] = Array('position' => 14,'name' => 'price', 'parent' => 12, 'previous' => 13, 'following' => 0,'children' => Array(0 => 15));
	$base[14] = Array('position' => 15, 'name' => 'one', 'parent' => 14, 'previous' => 0, 'following' => 0,'children' => Array());
	$base[15] = Array('position' => 16, 'name' => 'summary','parent' => 1,'previous' => 12, 'following' => 0,'children' => Array(0 => 17,1 => 18));
	$base[16] = Array('position' => 17, 'name' => 'one', 'parent' => 16,'previous' => 0, 'following' => 18, 'children' => Array());
	$base[17] = Array('position' => 18,'name' => 'sum','parent' => 16, 'previous' => 17, 'following' => 0, 'children' => Array());
	
	$em->fill('comodityList.xml');
	$em->getXML();
	echo "<!--\n";
	$test = $em->getArray();
	if($base == $test) echo 'ok';
	else {
		echo "ko\n";
		echo "base oproti test:\n";
		print_r(array_diff($base, $test));
		echo "test oproti base:\n";
		print_r(array_diff($test, $base));
		echo "test:\n";
		print_r($test);
	}
	echo "\n-->";
	
?>