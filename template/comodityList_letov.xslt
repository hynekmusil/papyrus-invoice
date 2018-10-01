<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:s="http://formax.cz/impresso/system"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:o="http://formax.cz/impresso/invoice"
	exclude-result-prefixes="s o">
	
	<xsl:import href="comodityList.xslt"/>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
		
	<xsl:template match="o:comodity" mode="header">
		<tr>
			<th>pořadí</th>
			<th>položka</th>
			<th><xsl:apply-templates select="o:name" mode="word"/></th>
			<th><xsl:apply-templates select="o:price/@VAT" mode="word"/></th>
			<th><xsl:apply-templates select="@quantity" mode="word"/></th>
			<th><xsl:apply-templates select="o:price/o:one/@nett" mode="word"/></th>
			<th class="last"><xsl:apply-templates select="o:price/o:sum/@gross" mode="word"/></th>
		</tr>
	</xsl:template>
	
	<xsl:template match="o:comodity">
		<tr>
			<td><xsl:value-of select="count(preceding-sibling::o:comodity) + 1"/></td>
			<td><xsl:value-of select="@id"/>&#160;</td>
			<td><xsl:value-of select="o:name"/>&#160;</td>
			<td><xsl:value-of select="o:price/@VAT"/></td>
			<td><xsl:value-of select="@quantity"/></td>
			<td><xsl:value-of select="translate(round(translate(o:price/o:one/@nett,',','.') * 100) div 100,'.',',')"/></td>
			<td class="last">
				<xsl:value-of select="translate(round(translate(o:price/o:sum/@gross,',','.') * 100) div 100,'.',',')"/>
				<xsl:value-of select="concat(' ',$currencyCode)"/>
			</td>
		</tr>
	</xsl:template>
	
</xsl:stylesheet>
