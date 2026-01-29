import React from "react";
import Header from "./Header";
import Footer from "./Footer";
import TopHeader from "./Header/TopHeader";

const DefaultLayout = (props) => {
  return (
    <>
        <TopHeader/>
      <div className="d-flex flex-column" style={{minHeight: '100vh'}}>
        <Header />
          <div className="flex-grow-1">
              {props.children}
          </div>
        <Footer />
      </div>
    </>
  );
};

export default DefaultLayout;
