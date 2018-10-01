<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns="http://www.w3.org/1999/xhtml" 
	xmlns:e="http://exslt.org/common" 
	xmlns:o="http://formax.cz/impresso/invoice"
	xmlns:s="http://formax.cz/impresso/system"
	exclude-result-prefixes="o e s">
	
	<xsl:import href="view.xslt"/>
	<!--xsl:import href="../../system/template/_e.xslt"/-->

	<xsl:template match="o:view" mode="script">
		<link rel="stylesheet" type="text/css" href="system/style/datePicker.css" />
		<script type="text/javascript" src="client/controller.js?version=3"><xsl:comment>script</xsl:comment></script>
		<script type="text/javascript" src="client/history.js"><xsl:comment>script</xsl:comment></script>
		<script type="text/javascript" src="system/client/datePicker.js"><xsl:comment>script</xsl:comment></script>
	</xsl:template>
	
</xsl:stylesheet>