/* eslint-disable react-hooks/exhaustive-deps */
import React, { useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { base_url } from "../../api";
import { Image } from "react-bootstrap";
import { Link, useNavigate } from "react-router-dom";
import BlogsSideBar from "./BlogsSideBar";
import CustomButton from "../../components/CustomButton";
import { BsFilterLeft } from "react-icons/bs";

const Blogs = () => {
  const token = localStorage.getItem("login");
  const [data, setData] = useState([]);
  const [category, setCategory] = useState([]);
  const [showOfCanvas, setShowOfCanvas] = useState(false);
  const [formData, setFormData] = useState({
    tags: [],
    sortOrder: "",
  });

  const navigate = useNavigate();

  useEffect(() => {
    document.title = "Blogs | Apricot Power";
    fetchBlogs();
    fetchBlogsCategory();
  }, []);

  const fetchBlogs = async (filters = {}) => {
    document.querySelector(".loaderBox").classList.remove("d-none");
    try {
      const params = new URLSearchParams();

      if (filters.tags?.length) {
        params.append("category_slug", filters.tags.join(","));
      }
      if (filters.sortOrder) {
        params.append("sort", filters.sortOrder);
      }

      const response = await fetch(`${base_url}/blog?${params.toString()}`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      });

      const jsonConvert = await response.json();
      setData(jsonConvert || []);
      document.querySelector(".loaderBox").classList.add("d-none");
    } catch (err) {
      console.log(err);
      document.querySelector(".loaderBox").classList.add("d-none");
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
    } catch (err) {
      console.log(err);
    }
  };

  const handleCloseOfCanvas = () => setShowOfCanvas(false);
  const handleShowOfCanvas = () => setShowOfCanvas(true);

  const applyFilters = () => {
    fetchBlogs(formData);
    handleCloseOfCanvas();
  };

  return (
    <DefaultLayout>
      <InnerBanner boldText="blogs" />
      <section className="blogs-sec">
        <BlogsSideBar
          handleCloseOfCanvas={handleCloseOfCanvas}
          handleShowOfCanvas={handleShowOfCanvas}
          showOfCanvas={showOfCanvas}
          setFormData={setFormData}
          formData={formData}
          category={category}
          applyFilters={applyFilters}
        />

        <div className="container">
          <CustomButton
            text={<BsFilterLeft size={30} />}
            variant={"primaryButton"}
            onClick={() => handleShowOfCanvas()}
          />

          <div className="row mt-3">
            {data.map((item, index) => (
              <div
                className="col-xl-4 col-lg-6 col-md-6 mb-5 cursor-pointer"
                key={index}
              >
                <div className="blog-post">
                  <div
                    className="blog-post-img"
                    onClick={() => navigate(`/blogs/${item?.slug}`)}
                  >
                    <Image
                      style={{ objectFit: "cover" }}
                      src={item?.image}
                      width={300}
                      height={400}
                      alt="Blog Img"
                    />
                  </div>
                  <div className="blog-post-content">
                    <h3 onClick={() => navigate(`/blogs/${item?.slug}`)} style={{cursor:"pointer"}}>
                      {item?.blog_title}
                    </h3>
                    <div className="blog-post-by">
                      By Apricot Power{" "}
                      <a href="javascript:;" className="blog-post-author">
                        {item?.date}
                      </a>
                    </div>
                    <div className="blog-post-categories">
                      {item?.blog_categories.map((cat, i) => (
                        <Link
                          to={`/blogByCategory/${cat?.id}/${cat?.category_name}`}
                          key={i}
                        >
                          {cat?.category_name}
                        </Link>
                      ))}
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>
    </DefaultLayout>
  );
};

export default Blogs;
