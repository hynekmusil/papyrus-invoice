<?php
	$text = '<table border="0" cellpadding="0" cellspacing="0" class="portal" width="219">

				  <tr>
					<td class="head"><a href="http://poradna-prace.jobs.cz/">Poradna</a></td>
				  </tr>
				  <tr>
					<td valign="top" class="content">
          <strong><a href="http://poradna-prace.jobs.cz/novinky/">Aktuální èlánky:</a></strong>
          <span class="dot"><a href="novinka/article/381/990/" title="První námluvy absolventù a firem probíhají on-line">První námluvy absolventù a firem probíhají on-line</a>

		</span><span class="dot"><a href="novinka/article/380/990/" title="Když máte kolegu pro dobrou náladu">Když máte kolegu pro dobrou náladu</a>
		</span>
          <br />
          </td>
				  </tr>
          </table>
					
          <table border="0" cellpadding="0" cellspacing="0" class="portal" width="219">
					<tr>

					<td valign="top" class="content">
          <strong><a href="http://poradna-prace.jobs.cz/hledam_praci/">Hledám práci</a></strong> Kde hledat zamìstnání...<br />
				  <strong><a href="http://poradna-prace.jobs.cz/vytvarim_cv/">Vytváøím CV</a></strong> Jak správnì napsat životopis... <br />
					<strong><a href="http://poradna-prace.jobs.cz/kalkulacky/">Kalkulaèky</a></strong> Výpoèet mzdy, nemocenské... 
          </td>

				  </tr>
</table>';
	$replacement = '$1http://poradna-prace.jobs.cz/$2"';
	echo preg_replace("/(\shref=\")(?<!http:\/\/)((\/?[-_~&=\?\.a-z0-9]*)*)\"/",$replacement,$text);
?>