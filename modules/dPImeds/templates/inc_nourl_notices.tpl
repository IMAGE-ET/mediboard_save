{{if !$app->_is_intranet}}
	<div class="big-info">
	Pour des raisons de s�curit�, l'affichage des r�sultats de laboratoire n'est pas disponible depuis l'acc�s distant.
	<br />
	Merci de r�essayer ult�rieurement depuis un <strong>acc�s sur site</strong>.
	</div>
	
{{else}}
	<div class="big-warning">
	Le module IMeds n'a pas �t� correctement configur�.
	<br />
	Merci de contacter un administrateur pour <strong>param�trer les URL de connexions au serveur de r�sultat</strong>.
</div>
{{/if}}