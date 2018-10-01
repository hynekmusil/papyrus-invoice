<?php //encoding=utf-8
$sc = new StateMachine('initialFork');
$sc->val['subjectId'] = '';
$sc->val['component'] = '';

$st = new State('initialFork'); 
$tr = new Transition();
function cond0(){
	global $sc;
	return empty($sc->val['subjectId']);
}
$tr->setCond('cond0');
$tr->setTarget('selecting');
$st->addTransition($tr);
$tr = new Transition();
function cond1(){
	global $sc;
	return isset($sc->val['isset(subjectId)']);
}
$tr->setCond('cond1');
$tr->setTarget('selected');
$st->addTransition($tr);
$sc->addState($st);

$st = new State('selecting'); 
function onentry0(){
	assign0();
	send0();
}
function assign0(){
	global $sc;
	$sc->val['component'] = new Component('./');
	$sc->val['component']->setCType('SubjectList');
	//$sc->val['component']->setLang('cs');
}
function send0(){
	global $sc;
	$sc->send('x-Command','showComponent', array($sc->val['component']));
}
$st->addHandler('onentry','onentry0');
$tr = new Transition();
$tr->setEvent('select');
$tr->setTarget('selected');
function assign1(){
	global $sc;
	$sc->val['subjectId'] = $sc->_eventdata('subjectId');
}
$tr->setAssign('assign1');
$st->addTransition($tr);
$sc->addState($st);

$st = new State('selected'); 
function onentry1(){
	assign2();
	send1();
}
function assign2(){
	global $sc;
	$sc->val['component'] = new Component('./');
	$sc->val['component']->setCType('Subject');
	//$sc->val['component']->setLang('cs');
	$sc->val['component']->setTemplateId('_sc');
	$sc->val['component']->setCId($sc->val['subjectId']);
}
function send1(){
	global $sc;
	$sc->send('x-Command','showComponent', array($sc->val['component']));
}
$st->addHandler('onentry','onentry1');
$tr = new Transition();
$tr->setEvent('another');
$tr->setTarget('selecting');
$st->addTransition($tr);
$tr = new Transition();
$tr->setEvent('edit');
$tr->setTarget('selected');
$st->addTransition($tr);
$sc->addState($st);
?>