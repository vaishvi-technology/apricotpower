import { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";

import "./style.css";

import { AuthLayout } from "../../components/Layout/AuthLayout";
import CustomButton from "../../components/CustomButton";
import CustomInput from "../../components/CustomInput";
import { base_url } from "../../api";
import InnerBanner from "../../components/InnerBanner";
import { toast } from "react-toastify";
import DefaultLayout from "../../components/DefaultLayout";
import { SelectBox } from "../../components/CustomSelect";

const Register = () => {
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    email: "",
    password: "",
    confirm_password: "",
    first_name: "",
    last_name: "",
    specify_other: "",
    how_to_know: "",
    subscription: false,
    privacy_policy: false,
    receive_update: false,
  });

  const handleChange = (event) => {
    const { name, type, value, checked } = event.target;
    setFormData((prevData) => ({
      ...prevData,
      [name]: type === "checkbox" ? checked : value,
    }));
  };

  useEffect(() => {
    document.title = "Register | Apricot Power ";
  }, []);

  const handleSubmit = async (event) => {
    event.preventDefault();
    if (formData.privacy_policy === false) {
      toast.error("Please accept the terms and condition");
      return;
    }
    // if (formData.receive_update === false) {
    //   toast.error("Would you like to receive is required");
    //   return;
    // }

    if (formData?.password === formData?.confirm_password) {
      document.querySelector(".loaderBox").classList.remove("d-none");

      const apiUrl = `${base_url}/user/auth/register`;

      try {
        const response = await fetch(apiUrl, {
          method: "POST",
          headers: {
            "content-type": "application/json",
          },
          body: JSON.stringify(formData),
        });
        const responseData = await response.json();

        if (response.ok) {
          // localStorage.setItem('login', responseData?.token);
          document.querySelector(".loaderBox").classList.add("d-none");
          // alert(responseData?.message)
          // toast.success(responseData?.message);

          if (responseData) {
            navigate("/login");
          }
        } else {
          document.querySelector(".loaderBox").classList.add("d-none");
          toast.error(responseData.message);
          console.error("Login failed");
        }
      } catch (error) {
        document.querySelector(".loaderBox").classList.add("d-none");
        console.error("Error:", error);
      }
    } else {
      alert("password and confirm password should be same.");
    }
  };
  const options = [
    { id: "Family or Friend", name: "Family or Friend" },
    { id: "Doctor or Clinic", name: "Doctor or Clinic" },
    { id: "Search Engine", name: "Search Engine" },
    { id: "Internet Article", name: "Internet Article" },
    { id: "Advertisement", name: "Advertisement" },
    { id: "Facebook", name: "Facebook" },
    { id: "Natural News", name: "Natural News" },
    { id: "Book", name: "Book" },
    { id: "Email or Newsletter", name: "Email or Newsletter" },
    { id: "Church", name: "Church" },
    { id: "Unfiltered News", name: "Unfiltered News" },
    { id: "Event, Expo or Tadeshow", name: "Event, Expo or Tadeshow" },
    { id: "other", name: "Other..." },
  ];

  return (
    <>
      <DefaultLayout>
        <InnerBanner
          className="refer-a-friend"
          boldText1="Sign Up"
          // lightText="To Apricot Power & You Both Save!"
        />
        <form onSubmit={handleSubmit} className="center-class">
          <CustomInput
            label="First Name"
            required
            id="fname"
            type="text"
            name="first_name"
            placeholder="Enter Your First Name"
            labelClass="mainLabel"
            inputClass="mainInput"
            onChange={handleChange}
          />

          <CustomInput
            label="Last Name"
            required
            id="lname"
            type="text"
            name="last_name"
            placeholder="Enter Your Last Name"
            labelClass="mainLabel"
            inputClass="mainInput"
            onChange={handleChange}
          />

          <CustomInput
            label="Email Address"
            required
            id="userEmail"
            type="email"
            name="email"
            placeholder="Enter Your Email Address"
            labelClass="mainLabel"
            inputClass="mainInput"
            onChange={handleChange}
          />
          <CustomInput
            label="Password"
            required
            id="pass"
            type="password"
            name="password"
            placeholder="Enter Password"
            labelClass="mainLabel"
            inputClass="mainInput"
            onChange={handleChange}
          />

          <CustomInput
            label="Confirm Password"
            required
            id="cpass"
            type="password"
            name="confirm_password"
            placeholder="Enter Confirm Password"
            labelClass="mainLabel"
            inputClass="mainInput"
            onChange={handleChange}
          />

          <SelectBox
            selectClass="mainInput"
            name="how_to_know"
            required
            label="How Did You Hear About Us?	"
            value={formData.how_to_know}
            option={options}
            onChange={handleChange}
          />
          {formData.how_to_know == "other" && (
            <CustomInput
        
              type="text"
              name="specify_other"
              placeholder="Specify other"
              labelClass="mainLabel"
              value={formData.specify_other}
              inputClass="mainInput"
              onChange={handleChange}
            />
          )}
          <div className="d-flex align-items-baseline justify-content-between mt-1">
            <div className="checkBox">
              <input
                type="checkbox"
                name="rememberMe"
                id="rememberMe"
                className="me-1"
              />
              <label htmlFor="rememberMe" className="fw-semibold">
                Remember Me
              </label>
            </div>
          </div>
          <div className="d-flex align-items-start gap-2 mt-1">
            <input
              type="checkbox"
              name="privacy_policy"
              id="privacy_policy"
              value="agree"
              className="mt-2"
              checked={formData.privacy_policy || false}
              onChange={handleChange}
            />
            <label htmlFor="agreePrivacy" className="fw-semibold">
              <b>
                I have read and understand{" "}
                <Link to="/privacy" style={{ color: "#7cbf3d" }}>
                  Apricot Power&#39;s privacy policy
                </Link>
              </b>{" "}
              which details personal information collected, why and how it is
              used, and the rights I have over my data.{" "}
              <b>(Must be checked to continue.)</b>
            </label>
          </div>
          <div className="d-flex align-items-start gap-2 mt-1">
            <input
              type="checkbox"
              name="receive_update"
              id="receive_update"
              value="agree"
              className="mt-1"
              checked={formData.receive_update || false}
              onChange={handleChange}
            />
            <label htmlFor="agreePrivacy" className="fw-semibold">
              Would you like to receive updates about your order, specials and
              more by text?
            </label>
          </div>

          <div className="d-flex align-items-start gap-2 mt-2">
            <input
              type="checkbox"
              name="subscription"
              id="subscription"
              value="subscribe"
              className="mt-2"
              checked={formData.subscription || false}
              onChange={handleChange}
            />
            <label htmlFor="subscribeNewsletter" className="fw-semibold">
              Yes! Subscribe me to the Apricot Power deals list for exclusive
              discounts and health tips! Emails sent approximately once per
              week, unsubscribe at any time{" "}
              <Link style={{ color: "#7cbf3d" }} to="/privacy">
                (read our privacy policy)
              </Link>
              .
            </label>
          </div>

          <div className="mt-4 text-center">
            <CustomButton
              variant="primaryButton"
              className="bg-success"
              text="Register"
              type="submit"
            />
          </div>
        </form>
      </DefaultLayout>
    </>
  );
};

export default Register;
