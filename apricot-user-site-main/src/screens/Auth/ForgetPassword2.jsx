import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

import './style.css';
import { AuthLayout } from '../../components/Layout/AuthLayout';
import CustomInput from '../../components/CustomInput';
import CustomButton from '../../components/CustomButton';
import DefaultLayout from '../../components/DefaultLayout';
import InnerBanner from '../../components/InnerBanner';
import { baseUrl } from '../../api/api';

const ForgetPassword2 = () => {
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    code: '',
    password: '',
    confirmPassword: ''
  });
  const [validate, setValidate] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    document.title = 'Apricot Power| Password Recovery';
  }, []);

  // Handle form submission (reset password)
const handleClick = async (event) => {
  event.preventDefault();

  // Validate that password and confirmPassword match
  if (formData.password !== formData.confirmPassword) {
    setValidate(true);
    setError("Passwords do not match");
    return;
  }

  const formDataMethod = new FormData();
  const email = localStorage.getItem('email');

  // Append form data to FormData object
  formDataMethod.append('otp', formData?.code);
  formDataMethod.append('email', email);
  formDataMethod.append('password', formData.password);
  formDataMethod.append('confirmPassword', formData.confirmPassword);

  document.querySelector('.loaderBox').classList.remove('d-none'); // Show loader

  const apiUrl = `${baseUrl}/password/reset`;

  try {
    const response = await fetch(apiUrl, {
      method: 'POST',
      body: formDataMethod,
    });

    if (response.ok) {
      const responseData = await response.json();  // Correct placement for JSON parsing

      // Hide loader after receiving response
      document.querySelector('.loaderBox').classList.add('d-none'); 

      // Handle successful response
      if (responseData.message) {
        
        navigate('/login');
      } else {
        // Failure: Display error message and log the issue
        setValidate(true);  // Set validation to true to show error message
        setError(responseData.message || "Reset failed. Please try again.");
        console.error('Reset failed', responseData);
      }
    } else {
      // Hide loader if response is not OK
      document.querySelector('.loaderBox').classList.add('d-none');
      alert('Failed to verify OTP');
      console.error('Verification failed');
    }
  } catch (error) {
    // Hide loader and handle unexpected errors
    document.querySelector('.loaderBox').classList.add('d-none');
    console.error('Error:', error);
  }
};


  return (
    <>
      <DefaultLayout>
        <InnerBanner className="refer-a-friend" boldText1="Verification Code" />
        <form onSubmit={handleClick} className="center-class">
          <div className="inputWrapper">
            <label htmlFor="verificationCode" className="mainLabel">
              Verification Code<span>*</span>
            </label>
          </div>

          <div className="verification-box flex-grow-1 flex-column gap-0">
            <CustomInput
              required
              id="verificationCode"
              type="number"
              placeholder='Enter Code'
              labelClass="mainLabel"
              inputClass="mainInput"
              onChange={(event) => {
                setFormData({ ...formData, code: event.target.value });
              }}
            />
          </div>

          {/* Password Field */}
          <div className="inputWrapper">
            <label htmlFor="password" className="mainLabel">
              New Password<span>*</span>
            </label>
          </div>

          <CustomInput
            required
            id="password"
            type="password"
            labelClass="mainLabel"
            inputClass="mainInput"
            placeholder="Enter New Password"
            onChange={(event) => {
              setFormData({ ...formData, password: event.target.value });
            }}
          />

          {/* Confirm Password Field */}
          <div className="inputWrapper">
            <label htmlFor="confirmPassword" className="mainLabel">
              Confirm Password<span>*</span>
            </label>
          </div>

          <CustomInput
            required
            id="confirmPassword"
            type="password"
            labelClass="mainLabel"
            inputClass="mainInput"
            placeholder="Confirm Your Password"
            onChange={(event) => {
              setFormData({ ...formData, confirmPassword: event.target.value });
            }}
          />

          {/* Display validation errors */}
          {validate && <p className="text-danger">{error}</p>}

          <div className="mt-4 text-center">
            <CustomButton variant="primaryButton" text="Continue" type="submit" />
          </div>
        </form>
      </DefaultLayout>
    </>
  );
};

export default ForgetPassword2;
