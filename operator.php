<?php //encoding=utf-8
	require_once 'paging.php';
	if(isset($_REQUEST['sc'])){
		header('Content-Type: text/xml; charset=utf-8');
		require_once 'system/server/StateMachine.php';
		require_once "server/state_{$_REQUEST['sc']}.php";
		if(isset($_REQUEST['dbg'])){
			$script = "<script type=\"text/javascript\">
			function throwEvent(aO){
				document.location.href = '?sc=subject&event=select&subjectId='+aO.value+'&state='+document.getElementById('currentState').innerHTML;
			}
		</script>";
			echo $script;
		}
		if(isset($_REQUEST['state'])) $sc->currentState = $_GET['state'];
		if(isset($_REQUEST['event'])){
			$event = new Event($_REQUEST['event']);
			foreach($_REQUEST as $k=>$v) {
				if($k != 'state'){
					if($k != 'event'){
						$data = new Data($k,$v);
						$event->addData($data);
					}
				}
			}
			$sc->process($event);
		} else $sc->process();
		echo $sc->result;
		if(isset($_REQUEST['dbg'])){
			echo $sc->currentState;
		}
		//if($sc->currentState == 'selected') echo "<a href=\"?sc=subject&event=another&state=selected\">Vybrat jiný</a>";
		exit;
	}
	$compute = '';
	require_once 'system/server/XMLManager.php';
	require_once 'system/server/componentComposer.php';
	
	$cType = '';
	$cId = '';
	
	if(isset($_REQUEST['component'])){
		$cType = $_REQUEST['component'];
		$cId = 0;
		if(($p = strpos($_REQUEST['component'],'_')) !== false){
			$cType = substr($_REQUEST['component'],0,$p);
			$cId = substr($_REQUEST['component'],$p+1);
		}
	}
	elseif(isset($_REQUEST['doRefresh'])){
		echo refreshPageCount();
		$viewDoc->save($viewFileName);
		exit;
	} else exit;
	
	header('Content-Type: text/xml; charset=utf-8');
	
	$component = new Component($appDir);
	
	$component->setCType($cType);
	$component->setCId($cId);
	
	require_once "server/schema_{$cType}.php";
	
	$cType = strtoupper($cType[0]).substr($cType,1);
	if(isset($templateList[$cType])){
		$component->setTemplateId($templateList[$cType]);
	}
	
	$cFullId = $component->getId();
	
	$manager = new ElementManager($root);
	if(isset($_REQUEST['component'])) $component->getDataDoc();
	
	
	if(isset($_REQUEST['new'])){
		file_put_contents("data/$cFullId.xml",initiate($manager));
	} 
	elseif($_REQUEST['component'] != 'subject_customer') {
		$manager->fill("data/$cFullId.xml");
	}
	
	if(isset($_REQUEST['addFollowing'])) {
		if(isset($adder[$_REQUEST['addFollowing']]))
			$el = $manager->addFollowing($_REQUEST['position'] +1,$_REQUEST['addFollowing'], $adder[$_REQUEST['addFollowing']]);
		else $el = $manager->addFollowing($_REQUEST['position'] +1,$_REQUEST['addFollowing']);
		$content = $manager->getXML();
		$fragment = $manager->getXML($el);
		echo $component->showFragment($fragment, $el[0]->position);
		file_put_contents("data/$cFullId.xml",$content);
	} 
	elseif(isset($_REQUEST['update'])){
		$data = $GLOBALS['HTTP_RAW_POST_DATA'];
		if($data === ''){
			echo $component->show()->saveXML();
		}
		elseif(strpos($data,'data/') === 0){
			//echo "dobre";
			$content = file_get_contents($data);
			file_put_contents("data/$cFullId.xml",$content);
			$component->getDataDoc();
			echo $component->show()->saveXML();
		}else{
			$values = explode('~|',$data);
			foreach($values as $value){
				if($value != '') {
					$attName = '';
					$dato = explode('~=',$value);
					if(($p = strpos($dato[0],'_')) !== false){
						$position = substr($dato[0],0,$p);
						$attName = substr($dato[0],$p+1);
					} else $position = $dato[0];
					$text = $dato[1];
					if($attName == '') {
						$update[$position] = $text;
					}
					else{
						$update[$position][$attName] = $text;
					}
				}
			}
			if($compute !='') $compute($update);
			foreach($update as $position => $v){
				if(is_array($v)){
					foreach($v as $attName => $attValue){
						$att = new Attribute($attName, $attValue);
						$manager->elementList[$position]->addAttribute($att);
					}
				} else $manager->elementList[$position]->text = $v;
			}
			$content = $manager->getXML();
			$el = array();
			$el = $manager->getElementWithAllChild($_REQUEST['position'] +1);
			$fragment = $manager->getXML($el);
			echo $component->showFragment($fragment, reset($el)->position);
			file_put_contents("data/$cFullId.xml",$content);
		}
	} 
	elseif(isset($_REQUEST['remove'])){
		$manager->remove($_REQUEST['position'] +1);
		$content = $manager->getXML();
		file_put_contents("data/$cFullId.xml",$content);
	}
	elseif(isset($_REQUEST['removeAll'])){
		$content = file_get_contents("data/{$cFullId}_new.xml",$content);
		file_put_contents("data/$cFullId.xml",$content);
	}
	else {
		echo $component->showEdit($_REQUEST['position']);
	}

?>