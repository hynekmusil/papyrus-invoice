<?php
	class Element{
		public $name;
		public $position;
		public $attributeList = array();
		public $parent;
		public $previous;
		public $following;
		public $children = array();
		public $namespace;
		public $text;
		
		function __construct($aName, $aPosition = 1, $aParent=0, $aPrevious=0, $aNamespace=''){
			$this->name = $aName;
			$this->position = $aPosition;
			$this->parent = $aParent;
			$this->previous = $aPrevious;
			$this->namespace = $aNamespace;
		}
	}
	
	class ElementManager{
		//private $typeList = array();
		private $elementList = array();
		private $forced;
		private $increment;
		
		function __construct($aRootName){
			$this->elementList[1] = new Element($aRootName);
		}
		
		function getNext($aPosition){
			if($this->elementList[$aPosition]->following > 0){
				return $this->elementList[$aPosition]->following;
			} elseif(($parent = $this->elementList[$aPosition]->parent) > 0) {
				$this->getNext($parent);
			} else return 0;
		}
		
		function increaseRelations($aFrom, &$aElementList){
			foreach($aElementList as $element){
				if($element->position > $aFrom) $element->position += $this->increment;
				if($element->parent > $aFrom) $element->parent += $this->increment;
				if($element->previous > $aFrom) $element->previous += $this->increment;
				if($element->following > $aFrom) $element->following += $this->increment;
				foreach($elementList->children as $child){
					if($child > $aFrom) $child += $this->increment;
				}
			}
		}
		
		function addFirstChild($aContextual, $aElementList){
			$this->increment = count($aElementList);
			$this->increaceRelations(0,$aElementList);
			$this->forced = $aContextual +1;
			$parent = this->elementList[$aContextual]->parent;
			$aElementList[1]->parent = $parent->position;
			$aElementList[1]->previous = 0;
			if(count($parent->children) > 0){
				$aElementList[1]->following = $parent->children[0] + $this->increment;
			} else {
				$aElementList[1]->following = 0;
			}
			$this->increaseRelations($this->forced,$this->elementList);
			$elementList1 = array_slice($this->elementList, 0 ,$aContextual);
			$elementList2 = array_slice($this->elementList, $aContextual);
			$this->elementList = array_merge($elementList1,$aElementList,$elementList2);
		}
	}
	
	
	$em1 = new ElementManager('root');
	$el1[] = new Element('child1');
	$el2[] = new ElementManager('child2');
	$ec->addFirstChild(1, $el1);
	
?>