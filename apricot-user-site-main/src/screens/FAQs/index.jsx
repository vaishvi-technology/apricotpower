import React, { useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";

import Accordion from "react-bootstrap/Accordion";
import { base_url } from "../../api";
import { getCmcPages, getTag } from "../../api/Services/getDynamicData";
import { Helmet } from "react-helmet-async";

let faqAccordianDataDescription =
  "You will need to determine the best amount for yourself. Start with one apricot seed an hour and see how you do. If you notice any unwanted side effects like dizziness, headache or upset stomach, then you are consuming too many seeds too fast. But remember to never over consume apricot seeds, always start with a small amount and slowly increase that amount if you feel you want to.";

const FAQs = () => {
  const token = localStorage.getItem("login");
    const [tag, setTag] = useState([]);
  const [data, setData] = React.useState([]);
  const [pagedata, setpagedata] = React.useState({});

  const fetchFAQS = async () => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");

      const response = await fetch(base_url + "/admin/faq/", {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });
      document.querySelector(".loaderBox").classList.add("d-none");

      const jsonConvert = await response.json();
      setData(jsonConvert || []);
      console.log("FAQS======================>", jsonConvert);
    } catch (err) {
      document.querySelector(".loaderBox").classList.add("d-none");
    }
  };

  const fetchCmc = async () => {
    try {
      const data = await getCmcPages(7);
      setpagedata(data);
    } catch (error) {
      console.log({ error });
    }
  };
  const fetchTag = async () => {
    try {
      const data = await getTag(7);
      setTag(data?.[0]);
    } catch (error) {
      console.log({ error });
    }
  };

  useEffect(() => {
    fetchFAQS();
    fetchCmc();
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
      <InnerBanner lightText="Common" boldText="QUESTIONS (FAQs)" />
      <section className="faq-sec">
        <div className="container">
          {Array.isArray(data) && (
            <div className="container">
              <div className="row">
                <div className="col-lg-6">
                  <div className="faq-accordians faq-accordians-col-1">
                    <Accordion defaultActiveKey="0">
                      {data.slice(0, data.length / 2).map((item, index) => (
                        <Accordion.Item eventKey={item.id} key={index}>
                          <Accordion.Header>
                            <span>{item.id}.</span> {item?.question}
                          </Accordion.Header>
                          <Accordion.Body>
                            {" "}
                            <div
                              className=""
                              dangerouslySetInnerHTML={{ __html: item?.answer }}
                            ></div>
                          </Accordion.Body>
                        </Accordion.Item>
                      ))}
                    </Accordion>
                  </div>
                </div>
                <div className="col-lg-6">
                  <div className="faq-accordians faq-accordians-col-2">
                    <Accordion defaultActiveKey="0">
                      {data.slice(data.length / 2).map((item, index) => (
                        <Accordion.Item eventKey={item.id} key={index}>
                          <Accordion.Header>
                            <span>{item.id}.</span> {item?.question}
                          </Accordion.Header>
                          <Accordion.Body>
                            {" "}
                            <div
                              className=""
                              dangerouslySetInnerHTML={{ __html: item?.answer }}
                            ></div>
                          </Accordion.Body>
                        </Accordion.Item>
                      ))}
                    </Accordion>
                  </div>
                </div>
              </div>
            </div>
          )}
          {/* <div className="row">
            <div className="col-lg-6">
              <div className="faq-accordians faq-accordians-col-1">
                <Accordion defaultActiveKey="0">
                  {(data||[]).slice(0, data.length/2).map((item, index) => (
                    <Accordion.Item eventKey={item.id} key={index}>
                      <Accordion.Header>
                        <span>{item.id}.</span> {item?.question}
                      </Accordion.Header>
                      <Accordion.Body>{item.answer}</Accordion.Body>
                    </Accordion.Item>
                  ))}
                </Accordion>
              </div>
            </div>
            <div className="col-lg-6">
              <div className="faq-accordians faq-accordians-col-2">
                <Accordion defaultActiveKey="0">
                  {(data||[]).slice((data||[]).length/2).map((item, index) => (
                   <Accordion.Item eventKey={item.id} key={index}>
                   <Accordion.Header>
                     <span>{item.id}.</span> {item?.question}
                   </Accordion.Header>
                   <Accordion.Body>{item.answer}</Accordion.Body>
                 </Accordion.Item>
                  ))}
                </Accordion>
              </div>
            </div>
          </div> */}
        </div>
      </section>
    </DefaultLayout>
    </>
  );
};

export default FAQs;
