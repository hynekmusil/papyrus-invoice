<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:s="http://formax.cz/impresso/system"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:o="http://formax.cz/impresso/invoice" exclude-result-prefixes="s o">
	<xsl:import href="vocab.xslt"/>
	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
	
	<xsl:template match="o:rate">
		<tr>
			<th class="coll">Sazba <xsl:value-of select="@percentage"/>%</th>
			<td><xsl:value-of select="translate(round(translate(@base,',','.') * 10) div 10,'.',',')"/> Kč</td>
			<td><xsl:value-of select="translate(round(translate(@ratio,',','.') * 10) div 10,'.',',')"/> Kč</td>
			<td class="last"><xsl:value-of select="translate(round(translate(@sum,',','.') * 10) div 10,'.',',')"/> Kč</td>
		</tr>
	</xsl:template>
	
	<xsl:template match="o:sum">
		<tr>
			<th class="coll sum">Celkem:</th>
			<td class="sum"><xsl:value-of select="translate(round(translate(@base,',','.') * 10) div 10,'.',',')"/> Kč</td>
			<td class="sum"><xsl:value-of select="translate(round(translate(@ratio,',','.') * 10) div 10,'.',',')"/> Kč</td>
			<td class="sum last"><xsl:value-of select="translate(round(translate(@sum,',','.') * 10) div 10,'.',',')"/> Kč</td>
		</tr>
	</xsl:template>
	
	<xsl:template match="o:summary">
		<div class="summary">
			<table class="border" cellspacing="0">
				<thead>
					<tr>
						<th>Rekapitulace:</th>
						<th>DPH</th>
						<th>Základ DPH</th>
						<th class="last">Celkem s DPH</th>
					</tr>
				</thead>
				<tbody><xsl:apply-templates select="o:rate"/></tbody>
				<tfoot><xsl:apply-templates select="o:sum"/></tfoot>	
			</table>
			<span>Razítko a podpis:</span>
		</div>
	</xsl:template>
	
</xsl:stylesheet>
