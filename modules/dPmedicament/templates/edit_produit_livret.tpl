<script type="text/javascript">

function submitProduitLivret(lettre, codeATC, code_cip){
  var oForm = document.editProduitLivret;
  submitFormAjax(oForm, 'systemMsg', { 
    onComplete : function(){ 
      window.opener.Livret.reloadAlpha(lettre, code_cip); 
      window.opener.Livret.reloadATC(codeATC, code_cip)  
    } 
  });
}

Main.add(function() {
  Calendar.regField("editProduitLivret", "date_prix_hopital");
  Calendar.regField("editProduitLivret", "date_prix_ville");
});

</script>

<form name="editProduitLivret" action="?" method="post">
  <input type="hidden" name="m" value="dPmedicament" />
  <input type="hidden" name="dosql" value="do_produit_livret_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="code_cip" value="{{$produit_livret->code_cip}}" />
  <table class="tbl">
    <tr>
      <th colspan="2">{{$produit_livret->_ref_produit->libelle}}</th>
    </tr>
    <tr>
      <td>
        Prix Hopital
      </td>
      <td>
        <input type="text" size="5" name="prix_hopital" value="{{$produit_livret->prix_hopital}}" /> &euro;
      </td>
    </tr>
    <tr>
      <td>
        Prix Ville
      </td>
      <td>
        <input type="text" size="5" name="prix_ville" value="{{$produit_livret->prix_ville}}" /> &euro;
      </td>
    </tr>
    <tr>
      <td>
        Date prix h�pital
      </td>
      <td class="date">
        <div id="editProduitLivret_date_prix_hopital_da">{{$produit_livret->date_prix_hopital|date_format:"%d/%m/%Y"}}</div>
        <input type="hidden" name="date_prix_hopital" value="{{$produit_livret->date_prix_hopital}}" />
        <img id="editProduitLivret_date_prix_hopital_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date" />
      </td>
    </tr>
    <tr>
      <td>
        Date prix ville
      </td>
      <td class="date">
        <div id="editProduitLivret_date_prix_ville_da">{{$produit_livret->date_prix_ville|date_format:"%d/%m/%Y"}}</div>
        <input type="hidden" name="date_prix_ville" value="{{$produit_livret->date_prix_ville}}" />
        <img id="editProduitLivret_date_prix_ville_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date" />
      </td>
    </tr>
    <tr>
      <td>
        Code Interne
      </td>
      <td>
        <input type="text" name="code_interne" value="{{$produit_livret->code_interne}}" />
      </td>
    </tr>
    <tr>
      <td>
        Commentaire
      </td>
      <td>
        <textarea cols="7" rows="2" name="commentaire">{{$produit_livret->commentaire}}</textarea>
        
      </td>
    </tr>
    <tr>
      <td colspan="2" style="text-align: center">
        <button type="button" class="submit" onclick="submitProduitLivret('{{$lettre}}','{{$codeATC}}','{{$code_cip}}');">Enregistrer</button>
      </td>
    </tr>
  </table>
</form>

