<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="text" version="1.0" encoding="UTF-8" indent="yes" omit-xml-declaration="yes"/>
	<xsl:param name="id"/>
	
	<xsl:template match="/*">
		<xsl:text>componentHistory = new Array(); &#10;</xsl:text>
		<xsl:apply-templates select="." mode="item"/>
		<xsl:text>viewHistory['</xsl:text>
		<xsl:value-of select="$id"/>
		<xsl:text>'] = componentHistory; &#10;</xsl:text>
	</xsl:template>
	
	<xsl:template match="*" mode="item">
		<xsl:variable name="position" select="count(preceding::*) + count(ancestor::*)"/>
		<xsl:text>componentHistory[</xsl:text><xsl:value-of select="$position"/>
		<xsl:text>] = </xsl:text><xsl:value-of select="$position"/>
		<xsl:text>;&#10;</xsl:text>
		<xsl:apply-templates mode="item"/>
	</xsl:template>
	
	<xsl:template match="text()" mode="item"/>
</xsl:stylesheet>
