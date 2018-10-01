var waitForRefresh = 0;
if((p = window.location.href.indexOf('waitForRefresh')) != -1){
	waitForRefresh = Number(window.location.href.substring(p+15));
	//alert(waitForRefresh);
}

function resetInput(aO){
	if(aO.id== 'nett') {
		document.getElementById('gross').value = '';
		document.getElementById('gross').disabled = 'disabled';
	}
	else{
		document.getElementById('nett').value = '';
		document.getElementById('nett').disabled = 'disabled';
	}
}

function doRefresh(){
	if((wait = loadText('operator.php?doRefresh','')) != ''){
		window.location.href = 'index.php?waitForRefresh='+wait;
		waitForRefresh = wait - 1;
	}
}

var xmlHttp;
var currNode;

window.onload = function () {
	var node = document.getElementById('focus');
	var body = document.documentElement.getElementsByTagName("BODY")[0];
	var fragment= loadXML('list.php?list');
	var searchText = getURLParameter('searchText');
	fragment = document.importNode(fragment.documentElement,true);
	body.appendChild(fragment);
	if(searchText != null){
		document.getElementById("searchText").value = searchText;
		doSearch();
	}
	//moveEditor(node);
}

function doReload(){
	var searchText = document.getElementById("searchText").value;
	var link = 'index.php';
	if(searchText != ''){
		link = link + '?searchText=' + searchText;
	}
	window.location.href = link;
}

function openInv(aId){
	loadText('list.php?open&id='+aId);
	doReload();
	return true;
}

function saveInv(){
	loadText('list.php?save');
	alert('uloženo');
	doReload();
	return true;
}

function newInv(){
	loadText('list.php?new');
	alert('uloženo');
	doReload();
	return true;
}

function doSearch(){
	var searchText = document.getElementById("searchText").value;
	var result = loadText('search.php?text='+searchText);
	document.getElementById("searchResult").innerHTML = result;
	return true;
}

if (!document.ELEMENT_NODE) {
	document.ELEMENT_NODE = 1;
	document.ATTRIBUTE_NODE = 2;
	document.TEXT_NODE = 3;
	document.CDATA_SECTION_NODE = 4;
	document.ENTITY_REFERENCE_NODE = 5;
	document.ENTITY_NODE = 6;
	document.PROCESSING_INSTRUCTION_NODE = 7;
	document.COMMENT_NODE = 8;
	document.DOCUMENT_NODE = 9;
	document.DOCUMENT_TYPE_NODE = 10;
	document.DOCUMENT_FRAGMENT_NODE = 11;
	document.NOTATION_NODE = 12;
}

