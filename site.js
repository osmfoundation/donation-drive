$('input[type="radio"]').on('change', function() {
  console.log('hit');
  $('#custom-value').attr('disabled', true);
});

$('#amount-custom').on('change', function() {
  $('#custom-value').attr('disabled', false).focus();
});

$('#donate').submit(function() {
  console.log('foo');
  e.preventDefault();
  e.stopPropagation();
  // Validate
});
