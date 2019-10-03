$(function(){
  'use strict';
  
  $('#new_todo').focus();

  // update
  $('#todos').on('click', '.update_todo', function() {
    // Get id
    var id = $(this).parents('li').data('id');
    // Operation ajax
    $.post('_ajax.php', {
      id: id,
      mode: 'update',
      token: $('#token').val()
    }, function(res) {
      if (res.state === '1') {
        $('#todo_' + id).find('.todo_title').addClass('done');
      } else {
        $('#todo_' + id).find('.todo_title').removeClass('done');
      }
    })
  });
  
  // create
  $('#new_todo_form').on('submit', function() {
    // Get title
    var title = $('#new_todo').val();
    // ajax operation
    $.post('_ajax.php', {
      title: title,
      mode: 'create',
      token: $('#token').val()
    }, function(res) {
      // Add li
      var $li = $('#todo_template').clone();
      $li
        .attr('id', 'todo_' + res.id)
        .data('id', res.id)
        .find('.todo_title').text(title);
      $('#todos').prepend($li.fadeIn());
      $('#new_todo').val('').focus();
    });
    return false;
  });
  
  // delete
  $('#todos').on('click', '.delete_todo', function() {
    // Get id
    var id = $(this).parents('li').data('id');
    // Operation ajax
    if (confirm('Are you sure to delete it?')) {
      $.post('_ajax.php', {
        id: id,
        mode: 'delete',
        token: $('#token').val()
      }, function() {
        $('#todo_' + id).fadeOut(800);
      });
    }
  });
  
});