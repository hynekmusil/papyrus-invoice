--TEST--
FSM: GraphViz
--SKIPIF--
<?php
/*require_once 'PEAR/Registry.php';
$registry = &new PEAR_Registry();
echo $registry->packageExists('FSM');
if (!$registry->packageExists('FSM')) die("skip\n");*/
?>--FILE--
<?php

require_once 'FSM.php';
require_once 'FSM/GraphViz.php';

$stack = array();
$fsm = new FSM('START', $stack);

$converter = new FSM_GraphViz($fsm);
$graph = $converter->export();

echo $graph->parse();
?>
--EXPECT--
digraph FSM {
"START" [ shape="invhouse" ];
}
