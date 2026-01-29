/* eslint-disable react/jsx-key */
import { useContext, useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";

import { base_url } from "../../api";
import { useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import { useDispatch } from "react-redux";
import { GetProfileData } from "../../redux/slices/userSlice";
import { Image } from "react-bootstrap";
import { getTag } from "../../api/Services/getDynamicData";
import { Helmet } from "react-helmet-async";
import { IPInfoContext } from "ip-info-react";
import { CartContext } from "../../Context/CartContext";
import StoreSideBar from "./StoreSideBar";
import { BsFilterLeft } from "react-icons/bs";
import CustomButton from "../../components/CustomButton";
import { SelectBox } from "../../components/CustomSelect";
import { trackKlaviyoEvent } from "../../utils/klaviyo";

const FuelProducts = () => {
  const [sortOrder, setSortOrder] = useState("");
  const dispatch = useDispatch();
  const [tag, setTag] = useState([]);
  const [showOfCanvas, setShowOfCanvas] = useState(false);
  const [loadingBuyNow, setLoadingBuyNow] = useState(null);
  const [loadingbtn, setLoadingBtn] = useState(false);
  const navigate = useNavigate();
  const { fetchCount } = useContext(CartContext);
  const [products, setProducts] = useState([]);
  const token = localStorage.getItem("login");

  const fetchProducts = (sort = "") => {
    document.querySelector(".loaderBox")?.classList.remove("d-none");

    let url = `${base_url}/products/`;
    if (sort) {
      url += `?sortby=${sort}`;
    }

    fetch(url, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => response.json())
      .then(async (data) => {
        document.querySelector(".loaderBox")?.classList?.add("d-none");
        setProducts(data);
        await dispatch(GetProfileData());
      })
      .catch((error) => {
        console.log(error);
        document.querySelector(".loaderBox")?.classList?.add("d-none");
      });
  };

  useEffect(() => {
    fetchProducts(sortOrder);
  }, [sortOrder]);

  const userInfo = useContext(IPInfoContext);

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
  const fetchTag = async () => {
    try {
      const data = await getTag(2);
      setTag(data?.[0]);
    } catch (error) {
      console.log({ error });
    }
  };
  useEffect(() => {
    fetchTag();
  }, []);
  const handleCloseOfCanvas = () => setShowOfCanvas(false);
  const handleShowOfCanvas = () => setShowOfCanvas(true);
  const sortbyoption = [
    { id: "highest_price", name: "Highest Price" },
    { id: "lowest_price", name: "Lowest Price" },
    { id: "best_sales", name: "Best sellers" },
    // { id: "lowest_sales", name: "Lowest Sales" },
  ];

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
        <InnerBanner boldText="Products" />

        <section className="fuel-product-sec">
          <StoreSideBar
            handleCloseOfCanvas={handleCloseOfCanvas}
            handleShowOfCanvas={handleShowOfCanvas}
            showOfCanvas={showOfCanvas}
            products={products}
          />

          <div className="container ">
            <div className="row align-items-center">
              <div className="col-6 col-md-2 mb-2 mb-md-0">
                <CustomButton
                  text={<BsFilterLeft size={24} />}
                  variant="primaryButton"
                  onClick={handleShowOfCanvas}
                  className="w-100"
                />
              </div>

              <div className="col-12 col-md-4 offset-md-6">
                <SelectBox
                  selectClass="mainInput w-100"
                  name="sort"
                  label="Sort By"
                  option={sortbyoption}
                  value={sortOrder}
                  onChange={(e) => setSortOrder(e.target.value)}
                />
              </div>
            </div>
          </div>

          {/* <div style={{ position: "fixed", left: "20px" }}>
            <CustomButton
              text={<BsFilterLeft size={30} />}
              variant={"primaryButton"}
              onClick={() => handleShowOfCanvas()}
            />
          </div> */}
          <div className="container">
            {products?.length > 0
              ? (products || [])?.map((item, idx) => (
                  <div
                    className="row justify-content-center"
                    id={`category-${idx}`}
                    key={idx}
                  >
                    <h1
                      className="product-Category-title secondary-color text-store text-center p-3 mt-5"
                      style={{
                        fontFamily:
                          "56px/1.25 ArsenalBold, Arial, Helvetica Neue, Helvetica, sans-serif",
                      }}
                    >
                      {item?.category_title || ""}
                    </h1>
                    {item?.products?.length > 0 &&
                      (item?.products || []).map((item, index) => (
                        <div
                          className="col-xl-4 col-lg-6 col-md-6 resposive-store "
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
                              {/* <div className="fuel-product-rating">
                              <div
                                className="feefo-product-stars-widget "
                                data-product-sku={item?.item_code}
                              />
                            </div> */}
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
                                <p style={{ height: "20px" }}>
                                  {item?.you_save}
                                </p>
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
                                  data-product-sku={item?.item_code}
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
                ))
              : ""}
          </div>
        </section>
      </DefaultLayout>
    </>
  );
};

export default FuelProducts;
