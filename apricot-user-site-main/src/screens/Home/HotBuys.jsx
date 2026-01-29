/* eslint-disable no-irregular-whitespace */
import { useContext, useEffect, useState } from "react";

import { nexticon, previcon } from "../../assets/images";

import { Swiper, SwiperSlide } from "swiper/react";
import hotBuyimage from "../../assets/images/hot-buy.png";
// Import Swiper styles
import "swiper/css";
import "swiper/css/navigation";

import { Navigation } from "swiper/modules";
import { base_url } from "../../api";
import { useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import { Image, Spinner } from "react-bootstrap";
import { IPInfoContext } from "ip-info-react";
import { CartContext } from "../../Context/CartContext";
import { trackKlaviyoEvent } from "../../utils/klaviyo";

const HotBuys = () => {
  const { fetchCount } = useContext(CartContext);
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(false);
  const [loadingBuyNow, setLoadingBuyNow] = useState(null);
  const [loadingbtn, setLoadingBtn] = useState(null);
  const navigate = useNavigate();
  const [cartList, setCartList] = useState({
    product_id: "",
    qty: 1,
  });

  const token = localStorage.getItem("login");

  const fetchProducts = async () => {
    setLoading(true);

    await fetch(`${base_url}/products/?filter=hot-products`, {
      method: "GET",

      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => response.json())
      .then((data) => {
        setLoading(false);

        setProducts(data);
      })
      .catch((error) => {
        console.log(error);
        setLoading(false);
      });
  };

  useEffect(() => {
    fetchProducts();
  }, []);

  const handleCart = (id) => {
    setCartList({
      ...cartList,
      product_id: id,
    });
  };
  const userInfo = useContext(IPInfoContext);
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
  //         fetchCount();
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
  //       fetchCount();
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
        // fetchProducts();
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
    const script = document.createElement("script");
    script.src = "https://api.feefo.com/api/javascript/apricot-power";
    script.async = true;
    document.body.appendChild(script);

    return () => {
      document.body.removeChild(script);
    };
  }, [products]);
  // useEffect(() => {
  //   if (cartList?.product_id) {
  //     addToCart();
  //   }
  // }, [cartList]);
  console.log(products);
  return loading ? (
    <div className=" d-flex justify-content-center align-items-center m-5">
      <div className="spinner-border text-warning" role="status">
        <span className="visually-hidden">Loading...</span>
      </div>
    </div>
  ) : (
    <section className="hot-buys-sec">
      <div className="">
        <div className="col-md-12">
          <div className="sec-content">
            <div className="row align-items-center ">
              <div className="col-12 col-md-8 d-flex justify-content-md-end justify-content-center mb-3 mb-md-0 text-md-end text-center">
                <h2 className="hot-buy-heading">
                  Hot <span className="primary-color">Buys</span>
                </h2>
              </div>
              <div className="col-12 col-md-4 d-flex justify-content-md-end justify-content-center text-md-end text-center">
                <img
                  src={hotBuyimage}
                  alt="Hot Buy"
                  className="hot-buy-image"
                />
              </div>
            </div>
          </div>
        </div>
        <div className="col-md-12 top-buttom-border">
          <div className="hot-buys-slider-content">
            <h3 className="hot-buys-slider-title">OUR SHOP</h3>

            <Swiper
              modules={[Navigation]}
              // slidesPerView={4}
              spaceBetween={0}
              // centeredSlides={true}
              // loop={true}
              navigation={{
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
              }}
              breakpoints={{
                320: {
                  slidesPerView: 1,
                },
                736: {
                  slidesPerView: 2,
                },
                850: {
                  slidesPerView: 3,
                },
                1280: {
                  slidesPerView: 3,
                },
                1300: {
                  slidesPerView: 4,
                },
              }}
              className="mySwiper hot-buys-swiper"
            >
              <div className="hot-buys-swiper-buttons">
                <div
                  className="swiper-button-next "
                  // style={{ marginRight: "20px" }}
                >
                  <img src={nexticon} alt="" />
                </div>
                <div
                  className="swiper-button-prev"
                  // style={{ marginRight: "20px" }}
                >
                  <img src={previcon} alt="" />
                </div>
              </div>
              {products?.length !== 0 &&
                products.map((item, index) => (
                  <SwiperSlide key={index}>
                    <div className="hot-buys-item left-border">
                      <div
                        className="cursor-pointer"
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
                          width={300}
                          height={400}
                          onError={(e) => {
                            e.target.onerror = null;
                            e.target.src = "/fallback.png";
                          }}
                          src={item?.image}
                          alt="Product 1"
                        />
                      </div>
                      <div className="hot-buys-item-content cursor">
                        <h4
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
                          {/* <Link
                            to={`/product-detail/${item?.id}`}
                            className="text-theme"
                          > */}
                          {item.product_name}
                          {/* </Link> */}
                        </h4>

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
                            minimum purchase required (?)
                          </p>
                        </div>
                        <div
                          className="feefo-product-stars-widget"
                          data-product-sku={item?.item_code}
                        ></div>
                        {/* <div className="rating">
                          <img src={rating} alt="Rating" />
                        </div> */}
                        <div className="d-flex justify-content-between ">
                          <button
                            className="add-to-cart-btn"
                            onClick={() => {
                              // handleCart(item?.id);
                              // addToCart(item?.id, "buy");
                              addToCart(item?.id, "buy", 1, item);
                            }}
                          >
                            Buy Now{" "}
                            {item?.id === loadingBuyNow && (
                              <Spinner size="sm" />
                            )}
                          </button>
                          <button
                            className="add-to-cart-btn"
                            onClick={() => {
                              // handleCart(item?.id);
                              // addToCart(item?.id, "addtocard");
                              addToCart(item?.id, "addtocard", 1, item);
                            }}
                          >
                            Add To Cart{" "}
                            {item?.id === loadingbtn && <Spinner size="sm" />}
                          </button>
                        </div>
                      </div>
                    </div>
                  </SwiperSlide>
                ))}
            </Swiper>
          </div>
        </div>
      </div>
    </section>
  );
};

export default HotBuys;
