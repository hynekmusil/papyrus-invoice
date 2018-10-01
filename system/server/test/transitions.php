--TEST--
FSM: Transitions
--FILE--
<?php

require_once 'FSM.php';

function defaultTransition($symbol, &$payload)
{
    array_push($payload, $symbol);
    echo "Default\n";
}

function transition1($symbol, &$payload)
{
    array_push($payload, $symbol);
    echo "Transition 1\n";
}

function transition2($symbol, &$payload)
{
    array_push($payload, $symbol);
    echo "Transition 2\n";
}

$stack = array();

$fsm = new FSM('START', $stack);
$fsm->setDefaultTransition('START', 'defaultTransition');
$fsm->addTransition('TRANS1', 'START', 'FINISH', 'transition1');
$fsm->addTransition('TRANS2', 'FINISH', 'START', 'transition2');

$fsm->process('TRANS2');
$fsm->process('TRANS1');

var_dump($stack);
?>
--EXPECT--
Default
Transition 1
array(2) {
  [0]=>
  string(6) "TRANS2"
  [1]=>
  string(6) "TRANS1"
}
