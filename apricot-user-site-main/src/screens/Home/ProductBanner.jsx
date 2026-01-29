import React from "react";
import { productbannerimg1, productbannerimg2 } from "../../assets/images";
import { use } from "react";
import { useNavigate } from "react-router-dom";

const ProductBanner = () => {
  const navigate=useNavigate();
  return (
    <section className="product-banenr-sec">
      <div className="product-banenr-imgDiv">
        <img src={productbannerimg2} alt="product banner" />
        <div className="product-banenr-content">
          <div  className="product-banenr-content-innerDiv">
            <h2>Sale B-17 Combo Packs Save Up to 33%</h2>
            <button
             onClick={()=>{
              navigate("category/42-Combo-Packs")
            }}
             className="product-banenr-content-btn">Shop Now</button>
          </div>
        </div>
      </div>
      <div className="product-banenr-imgDiv">
        <img src={productbannerimg1} alt="product banner" />
        <div className="product-banenr-content product-banenr-content-secondary">
          <div className="product-banenr-content-innerDiv">
            <h2 className="secondary-color">
              Products That Work Great With B-17
            </h2>
            <button
            onClick={()=>{
              navigate("category/44-B17-Boosters")
            }}
            className="product-banenr-content-btn">Shop Now</button>
          </div>
        </div>
      </div>
    </section>
  );
};

export default ProductBanner;
