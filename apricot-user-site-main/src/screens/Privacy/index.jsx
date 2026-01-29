import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import "../../App.css";
import { useEffect, useState } from "react";
import { getCmcPages, getTag } from "../../api/Services/getDynamicData";
import { Helmet } from "react-helmet-async";

export default function Index() {
  const [tag, setTag] = useState([]);
  const [pagedata, setpagedata] = useState({});

  const fetchCmc = async () => {
    try {
      const data = await getCmcPages(41);
      setpagedata(data[0]);
    } catch (error) {
      console.log({ error });
    }
  };
  const fetchTag = async () => {
    try {
      const data = await getTag(12);
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

        <div className="container policy py-5">
          <div dangerouslySetInnerHTML={{ __html: pagedata?.description }} />
        </div>
      </DefaultLayout>
    </>
  );
}
