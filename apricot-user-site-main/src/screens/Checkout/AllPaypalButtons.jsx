/* eslint-disable react/prop-types */
import { PayPalButtons, usePayPalScriptReducer } from "@paypal/react-paypal-js";
import { useEffect, useState } from "react";
import GooglePayButton from "./GooglePayButton";

export const PayPalMultiButtons = ({ total_amount, handleSubmitOrder }) => {
  const [{ isResolved }] = usePayPalScriptReducer();
  const [eligibility, setEligibility] = useState({
    paypal: false,
    venmo: false,
    googlepay: false,
    applepay: false,
  });

  useEffect(() => {
    if (isResolved && window?.paypal?.Buttons) {
      setEligibility({
        paypal: true, 
      
        venmo:
          window.paypal.Buttons({ fundingSource: "venmo" })?.isEligible() ||
          false,
        googlepay:
          window.paypal.Buttons({ fundingSource: "googlepay" })?.isEligible() ||
          false,
        applepay:
          window.paypal.Buttons({ fundingSource: "applepay" })?.isEligible() ||
          false,
      });
    }
  }, [isResolved]);

  if (!isResolved) return <p>Loading payment buttons...</p>;

  const commonProps = {
    style: { layout: "vertical" },
    createOrder: (data, actions) =>
      actions.order.create({
        purchase_units: [{ amount: { value: total_amount?.toFixed(2) } }],
      }),
    onApprove: (data, actions) =>
      actions.order.capture().then((details) => handleSubmitOrder(details)),
  };

  return (
    <div>
      <h2>Choose a Payment Method:</h2>

      {eligibility.paypal && <PayPalButtons  {...commonProps}  />}
      <GooglePayButton  total_amount={total_amount}  handleSubmitOrder={handleSubmitOrder}/>

    </div>
  );
};
