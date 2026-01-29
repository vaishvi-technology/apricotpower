import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";

import "./style.css";

import { AuthLayout } from "../../components/Layout/AuthLayout";
import CustomInput from "../../components/CustomInput";
import CustomButton from "../../components/CustomButton";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { baseUrl } from "../../api/api";

const ForgetPassword = () => {
  const navigate = useNavigate();

  const [formData, setFormData] = useState({});
  const [validate, setValidate] = useState(false);
  const [error, setError] = useState();

  useEffect(() => {
    document.title = "Apricot Power| Password Recovery";
  }, []);

  // const handleSubmit = (event) => {
  //     event.preventDefault();

  //     const formData = new FormData();
  //     formData.append('email', formData.email);

  //     document.querySelector('.loaderBox').classList.remove("d-none");

  //     const apiUrl = `${process.env.REACT_APP_API_URL}/public/api/forgot_password`;

  //     fetch(apiUrl, {
  //         method: 'POST',
  //         body: formData
  //     })
  //     .then(response => {
  //         if (response.ok) {
  //             return response.json();
  //         } else {
  //             document.querySelector('.loaderBox').classList.add("d-none");
  //             throw new Error('Network response was not ok.');
  //         }
  //     })
  //     .then(data => {
  //         localStorage.setItem('email', data.data.email);
  //         document.querySelector('.loaderBox').classList.add("d-none");
  //         if (data.status === true) {
  //             setValidate(false);
  //             navigate('/forget-password2');
  //         } else {
  //             setValidate(true);
  //             setError(data.message);
  //             console.log(data);
  //         }
  //     })
  //     .catch(error => {
  //         document.querySelector('.loaderBox').classList.add("d-none");
  //         console.error('Error:', error);
  //         alert('Invalid Credentials');
  //     });
  // };

  const handleSubmit = (event) => {
    event.preventDefault();

    setValidate(false);

    const formDataMethod = new FormData();
    formDataMethod.append("email", formData.email);

    document.querySelector(".loaderBox").classList.remove("d-none");

    const apiUrl = `${baseUrl}/password/forget`;

    fetch(apiUrl, {
      method: "POST",
      body: formDataMethod,
    })
      .then(async (response) => {
        document.querySelector(".loaderBox").classList.add("d-none");

        const data = await response.json();

        // response.ok = true (200)
        // response.ok = false (400/401/422 etc)
        return { ok: response.ok, data };
      })
      .then(({ data }) => {
        if (data.status == true) {
          navigate("/forget-password2");
          localStorage.setItem("email", formData?.email);
          setValidate(false);
        } else {
          setValidate(true);
          setError(data.message || "Something went wrong");
          console.log("API Error:", data);
        }
      })
      .catch((error) => {
        document.querySelector(".loaderBox").classList.add("d-none");
        console.error("Network Error:", error);
      });
  };

  const handleClick = (e) => {
    e.preventDefault();
    navigate("/forget-password2");
  };

  return (
    <>
      <DefaultLayout>
        <InnerBanner className="refer-a-friend" boldText1="Forget Password" />
        <form onSubmit={handleSubmit} className="center-class">
          <CustomInput
            label="Email Address"
            required
            id="userEmail"
            type="email"
            placeholder="Enter Your Email Address"
            labelClass="mainLabel"
            inputClass="mainInput"
            onChange={(event) => {
              setFormData({ ...formData, email: event.target.value });
            }}
          />
          {validate && <p className="text-danger">{error}</p>}
          <div className="mt-4 text-center">
            <CustomButton
              variant="primaryButton"
              text="Continue"
              type="submit"
            />
          </div>
        </form>
      </DefaultLayout>
    </>
  );
};

export default ForgetPassword;
