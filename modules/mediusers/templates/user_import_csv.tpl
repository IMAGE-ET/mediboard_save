{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage mediusers
 * @version $Revision: 6287 $
 * @author SARL OpenXtrem
 * @license GNU GPL
*}}

<h2>Import d'utilisateurs Mediboard.</h2>

{{mb_include module=system template=inc_import_csv_info_intro}}
<li><strong>{{mb_label class=CMediusers field=_user_last_name }}</strong></li>
<li><strong>{{mb_label class=CMediusers field=_user_first_name}}</strong></li>
<li>{{mb_label class=CMediusers field=_user_username  }} </li>
<li>{{mb_label class=CMediusers field=_user_password  }} </li>
<li><strong>{{mb_label class=CMediusers field=_user_type      }}</strong> (code num�rique)</li>
<li><strong>{{mb_label class=CMediusers field=function_id     }}</strong> ({{mb_label class=CFunctions field=text}}) : fonction cr��e si introuvable</li>
<li>{{mb_label class=CMediusers field=_profile_id     }} ({{mb_label class=CUser field=user_username}}): profil non cr�� si introuvable</li>
{{if $conf.ref_pays == 1}}
  <li>{{mb_label class=CMediusers field=adeli           }} </li>
  <li>{{mb_label class=CMediusers field=rpps            }} </li>
{{else}}
  <li>{{mb_label class=CMediusers field=ean           }} </li>
  <li>{{mb_label class=CMediusers field=rcc            }} </li>
{{/if}}
<li>{{mb_label class=CMediusers field=spec_cpam_id    }} (code � deux chiffres): spc�cialit� non cr��e si introuvable</li>
<li>{{mb_label class=CMediusers field=discipline_id   }} : discipline non cr��e si introuvable</li>
<li>{{mb_label class=CIdSante400 field=id400          }} : idex non cr�� si introuvable</li>
<li>{{mb_label class=CMediusers field=remote          }} : 0 ou 1, par d�faut � 1</li>
{{mb_include module=system template=inc_import_csv_info_outro}}

<form method="post" action="?m={{$m}}&{{$actionType}}={{$action}}&dialog=1" name="import" enctype="multipart/form-data">
  <input type="hidden"   name="m" value="{{$m}}" />
  <input type="hidden"   name="{{$actionType}}" value="{{$action}}" />
  <input type="hidden"   name="MAX_FILE_SIZE" value="4096000" />
  <input type="file"     name="import" />
  <input type="checkbox" name="dryrun" value="1" checked/>
  <label for="dryrun">Essai � blanc</label>
  <button class="submit">{{tr}}Save{{/tr}}</button>
</form>

{{if $results|@count}}
  <table class="tbl">
    <tr>
      <th class="title" colspan="14">{{$results|@count}} utilisateurs trouv�s</th>
    </tr>
    <tr>
      <th>Etat</th>
      <th>
        {{mb_label class=CMediusers field=_user_last_name }}
        {{if array_key_exists("user", $unfound)}}
          <br/>
          {{$unfound.user|@count}} d�j� existant(s)
        {{/if}}
      </th>
      <th>{{mb_label class=CMediusers field=_user_first_name}}</th>
      <th>{{mb_label class=CMediusers field=_user_username  }}</th>
      <th>{{mb_label class=CMediusers field=_user_password  }}</th>
      <th>
        {{mb_label class=CMediusers field=_user_type      }}
        {{if array_key_exists("user_type", $unfound)}}
          <br/>
          {{$unfound.user_type|@count}} non trouv�(s)
        {{/if}}
      </th>
      <th>{{mb_label class=CMediusers field=function_id     }}</th>
      {{if $conf.ref_pays == 1}}
        <th>{{mb_label class=CMediusers field=adeli         }}</th>
        <th>{{mb_label class=CMediusers field=rpps          }}</th>
      {{else}}
        <th>{{mb_label class=CMediusers field=ean           }}</th>
        <th>{{mb_label class=CMediusers field=rcc           }}</th>
      {{/if}}
      <th>
        {{mb_label class=CMediusers field=_profile_id     }}
        {{if array_key_exists("profil_name", $unfound)}}
          <br/>
          {{$unfound.profil_name|@count}} non trouv�(s)
        {{/if}}
      </th>
      <th>
        {{mb_label class=CMediusers field=spec_cpam_id    }}
        {{if array_key_exists("spec_cpam_code", $unfound)}}
          <br/>
          {{$unfound.spec_cpam_code|@count}} non trouv�e(s)
        {{/if}}
      </th>
      <th>
        {{mb_label class=CMediusers field=discipline_id   }}
        {{if array_key_exists("discipline_name", $unfound)}}
          <br/>
          {{$unfound.discipline_name|@count}} non trouv�e(s)
        {{/if}}
      </th>
      <th>
        {{mb_label class=CIdSante400 field=id400}}
      </th>
      <th>
        {{mb_label class=CMediusers field=remote}}
      </th>
    </tr>
    {{foreach from=$results item=_user}}
      <tr>
        {{if $_user.error}}
          <td class="text warning compact">
            {{$_user.error}}
          </td>
        {{elseif $dryrun}}
          <td class="">
            Essai
          </td>
        {{else}}
          <td class="text ok">
            OK
          </td>
        {{/if}}
        <td class="text {{if array_key_exists($_user.lastname, $unfound.user)}}warning{{/if}}">{{$_user.lastname}}     </td>
        <td class="text">{{$_user.firstname}}    </td>
        <td class="text">{{$_user.username}}     </td>
        <td class="text">{{$_user.password}}     </td>
        <td class="text {{if array_key_exists($_user.type, $unfound.user_type)}}warning{{/if}}">{{$_user.type}}         </td>
        <td class="text">{{$_user.function_name}}</td>
        {{if $conf.ref_pays == 1}}
          <td class="text">{{$_user.adeli}}      </td>
          <td class="text">{{$_user.rpps}}       </td>
        {{else}}
          <td class="text">{{$_user.ean}}        </td>
          <td class="text">{{$_user.rcc}}        </td>
        {{/if}}
        <td class="text {{if array_key_exists($_user.profil_name    , $unfound.profil_name    )}}warning{{/if}}">{{$_user.profil_name    }}</td>
        <td class="text {{if array_key_exists($_user.spec_cpam_code , $unfound.spec_cpam_code )}}warning{{/if}}">{{$_user.spec_cpam_code }}</td>
        <td class="text {{if array_key_exists($_user.discipline_name, $unfound.discipline_name)}}warning{{/if}}">{{$_user.discipline_name}}</td>
        <td class="text {{if array_key_exists($_user.idex           , $unfound.idex           )}}warning{{/if}}">{{$_user.idex           }}</td>
        <td class="text">{{$_user.remote}}</td>
      </tr>
    {{/foreach}}
  </table>
{{/if}}