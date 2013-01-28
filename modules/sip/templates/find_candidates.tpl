{{*
  * Find candidates
  *
  * @category sip
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  * @version  SVN: $Id:$
  * @link     http://www.mediboard.org
*}}

{{mb_script module="sip" script="SIP" ajax=true}}

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="find" action="?m=sip&a=ajax_find_candidates" method="post" onsubmit="return SIP.findCandidates(this)" class="prepared">
        <input type="hidden" name="pointer" value="{{$pointer}}" />

        <table class="form">
          <tr>
            <th class="category" colspan="4">Recherche d'un dossier patient sur le SIP</th>
          </tr>

          <tr>
            <th><label for="nom" title="Nom du patient � rechercher, au moins les premi�res lettres">Nom</label></th>
            <td><input tabindex="1" type="text" name="nom" value="{{$nom|stripslashes}}" /></td>

            <th><label for="cp" title="Code postal du patient � rechercher">Code postal</label></th>
            <td><input tabindex="4" type="text" name="cp" value="{{$cp|stripslashes}}" /></td>
          </tr>

          <tr>
            <th><label for="prenom" title="Pr�nom du patient � rechercher, au moins les premi�res lettres">Pr�nom</label></th>
            <td><input tabindex="2" type="text" name="prenom" value="{{$prenom|stripslashes}}" /></td>

            <th><label for="ville" title="Ville du patient � rechercher">Ville</label></th>
            <td><input tabindex="5" type="text" name="ville" value="{{$ville|stripslashes}}" /></td>
          </tr>

          <tr>
            <th><label for="nom_jeune_fille" title="Nom de naissance">Nom de naissance</label></th>
            <td><input tabindex="3" type="text" name="nom_jeune_fille" value="{{$nom_jeune_fille|stripslashes}}" /></td>

            <th> <label for="Date_Day" title="Date de naissance du patient � rechercher"> Date de naissance </label> </th>
            <td> {{mb_include module=patients template=inc_select_date date="--" tabindex=6}} </td>
          </tr>

          <tr>
            <th><label for="sexe" title="Sexe">Sexe</label></th>
            <td>
              <select name="sexe">
                <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                <option value="m" {{if $sexe == "m"}}selected{{/if}}>
                {{tr}}CPatient.sexe.m{{/tr}}
                </option>
                <option value="f" {{if $sexe == "f"}}selected{{/if}}>
                {{tr}}CPatient.sexe.f{{/tr}}
                </option>
              </select>
            </td>

            <td colspan="2"></td>
          </tr>

          <tr>
            <th><label for="quantity_limited_request" title="Limite des r�sultats">Limite des r�sultats recherch�s</label></th>
            <td><input tabindex="8" type="text" name="quantity_limited_request" value="{{$quantity_limited_request}}" /></td>

            <td colspan="2"></td>
          </tr>

          <tr>
            <td class="button" colspan="4">
              <button class="search singleclick"> {{tr}}Search{{/tr}} </button>
              <button class="tick singleclick" name="finder" {{if !$pointer}}disabled{{/if}} onclick=""> {{tr}}Continue{{/tr}} </button>
            </td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
  <tr>
    <td id="find_candidates">

    </td>
  </tr>
</table>