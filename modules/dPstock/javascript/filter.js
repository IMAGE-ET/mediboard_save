/** A filter function, useful */

function Filter (sForm, sModule, sAction, sList, aFields, sHiddenColumn) {
  this.sForm   = sForm;
  this.sModule = sModule;
  this.sAction = sAction;
  this.sList   = sList;
  this.aFields = aFields;
  this.sHiddenColumn = sHiddenColumn;
  this.selected = 0;
  
  element = this;
  oForm = document.forms[this.sForm];
  this.aFields.each (function (f) {
    if (oForm[f]) {
      $(oForm[f]).observe('change', element.resetRange.bindAsEventListener(element));
    }
  });
}

Filter.prototype = {
  submit: function (fieldToSelect) {
    oForm = document.forms[this.sForm];
    element = this;
    url = new Url;
    
    url.setModuleAction(this.sModule, this.sAction);

    this.aFields.each (function (f) {
      if (oForm[f]) {
        url.addParam(f, $V(oForm[f]));
      }
    });
    
    if (fieldToSelect) {
      var oField = oForm.elements[fieldToSelect];
      
      if (oField) {
        oField.focus();
        oField.select();
      }
    }
    
    if (this.sHiddenColumn) {
      url.addParam("hidden_column", this.sHiddenColumn);
    }
    
    makeRanges = function (total, step) {
      var ranges = [];
      var i = 0;
      while(total > 0) {
        ranges.push(i*step+','+step);
        total-=step;
        i++;
      }
      return ranges;
    }

    makeRangeSelector = function () {
      var count = $(element.sList+'-total-count');
      if (count) {
        count = parseInt(count.innerHTML);
      }
      
      var form = document.forms[element.sForm];
      var field = form.limit;

      var rangeSel = new Element('div').setStyle({textAlign: 'center'});
      if (count > 20) {
        var total = count;
        var r = makeRanges(total, 20);
        
        rangeSel.insert(
          new Element('a', {'href': '#1'})
             .update('|&lt;&nbsp;')
             .observe('click', function () {$V(field, 20); form.onsubmit(); element.selected = 0;})
        );
        
        r.each(function (e, k) {
          var a = new Element('a', {'href': '#1'})
                      .update('&nbsp;'+(k+1)+'&nbsp;')
                      .observe('click', function () {$V(field, e); form.onsubmit(); element.selected = k;});
          if (k == element.selected) {
            a.setStyle({fontWeight: 'bold'});
          }
          rangeSel.insert(a);
        });
        
        rangeSel.insert(
          new Element('a', {'href': '#1'})
             .update('&nbsp;&gt;|')
             .observe('click', 
                function () {
                  $V(field, (total-(total%20))+', '+20);
                  form.onsubmit(); 
                  element.selected = r.length-1;
                }
             )
        );
      }
      $(element.sList).insert(rangeSel);
    }
    
    url.requestUpdate(this.sList, { waitingText: null, onComplete: makeRangeSelector } );

    return false;
  },
  
  empty: function (fields) {
    oForm = document.forms[this.sForm];
    if (!fields) {
      this.aFields.each (function (f) {
        if (oForm[f]) {
          oForm[f].value = '';
          oForm[f].selectedIndex = 0;
        }
      });
    } else if (typeof fields == "string") {
      if (oForm[fields]) {
        oForm[fields].value = '';
        oForm[fields].selectedIndex = 0;
      }
    } else {
      fields.each (function (f) {
        if (oForm[f]) {
          oForm[f].value = '';
          oForm[f].selectedIndex = 0;
        }
      });
    }
    this.submit();
  }, 
  
  resetRange: function () {
    this.selected = 0;
    document.forms[this.sForm].limit.value = '';
  }
}