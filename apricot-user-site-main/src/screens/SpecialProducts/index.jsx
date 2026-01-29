import React, { useContext, useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";

import { rating } from "../../assets/images";
import { base_url } from "../../api";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import { useDispatch } from "react-redux";
import { GetProfileData } from "../../redux/slices/userSlice";
import { Image, Spinner } from "react-bootstrap";
import { IPInfoContext } from "ip-info-react";

const SpecialProducts = () => {
  const dispatch = useDispatch();

  const [loadingbtn, setLoadingBtn] = useState(false);
  const navigate = useNavigate();

  const [products, setProducts] = useState("");
  const [cartList, setCartList] = useState({
    product_id: "",
    qty: 1,
  });
  const token = localStorage.getItem("login");

  const fetchProducts = () => {
    document.querySelector(".loaderBox").classList.remove("d-none");
    fetch(`${base_url}/products/?filter=hot-products`, {
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
        document.querySelector(".loaderBox").classList.add("d-none");
      });
  };

  useEffect(() => {
    fetchProducts();
  }, []);
  const userInfo = useContext(IPInfoContext);
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

  useEffect(() => {
    const script = document.createElement("script");
    console.log(script, "script");
    script.src = "https://api.feefo.com/api/javascript/apricot-power";
    script.async = true;
    document.body.appendChild(script);

    return () => {
      document.body.removeChild(script);
    };
  }, [products]);
  useEffect(() => {
    document.title = "Special Product | Apricot Power";
  }, []);

  return (
    <DefaultLayout>
      <InnerBanner boldText={"Special Products"} />
      <section className="fuel-product-sec">
        <div className="container">
          <div className="row justify-content-center">
            {products?.length > 0 &&
              (products || []).map((item, index) => (
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
                        <p>{item?.you_save}</p>
                        <p
                          title={`You Must Have at least ${item?.min_quantity} items in your cart to receive this discounted base price `}
                        >
                          minimum purchase required (?)
                        </p>
                      </div>
                      <div className="fuel-product-rating" style={{height: "30px"}}>
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
        </div>
      </section>
    </DefaultLayout>
  );
};

export default SpecialProducts;
