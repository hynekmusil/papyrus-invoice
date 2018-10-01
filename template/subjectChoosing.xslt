<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:s="http://formax.cz/impresso/system"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:o="http://formax.cz/impresso/general">
	<xsl:import href="vocab.xslt"/>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
	
	<xsl:template match="o:subjectChoosing">
		<div id="{@role}" class="subject">
			<div class="title">
				<xsl:apply-templates select="@role"/>
				<select name="subjectName">
					<xsl:apply-templates select="o:subjectName"/>
				</select>
			</div>
		</div>
	</xsl:template>
	
	<xsl:template match="@role"><label><xsl:apply-templates select="." mode="word"/>:<xsl:text> </xsl:text></label></xsl:template>
	
	<xsl:template match="o:subjectName">
		<option value="{s:id}"><xsl:value-of select="."/></option>
	</xsl:template>
</xsl:stylesheet>
