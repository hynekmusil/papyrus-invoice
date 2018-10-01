<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns="http://www.w3.org/1999/xhtml" 
    xmlns:h="http://www.w3.org/1999/xhtml"
    xmlns:e="http://exslt.org/common"
    version="1.0"
    >
    
    <xsl:template match="*">
        <xsl:copy>
            <xsl:copy-of select="@*"/>
            <xsl:apply-templates/>
        </xsl:copy>
    </xsl:template>
    
    <xsl:template match="h:ul[@id = 'invoiceList' or @id = 'searchResult']">
        <xsl:variable name="cnt" select="ceiling(count(h:li) div 6)"/>
        <xsl:variable name="sortedList">
            <ul>
                <xsl:for-each select="h:li[not(@class)]">
                    <xsl:sort select="normalize-space(*[1])" data-type="number" order="descending"/>
                    <xsl:copy-of select="."/>
                </xsl:for-each>
            </ul>
        </xsl:variable>
        <xsl:choose>
            <xsl:when test="count(h:li) &lt;= 6">
                <xsl:copy-of select="$sortedList"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates mode="sorted" select="e:node-set($sortedList)/*/*[(position() mod $cnt) = 1]">
                    <xsl:with-param name="cnt" select="$cnt"/>
                </xsl:apply-templates>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template mode="sorted" match="*">
        <xsl:param name="cnt"/>
        <xsl:variable name="style">
            <xsl:if test="position() = 1"> padding-left:5px;</xsl:if>
            <xsl:if test="position() &gt; 1"> padding-left:20px;</xsl:if>
        </xsl:variable>
        <ul style="float:left;{$style}">
            <xsl:for-each select=". | following-sibling::h:li[not(@class)][position() &lt; $cnt]">
                <xsl:copy-of select="."/>
            </xsl:for-each> 
        </ul>
    </xsl:template>
    
</xsl:stylesheet>