document._importNode = function(node, allChildren) {
	/* find the node type to import */
	switch (node.nodeType) {
		case document.ELEMENT_NODE:
			/* create a new element */
			var newNode = document.createElement(node.nodeName.toUpperCase());
			/* does the node have any attributes to add? */
			if (node.attributes && node.attributes.length > 0)
				/* add all of the attributes */
				for (var i = 0, il = node.attributes.length; i < il;)
					if(node.attributes[i].nodeName == 'class') newNode.setAttribute('className', node.getAttribute(node.attributes[i++].nodeName));
					else newNode.setAttribute(node.attributes[i].nodeName, node.getAttribute(node.attributes[i++].nodeName));
			/* are we going after children too, and does the node have any? */
			if (allChildren && node.childNodes && node.childNodes.length > 0)
				/* recursively get all of the child nodes */
				for (var i = 0, il = node.childNodes.length; i < il;){
					var p = newNode.appendChild(document._importNode(node.childNodes[i++], allChildren));
				}
			return newNode;
			break;
		case document.TEXT_NODE:
		case document.CDATA_SECTION_NODE:
		case document.COMMENT_NODE:
			return document.createTextNode(node.nodeValue);
			break;
	}
};
function diagnose(aInputNode, aEvent){
	var keynum;
	var keychar;
	var value = aInputNode.value;
	// IE
	if(window.event) keynum = aEvent.keyCode;
	// Netscape/Firefox/Opera
	else if(aEvent.which) keynum = aEvent.which;
	keychar = String.fromCharCode(keynum);
	if(keynum == '' || keynum == undefined || keynum == 8) return true;
	value += keychar;
	return value;
}
function GetXmlHttpObject(){
	var xmlHttp=null;
	try{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}catch (e){
		// Internet Explorer
		try{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}catch (e){
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}
function inputCheck(aType, aInputNode, aEvent){
	var value = diagnose(aInputNode, aEvent);
	if(value == true) return true;
	if(aType == 'integer') check = '^[0-9]+$';
	else if(aType == 'float') check = '^[0-9]+,*[0-9]*$';
	else check = aType;
	if (value.match(check)) return true;
	else return false;
}
function loadXML(aURI, aSend){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Your browser does not support AJAX!");
		return;
	}
	//xmlHttp.onreadystatechange=stateChanged;
	xmlHttp.open("POST",aURI,false);
	xmlHttp.send(aSend);
	return xmlHttp.responseXML;
}
function loadText(aURI, aSend){
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
		alert ("Your browser does not support AJAX!");
		return;
	}
	xmlHttp.open("POST",aURI,false);
	xmlHttp.send(aSend);
	return xmlHttp.responseText;
}
function moveEditor(aNode){
	var editor = document.getElementById('editor');
	var node = aNode;
	if(editor){
		var top = 0;
		var left = 0;
		var str = ''
		while (node != null) {
			top += node.offsetTop;
			left += node.offsetLeft;
			str += node.nodeName+" "+node.offsetLeft+" class="+node.className+"\n";
			node = node.offsetParent;
		}
		editor.style.position = 'absolute';			
		editor.style.top =  top+'px';
		if(aNode.className.indexOf('relatedDocs') != -1) left = 10;
		editor.style.left = left+'px';
		
	}
}
function throwEvent(aO){
	var query = 'operator.php?sc=subject&event=select&subjectId='+aO.value+'&state=selecting';
	var dom = loadXML(query);
	var editor = document.getElementById('editor');
	var newNode = dom.documentElement.cloneNode(true);
	if(document.importNode) document.importNode(newNode, true);
	else{
		newNode = document._importNode(newNode, true);
		//alert(newNode.innerHTML);
	}
	if(editor) editor.parentNode.replaceChild(newNode, editor);
	else document.getElementsByTagName('body').item(0).appendChild(newNode);
	moveEditor(document.getElementById('focus'));

}
function doFocus(aComponent, aPosition, aNode, aEvent){
	currNode = aNode;
	var position = viewHistory[aComponent][aPosition];
	var focus = document.getElementById('focus');
	if(focus){
		var currParam = focus.getAttribute('onclick').substring(9);
		var p = currParam.indexOf("'");
		currComponent = currParam.substring(0,p);
		currParam = currParam.substring(p+3);
		p = currParam.indexOf("'");
		currPosition = currParam.substring(0,p);
		currPosition = viewHistory[currComponent][currPosition];
		//alert(currPosition);
		if(!aEvent) updateFromForm(currComponent,currPosition);
		if((currComponent == 'subject_customer') && (aEvent != 'another')){
			window.location.href = 'index.php';
			return true;
		}
		while(focus){
			focus.removeAttribute('id');
			focus = document.getElementById('focus');
		}
	}
	var customerNode = getElementsByClassName('customer')[0]
	if(aNode == null) aNode = customerNode;
	aNode.id = 'focus';
	var query = '';
	if(aEvent) query = 'operator.php?sc=subject&event='+aEvent+'&state=selected';
	else query = 'operator.php?component='+aComponent+'&position='+position;
	var dom = loadXML(query);
	var editor = document.getElementById('editor');
	var newNode = dom.documentElement.cloneNode(true);
	if(document.importNode) document.importNode(newNode, true);
	else{
		newNode = document._importNode(newNode, true);
		//alert(newNode.innerHTML);
	}
	if(editor) editor.parentNode.replaceChild(newNode, editor);
	else document.getElementsByTagName('body').item(0).appendChild(newNode);
	moveEditor(aNode);
}

