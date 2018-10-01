<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns="http://www.w3.org/1999/xhtml" 
	xmlns:e="http://exslt.org/common" 
	xmlns:o="http://formax.cz/impresso/invoice"
	xmlns:s="http://formax.cz/impresso/system"
	exclude-result-prefixes="o e s">
	
	<xsl:import href="comodityList.xslt"/>
	<xsl:import href="../../system/template/_e.xslt"/>

	<xsl:template match="o:comodity" mode="edit">
		<xsl:if test="$onlyEditor != 'false'">
			<xsl:variable name="position" select="count(preceding::*) + count(ancestor::*)"/>
			<div id="editor" style="background-color: #fff;">
				<form action="" name="editor">
					<table>
						<thead>
						<tr>
							<th><xsl:apply-templates select="o:name" mode="word"/></th>
							<th><xsl:apply-templates select="o:price/@VAT" mode="word"/></th>
							<th><xsl:apply-templates select="@quantity" mode="word"/></th>
							<th><xsl:apply-templates select="o:price/o:one/@nett" mode="word"/></th>
							<th class="last"><xsl:apply-templates select="o:price/o:one/@gross" mode="word"/></th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td><xsl:apply-templates select="o:name" mode="editValue"/></td>
							<td><xsl:apply-templates select="o:price/@VAT" mode="editValue"/></td>
							<td><xsl:apply-templates select="@quantity" mode="editValue"/></td>
							<td><xsl:apply-templates select="o:price/o:one/@nett" mode="editValue"/></td>
							<td class="last"><xsl:apply-templates select="o:price/o:one/@gross" mode="editValue"/></td>
						</tr>
						</tbody>
					</table>
				</form>
				<a class="operation" href="javascript:addFollowing('{$id}',{$position},'comodity')">další zboží</a>
				<a class="operation" href="javascript:updateFromForm('{$id}',{$position})">uložit</a>
				<div>&#160;</div>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="@nett | @gross" mode="editValue">
		<input value="{translate(.,'.',',')}">
			<xsl:attribute name="name"><xsl:apply-templates select="." mode="position"/></xsl:attribute>
			<xsl:apply-templates select="." mode="editType"/>
		</input>
	</xsl:template>
	
	<xsl:template match="@nett | @gross" mode="editType">
		<xsl:attribute name="onkeypress">return inputCheck('float', this, event)</xsl:attribute>
	</xsl:template>
	
	<xsl:template match="@quantity" mode="editType">
		<xsl:attribute name="onkeypress">return inputCheck('integer', this, event)</xsl:attribute>
	</xsl:template>
	
	<xsl:template match="@VAT" mode="editValue">
		<xsl:variable name="position" select="count(preceding::*) + count(ancestor::*)"/>
		<select>
			<xsl:attribute name="name"><xsl:apply-templates select="." mode="position"/></xsl:attribute>
			<option value="0">
				<xsl:if test=". = 0">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>0</option>
			<option value="9">
				<xsl:if test=". = 9">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>9</option>
			<option value="19">
				<xsl:if test=". = 19">
					<xsl:attribute name="selected">selected</xsl:attribute>
				</xsl:if>19</option>
		</select>
	</xsl:template>	
	
</xsl:stylesheet>