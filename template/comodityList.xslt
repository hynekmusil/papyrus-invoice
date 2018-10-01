<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:s="http://formax.cz/impresso/system"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:o="http://formax.cz/impresso/invoice"
	exclude-result-prefixes="s o">
	
	<xsl:import href="vocab.xslt"/>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
	
	<xsl:param name="pgRange">33..70</xsl:param>
	
	<xsl:variable name="firstRecord" select="number(substring-before($pgRange,'..'))"/>
	<xsl:variable name="lastRecord" select="substring-after($pgRange,'..')"/>
	
	<xsl:variable name="currencyCode">
		<xsl:apply-templates select="o:comodityList/@currencyCode" mode="word">
			<xsl:with-param name="byValue" select="true()"/>
		</xsl:apply-templates>
	</xsl:variable>
	
	<xsl:template match="o:comodityList">
		<table class="border comodity" cellspacing="0">
			<xsl:apply-templates select="o:comodity[1]" mode="header"/>
			<xsl:choose>
				<xsl:when test="$pgRange = '1..*'"><xsl:apply-templates select="o:comodity"/></xsl:when>
				<xsl:when test="$lastRecord = '*'"><xsl:apply-templates select="o:comodity[position() &gt;= $firstRecord]"/></xsl:when>
				<xsl:otherwise><xsl:apply-templates select="o:comodity[(position() &gt;= $firstRecord) and (position() &lt;= number($lastRecord))]"/></xsl:otherwise>
			</xsl:choose>
			<xsl:apply-templates select="o:summary"/>
		</table>
	</xsl:template>
		
	<xsl:template match="o:comodity" mode="header">
		<tr>
			<th><xsl:apply-templates select="o:name" mode="word"/></th>
			<th><xsl:apply-templates select="o:price/@VAT" mode="word"/></th>
			<th><xsl:apply-templates select="@quantity" mode="word"/></th>
			<th><xsl:apply-templates select="o:price/o:one/@nett" mode="word"/></th>
			<th class="last"><xsl:apply-templates select="o:price/o:sum/@gross" mode="word"/></th>
		</tr>
	</xsl:template>
	
	<xsl:template match="o:comodity">
		<tr>
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
	
	<xsl:template match="o:summary">
		<tr>
			<td class="empty">&#160;</td>
			<td class="empty">&#160;</td>
			<th class="sum">
				<xsl:apply-templates select="." mode="word"/>:
			</th>
			<td class="sum"><xsl:value-of select="translate(o:one/@nett,'.',',')"/></td>
			<td class="sum last">
				<xsl:value-of select="translate(o:one/@gross,'.',',')"/>
			</td>
		</tr>
	</xsl:template>
	
</xsl:stylesheet>
