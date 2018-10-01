<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns="http://www.w3.org/1999/xhtml" 
	xmlns:e="http://exslt.org/common" 
	xmlns:o="http://formax.cz/impresso/invoice"
	xmlns:s="http://formax.cz/impresso/system"
	exclude-result-prefixes="o e s">
	
	<xsl:import href="../system/template/_e.xslt"/>
	<xsl:import href="relatedDocs.xslt"/>
	
	<xsl:template match="/*">
		<xsl:choose>
			<xsl:when test="$onlyEditor = 'false'">
				<xsl:variable name="fragment">
					<xsl:apply-imports/>
				</xsl:variable>
				<div id="focusComponent">
					<xsl:apply-templates select="e:node-set($fragment)/*" mode="clickFragment">
						<xsl:with-param name="position">0</xsl:with-param>
						<xsl:with-param name="countElements" select="count(descendant-or-self::*)"/>
					</xsl:apply-templates>
				</div>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="." mode="edit"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="*" mode="edit">
		<xsl:if test="$onlyEditor != 'false'">
			<xsl:variable name="position" select="count(preceding::*) + count(ancestor::*)"/>
			<div id="editor" style="background-color: #fff;">
				<form action="" name="editor">
					<div class="relatedDocs">
						<xsl:apply-templates select="*" mode="labeledEditValue"/>
						<p class="clear"><xsl:comment>clear</xsl:comment></p>
					</div>
				</form>
				<div>&#160;</div>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="*" mode="labeledEditValue">
		<div>
			<xsl:if test="not(preceding-sibling::*)"><xsl:attribute name="class">first</xsl:attribute></xsl:if>
			<label><xsl:apply-templates select="." mode="word"/>: </label>
			<xsl:apply-templates select="@id" mode="editValue"/>
		</div>
	</xsl:template>
	
</xsl:stylesheet>