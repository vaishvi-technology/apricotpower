/* eslint-disable react/prop-types */
import CustomModal from "../../components/CustomModal";
import popup from "../../assets/images/popup.jpg";
import { useState } from "react";
import CustomButton from "../../components/CustomButton";
import { logo } from "../../assets/images";
import { base_url } from "../../api";
import { toast } from "react-toastify";

export default function HomePopup({ show, setshow }) {
  const [email, setEmail] = useState("");
  const token = localStorage.getItem("login");
  const [selectedTopic, setSelectedTopic] = useState("");
  const [formData, setFormData] = useState({
    email: "",
    first_name: "",
    last_name: "",
  });
  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");

      const res = await fetch(`${base_url}/news-latter`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });
      document.querySelector(".loaderBox").classList.add("d-none");
      const data = await res.json();
      if (data.success == true) {
        setFormData({
          first_name: "",
          last_name: "",
          email: "",
        });
        HandleCLose()
        // toast.success(data?.message);;
        // fetchaddress();
      } else {
        toast.error(data?.message);
      }
    } catch (err) {
      document.querySelector(".loaderBox").classList.add("d-none");

      console.log("Order failed:", err);
    }
  };
  const HandleCLose = () => {
    setshow(false);
    localStorage.setItem("isSeen", true);
  };
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  return (
    <CustomModal show={show} close={() => HandleCLose()} size="lg" ispopup={true}>
      <div
        className="d-flex "
        style={{
          backgroundImage: `url(${popup})`,
          backgroundSize: "cover",
          backgroundPosition: "center",
          minHeight: "500px",
          flexDirection: "column",
          padding: "2rem",
        }}
      >
        <div className="px-5">
          <img src={logo} width={100} height={50} />
        </div>
        <div className=" p-4 " style={{ maxWidth: "300px", width: "100%" }}>
          <h1 className="fw-bold text-center">GET 20% OFF</h1>
          <h5 className="mb-3 text-center">YOUR FIRST ORDER</h5>

          <p className="mb-2 text-dark fw-bold">
            What would you love to know more about?
          </p>
          <select
            className="form-select mb-3"
            value={selectedTopic}
            required
            onChange={(e) => setSelectedTopic(e.target.value)}
          >
            <option value="">-- Select Topic --</option>
            <option value="Apricot Seeds">
              Learn more about Apricot Seeds
            </option>
            <option value="Health & Wellness">General Health & Wellness</option>
            <option value="Extreme Health">Extreme Health Issues</option>
          </select>

          <input
            type="email"
            name="email"
            className="form-control"
            placeholder="Email"
            value={formData.email}
            onChange={handleChange}
            required
          />
          <div className="mt-2">
            <CustomButton
              className="primaryButton"
              onClick={handleSubmit}
              text={"Submit"}
            />
          </div>
        </div>
      </div>
    </CustomModal>
  );
}
