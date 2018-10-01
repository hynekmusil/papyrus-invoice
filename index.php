<?php //encoding=utf-8
	//header('Content-Type: text/xml; charset=utf-8');
	require_once 'system/server/XMLManager.php';
	require_once 'server/schema_summary.php';
	require_once 'paging.php';
	
	$summaryManager = new ElementManager($root);
	require_once 'server/schema_comodityList.php';
	
	$comodityListManager = new ElementManager($root);
	if(isset($_REQUEST['new'])) file_put_contents('data/comodityList.xml',initiate($comodityListManager));
	else {
		$comodityListManager->fill('data/comodityList.xml');
	}
	
	$summary = array();
	$comodityCount = 0;
	foreach($comodityListManager->elementList as $element){
		if($element->name == 'comodity'){
			$priceP = $element->children[1];
			$priceE = $comodityListManager->elementList[$priceP - 1];
			$vat = $priceE->attributeList['VAT']->value;
			$sumE = $comodityListManager->elementList[end($priceE->children) - 1];
			$sum = $sumE->attributeList['gross']->value;
			@$summary[$vat] += floatval(str_replace(",",".",$sum));
			$comodityCount ++;
		}
	}
	$i = 0;
	while($rateE = $summaryManager->elementList[$i]){
		if($rateE->name == 'rate') {
			break;
		}else $i++;
	}

	$sumSum = floatval(reset($summary));
	$rateE->attributeList['sum']->value = $sumSum;
	$firstVat = key($summary);
	$rateE->attributeList['percentage']->value = $firstVat;
	$sumBase = ($sumSum * floatval(str_replace(",",".",$firstVat)))/(floatval(str_replace(",",".",$firstVat)) + 100);
	$rateE->attributeList['base']->value = $sumBase;
	$sumRatio = $sumSum - $sumBase;
	$rateE->attributeList['ratio']->value = $sumRatio;
	
	$p = $rateE->position;
	foreach($summary as $vat=>$sum){
		if($vat != $firstVat){
			$el = $summaryManager->addFollowing($p,'rate');
			$p = $el[0]->position;
			$summaryManager->elementList[$p -1]->attributeList['percentage']->value = $vat;
			$summaryManager->elementList[$p -1]->attributeList['sum']->value = $sum;
			$base = (floatval(str_replace(",",".",$sum)) * floatval(str_replace(",",".",$vat)))/(floatval(str_replace(",",".",$vat)) + 100);
			$summaryManager->elementList[$p -1]->attributeList['base']->value = $base;
			$summaryManager->elementList[$p -1]->attributeList['ratio']->value = $sum - $base;
			$sumBase += $base;
			$sumRatio += $sum - $base;
			$sumSum += $sum;
		}
	}
	$summaryManager->elementList[$p]->attributeList['ratio']->value = $sumRatio;
	$summaryManager->elementList[$p]->attributeList['sum']->value = $sumSum;
	$summaryManager->elementList[$p]->attributeList['base']->value = $sumBase;
	file_put_contents('data/summary.xml',$summaryManager->getXML());
	
	show();

?>