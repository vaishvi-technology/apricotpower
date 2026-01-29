/* eslint-disable no-constant-binary-expression */
/* eslint-disable react/jsx-key */
import React, { useContext, useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { Button, Card, Col, Container, Form, Row } from "react-bootstrap";
import { base_url } from "../../api";
import { toast } from "react-toastify";
import CustomInput from "../../components/CustomInput";
import EmailPrefHead from "../../components/EmailPrefHead";
import CustomButton from "../../components/CustomButton";
import { SelectBox } from "../../components/CustomSelect";
import DashedCard from "../../components/DashedCard";
import InputMask from "react-input-mask";

import CustomModal from "../../components/CustomModal";
import { updateShippingAddress } from "../../api/Services/addressServices";
import { faSpinner } from "@fortawesome/free-solid-svg-icons";
import { useCookies } from "react-cookie";
import { CartContext } from "../../Context/CartContext";
import { detectCardType } from "../Checkout/data";
import visa from "../../assets/images/visa.png";
import mastercard from "../../assets/images/mastercard.png";
import discover from "../../assets/images/discover.png";
import amex from "../../assets/images/amex.png";
import unionpay from "../../assets/images/unionpay.png";
import jcb from "../../assets/images/jcb.png";
import dinersclub from "../../assets/images/dinersclub.png";
import maestro from "../../assets/images/maestro.png";
import visaElectron from "../../assets/images/visaElectron.png";
import ruPay from "../../assets/images/ruPay.png";
import solo from "../../assets/images/solo.png";
import elo from "../../assets/images/elo.png";
const categories = [
  {
    id: 1,
    name: "Category 1",
  },
  {
    id: 2,
    name: "Category 2",
  },
];

const AccountDetails = () => {
  const [cookies, setCookie] = useCookies(["role"]);
  const [cardNumber, setCardNumber] = useState("");
  const [expiryDate, setExpiryDate] = useState("");
  const [cardType, setCardType] = useState({
    name: "Unknown",
    maxLength: 16,
    formattedName: "Unknown Card",
  });
  const { cartItems } = useContext(CartContext);

  const [formData, setFormData] = useState({
    state_id: null,
    email: null,
    country_id: null,
  });

  const cardImages = {
    Visa: visa,
    Mastercard: mastercard,
    Discover: discover,
    Amex: amex,
    Unionpay: unionpay,
    Jcb: jcb,
    DinersClub: dinersclub,
    Maestro: maestro,
    VisaElectron: visaElectron,
    RuPay: ruPay,
    Solo: solo,
    Elo: elo,
  };

  const token = localStorage.getItem("login");
  const [addShippingAddress, setAddShippingAddress] = useState(false);
  const [editShippingAddress, setEditShippingAddress] = useState(false);
  const [addPaymentAccount, setAddPaymentAccount] = useState(false);
  const [editPaymentAccount, setEditPaymentAccount] = useState(false);
  const [isLoading, setisLoading] = useState(false);
  const [shippingAddress, setShippingAddress] = useState([]);
  const [billingAddress, setBillingAddress] = useState([]);
  const [states, setStates] = useState([]);
  const [data, setData] = useState([]);
  const [selectedCountry, setSelectedCountry] = useState("");
  const [selectedState, setSelectedState] = useState("");
  const [zipCode, setZipCode] = useState("");
  const [selectedAddressId, setSelectedAddressId] = useState(
    shippingAddress?.find((address) => address?.is_primary)?.id || null
  );
  const [selectedAddress, setSelectedAddress] = useState(
    shippingAddress?.find((address) => address?.is_primary) || null
  );

  const handleChange = (event) => {
    const { name, value, type, checked } = event.target;
    if (type === "checkbox") {
      setFormData((prevData) => ({
        ...prevData,
        [name]: checked,
      }));
    } else {
      setFormData((prevData) => ({
        ...prevData,
        [name]: value,
      }));
    }
  };

  useEffect(() => {
    document.title = "Apricot Power Admin | Account Details";
    fetchaddress();
    if (cartItems?.email) {
      setFormData((prev) => ({
        ...prev,
        email: cartItems?.email,
      }));
    }

    fetchpaymentAddress();
    document.querySelector(".loaderBox").classList.remove("d-none");
  }, []);
  // ==========================fetch Address=======================
  const fetchaddress = () => {
    document.querySelector(".loaderBox").classList.remove("d-none");
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
        document.querySelector(".loaderBox").classList.add("d-none");

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
        document.querySelector(".loaderBox").classList.add("d-none");
      });
  };
  const [countries, setCountries] = useState([]);
  const fetchCountries = async () => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");
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
    } catch (error) {
      console.error(error);
    } finally {
      document.querySelector(".loaderBox").classList.add("d-none");
    }
  };
  useEffect(() => {
    fetchCountries();
    if (formData.country_id) {
      fetchStates(formData.country_id);
    }
  }, [formData.country_id]);

  // ==========================fetch Payment Address=======================
  const fetchpaymentAddress = () => {
    document.querySelector(".loaderBox").classList.remove("d-none");
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
        document.querySelector(".loaderBox").classList.add("d-none");

        setBillingAddress(data);
      })
      .catch((error) => {
        document.querySelector(".loaderBox").classList.add("d-none");
      });
  };
  // ==========================Add Address=======================
  const handleSubmitAddress = async (e) => {
    // console.log(formData)
    // e.preventDefault();

    try {
      document.querySelector(".loaderBox").classList.remove("d-none");

      const res = await fetch(`${base_url}/add/shipping-address`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });
      document.querySelector(".loaderBox").classList.add("d-none");
      const data = await res.json();
      if (data?.errors) {
        toast.error(data?.errors);
      }

      if (res.ok) {
        setAddShippingAddress(false);
        // toast.success(data?.message);;
        fetchaddress();
      }
    } catch (err) {
      document.querySelector(".loaderBox").classList.add("d-none");

      console.log("Order failed:", err);
      alert("Something went wrong!");
    }
  };

  // ==========================Edit Address=======================

  // ==========================Add Payment Address =======================
  const handleSubmitPaymentAddress = async (e) => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");

      const res = await fetch(`${base_url}/add/payment-account`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });
      document.querySelector(".loaderBox").classList.add("d-none");
      const data = await res.json();
      if (data?.errors) {
        toast.error(data?.errors);
      }

      if (res.ok) {
        setAddPaymentAccount(false);
        // toast.success(data?.message);;
        fetchpaymentAddress();
      }
    } catch (err) {
      document.querySelector(".loaderBox").classList.add("d-none");

      console.log("Order failed:", err);
      alert("Something went wrong!");
    }
  };
  const handleUpdatePaymentAddress = async (e) => {
    try {
      setAddPaymentAccount(false);
      document.querySelector(".loaderBox").classList.remove("d-none");

      const res = await fetch(`${base_url}/update/payment-account`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });
      document.querySelector(".loaderBox").classList.add("d-none");
      const data = await res.json();
      if (data?.errors) {
        toast.error(data?.errors);
      }

      if (res.ok) {
        // toast.success(data?.message);;
        fetchpaymentAddress();
        setEditPaymentAccount(false);
      }
    } catch (err) {
      document.querySelector(".loaderBox").classList.add("d-none");

      console.log("Order failed:", err);
      alert("Something went wrong!");
    }
  };

  // ==========================Delete Address=======================
  const deleteAddress = async (id) => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");

      const res = await fetch(`${base_url}/delete/shipping-address`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ address_id: id }),
      });
      document.querySelector(".loaderBox").classList.add("d-none");

      const data = await res.json();
      // toast.success(data?.message);;
      fetchaddress();
    } catch (err) {
      document.querySelector(".loaderBox").classList.add("d-none");

      console.log("Order failed:", err);
      alert("Something went wrong!");
    }
  };
  const fetchStates = async (countryId) => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");
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
      document.querySelector(".loaderBox").classList.add("d-none");
    }
  };
  // ==========================Delete bILLING aDDRESS=======================
  const deletebillingAddress = async (id) => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");

      const res = await fetch(`${base_url}/delete/payment-account`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ id: id }),
      });
      document.querySelector(".loaderBox").classList.add("d-none");

      const data = await res.json();
      // toast.success(data?.message);;
      fetchpaymentAddress();
    } catch (err) {
      document.querySelector(".loaderBox").classList.add("d-none");

      console.log("Order failed:", err);
      alert("Something went wrong!");
    }
  };

  const handleCountryChange = (e) => {
    const { value, name } = e.target;

    setStates([]);
    fetchStates(value);
    setSelectedState("");
    setFormData((prevData) => ({
      ...prevData,
      [name]: value,
    }));
  };
  useEffect(() => {
    document.title = "Account Detail | Apricot Power";
  }, []);
  const handleUpdateAddress = async () => {
    setisLoading(true);
    try {
      const response = await updateShippingAddress(formData);

      if (response?.errors) {
        setisLoading(false);
        toast.error(response?.errors);
      } else {
        fetchaddress();
        setisLoading(false);
        setEditShippingAddress(false);
      }

      // toast.success(response?.message);
    } catch (error) {
      toast.error(error?.message);
    }
  };

  const handleCardNumberChange = (e) => {
    let value = e.target.value;
    value = value.replace(/\D/g, "");
    const type = detectCardType(value);
    setCardType(type);
    let maxLength = 16;
    if (type === "Amex") {
      maxLength = 15;
    }
    if (value.length > maxLength) {
      value = value.slice(0, maxLength);
    }
    if (type === "Amex") {
      value = value.replace(/(\d{4})(?=\d)/g, "$1 ");
    } else {
      value = value.replace(/(\d{4})(?=\d)/g, "$1 ");
    }

    setCardNumber(value);
    setFormData((prev) => ({
      ...prev,
      card_number: value,
    }));
  };
  const handleExpiryChange = (e) => {
    let value = e.target.value;
    value = value.replace(/[^\d]/g, "");
    if (value.length > 2) {
      value = value.slice(0, 2) + "/" + value.slice(2, 4);
    }
    if (value.length > 5) {
      value = value.slice(0, 5);
    }
    setExpiryDate(value);
    setFormData((prev) => ({
      ...prev,
      expiry_date: value,
    }));
  };
  return (
    <DefaultLayout>
      <InnerBanner boldText1="Account Details" />
      <div className="container-fluid p-5">
        <div className="email-preferences-sec">
          <div className="row justify-content-center mb-3">
            <div className="col-md-12 mb-5">
              <EmailPrefHead text="Shipping Addresses" variant="green" />
            </div>
            <div className="row justify-content-center">
              <div className="col-xxl-6 col-xl-11 mb-5">
                <div className="account_details">
                  {shippingAddress.map((item) => {
                    return (
                      <div className="row " key={item?.id}>
                        <div className="col-md-6 mb-3">
                          <div className="account_details-content">
                            <div className="account_details-content-text">
                              {item?.is_primary === 1 && (
                                <h5>Primary Address</h5>
                              )}

                              <p>
                                <strong>{item?.first_name}</strong> -{" "}
                                {item?.address} ,{item?.postal_code}{" "}
                              </p>
                              <p>US Phone:{item?.phone}</p>
                            </div>
                          </div>
                        </div>
                        <div className="col-md-6 mb-3">
                          <div className="account_details-buttons d-flex flex-wrap justify-content-end gap-2">
                            <CustomButton
                              text="Edit"
                              variant="secondary"
                              onClick={() => {
                                // setFormData((prev) => ({
                                //   ...prev,
                                //  item
                                // }));
                                setFormData({
                                  first_name: item?.first_name || "",
                                  last_name: item?.last_name || "",
                                  company: item?.company || "",
                                  phone: item?.phone || "",
                                  id: item?.id || "",
                                  email: item?.email || "",
                                  address: item?.address || "",
                                  city: item?.city || "",
                                  is_primary: 1 || "",
                                  state_id: item?.state_id || null,
                                  postal_code: item?.postal_code || "",
                                  country_id: item?.country_id || null,
                                });
                                setEditShippingAddress(true);
                              }}
                            />
                            <CustomButton
                              onClick={() => {
                                deleteAddress(item?.id);
                              }}
                              text="Delete"
                              variant="red"
                            />
                          </div>
                        </div>
                      </div>
                    );
                  })}
                </div>

                <CustomButton
                  text="Add Shipping Address"
                  variant="secondary"
                  onClick={() => {
                    setAddShippingAddress(true);
                    setFormData({});
                  }}
                />
              </div>
            </div>

            <div className="col-md-12 mb-5">
              <EmailPrefHead text="Payment Accounts" />
            </div>
            <div className="row justify-content-center">
              <div className="col-xxl-6 col-xl-11 mb-5">
                <div className="account_details">
                  {billingAddress.map((item) => {
                    return (
                      <div className="row">
                        <div className="col-md-6 mb-3">
                          <div className="account_details-content">
                            <div className="account_details-content-text">
                              {item?.is_primary === 1 && (
                                <h5>Primary Account</h5>
                              )}
                              <p>
                                <strong>
                                  {item?.first_name} {item?.last_name}
                                </strong>{" "}
                                {item?.address} {item?.postal_code} US
                              </p>
                              <p className="mb-0">
                                <strong>Phone: {item?.phone}</strong>
                              </p>
                              <p>
                                <strong>Card {item?.year}</strong>
                              </p>
                            </div>
                          </div>
                        </div>
                        <div className="col-md-6 mb-3">
                          <div className="account_details-buttons d-flex flex-wrap justify-content-end gap-2">
                            <CustomButton
                              text="Edit"
                              variant="secondary"
                              onClick={() => {
                                setFormData(item);
                                setEditPaymentAccount(true);
                              }}
                            />
                            <CustomButton
                              onClick={() => deletebillingAddress(item?.id)}
                              text="Delete"
                              variant="red"
                            />
                          </div>
                        </div>
                      </div>
                    );
                  })}
                </div>
                <CustomButton
                  text="Add Payment Account"
                  variant="secondary"
                  onClick={() => {
                    setAddPaymentAccount(true);
                    setFormData({});
                    setCardNumber("");
                    setExpiryDate("");
                  }}
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Add Shipping Address */}
      <CustomModal
        show={addShippingAddress}
        close={() => {
          setAddShippingAddress(false);
        }}
        size="lg"
        heading="ADD SHIPPING ADDRESS"
      >
        <div className="apricot-modal-content mt-5">
          <div className="container-fluid">
            <div className="row">
              <div className="col-lg-6 mb-3">
                <CustomInput
                  label="First Name"
                  required
                  id="addShippFirstName"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="first_name"
                  value={formData.first_name}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-6 mb-3">
                <CustomInput
                  label="Last Name"
                  required
                  id="addShippLastName"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="last_name"
                  value={formData.last_name}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <SelectBox
                  selectClass="mainInput"
                  name="country_id"
                  required
                  label="Select Country"
                  value={formData.country_id}
                  option={countries}
                  onChange={handleCountryChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <SelectBox
                  selectClass="mainInput"
                  name="state_id"
                  required
                  label="State/Province"
                  value={formData.state_id}
                  option={states}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="Address"
                  required
                  id="addShippAddress"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="address"
                  value={formData.address}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="City"
                  required
                  id="addShippCity"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="city"
                  value={formData.city}
                  onChange={handleChange}
                />
              </div>

              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="ZIP/Postal Code"
                  required
                  id="addShippZIPPostalCode"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="postal_code"
                  value={formData.postal_code}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="Phone"
                  required
                  id="addShippPhone"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="Company"
                  required
                  id="addShippCompany"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="company"
                  value={formData.company}
                  onChange={handleChange}
                />
              </div>
              {cookies?.role == 2 && (
                <div className="col-lg-12 mb-3">
                  <CustomInput
                    label="Email"
                    required
                    id="addShippEmail"
                    type="text"
                    labelClass="mainLabel"
                    inputClass="mainInput"
                    name="email"
                    value={formData.email}
                    onChange={handleChange}
                  />
                </div>
              )}
              <div className="col-md-12">
                <div className="account_details-buttons d-flex flex-wrap justify-content-center gap-2">
                  <CustomButton
                    text="Cancel"
                    variant="grey"
                    onClick={() => setAddShippingAddress(false)}
                  />
                  <CustomButton
                    onClick={() => {
                      handleSubmitAddress();
                    }}
                    text="Save Changes"
                    variant="secondary"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </CustomModal>

      {/* Edit Shipping Address */}
      <CustomModal
        show={editShippingAddress}
        close={() => {
          setEditShippingAddress(false);
        }}
        size="lg"
        heading="Edit SHIPPING ADDRESS"
      >
        <div className="apricot-modal-content mt-5">
          <div className="container-fluid">
            <div className="row">
              <div className="col-lg-6 mb-3">
                <CustomInput
                  label="First Name"
                  required
                  id="addShippFirstName"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="first_name"
                  value={formData.first_name}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-6 mb-3">
                <CustomInput
                  label="Last Name"
                  required
                  id="addShippLastName"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="last_name"
                  value={formData.last_name}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <SelectBox
                  selectClass="mainInput"
                  name="country_id"
                  required
                  label="Select Country"
                  value={formData.country_id}
                  option={countries}
                  onChange={handleCountryChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <SelectBox
                  selectClass="mainInput"
                  name="state_id"
                  required
                  label="State/Province"
                  value={formData.state_id}
                  option={states}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="Address"
                  required
                  id="addShippAddress"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="address"
                  value={formData.address}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="City"
                  required
                  id="addShippCity"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="city"
                  value={formData.city}
                  onChange={handleChange}
                />
              </div>

              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="ZIP/Postal Code"
                  required
                  id="addShippZIPPostalCode"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="postal_code"
                  value={formData.postal_code}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="Phone"
                  required
                  id="addShippPhone"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="Company"
                  required
                  id="addShippCompany"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="company"
                  value={formData.company}
                  onChange={handleChange}
                />
              </div>
              {cookies?.role == 2 && (
                <div className="col-lg-12 mb-3">
                  <CustomInput
                    label="Email"
                    required
                    id="addShippEmail"
                    type="text"
                    labelClass="mainLabel"
                    inputClass="mainInput"
                    name="email"
                    value={formData.email}
                    onChange={handleChange}
                  />
                </div>
              )}
              <div className="col-md-12">
                <div className="account_details-buttons d-flex flex-wrap justify-content-center gap-2">
                  <CustomButton
                    text="Cancel"
                    variant="grey"
                    onClick={() => setAddShippingAddress(false)}
                  />
                  <CustomButton
                    onClick={() => {
                      handleUpdateAddress();
                    }}
                    icon={isLoading ? faSpinner : null}
                    text="Save Changes"
                    variant="primaryButton"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </CustomModal>

      {/* Add Payment Account */}
      <CustomModal
        show={addPaymentAccount}
        close={() => {
          setAddPaymentAccount(false);
        }}
        size="lg"
        heading="Add Payment Account"
      >
        <div className="apricot-modal-content mt-5">
          <div className="container-fluid">
            <div className="row">
              <div className="col-lg-6 mb-3">
                <CustomInput
                  label="First Name"
                  required
                  id="addPaymentAccountFirstName"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="first_name"
                  value={formData.first_name}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-6 mb-3">
                <CustomInput
                  label="Last Name"
                  required
                  id="addPaymentAccountLastName"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="last_name"
                  value={formData.last_name}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <SelectBox
                  selectClass="mainInput"
                  name="country_id"
                  required
                  label="Select Country"
                  value={formData.country_id}
                  option={countries}
                  onChange={handleCountryChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <SelectBox
                  selectClass="mainInput"
                  name="state_id"
                  required
                  label="State/Province"
                  value={formData.state_id}
                  option={states}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="Address"
                  required
                  id="addPaymentAccountAddress"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="address"
                  value={formData.address}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="City"
                  required
                  id="addPaymentAccountCity"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="city"
                  value={formData.city}
                  onChange={handleChange}
                />
              </div>
              {/* <div className="col-lg-12 mb-3">
                <SelectBox
                  selectClass="mainInput"
                  name=""
                  required
                  label="State/Province"
                  value={""}
                  option={categories}
                  onChange={handleChange}
                />
              </div> */}
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="ZIP/Postal Code"
                  required
                  id="addPaymentAccountZIPPostalCode"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="postal_code"
                  value={formData.postal_code}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="Phone"
                  required
                  id="addPaymentAccountPhone"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="Email"
                  required
                  id="addShippEmail"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <Col md={12} className="mb-2">
                  <label> Card Number</label>
                  <div className="input-group">
                    <input
                      type="text"
                      className="form-control"
                      placeholder="Card number"
                      value={cardNumber}
                      onChange={handleCardNumberChange}
                      maxLength={cardType?.maxLength}
                    />

                    {cardType?.name !== "Unknown" && (
                      <div className="input-group-append">
                        <img
                          src={cardImages[cardType?.name]}
                          alt={cardType?.name}
                          style={{
                            width: "40px",
                            height: "auto",
                            marginLeft: "10px",
                            paddingTop: "5px",
                          }}
                        />
                      </div>
                    )}
                  </div>
                </Col>
              </div>
              <div className="col-lg-6 mb-3">
                <label> Expiry</label>
                <input
                  type="text"
                  className="form-control"
                  placeholder="MM / YY"
                  value={expiryDate}
                  onChange={handleExpiryChange}
                />
              </div>
              <div className="col-lg-6 mb-3">
                <label> Year</label>
                <input
                  type="number"
                  className="form-control"
                  placeholder="Year"
                  value={formData.year}
                  onChange={(e) =>
                    setFormData((prev) => ({
                      ...prev,
                      year: Number(e?.target?.value),
                    }))
                  }
                />
              </div>
              <div className="col-md-12">
                <div className="account_details-buttons d-flex flex-wrap justify-content-center gap-2">
                  <CustomButton
                    text="Cancel"
                    variant="grey"
                    onClick={() => setAddPaymentAccount(false)}
                  />
                  <CustomButton
                    onClick={() => {
                      handleSubmitPaymentAddress();
                    }}
                    text="Same As my primary Shipping Address"
                    variant="primary"
                  />
                  <CustomButton
                    onClick={() => {
                      handleSubmitPaymentAddress();
                    }}
                    text="Save Changes"
                    variant="secondary"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </CustomModal>

      {/* Edit Payment Account */}
      <CustomModal
        show={editPaymentAccount}
        close={() => {
          setEditPaymentAccount(false);
        }}
        size="lg"
        heading="Edit Payment Account"
      >
        <div className="apricot-modal-content mt-5">
          <div className="container-fluid">
            <div className="row">
              <div className="col-lg-6 mb-3">
                <CustomInput
                  label="First Name"
                  required
                  id="addPaymentAccountFirstName"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="first_name"
                  value={formData.first_name}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-6 mb-3">
                <CustomInput
                  label="Last Name"
                  required
                  id="addPaymentAccountLastName"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="last_name"
                  value={formData.last_name}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <SelectBox
                  selectClass="mainInput"
                  name="country_id"
                  required
                  label="Select Country"
                  value={formData.country_id}
                  option={countries}
                  onChange={handleCountryChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <SelectBox
                  selectClass="mainInput"
                  name="state_id"
                  required
                  label="State/Province"
                  value={formData.state_id}
                  option={states}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="Address"
                  required
                  id="addPaymentAccountAddress"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="address"
                  value={formData.address}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="City"
                  required
                  id="addPaymentAccountCity"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="city"
                  value={formData.city}
                  onChange={handleChange}
                />
              </div>
              {/* <div className="col-lg-12 mb-3">
                <SelectBox
                  selectClass="mainInput"
                  name=""
                  required
                  label="State/Province"
                  value={""}
                  option={categories}
                  onChange={handleChange}
                />
              </div> */}
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="ZIP/Postal Code"
                  required
                  id="addPaymentAccountZIPPostalCode"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="postal_code"
                  value={formData.postal_code}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="Phone"
                  required
                  id="addPaymentAccountPhone"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="phone"
                  value={formData.phone}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <CustomInput
                  label="Email"
                  required
                  id="addShippEmail"
                  type="text"
                  labelClass="mainLabel"
                  inputClass="mainInput"
                  name="email"
                  value={formData.email}
                  onChange={handleChange}
                />
              </div>
              <div className="col-lg-12 mb-3">
                <Col md={12} className="mb-2">
                  <label> Card Number</label>
                  <div className="input-group">
                    <input
                      type="text"
                      className="form-control"
                      placeholder="Card number"
                      value={cardNumber}
                      onChange={handleCardNumberChange}
                      maxLength={cardType?.maxLength}
                    />

                    {cardType?.name !== "Unknown" && (
                      <div className="input-group-append">
                        <img
                          src={cardImages[cardType?.name]}
                          alt={cardType?.name}
                          style={{
                            width: "40px",
                            height: "auto",
                            marginLeft: "10px",
                            paddingTop: "5px",
                          }}
                        />
                      </div>
                    )}
                  </div>
                </Col>
              </div>
              <div className="col-lg-6 mb-3">
                <label> Expiry</label>
                <input
                  type="text"
                  className="form-control"
                  placeholder="MM / YY"
                  value={expiryDate}
                  onChange={handleExpiryChange}
                />
              </div>
              <div className="col-lg-6 mb-3">
                <label> Year</label>
                <input
                  type="number"
                  className="form-control"
                  value={formData.year}
                  placeholder="Year"
                  onChange={(e) =>
                    setFormData((prev) => ({
                      ...prev,
                      year: Number(e?.target?.value),
                    }))
                  }
                />
              </div>
              <div className="col-md-12">
                <div className="account_details-buttons d-flex flex-wrap justify-content-center gap-2">
                  <CustomButton
                    text="Cancel"
                    variant="grey"
                    onClick={() => setAddPaymentAccount(false)}
                  />
                  <CustomButton
                    onClick={() => {
                      handleSubmitPaymentAddress();
                    }}
                    text="Same As my primary Shipping Address"
                    variant="primary"
                  />
                  <CustomButton
                    onClick={() => {
                      handleUpdatePaymentAddress();
                    }}
                    text="Save Changes"
                    variant="secondary"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </CustomModal>
    </DefaultLayout>
  );
};

export default AccountDetails;
