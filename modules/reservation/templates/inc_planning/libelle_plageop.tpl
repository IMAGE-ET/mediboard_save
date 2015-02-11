{{*
 * $Id$
 *  
 * @category Reservation
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}


<div style="float:right;">{{mb_include module=system template=inc_object_notes object=$plageop }}</div>
<div class="hover_chir">
  {{if $plageop->chir_id}}
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$plageop->_ref_chir}}
  {{else}}
    {{mb_include module=mediusers template=inc_vw_function function=$plageop->_ref_spec}}
  {{/if}}
</div>
<p style="text-align: center;">
  {{if $plageop->chir_id}}
    {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$plageop->_ref_chir}}
  {{else}}
    {{mb_include module=mediusers template=inc_vw_function function=$plageop->_ref_spec}}
  {{/if}}

  {{if $plageop->_ref_anesth->_id}}
    <br/>
    <img src='images/icons/anesth.png'/> {{$plageop->_ref_anesth}}
  {{/if}}


  {{if count($plageop->_ref_notes)}}
    {{foreach from=$plageop->_ref_notes item=_note}}
      <hr/>
      <strong style="font-size: 1.2em;">{{$_note->libelle}}</strong><br/>
      {{if $_note->text}}
        {{$_note->text}}
      {{/if}}
    {{/foreach}}
  {{/if}}
</p>