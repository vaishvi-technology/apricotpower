import React, { useEffect } from "react";

import { Swiper, SwiperSlide } from "swiper/react";

// Import Swiper styles
import "swiper/css";
import "swiper/css/navigation";

import { Navigation } from "swiper/modules";
import { useNavigate } from "react-router-dom";



const Testimoonial = () => {
  useEffect(() => {
    const script = document.createElement("script");
    script.src = "https://api.feefo.com/api/javascript/apricot-power";
    script.async = true;
    document.body.appendChild(script);

    return () => {
      document.body.removeChild(script);
    };
  }, []);
  const navigate = useNavigate();
  return (
    <section className="testimoinal-sec">
      <div className="container">
        <div className="row">
          <div className="col-md-12">
            <div className="sec-content">
              <h2>
                Our Customers <span className="secondary-color">Love Us!</span>
              </h2>
            </div>
          </div>
          <div
            id="feefo-service-review-widgetId"
            style={{ height: "310px", overflow: "hidden" }}
            className="feefo-review-widget-service"
          ></div>
          <div className="justify-content-center d-flex mt-5">
            <button
              className="button-with-icon"
              onClick={() => {
                navigate("/reviews");
              }}
            >
              View All Reviews
            </button>
          </div>
          {/* <div className="col-md-12">
            <Swiper
              modules={[Navigation]}
              slidesPerView={2}
              spaceBetween={20}
              //   centeredSlides={true}
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
                  slidesPerView: 2,
                },
              }}
              className="mySwiper testimonial-swiper"
            >
              <div className="testimonial-swiper-buttons">
                <div className="swiper-button-next">
                  <img src={nexticon} alt="" />
                </div>
                <div className="swiper-button-prev">
                  <img src={previcon} alt="" />
                </div>
              </div>

              
              {testimoinalContent.map((item, index) => (
                <SwiperSlide key={index}>
                  <div className="testimoinal-content">
                    <img src={testimonialicon} alt="Testimonial Icon" />
                    <p>{item.description}</p>
                    <div className="rating">
                      <img src={ratingicon} alt="Rating" />
                    </div>
                  </div>
                </SwiperSlide>
              ))}
            </Swiper>
          </div> */}
        </div>
      </div>
      <script
        type="text/javascript"
        src="https://api.feefo.com/api/javascript/apricot-power"
        async
      ></script>
    </section>
  );
};

export default Testimoonial;
