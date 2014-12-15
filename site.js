var goal = 100000; // Target value to be raised.
var raised = 25000; // Value raised so far.
var backers = 250; // Number of folks donated.

// Helper function: Add commas to longer integers.
function commaNum(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function validNumber(x) {
  return x;
}

// Helper function: Turn form values into an object.

function formKeyVal(form) {
    var record = {};
    ['input', 'textarea'].forEach(function(type) {
        $(form).find(type).each(function() {
            if (this.value.length < 1 ||
                this.name === '') return;
            if (this.type === 'checkbox') {
                record[this.name] = this.checked ? true : false;
            } else if (this.type === 'radio') {
                if (!this.checked) return;
                record[this.name] = this.id;
            } else {
                record[this.name] = this.value;
            }
        });
    });
    return record;
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

// Form submission
$('#donate').submit(function(e) {
  var array = formKeyVal(this), amount;
  if (array.amount === 'amount-custom') {
    amount = array['custom-value'];
  } else {
    amount = array.amount.split('-')[1];  
  }

  if (!validNumber) return alert('Value must be a valid number');

  // Amount to be submitted to
  // payment vendor.
  console.log(amount);
  return false;
});
