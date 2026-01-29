/* eslint-disable no-irregular-whitespace */
/* eslint-disable react/prop-types */
// import React from "react";

import {
  // bannerimg,
  bannerlogo,
  bannerplayicon,
  // buttonicon,
} from "../../assets/images";

import { Navigation, Pagination, Autoplay } from "swiper/modules";
import { useNavigate } from "react-router-dom";
import { Swiper, SwiperSlide } from "swiper/react";
import { nexticon, previcon } from "../../assets/images";
import B17 from "../../assets/images/b-17.png";
import ApricotSeeds from "../../assets/images/apricot-seeds.png";
import OrganicSeeds from "../../assets/images/organic-seeds.png";
import California from "../../assets/images/california.png";
import Big3 from "../../assets/images/big-3-copy.png";
import mushroom from "../../assets/images/mushroom.png";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
const Banner = ({ data }) => {
  const navigate = useNavigate();
  const images = [
    {
      src: B17,
      link: "/item/595-apricot-power-b17-amygdalin-500mg-capsules",
    },
    {
      src: ApricotSeeds,
      link: "/item/705-apricot-seed-capsules",
    },
    {
      src: OrganicSeeds,
      link: "/item/376-california-bitter-raw-apricot-seeds-32-oz",
    },
    {
      src: California,
      link: "/item/991-apfuel-california-special",
    },
    {
      src: Big3,
      link: "/item/730-big-3-b17-pack-500mg",
    },
    {
      src: mushroom,
      link: "/item/965-ap-fuel---mushroom-coffee-mix",
    },
  ];
  return (
    <section className="main-banner">
      <div className="main-banner-innerBG">
        <div className="container">
          <div className="row ">
            <div className="col-xl-6 banner-left-content ">
              <div className="main_banner-content">
                {/* <h1 className="mt-3"> */}
                <h1 className="mt-3 ">
                  {(() => {
                    const parts = data?.[0]?.title?.split(" ");
                    return (
                      <>
                        {parts?.[0]} {parts?.[1]} <br />
                        <span className="primary-color">{parts?.[2]}</span>{" "}
                        <span className="secondary-color">{parts?.[3]}</span>
                      </>
                    );
                  })()}
                </h1>

                {/* Welcome To <br />
                  <span className="primary-color">Apricot</span>
                  <span className="secondary-color">Power</span> */}
                {/* </h1> */}
                <div
                  dangerouslySetInnerHTML={{
                    __html: data?.[0]?.description,
                  }}
                />
                {/* <p className="text-white ">
                  Apricot Power is your reliable source for quality apricot
                  seeds and B17 products.
                </p> */}
                <button
                  onClick={() => {
                    navigate(`${data?.[0]?.btn_link}`);
                  }}
                  className="button-with-icon button-with-icon-primary button-with-icon-lg "
                >
                  {data?.[0]?.btn_name}
                  {/* <img src={buttonicon} alt="Button Icom" /> */}
                </button>
                <h3 className="slider-title">Apricot</h3>
              </div>
            </div>
            <div
              className="col-xl-6 banner-right-image"
              

            >
              <div className="main-banner-image-logo">
                <img src={bannerlogo} alt="Banner Logo" />
              </div>
              <div className="main-banner-image">
                <Swiper
                  modules={[Navigation, Pagination, Autoplay]}
                  // navigation
                  // pagination={{ clickable: true }}
                  // autoplay={{ delay: 3000 }}
                  loop
                  className="swiper-container"
                  navigation={{
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                  }}
                >
                  <div className="our-products-swiper-buttons">
                    <div className="swiper-button-next banner-next">
                      <img src={nexticon} alt="" />
                    </div>
                    <div className="swiper-button-prev banner-prev">
                      <img src={previcon} alt="" />
                    </div>
                  </div>
                  {images.map((src, index) => (
                    <SwiperSlide key={index}>
                      <div
                        className="banner-parent "
                        onClick={() => {
                          navigate(src.link);
                        }}
                      >
                        <img
                          src={src.src}
                          className={
                            src.src === "/src/assets/images/banner-img.png"
                              ? "banner-img-mashroom cursor-pointer"
                              : "banner-img cursor-pointer"
                          }
                          alt="Banner Img"
                        />
                      </div>
                    </SwiperSlide>
                  ))}
                </Swiper>
                {/* <div
                  onClick={() => {
                    navigate(
                      "/item/988-apfuel-mushroom-coffee---individual-serving-size"
                    );
                  }}
                >
                  <img
                    src={data?.[0]?.image_1}
                    className="banner-img"
                    alt="Banner Img"
                  />
                </div> */}

                {/* <button className="main-banner-image-btn">
                  <img
                    src={bannerplayicon}
                    alt="Banner Play Icon"
                    className="banner-play-icon"
                    onClick={() => {
                      const slug = `965-AP:-Fuel---Mushroom-Coffee-mix`;
                      navigate(`/item/${slug}`, {
                        state: { productID: "965" },
                      });
                    }}
                  />
                </button> */}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Banner;
