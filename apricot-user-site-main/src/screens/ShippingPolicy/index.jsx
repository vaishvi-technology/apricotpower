/* eslint-disable react/no-unescaped-entities */
import { useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { getCmcPages, getTag } from "../../api/Services/getDynamicData";
import { Helmet } from "react-helmet-async";

export default function Index() {
  const [tag, setTag] = useState([]);
  const [pagedata, setpagedata] = useState({});

  const fetchCmc = async () => {
    try {
      const data = await getCmcPages(42);
      setpagedata(data[0]);
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
      const data = await getTag(42);
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
        <InnerBanner boldText={pagedata?.title} />
        <div className="container policy my-5">
          <div className="b-page__container">
            <div className="b-page__content -type_single -type_noTitle">
              <div
                dangerouslySetInnerHTML={{ __html: pagedata?.description }}
              />
            </div>
          </div>
        </div>
      </DefaultLayout>
    </>
  );
}
