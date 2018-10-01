<?php //encoding=utf-8
	
	$root = new RootElementType('view');
	$root->addXMLNS('http://formax.cz/impresso/invoice');
	$root->addXMLNS('http://formax.cz/impresso/system','s');
	$root->addSchemaLocation('../schema/invoice.xsd');
	$att = new AttributeType('xml:lang',true,'cs');
	$root->addAttributeType($att);
	
?>