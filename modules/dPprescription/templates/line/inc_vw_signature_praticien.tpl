{{$line->_ref_praticien->_view}}
{{if $line->signee}}
 <img src="images/icons/tick.png" alt="Ligne sign�e par le praticien" title="Ligne sign�e par le praticien" />
{{else}}
	 <img src="images/icons/cross.png" alt="Ligne non sign�e par le praticien"title="Ligne non sign�e par le praticien" />
{{/if}}