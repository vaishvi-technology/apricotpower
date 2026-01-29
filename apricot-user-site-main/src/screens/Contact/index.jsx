import React, { useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { contactfeefoimage } from "../../assets/images";
import { toast } from "react-toastify";
import { base_url } from "../../api";
import { getCmcPages, getTag } from "../../api/Services/getDynamicData";
import { Helmet } from "react-helmet-async";
import ReviewModal from "../../components/ReviewModal";
import SuccesMessage from "../../components/SuccesMessage/SuccesMessage";

const Contact = () => {
  const [formData, setFormData] = useState({});
  const [show, setshow] = useState();
  const [isSuccess, setIsSuccess] = useState(false);
  const token = localStorage.getItem("login");
  const [tag, setTag] = useState([]);
  const [pagedata, setpagedata] = useState([]);
  const handleContactSubmit = async (e) => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");

      const res = await fetch(`${base_url}/contact`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });
      document.querySelector(".loaderBox").classList.add("d-none");
      const data = await res.json();

      if (data?.success) {
        setFormData({});
        setIsSuccess(true);
      }
      else{
        toast.error(data?.message)
      }
    } catch (err) {
      document.querySelector(".loaderBox").classList.add("d-none");
      toast.error("Something went wrong. Please try again later.");
    }
  };

  const fetchCmc = async () => {
    try {
      const data = await getCmcPages(4);
      setpagedata(data);
    } catch (error) {
      console.log({ error });
    }
  };
  useEffect(() => {
    fetchCmc();
    fetchTag();
  }, []);
  const fetchTag = async () => {
    try {
      const data = await getTag(5);
      setTag(data?.[0]);
    } catch (error) {
      console.log({ error });
    }
  };

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
        <InnerBanner boldText1="Contact" lightText="US" />

        <section className="contact-us-page">
          <div className="container">
            <div className="contact-us-formDiv">
              <div className="row">
                <div className="col-xl-5 col-lg-6">
                  <div className="contact-us-content">
                    <div
                      dangerouslySetInnerHTML={{ __html: pagedata?.[0]?.title }}
                    />
                    {/* <h2>Get In Touch With us</h2>
                  <p>
                    Apricot Power is your reliable source for quality apricot
                    seeds and B17 products.
                  </p>
                  <p>
                    Apricot Power has been providing apricot seeds since 1999.
                    Over the years our company has grown and now sells more than
                    100 different products and supplements to health conscious
                    customers around the world. Apricot seeds and B17 are our
                    top sellers.
                  </p>
                  <div className="contact-info">
                    <p>
                      <span>Toll Free:</span> (866) 468-7487
                    </p>
                    <p>
                      <span>Outside The USA:</span> 001 + 707-262-1394
                    </p>
                    <p>
                      <span>Fax:</span> 707-413-6556
                    </p>
                    <p>
                      <span>Email:</span> customerservice@apricotpower.com
                    </p>
                    <p>
                      <span>Office Hours:</span>{" "}
                      customerservice@apricotpower.com
                    </p>
                  </div>

                  <div className="feefo-imgDiv">
                    <img src={'https://api.feefo.com/api/logo?merchantidentifier=apricot-power&template=Service-Stars-White-316x80.png&since=alls'} alt="Feefo Image" />
                  </div> */}
                    <div className="feefo-imgDiv mt-2 cursor">
                      <img
                        src={
                          "https://api.feefo.com/api/logo?merchantidentifier=apricot-power&template=Service-Stars-White-316x80.png&since=alls"
                        }
                        alt="Feefo Image"
                        onClick={() => setshow(true)}
                      />
                    </div>
                  </div>
                </div>
                <div className="col-xl-7 col-lg-6">
                  <div className="contact-us-form">
                    <div className="row">
                      <div className="contact-form-group col-md-6 mb-5">
                        <label htmlFor="firstName" className="form-label">
                          First Name
                        </label>
                        <input
                          name={"first_name"}
                          value={formData.first_name}
                          onChange={(e) =>
                            setFormData({
                              ...formData,
                              first_name: e.target.value,
                            })
                          }
                          type="text"
                          id="firstName"
                          className="form-control"
                        />
                      </div>

                      <div className="contact-form-group col-md-6 mb-5">
                        <label htmlFor="lastName" className="form-label">
                          Last Name
                        </label>
                        <input
                          name="last_name"
                          value={formData.last_name}
                          onChange={(e) =>
                            setFormData({
                              ...formData,
                              last_name: e.target.value,
                            })
                          }
                          type="text"
                          id="lastName"
                          className="form-control"
                        />
                      </div>

                      <div className="contact-form-group col-md-6 mb-5">
                        <label htmlFor="email" className="form-label">
                          Email
                        </label>
                        <input
                          name="email"
                          value={formData.email}
                          onChange={(e) =>
                            setFormData({ ...formData, email: e.target.value })
                          }
                          type="text"
                          id="email"
                          className="form-control"
                        />
                      </div>

                      <div className="contact-form-group col-md-6 mb-5">
                        <label htmlFor="phone" className="form-label">
                          Phone Number
                        </label>
                        <input
                          name="phone"
                          value={formData.phone}
                          onChange={(e) =>
                            setFormData({ ...formData, phone: e.target.value })
                          }
                          type="text"
                          id="phone"
                          className="form-control"
                        />
                      </div>

                      <div className="contact-form-group col-md-12 mb-4">
                        <label htmlFor="message" className="form-label">
                          Message
                        </label>
                        <textarea
                          name="message"
                          value={formData.message}
                          onChange={(e) =>
                            setFormData({
                              ...formData,
                              message: e.target.value,
                            })
                          }
                          id="message"
                          placeholder="Write your message..."
                          className="form-control"
                          rows="5"
                        ></textarea>
                      </div>

                      <div className="col-md-12 ">
                        {isSuccess && (
                          <SuccesMessage
                            title="Message Sent Successfully!"
                            subTitle="Thank you for reaching out. Our team has received your message and will get back to you as soon as possible."
                            setIsSuccess={setIsSuccess}
                          />
                        )}
                        <button
                          onClick={handleContactSubmit}
                          type="submit"
                          className="button-with-icon mt-4"
                        >
                          Get in touch with us
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <ReviewModal show={show} setshow={setshow} />
      </DefaultLayout>
    </>
  );
};

export default Contact;
