<script type="text/javascript">
var form;
Main.add(function () {
  form = getForm("test");
  $('dom-creator').insert(
    DIV({className: 'big-info'}, 
      A({href: 'http://www.mozilla-europe.org', target: '_blank'}, 
        'Firefox rocks !!'
      )
    )
  );
});

</script>

{{if !$dialog}}
<a href="?m={{$m}}&amp;a={{$tab}}&amp;dialog=1">Lancer cette page sans les menus</a>
{{else}}
<a href="?m={{$m}}&amp;tab={{$a}}">Lancer cette page avec les menus</a>
{{/if}}

<div id="dom-creator"></div>

<form name="test" action="?" method="get" onsubmit="if (checkForm(this)) {Console.trace('form.submit()');} return false;" id="form-test-id">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  
  <table class="form">
  {{foreach from=$specs item=class key=spec}}
    <tr>
      <th>{{mb_title object=$object field=$spec}}</th>
      <td>{{mb_field object=$object field=$spec form=test register=1}}</td>
    </tr>
  {{/foreach}}
  </table>
  
  <table>
    <tr>
      <td>
        <select name="select_tree_1" class="select-tree" onchange="Console.debug(this.value+':'+this.options[this.selectedIndex].text);">
          <option value="1">Thomas</option>
          <option value="2" >Romain</option>
          <option value="11" >123</option>
          <optgroup label="Salari�s">
            <option value="3" >Alexis</option>
            <option value="4" >Fabien</option>
          </optgroup>
          <optgroup label="Stagiaires">
            <option value="8"  selected="selected">Alexandre</option>
            <option value="9" ></option>
            <option value="10" >encore un</option>
          </optgroup>
        </select>
      </td>
    </tr>
    <tr>
      <td><label for="text_1">Champ masqu�</label><input type="text" name="text_1" class="mask|+(99)S99S99S99P99P notNull"/></td>
    </tr>
    <tr>
      <td>
        <label for="user_username">Login</label><input type="text" name="user_username" value="fabien" class="str" /><br />
        <label for="text_2">Mot de passe</label><input type="password" name="text_2" value="123456789" 
               class="password minLength|6 notContaining|user_username notNear|user_username alphaAndNum"
               onkeyup="checkFormElement(this)" />
               <div id="text_2_message"></div>
        <button type="button" name="button_1" onclick="Console.debug($(this.form.text_2).caret(3, 6, 'toto'))">Caret</button>
      </td>
    </tr>
    <tr>
      <td><button type="submit" name="submit_1">OK</button></td>
    </tr>
  </table>
</form>