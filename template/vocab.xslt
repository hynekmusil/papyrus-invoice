<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns="http://www.w3.org/1999/xhtml" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:g="http://formax.cz/impresso/general"
	xmlns:s="http://formax.cz/impresso/system"
	xmlns:i="http://formax.cz/impresso/invoice">
	
	<xsl:output encoding="utf-8"/>

	<xsl:template match="i:component[@type = 'Page']" mode="word">Strana</xsl:template>
	<xsl:template match="i:evidence" mode="word">číslo dokladu</xsl:template>
	<xsl:template match="i:evidence[lang('en')]" mode="word">evidence number</xsl:template>
	<xsl:template match="i:contract" mode="word">číslo smlouvy</xsl:template>
	<xsl:template match="i:contract[lang('en')]" mode="word">contract number</xsl:template>
	<xsl:template match="i:relatedDocs/i:order" mode="word">objednávka</xsl:template>
	<xsl:template match="i:order[lang('en')]" mode="word">order number</xsl:template>
	<xsl:template match="@role[. = 'contractor']" mode="word">Dodavatel</xsl:template>
	<xsl:template match="@role[. = 'acceptor']" mode="word">Příjemce</xsl:template>
	<xsl:template match="@role[. = 'customer']" mode="word">Odběratel</xsl:template>
	<xsl:template match="i:email" mode="word">email</xsl:template>
	<xsl:template match="i:phone[not(@type)]" mode="word">tel</xsl:template>
	<xsl:template match="i:mobile" mode="word">mob</xsl:template>
	<xsl:template match="i:phone[@type = 'fax']" mode="word">fax</xsl:template>
	<xsl:template match="@ic" mode="word">IČ</xsl:template>
	<xsl:template match="@dic" mode="word">DIČ</xsl:template>
	<xsl:template match="@cj" mode="word">čj.</xsl:template>
	<xsl:template match="i:bankAccount" mode="word">číslo účtu</xsl:template>
	<xsl:template match="i:due" mode="word">Den splatnosti</xsl:template>
	<xsl:template match="i:order" mode="word">Den odeslání</xsl:template>
	<xsl:template match="i:payment" mode="word">Forma úhrady</xsl:template>
	<xsl:template match="i:tax" mode="word">Den splnění</xsl:template>
	<xsl:template match="i:name" mode="word">Předmět dodávky</xsl:template>
	<xsl:template match="i:price/@VAT" mode="word">DPH</xsl:template>
	<xsl:template match="@quantity" mode="word">Počet MJ</xsl:template>
	<xsl:template match="i:price/i:one/@nett" mode="word">Cena MJ bez DPH</xsl:template>
	<xsl:template match="i:price/i:one/@gross" mode="word">Cena MJ s DPH</xsl:template>
	<xsl:template match="i:sum/@gross" mode="word">Celkem s DPH</xsl:template>
	<xsl:template match="i:summary" mode="word">Celkem</xsl:template>
	<xsl:template match="i:comodityList/@currencyCode" mode="word">
		<xsl:param name="byValue" select="false()"/>
		<xsl:choose>
			<xsl:when test="$byValue">
				<xsl:choose>
					<xsl:when test=". = 'CZK'">Kč</xsl:when>
					<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise>měna</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
