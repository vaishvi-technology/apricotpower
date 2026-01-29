/* eslint-disable react/jsx-key */
import { useContext, useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";

// import { rating } from "../../assets/images";
import { Row, Col, Button, Spinner, Image } from "react-bootstrap";
import bannnerImage from "../../assets/images/banner.png";
import shiipingimage from "../../assets/images/shiipingimage.png";
import { base_url } from "../../api";
import { Link, useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import { useDispatch } from "react-redux";
import { GetProfileData } from "../../redux/slices/userSlice";
import CustomInput from "../../components/CustomInput";
import CustomButton from "../../components/CustomButton";
import { FaInfoCircle, FaTrash } from "react-icons/fa";
import InnerBanner from "../../components/InnerBanner";
import { Helmet } from "react-helmet-async";
import { getTag } from "../../api/Services/getDynamicData";
import { IPInfoContext } from "ip-info-react";
import { FaInfo, FaQuestion } from "react-icons/fa6";
import { CartContext } from "../../Context/CartContext";

const Cart = () => {
  //     console.log(window.loyaltylion?.customer?.claimedRewards?.[0]?.redeemable?.code)
  // console.log(window.loyaltylion?.customer?.claimedRewards?.[0]?.redeemable?.discount_amount)
  // console.log(window.loyaltylion?.customer?.claimedRewards?.[0]?.redeemable?.discount_amount)
  // console.log(window.loyaltylion?.customer?.claimedRewards?.[0]?.redeemable?.redeemable_type)
  // console.log(window.loyaltylion?.customer?.merchantId)
  const userInfo = useContext(IPInfoContext);
  const { fetchCount } = useContext(CartContext);
  const dispatch = useDispatch();
  const [cartItems, setCartItems] = useState([]);
  const [tag, setTag] = useState([]);
  const [products, setProducts] = useState([]);
  const [cartList, setCartList] = useState({
    product_id: "",
    qty: 1,
  });
  const [loadingbtn, setLoadingBtn] = useState(false);
  const [formData, setFormData] = useState({
    promo: "",
  });

  const token = localStorage.getItem("login");
  const navigate = useNavigate();
  const fetchProductsFuel = () => {
    document.querySelector(".loaderBox").classList.remove("d-none");
    fetch(`${base_url}/products/`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => response.json())
      .then(async (data) => {
        document.querySelector(".loaderBox").classList.add("d-none");
        setProducts(data);
      })
      .catch((error) => {
        console.log(error);
        document.querySelector(".loaderBox").classList.add("d-none");
      });
  };

  useEffect(() => {
    fetchProductsFuel();
  }, []);
  const [loading, setLoading] = useState(true);
  const referalCode = localStorage.getItem("referalCode");
  const fetchProducts = () => {
    const queryParams = new URLSearchParams();

    if (!token && userInfo?.ip) {
      queryParams.append("ip", userInfo.ip);
    }
    if (referalCode) {
      queryParams.append("refferal_code", referalCode);
    }

    setLoading(true);

    fetch(`${base_url}/cart/?${queryParams.toString()}`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        ...(token && { Authorization: `Bearer ${token}` }),
      },
    })
      .then((response) => response.json())
      .then((data) => {
        setCartItems(data);
        setLoading(false);
      })
      .catch((error) => {
        console.log(error);
        setLoading(false);
      });
  };

  useEffect(() => {
    fetchProducts();
  }, [userInfo?.ip, referalCode]);

  const updateQuantityAPI = async (productId, action, qty = null) => {
    try {
      const queryParams = new URLSearchParams();

      if (!token && userInfo?.ip) {
        queryParams.append("ip", userInfo.ip);
      }

      document.querySelector(".loaderBox")?.classList.remove("d-none");

      const bodyData = { id: productId, action };

      if (action === "manual") {
        bodyData.qty = qty; // send manual input
      }

      const res = await fetch(
        `${base_url}/cart/update-quantity?${queryParams}`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
          body: JSON.stringify(bodyData),
        }
      );

      // const data = await res.json();

      // // toast.success(data?.message);;
      document.querySelector(".loaderBox")?.classList.add("d-none");

      fetchProducts(); // refresh cart
    } catch (err) {
      document.querySelector(".loaderBox")?.classList.add("d-none");

      console.error("Failed to update quantity", err);
    }
  };

  const removeItemAPI = async (productId) => {
    try {
      const queryParams = new URLSearchParams();
      if (!token && userInfo?.ip) {
        queryParams.append("ip", userInfo.ip);
      }
      document.querySelector(".loaderBox")?.classList.remove("d-none");

      const res = await fetch(
        `${base_url}/cart/delete/${productId}?${queryParams}`,
        {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );
      document.querySelector(".loaderBox")?.classList.add("d-none");

      const data = await res.json();

      // toast.success(data?.message);;
      fetchCount();
      fetchProducts();
    } catch (err) {
      document.querySelector(".loaderBox")?.classList.add("d-none");

      console.error("Failed to delete item", err);
    }
  };

  const handleChange = (e) => {
    setFormData({
      promo: e?.target?.value,
    });
  };

  const HandlePromo = async () => {
    try {
      const enteredCode = formData?.promo?.trim();
      const claimedRewards = window.loyaltylion?.customer?.claimedRewards || [];

      const matchedCode = claimedRewards.find(
        (reward) =>
          reward?.redeemable?.code?.toLowerCase() === enteredCode?.toLowerCase()
      );
      // console.log(matchedCode)
      const queryParams = new URLSearchParams();
      if (!token && userInfo?.ip) {
        queryParams.append("ip", userInfo.ip);
      }
      const payloadll = {
        reward_id: matchedCode?.id,
        discount_amount: matchedCode?.reward?.discount_amount,
        discount_type: matchedCode?.reward?.discount_type,
        promoCode_type: "LL",
        promo: formData.promo,
      };
      const payload = {
        promoCode_type: "NORMAL",
        promo: formData.promo,
      };
      const FinalPayload = claimedRewards.length > 0 ? payloadll : payload;
      document.querySelector(".loaderBox").classList.remove("d-none");

      const res = await fetch(`${base_url}/apply/promo?${queryParams}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          ...(token && { Authorization: `Bearer ${token}` }),
        },
        body: JSON.stringify(FinalPayload),
      });

      document.querySelector(".loaderBox").classList.add("d-none");
      const data = await res.json();

      fetchProducts();

      if (!data?.message) {
        // toast.success("Promo Code Applied Successfully");
      } else {
        toast.error(data?.message);
      }
    } catch (err) {
      document.querySelector(".loaderBox").classList.add("d-none");
      console.log("Order failed:", err);
      alert("Something went wrong!");
    }
  };

  const DeletePromo = async () => {
    try {
      const res = await fetch(`${base_url}/remove/promo/${cartItems?.id}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });
      document.querySelector(".loaderBox").classList.add("d-none");
      const data = await res.json();
      // console.log(data,'res')
      fetchProducts();
      if (data?.message) {
        // toast.success(data?.message);;
      } else {
        toast.error(data?.message);
      }
    } catch (err) {
      //  toast.success(err?.?.data?.message);
      document.querySelector(".loaderBox").classList.add("d-none");

      console.log("Order failed:", err);
      alert("Something went wrong!");
    }
  };

  const addToCart = async () => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");

      if (token === null) {
        const upgradeFormdata = { ...cartList, ip: userInfo.ip };
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
          
          // toast.success(
          //   guestData?.message || "Added to guest cart based on IP."
          // );
        } else {
          toast.error(guestData?.message || "Guest cart failed.");
        }

        document.querySelector(".loaderBox").classList.add("d-none");
        return;
      }

      const response = await fetch(`${base_url}/cart/add`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(cartList),
      });

      const responseData = await response.json();

      if (responseData?.status === 200) {
        fetchCount();
        
        // toast.success(responseData?.message);
        fetchProducts();
      } else {
        toast.error(responseData?.message || "Failed to add to cart.");
      }
    } catch (error) {
      console.error("Cart error:", error);
      toast.error("Something went wrong.");
    } finally {
      setLoadingBtn(null);
      document.querySelector(".loaderBox").classList.add("d-none");
    }
  };

  const handleCart = (id) => {
    if (id) {
      setLoadingBtn(id);
    }
    setCartList({
      ...cartList,
      product_id: id,
    });
  };
  useEffect(() => {
    if (cartList?.product_id) {
      addToCart();
    }
  }, [cartList]);
  const fetchTag = async () => {
    try {
      const data = await getTag(16);
      setTag(data?.[0]);
    } catch (error) {
      console.log({ error });
    }
  };
  useEffect(() => {
    fetchTag();
  }, []);
  useEffect(() => {
    const script = document.createElement("script");
    script.src = "https://api.feefo.com/api/javascript/apricot-power";
    script.async = true;
    document.body.appendChild(script);

    return () => {
      document.body.removeChild(script);
    };
  }, [products]);

  useEffect(() => {
    if (
      !loading &&
      cartItems.message &&
      cartItems.message === "Cart is empty"
    ) {
      navigate("/store");
    }
  }, [loading, cartItems, navigate]);

  return (
    <>
      <Helmet>
        {tag.PageMetaTitle && <title>{tag.PageMetaTitle}</title>}

        {tag?.PageShowMETA === 1 && (
          <>
            <meta name="description" content={tag.PageMetaDesc || ""} />
            <meta name="keywords" content={tag.PageMetaKeywords || ""} />

            <meta property="og:title" content={tag.PageOGTitle || ""} />
            <meta property="og:type" content={tag.PageOGType || ""} />
            <meta property="og:url" content={tag.PageOGURL || ""} />
            <meta property="og:image" content={tag.PageOGImage || ""} />
          </>
        )}
      </Helmet>
      <DefaultLayout>
        <InnerBanner boldText="Cart" />
        <div className="my-5 container ">
          {/* {cartItems.message === "Cart is empty" ? (
            <h2 className="mb-4">No Cart Found</h2>
          ) : (
            <h2 className="mb-4">Cart Items</h2>
          )} */}
          <Row className={cartItems?.message && "d-none"}>
            <Col md={8}>
              <div className="email-preferences-sec pt-0">
                <div className="cart-items-row">
                  <div className="row mt-2">
                    {/* <div className="row justify-content-center mb-3">
                      <div className="col-md-12">
                        <div className="cart-banner-img">
                          <img
                            src={bannnerImage}
                            alt="Banner Image"
                            className="img-fluid w-100"
                          />
                        </div>
                      </div>
                    </div> */}
                    {loading ? (
                      <div className="text-center py-5">Loading cart...</div>
                    ) : cartItems?.items?.length > 0 ? (
                      cartItems.items.map((item) => (
                        <>
                          <div
                            className="cart-item-button cursor"
                            onClick={() => removeItemAPI(item.id)}
                          >
                            <FaTrash color="red" />
                          </div>
                          <div className="cart-item" key={item.id}>
                            <div className="cart-item-img">
                              <img src={item?.image} alt="Product Image" />
                            </div>
                            <div className="cart-item-content">
                              <h3>{item?.product_name}</h3>
                              <div className="cart-price-qty">
                                <div className="cart-price">
                                  ${item?.price_per_product}
                                </div>
                                <div className="cart-qty">
                                  <Button
                                    variant="outline-secondary"
                                    size="sm"
                                    onClick={() =>
                                      updateQuantityAPI(item.id, "decrease")
                                    }
                                  >
                                    -
                                  </Button>

                                  <input
                                    type="number"
                                    value={item?.qty}
                                    onChange={(e) =>
                                      updateQuantityAPI(
                                        item.id,
                                        "manual",
                                        Number(e.target.value)
                                      )
                                    }
                                    className="qty-input"
                                  />

                                  <Button
                                    variant="outline-secondary"
                                    size="sm"
                                    onClick={() =>
                                      updateQuantityAPI(item.id, "increase")
                                    }
                                  >
                                    +
                                  </Button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </>
                      ))
                    ) : (
                      <div className="text-center py-5">Your cart is empty</div>
                    )}
                  </div>
                </div>
                {cartItems?.accept_promo ? (
                  <div className="cart-form-row">
                    <div className="row">
                      <div className="col-md-12">
                        <div className="row">
                          <div className="cart-form-inputs">
                            <CustomInput
                              placeholder="Enter Promo Code"
                              label="Promo Code"
                              required
                              id="promoCode"
                              type="text"
                              disabled={!token}
                              labelClass="mainLabel "
                              inputClass="mainInput "
                              name="promo"
                              value={formData.promo}
                              onChange={handleChange}
                            />
                            <div className="mt-4">
                              <CustomButton
                                disabled={!token}
                                text="Apply"
                                onClick={HandlePromo}
                                variant="primaryButton"
                              />
                            </div>
                            {!token && (
                              <p style={{ fontSize: "15px" }}>
                                Please{" "}
                                <Link to={"/login"} className="greenColor">
                                  create an account or log in
                                </Link>{" "}
                                to use a promo code.
                              </p>
                            )}
                          </div>
                        </div>
                      </div>
                      {/* <div className="col-md-3">
                    <div className="cart-update-btnDiv d-flex align-items-center justify-content-center h-100">
                      <CustomButton text="Update Cart" variant="primaryButton" />
                    </div>
                  </div> */}
                    </div>
                  </div>
                ) : (
                  <div className="cart-form-row">
                    <div className="row">
                      <div className="col-md-12">
                        <div className="mt-4">
                          <h4 className="d-flex align-items-center ms-2">
                            {cartItems?.promo}
                          </h4>
                          <CustomButton
                            text="Remove Promo Code"
                            onClick={DeletePromo}
                            variant="dangerButton"
                          />
                          <FaInfoCircle
                            title="Only one promo code can be applied per order"
                            size={25}
                            className="ms-2 text-muted cursor-pointer"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                )}

                <div className="cart-subTotal-div">
                  <div className="row">
                    <div className="col-md-8">
                      <div className="cart-subTotal-left">
                        <div
                          dangerouslySetInnerHTML={{
                            __html: cartItems?.shipping_message,
                          }}
                        ></div>

                        {/* <p>
                          *Excludes Alaska, Hawaii, Puerto Rico, Guam and US
                          Virgin Islands.
                        </p> */}
                      </div>
                    </div>
                    <div className="col-md-4">
                      <div className="cart-subTotal-right">
                        {cartItems?.discount > 0 && (
                          <>
                            <p>Discount</p>
                            <p>
                              ${Number(cartItems?.discount)?.toFixed(2)} USD
                            </p>
                          </>
                        )}

                        <p>Subtotal</p>
                        <p>${cartItems?.sub_total} USD</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </Col>
            <Col md={4}>
              <div className="">
                <div className="cart-finish-shopping">
                  <h4>FINISH SHOPPING</h4>
                  <img src={shiipingimage} alt="FINISH SHOPPING" />
                  <h5>Check/Money Order</h5>

                  <CustomButton
                    text="CHECKOUT"
                    variant="primaryButton"
                    //  style={{ width: "52%" }}
                    // disabled={selectOrderSource ? false : true}
                    onClick={() =>
                      navigate(token ? "/checkout" : "/store/customer_info")
                    }
                  />
                </div>
              </div>
            </Col>
            <h2 className="text-center mt-5 fw-600">
              You may also be interested in...
            </h2>
            <section className="fuel-product-sec fuel-product-secnew">
              <div className="row justify-content-center">
                {cartItems?.related_products?.map((item, index) => (
                  <div className="col-xl-4 col-lg-6 col-md-6 " key={index}>
                    <div className="fuel-product-item product-margin">
                      <div className="fuel-product-item-top">
                        <div
                          className="fuel-product-img cursor-pointer"
                          onClick={() => {
                            const slug = `${item?.product_url.replace(
                              /\s+/g,
                              "-"
                            )}`;
                            navigate(`/item/${slug}`, {
                              state: { productID: item?.id },
                            });
                          }}
                        >
                          <Image
                            style={{
                              width: "200px",
                              height: "250px",
                            }}
                            src={item.image}
                            alt="Product Image"
                          />
                        </div>
                      </div>
                      <div className="fuel-product-item-content cursor">
                        <h3
                          onClick={() => {
                            const slug = `${item?.product_url.replace(
                              /\s+/g,
                              "-"
                            )}`;
                            navigate(`/item/${slug}`, {
                              state: { productID: item?.id },
                            });
                          }}
                          className="text-theme h-89"
                        >
                          {item.product_name}
                        </h3>

                        <div className="hot-buys-item-content-price">
                          <span className="price">Price:</span>{" "}
                          {item?.map_price === item?.sell_price ? (
                            <span className="discount-price secondary-color">
                              ${item?.sell_price}
                            </span>
                          ) : (
                            <>
                              <span className="actual-price">
                                ${item?.map_price}
                              </span>{" "}
                              <span className="discount-price secondary-color">
                                ${item?.sell_price}
                              </span>
                            </>
                          )}
                        </div>
                        <div className="hot-buys-item-description">
                          <p>{item?.you_save}</p>
                          <p
                            title={`You Must Have at least ${item?.min_quantity} items in your cart to receive this discounted base price `}
                          >
                            minimum purchase required (?)
                          </p>
                        </div>
                        <div
                          className="fuel-product-rating"
                          style={{ height: "30px",position: "relative",top:'10px' }}
                        >
                          <div
                            className="feefo-product-stars-widget "
                            data-product-sku={item?.item_code}
                          />
                        </div>
                        <button
                          className="button-with-icon"
                          onClick={() => {
                            handleCart(item?.id);
                          }}
                        >
                          Add To Cart{" "}
                          {item?.id === loadingbtn && <Spinner size="sm" />}
                        </button>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </section>
          </Row>
        </div>
      </DefaultLayout>
    </>
  );
};

export default Cart;
