<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:s="http://formax.cz/impresso/system"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:o="http://formax.cz/impresso/invoice">
	<xsl:import href="vocab.xslt"/>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
	
	<xsl:template match="o:paymentProcess">
		<table class="PaymentProcess" cellspacing="0">
			<tbody><xsl:apply-templates/></tbody>
		</table>
	</xsl:template>
	
	<xsl:template match="o:paymentProcess/*">
		<tr>
			<th><xsl:apply-templates select="." mode="word"/></th>
			<xsl:apply-templates select="@*"/>
		</tr>
	</xsl:template>
	
	<xsl:template match="o:paymentProcess/*/@date">
		<td><xsl:call-template name="date"/></td>
	</xsl:template>
	
	<xsl:template name="date">
		<xsl:variable name="year" select="substring-before(.,'-')"/>
		<xsl:variable name="monthZ" select="substring-before(substring-after(.,'-'),'-')"/>
		<xsl:variable name="month">
			<xsl:call-template name="withoutLeadingZero"><xsl:with-param name="value" select="$monthZ"/></xsl:call-template>
		</xsl:variable>
		<xsl:variable name="dayZ" select="substring-after(.,concat('-',$monthZ,'-'))"/>
		<xsl:variable name="day">
			<xsl:call-template name="withoutLeadingZero"><xsl:with-param name="value" select="$dayZ"/></xsl:call-template>
		</xsl:variable>
		<xsl:value-of select="concat($day,'.',$month,'.',$year)"/>
	</xsl:template>
	
	<xsl:template name="withoutLeadingZero">
		<xsl:param name="value"/>
		<xsl:choose>
			<xsl:when test="starts-with($value,0)"><xsl:value-of select="substring-after($value,'0')"/></xsl:when>
			<xsl:otherwise><xsl:value-of select="$value"/></xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="o:paymentProcess/*/@type">
		<td><xsl:value-of select="."/></td>
	</xsl:template>
</xsl:stylesheet>