function addFollowing(aComponent, aPosition, aTypeName){
	//var position = viewHistory[aComponent][aPosition];
	var position = Number(aPosition);
	var query = 'operator.php?component='+aComponent+'&position='+position+'&addFollowing='+aTypeName;
	var node = document.getElementById('focus');
	var dom = loadXML(query);
	var newNode = dom.documentElement.cloneNode(true);
	if(document.importNode) document.importNode(newNode, true);
	else newNode = document._importNode(newNode, true);
	
	var focus = document.getElementById('focus');
	if(focus.nextSibling) newNode = focus.parentNode.insertBefore(newNode, focus.nextSibling);
	else newNode = node.parentNode.appendChild(newNode);
	
	var info = newNode.className;
	info = info.substring(info.indexOf('~') + 2);
	var newPosition = Number(info.substring(0,info.indexOf(' ')));
	var newCountElements = Number(info.substring(info.indexOf(' ')+1));
	var i=0;
	var last = viewHistory[aComponent].length;
	newNode.setAttribute('onclick', "doFocus('"+aComponent+"','"+last+"', this);");
	for (i=0; i < last ; i++) if(viewHistory[aComponent][i] >= newPosition) viewHistory[aComponent][i] += newCountElements;
	for (i=0; i < newCountElements; i++) viewHistory[aComponent][last+ i] = newPosition + i;
	//alert(dump(viewHistory[aComponent]));
	doFocus(aComponent, last, newNode);
	//alert(waitForRefresh);
	if(waitForRefresh > 0){
		waitForRefresh--;
		if(waitForRefresh == 0){
			window.location.href = 'index.php';
		}
	}
	doRefresh();
	//alert(waitForRefresh);
}
function remove(aComponent, aPosition){
	var position = Number(aPosition);
	var query = 'operator.php?component='+aComponent+'&position='+position+'&remove';
	loadText(query);
	var info = currNode.className;
	info = info.substring(info.indexOf('~') + 2);
	var removeHPosition = Number(info.substring(0,info.indexOf(' ')));
	var removeCountElements = Number(info.substring(info.indexOf(' ')+1));
	var last = viewHistory[aComponent].length;
	for (i=0; i < last ; i++){
		if(viewHistory[aComponent][i] == (position - removeCountElements)){
			n = i;
			fNode = currNode.previousSibling;
		}
		if((viewHistory[aComponent][i] >= position)&&(viewHistory[aComponent][i] < (position + removeCountElements)))  viewHistory[aComponent][i] = -1;
	}
	var n
	for (i=0; i < last ; i++){
		if(viewHistory[aComponent][i] == (position + removeCountElements)){
			n = i;
			fNode = currNode.nextSibling;
		}
		if(viewHistory[aComponent][i] >= (position + removeCountElements)) viewHistory[aComponent][i] -= removeCountElements;
	}
	//alert(fNode.className);
	currNode.parentNode.removeChild(currNode);
	doFocus(aComponent,n, fNode);
	doRefresh();
	//alert(dump(viewHistory[aComponent]));
}
function removeAll(aComponent){
	var query = 'operator.php?component='+aComponent+'&removeAll';
	loadText(query);
	var last = viewHistory[aComponent].length;
	var node = currNode.parentNode;
	while(node.childNodes.length > 2){
		node.removeChild(node.childNodes[node.childNodes.length - 1]);
	}
	for (i=0; i < last ; i++){
		if((viewHistory[aComponent][i] >= 6)) viewHistory[aComponent][i] = -1;
	}
	doFocus(aComponent,1, node.childNodes[1]);
	doRefresh();
}
function updateFromForm(aComponent, aPosition){
	var position = Number(aPosition);
	var send = '';
	var form = document.getElementsByTagName('form').item(0);
	if(form){
		for (var i=0; i < form.length; i++){
			if(form.elements[i].type != 'button' && form.elements[i].type != 'submit')
			send += form.elements[i].name + '~=' + form.elements[i].value + '~|';
		}
	}else if(chXML = document.getElementById('changedXML')){
		send = 'data/'+chXML.innerHTML+'.xml';
	}
	var query = 'operator.php?component='+aComponent+'&position='+position+'&update';
	var dom = loadXML(query,send);
	var newNode = dom.documentElement.cloneNode(true);
	if(document.importNode) document.importNode(newNode, true);
	else newNode = document._importNode(newNode, true);
	var focus = document.getElementById('focus');
	var oper = focus.getAttribute('onclick');
	newNode.setAttribute('onclick', oper);
	newnode =focus.parentNode.replaceChild(newNode, focus);
	newNode.id = 'focus';
}

function updateFromArray(aComponent, aPosition, aArray){
	var position = Number(aPosition);
	var send = '';
	for (name in aArray){
		send += name + '~=' + aArray[name] + '~|';
	}
	var query = 'operator.php?component='+aComponent+'&position='+position+'&update';
	var dom = loadXML(query,send);
}

/**
* Function : dump()
* Arguments: The data - array,hash(associative array),object
*    The level - OPTIONAL
* Returns  : The textual representation of the array.
* This function was inspired by the print_r function of PHP.
* This will accept some data as the argument and return a
* text that will be a more readable version of the
* array/hash/object that is given.
*/
function dump(arr,level) {
var dumped_text = "";
if(!level) level = 0;

//The padding given at the beginning of the line.
var level_padding = "";
for(var j=0;j<level+1;j++) level_padding += "    ";

if(typeof(arr) == 'object') { //Array/Hashes/Objects
 for(var item in arr) {
  var value = arr[item];
 
  if(typeof(value) == 'object') { //If it is an array,
   dumped_text += level_padding + "'" + item + "' ...\n";
   dumped_text += dump(value,level+1);
  } else {
   dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
  }
 }
} else { //Stings/Chars/Numbers etc.
 dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
}
return dumped_text;
}

function substringOf(aHaystack, aNeedle){
	var list = aHaystack.split(' ');
	var i = 0;
	while(i < list.length){
		if(list[i] == aNeedle) return i; 
		i++;
	}
	return -1;
}

function getElementsByClassName(aClassName, aNode) {
	var result = new Array();
	var node;
	if(aNode) node = aNode;
	else node = document.documentElement.getElementsByTagName("BODY")[0];
	var allNodes = node.getElementsByTagName("*");
	for (var n = 0; n < allNodes.length; n++) {
		if (substringOf(allNodes[n].className, aClassName) != -1) {
			result.push(allNodes[n]);
		}
	}
	return(result);
}

function getURLParameter(name) {
  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
}
