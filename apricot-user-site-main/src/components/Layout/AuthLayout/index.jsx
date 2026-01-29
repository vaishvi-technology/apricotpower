
import { Link } from "react-router-dom";

import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faLeftLong } from "@fortawesome/free-solid-svg-icons";

import "./style.css";
import Header from "../../DefaultLayout/Header";
import Footer from "../../DefaultLayout/Footer";
import TopHeader from "../../DefaultLayout/Header/TopHeader";

export const AuthLayout = (props) => {
    return (
        <>
        <TopHeader/>
            <div className="d-flex flex-column" style={{ minHeight: '100vh' }}>
                <Header />
                <section className="authBg">
                    <div className='container'>
                        <div className="row g-0">
                            <div className="col-lg-6 authBox m-auto">
                                <div className="authFormWrapper">
                                    <div className="authForm">
                                       
                                        {props?.children}
                                        {props?.backOption &&
                                            <div className="text-center mt-4">
                                                <Link to={'/login'} className='grayColor text-decoration-none fw-bold'><FontAwesomeIcon icon={faLeftLong} className='primaryColor me-2' />Back To <span class="text-theme-primary"> Login</span> </Link>
                                            </div>
                                        }
                                    </div>
                                </div>
                            </div>
                            {/* <div className="col-lg-6 d-none d-lg-block">
                            <div className='authImage'>
                                <img src={authImage} alt="authImage" className="h-100" />
                            </div>
                        </div> */}
                        </div>
                    </div>
                </section>
                <Footer />
            </div>

        </>
    )
}
