// $Id: $

Prestations = {
  edit: function(sejour_id, context) {
    var url = new Url('planningOp', 'ajax_vw_prestations');
    url.addParam('sejour_id', sejour_id);
    if (context) {
      url.addParam('context', context);
    }
    url.requestModal(800, 600);
  }
}
