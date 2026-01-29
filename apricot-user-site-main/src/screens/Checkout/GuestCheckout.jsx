/* eslint-disable react-hooks/exhaustive-deps */
import DefaultLayout from "../../components/DefaultLayout";
import { Accordion, Card, Row, Col } from "react-bootstrap";
import InnerBanner from "../../components/InnerBanner";
// import productImage from "../../assets/images/recomended-item-img.png";
import { useContext, useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import CustomButton from "../../components/CustomButton";
import CustomInput from "../../components/CustomInput";
import { useDispatch } from "react-redux";
import { IPInfoContext } from "ip-info-react";
import { base_url } from "../../api";
import { toast } from "react-toastify";
import { GetProfileData } from "../../redux/slices/userSlice";
import { FaMinus, FaPlus } from "react-icons/fa";
export default function GuestCheckout() {
  const [activeKey, setActiveKey] = useState("0");
  const [cartItems, setCartItems] = useState([]);
  const userInfo = useContext(IPInfoContext);

  console.log(userInfo);
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const [formData, setFormData] = useState({
    email: "",
    password: "",
  });
  const token = localStorage.getItem("login");
  const fetchProducts = () => {
    const queryParams = new URLSearchParams();

    if (userInfo.ip) queryParams.append("ip", userInfo.ip);
    document.querySelector(".loaderBox")?.classList.remove("d-none");
    fetch(`${base_url}/cart/?${queryParams}`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => response.json())
      .then(async (data) => {
        document.querySelector(".loaderBox")?.classList.add("d-none");
        setCartItems(data);
        await dispatch(GetProfileData());
      })
      .catch((error) => {
        console.log(error);
        document.querySelector(".loaderBox")?.classList.add("d-none");
      });
  };

  useEffect(() => {
    fetchProducts();
  }, [userInfo?.ip]);
  const handleSubmit = async (event) => {
    event.preventDefault();

    document.querySelector(".loaderBox").classList.remove("d-none");

    const apiUrl = `${base_url}/login`;
    const upgradeFormdata = { ...formData, ip: userInfo.ip };

    try {
      const response = await fetch(apiUrl, {
        method: "POST",
        headers: {
          "content-type": "application/json",
        },
        body: JSON.stringify(upgradeFormdata),
      });

      if (response.ok) {
        const responseData = await response.json();
        // toast.success(responseData?.message);
        localStorage.setItem("login", responseData?.token);
        document.querySelector(".loaderBox").classList.add("d-none");
        dispatch(GetProfileData(responseData?.token));
        if (responseData?.token) {
          navigate("/cart");
        }
      } else {
        document.querySelector(".loaderBox").classList.add("d-none");
        // alert("Invalid Credentials");
        const responseData = await response.json();
        toast.error(responseData?.message);
        if(responseData?.message == "Account already registered, please login!"){
          navigate('/login')
        }
        console.error("Login failed");
      }
    } catch (error) {
      document.querySelector(".loaderBox").classList.add("d-none");
      console.error("Error:", error);
    }
  };
  const handleRegister = async (event) => {
    event.preventDefault();

    document.querySelector(".loaderBox").classList.remove("d-none");

    const apiUrl = `${base_url}/user/auth/guest/register`;
    const upgradeFormdata = { email: formData.email, ip: userInfo.ip };

    try {
      const response = await fetch(apiUrl, {
        method: "POST",
        headers: {
          "content-type": "application/json",
        },
        body: JSON.stringify(upgradeFormdata),
      });

      if (response.ok) {
        const responseData = await response.json();
        // toast.success(responseData?.message);
        localStorage.setItem("login", responseData?.token);
        document.querySelector(".loaderBox").classList.add("d-none");
        dispatch(GetProfileData(responseData?.token));
        if (responseData?.token) {
          navigate("/cart");
        }
      } else {
        document.querySelector(".loaderBox").classList.add("d-none");
        // alert("Invalid Credentials");
        const responseData = await response.json();
        toast.error(responseData?.message);

        if(responseData?.message == "Account already registered, please login!"){
          navigate('/login')
        }
      }
    } catch (error) {
      document.querySelector(".loaderBox").classList.add("d-none");
      console.error("Error:", error);
    }
  };

  const toggleIcon = (key) => {
    return activeKey === key ? <FaMinus /> : <FaPlus />;
  };

  return (
    <DefaultLayout>
      <InnerBanner boldText1="Customer Information" />
      <div className="container my-5 ">
        <Row>
          <Col md={8} className="border p-4">
            <Accordion activeKey={activeKey} onSelect={(k) => setActiveKey(k)}>
              <Accordion.Item eventKey="0" className="accordion-item-spacing">
                <Accordion.Header>
                  {toggleIcon("0")}{" "}
                  <div className="w-100 d-flex justify-content-center align-items-center gap-2">
                    <h3 className="secondary-color fw-bold">
                      Existing Customer
                    </h3>
                  </div>
                </Accordion.Header>

                <Accordion.Body>
                  <div className="d-flex justify-content-end">
                    <form onSubmit={handleSubmit}  className="guest-form">
                      <CustomInput
                        label="Email Address"
                        required
                        id="userEmail"
                        type="email"
                        placeholder="Enter Your Email Address"
                        labelClass="mainLabel"
                        inputClass="mainInput"
                        onChange={(event) => {
                          setFormData({
                            ...formData,
                            email: event.target.value,
                          });
                        }}
                      />
                      <CustomInput
                        label="Password"
                        required
                        id="pass"
                        type="password"
                        placeholder="Enter Password"
                        labelClass="mainLabel"
                        inputClass="mainInput"
                        onChange={(event) => {
                          setFormData({
                            ...formData,
                            password: event.target.value,
                          });
                        }}
                      />

                      <div className="d-flex justify-content-end gap-3">
                        {/* <div>
                          <p className="text-center mt-5">
                            Forgot username or password?{" "}
                            <Link
                              to={"/register"}
                              className="text-dark text-decoration-underline"
                            >
                              Click Here
                            </Link>
                          </p>
                        </div> */}
                        <div style={{ marginTop: "43px" }}>
                          <CustomButton
                            variant="primaryButton"
                            text="Login"
                            type="submit"
                          />
                        </div>
                      </div>
                    </form>
                  </div>
                </Accordion.Body>
              </Accordion.Item>

              <Accordion.Item eventKey="1" className="accordion-item-spacing">
                <Accordion.Header>
                  {toggleIcon("1")}{" "}
                  <div className="w-100 d-flex justify-content-center align-items-center gap-2">
                    <h3 className="secondary-color fw-bold">New Customer</h3>
                  </div>
                </Accordion.Header>
                <Accordion.Body>
                  <Accordion.Body>
                    <div className="d-flex justify-content-end">
                      <form onSubmit={handleRegister} className="guest-form">
                        <CustomInput
                          label="Email Address"
                          required
                          id="userEmail"
                          type="email"
                          placeholder="required for order confirmation"
                          labelClass="mainLabel"
                          inputClass="mainInput"
                          onChange={(event) => {
                            setFormData({
                              ...formData,
                              email: event.target.value,
                            });
                          }}
                        />

                        <div className="d-flex align-items-start mt-1">
                          <input
                            type="checkbox"
                            name="rememberMe"
                            id="rememberMe"
                            className="me-2 mt-1"
                            style={{ minWidth: "16px", height: "16px" }}
                          />
                          <label htmlFor="rememberMe" className="fw-semibold">
                            <b>
                              I have read and understand{" "}
                              <a href="" className="text-decoration-underline">
                                Apricot Power&#39;s privacy policy
                              </a>
                            </b>{" "}
                            which details personal information collected, why
                            and how it is used, and the rights I have over my
                            data. <b>(Must be checked to continue.)</b>
                          </label>
                        </div>
                        <p className="mt-4 text-center">
                          <strong>
                            <a href="" target="_blank">
                              Subscribe to our Email List
                            </a>{" "}
                            for promos, news and more!
                          </strong>
                        </p>
                        <div className="d-flex justify-content-end gap-3">
                          <div style={{ marginTop: "43px" }}>
                            <CustomButton
                              variant="primaryButton"
                              text="Continue"
                              type="submit"
                            />
                          </div>
                        </div>
                      </form>
                    </div>
                  </Accordion.Body>
                </Accordion.Body>
              </Accordion.Item>

              <Accordion.Item eventKey="2" className="accordion-item-spacing">
                <Accordion.Header>
                  {toggleIcon("2")}{" "}
                  <div className="w-100 d-flex justify-content-center align-items-center gap-2">
                    <h3 className="secondary-color fw-bold">
                      Checkout as a Guest
                    </h3>
                  </div>
                </Accordion.Header>
                <Accordion.Body>
                  <div className="d-flex justify-content-end">
                    <form onSubmit={handleRegister} className="guest-form">
                      <CustomInput
                        label="Email Address"
                        required
                        id="userEmail"
                        type="email"
                        placeholder="required for order confirmation"
                        labelClass="mainLabel"
                        inputClass="mainInput"
                        onChange={(event) => {
                          setFormData({
                            ...formData,
                            email: event.target.value,
                          });
                        }}
                      />

                      <div className="d-flex align-items-start mt-1">
                        <input
                          type="checkbox"
                          name="rememberMe"
                          id="rememberMe"
                          className="me-2 mt-1"
                          style={{ minWidth: "16px", height: "16px" }}
                        />
                        <label htmlFor="rememberMe" className="fw-semibold">
                          <b>
                            I have read and understand{" "}
                            <a
                              href="/privacy.asp"
                              className="text-decoration-underline"
                            >
                              Apricot Power&#39;s privacy policy
                            </a>
                          </b>{" "}
                          which details personal information collected, why and
                          how it is used, and the rights I have over my data.{" "}
                          <b>(Must be checked to continue.)</b>
                        </label>
                      </div>
                      <p className="mt-4 text-center">
                        <strong>
                          <a href="/user/email-preferences" target="_blank">
                            Subscribe to our Email List
                          </a>{" "}
                          for promos, news and more!
                        </strong>
                      </p>
                      <div className="d-flex justify-content-end gap-3">
                        <div style={{ marginTop: "43px" }}>
                          <CustomButton
                            variant="primaryButton"
                            text="Continue"
                            type="submit"
                          />
                        </div>
                      </div>
                    </form>
                  </div>
                </Accordion.Body>
              </Accordion.Item>
            </Accordion>
          </Col>

          <Col md={4}>
            <Card className="shadow-sm mt-3 mt-md-0">
              <Card.Body>
                {cartItems?.items?.map((item) => (
                  <div
                    className="d-flex align-items-center  mb-3"
                    key={item?.id}
                  >
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
                    <div className="ms-3">
                      <div className="fw-bold text-end">
                        {item?.product_name}
                      </div>
                      <div className="text-muted">${item?.sub_total}</div>
                    </div>
                  </div>
                ))}

                <hr />
                <div className="d-flex justify-content-between text-muted">
                  <span>Subtotal</span>
                  <span>${cartItems?.total}</span>
                </div>
                <div className="d-flex justify-content-between text-muted">
                  <span>Shipping</span>
                  <span>--</span>
                </div>
                <div className="d-flex justify-content-between text-muted">
                  <span>Handling Fee</span>
                  <span>--</span>
                </div>
                <hr />
                <div className="d-flex justify-content-between fw-bold">
                  <span>Total</span>
                  <span>${cartItems?.total}</span>
                </div>
              </Card.Body>
            </Card>
          </Col>
        </Row>
      </div>
    </DefaultLayout>
  );
}
