import React from "react";

import {
  aboutimg1,
  aboutimg2,
  aboutimg3,
  aboutimgbefore,
  aboutimgafter,
  buttonicon,
} from "../../assets/images";
import { useNavigate } from "react-router-dom";

const About = ({ data }) => {
  const navigate = useNavigate();
  const AboutData = data?.[1];
  return (
    <section className="main-about-sec overflow-x-hidden">
      <img src={aboutimgbefore} className="shape1" alt="About" />
      <img src={aboutimgafter} className="shape2" alt="About" />
      <div className="container-about">
        <div className="row align-items-center">
          <div className="col-lg-6">
            <div className="main-about-imgaes">
              <img
                src={AboutData?.image_1}
                className="main-about-img-1"
                alt="About Us"
              />
              <div className="main-about-img-2-div">
                <img
                  src={'https://staging.apricotpower.com:5013/uploads/products/item_595_1588947505.png'}
                  className="main-about-img-2"
                  alt="About"
                />
              </div>
              <div className="main-about-img-3-div">
                <img
                  src={AboutData?.image_3}
                  className="main-about-img-3"
                  alt="About"
                />
              </div>
            </div>
          </div>
          <div className="col-lg-6">
            <div className="sec-content">
              <h2 className="">
                {(() => {
                  const parts = AboutData?.title?.split(" ");
                  return (
                    <>
                      {parts?.[0]} <br />
                      <span className="primary-color">{parts?.[1]}</span>{" "}
                      <span className="secondary-color">{parts?.[2]}</span>
                    </>
                  );
                })()}
              </h2>

              <p className="col-12">
                <div
                  dangerouslySetInnerHTML={{ __html: AboutData?.description }}
                />
              </p>
              {/* <p>
                It was popularised in the 1960s with the release of Letraset
                sheets containing Lorem Ipsum passages, and more recently with
                desktop publishing software like Aldus PageMaker including
                versions of Lorem{" "}
              </p>
              <p>
                Lorem Ipsum is simply dummy text of the printing and typesetting
                industry. Lorem Ipsum has been the industry's standard dummy
                text ever since the 1500s, when an unknown printer took a galley
                of type and scrambled it to make a type{" "}
              </p> */}
              <div className="col-md-12 mb-3">
                <button
                  onClick={() => {
                    navigate(`${AboutData?.btn_link}`);
                  }}
                  className="button-with-icon"
                >
                  {AboutData?.btn_name}
                  {/* <img src={buttonicon} alt="Button Icon" /> */}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default About;
