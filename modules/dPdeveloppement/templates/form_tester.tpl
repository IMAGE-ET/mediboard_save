<script type="text/javascript">
var form;
Main.add(function () {
  //References.clean(document.documentElement);
  form = Form.get("test");
  //form.text_1.mask("~(99) 99 99 99 99", {placeholder: '_', completed: function(s) {alert(s.rawvalue)} });
  //Console.debug(checkForm(form));
  
  $('bah-2').insert(A({href: 'http://www.mozilla-europe.org', target: '_blank'}, 'Firefox rocks !!', DIV({className: 'big-info'}, 'hihi')));
});

</script>

{{if !$dialog}}
<a href="?m={{$m}}&amp;a={{$tab}}&amp;dialog=1">Lancer cette page sans les menus</a>
{{else}}
<a href="?m={{$m}}&amp;tab={{$a}}">Lancer cette page avec les menus</a>
{{/if}}

<div id="bah-2"></div>
<form name="test" action="?" method="get" onsubmit="if (checkForm(this)) {Console.trace('form.submit()');} return false;" id="form-test-id">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  
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
      <td><label for="text_1">Champ masqu�</label><input type="text" name="text_1" class="mask|+(99)S99S99S99P99P notNull"/> ~(99) 99 99 99 99</td>
    </tr>
    <tr>
      <td><label for="num_1">Champ num�rique</label><input type="text" name="num_1" class="incrementable"/> ~(99) 99 99 99 99</td>
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