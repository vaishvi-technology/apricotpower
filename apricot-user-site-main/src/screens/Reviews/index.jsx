import React, { useEffect } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";


import { Swiper, SwiperSlide } from "swiper/react";

// Import Swiper styles
import "swiper/css";
import "swiper/css/navigation";
import { Navigation } from "swiper/modules";

const Reviews = () => {
   useEffect(() => {
      const script = document.createElement("script");
      script.src = "https://api.feefo.com/api/javascript/apricot-power";
      script.async = true;
      document.body.appendChild(script);
  
      return () => {
        document.body.removeChild(script);
      };
    }, []);
  const slides = [
    {
      id: 1,
      text: "Order received in reasonable time and in good condition Easy ordered and delivered",
      name: "Cynthia Crystal",
      date: "November 17, 2024",
    },
    {
      id: 2,
      text: "Ordered items were delivered.",
      name: "Malgorzata Zapolska",
      date: "November 14, 2024",
    },
    {
      id: 3,
      text: "Beautiful packaging, I received tea with amygdalin as a gift. I bought it for prevention, in view of the genetic predisposition in the family.",
      name: "EKATERINA",
      date: "24 days ago",
    },
    {
      id: 4,
      text: "Always have been the best product and service over the years. Fast shipping great product",
      name: "Brian Bryson",
      date: "November 7, 2024",
    },
    {
      id: 5,
      text: "The gal who helped me on the phone was terrific, very helpful and made suggestions for me.",
      name: "November 5, 2024",
      date: "Trusted Customer",
    },
    {
      id: 6,
      text: "Easy to order online and fast shipping!!",
      name: "Trusted Customer",
      date: "November 7, 2024",
    },
  ];
 useEffect(() => {
    document.title = "Reviews | Apricot Power";
  }, []);
  return (
    <DefaultLayout>
      <InnerBanner boldText="REVIEWS" />

      <div className="reviews-page ">
      <div id="feefo-service-review-widgetId" className="feefo-review-widget-service"></div>

        {/* <section className="service-award-sec">
          <div className="container">
            <div className="row">
              <div className="col-md-12">
                <div className="service-award-content">
                  <div className="service-award-content-left">
                    <img
                      src={platinumserviceaward}
                      alt="platinum service award"
                    />
                  </div>
                  <div className="service-award-right">
                    <p>
                      Independent Service Rating based on 1998 verified reviews
                    </p>
                    <img
                      src={averagecustomerrating}
                      alt="average customer rating"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section className="reviews-testimonial">
          <div className="container">
            <div className="row">
              <div className="col-md-12">
                <div className="reviews-testimonial-content">
                  <Swiper
                    modules={[Navigation]}
                    spaceBetween={20}
                    centeredSlides={true}
                    loop={true}
                    navigation={{
                      nextEl: ".swiper-button-next",
                      prevEl: ".swiper-button-prev",
                    }}
                    breakpoints={{
                      320: {
                        slidesPerView: 1,
                      },
                      992: {
                        slidesPerView: 2,
                        centeredSlides:false
                      },
                      1200: {
                        slidesPerView: 3,
                      },
                    }}
                    className="mySwiper"
                  >
                    <div className="our-products-swiper-buttons">
                      <div className="swiper-button-next">
                        <img src={nexticon} alt="" />
                      </div>
                      <div className="swiper-button-prev">
                        <img src={previcon} alt="" />
                      </div>
                    </div>
                    {slides.map((slide) => (
                      <SwiperSlide key={slide.id}>
                        <div className="reviews-testimonial-item">
                          <img
                            src={reviewicon}
                            className="testimonial-icon"
                            alt="Testimonial Icon"
                          />
                          <p className="reviews-testimonial-description">
                            {slide.text}
                          </p>
                          <img src={rating} className="" alt="" />
                          <div className="reviews-testimonial-user-info">
                            <p>{slide.name}</p>
                            <span>-</span>
                            <p>{slide.date}</p>
                          </div>
                        </div>
                      </SwiperSlide>
                    ))}
                  </Swiper>
                </div>
              </div>
            </div>
          </div>
        </section> */}
      </div>
    </DefaultLayout>
  );
};

export default Reviews;
