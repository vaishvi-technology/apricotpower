import React, { useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { getVideo } from "../../api/Services/videoLibraryservices";

const VideoLibrary = () => {
  const [data, setData] = useState([]);

  const fetchVideo = async () => {
    try {
      const response = await getVideo();
      setData(response || []);
    } catch (error) {
      console.log(error);
    }
  };

  useEffect(() => {
    fetchVideo();
  }, []);

  useEffect(() => {
    document.title = "Video Library | Apricot Power";
  }, []);
  useEffect(() => {
    if (window.location.hash) {
      const id = window.location.hash.replace("#", "");
      setTimeout(() => {
        const element = document.getElementById(id);
        if (element) {
          element.scrollIntoView({ behavior: "smooth" });
        }
      }, 300);
    }
  }, [data]);
  const getYouTubeEmbedUrl = (url) => {
    let videoId = "";

    const fullMatch = url.match(/v=([a-zA-Z0-9_-]{11})/);
    const shortMatch = url.match(/youtu\.be\/([a-zA-Z0-9_-]{11})/);

    if (fullMatch) {
      videoId = fullMatch[1];
    } else if (shortMatch) {
      videoId = shortMatch[1];
    }

    return videoId ? `https://www.youtube.com/embed/${videoId}` : "";
  };

  return (
    <DefaultLayout>
      <InnerBanner boldText="Video Library" />

      <div className="vedio-library container py-5">
        {data?.map((item, index) => (
          <React.Fragment key={index + 1}>
            <div className="row mb-4" id={item?.id}>
              <div className="col-12 text-center">
                <h1 className="fw-bold secondary-color">{item?.title}</h1>
              </div>
            </div>

            <div className="row justify-content-center">
  <div
    key={index}
    className="col-md-6 col-lg-4 mb-4 d-flex flex-column align-items-center"
  >
    {item?.link?.includes("youtube.com") || item?.link?.includes("youtu.be") ? (
      <iframe
        width="100%"
        height="315"
        src={getYouTubeEmbedUrl(item.link)}
        title="YouTube video"
        frameBorder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowFullScreen
      ></iframe>
    ) : (
      <video width="100%" height="315" controls>
        <source src={item.link} type="video/mp4" />
        Your browser does not support the video tag.
      </video>
    )}
  </div>
</div>
          </React.Fragment>
        ))}
      </div>
    </DefaultLayout>
  );
};

export default VideoLibrary;
