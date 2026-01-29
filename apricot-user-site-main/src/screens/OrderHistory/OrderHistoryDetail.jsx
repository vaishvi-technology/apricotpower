import { useEffect, useState } from "react";
import CustomTable from "../../components/CustomTable";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import PdfView from "./PdfView";
import { useParams } from "react-router-dom";
import { getOrderDetail } from "../../api/Services/orderServices";

export default function OrderHistoryDetail() {
  const [show, setShow] = useState(false);
  const [data, setData] = useState([]);
  const { id } = useParams();
  const inventoryHeaders = [
    { key: "item", title: "Item" },
    { key: "qty", title: "Qty" },
    { key: "price", title: "Price" },
  ];

  const fetchOrderDetail = async () => {
    document.querySelector(".loaderBox").classList.remove("d-none");
    try {
      const data = await getOrderDetail(id);
      document.querySelector(".loaderBox").classList.add("d-none");
      setData(data);
    } catch (error) {
      document.querySelector(".loaderBox").classList.add("d-none");
      console.log(error);
    }
  };
  useEffect(() => {
    fetchOrderDetail();
  }, []);
  return (
    <DefaultLayout>
      <InnerBanner boldText1={`Order Detail #${id}`} />
      <div className="container mt-5">
        <div className="row mb-4">
          <div className="col-12 text-center">
            <h4
              className="greenColor lemonMilk-med mb-3 cursor-pointer"
              onClick={() => setShow(true)}
            >
              View Printable Receipt
            </h4>
          </div>
        </div>

        <div className="row mb-5">
          <div className="col-12">
            <div className="dashboard-table checkout-assist-table">
              <CustomTable headers={inventoryHeaders}>
                <tbody>
                  {(data?.products || []).map((item, index) => (
                    <tr key={index}>
                      <td>{item?.product_name}</td>
                      <td>{item?.quantity}</td>
                      <td>${item?.price.toFixed(2)}</td>
                    </tr>
                  ))}
                </tbody>
              </CustomTable>
            </div>
          </div>
        </div>

        <div className="row mb-5">
          <div className="col-md-6">
            <p className="poppins blackColor mb-2">
              <span className="lemonMilk-med greenColor">Promo Used:</span>{" "}
              {data?.promo || "N/A"}
            </p>
            <p className="poppins blackColor mb-2">
              <span className="lemonMilk-med greenColor">Order Source:</span>{" "}
              {data?.CartOrderType || "N/A"}
            </p>

            <p className="poppins blackColor mb-2">
              <span className="lemonMilk-med greenColor">Payment Method:</span>{" "}
              {data?.payment_method
                ? `${data.payment_method} #${data.payment_card}`
                : "N/A"}
            </p>
          </div>

          <div className="col-md-6">
            <p className="poppins blackColor mb-2">
              <span className="lemonMilk-med greenColor">
                Shipping Contact:
              </span>{" "}
              {data?.shipping_address
                ? `${data.shipping_address.address}, ${data.shipping_address.city}, ${data.shipping_address.state_name}, ${data.shipping_address.country_name}`
                : "N/A"}
            </p>
            <p className="poppins blackColor mb-2">
              <span className="lemonMilk-med greenColor">Email:</span>{" "}
              {data?.shipping_address?.email || "N/A"}
            </p>
            <p className="poppins blackColor mb-2">
              <span className="lemonMilk-med greenColor">Phone:</span>{" "}
              {data?.shipping_address?.phone || "N/A"}
            </p>
            <p className="poppins blackColor mb-2">
              <span className="lemonMilk-med greenColor">Subtotal:</span>{" "}
              {data?.sub_total || "N/A"}
            </p>
            <p className="poppins blackColor mb-2">
              <span className="lemonMilk-med greenColor">Shipping:</span>{" "}
              {data?.shipping_fee || "N/A"}
            </p>
            <p className="poppins blackColor mb-2">
              <span className="lemonMilk-med greenColor">Tax:</span>{" "}
              {"$"+data?.tax_amount || "N/A"}
            </p>
         
            <p
              className="lemonMilk-med blackColor mb-2 "
              style={{ fontSize: "20px" }}
            >
            <span className="greenColor">  Total: </span> ${data?.total_amount}
            </p>
          </div>
        </div>
      </div>
      <PdfView setShow={setShow} show={show} data={data} />
    </DefaultLayout>
  );
}
