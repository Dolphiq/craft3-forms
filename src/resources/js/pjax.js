/**
 * Created by lucasweijers on 01-08-17.
 */

$(function(){

  //Handle pjax form submit
  $.pjax.defaults.timeout = 12000;
  $.pjax.defaults.push = false;
  $.pjax.defaults.type = 'POST';
  $.pjax.defaults.scrollTo = false;

  $('form[data-pjax=true][data-pjax-container]').on('submit', function(e){
    e.preventDefault();
  });

  //Check if a pjax form is being submitted
  $(document).on('submit', 'form[data-pjax=true][data-pjax-container]', function(event) {

    var c = $(this).data('pjax-container');
    var p = $(this).data('pjax-push');
    var t = $(this).data('pjax-method');

    t = (t !== undefined) ? t : $.pjax.defaults.type;
    p = (p !== undefined) ? p : $.pjax.defaults.push;

    if(c !== undefined && c !== '') {
      $.pjax.submit(event, {container: c, fragment: c, type: t, push: p});
    }
  });

  $(document).on('pjax:send', function(event, options) {
    var t = $(event.target);
    t.find('.pjax__loading').show();
  });

  $(document).on('pjax:complete', function(event, options) {
    var t = $(event.target);
    t.find('.pjax__loading').hide();
  });

});