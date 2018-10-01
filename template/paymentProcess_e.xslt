<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns="http://www.w3.org/1999/xhtml" 
	xmlns:e="http://exslt.org/common" 
	xmlns:o="http://formax.cz/impresso/invoice"
	xmlns:s="http://formax.cz/impresso/system"
	exclude-result-prefixes="o e s">
	
	<xsl:import href="../system/template/_e.xslt"/>
	<xsl:import href="paymentProcess.xslt"/>
	
	<xsl:template match="/*">
		<xsl:choose>
			<xsl:when test="$onlyEditor = 'false'">
				<xsl:variable name="fragment">
					<xsl:apply-imports/>
				</xsl:variable>
				<!--div id="focusComponent"-->
					<!--xsl:apply-templates select="$fragment/*" mode="clickFragment"-->
					<xsl:apply-templates select="e:node-set($fragment)/*" mode="clickFragment">
						<xsl:with-param name="position">0</xsl:with-param>
						<xsl:with-param name="countElements" select="count(descendant-or-self::*)"/>
					</xsl:apply-templates>
				<!--/div-->
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="." mode="edit"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="o:paymentProcess" mode="edit">
		<xsl:if test="$onlyEditor != 'false'">
			<xsl:variable name="position" select="count(preceding::*) + count(ancestor::*)"/>
			<div id="editor" style="background-color: #fff;">
				<form action="" name="editor">
					<table cellspacing="0">
						<tbody><xsl:apply-templates mode="edit"/></tbody>
					</table>
				</form>
				<div>&#160;</div>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="o:paymentProcess/*" mode="edit">
		<tr>
			<th><xsl:apply-templates select="." mode="word"/></th>
			<xsl:apply-templates select="@*" mode="edit"/>
		</tr>
	</xsl:template>
	
	<xsl:template match="o:paymentProcess/*/@*" mode="edit">
		<td><xsl:apply-templates select="." mode="editValue"/></td>
	</xsl:template>
	
	<xsl:template match="@date" mode="editValue">
		<xsl:variable name="name"><xsl:apply-templates select="." mode="position"/></xsl:variable>
		<input>
			<xsl:attribute name="value"><xsl:call-template name="date"/></xsl:attribute>
			<xsl:attribute name="name"><xsl:value-of select="$name"/></xsl:attribute>
			<xsl:apply-templates select="." mode="editType"/>
		</input>
		<input type="button" value="vyber" onclick="displayDatePicker('{$name}', false, 'dmy', '.');"/>
	</xsl:template>	
	
</xsl:stylesheet>