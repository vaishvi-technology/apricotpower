import React, { useContext, useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";

import { rating } from "../../assets/images";
import { base_url } from "../../api";
import hotBuyimage from "../../assets/images/hot-buy.png";
import { Link, useLocation, useNavigate, useParams } from "react-router-dom";
import { toast } from "react-toastify";
import { useDispatch } from "react-redux";
import { GetProfileData } from "../../redux/slices/userSlice";
import { Image, Spinner } from "react-bootstrap";
import { IPInfoContext } from "ip-info-react";
import { CartContext } from "../../Context/CartContext";
import { trackKlaviyoEvent } from "../../utils/klaviyo";

const TagsProduct = () => {
  const dispatch = useDispatch();
  const location = useLocation();
  const [loadingBuyNow, setLoadingBuyNow] = useState(null);
  const { id } = useParams();
  const { fetchCount } = useContext(CartContext);

  const finalid = parseInt(id.split("-")[0]);

  const [loadingbtn, setLoadingBtn] = useState(false);
  const navigate = useNavigate();

  const [products, setProducts] = useState([]);
  const [cartList, setCartList] = useState({
    product_id: "",
    qty: 1,
  });
  const token = localStorage.getItem("login");

  const fetchProducts = () => {
    document.querySelector(".loaderBox").classList.remove("d-none");
    fetch(`${base_url}/product-by-tags/${finalid}`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        // Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => response.json())
      .then(async (data) => {
        document.querySelector(".loaderBox").classList.add("d-none");
        setProducts(data);

        await dispatch(GetProfileData());
      })
      .catch((error) => {
        console.log(error);
        document.querySelector(".loaderBox").classList.add("d-none");
      });
  };

  useEffect(() => {
    fetchProducts();
  }, [finalid]);
  const userInfo = useContext(IPInfoContext);
  const handleCart = (id) => {
    setCartList({
      ...cartList,
      product_id: id,
    });
  };

  // const addToCart = async (id, type) => {
  //   if (type == "addtocard") {
  //     setLoadingBtn(id);
  //   } else {
  //     setLoadingBuyNow(id);
  //   }

  //   try {
  //     if (token === null) {
  //       const upgradeFormdata = { ...cartList, ip: userInfo.ip };
  //       const response = await fetch(`${base_url}/cart/guest/add`, {
  //         method: "POST",
  //         headers: {
  //           "Content-Type": "application/json",
  //         },
  //         body: JSON.stringify(upgradeFormdata),
  //       });

  //       const guestData = await response.json();

  //       if (response.ok) {
  //         toast.success(
  //           guestData?.message || "Added to guest cart based on IP."
  //         );
  //         if (type === "buy" && !token) {
  //           navigate("/store/customer_info");
  //         }
  //       } else {
  //         toast.error(guestData?.message || "Guest cart failed.");
  //       }

  //       document.querySelector(".loaderBox").classList.add("d-none");
  //       return;
  //     }

  //     const response = await fetch(`${base_url}/cart/add`, {
  //       method: "POST",
  //       headers: {
  //         "Content-Type": "application/json",
  //         Authorization: `Bearer ${token}`,
  //       },
  //       body: JSON.stringify(cartList),
  //     });

  //     const responseData = await response.json();
  //     if (responseData?.status === 200) {
  //       if (type === "buy" && token) {
  //         navigate("/checkout");
  //       }
  //       toast.success(responseData?.message);

  //       fetchProducts();
  //     } else {
  //       toast.error(responseData?.message || "Failed to add to cart.");
  //     }
  //   } catch (error) {
  //     console.error("Cart error:", error);
  //     toast.error("Something went wrong.");
  //   } finally {
  //     setLoadingBtn(null);
  //     setLoadingBuyNow(null);
  //     document.querySelector(".loaderBox").classList.add("d-none");
  //   }
  // };
  const addToCart = async (id, type, qty = 1, item) => {
    trackKlaviyoEvent("Added to Cart", item);
    if (type === "addtocard") {
      setLoadingBtn(id);
    } else {
      setLoadingBuyNow(id);
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
          // toast.success(
          //   guestData?.message || "Added to guest cart based on IP."
          // );
          if (type === "buy" && !token) {
            navigate("/store/customer_info");
          }
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
        body: JSON.stringify(payload),
      });

      const responseData = await response.json();
      if (responseData?.status === 200) {
        fetchCount();
        navigate("/cart");
        if (type === "buy" && token) {
          navigate("/checkout");
        }
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
      setLoadingBuyNow(null);
      document.querySelector(".loaderBox").classList.add("d-none");
    }
  };
  useEffect(() => {
    if (cartList?.product_id) {
      addToCart();
    }
  }, [cartList]);

  useEffect(() => {
    const script = document.createElement("script");
    script.src = "https://api.feefo.com/api/javascript/apricot-power";
    script.async = true;
    document.body.appendChild(script);

    return () => {
      document.body.removeChild(script);
    };
  }, [products]);
  return (
    <DefaultLayout>
      <InnerBanner boldText={products?.name} />
      <section className="fuel-product-sec">
        <div className="container">
          <div className="row justify-content-center">
            {products?.products?.length > 0 &&
              (products?.products || []).map((item, index) => (
                <div className="col-xl-4 col-lg-6 col-md-6 mb-5" key={index}>
                  <div className="fuel-product-item">
                    <div className="fuel-product-item-top">
                      <div className="fuel-product-img">
                        <Image
                          style={{
                            width: "200px",
                            height: "250px",
                            // transform: "rotate(-15deg)",
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
                        className="text-theme"
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
                        <p style={{ height: "20px" }}>{item?.you_save}</p>
                        <p
                          title={`You Must Have at least ${item?.min_quantity} items in your cart to receive this discounted base price `}
                        >
                          minimum purchase required (?)
                        </p>
                      </div>
                      <div
                        className="fuel-product-rating"
                        style={{ height: "30px", marginTop: "10px" }}
                      >
                        <div
                          className="feefo-product-stars-widget  "
                          data-product-sku={item?.item_code}
                        />
                      </div>
                      <div className="d-flex justify-content-evenly w-100">
                        <button
                          className="button-with-icon"
                          onClick={() => {
                            // handleCart(item?.id);
                            // addToCart(item?.id, "buy");
                            addToCart(item?.id, "buy", 1, item);
                          }}
                        >
                          Buy Now{" "}
                          {item?.id === loadingBuyNow && (
                            <div
                              className="spinner-border text-white"
                              style={{ width: "20px", height: "20px" }}
                              role="status"
                            >
                              <span className="visually-hidden">
                                Loading...
                              </span>
                            </div>
                          )}
                        </button>
                        <button
                          className="button-with-icon"
                          onClick={() => {
                            // handleCart(item?.id);
                            // addToCart(item?.id, "addtocard");
                            addToCart(item?.id, "addtocard", 1, item);
                          }}
                        >
                          Add To Cart{" "}
                          {item?.id === loadingbtn && (
                            <div
                              className="spinner-border text-white"
                              style={{ width: "20px", height: "20px" }}
                              role="status"
                            >
                              <span className="visually-hidden">
                                Loading...
                              </span>
                            </div>
                          )}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
          </div>
        </div>
        <div className="col-12 col-md-5 d-flex  ">
          <img
            src={hotBuyimage}
            alt="Hot Buy"
            style={{ width: "250px", maxWidth: "100%", height: "150px" }}
          />
        </div>
      </section>
    </DefaultLayout>
  );
};

export default TagsProduct;
