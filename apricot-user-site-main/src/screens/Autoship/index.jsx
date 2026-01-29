import { useEffect } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";

export default function AutoShip() {
  useEffect(() => {
    document.title = "Auto Shipments | Apricot Power";
  }, []);
  return (
    <DefaultLayout>
      <InnerBanner boldText="Auto-shipments" small={true} />

      <div className="container my-5">
        <h2 className="text-theme">
          Sign up now and get free shipping on all auto-shipped orders!
        </h2>
        <br />

        <h2 className="text-theme">
          Worried about committing to automatic shipments?
        </h2>
        <p>
          Don’t be! You can update that online, or call us anytime to change
          your shipment date, frequency, or even the products in your order
        </p>
        <h2 className="text-theme">Don't let inflation affect you! </h2>
        <p>
          The cost of living can fluctuate a lot in today’s economy, and prices
          tend to raise over time. At Apricot Power we do our very best to keep
          our prices steady for as long as possible, but over the years we may
          need to adjust our pricing so that we can continue to provide you with
          quality products for many more years to come.
          <br />
          <br />
          However, when you sign up for our auto-ship program you’ll be locked
          into the price you pay today for as long as you have an active
          autoshipments with us, regardless of the change in market prices.
        </p>
        
        <h2 className="text-theme">What are you waiting for? </h2>
        <p>
          Call us at <b>866-468-7487</b> now to get started or select your
          products and choose auto-shipment during your normal checkout process.
          <br />
          <br />
          *Offer not currently applicable for wholesale accounts or accounts
          outside of the United States
        </p>
      </div>
    </DefaultLayout>
  );
}
