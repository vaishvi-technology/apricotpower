import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import "../../App.css";
import { useEffect, useState } from "react";
import { getCmcPages } from "../../api/Services/getDynamicData";

export default function Index() {
  useEffect(() => {
    document.title = "Return Policy | Apricot Power";
  }, []);
  const [pagedata, setpagedata] = useState({});

  const fetchCmc = async () => {
    try {
      const data = await getCmcPages(40);
      setpagedata(data[0]);
    } catch (error) {
      console.log({ error });
    }
  };
  useEffect(() => {
    fetchCmc();
  }, []);

  return (
    <DefaultLayout>
      <InnerBanner boldText={pagedata?.title} />

      <div className="container my-5 policy">
        <div className="b-page__container">
          <div className="b-page__content -type_single -type_noTitle">
            <div className="b-text -type_biggest">
              <div
                dangerouslySetInnerHTML={{ __html: pagedata?.description }}
              />
            </div>
          </div>
        </div>
      </div>
    </DefaultLayout>
  );
}
