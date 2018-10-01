<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xs="http://www.w3.org/2001/XMLSchema">
	<xsl:import href="../template/vocab.xslt"/>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes" omit-xml-declaration="yes"/>
	<xsl:param name="component">comodityList</xsl:param>
	
	<xsl:template match="xs:schema">
		<xsl:if test="xs:element[@name = $component]">
			<xsl:apply-templates select="xs:element[@name = $component]" mode="component"/>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="xs:element" mode="component">
		<xsl:apply-templates select="xs:complexType">
			<xsl:with-param name="id" select="1"/>
		</xsl:apply-templates>
	</xsl:template>
	
	<xsl:template match="xs:element" mode="array">
		<xsl:param name="id"/>
		<xsl:text>'</xsl:text>
		<xsl:value-of select="$id"/>
		<xsl:text>'=&gt;'xxx'</xsl:text>
	</xsl:template>
	
	<xsl:template match="xs:element">
		<xsl:text>&#x09;private $</xsl:text><xsl:value-of select="@name"/>
		<xsl:if test="@maxOccurs"> = array()</xsl:if>
		<xsl:text>;&#10;</xsl:text>
	</xsl:template>
	
	<xsl:template match="xs:complexType">
		<xsl:param name="id"/>
		<xsl:apply-templates select="xs:complexContent">
			<xsl:with-param name="id" select="$id"/>
		</xsl:apply-templates>
	</xsl:template>
	
	<xsl:template match="xs:complexType[xs:sequence | xs:choice]">
		<xsl:param name="id"/>
		<xsl:text>&#x09;private $elementList = array(</xsl:text>
		<xsl:apply-templates select="*/xs:element[not(@minOccurs = 0)]" mode="array">
			<xsl:with-param name="id" select="$id"/>
		</xsl:apply-templates>
		<xsl:text>);&#10;</xsl:text>
		<xsl:text>}</xsl:text>
	</xsl:template>
	
	<xsl:template match="xs:complexContent">
		<xsl:param name="id"/>
		<xsl:apply-templates select="xs:extension">
			<xsl:with-param name="id" select="$id"/>
		</xsl:apply-templates>
	</xsl:template>
	
	<xsl:template match="xs:element//xs:extension">
		<xsl:param name="id"/>
		<xsl:text>class </xsl:text>
		<xsl:value-of select="@base"/>
		<xsl:text>{&#10;</xsl:text>
		<xsl:text>&#x09;private $attributeList = array(</xsl:text>
		<xsl:apply-templates select="xs:attribute">
			<xsl:with-param name="id" select="$id"/>
		</xsl:apply-templates>
		<xsl:call-template name="findType"/>
	</xsl:template>
	
	<xsl:template match="xs:attribute">
		<xsl:param name="id"/>
		<xsl:text>'</xsl:text>
		<xsl:value-of select="$id"/>
		<xsl:text>_</xsl:text>
		<xsl:value-of select="@ref"/><xsl:value-of select="@name"/>
		<xsl:text>',</xsl:text>
	</xsl:template>
	
	<xsl:template name="findType">
		<xsl:param name="xpath" select="/xs:schema"/>
		<xsl:param name="base" select="@base"/>
		<xsl:choose>
			<xsl:when test="$xpath/xs:complexType/@name = $base">
				<xsl:apply-templates select="$xpath/xs:complexType[@name = $base]"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:for-each select="/xs:schema/xs:redefine">
					<xsl:call-template name="findType">
						<xsl:with-param name="xpath" select="document(@schemaLocation)/xs:schema"/>
						<xsl:with-param name="base" select="$base"/>
					</xsl:call-template>
				</xsl:for-each>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>
