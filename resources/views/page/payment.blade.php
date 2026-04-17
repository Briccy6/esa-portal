<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Payment</title>
  </head>
  <body>
<div class="container">
    <div class="header px-5 text-center bg-primary py-5 text-white">
      <h1> Pay For Registration fee</h1> 
      
    </div>
    <div class="main">
        <form action="">
            button
        </form>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="https://checkout.flutterwave.com/v3.js"></script> 
   <script>
        function makePayment() {
	FlutterwaveCheckout({
		public_key: 'FLWPUBK_TEST-SANDBOXDEMOKEY-X',
		tx_ref: 'titanic-48981487343MDI0NzMx',
		amount: 54600,
		currency: 'NGN',
		payment_options: 'card, mobilemoneyghana, ussd',
		redirect_url: 'https://glaciers.titanic.com/handle-flutterwave-payment',
		meta: {
			consumer_id: 23,
			consumer_mac: '92a3-912ba-1192a',
		},
		customer: {
			email: 'rose@unsinkableship.com',
			phone_number: '08102909304',
			name: 'Rose DeWitt Bukater',
		},
		customizations: {
			title: 'The Titanic Store',
			description: 'Payment for an awesome cruise',
			logo: 'https://www.logolynx.com/images/logolynx/22/2239ca38f5505fbfce7e55bbc0604386.jpeg',
		},
	});
}
    </script>
    </body>
</html>