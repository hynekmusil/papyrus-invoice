<?php
	$text = '<table border="0" cellpadding="0" cellspacing="0" class="portal" width="219">

				  <tr>
					<td class="head"><a href="http://poradna-prace.jobs.cz/">Poradna</a></td>
				  </tr>
				  <tr>
					<td valign="top" class="content">
          <strong><a href="http://poradna-prace.jobs.cz/novinky/">Aktu�ln� �l�nky:</a></strong>
          <span class="dot"><a href="novinka/article/381/990/" title="Prvn� n�mluvy absolvent� a firem prob�haj� on-line">Prvn� n�mluvy absolvent� a firem prob�haj� on-line</a>

		</span><span class="dot"><a href="novinka/article/380/990/" title="Kdy� m�te kolegu pro dobrou n�ladu">Kdy� m�te kolegu pro dobrou n�ladu</a>
		</span>
          <br />
          </td>
				  </tr>
          </table>
					
          <table border="0" cellpadding="0" cellspacing="0" class="portal" width="219">
					<tr>

					<td valign="top" class="content">
          <strong><a href="http://poradna-prace.jobs.cz/hledam_praci/">Hled�m pr�ci</a></strong> Kde hledat zam�stn�n�...<br />
				  <strong><a href="http://poradna-prace.jobs.cz/vytvarim_cv/">Vytv���m CV</a></strong> Jak spr�vn� napsat �ivotopis... <br />
					<strong><a href="http://poradna-prace.jobs.cz/kalkulacky/">Kalkula�ky</a></strong> V�po�et mzdy, nemocensk�... 
          </td>

				  </tr>
</table>';
	$replacement = '$1http://poradna-prace.jobs.cz/$2"';
	echo preg_replace("/(\shref=\")(?<!http:\/\/)((\/?[-_~&=\?\.a-z0-9]*)*)\"/",$replacement,$text);
?>