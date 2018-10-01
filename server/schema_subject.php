<?php //encoding=utf-8
	
	$root = new RootElementType('subject');
	$root->addXMLNS('http://formax.cz/impresso/invoice');
	$root->addXMLNS('http://formax.cz/impresso/system','s');
	$root->addSchemaLocation('../schema/invoice.xsd');
	$att = new AttributeType('id',false);
	$root->addAttributeType($att);
	$att = new AttributeType('ref',false);
	$root->addAttributeType($att);
	$att = new AttributeType('role',true,'customer');
	$root->addAttributeType($att);
	$att = new AttributeType('xml:lang',true,'cs');
	$root->addAttributeType($att);
	$m0 = new Model('choice');
	$m0->minOcc = 0;
	$root->setModel($m0);
	
	$et = new ElementType('company');
	$m0->addElementType($et);
	$m1 = new Model('sequence');
	$et->setModel($m1);
	
	$et = new ElementType('name');
	$m1->addElementType($et);
	
	$et = new ElementType('identity');
	$et->minOcc = 0;
	$att = new AttributeType('ic',true);
	$et->addAttributeType($att);
	$att = new AttributeType('dic',false);
	$et->addAttributeType($att);
	$att = new AttributeType('cj',false);
	$et->addAttributeType($att);
	$m1->addElementType($et);
	
	$et = new ElementType('logo');
	$et->minOcc = 0;
	$att = new AttributeType('src',true);
	$et->addAttributeType($att);
	$m1->addElementType($et);
	
	$contact = new ElementType('contact');
	$contact->minOcc = 0;
	$m1->addElementType($contact);
	$m2 = new Model('sequence');
	$contact->setModel($m2);
	
	$et = new ElementType('address');
	$et->maxOcc = 'unbounded';
	$att = new AttributeType('type',false);
	$et->addAttributeType($att);
	$m2->addElementType($et);
	$m3 = new Model('sequence');
	$et->setModel($m3);
	
	$et = new ElementType('country');
	$att = new AttributeType('code',true,'CZ');
	$et->addAttributeType($att);
	$m3->addElementType($et);
	
	$et = new ElementType('city');
	$m3->addElementType($et);
	
	$et = new ElementType('cityPart');
	$et->minOcc = 0;
	$m3->addElementType($et);
	
	$et = new ElementType('street');
	$et->minOcc = 0;
	$m3->addElementType($et);
	
	$et = new ElementType('number');
	$m3->addElementType($et);
	
	$et = new ElementType('orientationNumber');
	$et->minOcc = 0;
	$m3->addElementType($et);
	
	$et = new ElementType('zip');
	$m3->addElementType($et);
	
	$et = new ElementType('phone');
	$et->maxOcc = 'unbounded';
	$et->minOcc = 0;
	$att = new AttributeType('type',false);
	$et->addAttributeType($att);
	$att = new AttributeType('cuntryCode',false,'+420');
	$et->addAttributeType($att);
	$att = new AttributeType('number',true);
	$et->addAttributeType($att);
	$m2->addElementType($et);
	
	$et = new ElementType('email');
	$et->maxOcc = 'unbounded';
	$et->minOcc = 0;
	$att = new AttributeType('type',false);
	$et->addAttributeType($att);
	$att = new AttributeType('address',true);
	$et->addAttributeType($att);
	$m2->addElementType($et);
	
	$bankAccount = new ElementType('bankAccount');
	$bankAccount->minOcc = 0;
	$att = new AttributeType('number',true);
	$bankAccount->addAttributeType($att);
	$att = new AttributeType('bankCode',true);
	$bankAccount->addAttributeType($att);
	$m1->addElementType($bankAccount);
	
	$person = new ElementType('person');
	$m0->addElementType($person);
	$m1 = new Model('sequence');
	$person->setModel($m1);
	
	$et = new ElementType('name');
	$att = new AttributeType('apostrophe',false, 'p.');
	$et->addAttributeType($att);
	$att = new AttributeType('titleBefore',false, 'Ing.');
	$et->addAttributeType($att);
	$att = new AttributeType('first',false);
	$et->addAttributeType($att);
	$att = new AttributeType('surname',true);
	$et->addAttributeType($att);
	$att = new AttributeType('titleAfter',false, 'CSc.');
	$et->addAttributeType($att);
	$m1->addElementType($et);
	
	$et = clone $contact;
	$m1->addElementType($et);
	
	$et = clone $bankAccount;
	$m1->addElementType($et);
?>