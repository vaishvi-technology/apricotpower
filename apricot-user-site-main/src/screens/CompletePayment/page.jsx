import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { Link } from "react-router-dom"; // Use this if you're using react-router

export default function OrderPlacedPage() {
  return (
    <DefaultLayout>
      <InnerBanner boldText="Order" />
      <div className="container my-5">
        <div className="text-center">
          <img
            src="https://cdn-icons-png.flaticon.com/512/845/845646.png"
            alt="Success"
            style={{ width: "100px", marginBottom: "20px" }}
          />
          <h2 className="text-success">Your Order Has Been Placed!</h2>
          <p className="lead">
            Thank you for your purchase. A confirmation email has been sent to
            you.
          </p>
          <div className="my-4">
            {/* <Link to="/orders" className="btn btn-primary mx-2">
              View My Orders
            </Link> */}
            <Link to="/" className="btn btn-outline-secondary mx-2">
              Continue Shopping
            </Link>
          </div>
        </div>
      </div>
    </DefaultLayout>
  );
}
