/* eslint-disable react/prop-types */
import CustomModal from "../../components/CustomModal";

export default function TestimonialModal({ setShow, show, data }) {
  return (
    <CustomModal
      show={show}
      close={() => {
        setShow(false);
      }}
      size="lg"
    >
      <div className="text-center">
        {data?.type === "audio" && (
          <audio controls style={{ width: "100%" }}>
            <source src={data.url} type="audio/mp3" />
            Your browser does not support the audio element.
          </audio>
        )}

        {data?.type === "video" && (
          <video controls style={{ width: "100%" }}>
            <source src={data.url} type="video/mp4" />
            Your browser does not support the video element.
          </video>
        )}
      </div>
    </CustomModal>
  );
}
