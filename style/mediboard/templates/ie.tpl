<!--[if lte IE 7]>
<div class="deprecated-browser-warning">
  <img src="images/icons/error.png" />
  Votre navigateur web est trop ancien, Mediboard ne peut pas fonctionner correctement. 
  Veuillez mettre � jour votre navigateur � la version 11 ou
  <a href="http://mediboard.org/public/Firefox" target="_blank"><strong>cliquez ici</strong></a> 
  pour installer Firefox et profiter d'une meilleure exp�rience.
</div>

<script>
window.onerror = function(){};
</script>
<![endif]-->

{{if $browser.ie8}}
  <div class="deprecated-browser-warning">
    <img src="images/icons/error.png" />
    Vous utilisez le navigateur Internet Explorer 8, qui ne sera plus pris en charge par Mediboard � partir de la version de juillet 2014.
    <a href="modules/system/public/ie8_deprecated_info.html" target="_blank" style="font-weight: bold;">En savoir plus</a>
  </div>
{{/if}}
