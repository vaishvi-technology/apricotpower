/* eslint-disable react-hooks/exhaustive-deps */
// import React from 'react'
import DefaultLayout from "../../components/DefaultLayout";
import Banner from "./Banner";
import About from "./About";
import OurProducts from "./OurProducts";
import HotBuys from "./HotBuys";
import FreeShipping from "./FreeShipping";
import Testimoonial from "./Testimoonial";
import { useContext, useEffect, useState } from "react";
import { useLocation, useSearchParams } from "react-router-dom";
import BannerSlider from "../BannerSlider/Banner";
import { getCmcPages, getTag } from "../../api/Services/getDynamicData";
import { Helmet } from "react-helmet-async";
// import HomePopup from "./HomePopup";
import { trackKlaviyoEvent } from "../../utils/klaviyo";
import { CartContext } from "../../Context/CartContext";
import { IPInfoContext } from "ip-info-react";

const Home = () => {
  const { cartItems } = useContext(CartContext);
  const [serahcparams] = useSearchParams();
  const userInfo = useContext(IPInfoContext);
  console.log(userInfo);
  const [data, setData] = useState([]);
  // const [show, setshow] = useState(true);
  const [tag, setTag] = useState([]);
  useEffect(() => {
    if (serahcparams) {
      const referalcode = serahcparams.get("referralCode");
      localStorage.setItem("referalCode", referalcode);
    }
  }, []);
  const fetchCmc = async () => {
    try {
      const data = await getCmcPages(1);
      setData(data);
    } catch (error) {
      console.log({ error });
    }
  };
  const fetchTag = async () => {
    try {
      const data = await getTag(1);
      setTag(data?.[0]);
    } catch (error) {
      console.log({ error });
    }
  };
  useEffect(() => {
    fetchCmc();
    fetchTag();
  }, []);
  // const isSeen = localStorage.getItem("isSeen");
  const { pathname } = useLocation();
  useEffect(() => {
    if (cartItems?.first_name) {
      trackKlaviyoEvent("Active on Site Home Page", cartItems);
    } else {
      trackKlaviyoEvent("Active on Site Home Page");
    }
    // window.scrollTo({ top: 0, left: 0, behavior: "instant" });
  }, [pathname, cartItems]);

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
        <Banner data={data} />

        <OurProducts />
        {/* <ProductBanner /> */}
        <HotBuys />
        <FreeShipping data={data} />
        <BannerSlider />
        <About data={data} />
        <Testimoonial />
        {/* {!isSeen && <HomePopup show={show} setshow={setshow} />} */}
      </DefaultLayout>
    </>
  );
};

export default Home;
