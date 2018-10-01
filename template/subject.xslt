<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:s="http://formax.cz/impresso/system"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:o="http://formax.cz/impresso/invoice">
	<xsl:import href="vocab.xslt"/>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
	
	<xsl:template match="o:company">
		<xsl:apply-templates select="o:logo"/>
		<div class="title">
			<xsl:apply-templates select="../@role"/>
			<xsl:apply-templates select="o:person/o:name"/>
		</div>
		<div class="address">
			<xsl:apply-templates select="o:name"/>
			<xsl:apply-templates select="o:contact/o:address"/>
		</div>
		<table cellspacing="0">
			<xsl:apply-templates select="o:contact/o:email"/>
			<xsl:apply-templates select="o:contact/o:phone"/>
			<xsl:apply-templates select="o:contact/o:mobile"/>
		</table>
		<xsl:apply-templates select="o:identity"/>
		<xsl:apply-templates select="o:bankAccount"/>
	</xsl:template>
	<xsl:template match="o:address">
			<div>
				<xsl:apply-templates select="o:street"/>
				<xsl:apply-templates select="o:number"/>
			</div>
			<div class="zip">
				<xsl:apply-templates select="o:zip"/>
				<xsl:apply-templates select="o:city"/>
				<xsl:apply-templates select="o:cityPart"/>
			</div>
	</xsl:template>
	<xsl:template match="o:identity">
		<table><xsl:apply-templates select="@*" mode="tableRow"/></table>
	</xsl:template>
	<xsl:template match="@*" mode="tableRow">
		<tr>
			<th><xsl:apply-templates select="." mode="word"/>: </th>
			<td><xsl:value-of select="."/></td>
		</tr>
	</xsl:template>
	<xsl:template match="@dic[../@ic = '26131820']" mode="tableRow">
		<tr>
			<th><xsl:apply-templates select="." mode="word"/>: </th>
			<td>CZ26131820</td>
		</tr>
	</xsl:template>
	<xsl:template match="o:bankAccount">
		<table>
			<tr>
				<th><xsl:apply-templates select="." mode="word"/>: </th>
				<td><xsl:value-of select="."/>,&#160;&#160;<xsl:value-of select="@number"/>/<xsl:value-of select="@bankCode"/></td>
			</tr>
		</table>
	</xsl:template>
	<xsl:template match="o:address/*">&#160;<span><xsl:value-of select="."/></span></xsl:template>
	<xsl:template match="o:address/o:street"><span><xsl:value-of select="."/></span></xsl:template>
	<xsl:template match="o:address/o:zip"><span><xsl:value-of select="substring(.,1,3)"/>&#160;<xsl:value-of select="substring(.,4)"/></span></xsl:template>
	<xsl:template match="o:email">
		<tr>
			<th><xsl:apply-templates select="." mode="word"/>: </th>
			<td><xsl:value-of select="@address"/></td>
		</tr>
	</xsl:template>
	<xsl:template match="o:phone | o:mobile">
		<tr>
			<th><xsl:apply-templates select="." mode="word"/>: </th>
			<td>
				<xsl:apply-templates select="@countryCode"/>
				<xsl:apply-templates select="@number"/>
			</td>
		</tr>
	</xsl:template>
	<xsl:template match="@countryCode">(+<xsl:value-of select="."/>)</xsl:template>
	<xsl:template match="@number">
		<xsl:value-of select="substring(.,1,3)"/>&#160;<xsl:value-of select="substring(.,4,3)"/>&#160;<xsl:value-of select="substring(.,7)"/>
	</xsl:template>
	<xsl:template match="o:logo"><img class="right" src="{@src}" alt="{.}" /></xsl:template>
	<xsl:template match="@role"><label><xsl:apply-templates select="." mode="word"/>:<xsl:text> </xsl:text></label></xsl:template>
	<xsl:template match="o:company/o:name"><h3><xsl:value-of select="."/></h3></xsl:template>
	<xsl:template match="o:company/o:person/o:name">
		<strong>
			<xsl:for-each select="@*"><xsl:value-of select="."/><xsl:if test="position() != last()">&#160;</xsl:if></xsl:for-each>
		</strong>
	</xsl:template>
	
	<xsl:template match="o:subject">
		<div class="subject {@role}"><xsl:apply-templates select="o:company"/></div>
	</xsl:template>
	
</xsl:stylesheet>
