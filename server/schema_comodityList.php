<?php //encoding=utf-8
	
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
	$att = new AttributeType('id',false,'-');
	$et->addAttributeType($att);
	$att = new AttributeType('quantity',false, 0);
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
	$att = new AttributeType('nett',true, 0);
	$et->addAttributeType($att);
	$att = new AttributeType('gross',false, 0);
	$et->addAttributeType($att);
	$m2->addElementType($et);
	
	$et = new ElementType('sum');
	$et->minOcc = 0;
	$att = new AttributeType('nett',true, 0);
	$et->addAttributeType($att);
	$att = new AttributeType('gross',false, 0);
	$et->addAttributeType($att);
	$m2->addElementType($et);
	
	$et = new ElementType('summary');
	$et->minOcc = 0;
	$m0->addElementType($et);
	$m1= new Model('sequence');
	$et->setModel($m1);
	
	$et = new ElementType('one');
	$att = new AttributeType('nett',true, 0);
	$et->addAttributeType($att);
	$att = new AttributeType('gross',false, 0);
	$et->addAttributeType($att);
	$m1->addElementType($et);
	
	$et = new ElementType('sum');
	$att = new AttributeType('nett',true, 0);
	$et->addAttributeType($att);
	$att = new AttributeType('gross',false, 0);
	$et->addAttributeType($att);
	$m1->addElementType($et);
	
	$adder['comodity']['following'][4] = 'sum';
	$adder['comodity']['attribute'][1] = array(array('name'=>'id','value'=>'-'),array('name'=>'quantity','value'=>0));
	$adder['comodity']['attribute'][3] = array('name'=>'VAT','value'=>0);
	$adder['comodity']['attribute'][4] = array('name'=>'gross','value'=>0);
	$adder['comodity']['attribute'][5] = array('name'=>'gross','value'=>0);
	
	$compute = 'computeComodity';
	
	function initiate(&$aManager){
		$att = new Attribute('quantity', 0);
		$aManager->elementList[1]->addAttribute($att);
		$att = new Attribute('id', '-');
		$aManager->elementList[1]->addAttribute($att);
		$att = new Attribute('VAT', 0);
		$aManager->elementList[3]->addAttribute($att);
		$aManager->addFollowing(5,'sum');
		$att = new Attribute('gross', 0);
		$aManager->elementList[5]->addAttribute($att);
		$aManager->elementList[4]->addAttribute($att);
		return $aManager->getXML();
	}
	
	function computeComodity(&$aUpdate){
		$keys = array_keys($aUpdate);
		$min = $keys[0];
		foreach($keys as $key)  if($min > $key) $min = $key;
		$aUpdate[$min + 3]['nett'] = floatval(str_replace(',','.',$aUpdate[$min + 3]['nett']));
		$aUpdate[$min + 3]['gross'] = floatval(str_replace(',','.',$aUpdate[$min + 3]['gross']));
		if($aUpdate[$min + 3]['nett'] > 0){
			$aUpdate[$min + 3]['gross'] = $aUpdate[$min + 3]['nett'] * (1 + $aUpdate[$min + 2]['VAT']/100);
		} else {
			$aUpdate[$min + 3]['nett'] = $aUpdate[$min + 3]['gross'] /(1 + $aUpdate[$min + 2]['VAT']/100);
		}
		$aUpdate[$min + 4]['nett'] = $aUpdate[$min + 3]['nett'] * intval($aUpdate[$min]['quantity']);
		$aUpdate[$min + 4]['gross'] = $aUpdate[$min + 3]['gross'] * intval($aUpdate[$min]['quantity']);
	}
	
?>