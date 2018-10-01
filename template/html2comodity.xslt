<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
	
	<xsl:template match="table">
		<comodityList>
			<xsl:apply-templates select="tr"/>
		</comodityList>	
	</xsl:template>
	
	<xsl:template match="tr">
		<comodity quantity="{td[3]}">
			<xsl:apply-templates select="td[1]"/>
			<xsl:apply-templates select="td[2]"/>
		</comodity>
	</xsl:template>
	
	<xsl:template match="td[1]">
		<name><xsl:value-of select="."/></name>
	</xsl:template>
	
	<xsl:template match="td[2]">
		<currency VAT="{substring-before(.,'%')}">
			<price value="{translate(../td[4],',','.')}"/>
		</currency>
	</xsl:template>
		
</xsl:stylesheet>
