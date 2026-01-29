/* eslint-disable react/jsx-key */
import { useContext, useEffect, useState } from "react";
import { nexticon, previcon } from "../../assets/images";

import { Swiper, SwiperSlide } from "swiper/react";
import { base_url } from "../../api";
import { toast } from "react-toastify";
import { useNavigate } from "react-router-dom";
// Import Swiper styles
import "swiper/css";
import "swiper/css/navigation";
import { Navigation } from "swiper/modules";
import { Image } from "react-bootstrap";
import { IPInfoContext } from "ip-info-react";
import { CartContext } from "../../Context/CartContext";
import { trackKlaviyoEvent } from "../../utils/klaviyo";

const OurProducts = () => {
  const navigate = useNavigate();

  const [products, setProducts] = useState([]);
  const [, setLoading] = useState(false);
  const [loadingBuyNow, setLoadingBuyNow] = useState(null);

  const [loadingbtn, setLoadingBtn] = useState(false);
  const token = localStorage.getItem("login");
  const { fetchCount } = useContext(CartContext);

  const [cartList, setCartList] = useState({
    product_id: "",
    qty: 1,
  });
  const fetchProducts = async () => {
    setLoading(true);

    await fetch(`${base_url}/products/?filter=top`, {
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

  // useEffect(() => {
  //   if (cartList?.product_id) {
  //     addToCart(cartList?.product_id);
  //   }
  // }, [cartList]);
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

  return (
    <section className="our-products">
      <div className="container">
        <div className="row">
          <div className="col-md-12">
            <div className="sec-content text-center">
              <h2>
                Our <span className="secondary-color">Products</span>
              </h2>
              <p className="ourProduct-text">
                Apricot Seeds, Vitamin B17 (Amygdalin) and more!
              </p>
              <hr
                className="border-buttom border-2 "
                style={{ borderColor: "black" }}
              />
            </div>
          </div>
          <div className="col-md-12">
            <Swiper
              modules={[Navigation]}
              slidesPerView={3}
              spaceBetween={20}
              centeredSlides={true}
              loop={true}
              navigation={{
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
              }}
              breakpoints={{
                320: {
                  slidesPerView: 1,
                },
                768: {
                  slidesPerView: 1,
                },
                992: {
                  slidesPerView: 3,
                },
              }}
              className="mySwiper"
            >
              <div className="our-products-swiper-buttons">
                <div className="swiper-button-next">
                  <img src={nexticon} alt="" />
                </div>
                <div className="swiper-button-prev">
                  <img src={previcon} alt="" />
                </div>
              </div>
              {products?.length !== 0 &&
                products?.map((item) => (
                  <SwiperSlide>
                    <div className="our-products-item">
                      <div
                        className="our-products-item-img cursor-pointer"
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
                          src={item?.image}
                          onError={(e) => {
                            e.target.onerror = null;
                            e.target.src = "/fallback.png";
                          }}
                          alt="Product Image"
                        />
                      </div>
                      <div className="our-products-item-content cursor">
                        <h4
                          className="text-black res-border"
                          // style={{height:"50px"}}
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
                          {/* <Link to={`/product-detail/${item?.id}`} className="text-theme"> */}
                          {item?.product_name}
                          {/* </Link> */}
                        </h4>
                        <div className="our-products-item-content-price">
                          {item?.map_price === item?.sell_price ? (
                            <span className="discount-price">
                              ${item?.sell_price}
                            </span>
                          ) : (
                            <>
                              <span className="actual-price">
                                ${item?.map_price}
                              </span>
                              <span className="discount-price">
                                ${item?.sell_price}
                              </span>
                            </>
                          )}
                        </div>
                        <div className="d-flex justify-content-evenly">
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
                  </SwiperSlide>
                ))}
            </Swiper>
          </div>
        </div>
      </div>
      <div className="justify-content-center d-flex mt-2">
        <button
          className="button-with-icon"
          onClick={() => {
            navigate("/store");
          }}
        >
          View All Products
        </button>
      </div>
    </section>
  );
};

export default OurProducts;
