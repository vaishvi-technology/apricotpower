import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Pagination, Autoplay } from "swiper/modules";
import { useEffect, useState } from "react";
import { nexticon, previcon } from "../../assets/images";
import { base_url } from "../../api";
import { Image } from "react-bootstrap";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "./BannerSlider.css";
import { Link } from "react-router-dom";
import { FaChevronLeft, FaChevronRight } from "react-icons/fa6";

const BannerSlider = () => {
  const [images, setImages] = useState([]);
  const [isMobile, setIsMobile] = useState(false);

  // Detect screen < 480px
  useEffect(() => {
    const checkScreen = () => {
      setIsMobile(window.innerWidth < 480);
    };
    checkScreen();

    window.addEventListener("resize", checkScreen);
    return () => window.removeEventListener("resize", checkScreen);
  }, []);

  const fetchBanner = () => {
    document.querySelector(".loaderBox")?.classList.remove("d-none");

    fetch(`${base_url}/slider/`)
      .then((response) => response.json())
      .then((data) => {
        document.querySelector(".loaderBox")?.classList.add("d-none");
        setImages(data);
      })
      .catch((err) => {
        console.error(err);
        document.querySelector(".loaderBox")?.classList.add("d-none");
      });
  };

  useEffect(() => {
    fetchBanner();
  }, []);

  return (
    <section className="banner-slider-section">
      {images.length !== 0 && (
        <Swiper
          modules={[Navigation, Autoplay]}
          pagination={{ clickable: true }}
          autoplay={{ delay: 5000 }}
          loop
          navigation={{
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          }}
          className="swiper-container"
        >
          {/* Navigation Buttons */}
          <div className="our-products-swiper-buttons swiper-buttons-banner">
            <div
              className="swiper-button-next laptop-button-next"
              // style={{ backgroundColor: "transparent", border: "none" }}
            >
              {/* <FaChevronRight color="white" /> */}
                    <img src={nexticon} alt="" />
            </div>
            <div
              className="swiper-button-prev laptop-button-prev"
              // style={{ backgroundColor: "transparent", border: "none" }}
            >
                 <img src={previcon} alt="" />
            </div>
          </div>

          {images.map((src, index) => (
            <SwiperSlide key={index}>
              <div className="banner-image-wrapper">
                {src.type === "video" ? (
                  <Link to={`/videos#${src.id}`}>
                    <Image
                      src={isMobile ? src.mobile_url : src.file}
                      alt={`Banner ${index + 1}`}
                      className="banner-image"
                    />
                  </Link>
                ) : (
                  <a
                    href={src.product_link}
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    <Image
                      src={isMobile ? src.mobile_url : src.file}
                      alt={`Banner ${index + 1}`}
                      className="banner-image"
                    />
                  </a>
                )}
              </div>
            </SwiperSlide>
          ))}
        </Swiper>
      )}
    </section>
  );
};

export default BannerSlider;
