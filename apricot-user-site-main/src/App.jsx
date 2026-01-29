import "bootstrap/dist/css/bootstrap.min.css";
import "react-toastify/dist/ReactToastify.css";
import "./App.css";
import AppRouter from "./Router/AppRouter";
import "./Assets/css/style.css";
import { CookiesProvider } from "react-cookie";

import { Helmet, HelmetProvider } from "react-helmet-async";
import { ToastContainer } from "react-toastify";

import { Provider } from "react-redux";
import { store } from "./redux/store";

import { PayPalScriptProvider } from "@paypal/react-paypal-js";
import { CartProvider } from "./Context/CartContext";
// import { useEffect } from "react";

const helmetContext = {};

function App() {
  // useEffect(() => {
  //   const script = document.createElement("script");
  //   script.type = "text/javascript";
  //   script.async = true;
  //   script.src =
  //     "https://static.klaviyo.com/onsite/js/UQqSAC/klaviyo.js";
  //   script.onload = () => {
  //     window._learnq = window._learnq || [];
  //     window._learnq.push(["track", "Active on Site Hello world"]);
  //   };
  //   document.head.appendChild(script);

  // }, []);

  return (
    <>
      <HelmetProvider context={helmetContext}>
        <Helmet>
          <link
            rel="canonical"
            href={`https://staging.apricotpower.com${window.location.pathname}`}
          />
        </Helmet>
        <CartProvider>
          <Provider store={store}>
            <CookiesProvider>
              <PayPalScriptProvider
                options={{
                  "client-id":
                    // "AVIt6iioPGgh0DRvW3F89wcHwR4xYi75Gx7Gs7NlZernsq86hcYU1HXdRgnCtqf9aeaEFyUeLCOwLKFy", //Live
                   "AY04xvfN1VYDQHy4nIxwnTqhGBEzqN3-WJ0bntuM-yzFX344u-Wx7_-HHopQ3j2hGt3hiVBncjpsPBpA", //  Testing--->
                  // "buyer-country": "US",

                  // "merchant-id": "MQ78CLCRTAYZL",  //Live
                  "merchant-id": "ML2G5TMZAFFAN", //  Testing--->
                  currency: "USD",
                  components: "googlepay,buttons,funding-eligibility",
                   enableFunding: "venmo,paylater,applepay"
                }}
              >
                <AppRouter />
                <ToastContainer />
              </PayPalScriptProvider>
            </CookiesProvider>
          </Provider>
        </CartProvider>
      </HelmetProvider>
    </>
  );
}

export default App;
