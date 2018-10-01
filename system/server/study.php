function setIncrement(){
			$this->increment = 1;
			if(($this->operation == 'replace') or ($this->operation == 'remove')){
				if(($next = $this->getNext($this->position)) > 0){
					$this->increment = $this->position - $next;
				}
			}
			if($this->operation == 'remove') $this->incerement--;
		}
		
		
		
		function prepareOnPrevious(){
			for($i = 1; $i < $this->position; $i++){
				foreach($this->elementList[$i]->children as $child){
					if($child > $this->position) $child + $this->increment;
				}
			}
			if(($this->operation == 'addPrevious') or ($this->operation == 'addFollowing')){
				$parent = $this->elementList[$this->position]->parent;
				$children = array();
				foreach($this->elementList[$parent]->children as $child){
					if($child < $this->position) $children[] = $child;
					elseif($child == $this->position){
						if($this->operation == 'addPrevious') $children[] = $this->position;
						$children[] = $this->position + 1;
					}else $children[] = $child;
				}
				$this->elementList[$parent]->children = $children;
			}
		}
		function prepareOnCurr(){
			if($this->operation == 'addFirstChild'){
				$children = array();
				$children[] = reset($this->elementList[$this->position]->children[]);
				foreach($this->elementList[$this->position]->children as $child){
					$children[] = $child + 1;
				}
				$this->elementList[$this->position]->children = $children;
				$this->elementList[$this->position]->following++;
			}
			if($this->operation == 'addLastChild'){
				$this->elementList[$this->position]->children[] = end($this->elementList[$aPosition]->children) + 1;
				if($this->elementList[$this->position]->following > 0) $this->elementList[$this->position]->following++;
			}
			if($this->operation == 'addPrevious'){
				$this->elementList[$this->position]->position = $this->position + 1;
				$this->elementList[$this->position]->previous = $this->position;
				if($this->elementList[$this->position]->following > 0) $this->elementList[$this->position]->following++;
			}
			if($aOperation == 'replace'){
				if($this->elementList[$this->position]->following > 0) $this->elementList[$this->position]->following = $this->position + 1;
				$this->elementList[$this->position]->children = array();
			}
			if($aOperation == 'doWrapper'){
				$children = array();
				foreach($this->elementList[$this->position]->children as $child){
					$child++;
				}
				if($this->elementList[$this->position]->following > 0) $this->elementList[$this->position]->following++;
				$children = array();
				foreach($this->elementList[$this->position]->children as $child){
					$children[] = $child++;
				}
				$this->elementList[$this->position]->children = array();
				$this->elementList[$this->position]->children[] = $this->position + 1;
				$this->elementList[$this->position + 1]->position = $this->position +1;
				$this->elementList[$this->position + 1]->previous = 0;
				$this->elementList[$this->position + 1]->following = 0;
				$this->elementList[$this->position + 1]->children  = $children;
			}
		}
		function prepareOnFollowing(){
			for($i = $this->position + $this->increment; $i <= count($this->elementList); $i++){
				$this->elementList[$i]->position = 
			}
		}
		
		
		function doOperation($aOperation, $aPosition){
			$this->operation = $aOperation;
			$this->position = $aPosition;
			if(count($this->elementList) == 0){
				$this->operation = 'addRoot';
				$this->position = 1;
			}elseif(isset($this->elementList[$this->position])){
				$this->setIncrement();
				$this->prepareOnPrevious();
				$this->prepareOnCurr();
				$this->prepareOnFollowing();
			}else{
				return false;
			}
		}
		
		function createElement($aName, $aMode='before', $aParent=0, $aPrevious=0, $aNamespace=''){
			$currPosition = 1;
			if(count($this->elementList) > 0){
				if($aPrevious > 0){
					$currPosition = $aPrevious + 1;
				}
				if($aParent > 0){
					$currPosition = $aParent + 1;
					$this->elementList[$aParent]->children[] = $currPosition;
				}
			}
			echo "parent:$aParent  currPosition:$currPosition  name:$aName<br/>";
			$this->currentElement = new Element($aName, $currPosition, $aParent, $aPrevious, $aNamespace);
			if(count($this->elementList) < $currPosition) {
				$this->elementList[$currPosition] = $this->currentElement;
				$this->currentElement->following = 0;
			} else {
				$elementList = array();
				$i = 1;
				foreach($this->elementList as $position=>$element){
					if($position < $currPosition){
						$elementList[$position] = $element;
					}
					elseif($position == $currPosition){
						$elementList[$position] = $this->currentElement;
						if($aMode == 'wrapper'){
							if($element->following > 0){
								$this->currentElement->following = $element->following+1;
							}else{
								$this->currentElement->following = 0;
							}
							$this->currentElement->children[] = $position+1;
							$element->parent = $position;
							$element->following = 0;
							$element->previous =0;
							foreach($element->children as $key=>$value){
								$value++;
							}
							$elementList[$position + 1] = $element;
						}elseif($aMode == 'replace'){
							if($element->following !== 0){
								$i = $currPosition - $element->following + 1;
								$this->currentElement->following = $element->following + $i;
							}else{
								$this->currentElement->following = 0;
							}
						}elseif($aMode == 'before'){
							$this->currentElement->following = $element->following + $i;
							$element->previous = $position;
							if($element->following !== 0){
								$element->following++;
							}
							foreach($element->children as $key=>$value){
								$value++;
							}
							$elementList[$position + 1] = $element;
						}
					} 
					else {
						if(($position + $i) > $currPosition){
							if($element->parent > $currPosition){
								$element->parent += $i;
							}
							if($element->previous > $currPosition){
								$element->previous += $i;
							}
							foreach($element->children as $key=>$value){
								$value++;
							}
							$element->following += $i;
							$elementList[$position + $i] = $element;
						}
					}
				}
				$this->elementList = $elementList;
			}
		}
		function saveXML($aPosition = 1){
			print_r($this->elementList);
			foreach($this->elementList as $element){
				echo $element->name;
			}
			/*echo "<{$this->elementList[$aPosition]->name}";
			if(count($this->elementList[$aPosition]->children) > 0){
				echo ">";
				
				echo "<{$this->elementList[$aPosition]->name}>";
			} else {
				echo "/>";
				if($this->elementList[$aPosition]->following){
			
				}
			}*/
		}
	}
	
	class ComplexType{
		private $name;
		
		function _construct($aName){
			$this->name = $aName;
		}