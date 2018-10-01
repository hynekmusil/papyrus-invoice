<?php //encoding=utf-8

$root = new StateMachine('off'); // initialstate = off

$cst = new ComponentState("off"); // id = off;
$tr = new Transition();
$tr->setEvent("turn_on");
$tr->setTarget("on");
$cst->addTransition($tr);
$root->addState($cst);

$cst = new ComponentState("on");
$ist = new InitialState();
$cst->addState($ist);
$tr = new Transition();
$tr->setTarget("idle");
$cst->addTransition($tr);
function onentry(){
	if(isset(StateMachine::$val['cook_time'])) StateMachine::$val['cook_time'] = 5;
	if(isset(StateMachine::$val['door_closed'])) StateMachine::$val['door_closed'] = true;
	StateMachine::$val['timer'] = 0;
}
$cst->setFce('onentry');
$tr = new Transition();
$tr->setEvent("turn_off");
$tr->setTarget("off");
$cst->addTransition($tr);
$tr = new Transition();
function cond(){
	return StateMachine::$val['timer'] > StateMachine::$val['cook_time'];
}
$tr->setCond('cond');
$tr->setTarget("off");
$cst->addTransition($tr);

$cst1 = new ComponentState("idle");
$tr = new Transition();
function cond1(){
	return StateMachine::$val['door_closed'];
}
$tr->setCond('cond1');
$tr->setTarget("cooking");
$cst1->addTransition($tr);
$tr = new Transition();
$tr->setEvent("door_close");
$tr->setTarget("cooking");
function assign(){
	StateMachine::$val['door_closed'] = true;
}
$tr->setAssign('assign');
$cst1->addTransition($tr);
$cst->addState($cst1);

$cst1 = new ComponentState("cooking");
$tr = new Transition();
$tr->setEvent("door_open");
$tr->setTarget("idle");
function assign1(){
	StateMachine::$val['door_closed'] = false;
}
$tr->setAssign('assign1');
$cst1->addTransition($tr);
$tr = new Transition();
$tr->setEvent("time");
$tr->setTarget("cooking");
function assign2(){
	StateMachine::$val['time']++;
}
$tr->setAssign('assign2');
$cst1->addTransition($tr);
$cst->addState($cst1);
$root->addState($cst);
?>