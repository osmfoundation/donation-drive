var goal = 100000; // Target value to be raised.
var raised = 25000; // Value raised so far.
var backers = 250; // Number of folks donated.

function commaNum(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

$('input[type="radio"]').on('change', function() {
  $('#custom-value').attr('disabled', true);
});

$('#amount-custom').on('change', function() {
  $('#custom-value').attr('disabled', false).focus();
});

$('#js-raised').text('£' + commaNum(raised));
$('#js-progress').css('width', (raised / goal) * 100 + '%');
$('#js-backers').text(backers);
