import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import WeShipFast from "../../components/WeShipFast";

import { retailerlocationsideimg } from "../../assets/images";
import { useNavigate } from "react-router-dom";

import { Helmet } from "react-helmet-async";
import { useState } from "react";
import ReviewModal from "../../components/ReviewModal";

const Recipes = () => {
  const [show, setshow] = useState();
  const navigate = useNavigate();
  return (
    <>
      <Helmet>
        <title>{"Recipes"}</title>
      </Helmet>
      <DefaultLayout>
        <InnerBanner lightText="Retailer" boldText="Locations" />

        <section className="retailer-locations">
          <div className="container">
            <div className="row">
              <div className="col-xxl-9 col-xl-8 col-lg-7">
                <div className="retailer-locations-content">
                  <div className="retailer-locations-content-head">
                    <p></p>
                  </div>

                  <div className="attention-retailers-form">
                    <div className="attention-retailers-form-head">
                      <h2>
                        <span className="primary-color">Attention</span>{" "}
                        <span className="secondary-color">Retailers</span>
                      </h2>
                      <p>
                        If you are a retail store and would like information
                        about carrying our products call us at{" "}
                        <a href="tel:1-866-468-7487">1-866-468-7487</a>.
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div className="col-xxl-3 col-xl-4 col-lg-5">
                <div className="attention-retailers-aside">
                  <div className="attention-retailers-aside-head">
                    <h2
                      onClick={() => navigate("/wholesale-application")}
                      className="cursor"
                    >
                      JOIN THE MOVEMENT! <br /> BECOME A RETAILER TODAY
                    </h2>
                    <img
                      src={
                        "https://api.feefo.com/api/logo?merchantidentifier=apricot-power&template=Service-Stars-White-316x80.png&since=all"
                      }
                      alt="Feefo"
                      className="cursor"
                      onClick={() => setshow(true)}
                    />
                  </div>
                  <div className="  gap-2 p-3 border d-flex justify-content-center">
                    <img
                      src={
                        "https://www.apricotpower.com/global/assets/img/blocks/partners/partner1.webp"
                      }
                      alt="partner"
                      className=""
                    />

                    <img
                      src={
                        "https://www.apricotpower.com/global/assets/img/blocks/partners/partner2.webp"
                      }
                      alt="partner"
                      className=""
                    />
                    <img
                      src={
                        "https://www.apricotpower.com/global/assets/img/blocks/partners/partner3.webp"
                      }
                      alt="Refer Friend"
                    />
                  </div>

                  <div className="attention-retailers-aside-img">
                    <img
                      src={retailerlocationsideimg}
                      alt="Retailer Location Side"
                      className="img-fluid"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div className="col-md-12 mt-5">
              <div className="row">
                <div className="col-lg-9">
                  <div className="store-form-head">
                    <h2>
                      Is there a retail location that you wish carried Apricot
                      Power&lsquo;s fine line of products? Let us know about
                      it...
                    </h2>
                  </div>
                </div>
                <div className="col-md-12">
                  <div className="store-form">
                    <input
                      type="text"
                      className="form-control"
                      placeholder="Store Name"
                    />
                    <input
                      type="text"
                      className="form-control"
                      placeholder="Location & Contact Information"
                    />
                    <button className="button-with-icon">Submit</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* <WeShipFast
          data={`  <h2>We Ship Fast | Same Day Shipping¹ | Located in the US</h2>
                        <h3>100% Money Back Guaranteed²</h3>
                        <h4>Contact us:</h4>
                        <div className="phone-email">
                            <p><span>Phone:</span> <a href="tel:866-468-7487">866-468-7487</a></p>
                            <p><span>Email:</span> <a href="mailto:CustomerService@ApricotPower.com">CustomerService@ApricotPower.com</a></p>
                        </div>
                        <div className="swe-ship-fast-moreDetail">
                            <p>¹ Shipping cutoff 2PM CST. Open Monday - Friday.</p>
                            <p>² Please see return policy for further details.</p>
                        </div>`}
        /> */}
        <ReviewModal show={show} setshow={setshow} />
      </DefaultLayout>
    </>
  );
};

export default Recipes;
