<?php //encoding=utf-8
	
	$root = new RootElementType('paymentProcess');
	$root->addXMLNS('http://formax.cz/impresso/invoice');
	$root->addXMLNS('http://formax.cz/impresso/system','s');
	$root->addSchemaLocation('../schema/invoice.xsd');
	$att = new AttributeType('xml:lang',true,'cs');
	$root->addAttributeType($att);
	$m0 = new Model('sequence');
	$root->setModel($m0);
	
	$et = new ElementType('due');
	$att = new AttributeType('date',true,date('Y-m-d'));
	$et->addAttributeType($att);
	$m0->addElementType($et);
	
	$taxDate  = date('Y-m-d', mktime (0,0,0,date("m")  ,date("d")+14,date("Y")));

	$et = new ElementType('order');
	$att = new AttributeType('date',true,$taxDate);
	$et->addAttributeType($att);
	$m0->addElementType($et);
	
	$et = new ElementType('payment');
	$att = new AttributeType('type',true,'převod');
	$et->addAttributeType($att);
	$m0->addElementType($et);
	
	$et = new ElementType('tax');
	$att = new AttributeType('date',true,$taxDate);
	$et->addAttributeType($att);
	$m0->addElementType($et);
	
	$compute = 'computeDate';
	
	function computeDate(&$aUpdate){
		$keys = array_keys($aUpdate);
		$min = $keys[0];
		foreach($keys as $key)  if($min > $key) $min = $key;
		$aUpdate[$min]['date'] = formatDate($aUpdate[$min]['date']);
		$aUpdate[$min + 1]['date'] = formatDate($aUpdate[$min + 1]['date']);
		$aUpdate[$min + 3]['date'] = formatDate($aUpdate[$min + 3]['date']);
	}
	
	function formatDate($aDate){
		$date = $aDate;
		$dateItems = explode('.',$date);
		if(strlen($dateItems[0]) == 1) $day = '0'.$dateItems[0];
		else $day = $dateItems[0];
		if(strlen($dateItems[1]) == 1) $month = '0'.$dateItems[1];
		else $month = $dateItems[1];
		return "{$dateItems[2]}-$month-$day";
	}
	
?>