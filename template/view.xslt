<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:s="http://formax.cz/impresso/system"
	xmlns:o="http://formax.cz/impresso/invoice">
	<xsl:import href="vocab.xslt"/>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" 
	doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" 
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>
	
	<xsl:param name="focused">false</xsl:param>
	
	<xsl:template match="o:view">
		<html>
			<head>
				<title><xsl:value-of select="@title"/></title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<link rel="stylesheet" type="text/css" href="style/screen.css" />
				<link rel="stylesheet" type="text/css" media="print" href="style/print.css" />
				<xsl:apply-templates select="." mode="script"/>
			</head>
			<body>
				<div class="container">
					<xsl:apply-templates/>
				</div>
			</body>
		</html>
	</xsl:template>
	
	<xsl:template match="o:view" mode="script"/>
	
	<xsl:template match="o:component">
		<div class="page">
			<div class="header">
				<div class="title"><xsl:value-of select="/o:view/@title"/></div>
				<div class="info">
					<label><xsl:apply-templates select="." mode="word"/>:</label>
					<span><xsl:value-of select="count(preceding-sibling::o:component) + 1"/></span>
				</div>
				<hr/>
			</div>
			<o:component><xsl:copy-of select="* | @*"/></o:component>
		</div>
		<xsl:if test="following-sibling::o:component"><hr class="clear pagebreak" /></xsl:if>
	</xsl:template>
</xsl:stylesheet>
