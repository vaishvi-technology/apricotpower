// import  { useEffect, useRef } from 'react';

// const GooglePayButton = () => {
//   const googlePayRef = useRef(null);

//   useEffect(() => {
//     const script = document.createElement('script');
//     script.src = 'https://pay.google.com/gp/p/js/pay.js';
//     script.async = true;
//     script.onload = () => {
//       const paymentsClient = new window.google.payments.api.PaymentsClient({
//         environment: 'TEST',
//       });

//       const baseRequest = {
//         apiVersion: 2,
//         apiVersionMinor: 0,
//       };

//       const allowedCardNetworks = ['VISA', 'MASTERCARD'];
//       const allowedCardAuthMethods = ['PAN_ONLY', 'CRYPTOGRAM_3DS'];

//       const tokenizationSpecification = {
//         type: 'PAYMENT_GATEWAY',
//         parameters: {
//           gateway: 'paypal',
//           gatewayMerchantId: 'MQ78CLCRTAYZL',
//         },
//       };

//       const baseCardPaymentMethod = {
//         type: 'CARD',
//         parameters: {
//           allowedAuthMethods: allowedCardAuthMethods,
//           allowedCardNetworks: allowedCardNetworks,
//         },
//       };

//       const cardPaymentMethod = Object.assign({}, baseCardPaymentMethod, {
//         tokenizationSpecification: tokenizationSpecification,
//       });

//       const paymentDataRequest = Object.assign({}, baseRequest, {
//         allowedPaymentMethods: [cardPaymentMethod],
//         transactionInfo: {
//           totalPriceStatus: 'FINAL',
//           totalPrice: '10.00',
//           currencyCode: 'USD',
//           countryCode: 'US',
//         },
//         merchantInfo: {
//           merchantId: '01234567890123456789', // Optional in TEST
//           merchantName: 'Example Merchant',
//         },
//       });

//       // Check if Google Pay is ready
//       paymentsClient
//         .isReadyToPay({
//           apiVersion: 2,
//           apiVersionMinor: 0,
//           allowedPaymentMethods: [baseCardPaymentMethod],
//         })
//         .then(function (response) {
//           if (response.result) {
//             const button = paymentsClient.createButton({
//               onClick: () => {
//                 paymentsClient.loadPaymentData(paymentDataRequest)
//                   .then(paymentData => {
//                     console.log('Google Pay Success:', paymentData);
//                     // 👉 Process the payment token with your server here
//                   })
//                   .catch(err => {
//                     console.error('Google Pay Failed:', err);
//                   });
//               },
//             });

//             if (googlePayRef.current && !googlePayRef.current.hasChildNodes()) {
//               googlePayRef.current.appendChild(button);
//             }
//           } else {
//             console.warn('Google Pay not available');
//           }
//         })
//         .catch(err => {
//           console.error('isReadyToPay error:', err);
//         });
//     };

//     document.body.appendChild(script);
//   }, []);

//   return (
//     <div>
//       <div ref={googlePayRef} id="googlepay-button-container"></div>
//     </div>
//   );
// };

// export default GooglePayButton;
/* eslint-disable react/prop-types */
import { useEffect } from "react";

const GooglePayButton = ({ total_amount, handleSubmitOrder }) => {
  useEffect(() => {
    const script = document.createElement("script");
    script.src = "https://pay.google.com/gp/p/js/pay.js";
    script.async = true;
    script.onload = () => {
      if (window.google) {
        onGooglePayLoaded();
      }
    };
    document.body.appendChild(script);
  }, []);

  const getGooglePaymentsClient = () => {
    return new window.google.payments.api.PaymentsClient({
      environment: "TEST",
      // environment: "PRODUCTION",
    });
  };

  const onGooglePayLoaded = () => {
    addGooglePayButton();
  };

  const addGooglePayButton = () => {
    const paymentsClient = getGooglePaymentsClient();

    const buttonOptions = {
      onClick: onGooglePaymentButtonClicked,
    };

    const button = paymentsClient.createButton(buttonOptions);

    const container = document.getElementById("gpay-button-container");
    container.innerHTML = "";
    container.appendChild(button);
  };

  const getRequest = () => {
    const allowedCardNetworks = [
      "AMEX",
      "DISCOVER",
      "INTERAC",
      "JCB",
      "MASTERCARD",
      "VISA",
    ];

    const googlePayConfig = {
      apiVersion: 2,
      apiVersionMinor: 0,
    };

    const paymentDataRequest = { ...googlePayConfig };

    paymentDataRequest.transactionInfo = {
      totalPriceStatus: "FINAL",
      totalPrice: String(total_amount),
      currencyCode: "USD",
      countryCode: "US",
    };

    paymentDataRequest.merchantInfo = {
      // merchantId: "MQ78CLCRTAYZL",
      merchantId: "MQ78CLCRTAYZL",
      merchantName: "Paypal",
    };

    const tokenizationSpec = {
      type: "PAYMENT_GATEWAY",
      parameters: {
        gateway: "authorizenet",
        gatewayMerchantId: "804131",
      },
    };

    const cardPaymentMethod = {
      type: "CARD",
      tokenizationSpecification: tokenizationSpec,
      parameters: {
        allowedCardNetworks,
        allowedAuthMethods: ["PAN_ONLY", "CRYPTOGRAM_3DS"],
      },
    };

    paymentDataRequest.shippingAddressRequired = false;
    paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];

    return paymentDataRequest;
  };

  const onGooglePaymentButtonClicked = () => {
    const paymentDataRequest = getRequest();

    const paymentsClient = getGooglePaymentsClient();
    paymentsClient
      .loadPaymentData(paymentDataRequest)
      .then((paymentData) => {
        const finaldata = {
          id: paymentData.paymentMethodData.tokenizationData.token,
        };
        handleSubmitOrder(finaldata);
      
      })
      .catch((error) => {
        console.error("Google Pay Error", error);
      });
  };

  return (
    <div>
      <div
        id="gpay-button-container"
        // style={{ width: "400px", height: "40px" }}
      ></div>
    </div>
  );
};

export default GooglePayButton;
