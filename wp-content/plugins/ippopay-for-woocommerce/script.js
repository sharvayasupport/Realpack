ippopayHandler(response, function (e) {
  if(e.data.status == 'success'){
    document.getElementById('ippopay_payment_id').value = e.data.transaction_no;
    document.getElementById("btn-ippopay-submit").click();
  }
  if(e.data.status == 'failure'){
      setTimeout(function () {
        ip1.close();
      }, 3000);
  }
}); 
document.getElementById('btn-ippopay').onclick = function(e){
  ip1.open();
  e.preventDefault();
  }
var options = {
  "order_id": ippopay_params.order_id,
  "public_key": ippopay_params.public_key,
  "secret_key": ippopay_params.secret_key
}
var ip1 = new Ippopay(options);
ip1.open();