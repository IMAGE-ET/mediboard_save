function refreshValue(element, class, id, field) {
  if (id && $(element)) {
    var url = new Url;
    url.setModuleAction("dPstock", "httpreq_vw_object_value");
    url.addParam("class", class);
    url.addParam("id", id);
    url.addParam("field", field);
    url.requestUpdate(element, {waitingText: null});
  }
}

/** Submit order function 
 *  Used to submit an order : new or edit order
 *  @param oForm The form containing all the info concerning the order to submit
 *  @param options Options used to execute functions after the submit : {refreshLists, close}
 */
function submitOrder (oForm, options) {
  var oOptions = {
    close: false,
    confirm: false,
    refreshLists: false
  };
  
  Object.extend(oOptions, options);
  
  if (!oOptions.confirm || (oOptions.confirm && confirm('Voulez-vous vraiment effectuer cette action ?'))) {
    submitFormAjax(oForm, 'systemMsg',{
      onComplete: function() {
        if (oOptions.close && window.opener) {
          window.close();
        } else {
          refreshOrder($V(oForm.order_id), oOptions);
        }
      }
    });
  }
}

/** Submit order item function
 *  Used to submit an order item : new or edit order item
 *  @param oForm The form containing all the info concerning the order item to submit
 *  @param options Options used to execute functions after the submit : {refreshLists, close}
 */
function submitOrderItem (oForm, options) {
  if (options && options.noAjax) {
    oForm.submit();
  } else {
    submitFormAjax(oForm, 'systemMsg',{
      onComplete: function() { 
        if (!options.noRefresh) {
          refreshOrderItem($V(oForm.order_item_id), options);
        }
      }
    });
  }
}

/** The refresh order function
 *  Used to refresh the view of an order
*/
function refreshOrder(order_id, options) {
  if (options.refreshLists) {
    refreshLists();
  }
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_order");
  url.addParam("order_id", order_id);
  url.requestUpdate("order-"+order_id, { waitingText: null } );
}

function refreshOrderItem(order_item_id) {
  url = new Url;
  url.setModuleAction("dPstock", "httpreq_vw_order_item");
  url.addParam("order_item_id", order_item_id);
  url.requestUpdate("order-item-"+order_item_id, { waitingText: null } );
}

function refreshListOrders(type, keywords) {
  url = new Url;
  url.setModuleAction("dPstock","httpreq_vw_orders_list");
  url.addParam("type", type);
  url.addParam("keywords", keywords);
  url.requestUpdate("list-orders-"+type, { waitingText: null } );
  
  return false;
}

function refreshLists(keywords) {
  if (!window.opener) {
    refreshListOrders("waiting",   keywords);
    refreshListOrders("locked",    keywords);
    refreshListOrders("pending",   keywords);
    refreshListOrders("received",  keywords);
    refreshListOrders("cancelled", keywords);
  } else {
    window.opener.refreshLists();
  }
  return false;
}

function popupOrder(iOrderId, width, height, bAutofill) {
  width = width || 500;
  height = height || 500;

  var url = new Url();
  url.setModuleAction("dPstock", "vw_aed_order");
  url.addParam("order_id", iOrderId);
  url.addParam("_autofill", bAutofill != undefined);

  url.popup(width, height, "Edition de commande");
}

function popupOrderForm(iOrderId, width, height) {
  width = width || 500;
  height = height || 500;

  var url = new Url();
  url.setModuleAction("dPstock", "vw_order_form");
  url.addParam("order_id", iOrderId);

  url.popup(width, height, "Bon de commande");
}