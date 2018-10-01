<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:s="http://formax.cz/impresso/system"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:o="http://formax.cz/impresso/invoice">
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
	
	<xsl:template match="o:page">
		<div class="content">
			<xsl:apply-templates select="o:component[@type = 'RelatedDocs']"/>
			<xsl:if test="o:component[@s:id = 'contractor']">
				<div class="left column">
					<xsl:apply-templates select="o:component[2]"/>
					<xsl:apply-templates select="o:component[3]"/>
				</div>
				<div class="right column">
					<xsl:apply-templates select="o:component[4]"/>
					<xsl:apply-templates select="o:component[@type = 'PaymentProcess']"/>
				</div>
				<p class="clear"><xsl:comment>float cleaner</xsl:comment></p>
			</xsl:if>
			<xsl:apply-templates select="o:component[@type = 'ComodityList']"/>
			<xsl:apply-templates select="o:component[@type = 'Summary']"/>
		</div>
	</xsl:template>
	
	<xsl:template match="o:component">
		<o:component><xsl:copy-of select="* | @*"/></o:component>
	</xsl:template>
	
</xsl:stylesheet>
