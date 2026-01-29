import { useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import LifestyleSection from "./LifeStyle";
import { getLifeStyle } from "../../api/Services/lifeStyleservices";

const LifeStyle = () => {
  const [data, setData] = useState([]);

  const fetchVideo = async () => {
    try {
      const response = await getLifeStyle();
      setData(response || []);
    } catch (error) {
      console.log(error);
    }
  };

  useEffect(() => {
    fetchVideo();
  }, []);

  useEffect(() => {
    document.title = "Life Style | Apricot Power";
  }, []);

  return (
    <DefaultLayout>
      <InnerBanner boldText="Life Style" />
      <LifestyleSection images={data} />
    </DefaultLayout>
  );
};

export default LifeStyle;
