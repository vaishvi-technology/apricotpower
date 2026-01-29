import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import App from './App.jsx'
import IPInfo from "ip-info-react";
createRoot(document.getElementById('root')).render(

  <StrictMode>
    <IPInfo>


    <App />
    <div className='loaderBox d-none'>
      <div className="custom-loader"></div>
    </div>
        </IPInfo>


  </StrictMode>,
)
