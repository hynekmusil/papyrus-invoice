<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  
	xmlns:o="http://formax.cz/impresso/invoice">
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" exclude-result-prefixes="o"/>
	
	<xsl:template match="/">
		<xsl:apply-templates/>
	</xsl:template>
	
	<xsl:template match="*">
		<xsl:element name="{name()}">
			<xsl:apply-templates select="@*"/>
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>
	
	<xsl:template match="@*"><xsl:copy-of select="."/></xsl:template>
	
	<xsl:template match="o:name">
		<name><xsl:value-of select="count(../preceding-sibling::o:comodity) + 1"/></name>
	</xsl:template>
</xsl:stylesheet>
