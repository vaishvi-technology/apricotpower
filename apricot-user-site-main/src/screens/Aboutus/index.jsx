import React, { useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { getCmcPages, getTag } from "../../api/Services/getDynamicData";
import { Helmet } from "react-helmet-async";

const Aboutus = () => {
  // const token = localStorage.getItem("login");
  const [data, setData] = React.useState([]);

  const [tag, setTag] = useState([]);
  const fetchCmc = async () => {
    try {
      const data = await getCmcPages(44);
      setData(data?.[0]);
    } catch (error) {
      console.log({ error });
    }
  };
  const fetchTag = async () => {
    try {
      const data = await getTag(44);
      setTag(data?.[0]);
    } catch (error) {
      console.log({ error });
    }
  };
  useEffect(() => {
    fetchCmc();
    fetchTag();
  }, []);

  return (
    <>
      <Helmet>
        <title>{tag.PageMetaTitle}</title>

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
        <InnerBanner boldText="About Us" />

        <section className="blogs-sec">
          <div className="container">
            <div className="row">
              <div dangerouslySetInnerHTML={{ __html: data?.description }} />
            </div>
          </div>
        </section>
      </DefaultLayout>
    </>
  );
};

export default Aboutus;
