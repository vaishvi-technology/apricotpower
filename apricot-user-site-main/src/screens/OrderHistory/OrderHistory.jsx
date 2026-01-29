import React, { useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { Card, Container, Row } from "react-bootstrap";
import { base_url } from "../../api";
import CustomTable from "../../components/CustomTable";
import { Link } from "react-router-dom";
// import { Link } from "react-router-dom";

const OrderHistory = () => {
  const [orders, setOrders] = useState([]);
  const token = localStorage.getItem("login");

  const fetchOrders = async () => {
    document.querySelector(".loaderBox").classList.remove("d-none");

    try {
      const response = await fetch(`${base_url}/orders`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });

      if (!response.ok) {
        document.querySelector(".loaderBox").classList.add("d-none");

        throw new Error(`HTTP error! status: ${response.status}`);
      }
      document.querySelector(".loaderBox").classList.add("d-none");

      const data = await response.json();
      setOrders(data || []);
    } catch (error) {
      document.querySelector(".loaderBox").classList.add("d-none");

      console.log("Error fetching orders:", error);
    }
  };
  console.log(orders);

  useEffect(() => {
    fetchOrders();
  }, []);

  const maleHeaders = [
    // {
    //   key: "id",
    //   title: "S.No",
    // },
    {
      key: "no",
      title: "Order no./date",
    },
    {
      key: "details",
      title: "Details",
    },
    {
      key: "source",
      title: "Source",
    },
    {
      key: "payment",
      title: "Payment",
    },
    {
      key: "shipping-status",
      title: "Shipping Status",
    },
  ];
  useEffect(() => {
    document.title = "Order History | Apricot Power";
  }, []);

  return (
    <DefaultLayout>
      <InnerBanner boldText1="Order History" />

      {orders?.length === 0 ? (
        <Container className="mt-4 mb-5  " style={{ width: "60%" }}>
          <Card className="p-3 shadow">
            <h3 className="text-theme text-center">
              You have no orders{" "}
              <Link to={'/store'} className="greenColor">Start shopping now!.</Link>
            </h3>
          </Card>
        </Container>
      ) : (
        <>
          <div className="row mb-3 p-5">
            <div className="col-md-12">
              <div className="dashboard-table">
                <CustomTable headers={maleHeaders}>
                  <tbody>
                    {(orders || []).map((item, index) => (
                      <tr key={index}>
                        {/* <td>{index + 1}</td> */}
                        <td className="text-capitalize">
                          <Link
                            to={`/orderHistory/${item?.id}`}
                            className="greenColor"
                          >
                            #{item?.id}
                          </Link>
                          <br />
                          <span> {item?.created_at}</span>
                        </td>
                        <td>
                          <span>
                            {item?.first_name} {item?.last_name}
                          </span>
                          <br />
                          <span>Order Total: ${item?.total_amount}</span>
                        </td>
                        <td>Phone {item?.phone}</td>
                        <td>
                          <span>Payment Status: {item?.payment_status}</span>
                          <br />
                          <span>Via {item?.payment_method}</span>
                        </td>
                        <td>
                          <span>Order Status: {item?.order_status}</span>
                          <br />
                          <span>Discount: {item?.discount_amount}</span>
                          <br />
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </CustomTable>
              </div>
            </div>
          </div>
        </>
      )}
    </DefaultLayout>
  );
};

export default OrderHistory;
