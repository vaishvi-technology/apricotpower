import { Link, useLocation, useParams } from "react-router-dom";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import {
  FaFacebookF,
  FaTwitter,
  FaPinterestP,
  FaEnvelope,
} from "react-icons/fa";
import { useEffect, useState } from "react";
import "./style.css";
import { getBlogsBySlug } from "../../api/Services/blogsServices";

export default function BlogsDetail() {
  const [data, setData] = useState([]);
  const { slug } = useParams();
  const getSingleBlog = async () => {
    try {
      const response = await getBlogsBySlug(slug);
      setData(response);
    } catch (error) {
      console.log(error);
    }
  };
  useEffect(() => {
    getSingleBlog();
  }, []);
  const iconColors = {
    facebook: "#3b5998",
    twitter: "#1da1f2",
    pinterest: "#bd081c",
    envelope: "#6e6e6e",
  };
  useEffect(() => {
    document.title = data?.blog_title;
  }, [data]);
 
  return (
    <DefaultLayout>
      <InnerBanner boldText={data?.blog_title} small={true} />

      <div className="container blogg my-5 b-page__container">
        <div className="d-flex justify-content-between align-items-center  border-bottom">
          <p
            className="mb-0"
            style={{ color: "#999", fontWeight: "bold", fontSize: "20px" }}
          >
            By Apricot Power
          <p className="blog-post-categories">
            {data?.blog_categories?.map((cat, index) => (
              <Link
                to={`/blogByCategory/${cat?.id}/${cat?.category_name}`}
                key={index}
              >
                {cat?.category_name}
              </Link>
            ))}
          </p>
          </p>

          <div className="d-flex gap-4">
            <a
              href={`https://www.facebook.com/sharer/sharer.php?u=${window.location.href}`}
              className="text-dark"
              style={{ color: iconColors.facebook, fontSize: "1.5rem" }}
              title="Share on Facebook"
              target="_blank"
              rel="noopener noreferrer"
            >
              <FaFacebookF color={iconColors.facebook} />
            </a>

            <a
              href={`https://twitter.com/intent/tweet?url=${
                window.location.href
              }&text=${encodeURIComponent(data?.blog_title || "")}`}
              className="text-dark"
              style={{ color: iconColors.twitter, fontSize: "1.5rem" }}
              title="Share on Twitter"
              target="_blank"
              rel="noopener noreferrer"
            >
              <FaTwitter color={iconColors.twitter} />
            </a>

            <a
              href={`https://pinterest.com/pin/create/button/?url=${
                window.location.href
              }&media=${data?.image}&description=${encodeURIComponent(
                data?.blog_title || ""
              )}`}
              className="text-dark"
              style={{ color: iconColors.pinterest, fontSize: "1.5rem" }}
              title="Share on Pinterest"
              target="_blank"
              rel="noopener noreferrer"
            >
              <FaPinterestP color={iconColors.pinterest} />
            </a>

            <a
              href={`mailto:?subject=${encodeURIComponent(
                data?.blog_title || ""
              )}&body=${window.location.href}`}
              className="text-dark"
              style={{ color: iconColors.envelope, fontSize: "1.5rem" }}
              title="Share via Email"
            >
              <FaEnvelope color={iconColors.envelope} />
            </a>
          </div>
        </div>
        <div className="blogImage">
          <img src={data?.image} />
        </div>
        <div
          className=""
          dangerouslySetInnerHTML={{ __html: data?.body }}
        ></div>
      </div>
    </DefaultLayout>
  );
}
