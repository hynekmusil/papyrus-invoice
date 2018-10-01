<?php //encoding=utf-8
	
	$root = new RootElementType('summary');
	$root->addXMLNS('http://formax.cz/impresso/invoice');
	//$root->addXMLNS('http://formax.cz/impresso/system','s');
	$root->addSchemaLocation('../schema/invoice.xsd');
	$att = new AttributeType('xml:lang',true,'cs');
	$root->addAttributeType($att);
	$m0 = new Model('sequence');
	$root->setModel($m0);
	
	$et = new ElementType('rate');
	$et->maxOcc = 'unbouded';
	$att = new AttributeType('percentage',true,0);
	$et->addAttributeType($att);
	$att = new AttributeType('base',true, 0);
	$et->addAttributeType($att);
	$att = new AttributeType('ratio',true, 0);
	$et->addAttributeType($att);
	$att = new AttributeType('sum',true, 0);
	$et->addAttributeType($att);
	$m0->addElementType($et);

	$et = new ElementType('discount');
	$et->minOcc = 0;
	$att = new AttributeType('percentage',true,0);
	$et->addAttributeType($att);
	$att = new AttributeType('base',true, 0);
	$et->addAttributeType($att);
	$att = new AttributeType('ratio',true, 0);
	$et->addAttributeType($att);
	$att = new AttributeType('sum',true, 0);
	$et->addAttributeType($att);
	$m0->addElementType($et);
	
	$et = new ElementType('sum');
	$att = new AttributeType('base',true, 0);
	$et->addAttributeType($att);
	$att = new AttributeType('ratio',true, 0);
	$et->addAttributeType($att);
	$att = new AttributeType('sum',true, 0);
	$et->addAttributeType($att);
	$m0->addElementType($et);
	
?>