<?php 
    $fetchPublishableUrl = Session::get('fetchPublishableUrl');
    $paymentUrl = Session::get('paymentUrl');
    $redirectUrl = Session::get('redirectUrl');
    $currency = Session::get('currency');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>Stripe Card Elements sample</title>
    <meta name="description" content="A demo of Stripe Payment Intents" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" type="image/png" href="{{asset('image/logo/k-symbol.png')}}"/>
    <link rel="stylesheet" href="{{ asset('backend/assets/plugins/stripe/css/normalize.css') }}" />
    <link rel="stylesheet" href="{{ asset('backend/assets/plugins/stripe/css/global.css') }}" />
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('backend/assets/plugins/stripe/js/script.js') }}" defer></script>
  </head>

  <body>
    <div class="main-wrap-bg">
      <div class="payment-content">
          <h1>Kika Payment</h1>
          <p>Payment gateway to buy new subscription plan!</p>
      </div>
      <div class="sr-root">
        <div class="payment-bg">
            <img src="{{ asset('backend/assets/plugins/stripe/image/payment.png') }}" alt="Payment">
        </div>
        <div class="sr-main">
          <form id="payment-form" class="sr-payment-form">
            <h2>Stripe Payment Gateway</h2>
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <input type="hidden" name="id" id="userId" value="{{ Session::get('id') }}">
            <input type="hidden" name="amount" id="amount" value="{{ Session::get('amount') }}">
            <input type="hidden" name="currency" id="currency" value="{{ Session::get('currency') }}">
            <input type="hidden" name="subscription" id="subscription" value="{{ Session::get('subscriptionID') }}">
            <input type="hidden" name="addon" id="addon" value="{{ Session::get('addon') }}">
            <div class="sr-combo-inputs-row">
              <div class="sr-input sr-card-element" id="card-element"></div>
            </div>
            <div class="sr-field-error" id="card-errors" role="alert"></div>
            <button id="submit">
              <div class="spinner hidden" id="spinner"></div>
              <span id="button-text">Pay</span><span id="order-amount"></span>
            </button>
          </form>
          <div class="sr-result hidden">
            <p>Payment completed<br /></p>
            <pre>
              <code></code>
            </pre>
          </div>
        </div>  
      </div>
    </div>
  </body>
  <script type="text/javascript">
    var fetchPublishableurl = '{{ $fetchPublishableUrl }}';
    var paymentUrl = '{{ $paymentUrl }}';
    var redirectUrl = '{{ $redirectUrl }}';
    var currency = '{{ $currency }}';
  </script>
</html>
