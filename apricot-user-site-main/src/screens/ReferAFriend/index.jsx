import React, { useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { contactfeefoimage, referfriendverifyimg } from "../../Assets/images";
import { base_url, Referal_url } from "../../api";
import { toast } from "react-toastify";
import CustomModal from "../../components/CustomModal";
import { Button } from "react-bootstrap";
import { FaFacebook, FaEnvelope } from "react-icons/fa";
import { getCmcPages, getTag } from "../../api/Services/getDynamicData";
import { Helmet } from "react-helmet-async";
import { Link, useNavigate } from "react-router-dom";
import ReviewModal from "../../components/ReviewModal";

const ReferAFriend = () => {
  const [formData, setFormData] = React.useState({});
  const [show, setshow] = useState();
  const [pagedata, setpagedata] = React.useState({});
  const [tag, setTag] = useState([]);
  const [referCodeShow, setReferCodeShow] = React.useState(false);
  const [referCode, setReferCode] = React.useState("");
  const token = localStorage.getItem("login");

  const handleReferalCode = async (e) => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");
      if (formData?.email === undefined || formData?.email === "") {
        document.querySelector(".loaderBox").classList.add("d-none");

        return toast.error("Please enter email address");
      }

      const res = await fetch(`${base_url}/reffer/user`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          // Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });
      document.querySelector(".loaderBox").classList.add("d-none");
      const data = await res.json();

      if (data.success) {
        // toast.success(data?.message);;
        setReferCodeShow(true);
        setFormData({});
        setReferCode(data?.code);
      } else {
        toast.error(data?.message);
      }
    } catch (err) {
      console.log(err);
      document.querySelector(".loaderBox").classList.add("d-none");
      toast.error("Something went wrong. Please try again later.");
    }
  };

  const referralLink = "apricotpower.com/r/117564";

  const handleFacebookShare = () => {
    const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(
      referralLink
    )}`;
    window.open(shareUrl, "_blank");
  };

  const handleEmailShare = () => {
    window.location.href = `mailto:?body=Check this out: ${referralLink}`;
  };
  const [url, seturl] = useState("wfwerrwrwerr");
  const GetReferal = () => {
    fetch(`${base_url}/refferal/link`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => response.json())
      .then(async (data) => {
        seturl(Referal_url + data?.code);
      })
      .catch((error) => {});
  };

  const fetchCmc = async () => {
    try {
      const data = await getCmcPages(6);
      setpagedata(data);
    } catch (error) {
      console.log({ error });
    }
  };
  useEffect(() => {
    fetchCmc();
    GetReferal();
    fetchTag();
  }, []);
  const fetchTag = async () => {
    try {
      const data = await getTag(6);
      setTag(data?.[0]);
    } catch (error) {
      console.log({ error });
    }
  };

  const navigate = useNavigate();
  const [emailPlaceholder, setEmailPlaceholder] = useState("");

  useEffect(() => {
    const updatePlaceholder = () => {
      if (window.innerWidth < 450) {
        setEmailPlaceholder("Email Address");
      } else {
        setEmailPlaceholder(
          "What is the email address on your Apricot Power account?"
        );
      }
    };

    updatePlaceholder();
    window.addEventListener("resize", updatePlaceholder);

    return () => window.removeEventListener("resize", updatePlaceholder);
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
        <InnerBanner
          className="refer-a-friend"
          boldText1="Refer A Friend"
          lightText="To Apricot Power & You Both Save!"
        />

        <section className="referFriend-sec">
          <div className="container">
            <div className="referFriend-secondary-banner">
              <div className="row align-items-center">
                <div className="col-xl-6">
                  <div className="referFriend-form">
                    <div className="referFriend-form-head">
                      <img src={referfriendverifyimg} alt="verify image" />
                    </div>
                    <div className="referFriend-form-body">
                      <h3
                        onClick={() => navigate("/wholesale-application")}
                        className="cursor"
                      >
                        JOIN THE MOVEMENT! <br /> BECOME A RETAILER TODAY
                      </h3>
                      <img
                        src={
                          "https://api.feefo.com/api/logo?merchantidentifier=apricot-power&template=Service-Stars-White-316x80.png&since=all"
                        }
                        alt="Feefo"
                        className="cursor"
                        onClick={() => setshow(true)}
                      />
                      {/* <img src={contactfeefoimage} alt="Feefo Image" /> */}
                    </div>
                    {token ? (
                      <div
                        className="container mt-5 p-4 border rounded"
                        style={{ maxWidth: "500px" }}
                      >
                        <h3 className="text-center mb-4">
                          {pagedata?.[1]?.title}
                        </h3>
                        <p className="text-center mb-3">
                          Use these links to easily refer a friend:
                        </p>

                        <div className="mb-4">
                          <label htmlFor="referralLink" className="form-label">
                            Direct Link:
                          </label>
                          <div className="input-group">
                            <input
                              type="text"
                              className="form-control"
                              id="referralLink"
                              value={url}
                              readOnly
                            />
                            <button
                              className="btn btn-outline-secondary"
                              onClick={() => navigator.clipboard.writeText(url)}
                            >
                              Copy
                            </button>
                          </div>
                        </div>

                        <div className="d-flex justify-content-center gap-3">
                          <Button
                            variant="primary"
                            onClick={handleFacebookShare}
                          >
                            <FaFacebook className="me-2" />
                            Share on Facebook
                          </Button>
                          <Button variant="info" onClick={handleEmailShare}>
                            <FaEnvelope className="me-2" />
                            Share via E-mail
                          </Button>
                        </div>
                      </div>
                    ) : (
                      <div className="referFriend-inputDiv">
                        <h4>Start Referring Today!</h4>
                        <div className="referFriend-input-group">
                          <input
                            name="email"
                            value={formData?.email}
                            onChange={(e) => {
                              setFormData({
                                ...formData,
                                [e.target.name]: e.target.value,
                              });
                            }}
                            type="text"
                            className="form-control"
                            placeholder={emailPlaceholder}
                          />
                          <button
                            onClick={() => {
                              handleReferalCode();
                            }}
                            className="button-with-icon"
                          >
                            Submit
                          </button>
                        </div>
                      </div>
                    )}
                  </div>
                </div>
                <div className="col-xl-6">
                  <div className="referFriend-content">
                    <div
                      dangerouslySetInnerHTML={{
                        __html: pagedata?.[0]?.description,
                      }}
                    />
                    {/* <h2>
                    Share what you know <br /> with your friends!
                  </h2>
                  <ul className="sreferFriend-content-list">
                    <li>
                      <span className="white-color">Earn $20*</span> for every
                      person you recommend
                    </li>
                    <li>
                      Your friend <span className="white-color">saves $10</span>{" "}
                      on their first order
                    </li>
                    <li>
                      <span className="white-color">No limits</span> on referral
                      credits
                    </li>
                  </ul> */}
                  </div>
                </div>
              </div>
            </div>

            <div className="referFriend-cards">
              <div className="row">
                <div className="col-lg-4">
                  <div className="referFriend-card">
                    <h3>{pagedata?.[1]?.title}</h3>
                    <p>
                      {" "}
                      <div
                        dangerouslySetInnerHTML={{
                          __html: pagedata?.[1]?.description,
                        }}
                      />
                    </p>
                  </div>
                </div>
                <div className="col-lg-4">
                  <div className="referFriend-card">
                    <h3>{pagedata?.[2]?.title}</h3>
                    <p>
                      {" "}
                      <div
                        dangerouslySetInnerHTML={{
                          __html: pagedata?.[2]?.description,
                        }}
                      />
                    </p>
                  </div>
                </div>
                <div className="col-lg-4">
                  <div className="referFriend-card">
                    <h3>{pagedata?.[3]?.title}</h3>
                    <p>
                      {" "}
                      <div
                        dangerouslySetInnerHTML={{
                          __html: pagedata?.[3]?.description,
                        }}
                      />
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div className="referFriend-info">
              <div className="row">
                <div className="col-md-12">
                  <div className="referFriend-info-content">
                    *Our regular referral bonus is $10, but we are running a
                    limited-time DOUBLE BONUS <br /> PROGRAM! Your bonus is paid
                    as soon as your referred friend places their first order.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <CustomModal
          show={referCodeShow}
          close={() => {
            setReferCodeShow(false);
          }}
          size="lg"
          heading="Refer Code"
        >
          <h1 style={{ textAlign: "center", fontSize: "30px" }}>
            {Referal_url + referCode}
          </h1>
        </CustomModal>
        <ReviewModal show={show} setshow={setshow} />
      </DefaultLayout>
    </>
  );
};

export default ReferAFriend;
