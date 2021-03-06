{{*
 * $Id$
 *  
 * @category dPplanningOp
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<h2>Importation des {{tr}}{{$mode_class}}{{/tr}}</h2>

<div class="small-info">
  Veuillez indiquez les champs suivants (code, libelle, mode, actif) dans un fichier CSV (<strong>au format ISO</strong>)
  dont les champs sont s�par�s par <strong>;</strong> et les textes par <strong>"</strong>, la premi�re ligne �tant ignor�e.
</div>

<form method="post" name="import" enctype="multipart/form-data">
  <input type="hidden" name="m" value="{{$m}}" />

  <input type="hidden" name="MAX_FILE_SIZE" value="4096000" />
  <input type="file" name="import" />

  <button type="submit" class="submit">{{tr}}Save{{/tr}}</button>
</form>