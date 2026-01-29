import React, { useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { contactfeefoimage, referfriendverifyimg } from "../../assets/images";
import WeShipFast from "../../components/WeShipFast";
import { useNavigate } from "react-router-dom";
import { recipes } from "./data";
import ReviewModal from "../../components/ReviewModal";

const SeedsRecipes = () => {
  const [show, setshow] = useState();
  const navigate = useNavigate();
  const data = {
    description: `  <h2>We Ship Fast | Same Day Shipping¹ | Located in the US</h2>
                        <h3>100% Money Back Guaranteed²</h3>
                        <h4>Contact us:</h4>
                        <div className="phone-email">
                            <p><span>Phone:</span> <a href="tel:866-468-7487">866-468-7487</a></p>
                            <p><span>Email:</span> <a href="mailto:CustomerService@ApricotPower.com">CustomerService@ApricotPower.com</a></p>
                        </div>
                        <div className="swe-ship-fast-moreDetail">
                            <p>¹ Shipping cutoff 2PM CST. Open Monday - Friday.</p>
                            <p>² Please see return policy for further details.</p>
                        </div>`,
  };

  const [activeTab, setActiveTab] = useState(recipes[0].name);

  return (
    <DefaultLayout>
      <InnerBanner lightText="Apricot" boldText="Seed Recipes" />
      <section className="seed-recipes-sec">
        <div className="container">
          <div className="row">
            <div className="col-md-12">
              <h2 className="seed-recipes-sec-title">
                <span className="primary-color">
                  Find a wide variety of delicious and easy to make apricot seed
                  recipes, cooking tips,
                </span>{" "}
                <span className="secondary-color">
                  and more for every meal.
                </span>
              </h2>
            </div>
          </div>

          <div className="seed-recipes-content-row">
            <div className="row ">
              <div className="col-xxl-3 col-xl-4 col-lg-5 seed-recipes-sidebar-col">
                <div className="seed-recipes-sidebar">
                  <div className="seed-recipes-sidebar-list">
                    {recipes.map((recipe, index) => (
                      <a
                        key={index}
                        className={`seed-recipes-sidebar-list-item ${
                          activeTab === recipe.name
                            ? "seed-recipes-sidebar-list-itemactive"
                            : ""
                        }`}
                        href="javascript:;"
                        onClick={() => setActiveTab(recipe.name)}
                      >
                        {recipe.name}
                      </a>
                    ))}
                  </div>
                </div>
              </div>
              <div className="col-xxl-9 col-xl-8 col-lg-7 resposive-seeds">
                <div className="seed-recipes-content">
                  <h3 className="secondary-color">{activeTab}</h3>

                  <div
                    className="recipe-content"
                    dangerouslySetInnerHTML={{
                      __html:
                        recipes.find((r) => r.name === activeTab)?.content ||
                        "",
                    }}
                  ></div>
                  <div className="seed-recipes-content">
                    <div
                      className="recipe-content"
                      dangerouslySetInnerHTML={{
                        __html:
                          recipes.find((r) => r.name === activeTab)
                            ?.description || "",
                      }}
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div className="row">
            <div className="col-md-12">
              <div className="seed-recipes-content-footer">
                <a>
                  <h2
                    onClick={() => navigate("/wholesale-application")}
                    className="cursor"
                  >
                    JOIN THE MOVEMENT! <br /> BECOME A RETAILER TODAY
                  </h2>
                </a>
                <img
                  src={
                    "https://api.feefo.com/api/logo?merchantidentifier=apricot-power&template=Service-Stars-White-316x80.png&since=all"
                  }
                  alt="Feefo"
                  className="cursor"
                  onClick={() => setshow(true)}
                />
                {/* <img src={contactfeefoimage} alt="verify image" /> */}
                <img src={referfriendverifyimg} alt="verify image" />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* <WeShipFast data={data} /> */}
      <ReviewModal show={show} setshow={setshow} />
    </DefaultLayout>
  );
};

export default SeedsRecipes;
