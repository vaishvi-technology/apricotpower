import { useState } from "react";
import CustomButton from "../../components/CustomButton";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import CustomInput from "../../components/CustomInput";
import {
  addTestimonial,
  getImageUrl,
} from "../../api/Services/testimonialServices";
import { useLocation, useNavigate } from "react-router-dom";
import CustomTextArea from "../../components/CustomTextArea";
import { toast } from "react-toastify";
import { Rating } from "react-simple-star-rating";

export default function AddFeedBack() {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const location = useLocation();
  const queryParams = new URLSearchParams(location.search);
  const orderId = queryParams.get("order_id");
  const [formData, setFormData] = useState({
    review: "",
    video: "",
    audio: "",
    file_type: "",
  });
  const [rating, setRating] = useState(0);

  const handleRating = (rate) => {
    setRating(rate);
  };

  const handleChange = async (e) => {
    const { name, value, files } = e.target;

    if (files && files[0]) {
      try {
        const uploadData = new FormData();
        uploadData.append("file", files[0]);
        uploadData.append("type", "testimonials");
        const response = await getImageUrl(uploadData);

        if (response?.status) {
          setFormData((prev) => ({
            ...prev,
            [name]: response.file,
            file_type: response.file_type,
          }));
        }
      } catch (error) {
        console.error("File upload error:", error);
      }
    } else {
      setFormData((prev) => ({
        ...prev,
        [name]: value,
      }));
    }
  };

  const handleSubmit = async (e) => {
    setLoading(true);
    e.preventDefault();

    try {
      const finaldata = {
        ...formData,
        rating: rating,
      };
      const response = await addTestimonial(finaldata, orderId);
      setLoading(false);
      // toast.success(response?.message);
      navigate("/");
    } catch (error) {
      console.error("Submission error:", error);
      setLoading(false);
    }
  };

  return (
    <DefaultLayout>
      <InnerBanner boldText="Add FeedBack" />
      <form onSubmit={handleSubmit} className="center-class">
        <div className="row">
          <div className="col-lg-12 mb-3">
            <CustomTextArea
              label="Your Reviews"
              placeholder="Submit Your Reviews"
              required
              id="review"
              type="textarea"
              labelClass="mainLabel"
              inputClass="mainInput"
              name="review"
              value={formData.review}
              onChange={handleChange}
              rows={5}
            />
          </div>

          <div className="col-lg-12 mb-3">
            <CustomInput
              label="Upload Voice"
              id="uploadFiles"
              type="file"
              accept='audio/*'
              labelClass="mainLabel"
              inputClass="mainInput"
              name="audio"
              onChange={handleChange}
            />
          </div>
          <div className="col-lg-12 mb-3">
            <CustomInput
              label="Upload Video"
              id="uploadFiles"
              type="file"
              accept="video/*"
              labelClass="mainLabel"
              inputClass="mainInput"
              name="video"
              onChange={handleChange}
            />
          </div>
          <Rating onClick={handleRating} allowFraction />
          <div className="col-md-12">
            <div className="account_details-buttons d-flex flex-wrap justify-content-center gap-2">
              <CustomButton
                type="submit"
                text={`${loading ? "Submitting..." : "Submit Feedback"}`}
                variant="secondaryButton"
              />
            </div>
          </div>
        </div>
      </form>
    </DefaultLayout>
  );
}
