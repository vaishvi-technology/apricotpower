/* eslint-disable react/prop-types */
import React from "react";
import { buttonicon } from "../../assets/images";
import { useNavigate } from "react-router-dom";

const FreeShipping = ({ data }) => {
  const navigate = useNavigate();
  const FreeShippingData = data?.[2];
  return (
    <section className="free-shipping-sec">
      <div>
        <div className="com-md-12">
          <div
            className="free-shipping-content"
            style={{ backgroundImage: `url(${FreeShippingData?.image_1})` }}
          >
            <div className="free-shipping-content-innerDiv">
              <h2>
                {(() => {
                  const parts = FreeShippingData?.title?.split(" ");
                  return (
                    <>
                      {parts?.[0]} {parts?.[1]} {parts?.[2]} {""}
                      <span
                        className="primary-color"
                        style={{ fontWeight: 900 }}
                      >
                        {parts?.[3]}
                      </span>
                    </>
                  );
                })()}
              </h2>

              {/* <p> */}
                <div
                  dangerouslySetInnerHTML={{
                    __html: FreeShippingData?.description,
                  }}
                />
              {/* </p> */}
              <button
                onClick={() => {
                  navigate(`${FreeShippingData?.btn_link}`);
                }}
                className="button-with-icon mt-2"
              >
                {FreeShippingData?.btn_name}
                {/* <img src={buttonicon} alt="Button Icon" /> */}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default FreeShipping;
