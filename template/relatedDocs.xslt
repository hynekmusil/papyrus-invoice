<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:s="http://formax.cz/impresso/system"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:o="http://formax.cz/impresso/invoice">
	<xsl:import href="vocab.xslt"/>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
	
	<xsl:template match="o:relatedDocs">
		<div class="relatedDocs">
			<xsl:apply-templates/>
			<p class="clear"></p>
		</div>
	</xsl:template>
	
	<xsl:template match="*">
		<div>
			<xsl:if test="not(preceding-sibling::*)"><xsl:attribute name="class">first</xsl:attribute></xsl:if>
			<label><xsl:apply-templates select="." mode="word"/>: </label>
			<strong><xsl:value-of select="@id"/></strong>
		</div>
	</xsl:template>

</xsl:stylesheet>
