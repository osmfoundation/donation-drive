var goal = 100000;
var raised = 25000;

function commaNum(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

$('input[type="radio"]').on('change', function() {
  console.log('hit');
  $('#custom-value').attr('disabled', true);
});

$('#amount-custom').on('change', function() {
  $('#custom-value').attr('disabled', false).focus();
});

$('#js-raised').text('£' + commaNum(raised));
$('#js-progress').css('width', (raised / goal) * 100 + '%');
