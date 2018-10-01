<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml" xmlns:e="http://exslt.org/common" xmlns:xd="http://www.pnp-software.com/XSLTdoc">
	<!--pozice editovaneho elementu - uplatnuje se pri transformaci kompletniho xmlka a v soucinosti s parametrem onlyEditor = true -->
		<xd:doc type="stylesheet">
			<xd:author>ibirrer</xd:author>
			<xd:copyright>P&amp;P Software, 2007</xd:copyright>
			<xd:cvsId>$Id: XSLTdocConfig.xml 38 2007-12-14 17:12:04Z ibirrer $</xd:cvsId>
		</xd:doc>
	<xsl:param name="pe">1</xsl:param>
	<!--identifikace dat (nazev xmlka) napr: subject_letov-->
	<xsl:param name="id"/>
	<!--Ma byt vystupem jen formular pro editaci dat elementu na pozici pe? -->
	<xsl:param name="onlyEditor">false</xsl:param>
	<!--Pozice elementu upravovanych dat - uplatnuje se pri transformaci fragmentu xml-->
	<xsl:param name="dataPosition">false</xsl:param>
	
	<xsl:template match="/*">
		<xsl:choose>
			<!-- Provadi se pri component->show() -->
			<xsl:when test="$onlyEditor = 'false' and $dataPosition = 'false'">
					<xsl:apply-imports/>
			</xsl:when>
			<!-- Provadi se pri component->showFragment() -->
			<xsl:when test="($dataPosition != 'false')">
				<xsl:variable name="fragment">
					<xsl:apply-imports/>
				</xsl:variable>
				<!--xsl:apply-templates select="$fragment/*" mode="clickFragment"-->
				<xsl:apply-templates select="e:node-set($fragment)/*" mode="clickFragment">
					<xsl:with-param name="position" select="$dataPosition"/>
					<xsl:with-param name="countElements" select="count(descendant-or-self::*)"/>
				</xsl:apply-templates>
			</xsl:when>
			<!-- Provadi se pri component->showEdit() -->
			<xsl:otherwise>
				<xsl:apply-templates select="(//*)[count(preceding::*) + count(ancestor::*) = $pe]" mode="edit"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	
	
	<xsl:template match="* | @*" mode="editValue">
		<input value="{.}">
			<xsl:attribute name="name"><xsl:apply-templates select="." mode="position"/></xsl:attribute>
			<xsl:apply-templates select="." mode="editType"/>
		</input>
	</xsl:template>
	
	<xsl:template match="* | @*" mode="editType"/>
	
	<xsl:template match="*" mode="position">
		<xsl:value-of select="count(preceding::*) + count(ancestor::*)"/>
	</xsl:template>
	
	<xsl:template match="@*" mode="position">
		<xsl:apply-templates select=".." mode="position"/>_<xsl:value-of select="name()"/>
	</xsl:template>
	
	<xsl:template match="*">
		<xsl:variable name="fragment">
			<xsl:apply-imports/>
		</xsl:variable>
		<xsl:variable name="position" select="count(preceding::*) + count(ancestor::*)"/>
		<xsl:apply-templates select="e:node-set($fragment)/*" mode="clickFragment">
		<!--xsl:apply-templates select="$fragment/*" mode="clickFragment"-->
			<xsl:with-param name="position" select="$position"/>
			<xsl:with-param name="countElements" select="count(descendant-or-self::*)"/>
		</xsl:apply-templates>
	</xsl:template>
	
	<xsl:template match="*" mode="clickFragment">
		<xsl:param name="position"/>
		<xsl:param name="countElements"/>
		<xsl:param name="event"/>
		<xsl:variable name="addInfo" select="concat('~ ',$position,' ',$countElements)"/>
		<xsl:element name="{name()}">
			<xsl:attribute name="onclick">doFocus('<xsl:value-of select="$id"/>','<xsl:value-of select="$position"/>
				<xsl:text>', this</xsl:text>
				<xsl:if test="$event != ''">,'<xsl:value-of select="$event"/>'</xsl:if>
				<xsl:text>);</xsl:text>
			</xsl:attribute>
			<xsl:choose>
				<xsl:when test="$countElements = ''"><xsl:copy-of select="@*"/></xsl:when>
				<xsl:otherwise>
					<xsl:attribute name="class">
						<xsl:choose>
							<xsl:when test="@class"><xsl:value-of select="concat(@class,' ',$addInfo)"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="$addInfo"/></xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
					<xsl:copy-of select="@*[local-name() != 'class']"/>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:copy-of select="*"/>
		</xsl:element>
	</xsl:template>

</xsl:stylesheet>
