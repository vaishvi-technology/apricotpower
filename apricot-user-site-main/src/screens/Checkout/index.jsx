/* eslint-disable react/no-unescaped-entities */
/* eslint-disable react-hooks/exhaustive-deps */
import { useContext, useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import {
  Form,
  Row,
  Col,
  Button,
  Card,
  Table,
  Container,
} from "react-bootstrap";
import { base_url } from "../../api";
import { toast } from "react-toastify";
import InputMask from "react-input-mask";
import bitCoin from "../../assets/images/bitCoin.png";
import paypalImages from "../../assets/images/paypalImages.png";
import { PayPalButtons } from "@paypal/react-paypal-js";
import { useNavigate } from "react-router-dom";
import { SelectBox } from "../../components/CustomSelect";
import { FaPencil } from "react-icons/fa6";
import { FaTrash } from "react-icons/fa";
import EditShippingAdress from "./EditShippingAdress";
import {
  detectCardType,
  getPaymentOptions,
  subscriptionFrequencies,
} from "./data";
import { CustomSelectAutoShip } from "../../components/CustomSelectAutoShip";
import { PayPalMultiButtons } from "./AllPaypalButtons";
import { getCoinBaseUrl } from "../../api/Services/orderServices";
import { IPInfoContext } from "ip-info-react";
import CustomButton from "../../components/CustomButton";
import SideBar from "./SideBar";
import { CartContext } from "../../Context/CartContext";
import { upSellProduct } from "../../api/Services/productServices";
import { trackKlaviyoEvent } from "../../utils/klaviyo";
import visa from "../../assets/images/visa.png";
import mastercard from "../../assets/images/mastercard.png";
import discover from "../../assets/images/discover.png";
import amex from "../../assets/images/amex.png";
import CreditCard from "./CreditCard";

const Checkout = () => {
  const [cartItems, setCartItems] = useState([]);
  const navigate = useNavigate();
  const { fetchCount } = useContext(CartContext);
  const [loading, setloading] = useState(false);
  const [loadingCount, setLoadingCount] = useState(0);
  const [showOfCanvas, setShowOfCanvas] = useState(false);
  const [loadingbtn, setLoadingBtn] = useState(false);
  const [data, setdata] = useState([]);

  const [addShippingAddress, setAddShippingAddress] = useState(false);
  const token = localStorage.getItem("login");
  const [paymentMethod, setPaymentMethod] = useState("credit_card");
  const [billingSameAsShipping, setBillingSameAsShipping] = useState(true);
  const userInfo = useContext(IPInfoContext);
  const [formData, setFormData] = useState({
    first_name: "",
    transaction_token: "",
    ip: "",
    billing_id: null,
    comments: "",
    auto_ship_doration: "",
    tax_amount: 0,
    last_name: "",
    refferal_code: "",
    new_credit_card: true,
    shipping_id: "",
    discount_amount: cartItems.discount,
    shipping_fee: 0,
    total_amount: cartItems.total,
    address: "",
    city: "",
    state_id: null,
    same_as_shipping_address: true,
    postal_code: "",
    phone: "",
    company: "",
    email: "",
    type: "shipping",
    payment_method: "credit_card",
    payment_status: "",
    order_status: "processing",
  });

  const showLoader = () => setLoadingCount((prev) => prev + 1);
  const hideLoader = () => setLoadingCount((prev) => Math.max(prev - 1, 0));

  // useEffect(() => {
  //   if (paymentMethod) {
  //     setFormData((prev) => ({
  //       ...prev,
  //       payment_method: paymentMethod,
  //     }));
  //   }
  // }, [paymentMethod]);
  useEffect(() => {
    if (userInfo) {
      setFormData((prev) => ({
        ...prev,
        ip: userInfo.ip,
      }));
      console.log("render");
    }
  }, [userInfo?.ip]);

  const referalCode = localStorage.getItem("referalCode");
  useEffect(() => {
    if (referalCode) {
      setFormData({
        ...formData,
        refferal_code: referalCode,
      });
    }
  }, [referalCode]);
  // console.log(formData)
  const tax_amount = 0;
  const discount_amount = cartItems.discount;

  const cartTotal = cartItems?.items?.reduce(
    (acc, item) => acc + item?.qty * item?.price_per_product,
    0
  );
  const [shippingPrice, setShippingPrice] = useState({});
  const [credits, setCredits] = useState({});
  const [isWholseller, setisWholseller] = useState(false);
  let total_amount =
    (cartItems.sub_total || 0) +
    (shippingPrice?.shipping_service_rate || 0) -
    (discount_amount || 0);

  let remainingCredits = credits;

  if (formData.payment_method === "credits") {
    total_amount = Math.max(0, total_amount - credits);

    remainingCredits = Math.max(
      0,
      credits -
        ((cartItems.total || 0) +
          (shippingPrice?.shipping_service_rate || 0) -
          (discount_amount || 0))
    );
  }

  const [billingAddress, setBillingAddress] = useState([]);
  const [shippingAddress, setShippingAddress] = useState([]);
  const [states, setStates] = useState([]);
  const [agrement, setAgrement] = useState(false);
  const [step, setStep] = useState(1);
  const [selectedAddressId, setSelectedAddressId] = useState(
    shippingAddress?.find((address) => address?.is_primary)?.id || null
  );

  const [selectedAddress, setSelectedAddress] = useState(
    shippingAddress?.find((address) => address?.is_primary) || null
  );

  const handleSelect = (id) => {
    setSelectedAddressId(id);
    setFormData({
      ...formData,
      shipping_id: id,
    });
  };

  const dataShip = () => {
    if (!agrement && step == 2 && formData?.auto_shipping) {
      toast.error(
        "Please check the box to confirm that you have read, understand, and agree."
      );
      return;
    }
    if (
      agrement &&
      step == 2 &&
      formData?.auto_shipping &&
      formData.auto_ship_doration == ""
    ) {
      toast.error("Please Select Receive this order every.");
      return;
    }
    if (selectedAddressId) {
      setStep(step + 1);
    } else {
      toast.error("Please Select the Shipping Address");
    }
  };

  const fetchProducts = () => {
    const queryParams = new URLSearchParams();
    if (referalCode) {
      queryParams.append("refferal_code", referalCode);
    }
    showLoader();
    fetch(`${base_url}/cart/?${queryParams.toString()}`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => response.json())
      .then((data) => {
        hideLoader();
        setCartItems(data || []);
        if (data?.is_customer) {
          setFormData((prev) => ({
            ...prev,
            email: data?.email,
          }));
        }
      })
      .catch((error) => {
        hideLoader();
      });
  };

  // ==========================fetch Address=======================
  const fetchInfo = () => {
    showLoader();
    fetch(`${base_url}/user-shipping-address/`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => response.json())
      .then((data) => {
        hideLoader();

        setShippingAddress(data);
        setFormData((prev) => ({
          ...prev,
          shipping_id: data?.find((address) => address?.is_primary)?.id || null,
        }));
        setSelectedAddressId(
          data?.find((address) => address?.is_primary)?.id || null
        );
        setSelectedAddress(
          data?.find((address) => address?.is_primary) || null
        );
      })
      .catch((error) => {
        hideLoader();
      });
  };

  useEffect(() => {
    fetchProducts();
    fetchCredit();
    fetchpaymentAddress();
    fetchInfo();
  }, [referalCode]);

  const handleSubmitOrder = async (details) => {
    // e.preventDefault();
    const finaldata = {
      ...formData,
      transaction_token: details?.id,
      points: remainingCredits,
      total_points: credits,
    };
    const check =
      formData.payment_method === "credit_card" ? formData : finaldata;
    try {
      showLoader();

      const res = await fetch(`${base_url}/checkout`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },

        body: JSON.stringify(check),
      });
      hideLoader();

      const data = await res.json();
      if (res.ok) {
        localStorage.removeItem("referalCode");
        // // toast.success(data?.message);;
        navigate("/complete-payment");
      } else {
        toast.error(data?.message);
      }
    } catch (err) {
      hideLoader();
      toast.error(err?.message);
      console.error("Order failed:", err);
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  // ================================= submit new address=====================
  const handleSubmitAddress = async (e) => {
    e.preventDefault();

    try {
      showLoader();

      const res = await fetch(`${base_url}/add/shipping-address`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });
      hideLoader();
      const data = await res.json();

      if (data?.errors) {
        toast.error(data?.errors);
      } else {
        // toast.success(data?.message);;
        setFormData((prev) => ({
          ...prev,
          first_name: "",
          last_name: "",
          company: "",
          email: "",
          address: "",
          city: "",
          state_id: "",
          country_id: 231,
          postal_code: "",
          phone: "",
        }));
        fetchInfo();
      }
      console.log(data);
    } catch (err) {
      hideLoader();
      console.log(err);
      // console.log("Order failed:", err);
      // alert("Something went wrong!");
    }
  };

  // ================================= Delete address=====================
  const deleteAddress = async (id) => {
    try {
      showLoader();

      const res = await fetch(`${base_url}/delete/shipping-address`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ address_id: id }),
      });
      hideLoader();

      const data = await res.json();
      // toast.success(data?.message);;
      fetchInfo();
    } catch (err) {
      hideLoader();

      console.log("Order failed:", err);
      alert("Something went wrong!");
    }
  };

  const deletebillingAddress = async (id) => {
    try {
      showLoader();

      const res = await fetch(`${base_url}/delete/payment-account`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ id: id }),
      });
      hideLoader();

      const data = await res.json();
      // toast.success(data?.message);;
      fetchpaymentAddress();
    } catch (err) {
      hideLoader();

      console.log("Order failed:", err);
      alert("Something went wrong!");
    }
  };

  const handlePrimaryAddress = async (id) => {
    try {
      showLoader();

      const res = await fetch(`${base_url}/primary/shipping-address`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ address_id: id }),
      });
      hideLoader();

      const data = await res.json();
      // toast.success(data?.message);;
      fetchInfo();
    } catch (err) {
      hideLoader();

      console.log("Order failed:", err);
      alert("Something went wrong!");
    }
  };
  const [countries, setCountries] = useState([]);
  const fetchCountries = async () => {
    try {
      showLoader();
      const response = await fetch(`${base_url}/countries`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });
      const data = await response.json();
      setCountries(data);
      setFormData((prev) => ({
        ...prev,
        country_id: data?.[0]?.id,
      }));
    } catch (error) {
      console.error(error);
    } finally {
      hideLoader();
    }
  };
  useEffect(() => {
    fetchCountries();
  }, []);
  const fetchStates = async (countryId) => {
    try {
      showLoader();
      const response = await fetch(`${base_url}/state/${countryId}`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });
      const data = await response.json();
      setStates(data);
    } catch (error) {
      console.error(error);
    } finally {
      hideLoader();
    }
  };

  const fetchpaymentAddress = () => {
    showLoader();
    fetch(`${base_url}/user-billing-address`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => response.json())
      .then((data) => {
        hideLoader();

        setBillingAddress(data);
        const filter = data?.find((e) => e?.is_primary == 1);

        if (filter?.id) {
          setFormData((prev) => ({
            ...prev,
            billing_id: filter?.id,
            payment_method: "",
            new_credit_card: false,
          }));
        }
      })
      .catch((error) => {
        console.log(error);
        hideLoader();
      });
  };

  const handleCountryChange = (e) => {
    const { value, name } = e.target;

    fetchStates(value);

    setFormData((prevData) => ({
      ...prevData,
      [name]: value,
    }));
  };

  const HandleEdit = () => {
    setAddShippingAddress(true);
  };
  const handleAutoship = (e) => {
    if (!formData.auto_shipping) {
      toast.error("AUTO-SHIP CHECKED FIRST");
    } else {
      const [freqType, freq] = e.target.value.split("|");
      setFormData((prev) => ({
        ...prev,
        auto_ship_doration: { freqType, freq },
      }));
    }
  };

  const HandleCoin = async () => {
    showLoader();
    try {
      const response = await getCoinBaseUrl();
      if (response?.status == true) {
        window.open(response?.hosted_url, "_blank");
        hideLoader();
      }
    } catch (error) {
      console.log(error);
      hideLoader();
    }
  };
  const handleCloseOfCanvas = () => setShowOfCanvas(false);
  const handleShowOfCanvas = () => setShowOfCanvas(true);
  const addToCart = async (id, type, qty = 1, item) => {
    trackKlaviyoEvent("Added to Cart", item);
    if (type === "addtocard") {
      setLoadingBtn(id);
    }

    const payload = {
      product_id: id,
      qty,
    };

    try {
      if (token === null) {
        const upgradeFormdata = { ...payload, ip: userInfo?.ip };
        const response = await fetch(`${base_url}/cart/guest/add`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(upgradeFormdata),
        });

        const guestData = await response.json();

        if (response.ok) {
          fetchCount();
          navigate("/cart");
          fetchProducts();
          // toast.success(
          //   guestData?.message || "Added to guest cart based on IP."
          // );
          if (type === "buy" && !token) {
            navigate("/store/customer_info");
          }
        } else {
          toast.error(guestData?.message || "Guest cart failed.");
        }

        hideLoader();
        return;
      }

      const response = await fetch(`${base_url}/cart/add`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(payload),
      });

      const responseData = await response.json();
      if (responseData?.status === 200) {
        fetchCount();
        navigate("/cart");
        fetchProducts();
        if (type === "buy" && token) {
          navigate("/checkout");
        }
        // toast.success(responseData?.message);
        // fetchProducts();
      } else {
        toast.error(responseData?.message || "Failed to add to cart.");
      }
    } catch (error) {
      console.error("Cart error:", error);
      toast.error("Something went wrong.");
    } finally {
      setLoadingBtn(null);
      hideLoader();
    }
  };
  const getupSellProduct = async () => {
    try {
      const response = await upSellProduct();
      setdata(response);
    } catch (error) {
      console.log(error);
    }
  };
  useEffect(() => {
    getupSellProduct();
  }, []);
  useEffect(() => {
    trackKlaviyoEvent("Started Checkout", {
      //   Items: cartItems.map((item) => ({
      //     ProductName: item.name,
      //     ProductID: item.id,
      //     Price: item.price,
      //     Quantity: item.quantity,
      //   })),
      // });
      Items: cartItems,
    });
  }, [cartItems]);
  const fetchShiipingPrice = async (shippingId, isAutoship = false) => {
    showLoader();
    try {
      const response = await fetch(
        `${base_url}/shipingStation/getRate/${shippingId}?isAutoship=${isAutoship} `,
        {
          method: "GET",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (!response.ok) {
        throw new Error("Failed to fetch shipping price");
      }

      const responseData = await response.json();

      if (responseData) {
        hideLoader();
        setShippingPrice(responseData);
      }
    } catch (error) {
      console.error("Error:", error);
    }
  };
  const fetchCredit = async () => {
    // showLoader();
    try {
      const response = await fetch(`${base_url}/get/credits/total`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });

      if (!response.ok) {
        throw new Error("Failed to fetch shipping price");
      }

      const responseData = await response.json();

      if (responseData?.status) {
        hideLoader();
        setCredits(responseData?.data);
        setisWholseller(responseData?.net_thirty);
      }
    } catch (error) {
      console.error("Error:", error);
    }
  };

  useEffect(() => {
    if (selectedAddress?.id) {
      setFormData((prev) => ({
        ...prev,
        shipping_id: selectedAddress.id,
      }));
      fetchShiipingPrice(selectedAddress.id, false);
    }
  }, [selectedAddress]);
  useEffect(() => {
    if (formData.country_id) {
      fetchStates(formData?.country_id);
    }
  }, [formData?.country_id]);
  const paymentOptions = getPaymentOptions({ amount: credits });
  useEffect(() => {
    if (formData.auto_shipping) {
      fetchShiipingPrice(selectedAddress?.id, true);
    } else {
      fetchShiipingPrice(selectedAddress?.id, false);
    }
  }, [formData.auto_shipping]);
  const checkCardType = detectCardType(formData?.card_number);

  return (
    <>
      <div className={`loaderBox ${loadingCount > 0 ? "" : "d-none"}`}>
        <div className="custom-loader"></div>
      </div>

      <DefaultLayout>
        <InnerBanner boldText="Checkout" />
        <Container className="my-5 ">
          <Row className="d-flex justify-content-center">
            {/* <div className="container mb-4">
              <div className="row align-items-center">
                <div className="col-6 col-md-3 mb-2 mb-md-0">
                  <CustomButton
                    text={"We think you may also like"}
                    variant="primaryButton"
                    onClick={handleShowOfCanvas}
                    className="w-100"
                  />
                </div>
              </div>
            </div> */}
            <SideBar
              handleCloseOfCanvas={handleCloseOfCanvas}
              handleShowOfCanvas={handleShowOfCanvas}
              showOfCanvas={showOfCanvas}
              products={data}
              loadingbtn={loadingbtn}
              handleCart={addToCart}
            />
            <ul className="nav nav-tabs mb-4 checkoutTabs" role="tablist">
              <li className="nav-item" role="presentation">
                <button
                  className={`nav-link tab-checkout `}
                  onClick={() => navigate("/cart")}
                  type="button"
                >
                  Shopping Cart
                </button>
              </li>

              <li className="nav-item" role="presentation">
                <button
                  className={`nav-link tab-checkout ${
                    step === 1 ? "active" : ""
                  }`}
                  onClick={() => setStep(1)}
                  type="button"
                >
                  Customer Information
                </button>
              </li>

              <li className="nav-item" role="presentation">
                <button
                  className={`nav-link tab-checkout ${
                    step === 2 ? "active" : ""
                  }`}
                  onClick={() => setStep(2)}
                  type="button"
                >
                  Shipping Method
                </button>
              </li>

              <li className="nav-item" role="presentation">
                <button
                  className={`nav-link tab-checkout ${
                    step === 3 ? "active" : ""
                  }`}
                  onClick={() => setStep(3)}
                  type="button"
                >
                  Payment Method
                </button>
              </li>
            </ul>

            <Col xs={12} lg={7}>
              {step === 1 ? (
                <div className="mainShip">
                  {shippingAddress?.length > 0 && (
                    <div className="detailBox mb-5 border p-3">
                      <h3 className="mb-3 greenColor text-center fw-bold">
                        Select Shipping Address
                      </h3>
                      {shippingAddress?.map((address) => (
                        <div key={address.id} className="saveshipping">
                          <label
                            style={{
                              display: "flex",
                              alignItems: "flex-start",
                              gap: "10px",
                            }}
                          >
                            <input
                              type="radio"
                              name="shippingAddress"
                              checked={selectedAddressId === address.id}
                              onChange={() => {
                                setSelectedAddress(address);
                                handleSelect(address.id);
                              }}
                            />
                            <div className="shipping-address">
                              <strong>
                                {address.first_name} {address.last_name}
                              </strong>
                              <div>{address.company}</div>
                              <div>
                                {address.address}, {address.city},{" "}
                                {address.postal_code}
                              </div>
                              <div>Phone: {address.phone}</div>
                              {address.is_primary ? (
                                <div style={{ fontWeight: "bold" }}>
                                  Primary Address -{" "}
                                  <FaPencil
                                    className="cursor"
                                    color="green"
                                    onClick={() => HandleEdit()}
                                  />{" "}
                                  -{" "}
                                  <a
                                    style={{ cursor: "pointer" }}
                                    onClick={() => {
                                      deleteAddress(address.id);
                                    }}
                                  >
                                    <FaTrash color="red" />
                                  </a>
                                </div>
                              ) : (
                                <div>
                                  <a
                                    style={{ cursor: "pointer" }}
                                    onClick={() => {
                                      handlePrimaryAddress(address.id);
                                    }}
                                  >
                                    Set as primary
                                  </a>{" "}
                                  -{" "}
                                  <FaPencil
                                    className="cursor"
                                    color="green"
                                    onClick={() => HandleEdit()}
                                  />{" "}
                                  -{" "}
                                  <a
                                    onClick={() => {
                                      deleteAddress(address.id);
                                    }}
                                  >
                                    <FaTrash color="red" />
                                  </a>
                                </div>
                              )}
                            </div>
                          </label>
                        </div>
                      ))}

                      <div className="useData">
                        <CustomButton
                          text="Use Selected Shipping Address"
                          className="primaryButton"
                          onClick={dataShip}
                        />
                      </div>
                    </div>
                  )}
                  <div className="border p-3">
                    <h3 className="greenColor text-center fw-bold">
                      Add New Shipping Information
                    </h3>
                    <Form>
                      <Row>
                        <Col xs={12} sm={6}>
                          <Form.Group controlId="first_name">
                            <Form.Label>First Name</Form.Label>
                            <Form.Control
                              name="first_name"
                              value={formData.first_name}
                              onChange={handleChange}
                            />
                          </Form.Group>
                        </Col>
                        <Col xs={12} sm={6}>
                          <Form.Group controlId="last_name">
                            <Form.Label>Last Name</Form.Label>
                            <Form.Control
                              name="last_name"
                              value={formData.last_name}
                              onChange={handleChange}
                            />
                          </Form.Group>
                        </Col>
                      </Row>

                      <Form.Group controlId="company">
                        <Form.Label>Company</Form.Label>
                        <Form.Control
                          name="company"
                          value={formData.company}
                          onChange={handleChange}
                        />
                      </Form.Group>

                      {cartItems?.is_customer !== true && (
                        <Form.Group controlId="email">
                          <Form.Label>Email</Form.Label>
                          <Form.Control
                            name="email"
                            value={formData.email}
                            onChange={handleChange}
                            type="email"
                          />
                        </Form.Group>
                      )}

                      <Form.Group controlId="address" className="mt-3">
                        <Form.Label>Address</Form.Label>
                        <Form.Control
                          name="address"
                          value={formData.address}
                          onChange={handleChange}
                        />
                      </Form.Group>

                      <Row className="mt-2">
                        <Col xs={12} sm={6}>
                          <Form.Group controlId="city">
                            <Form.Label>City</Form.Label>
                            <Form.Control
                              name="city"
                              value={formData.city}
                              onChange={handleChange}
                            />
                          </Form.Group>
                        </Col>
                        <Col xs={12} sm={6}>
                          <SelectBox
                            selectClass="mainInput"
                            name="state_id"
                            required
                            label="Select State/Province"
                            value={formData.state_id}
                            option={states}
                            onChange={handleChange}
                          />
                        </Col>
                      </Row>

                      <Row>
                        <Col xs={12} sm={6}>
                          <SelectBox
                            selectClass="mainInput"
                            name="country_id"
                            required
                            label="Select Country"
                            value={formData.country_id}
                            option={countries}
                            onChange={handleCountryChange}
                          />
                        </Col>
                        <Col xs={12} sm={6}>
                          <Form.Group controlId="postal_code">
                            <Form.Label>Postal Code</Form.Label>
                            <Form.Control
                              type="number"
                              name="postal_code"
                              value={formData.postal_code}
                              onChange={handleChange}
                            />
                          </Form.Group>
                        </Col>
                      </Row>

                      <Form.Group controlId="phone">
                        <Form.Label>Phone</Form.Label>
                        <Form.Control
                          name="phone"
                          type="number"
                          value={formData.phone}
                          onChange={handleChange}
                        />
                      </Form.Group>

                      <CustomButton
                        onClick={(e) => handleSubmitAddress(e)}
                        text="Add New Address"
                        variant="primaryButton"
                        className="mt-3"
                      />
                    </Form>
                  </div>
                </div>
              ) : step === 2 ? (
                <div className=" border  p-3">
                  {console.log(selectedAddress)}
                  {selectedAddress !== null ? (
                    <div className="border-bottom p-3">
                      <h4 className="text-theme">Physical Shipping Address</h4>
                      <div
                        key={selectedAddress?.id}
                        style={{ marginBottom: "20px" }}
                      >
                        <div className="ml-3">
                          <strong>
                            {selectedAddress?.first_name}{" "}
                            {selectedAddress?.last_name}
                          </strong>
                          <div>{selectedAddress?.company}</div>
                          <div>
                            {selectedAddress?.address}, {selectedAddress?.city},{" "}
                            {selectedAddress?.postal_code}
                          </div>
                          <div>Phone: {selectedAddress?.phone}</div>
                          <div className="mt-2">
                            <CustomButton
                              text="Edit Shipping Details"
                              className="primaryButton"
                              onClick={() => HandleEdit()}
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  ) : (
                    ""
                  )}
                  <div className="border-bottom p-3">
                    <h4 className="text-theme">Shipping Method</h4>
                    <p style={{ fontSize: "14px" }}>
                      <strong>Please Note:</strong> We cannot ship on Saturdays,
                      Sundays, or USPS/UPS recognized holidays. Orders placed
                      Monday through Thursday before 2:00 PM CST or Friday
                      before 12:00 PM CST are typically shipped the same day.
                      Auto-Ship is only available for the 48 contiguous states
                      and Washington D.C
                    </p>
                    <p className="fw-bold mt-2">
                      Auto-Ship available on orders over $
                      {cartItems?.auto_ship_amount}
                    </p>

                    <Card className="mb-3">
                      <Card.Body>
                        <Form>
                          {/* Standard Shipping (Active) */}
                          <Form.Check
                            type="radio"
                            id="standardShipping"
                            name="shippingMethod"
                            onClick={(e) =>
                              setFormData((prev) => ({
                                ...prev,
                                auto_shipping: false,
                              }))
                            }
                            label={
                              <div className="d-flex justify-content-between align-items-center gap-2">
                                <span>
                                  <strong className="text-success">
                                    Standard Shipping
                                  </strong>
                                </span>
                                <span className="mt-1">
                                  ${shippingPrice?.standard_shipping}
                                </span>
                              </div>
                            }
                            defaultChecked
                            className="mb-3"
                          />
                          <div className="d-flex col-12 flex-wrap">
                            <div className="col-12 col-sm-8">
                              <Form.Check
                                type="radio"
                                id="autoShip"
                                name="shippingMethod"
                                onClick={(e) =>
                                  setFormData((prev) => ({
                                    ...prev,
                                    auto_shipping: e.target.checked,
                                  }))
                                }
                                label={
                                  <div
                                    className={`d-flex justify-content-between flex-wrap flex-sm-nowrap align-items-center text-muted  gap-2`}
                                  >
                                    <span
                                      className={`${
                                        !cartItems?.auto_ship &&
                                        "text-decoration-line-through"
                                      }`}
                                    >
                                      <strong>
                                        FREE SHIPPING AND PRICE LOCK WITH
                                        AUTO-SHIP
                                      </strong>
                                    </span>

                                    <span className="">
                                      {cartItems?.auto_ship
                                        ? "Receive this order every: "
                                        : `${shippingPrice?.autoShipMessage}`}
                                    </span>
                                  </div>
                                }
                                disabled={!cartItems?.auto_ship}
                              />
                            </div>

                            <div className="col-12 col-sm-4 jusify-content-end">
                              {cartItems?.auto_ship && (
                                <CustomSelectAutoShip
                                  selectClass="mainInput"
                                  name="auto_ship_doration"
                                  required
                                  value={formData.auto_ship_doration}
                                  option={subscriptionFrequencies}
                                  onChange={handleAutoship}
                                />
                              )}
                            </div>
                          </div>
                        </Form>
                      </Card.Body>
                    </Card>
                  </div>

                  <div className="border-bottom p-3">
                    <h4 className="text-theme">Comments</h4>
                    {/* <div style={{ fontSize: "16px" }}>
                      Delivery notes or other comments? Please enter below
                    </div> */}
                    <p style={{ fontSize: "14px" }}>
                      <strong>Note:</strong> Notes left below will be seen by
                      our shipping/fulfillment team to assist with order
                      processing, but they will not be transmitted to the
                      shipping carrier.
                    </p>
                    <Form.Group controlId="comments">
                      <Form.Control
                        as="textarea"
                        rows={3}
                        name="comments"
                        value={formData.comments}
                        onChange={handleChange}
                      />
                    </Form.Group>

                    {selectedAddress?.is_usa === 1 &&
                      formData?.auto_shipping && (
                        <div id="intlShipping-notice ">
                          <label
                            htmlFor="intlShipping-confirm "
                            style={{ marginTop: "20px" }}
                          >
                            * After this order is placed your next autoshipment
                            will be shipped out on{" "}
                            {subscriptionFrequencies?.find(
                              (item) =>
                                item.freqType ===
                                  formData?.auto_ship_doration?.freqType &&
                                item.freq === formData?.auto_ship_doration?.freq
                            )?.name || ""}
                            . You can alter the date at any time by updating the
                            autoshipment online or giving us a call at least 48
                            hours before your order goes out. If the
                            autoshipment is not updated or we are not contacted
                            at least 48 hours before your autoshipment processes
                            we will not be liable for processing any returns,
                            and you would have to cover the cost to return the
                            product if you would like a refund.
                            <br />
                            <div className="form_check_boxes mt-2">
                              <div className="form-group">
                                <input
                                  type="checkbox"
                                  id={`store_type`}
                                  value={agrement}
                                  onChange={(e) =>
                                    setAgrement(e.target.checked)
                                  }
                                />
                                <label htmlFor={`store_type`} className="mt-2">
                                  Please check this box to confirm that you have
                                  read, understand, and agree.
                                </label>
                              </div>
                            </div>
                          </label>
                        </div>
                      )}
                  </div>

                  <div className="d-flex justify-content-center justify-content-sm-end mt-2">
                    <CustomButton
                      text="Continue to Payment method"
                      variant="primaryButton"
                      onClick={() => {
                        dataShip();
                      }}
                    />
                  </div>
                </div>
              ) : (
                step === 3 && (
                  <Form className="px-3">
                    <Card className="mb-4 rounded-1">
                      <Card.Body>
                        <h4 className="text-theme">Payment Method</h4>
                        <p className="text-muted">
                          All transactions are secure and encrypted.
                        </p>

                        {billingAddress?.map((address) => (
                          <div
                            key={address.id}
                            style={{ marginBottom: "20px" }}
                          >
                            <label
                              style={{
                                display: "flex",
                                alignItems: "flex-start",
                                gap: "10px",
                              }}
                            >
                              <input
                                type="radio"
                                name="billingOption"
                                value={address.id}
                                checked={formData.billing_id === address.id}
                                onChange={() => {
                                  setFormData((prev) => ({
                                    ...prev,
                                    billing_id: address.id,
                                    payment_method: "",
                                    new_credit_card: false,
                                  }));
                                  setPaymentMethod(null);
                                }}
                              />
                              <div>
                                <strong>
                                  {address.first_name} {address.last_name}
                                </strong>
                                <div>{address.company}</div>
                                <div>
                                  {address.address}, {address.city},{" "}
                                  {address.postal_code}
                                </div>
                                <div>Phone: {address.phone}</div>
                                <div>
                                  <a
                                    className="cursor-pointer"
                                    style={{
                                      fontWeight: address.is_primary
                                        ? "bold"
                                        : "normal",
                                      color: address.is_primary
                                        ? "black"
                                        : "red",
                                    }}
                                    onClick={() =>
                                      deletebillingAddress(address.id)
                                    }
                                  >
                                    <FaTrash />
                                  </a>
                                </div>
                              </div>
                            </label>
                          </div>
                        ))}

                        <div style={{ marginBottom: "20px" }}>
                          {paymentOptions

                            .filter((option) => {
                              const anyCheckboxChecked =
                                formData.credits || formData.net_thirty;
                              if (
                                option.value === "net_thirty" &&
                                !isWholseller
                              ) {
                                return false;
                              }

                              if (anyCheckboxChecked) {
                                return (
                                  option.value === "coinbase" ||
                                  (option.checkbox && formData[option.value])
                                );
                              }
                              return true;
                            })
                            .map((option) =>
                              option.checkbox ? (
                                <div
                                  className="form_check_boxes mt-2"
                                  key={option.value}
                                >
                                  <div
                                    className="form-group"
                                    style={{
                                      display: "flex",
                                      alignItems: "center",
                                      gap: "10px",
                                    }}
                                  >
                                    <input
                                      type="checkbox"
                                      id={option.value}
                                      name={option.value}
                                      checked={formData[option.value]}
                                      onChange={(e) => {
                                        const isChecked = e.target.checked;
                                        setFormData((prev) => ({
                                          ...prev,
                                          credits: false,
                                          net_thirty: false,
                                          new_credit_card: false,
                                          [option.value]: isChecked,
                                          payment_method: isChecked
                                            ? option.value
                                            : "",
                                        }));
                                      }}
                                    />
                                    <label
                                      htmlFor={option.value}
                                      className="mt-2 text-theme"
                                    >
                                      {option.label}
                                    </label>
                                  </div>
                                </div>
                              ) : (
                                <Form.Check
                                  key={option.value}
                                  type="radio"
                                  label={
                                    <span
                                      className={
                                        formData.payment_method === option.value
                                          ? "text-theme"
                                          : ""
                                      }
                                    >
                                      {option.label}

                                      {/* PayPal */}
                                      {option?.value === "paypal" && (
                                        <img
                                          src={paypalImages}
                                          className="paypal-image"
                                        
                                          alt={option.name}
                                        />
                                      )}

                                      {/* Credit Card */}
                                      {option?.value === "credit_card" && (
                                        <>
                                          <img
                                            src={visa}
                                            alt="Visa"
                                            style={{ marginLeft: "10px" }}
                                          />
                                          <img
                                            src={mastercard}
                                            alt="Mastercard"
                                          />
                                          <img src={discover} alt="Discover" />
                                          <img src={amex} alt="Amex" />
                                        </>
                                      )}

                                      {/* CMO */}
                                      {option?.value === "cmo" && (
                                        <img
                                          src="https://www.shutterstock.com/image-vector/sample-fake-us-stimulus-check-600nw-1909326448.jpg"
                                          alt="CMO"
                                          style={{
                                            marginLeft: "10px",
                                            width: "50px",
                                          }}
                                        />
                                      )}

                                      {/* Coinbase */}
                                      {option?.value === "coinbase" && (
                                        <img
                                          src={bitCoin}
                                          alt="Coinbase"
                                          style={{
                                            marginLeft: "10px",
                                            marginBottom: "7px",
                                          }}
                                        />
                                      )}
                                    </span>
                                  }
                                  name="billingOption"
                                  value={option.value}
                                  checked={
                                    formData.payment_method === option.value
                                  }
                                  onChange={() => {
                                    setFormData((prev) => ({
                                      ...prev,
                                      billing_id: null,
                                      payment_method: option.value,
                                      new_credit_card:
                                        option.value === "credit_card",
                                      credits: false,
                                      net_thirty: false, // clear checkboxes when radio selected
                                    }));

                                    handleChange({
                                      target: {
                                        name: option.onChangeExtra.name,
                                        value: option.onChangeExtra.value,
                                      },
                                    });
                                  }}
                                  className="mb-2"
                                />
                              )
                            )}
                        </div>

                        {formData.payment_method === "credit_card" && (
                          <>
                            <CreditCard setFormData={setFormData} key={1 + 1} />
                            {/* <Row className="mb-3">
                              <Col md={12} className="mb-2">
                                <div className="input-group">
                                  <InputMask
                                    mask="9999 9999 9999 9999"
                                    value={formData.card_number}
                                    onChange={handleChange}
                                    onClick={(e) => {
                                      const input = e.target;
                                      input.setSelectionRange(0, 0);
                                    }}
                                  >
                                    {(inputProps) => (
                                      <Form.Control
                                        {...inputProps}
                                        name="card_number"
                                        placeholder="Card Number"
                                      />
                                    )}
                                  </InputMask>
                                  {checkCardType !== "Unknown Card Type" && (
                                    <div className="input-group-append">
                                      <img
                                        src={
                                          checkCardType == "Visa"
                                            ? visa
                                            : checkCardType == "Amex"
                                            ? amex
                                            : checkCardType == "Discover"
                                            ? discover
                                            : checkCardType == "Mastercard"
                                            ? mastercard
                                            : ""
                                        }
                                        alt="Visa"
                                        style={{
                                          width: "30px",
                                          height: "auto",
                                          marginLeft: "10px",
                                          paddingTop: "5px",
                                        }}
                                      />
                                    </div>
                                  )}
                                </div>
                              </Col>

                              <Col md={6} className="mb-2">
                                <Form.Control
                                  name="card_name"
                                  value={formData.card_name}
                                  onChange={handleChange}
                                  placeholder="Name On Card"
                                />
                              </Col>

                              <Col md={3} className="mb-2">
                                <InputMask
                                  mask="99/99"
                                  value={formData.expiry_date}
                                  onChange={handleChange}
                                >
                                  {(inputProps) => (
                                    <Form.Control
                                      {...inputProps}
                                      name="expiry_date"
                                      placeholder="MM / YY"
                                    />
                                  )}
                                </InputMask>
                              </Col>

                              <Col md={3} className="mb-2">
                                <InputMask
                                  mask="9999"
                                  value={formData.cvv}
                                  onChange={handleChange}
                                >
                                  {(inputProps) => (
                                    <Form.Control
                                      {...inputProps}
                                      name="cvv"
                                      placeholder="CVV Security Code"
                                    />
                                  )}
                                </InputMask>
                              </Col>
                              <Col md={12}>
                                <div className="d-flex justify-content-end gap-2">
                                  <img
                                    src={visa}
                                    alt="Visa"
                                    style={{ marginLeft: "10px" }}
                                  />
                                  <img src={mastercard} alt="Mastercard" />
                                  <img src={discover} alt="Discover" />
                                  <img src={amex} alt="Amex" />
                                </div>
                              </Col>
                            </Row> */}
                          </>
                        )}
                        {formData.payment_method === "paypal" && (
                          <PayPalMultiButtons
                            handleSubmitOrder={handleSubmitOrder}
                            total_amount={total_amount}
                          />
                        )}
                        {formData.payment_method === "coinbase" && (
                          <div className="bitcoin-payment-section">
                            <h4 className="text-theme mb-3">
                              Pay with Bitcoin
                            </h4>
                            <div className="alert alert-info mb-3">
                              <p>
                                You'll be redirected to Coinbase Commerce to
                                complete your Bitcoin payment.
                              </p>
                              <p className="mb-0">
                                Only Bitcoin payments are accepted through this
                                method.
                              </p>
                            </div>

                            <Button
                              variant="primary"
                              onClick={() => HandleCoin()}
                            >
                              Proceed to Bitcoin Payment
                            </Button>
                          </div>
                        )}
                      </Card.Body>
                    </Card>

                    <Card className="mb-4">
                      <Card.Body>
                        <h5 className="text-theme">Billing Address</h5>

                        <Form.Check
                          type="radio"
                          label={
                            <span
                              className={
                                billingSameAsShipping ? "text-theme" : ""
                              }
                            >
                              Same as shipping address
                            </span>
                          }
                          name="billing"
                          value="same"
                          checked={billingSameAsShipping}
                          onChange={() => {
                            setBillingSameAsShipping(true);
                            handleChange({
                              target: {
                                name: "same_as_shipping_address",
                                value: true,
                              },
                            });
                          }}
                          className="mb-2"
                        />

                        <Form.Check
                          type="radio"
                          label={
                            <span
                              className={
                                !billingSameAsShipping ? "text-theme" : ""
                              }
                            >
                              Use a different billing address
                            </span>
                          }
                          name="billing"
                          value="different"
                          checked={!billingSameAsShipping}
                          onChange={() => {
                            setBillingSameAsShipping(false);
                            handleChange({
                              target: {
                                name: "same_as_shipping_address",
                                value: false,
                              },
                            });
                          }}
                        />
                        {!billingSameAsShipping && (
                          <Form>
                            <Row>
                              <Col>
                                <Form.Group controlId="first_name">
                                  <Form.Label>First Name</Form.Label>
                                  <Form.Control
                                    name="first_name"
                                    value={formData.first_name}
                                    onChange={handleChange}
                                  />
                                </Form.Group>
                              </Col>
                              <Col>
                                <Form.Group controlId="last_name">
                                  <Form.Label>Last Name</Form.Label>
                                  <Form.Control
                                    name="last_name"
                                    value={formData.last_name}
                                    onChange={handleChange}
                                  />
                                </Form.Group>
                              </Col>
                            </Row>

                            <Form.Group controlId="company">
                              <Form.Label>Company</Form.Label>
                              <Form.Control
                                name="company"
                                value={formData.company}
                                onChange={handleChange}
                              />
                            </Form.Group>

                            <Form.Group controlId="email">
                              <Form.Label>Email</Form.Label>
                              <Form.Control
                                name="email"
                                value={formData.email}
                                onChange={handleChange}
                                type="email"
                              />
                            </Form.Group>

                            <Form.Group controlId="phone">
                              <Form.Label>Phone</Form.Label>
                              <Form.Control
                                name="phone"
                                value={formData.phone}
                                onChange={handleChange}
                              />
                            </Form.Group>
                            <SelectBox
                              selectClass="mainInput"
                              name="country_id"
                              required
                              label="Select Country"
                              value={formData.country_id}
                              option={countries}
                              onChange={handleCountryChange}
                            />
                            <SelectBox
                              selectClass="mainInput"
                              name="state_id"
                              required
                              label="State/Province"
                              value={formData.state_id}
                              option={states}
                              onChange={handleChange}
                            />
                            <Form.Group controlId="address">
                              <Form.Label>Address</Form.Label>
                              <Form.Control
                                name="address"
                                value={formData.address}
                                onChange={handleChange}
                              />
                            </Form.Group>

                            <Row>
                              <Col>
                                <Form.Group controlId="city">
                                  <Form.Label>City</Form.Label>
                                  <Form.Control
                                    name="city"
                                    value={formData.city}
                                    onChange={handleChange}
                                  />
                                </Form.Group>
                              </Col>
                              <Col>
                                <Form.Group controlId="postal_code">
                                  <Form.Label>Postal Code</Form.Label>
                                  <Form.Control
                                    type="number"
                                    name="postal_code"
                                    value={formData.postal_code}
                                    onChange={handleChange}
                                  />
                                </Form.Group>
                              </Col>
                            </Row>
                          </Form>
                        )}
                      </Card.Body>
                    </Card>

                    <div className="text-end mb-2">
                      <Button
                        onClick={() => {
                          handleSubmitOrder();
                        }}
                        variant="success"
                      >
                        Complete Order
                      </Button>
                    </div>
                  </Form>
                )
              )}
            </Col>

            <Col xs={12} lg={3} className="checkout-card">
              <Card className="p-4 shadow-sm border rounded-1">
                <h6 className="text-theme"> Shipping Address</h6>
                {selectedAddress && (
                  <div
                    key={selectedAddress?.id}
                    style={{ marginBottom: "20px" }}
                  >
                    <div className="ml-3">
                      <strong>
                        {selectedAddress?.first_name}{" "}
                        {selectedAddress?.last_name}
                      </strong>
                      <div>{selectedAddress?.company}</div>
                      <div>
                        {selectedAddress?.address}, {selectedAddress?.city},{" "}
                        {selectedAddress?.postal_code}
                      </div>
                      <div>Phone: {selectedAddress?.phone}</div>
                    </div>
                  </div>
                )}
                <h6 className="text-theme"> Items</h6>
                {cartItems?.items?.map((item, i) => (
                  <div key={i} className="d-flex align-items-center mb-3 resposive-card-checkout">
                    <div className="border p-3 position-relative">
                      <span
                        className="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning"
                        style={{ fontSize: "12px" }}
                      >
                        {item?.qty}
                      </span>

                      <img
                        src={item?.image}
                        alt="Product"
                        style={{
                          width: "60px",
                          height: "60px",
                          objectFit: "contain",
                        }}
                      />
                    </div>

                    <div className="ms-3 ms-responsive">
                      <div className="fw-bold text-secondary">
                        {item?.product_name}
                      </div>

                      <div className="ms-auto fw-medium text-secondary">
                        ${item?.price_per_product}
                      </div>
                    </div>
                  </div>
                ))}

                {/* Responsive Dotted Lines */}
                <div className="checkout-row">
                  <span>Subtotal</span>
                  <span>${cartItems?.sub_total}</span>
                </div>

                <div className="checkout-row">
                  <span>Shipping</span>
                  <span>
                    {cartItems?.free_shipping === true
                      ? "Free"
                      : `$${
                          shippingPrice?.shipping_service_rate?.toFixed(2) || 0
                        }`}
                  </span>
                </div>

                {/* <div className="checkout-row">
                  <span>Tax</span>
                  <span>${tax_amount?.toFixed(2)}</span>
                </div> */}
                {discount_amount > 0 && (
                  <div className="checkout-row text-danger">
                    <span>Discount</span>
                    <span>-${discount_amount?.toFixed(2)}</span>
                  </div>
                )}

                {/* Credits */}
                {formData.payment_method === "credits" && (
                  <div className="checkout-row">
                    <span>Account Credits</span>
                    <span>${remainingCredits.toFixed(2)}</span>
                  </div>
                )}

                {/* Total */}
                <div className="checkout-row total-row">
                  <span className="fw-semibold">Total</span>
                  <span className="fw-semibold">
                    ${isNaN(total_amount) ? 0 : total_amount?.toFixed(2)}
                  </span>
                </div>
              </Card>
            </Col>
          </Row>
        </Container>
      </DefaultLayout>
      <EditShippingAdress
        addShippingAddress={addShippingAddress}
        countries={countries}
        states={states}
        setAddShippingAddress={setAddShippingAddress}
        shippingdata={shippingAddress}
        setSelectedAddress={setSelectedAddress}
        fetchStates={fetchStates}
        fetchpaymentAddress={fetchpaymentAddress}
      />
    </>
  );
};

export default Checkout;
