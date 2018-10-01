<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns="http://www.w3.org/1999/xhtml" 
	xmlns:o="http://formax.cz/impresso/invoice"
	exclude-result-prefixes="o">
	
	<xsl:import href="subject.xslt"/>
	<xsl:param name="id"/>
	
	<xsl:template match="o:subject">
		<div id="editor" class="subject {@role}">
			<xsl:apply-templates select="o:company"/>
			<br/>
			<span id="changedXML" class="subject_customer"><xsl:value-of select="$id"/></span>
			<a href="" onclick="doFocus('subject_customer','0',null,'another'); return false;">vyber jinou</a>
		</div>
	</xsl:template>
	
</xsl:stylesheet>