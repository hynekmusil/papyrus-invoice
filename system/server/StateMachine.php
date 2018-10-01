<?php //encoding=utf-8
	require_once 'system/server/componentComposer.php';
	
	class Data{
		public $name;
		private $value;
		
		function __construct($aName, $aValue){
			$this->name = $aName;
			$this->value = $aValue;
		}
		function getContent(){
			return $this->value;
		}
	}
	class Event{
		private $name;
		private $data = array();
		
		function __construct($aName){ $this->name = $aName; }
		function getName(){ return $this->name; }
		function addData(&$aData){ $this->data[$aData->name] = $aData; }
		function getData($aName) { 
			if(isset($this->data[$aName])) return $this->data[$aName]->getContent(); 
			return '';
		}
	}
	
	
	abstract class Command{
		public $paramList = array();
		abstract public function execute();
	}
	class ShowComponent extends Command{
		public function execute(){
			$this->paramList[0]->getDataDoc();
			return $this->paramList[0]->show()->saveXML();
		}
	}
	class StateMachine{
		private $initialState = '';
		public $currentState = '';
		private $stateList = array();
		public $val = array();
		private $event = null;
		private $component = null;
		public $result = '';
		public $sendRegister = array('x-Command' => array('showComponent' => null));
		
		function __construct($aInitialState){
			$this->initialState = $aInitialState;
			$this->currentState = $this->initialState;
			$this->sendRegister['x-Command']['showComponent'] = new ShowComponent();
		}
		
		function addState(&$aState){
			$aState->setStateMachine($this);
			$this->stateList[$aState->getName()] = $aState;
		}
		
		function reset(){
			$this->currentState = $this->initialState;
		}
		
		function process(&$aEvent = null){
			$this->event = $aEvent;
			if(($currentState = $this->findState($this->currentState)) !== null){
				$this->currentState = $currentState->process($aEvent);
			}
		}
		function getState($aName){
			if(isset($this->stateList[$aName])) return $this->stateList[$aName];
			return null;
		}
		private function findState($aName){
			foreach($this->stateList as $state){
				if(($state = $state->findState($aName)) !== null) return $state;
			}
			return null;
		}
		public function _eventdata($aName){
			if($this->event == null) return '';
			return $this->event->getData($aName);
		}
		public function send($aTargettype, $aTarget, $aParamList){
			if(isset($this->sendRegister[$aTargettype][$aTarget])){
				$this->sendRegister[$aTargettype][$aTarget]->paramList = $aParamList;
				$this->result = $this->sendRegister[$aTargettype][$aTarget]->execute();
			}
		}
	}
	
	class State{
		protected $name;
		protected $transitionList = array();
		protected $stateList = array();
		public $handlerList = array();
		protected $stateMachine = null;
		protected $parentState = null;
		public $val = array();
		
		function __construct($aName){
			$this->name = $aName;
		}
		function process(&$aEvent = null){
			$eventName = '';
			if($aEvent != null) $eventName = $aEvent->getName();
			//zjisti prechod kterym dal.
			if(($transition = $this->getTransition($eventName)) === null) return $this->name;
			$target = $transition->getTarget();
			//jestlize opoustim tento stav a:
			if(($target != '') && ($target != $this->name)){
				//existuje onexit obsluha pak ji spust.
				if(isset($this->handlerList['onexit'])) call_user_func($this->handlerList['onexit']);
				//zjisti stav do ktereho se preklapim
				$state = $this->getTargetState($target);
				//existuje onentry obsluha u stavu do krereho se preklapim pak ji spust.
				if($state != null) {
					if (($handler = $state->getHandler('onentry')) !== false) {
						call_user_func($handler);
					}
				}
				$result = $state->getName();
			} else return $this->name;
			
			//jestlize existuje vnoreny inicializacni stav predej mu process
			if(count($state->stateList) > 0){
				if(($state = $state->getInitialState()) != null){
					$result = $state->process();
				}
			}
			return $result;
		}
		function addHandler($aType, $aFce = ''){
			if($aFce == '') $aFce = $aType;
			$this->handlerList[$aType] = $aFce;
		}
		function onEntry(){
			if(isset($this->handlerList['onentry'])) call_user_func($this->handlerList['onentry']);
		}
		function onExit(){
			if(isset($this->handlerList['onexit'])) call_user_func($this->handlerList['onexit']);
		}
		function addTransition(&$aTransition){
			$this->transitionList[] = $aTransition;
		}
		function addState(&$aState){
			$aState->setParentState($this);
			$aState->setStateMachine($this->stateMachine);
			$this->stateList[$aState->name] = $aState;
		}
		function getTransition($aEvent = ''){
			$i = 0;
			while($i < count($this->transitionList)){
				if($this->transitionList[$i]->test($aEvent)){
					return $this->transitionList[$i];
				}
				//echo "\n nevyhovuje: event=$aEvent  target=".$this->transitionList[$i]->getTarget()."\n";
				$i++;
			}
			if(($ps = $this->parentState) !== null){
				return $ps->getTransition($aEvent);
			}
			else return null;
		}
		function getName(){
			return $this->name;
		}
		function getInitialState(){
			foreach($this->stateList as $name => $state){
				if(get_class($state) == 'InitialState') return $state;
			}
			return null;
		}
		function getState($aName){
			if(isset($this->stateList[$aName])) return $this->stateList[$aName];
			return null;
		}
		function getHandler($aName){
			if(isset($this->handlerList[$aName])) return $this->handlerList[$aName];
			return false;
		}
		function setStateMachine(&$aStateMachine){
			$this->stateMachine = $aStateMachine;
		}
		function setParentState(&$aState){
			$this->parentState = $aState;
		}
		function getTargetState($aTarget){
			if(($ps = $this->parentState) != null) {
				if(($st = $ps->getState($aTarget)) == null) return $ps->getTargetState($aTarget);
				else return $st;
			}
			if(($sm = $this->stateMachine) != null) return $sm->getState($aTarget);
			return null;
		}
		function findState($aName){
			if($this->name == $aName) return $this;
			foreach($this->stateList as $state){
				if(($state = $state->findState($aName))  !== null) return $state;
			}
			return null;
		}
	}
	
	class InitialState extends State{
		function __construct(){
			$this->name = '';
		}
		function addState(&$aState){;}
	}
		
	class Transition{
		private $cond = '';
		private $assign = '';
		private $event = '';
		private $target = '';
		
		function test($aEvent = ''){
			$result = false;
			if($this->cond == ''){
				if($aEvent == $this->event) $result = true;
				if($this->target == '') $result = true;
			}
			else $result=call_user_func($this->cond);
			if($result && ($this->assign != '')){
				call_user_func($this->assign);
			}
			return $result;
		}
		function setAssign($aAssign){
			$this->assign = $aAssign;
		}
		function setCond($aCond = 'cond'){
			$this->cond = $aCond;
		}
		function setEvent($aEvent){
			$this->event = $aEvent;
		}
		function setTarget($aTarget){
			$this->target = $aTarget;
		}
		function getTarget(){
			return $this->target;
		}
	}
?>