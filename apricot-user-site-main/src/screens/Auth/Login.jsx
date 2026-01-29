import { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";

import "./style.css";

import { AuthLayout } from "../../components/Layout/AuthLayout";
import CustomButton from "../../components/CustomButton";
import CustomInput from "../../components/CustomInput";
import { base_url } from "../../api";
import InnerBanner from "../../components/InnerBanner";
import { toast } from "react-toastify";
import { GetProfileData } from "../../redux/slices/userSlice";
import { useDispatch } from "react-redux";
import { useContext } from "react";
import { IPInfoContext } from "ip-info-react";
import DefaultLayout from "../../components/DefaultLayout";
import { useCookies } from "react-cookie";

const AdminLogin = () => {
  const [cookies, setCookie] = useCookies(["role"]);

  const userInfo = useContext(IPInfoContext);
  const navigate = useNavigate();
  const dispatch = useDispatch();

  const [formData, setFormData] = useState({
    email: "",
    password: "",
  });

  useEffect(() => {
    document.title = "Apricot Power | Login";
  }, []);

  const handleSubmit = async (event) => {
    event.preventDefault();

    document.querySelector(".loaderBox").classList.remove("d-none");

    const apiUrl = `${base_url}/login`;
    const upgradeFormdata = { ...formData, ip: userInfo.ip };

    try {
      const response = await fetch(apiUrl, {
        method: "POST",
        headers: {
          "content-type": "application/json",
        },
        body: JSON.stringify(upgradeFormdata),
      });

      if (response.ok) {
        const responseData = await response.json();
        // toast.success(responseData?.message);
        localStorage.setItem("login", responseData?.token);
        document.querySelector(".loaderBox").classList.add("d-none");
        setCookie("role", responseData?.role, { path: "/" });

        // dispatch(GetProfileData(responseData?.token));
        if (responseData?.token) {
          navigate("/");
          window.location.reload()
        }
      } else {
        document.querySelector(".loaderBox").classList.add("d-none");
        // alert("Invalid Credentials");

          const responseData = await response.json();
        toast.error(responseData?.message);

        console.error("Login failed");
      }
    } catch (error) {
      document.querySelector(".loaderBox").classList.add("d-none");
      console.error("Error:", error);
    }
  };

  return (
    <>
      <DefaultLayout>
        <InnerBanner className="refer-a-friend" boldText1="Login" />
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
          <CustomInput
            label="Password"
            required
            id="pass"
            type="password"
            placeholder="Enter Password"
            labelClass="mainLabel"
            inputClass="mainInput"
            onChange={(event) => {
              setFormData({ ...formData, password: event.target.value });
            }}
          />
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
            <Link to={'/forget-password'} className='text-dark text-decoration-underline'>Forget Password?</Link>
          </div>
          <div className="mt-4 text-center">
            <CustomButton
              variant="primaryButton"
              className="bg-success"
              text="Login"
              type="submit"
            />
          </div>
          <p className="text-center mt-3">
            If you don&#39;t have account?{" "}
            <Link
              to={"/register"}
              className="text-dark text-decoration-underline"
            >
              Sign up now!
            </Link>
          </p>
        </form>
      </DefaultLayout>
    </>
  );
};

export default AdminLogin;
