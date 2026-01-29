import { useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";

import { toast } from "react-toastify";
import { Helmet } from "react-helmet-async";
import { getTag } from "../../api/Services/getDynamicData";
import SuccesMessage from "../../components/SuccesMessage/SuccesMessage";

const WholesaleApplication = () => {
  const [tag, setTag] = useState([]);
  const [isSuccess, setIsSuccess] = useState(false);
  const [formData, setFormData] = useState({
    company_name: "",
    store_type: [],
    experience: "",
    interest_in_product: [],
    first_name: "",
    last_name: "",
    job_title: "",
    area_code: "",
    phone_number: "",
    email: "",
    website: "",
    street_address: "",
    street_address_line2: "",
    city: "",
    state: "",
    zip_code: "",
    country_id: "",
    selling_plane: "",
    signature_first_name: "",
    signature_last_name: "",
    signature_date: "",
    attachment: null,
  });

  const handleChange = (e) => {
    const { id, value, type, files } = e.target;
    if (type === "file") {
      setFormData({ ...formData, attachment: files[0] });
    } else {
      setFormData({ ...formData, [id]: value });
    }
  };

  const handleCheckboxChange = (group, value) => {
    setFormData((prev) => {
      const current = prev[group];
      const updated = current.includes(value)
        ? current.filter((item) => item !== value)
        : [...current, value];
      return { ...prev, [group]: updated };
    });
  };

  const handleSubmit = async () => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");

      const payload = new FormData();
      for (const key in formData) {
        if (Array.isArray(formData[key])) {
          payload.append(key, formData[key].join(", "));
        } else {
          payload.append(key, formData[key]);
        }
      }

      const response = await fetch(
        "https://staging.apricotpower.com/apricot-app/mail.php",
        {
          method: "POST",
          body: payload,
        }
      );

      const result = await response.json();
      if (result?.status == 200) {
        setIsSuccess(true);
        setFormData({
          company_name: "",
          store_type: [],
          experience: "",
          interest_in_product: [],
          first_name: "",
          last_name: "",
          job_title: "",
          area_code: "",
          phone_number: "",
          email: "",
          website: "",
          street_address: "",
          street_address_line2: "",
          city: "",
          state: "",
          zip_code: "",
          country_id: "",
          selling_plane: "",
          signature_first_name: "",
          signature_last_name: "",
          signature_date: "",
          attachment: null,
        });
      }
      else{
        toast.error(result?.errors)
      }

      // toast.success("Application submitted successfully!");
      document.querySelector(".loaderBox").classList.add("d-none");
    } catch (error) {
      console.error("Submission error:", error);
      document.querySelector(".loaderBox").classList.add("d-none");

      toast.error(error?.response?.data?.message);
    }
  };
  const fetchTag = async () => {
    try {
      const data = await getTag(18);
      setTag(data?.[0]);
    } catch (error) {
      console.log({ error });
    }
  };
  useEffect(() => {
    fetchTag();
  }, []);

  return (
    <>
      <Helmet>
        {tag.PageMetaTitle && <title>{tag.PageMetaTitle}</title>}

        {tag?.PageShowMETA === 1 && (
          <>
            <meta name="description" content={tag.PageMetaDesc || ""} />
            <meta name="keywords" content={tag.PageMetaKeywords || ""} />

            <meta property="og:title" content={tag.PageOGTitle || ""} />
            <meta property="og:type" content={tag.PageOGType || ""} />
            <meta property="og:url" content={tag.PageOGURL || ""} />
            <meta property="og:image" content={tag.PageOGImage || ""} />
          </>
        )}
      </Helmet>
      <DefaultLayout>
        <InnerBanner lightText="Carry our" boldText="products" />
        <section className="wholesale-application">
          <div className="container">
            <div className="wholesale-application-formDiv">
              {/* Company Info Section */}
              <div className="row justify-content-center ">
                <div className="col-md-12 text-center ">
                  <h2>
                    <span className="lightText primary-color">
                      Apricot Power -
                    </span>
                    <br />
                    <span className="boldText secondary-color">
                      WHOLESALE APPLICATION
                    </span>
                  </h2>
                </div>
                <div className="col-xxl-6 col-xl-8 col-lg-10">
                  <div className="wholesale-application-form">
                    {/* Company Name */}
                    <div className="wholesale-application-form-group">
                      <label htmlFor="company_name">Company/Store Name</label>
                      <input
                        type="text"
                        className="form-control"
                        id="company_name"
                        value={formData?.company_name}
                        placeholder="Company/Store Name"
                        onChange={handleChange}
                      />
                    </div>

                    {/* Store Type */}
                    <div className="wholesale-application-form-group">
                      <label>What type of store are you?</label>
                      <div className="form_check_boxes">
                        {[
                          "Retail Storefront",
                          "Online Retailer",
                          "Amazon Seller",
                          "Clinic/doctor",
                          "Distributor",
                        ].map((type, idx) => (
                          <div className="form-group" key={idx}>
                            <input
                              type="checkbox"
                              id={`store_type_${idx}`}
                              onChange={() =>
                                handleCheckboxChange("store_type", type)
                              }
                            />
                            <label htmlFor={`store_type_${idx}`}>{type}</label>
                          </div>
                        ))}
                      </div>
                    </div>

                    {/* Years in Business */}
                    <div className="wholesale-application-form-group">
                      <label htmlFor="experience">
                        How many years in business?
                      </label>
                      <input
                        type="text"
                        className="form-control"
                        id="experience"
                        value={formData?.experience}
                        placeholder="Years"
                        onChange={handleChange}
                      />
                    </div>

                    {/* Interest in Product */}
                    <div className="wholesale-application-form-group">
                      <label>Interest in Apricot Power products</label>
                      <div className="form_check_boxes">
                        {[
                          "Customer Requested product in store.",
                          "Wanting to add new health products in general.",
                          "Love the health benefits of Apricot Seeds and/or B17",
                          "Personal Experience",
                          "Social Media",
                          "Others",
                        ].map((interest, idx) => (
                          <div className="form-group" key={idx}>
                            <input
                              type="checkbox"
                              id={`interest_${idx}`}
                              onChange={() =>
                                handleCheckboxChange(
                                  "interest_in_product",
                                  interest
                                )
                              }
                            />
                            <label htmlFor={`interest_${idx}`}>
                              {interest}
                            </label>
                          </div>
                        ))}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {/* Contact Information */}
              <hr />
              <div className="row justify-content-center  ">
                <div className="col-md-12 text-center">
                  <h2>
                    <span className="lightText primary-color">Contact</span>{" "}
                    <span className="boldText secondary-color">
                      INFORMATION
                    </span>
                  </h2>
                </div>
                <div className="col-xxl-6 col-xl-8 col-lg-10">
                  <div className="wholesale-application-ContactInfo">
                    {/* Full Name */}
                    <div className="wholesale-application-form-group">
                      <label>Full Name</label>
                      <div className="row">
                        <div className="col-md-6 mb-md-0 mb-3">
                          <input
                            type="text"
                            className="form-control"
                            id="first_name"
                            value={formData?.first_name}
                            placeholder="First Name"
                            onChange={handleChange}
                          />
                        </div>
                        <div className="col-md-6">
                          <input
                            type="text"
                            className="form-control"
                            id="last_name"
                            value={formData?.last_name}
                            placeholder="Last Name"
                            onChange={handleChange}
                          />
                        </div>
                      </div>
                    </div>

                    <div className="wholesale-application-form-group">
                      <label htmlFor="job_title">Job Title</label>
                      <input
                        type="text"
                        value={formData?.job_title}
                        className="form-control"
                        id="job_title"
                        onChange={handleChange}
                      />
                    </div>

                    {/* Phone Number */}
                    <div className="wholesale-application-form-group">
                      <label>Phone Number*</label>
                      <div className="row">
                        <div className="col-md-6 mb-md-0 mb-3">
                          <input
                            type="text"
                            className="form-control"
                            id="area_code"
                            value={formData?.area_code}
                            placeholder="Area code"
                            onChange={handleChange}
                          />
                        </div>
                        <div className="col-md-6">
                          <input
                            type="number"
                            className="form-control"
                            id="phone_number"
                            value={formData?.phone_number}
                            placeholder="Phone number"
                            onChange={handleChange}
                          />
                        </div>
                      </div>
                    </div>

                    {/* Email, Website */}
                    <div className="wholesale-application-form-group">
                      <label htmlFor="email">Email*</label>
                      <input
                        type="text"
                        value={formData?.email}
                        className="form-control"
                        id="email"
                        onChange={handleChange}
                      />
                    </div>
                    <div className="wholesale-application-form-group">
                      <label htmlFor="website">Website</label>
                      <input
                        type="text"
                        value={formData?.website}
                        className="form-control"
                        id="website"
                        onChange={handleChange}
                      />
                    </div>

                    {/* Shipping Address */}
                    <div className="wholesale-application-form-group ">
                      <label>Shipping Address*</label>
                      <input
                        type="text"
                        value={formData?.street_address}
                        className="form-control mb-3"
                        id="street_address"
                        placeholder="Street Address"
                        onChange={handleChange}
                      />
                      <input
                        type="text"
                        className="form-control mb-3"
                        id="street_address_line2"
                        value={formData?.street_address_line2}
                        placeholder="Street Address Line 2"
                        onChange={handleChange}
                      />
                      <div className="row">
                        <div className="col-md-6 mb-3">
                          <input
                            type="text"
                            value={formData?.city}
                            className="form-control"
                            id="city"
                            placeholder="City"
                            onChange={handleChange}
                          />
                        </div>
                        <div className="col-md-6 mb-3">
                          <input
                            type="text"
                            value={formData?.state}
                            className="form-control"
                            id="state"
                            placeholder="State"
                            onChange={handleChange}
                          />
                        </div>
                        <div className="col-md-6 mb-3">
                          <input
                            type="text"
                            className="form-control"
                            id="zip_code"
                            value={formData?.zip_code}
                            placeholder="Postal/Zip Code"
                            onChange={handleChange}
                          />
                        </div>
                        <div className="col-md-6 mb-3">
                          <input
                            type="text"
                            value={formData?.country_id}
                            className="form-control"
                            id="country_id"
                            placeholder="Country"
                            onChange={handleChange}
                          />
                        </div>
                      </div>
                    </div>

                    <div className="wholesale-application-form-group">
                      <label htmlFor="selling_plane">
                        How do you plan on marketing? *
                      </label>
                      <textarea
                        className="form-control"
                        id="selling_plane"
                        value={formData?.selling_plane}
                        rows="4"
                        onChange={handleChange}
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>

              <hr />
              <div className="row justify-content-center ">
                <div className="col-md-12  text-center">
                  <h2>
                    <span className="lightText primary-color">
                      DISCLAIMER &{" "}
                    </span>
                    <span className="boldText secondary-color">
                      TERMS OF AGREEMENT
                    </span>
                  </h2>
                </div>
                <div className="col-xxl-6 col-xl-8 col-lg-10">
                  <div className="wholesale-application-termsAgreement">
                    <div className="wholesale-application-form-group">
                      <label>Signature*</label>
                      <div className="row">
                        <div className="col-md-6 mb-md-0 mb-3">
                          <input
                            type="text"
                            className="form-control"
                            name="ignature_first_name"
                            id="signature_first_name"
                            placeholder="First Name"
                            value={formData?.signature_first_name}
                            onChange={handleChange}
                          />
                        </div>
                        <div className="col-md-6">
                          <input
                            type="text"
                            className="form-control"
                            id="signature_last_name"
                            value={formData?.signature_last_name}
                            name="signature_last_name"
                            placeholder="Last Name"
                            onChange={handleChange}
                          />
                        </div>
                      </div>
                    </div>
                    <div className="wholesale-application-form-group">
                      <label htmlFor="signature_date">Signature date</label>
                      <input
                        type="date"
                        className="form-control"
                        id="signature_date"
                        name="signature_date"
                        value={formData?.signature_date}
                        placeholder="MM-DD-YYYY"
                        onChange={handleChange}
                      />
                    </div>

                    <div className="wholesale-application-form-group">
                      <label htmlFor="attachment">
                        Attach Business License or Tax Resale Certificate*
                      </label>
                      <input
                        type="file"
                        className="form-control"
                        id="attachment"
                        name="file"
                        onChange={handleChange}
                      />
                    </div>
                    {isSuccess && (
                      <SuccesMessage
                        title="Wholesale Application Submitted!"
                        subTitle="Thank you for your interest in partnering with us. Our team will review your application and contact you shortly with the next steps."
                        setIsSuccess={setIsSuccess}
                      />
                    )}
                  </div>
                </div>
              </div>

              <div className="row">
                <div className="col-md-12 text-center">
                  <button className="button-with-icon" onClick={handleSubmit}>
                    Submit
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>
      </DefaultLayout>
    </>
  );
};

export default WholesaleApplication;
