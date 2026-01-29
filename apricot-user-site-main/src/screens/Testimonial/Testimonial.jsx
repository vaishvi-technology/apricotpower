import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";
import "swiper/css/navigation";
import { Navigation } from "swiper/modules";
import { nexticon, previcon } from "../../assets/images";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import TestimonialModal from "./TestimonialModal";
import { useEffect, useState } from "react";
import { getTestimonial } from "../../api/Services/testimonialServices";
import { FaVideo } from "react-icons/fa";
import { AiFillAudio } from "react-icons/ai";
import { Rating } from "react-simple-star-rating";

export default function Testimonial() {
  const [show, setShow] = useState(null);
  const [previewdata, setPreviewdata] = useState(null);
  const [data, setData] = useState([]);

  const fetchTestimonial = async () => {
    try {
      const res = await getTestimonial();
      setData(res);
    } catch (error) {
      console.log({ error });
    }
  };

  useEffect(() => {
    fetchTestimonial();
  }, []);

  return (
    <DefaultLayout>
      <InnerBanner boldText="Testimonials" />
      <section className="testimoinal-sec position-relative">
        {data?.length !== 0 && (
          <div className="our-products-swiper-buttons">
            <div className="swiper-button-next">
              <img src={nexticon} alt="Next" />
            </div>
            <div className="swiper-button-prev">
              <img src={previcon} alt="Previous" />
            </div>
          </div>
        )}

        <div className="container">
          <div className="row">
            <div className="col-md-12">
              {data?.length === 0 ? (
                <div className="text-center py-5">
                  <h4>No testimonials found</h4>
                  <p>Be the first to share your experience!</p>
                </div>
              ) : (
                <Swiper
                  modules={[Navigation]}
                  
                  spaceBetween={20}
                  centeredSlides={data?.length === 1}
                  loop={true}
                  navigation={{
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                  }}
                  breakpoints={{
                    320: {
                      slidesPerView: 1,
                      centeredSlides: true,
                    },
                    768: {
                      slidesPerView: 1,
                      centeredSlides: true,
                    },
                    1024: {
                      slidesPerView: 1,
                      centeredSlides: true,
                    },
                    1200: {
                      slidesPerView: 2,
                      spaceBetween: 20,
                      centeredSlides: false,
                    },
                  }}
                  className="mySwiper testimonial-swiper"
                >
                  {data.map((item, index) => (
                    <SwiperSlide key={index}>
                      <div className="testimonial-card">
                        <div className="testimonial-image">
                          <img
                            src={
                              "https://fortmyersradon.com/wp-content/uploads/2019/12/dummy-user-img-1.png"
                            }
                            alt="User"
                          />
                        </div>

                        <div className="testimonial-content">
                          <div className="quote">“</div>
                          <p>{item.description}</p>
                          <div className="quote">”</div>
                          <div className="testimonial-footer">
                            <div className="icons">
                              <Rating
                                readonly
                                initialValue={item?.rating}
                                allowFraction
                              />

                              {item.file_type === "mp3" && (
                                <AiFillAudio
                                  onClick={() => {
                                    setPreviewdata({
                                      type: "audio",
                                      url: item.audio,
                                      ...item,
                                    });
                                    setShow(true);
                                  }}
                                  style={{
                                    fontSize: "20px",
                                    color: "#28a745",
                                    cursor: "pointer",
                                    marginRight: "8px",
                                  }}
                                />
                              )}

                              {item.file_type === "mp4" && (
                                <FaVideo
                                  onClick={() => {
                                    setPreviewdata({
                                      type: "video",
                                      url: item.video,
                                      ...item,
                                    });
                                    setShow(true);
                                  }}
                                  style={{
                                    fontSize: "20px",
                                    color: "#007bff",
                                    cursor: "pointer",
                                  }}
                                />
                              )}

                              {item.audio && item.video && (
                                <>
                                  <AiFillAudio
                                    onClick={() => {
                                      setPreviewdata({
                                        type: "audio",
                                        url: item.audio,
                                        ...item,
                                      });
                                      setShow(true);
                                    }}
                                    style={{
                                      fontSize: "20px",
                                      color: "#28a745",
                                      cursor: "pointer",
                                      marginRight: "8px",
                                    }}
                                  />
                                  <FaVideo
                                    onClick={() => {
                                      setPreviewdata({
                                        type: "video",
                                        url: item.video,
                                        ...item,
                                      });
                                      setShow(true);
                                    }}
                                    style={{
                                      fontSize: "20px",
                                      color: "#007bff",
                                      cursor: "pointer",
                                    }}
                                  />
                                </>
                              )}
                            </div>
                            <strong>{item.name || "Anonymous"}</strong>
                          </div>
                        </div>
                      </div>
                    </SwiperSlide>
                  ))}
                </Swiper>
              )}
            </div>
          </div>
        </div>
      </section>

      <TestimonialModal
        show={show !== null}
        setShow={() => setShow(null)}
        data={previewdata}
      />
    </DefaultLayout>
  );
}
