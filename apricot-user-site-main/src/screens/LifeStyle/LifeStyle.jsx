/* eslint-disable react/prop-types */
import { useState } from "react";
import "./LifestyleSection.css";

const LifestyleSection = ({ images }) => {
  const [selectedImage, setSelectedImage] = useState(null);

  return (
    <>
      <section className="lifestyle-section py-5">
        <div className="container text-center">
          <div className="lifestyle-images d-flex flex-wrap justify-content-center gap-4">
            {images?.map((image, index) => (
              <img
                key={index}
                src={image?.image_url}
                alt={image?.image}
                className="lifestyle-img"
                onClick={() => setSelectedImage(image?.image_url)}
              />
            ))}
          </div>
        </div>
      </section>

      {selectedImage && (
        <div className="image-modal" onClick={() => setSelectedImage(null)}>
          <img src={selectedImage} alt="Full View" className="modal-img" />
          <span className="close-btn">&times;</span>
        </div>
      )}
    </>
  );
};

export default LifestyleSection;
