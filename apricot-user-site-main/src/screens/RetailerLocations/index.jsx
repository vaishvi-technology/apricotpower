/* eslint-disable react-hooks/exhaustive-deps */
import { useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import WeShipFast from "../../components/WeShipFast";

import {
  contactfeefoimage,
  retailerlocationsideimg,
  referfriendverifyimg,
  authorize,
  paypal,
} from "../../assets/images";
import { base_url } from "../../api";
import CustomTable from "../../components/CustomTable";
import { Link, useNavigate } from "react-router-dom";
import { getCmcPages, getTag } from "../../api/Services/getDynamicData";
import { Helmet } from "react-helmet-async";
import ReviewModal from "../../components/ReviewModal";
import { addRetailer } from "../../api/Services/retailerServices";
import SuccesMessage from "../../components/SuccesMessage/SuccesMessage";
import { toast } from "react-toastify";

const RetailerLocations = () => {
  const [show, setshow] = useState();
  const [isSuccess, setIsSuccess] = useState(false);
  const [countries, setCountries] = useState([]);
  const [states, setStates] = useState([]);
  const [formData, setformData] = useState({
    store_name: "",
    location: "",
  });
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [data, setData] = useState([]);
  const [tag, setTag] = useState([]);
  const [pagedata, setpagedata] = useState([]);

  const [selectedCountry, setSelectedCountry] = useState("");
  const [selectedState, setSelectedState] = useState("");
  const [zipCode, setZipCode] = useState("");
  const token = localStorage.getItem("login");

  useEffect(() => {
    fetchCountries();
  }, []);

  const fetchCountries = async () => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");
      const response = await fetch(`${base_url}/countries`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });
      const data = await response.json();
      setCountries(data);
    } catch (error) {
      console.error(error);
    } finally {
      document.querySelector(".loaderBox").classList.add("d-none");
    }
  };

  const fetchStates = async (countryId) => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");
      const response = await fetch(`${base_url}/state/${countryId}`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });
      const data = await response.json();
      setStates(data);
    } catch (error) {
      console.error(error);
    } finally {
      document.querySelector(".loaderBox").classList.add("d-none");
    }
  };

  const handleCountryChange = (e) => {
    const countryId = e.target.value;
    setSelectedCountry(countryId);
    setStates([]);
    setSelectedState("");
    fetchStates(countryId);
  };
  const handleCountrySearch = async () => {
    if (!selectedCountry) {
      alert("Please select a country.");
      return;
    }

    await fetchRetailers(selectedCountry, "", "", 1);
  };

  const handleStateSearch = async () => {
    if (!selectedCountry) {
      alert("Please select a country.");
      return;
    }

    if (!selectedState) {
      alert("Please select a state.");
      return;
    }

    await fetchRetailers(selectedCountry, selectedState, "", 1);
  };

  const handleZipSearch = async () => {
    if (!selectedCountry) {
      alert("Please select a country.");
      return;
    }

    if (!selectedState) {
      alert("Please select a state.");
      return;
    }

    if (!zipCode.trim()) {
      alert("Please enter a ZIP code.");
      return;
    }

    await fetchRetailers(selectedCountry, selectedState, zipCode, 1);
  };

  const fetchRetailers = async (countryId, stateId, zip, pageNum = page) => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");

      const queryParams = new URLSearchParams({
        country_id: countryId,
        state_id: stateId,
        page: pageNum,
        zip_code: zip,
      });

      const response = await fetch(
        `${base_url}/retailer-search?${queryParams.toString()}`,
        {
          method: "GET",
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        }
      );

      const data = await response.json();
      setData(data);
      setTotalPages(data?.total_pages);
    } catch (error) {
      console.error("Error fetching retailers:", error);
    } finally {
      document.querySelector(".loaderBox").classList.add("d-none");
    }
  };

  const maleHeaders = [
    {
      key: "id",
      title: "S.No",
    },

    {
      key: "Name",
      title: "Name",
    },
    {
      key: "Phone",
      title: "Phone",
    },
    {
      key: "link",
      title: "link",
    },
    {
      key: "email",
      title: "Email",
    },
  ];

  useEffect(() => {
    if (selectedCountry) {
      fetchRetailers(selectedCountry, selectedState, zipCode, page);
    }
  }, [page]);

  const handlePageChange = (newPage) => {
    if (newPage >= 1 && newPage <= totalPages) {
      setPage(newPage);
    }
  };
  const fetchCmc = async () => {
    try {
      const data = await getCmcPages(3);
      setpagedata(data);
    } catch (error) {
      console.log({ error });
    }
  };
  const fetchTag = async () => {
    try {
      const data = await getTag(3);
      setTag(data?.[0]);
    } catch (error) {
      console.log({ error });
    }
  };

  useEffect(() => {
    fetchCmc();
    fetchTag();
  }, []);

  const navigate = useNavigate();
  const HandleChange = (e) => {
    const { value, name } = e.target;
    setformData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };
  const HandleSubmit = async () => {
    document.querySelector(".loaderBox").classList.remove("d-none");
    try {
      const response = await addRetailer(formData);
      console.log(response)
      if (response?.status) {
        setIsSuccess(true);
        setformData({
          location: "",
          store_name: "",
        });
        document.querySelector(".loaderBox").classList.add("d-none");
      } else {
        toast.error(response?.errors);
        document.querySelector(".loaderBox").classList.add("d-none");
      }
    } catch (error) {
      document.querySelector(".loaderBox").classList.add("d-none");
      console.log(error);
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
        <InnerBanner lightText="Retailer" boldText="Locations" />

        <section className="retailer-locations">
          <div className="container">
            <div className="row">
              <div className="col-xxl-9 col-xl-8 col-lg-7">
                <div className="retailer-locations-content">
                  <div className="retailer-locations-content-head">
                    <p>
                      <div
                        dangerouslySetInnerHTML={{
                          __html: pagedata?.[0]?.title,
                        }}
                      />
                    </p>
                  </div>

                  <div className="attention-retailers-form">
                    <div className="attention-retailers-form-head">
                      <h2>
                        <span className="primary-color">Attention</span>{" "}
                        <span className="secondary-color">Retailers</span>
                      </h2>
                      <p>
                        Apricot Power is sold in over 1,000 retail locations
                        throughout North America. Please give us a call at{" "}
                        <a href="tel:1-866-468-7487">1-866-468-7487</a>, or
                        shoot us an email at{" "}
                        <a href="mailto:customerservice@apricotpower.com">
                          {" "}
                          customerservice@apricotpower.com
                        </a>{" "}
                        for information regarding local stores in your
                        community, or let us know of any stores that you’d like
                        to carry our products below.
                      </p>
                    </div>

                    {/* <div className="attention-retailers-formDiv">
                      <div className="attention-retailers-form-group">
                        <label>Search by Country</label>
                        <div className="attention-retailers-input-group">
                          <select
                            className="form-control"
                            onChange={handleCountryChange}
                            value={selectedCountry}
                          >
                            <option value="">Select Country</option>
                            {countries.map((country) => (
                              <option key={country.id} value={country.id}>
                                {country.name}
                              </option>
                            ))}
                          </select>
                          <button
                            className="button-with-icon"
                            onClick={handleCountrySearch}
                          >
                            Go
                          </button>
                        </div>
                      </div>

                      <div className="attention-retailers-form-group">
                        <label>Search by State or Province</label>
                        <div className="attention-retailers-input-group">
                          <select
                            className="form-control"
                            onChange={(e) => setSelectedState(e.target.value)}
                            value={selectedState}
                            disabled={!states.length}
                          >
                            <option value="">Select State</option>
                            {states.map((state) => (
                              <option key={state.id} value={state.id}>
                                {state.name}
                              </option>
                            ))}
                          </select>
                          <button
                            className="button-with-icon"
                            onClick={handleStateSearch}
                          >
                            Go
                          </button>
                        </div>
                      </div>

                      <div className="attention-retailers-form-group">
                        <label>Search by ZIP CODE</label>
                        <div className="attention-retailers-input-group">
                          <input
                            type="text"
                            className="form-control"
                            placeholder="Zip Code"
                            value={zipCode}
                            onChange={(e) => setZipCode(e.target.value)}
                          />
                          <button
                            className="button-with-icon"
                            onClick={handleZipSearch}
                          >
                            Go
                          </button>
                        </div>
                      </div>
                    </div> */}
                  </div>

                  <div className="col-md-12 mt-5">
                    <div className="row">
                      <div className="col-lg-9">
                        <div className="store-form-head">
                          <h2>
                            Is there a retail location that you wish carried
                            Apricot Power's fine line of products? Let us know
                            about it...
                          </h2>
                        </div>
                      </div>
                      <div className="col-md-12">
                        <div className="store-form">
                          <input
                            type="text"
                            className="form-control"
                            name="store_name"
                            placeholder="Store Name"
                            value={formData.store_name}
                            onChange={HandleChange}
                          />
                          <input
                            type="text"
                            className="form-control"
                            value={formData.location}
                            name="location"
                            onChange={HandleChange}
                            placeholder="Location & Contact Information"
                          />
                          <button
                            className="button-with-icon"
                            onClick={HandleSubmit}
                          >
                            Submit
                          </button>
                        </div>
                        {isSuccess && (
                          <SuccesMessage
                            title="Retailer Location Added!"
                            subTitle="The retailer location has been added. You can continue updating or adding more locations anytime."
                            setIsSuccess={setIsSuccess}
                          />
                        )}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div className="col-xxl-3 col-xl-4 col-lg-5">
                <div className="attention-retailers-aside">
                  <div className="attention-retailers-aside-head">
                    <h2
                      onClick={() => navigate("/wholesale-application")}
                      className="cursor"
                    >
                      JOIN THE MOVEMENT!
                    </h2>
                    <img
                      src={
                        "https://api.feefo.com/api/logo?merchantidentifier=apricot-power&template=Service-Stars-White-316x80.png&since=all"
                      }
                      alt="Feefo"
                      className="cursor"
                      onClick={() => setshow(true)}
                    />
                  </div>
                  {/* <div className="  gap-2 p-3 border d-flex justify-content-center">
                    <img
                      src={
                        "https://www.apricotpower.com/global/assets/img/blocks/partners/partner1.webp"
                      }
                      alt="partner"
                      className=""
                    />

                    <img
                      src={
                        "https://www.apricotpower.com/global/assets/img/blocks/partners/partner2.webp"
                      }
                      alt="partner"
                      className=""
                    />
                    <img
                      src={
                        "https://www.apricotpower.com/global/assets/img/blocks/partners/partner3.webp"
                      }
                      alt="Refer Friend"
                    />
                  </div> */}

                  <div className="attention-retailers-aside-img">
                    <img
                      src={retailerlocationsideimg}
                      alt="Retailer Location Side"
                      className="img-fluid"
                    />
                  </div>
                </div>
              </div>
            </div>
            {data.length !== 0 && (
              <>
                <div className="row mb-3 ">
                  <div className="col-md-12">
                    <div className="dashboard-table">
                      <CustomTable headers={maleHeaders}>
                        <tbody>
                          {(data?.data || []).map((item, index) => (
                            <tr key={index}>
                              <td>{index + 1}</td>
                              <td className="text-capitalize">
                                {/* <Link  to={"/order-history/order-detail"} className="greenColor">{item?.id}</Link><br/> */}
                                <span>
                                  {item?.first_name} {item?.last_name}
                                </span>
                              </td>
                              <td>
                                <span> {item?.phone} </span>
                              </td>

                              <td>
                                {/* <span>Website: {item?.website_url }</span><br/> */}
                                <Link
                                  to={item?.website_url}
                                  className="greenColor"
                                >
                                  {item?.website_url}
                                </Link>
                                <br />
                              </td>
                              <td>
                                <span>{item?.email} </span>
                              </td>
                            </tr>
                          ))}
                        </tbody>
                      </CustomTable>
                    </div>
                  </div>
                </div>
                <div className="d-flex justify-content-center mt-3">
                  <button
                    className="btn btn-secondary mx-1"
                    onClick={() => handlePageChange(page - 1)}
                    disabled={page === 1}
                  >
                    Previous
                  </button>
                  <span className="mx-2 align-self-center">
                    Page {page} of {totalPages}
                  </span>
                  <button
                    className="btn btn-secondary mx-1"
                    onClick={() => handlePageChange(page + 1)}
                    disabled={page === totalPages}
                  >
                    Next
                  </button>
                </div>
              </>
            )}
            {/* <div className="col-md-12 mt-5">
              <div className="row">
                <div className="col-lg-9">
                  <div className="store-form-head">
                    <h2>
                      Is there a retail location that you wish carried Apricot
                      Power's fine line of products? Let us know about it...
                    </h2>
                  </div>
                </div>
                <div className="col-md-12">
                  <div className="store-form">
                    <input
                      type="text"
                      className="form-control"
                      placeholder="Store Name"
                    />
                    <input
                      type="text"
                      className="form-control"
                      placeholder="Location & Contact Information"
                    />
                    <button className="button-with-icon">Submit</button>
                  </div>
                </div>
              </div>
            </div> */}
          </div>
        </section>

        <WeShipFast data={pagedata?.[1]} />
        <ReviewModal show={show} setshow={setshow} />
      </DefaultLayout>
    </>
  );
};

export default RetailerLocations;
