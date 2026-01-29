/* eslint-disable react/jsx-key */
/* eslint-disable no-irregular-whitespace */
import React, { useEffect, useState } from "react";
import {
  buttonicon,
  emailicon,
  footerlogo,
  mapicon,
  phoneicon,
  facebookicon,
  twittericon,
  instagramicon,
  pinteresticon,
  referfriendverifyimg,
  authorize,
  paypal,
} from "../../../assets/images";
import { Link, useNavigate } from "react-router-dom";
import { base_url } from "../../../api";
import { toast } from "react-toastify";
import SuccesMessage from "../../SuccesMessage/SuccesMessage";
import { FooterCategorySkeleton } from "../../FooterCategorySkeleton/FooterCategorySkeleton";

const Footer = () => {
  const [categories, setCategories] = useState([]);
  const [isSuccess, setIsSuccess] = useState(false);
  const [isLoading, setisLoading] = useState(false);
  const token = localStorage.getItem("login");
  const fetchCategories = () => {
    setisLoading(true);
    fetch(`${base_url}/categories/`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then(async (data) => {
        setisLoading(false);
        setCategories(data);
      })
      .catch((error) => {
        setisLoading(false);
      });
  };
  useEffect(() => {
    fetchCategories();
  }, []);
  const [formData, setFormData] = useState({
    first_name: "",
    last_name: "",
    email: "",
  });

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    console.log(formData);
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");

      const res = await fetch(`${base_url}/news-latter`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });
      document.querySelector(".loaderBox").classList.add("d-none");
      const data = await res.json();

      if (data.success == true) {
        setFormData({
          first_name: "",
          last_name: "",
          email: "",
        });
        setIsSuccess(true);

        // // toast.success(data?.message);;
        // fetchaddress();
      } else {
        toast.error(data?.message);
      }
    } catch (err) {
      document.querySelector(".loaderBox").classList.add("d-none");

      console.log("Order failed:", err);
    }
  };
  const navigate = useNavigate();
  return (
    <>
      <section className="main-footer">
        <div className="container">
          <div className="row main-footer-row">
            <div className="col-xl-3 col-lg-4">
              <div className="footer-about">
                <div className="footer-logo" onClick={() => navigate("/")}>
                  <img src={footerlogo} alt="Footer Logo" />
                </div>
                <p className="text-white">
                  Apricot Power has been providing apricot seeds since 1999.{" "}
                </p>
                <div className="footer-information">
                  <div className="text-with-icon">
                    <img
                      className="text-with-icon-img"
                      src={mapicon}
                      alt="Map Icon"
                    />
                    <p className="text-with-icon-text">
                      Address: 13501 Ranch Road 12,
                      <br /> Ste 103 Wimberley, Tx. 78676
                    </p>
                  </div>

                  <div className="text-with-icon">
                    <img
                      className="text-with-icon-img"
                      src={phoneicon}
                      alt="Phone Icon"
                    />
                    <p className="text-with-icon-text">
                      <a href="tel:1-866-468-7487">
                        Toll Free: 1-866-468-7487 (866-GOT-PITS)
                      </a>
                      <br />
                      {/* <a href="tel: 1-707-262-1394"> 1-707-262-1394</a> */}
                    </p>
                  </div>
                  <div className="text-with-icon">
                    <img
                      className="text-with-icon-img"
                      src={phoneicon}
                      alt="Phone Icon"
                    />
                    <p className="text-with-icon-text">
                      <a href="tel: 1-707-262-1394"> Local: 1-707-262-1394</a>
                    </p>
                  </div>
                  <div className="text-with-icon">
                    <img
                      className="text-with-icon-img"
                      src={emailicon}
                      alt="Email Icon"
                    />
                    <p className="text-with-icon-text">
                      <a href="mailto:customerservice@apricotpower.com">
                        customerservice@apricotpower.com
                      </a>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div className="col-xl-6 col-lg-4">
              <div className="footer-main-links">
                <h3>Apricot Power's Product Line</h3>
                <ul className="footer-main-links-list">
                  {isLoading ? (
                    <FooterCategorySkeleton />
                  ) : (
                    (categories || []).map((item, index) => {
                      const slug = `${item?.id}-${item?.category_title.replace(
                        /\s+/g,
                        "-"
                      )}`;
                      return (
                        <li className="footer-main-links-item" key={index}>
                          <Link
                            to={`/category/${slug}`}
                            state={{ categoryID: item?.id }}
                          >
                            {item?.category_title}
                          </Link>
                        </li>
                      );
                    })
                  )}
                </ul>
              </div>
            </div>
            <div className="col-xl-3 col-lg-4">
              <div className="footer-newsletter">
                <h3
                  className="subscribe-header-top cursor"
                  onClick={() => navigate("/wholesale-application")}
                >
                  JOIN THE MOVEMENT!
                  <br /> BECOME A RETAILER TODAY
                </h3>
                <h2>Subscribe to our Email List</h2>
                <p className="text-white">
                  Sign up for exclusive offers, original stories, events and
                  more.
                </p>
                <form
                  onSubmit={handleSubmit}
                  className="row footer-newsletter-form"
                >
                  <div className="col-md-6 mb-2">
                    <input
                      type="text"
                      name="first_name"
                      className="form-control"
                      placeholder="First Name"
                      value={formData.first_name}
                      onChange={handleChange}
                      required
                    />
                  </div>
                  <div className="col-md-6 mb-2">
                    <input
                      type="text"
                      name="last_name"
                      className="form-control"
                      placeholder="Last Name"
                      value={formData.last_name}
                      onChange={handleChange}
                      required
                    />
                  </div>
                  <div className="col-md-12 mb-2">
                    <input
                      type="email"
                      name="email"
                      className="form-control"
                      placeholder="Email"
                      value={formData.email}
                      onChange={handleChange}
                      required
                    />
                  </div>
                  <div className="col-md-12 mt-2 mb-2">
                    {isSuccess && (
                      <SuccesMessage
                        title="Subscription Successful!"
                        subTitle="You’ve been added to our email list. Stay tuned for updates."
                        setIsSuccess={setIsSuccess}
                      />
                    )}

                    <button
                      type="submit"
                      className={`button-with-icon  ${isSuccess && "mt-2"}`}
                    >
                      Subscribe
                      {/* <img src={buttonicon} alt="Button Icon" /> */}
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <div className="col-md-12">
              <div className="footer-secondary-links">
                <ul className="footer-secondary-links-list">
                  <li className="footer-main-links-item new-class">
                    <Link to="/special-product">Monthly Specials </Link>
                  </li>
                  <li className="footer-main-links-item new-class">
                    <Link to="/contact-us">Contact us</Link>
                  </li>
                  <li className="footer-main-links-item new-class">
                    <Link to="/faq">Apricot Seed Info</Link>
                  </li>
                  <li className="footer-main-links-item new-class">
                    <Link to="/return_policy">Return Policy</Link>
                  </li>
                  <li className="footer-main-links-item new-class">
                    <Link to="/privacy">Privacy Policy</Link>
                  </li>
                  <li className="footer-main-links-item new-class">
                    <Link to="/shipping_privacy">Shipping Policy</Link>
                  </li>
                </ul>
              </div>
            </div>

            <div className="col-md-12">
              <div className="footer-para-text-div">
                <p>
                  Information and statements regarding dietary supplements have
                  not been evaluated by the Food and Drug Administration and are
                  not intended to diagnose, treat, cure or prevent any disease
                  or health condition. Content on this website is for reference
                  purposes only and is not intended to substitute for advice
                  given by a physician, pharmacist or other licensed healthcare
                  professional. You should not use this information as
                  self-diagnosis or for treating a health problem or disease.
                  Contact your health-care provider immediately if you suspect
                  that you have a medical problem.
                </p>
                <p>
                  Section 10786, Title 17, California Admin. Code: Warning
                  apricot kernels may be toxic; very low quantities may cause
                  reaction.
                </p>
                <p>
                  WARNING: Consuming certain dietary supplements and/or other
                  products offered for sale on this website may expose you to
                  chemicals including lead, which is known to the State of
                  California to cause cancer and birth defects or other
                  reproductive harm. For more information go
                  to www.P65Warnings.ca.gov/Food.
                </p>
                <p>
                  The Apricot Power Brand works hard to ensure its strict
                  quality standards are upheld when products reach consumers. As
                  such, to ensure consumers receive the highest quality,
                  authentic Apricot Power products when shopping on e-commerce
                  platforms (including, but not limited to Amazon.com, eBay.com,
                  and Walmart.com), Apricot Power will only honor its
                  guarantee/warranty with valid proof of purchase from
                  authorized and verified e-commerce sellers.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section className="footer-copyright">
        <div className="container">
          {/* <div className="row">
        </div> */}
          <div className="copyrightDiv">
            <div className="  d-flex p-3 gap-2 social-image">
              <img
                src={
                  "https://www.apricotpower.com/global/assets/img/blocks/partners/partner1.webp"
                }
                alt="partner"
                className="b-partners__img"
                style={{ width: "42px", height: "42px" }}
              />
              <img
                src={
                  "https://www.apricotpower.com/global/assets/img/blocks/partners/partner2.webp"
                }
                alt="partner"
                className="b-partners__img"
                style={{ width: "52px", height: "42px" }}
              />

              <img
                src={
                  "https://www.apricotpower.com/global/assets/img/blocks/partners/partner3.webp"
                }
                style={{ width: "193px", height: "37px" }}
                alt="Refer Friend"
              />
            </div>
            <div className="copyright-center">
              <p>&copy; 2025 apricot power, inc. all rights reserved.</p>
            </div>
            <div className="copyright-right">
              <div className="copyright-right-text">
                apricot power in social:
              </div>
              <div className="copyright-right-icons">
                <a
                  href="https://www.facebook.com/ApricotPowerB17"
                  target="_blank"
                  // style={{width:"24px",height:"24px"}}
                >
                  <img
                    src={facebookicon}
                    style={{ width: "24px", height: "24px" }}
                    alt="facebook icon"
                  />
                </a>
                {/* <a href="https://x.com/ApricotPowerB17" target="_blank">
                  <img
                    src={twittericon}
                    alt="twitter icon"
                    style={{ width: "29px", height: "24px" }}
                  />
                </a> */}
                <a
                  href="https://www.instagram.com/accounts/login/?next=https%3A%2F%2Fwww.instagram.com%2Fapricotpower%2F&is_from_rle"
                  target="_blank"
                >
                  <img
                    src={instagramicon}
                    alt="instagram icon"
                    style={{ width: "24px", height: "24px" }}
                  />
                </a>
                {/* <a
                  href="https://www.pinterest.com/apricotpower/"
                  target="_blank"
                >
                  <img
                    src={pinteresticon}
                    alt="pinterest icon"
                    style={{ width: "24px", height: "24px" }}
                  />
                </a> */}
              </div>
            </div>
          </div>
        </div>
      </section>
    </>
  );
};

export default Footer;
