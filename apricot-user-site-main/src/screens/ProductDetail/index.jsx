/* eslint-disable react-hooks/exhaustive-deps */
/* eslint-disable no-irregular-whitespace */
import { useState, useEffect, useContext } from "react";
import { useNavigate, useParams } from "react-router-dom";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { base_url } from "../../api";
import { Button, Col, Image, Tab, Tabs } from "react-bootstrap";

import { toast } from "react-toastify";
import PdfView from "./PdfView";
import { Helmet } from "react-helmet-async";
import { IPInfoContext } from "ip-info-react";
import { CartContext } from "../../Context/CartContext";
import { trackKlaviyoEvent } from "../../utils/klaviyo";

const ProductDetail = () => {
  const { id } = useParams();
  const finalid = parseInt(id.split("-")[0]); // 374 as a number

  const token = localStorage.getItem("login");
  const { fetchCount } = useContext(CartContext);
  const [loadingBuyNow, setLoadingBuyNow] = useState(null);
  // const [, setLoadingBuyNow] = useState(null);

  const [data, setData] = useState({
    related_products: [],
  });

  const [loadingbtn, setLoadingBtn] = useState(false);

  const navigate = useNavigate();

  const [cartList, setCartList] = useState({
    product_id: "",
    qty: 1,
  });

  useEffect(() => {
    document.querySelector(".loaderBox").classList.remove("d-none");
    fetch(`${base_url}/products/${finalid}`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => {
        return response.json();
      })
      .then((data) => {
        document.querySelector(".loaderBox").classList.add("d-none");

        setData(data);
        const script = document.createElement("script");
        script.src = "https://api.feefo.com/api/javascript/apricot-power";
        script.async = true;
        document.body.appendChild(script);

        return () => {
          document.body.removeChild(script);
        };
      })
      .catch((error) => {
        document.querySelector(".loaderBox").classList.add("d-none");
        console.log(error);
      });
  }, [finalid]);

  const handleCart = (idData) => {
    setCartList({
      ...cartList,
      product_id: idData,
    });
  };

  // useEffect(() => {
  //   if (cartList?.product_id) {
  //     addToCart();
  //   }
  // }, [cartList]);
  const userInfo = useContext(IPInfoContext);
  //  const handleCart = (id) => {
  //   setCartList({
  //     ...cartList,
  //     product_id: id,
  //   });
  // };

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
  }, [data?.related_products]);

  useEffect(() => {
    trackKlaviyoEvent("View Product", {
      ProductName: data?.product_name,
      ProductID: data?.id,
      SKU: data?.item_code,
      Categories: data.categories,
      ImageURL: data.image,
      //  "URL": data.URL,
      //  "Brand": data.Brand,
      Price: data.price,
      //  "CompareAtPrice": data.CompareAtPrice
    });
  }, [data]);
  return (
    <>
      <Helmet>
        <script type="application/ld+json">
          {JSON.stringify(data?.seoObject)}
        </script>
        <title>{data.meta_title}</title>

        <>
          <meta name="description" content={data.meta_description} />
          <meta name="keywords" content={data.meta_keyword} />

          <meta property="og:title" content={data.meta_og_title} />
          <meta property="og:url" content={data.meta_og_url} />
          <meta property="og:image" content={data.meta_og_image} />
        </>
      </Helmet>
      <DefaultLayout>
        <InnerBanner boldText={data?.product_name} small />
        <section className="fuel-product-sec">
          <div className="container">
            <div className="row mb-3">
              <div className="col-12">
                <div className="row">
                  <div className="col-md-6 mb-4">
                    <div className="productImage">
                      <Image src={data?.image} />
                    </div>
                  </div>
                  <div className="col-md-6 mb-4">
                    <div className="productInfo">
                      <h3 className="text-capitalize">{data?.product_name}</h3>

                      <h5>
                        <span className="font-weight"></span>
                        {data?.product_descriptor}
                      </h5>
                      {data?.quantity_size && (
                        <h6>
                          <span className="font-weight-bold">
                            <b>Size: </b>
                          </span>
                          {`${data?.quantity_size}`}
                        </h6>
                      )}

                      <h4>
                        <span className="font-weight-bold">
                          Price:
                          {data?.map_price === data?.sell_price ? (
                            <span className="discount-price secondary-color">
                              ${data?.sell_price}
                            </span>
                          ) : (
                            <>
                              <span className="actual-price">
                                ${data?.map_price}
                              </span>{" "}
                              <span className="discount-price secondary-color">
                                ${data?.sell_price}
                              </span>
                            </>
                          )}
                        </span>
                      </h4>
                      {/* <div className="fuel-product-rating m-3">
                        <img src={rating} alt="Rating" />
                      </div> */}
                      {/* <div
                        className="feefo-product-stars-widget"
                        data-product-sku={data?.item_code}
                      ></div> */}

                      <div className="hot-buys-item-description mt-3">
                        <div
                          className="feefo-product-stars-widget"
                          data-product-sku={data?.item_code}
                        ></div>
                        <p>{data?.you_save}</p>
                        {data?.smart_saving?.length !== 0 &&
                          data?.smart_saving && (
                            <h6>
                              <b>Smart Savings! </b>
                              {data?.smart_saving?.map((e, index) => (
                                <div
                                  key={index}
                                  dangerouslySetInnerHTML={{
                                    __html: e?.message,
                                  }}
                                />
                              ))}
                            </h6>
                          )}

                        {/* <p
                          title={`You Must Have at least ${data?.min_quantity} items in your cart to receive this discounted base price `}
                        >
                          minimum purchase required (?)
                        </p> */}
                      </div>

                      {/* <p>{data?.meta_description}</p> */}
                      {/* <p><span className="font-weight-bold">Category:</span> <span>{data?.categories[0]?.category_title}</span></p> */}
                      <Col md={3} className="d-flex align-items-center gap-2">
                        <Button
                          variant="outline-secondary"
                          size="sm"
                          onClick={() =>
                            setCartList((prev) => ({
                              ...prev,
                              qty: prev.qty == 1 ? 1 : prev.qty - 1,
                            }))
                          }
                        >
                          -
                        </Button>
                        <span>{cartList?.qty}</span>
                        <Button
                          variant="outline-secondary"
                          size="sm"
                          onClick={() =>
                            setCartList((prev) => ({
                              ...prev,
                              qty: prev.qty + 1,
                            }))
                          }
                        >
                          +
                        </Button>
                      </Col>
                      <div className="d-flex align-items-center mt-3 cat-align">
                        <span className="me-1">
                          <b>Categories:</b>
                        </span>
                        {data?.categories?.map((cat, index) => {
                          const slug = `${
                            cat?.id
                          }-${cat?.category_title.replace(/\s+/g, "-")}`;
                          return (
                            <span key={index} className="me-1">
                              <a href={`/category/${slug}`} target="_blank">
                                {" "}
                                {cat?.category_title}
                              </a>
                              {index < data?.categories?.length - 1 && ","}
                            </span>
                          );
                        })}
                      </div>
                      <div className="d-flex align-items-center mt-3 cat-align">
                        <span className="me-1">
                          <b>Tags:</b>
                        </span>
                        {data?.tags?.map((cat, index) => {
                          const slug = `${cat?.id}-${cat?.name.replace(
                            /\s+/g,
                            "-"
                          )}`;
                          return (
                            <span key={index} className="me-1">
                              <a href={`/tag/${slug}`} target="_blank">
                                {" "}
                                {cat?.name}
                              </a>
                              {index < data.tags.length - 1 && ","}
                            </span>
                          );
                        })}
                      </div>
                      {data?.product_addons?.length > 0 && (
                        <div className="addons-section mt-4">
                          <h5 className="mb-3">
                            <b>Other Sizes</b>
                          </h5>
                          <div className="d-flex flex-wrap gap-3">
                            {data.product_addons?.map((addon, i) => (
                              <div
                                key={i}
                                title={addon.product_name}
                                className="addon-card text-center"
                                onClick={() => {
                                  const slug = `${addon?.product_url.replace(
                                    /\s+/g,
                                    "-"
                                  )}`;
                                  navigate(`/item/${slug}`, {
                                    state: { productID: addon?.id },
                                  });
                                }}
                              >
                                <img
                                  src={addon?.image}
                                  alt={addon?.product_name}
                                  className="addon-img"
                                />
                              </div>
                            ))}
                          </div>
                        </div>
                      )}
                      {loadingbtn ? (
                        <div className="justify-content-center align-items-center m-5">
                          <div
                            className="spinner-border text-warning"
                            role="status"
                          >
                            <span className="visually-hidden">Loading...</span>
                          </div>
                        </div>
                      ) : (
                        <div className="d-flex gap-3">
                          <button
                            className="button-with-icon mt-3"
                            onClick={() => {
                              // handleCart(finalid);
                              // addToCart(finalid, "buy");
                              addToCart(
                                finalid,
                                "buy",
                                cartList?.qty || 1,
                                data
                              );
                            }}
                          >
                            Buy Now
                            {/* <img src={buttonicon} alt="Button Icon" /> */}
                          </button>
                          <button
                            className="button-with-icon mt-3"
                            onClick={() => {
                              // handleCart(finalid);
                              // addToCart(finalid, "addtocard");

                              addToCart(
                                finalid,
                                "addtocard",
                                cartList?.qty || 1,
                                data
                              );
                            }}
                          >
                            Add to cart
                            {/* <img src={buttonicon} alt="Button Icon" /> */}
                          </button>
                        </div>
                      )}
                    </div>
                  </div>
                  <div className="col-md-6 col-12  d-flex justify-content-center">
                    {data?.product_badge?.map((e, i) => {
                      return (
                        <img
                          title={e?.description}
                          src={e?.badge_image}
                          key={i + 1}
                          alt={e?.badge_image + i + 1}
                          width={70}
                          height={70}
                        />
                      );
                    })}
                  </div>
                  {data?.sku && (
                    <h6 className="mt-4">
                      <span className="font-weight-bold">
                        <b>SKU: </b>
                      </span>
                      {`${data?.sku}`}
                    </h6>
                  )}
                  <div className="border  mobile-screen">
                    <Tabs
                      defaultActiveKey="Intro"
                      id="uncontrolled-tab-example"
                      className="mb-3 fs-4"
                    >
                      <Tab eventKey="Intro" title="Intro">
                        <div className="col-md-12 mb-4">
                          <div
                            className="responsive-html-content"
                            dangerouslySetInnerHTML={{
                              __html: data?.product_description,
                            }}
                          />
                        </div>
                      </Tab>

                      {data?.product_nutrition?.show_nutrition_label === 1 && (
                        <Tab
                          eventKey={
                            data?.product_nutrition?.nutrition_label_text
                          }
                          title={data?.product_nutrition?.nutrition_label_text}
                        >
                          {/* <h3>No reviews found</h3> */}

                          <PdfView data={data?.product_nutrition} />
                        </Tab>
                      )}
                      <Tab eventKey="Reviews" title="Reviews">
                        {/* <h3>No reviews found</h3> */}
                        <div
                          id="feefo-product-review-widgetId"
                          className="feefo-review-widget-product"
                          data-product-sku={data?.item_code}
                        ></div>
                      </Tab>
                    </Tabs>
                  </div>
                  <h2 className="RelatedProducts_title">
                    Customers who bought this product also bought:
                  </h2>
                  {data?.related_products.length > 0 &&
                    (data?.related_products || []).map((item, index) => (
                      <div
                        className="col-xl-4 col-lg-6 col-md-6 mb-5"
                        key={index}
                      >
                        <div className="fuel-product-item">
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
                                className="feefo-product-stars-widget "
                                data-product-sku={item?.id}
                              />
                            </div>
                            {/* <button
                                className="button-with-icon"
                                onClick={() => {
                                  handleCart(item?.id);
                                }}
                              >
                                Add To Cart{" "}
                                {item?.id === loadingbtn && (
                                  <Spinner size="sm" />
                                )}
                              </button> */}
                            {/* <div className="d-flex justify-content-evenly w-100">
                              <button
                                className="button-with-icon"
                                onClick={() => {
                                  // handleCart(item?.id);
                                  // addToCart(item?.id, "buy");
                                  addToCart(item?.id, "buy", 1);
                                }}
                              >
                                Buy Now{" "}
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
                              <button
                                className="button-with-icon"
                                onClick={() => {
                                  // handleCart(item?.id);
                                  // addToCart(item?.id, "addtocard");
                                  addToCart(item?.id, "addtocard", 1,item);
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
                            </div> */}
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
            </div>
          </div>
        </section>
      </DefaultLayout>
    </>
  );
};

export default ProductDetail;
