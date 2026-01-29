/* eslint-disable react/prop-types */

import moment from "moment";
import CustomModal from "../../components/CustomModal";
import { logo } from "../../assets/images";
import CustomButton from "../../components/CustomButton";

export default function PdfView({ setShow, show, setEditItem, data }) {
  const handlePrint = () => {
    window.print();
  };

  return (
    <CustomModal
      show={show}
      ischild={true}
      close={() => {
        setShow(false);
        setEditItem(true);
      }}
      size="lg"
      heading="View Printable Receipt"
    >
      <div className="container px-4 py-4" id="print-area">
        <div className="text-end mb-3 d-print-none">
          <CustomButton
            variant="primaryButton"
            onClick={handlePrint}
            text=" 🖨️ Print Receipt "
          />
        </div>
        <div className="d-flex justify-content-between align-items-start mb-4 flex-column flex-md-row">
          <div>
            <img src={logo} alt="Logo" height="60" />
          </div>
          <div className="text-end">
            <p className="mb-0">www.apricotpower.com</p>
            <p className="mb-0">customerservice@apricotpower.com</p>
            <p className="mb-0">13501 Ranch Road 12, Ste 103</p>
            <p className="mb-0">Wimberley, Tx. 78676</p>
            <p className="mb-0">(866) GOT PITS</p>
            <p className="mb-0">(866) 468 7487</p>
          </div>
        </div>

        <h5 className="fw-bold">Receipt for Order #{data?.id}</h5>
        <p className="mb-1">
          <strong>Account:</strong> #{data?.user_id}
        </p>
        <p className="mb-1">
          <strong>Order Date:</strong>{" "}
          {moment(data?.CartCheckoutDate).format("MM/DD/YYYY ")}
        </p>
        <p className="mb-3">
          <strong>Ship Date:</strong>{" "}
          {data?.shipping_date
            ? moment(data?.shipping_date).format("MM/DD/YYYY ")
            : "N/A"}
        </p>
        <p className="mb-3">
          Thank you for your order!
          <br />
          An email receipt has been sent to you at{" "}
          <strong>{data?.shipping_email}</strong>.
        </p>

        <table className="table table-bordered">
          <thead>
            <tr>
              <th>Item Name</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Item Total</th>
            </tr>
          </thead>
          <tbody>
            {data?.products?.map((e) => {
              return (
                <tr key={e?.id}>
                  <td>{e?.product_name}</td>
                  <td>{e?.quantity}</td>
                  <td>${e?.price}</td>
                  <td>${e?.subtotal}</td>
                </tr>
              );
            })}
          </tbody>
        </table>
        {/* 
        <p>
          <strong>Promo Used:</strong> POWER20 - 20% off First Order
        </p> */}
        <div className="text-end">
          <p className="mb-1">Subtotal: ${data?.sub_total}</p>
          <p className="mb-1">Shipping: ${data?.shipping_fee}</p>
          <p className="mb-1">Tax: ${data?.tax_amount}</p>
          <h5>
            <strong>Total: ${data?.total_amount}</strong>
          </h5>
        </div>

        <div className="mt-4">
          <h6 className="fw-bold">Shipped & Billed To:</h6>
          <p className="mb-1">
            {data?.shipping_first_name} {data?.shipping_last_name}
          </p>
          <p className="mb-1">{data?.shipping_address?.address}</p>
          <p className="mb-1">{data?.shipping_address?.shipping_city}</p>
          <p className="mb-1">Phone: {data?.shipping_address?.phone}</p>
          <p className="mb-1">Email: {data?.shipping_address?.email}</p>
        </div>

        <div className="mt-4 d-flex justify-content-between">
          <p className="mb-0">
            Paid via
            <br />
            <strong>{data?.payment_method}</strong>
          </p>
          <p className="mb-0">
            Shipped via
            <br />
            <strong>Standard Shipping</strong>
          </p>
        </div>
      </div>
    </CustomModal>
  );
}
