 import React, { useEffect } from 'react';
import { useNavigate } from 'react-router';
import { base_url } from '../api';

export const ProtectedRoutes = (props) => {
  const { Components } = props;
  const navigate = useNavigate();

  const loginToken = localStorage.getItem('login');
  const CopyPaste = () => {
    useEffect(() => {
      const ctrlKey = 17;
      const cmdKey = 91;
      const vKey = 86;
      const cKey = 67;
  
      let ctrlDown = false;
  
      const handleKeyDown = (e) => {
        if (e.keyCode === ctrlKey || e.keyCode === cmdKey) ctrlDown = true;
      };
  
      const handleKeyUp = (e) => {
        if (e.keyCode === ctrlKey || e.keyCode === cmdKey) ctrlDown = false;
      };
  
      const handleKeyPress = (e) => {
        if (ctrlDown && (e.keyCode === vKey || e.keyCode === cKey)) e.preventDefault();
      };
  
      document.addEventListener('keydown', handleKeyDown);
      document.addEventListener('keyup', handleKeyUp);
  
      const elements = document.querySelectorAll("body");
  
      elements.forEach((element) => {
        element.addEventListener('keydown', handleKeyPress);
      });
  
      return () => {
        document.removeEventListener('keydown', handleKeyDown);
        document.removeEventListener('keyup', handleKeyUp);
  
        elements.forEach((element) => {
          element.removeEventListener('keydown', handleKeyPress);
        });
      };
    }, []); 
  };

  // const apiStatus = () => {
  //   const formDataMethod = new FormData();
  //   formDataMethod.append('token', loginToken);
  //   document.querySelector('.loaderBox').classList.remove("d-none")
 
  //   fetch(`${base_url}/public/api/auth/check-token`, {
  //     method: 'POST',
  //     headers: {
  //       'Accept': 'application/json',
  //     },
  //     body: formDataMethod
  //   })
  //     .then((response) => response.json())
  //     .then((data) => {
  //       if (data?.status === false) {
  //         localStorage.removeItem('login');
  //         navigate('/');
  //       }
  //       document.querySelector('.loaderBox').classList.add("d-none");
  //     })
  //     .catch(() => {
  //       document.querySelector('.loaderBox').classList.add("d-none");
  //     });
  // };

  useEffect(() => {
    let login = localStorage.getItem('login');
    if (!login) {
      navigate('/login');
    }

    // apiStatus();
    

  }, []);  

  return (
    <>
      <Components />
    </>
  );
};
