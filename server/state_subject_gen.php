<?php //encoding=utf-8
$sc = new StateMachine('initialFork');
$sc->val['subjectId'] = '';

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

$st = new ComponentState('selecting'); 
$st->setComponent('SubjectList','cs');
$tr = new Transition();
$tr->setEvent('select');
$tr->setTarget('selected');
function assign0(){
	global $sc;
	$sc->val['subjectId'] = $sc->_eventdata('subjectId');
}
$tr->setAssign('assign0');
$st->addTransition($tr);
$sc->addState($st);

$st = new ComponentState('selected'); 
$st->setComponent('Subject','cs','');
function onentry0(){
	send0();
}
function send0(){
	global $sc;
	$sc->send('setId','x-Component',$sc->val['subjectId']);
}
$st->addHandler('onentry0');$tr = new Transition();
$tr->setEvent('another');
$tr->setTarget('selecting');
$st->addTransition($tr);
$tr = new Transition();
$tr->setEvent('edit');
$tr->setTarget('selected');
$st->addTransition($tr);
$sc->addState($st);
?>