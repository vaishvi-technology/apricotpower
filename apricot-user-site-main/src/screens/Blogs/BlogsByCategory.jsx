/* eslint-disable react-hooks/exhaustive-deps */
import React, { useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";

import { base_url } from "../../api";
import { Image } from "react-bootstrap";
import { useNavigate, useParams } from "react-router-dom";
import BlogsSideBar from "./BlogsSideBar";
import CustomButton from "../../components/CustomButton";
import { BsFilterLeft } from "react-icons/bs";

const BlogsByCategory = () => {
  const token = localStorage.getItem("login");
  const { id ,name} = useParams();

  const [data, setData] = React.useState([]);
  const [category, setCategory] = React.useState([]);
  const [showOfCanvas, setShowOfCanvas] = useState(false);
  const [formData, setFormData] = useState({
    tags: [],
  });

  const fetchBlogs = async () => {
    try {
      const response = await fetch(base_url + `/blogs/by/category/${id}`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });
      const jsonConvert = await response.json();
      setData(jsonConvert || []);
      console.log("Blogs data", typeof jsonConvert);
    } catch (err) {
      console.log(err);
    }
  };
  const fetchBlogsCategory = async () => {
    try {
      const response = await fetch(base_url + "/blogs/categories/", {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });
      const jsonConvert = await response.json();
      setCategory(jsonConvert || []);
      console.log("Blogs data", typeof jsonConvert);
    } catch (err) {
      console.log(err);
    }
  };
  useEffect(() => {
    fetchBlogs();
    fetchBlogsCategory();
  }, []);
  const navigate = useNavigate();
  useEffect(() => {
    document.title = "Blogs | Apricot Power";
  }, []);
  const handleCloseOfCanvas = () => setShowOfCanvas(false);
  const handleShowOfCanvas = () => setShowOfCanvas(true);

  return (
    <DefaultLayout>
      <InnerBanner boldText={name} />

      <section className="blogs-sec">
   
        <div className="container">
   
          <div className="row mt-3">
            {(data || [])?.map((item, index) => (
              <div
                className="col-xl-4 col-lg-6 col-md-6 mb-5 cursor-pointer"
                key={index}
                onClick={() => navigate(`/blogs/${item?.slug}`)}
              >
                <div className="blog-post">
                  <div className="blog-post-img">
                    <Image
                      style={{ objectFit: "cover" }}
                      src={item?.image}
                      width={300}
                      height={400}
                      alt="Blog Img"
                    />
                  </div>
                  <div className="blog-post-content">
                    <h3>{item?.blog_title}</h3>
                    <div className="blog-post-by">
                      By Apricot Power{" "}
                      <a href="javascript:;" className="blog-post-author">
                        {item?.date}
                      </a>
                    </div>
                    <div className="blog-post-categories">
                      {item?.blog_categories.map((cat, index) => (
                        <a href="javascript:;" key={index}>
                          {cat?.category_name}
                        </a>
                      ))}
                    </div>
                  </div>
                </div>
              </div>
            ))}
            {/* <div className="col-md-4">
              <div className="blog-post">
                <div className="blog-post-img">
                  <img src={blogimg1} alt="Blog Img" />
                </div>
                <div className="blog-post-content">
                    <h3>From Ancient Wisdom to Modern Wellness: The Enduring Relevance of Esiak</h3>
                    <div className="blog-post-by">By <a href="javascript:;" className="blog-post-author">Apricot Power</a></div>
                    <div className="blog-post-categories">
                        <a href="javascript:;">Boosters</a>
                        <a href="javascript:;">Health & Wellness</a>
                    </div>
                </div>
              </div>
            </div> */}
          </div>
        </div>
      </section>
    </DefaultLayout>
  );
};

export default BlogsByCategory;
