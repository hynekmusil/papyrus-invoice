<?php //encoding=utf-8
	
	$root = new RootElementType('relatedDocs');
	$root->addXMLNS('http://formax.cz/impresso/invoice');
	$root->addXMLNS('http://formax.cz/impresso/system','s');
	$root->addSchemaLocation('../schema/invoice.xsd');
	$att = new AttributeType('xml:lang',true,'cs');
	$root->addAttributeType($att);
	$m0 = new Model('sequence');
	$root->setModel($m0);
	
	$et = new ElementType('evidence');
	$att = new AttributeType('id',true,1);
	$et->addAttributeType($att);
	$m0->addElementType($et);
	
	$et = new ElementType('contract');
	$att = new AttributeType('id',true,1);
	$et->addAttributeType($att);
	$m0->addElementType($et);
	
	$et = new ElementType('order');
	$att = new AttributeType('id',true,1);
	$et->addAttributeType($att);
	$m0->addElementType($et);
	
?>