// A reference to Stripe.js
var stripe;

var orderData = {
  items: [{ id: "photo-subscription" }],
  currency: currency
};

// Disable the button until we have Stripe set up on the page
document.querySelector("button").disabled = true;

fetch(fetchPublishableurl)
  .then(function(result) {
    return result.json();
  })
  .then(function(data) {
    return setupElements(data);
  })
  .then(function({ stripe, card, clientSecret }) {
    document.querySelector("button").disabled = false;

    var form = document.getElementById("payment-form");
    form.addEventListener("submit", function(event) {
      event.preventDefault();
      createToken(card);
      pay(stripe, card, clientSecret);
    });
  });


function stripeTokenHandler(token) {
  // Insert the token ID into the form so it gets submitted to the server
  var form = document.getElementById("payment-form");
  var hiddenInput = document.createElement('input');
  hiddenInput.setAttribute('type', 'hidden');
  hiddenInput.setAttribute('id', 'stripeToken');
  hiddenInput.setAttribute('value', token.id);
  form.appendChild(hiddenInput);
  console.log("stripeTokenHandler",form);
  // Submit the form
  // form.submit();
}

function createToken(card) {
  stripe.createToken(card).then(function(result) {
    if (result.error) {
      // Inform the user if there was an error
      var errorElement = document.getElementById('card-errors');
      errorElement.textContent = result.error.message;
    } else {
      // Send the token to your server
      stripeTokenHandler(result.token);
    }
  });
};
var setupElements = function(data) {
  stripe = Stripe(data.publishableKey);
  /* ------- Set up Stripe Elements to use in checkout form ------- */
  var elements = stripe.elements();
  var style = {
    base: {
      color: "#32325d",
      fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
      fontSmoothing: "antialiased",
      fontSize: "16px",
      "::placeholder": {
        color: "#aab7c4"
      }
    },
    invalid: {
      color: "#fa755a",
      iconColor: "#fa755a"
    }
  };

  var card = elements.create("card", { style: style });
  card.mount("#card-element");

  return {
    stripe: stripe,
    card: card,
    clientSecret: data.clientSecret
  };
};

var handleAction = function(clientSecret) {
  stripe.handleCardAction(clientSecret).then(function(data) {
    console.log('handleCardAction',data);
    if (data.error) {
      showError("Your card was not authenticated, please try again");
    } else if (data.paymentIntent.status === "requires_confirmation") {
      fetch(paymentUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          paymentIntentId: data.paymentIntent.id,
          _token : document.getElementById('token').value,
          id : document.getElementById('userId').value,
          amount : document.getElementById('amount').value,
          subscription : document.getElementById('subscription').value,
          addon : document.getElementById('addon').value,
        })
      })
        .then(function(result) {
          return result.json();
        })
        .then(function(json) {
          if (json.error) {
            showError(json.error);
          } else {
            orderComplete(redirectUrl);
          }
        });
    }
  });
};

// stripe.confirmCardPayment(clientSecret, {
//   payment_method: {
//     card: card,
//     billing_details: {
//       name: 'Jenny Rosen'
//     }
//   },
//   setup_future_usage: 'off_session'
// }).then(function(result) {
//   if (result.error) {
//     // Show error to your customer
//     console.log(result.error.message);
//   } else {
//     if (result.paymentIntent.status === 'succeeded') {
//       // Show a success message to your customer
//       // There's a risk of the customer closing the window before callback execution
//       // Set up a webhook or plugin to listen for the payment_intent.succeeded event
//       // to save the card to a Customer

//       // The PaymentMethod ID can be found on result.paymentIntent.payment_method
//     }
//   }
// });

/*
 * Collect card details and pays for the order
 */
var pay = function(stripe, card) {
  changeLoadingState(true);
var form = document.getElementById('payment-form');
  stripe
    .createPaymentMethod("card", card)
    // .createPaymentMethod({
    //   payment_method: {
    //     card: card,
    //   },
    //   setup_future_usage: 'off_session'
    // })
    .then(function(result) {
      console.log("paymentMethod",result);
      // Stripe.card.createToken(form, stripeResponseHandler);
      if (result.error) {
        showError(result.error.message);
      } else {
        orderData.paymentMethodId = result.paymentMethod.id;
        orderData._token = document.getElementById('token').value;
        orderData.id = document.getElementById('userId').value;
        orderData.amount = document.getElementById('amount').value;
        orderData.subscription = document.getElementById('subscription').value;
        orderData.addon = document.getElementById('addon').value;
        orderData.stripeToken = document.getElementById('stripeToken').value;

        return fetch(paymentUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(orderData)
        });
      }
    })
    .then(function(result) {
      return result.json();
    })
    .then(function(paymentData) {
      if (paymentData.requiresAction) {
        // Request authentication
        handleAction(paymentData.clientSecret);
      } else if (paymentData.error) {
        showError(paymentData.error);
      } else {
        orderComplete(redirectUrl);
      }
    });
};

/* ------- Post-payment helpers ------- */

/* Shows a success / error message when the payment is complete */
var orderComplete = function(redirectUrl) {
  window.location.href = redirectUrl;
};

var showError = function(errorMsgText) {
  changeLoadingState(false);
  var errorMsg = document.querySelector(".sr-field-error");
  errorMsg.textContent = errorMsgText;
  setTimeout(function() {
    errorMsg.textContent = "";
  }, 4000);
};

// Show a spinner on payment submission
var changeLoadingState = function(isLoading) {
  if (isLoading) {
    document.querySelector("button").disabled = true;
    document.querySelector("#spinner").classList.remove("hidden");
    document.querySelector("#button-text").classList.add("hidden");
  } else {
    document.querySelector("button").disabled = false;
    document.querySelector("#spinner").classList.add("hidden");
    document.querySelector("#button-text").classList.remove("hidden");
  }
};
