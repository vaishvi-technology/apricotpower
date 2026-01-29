import React, { useEffect } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { logo } from "../../Assets/images";

const AffiliateMarketing = () => {
   useEffect(() => {
    document.title = "Affiliate Marketing | Apricot Power";
  }, []);
  return (
    <DefaultLayout>
      <InnerBanner lightText="AFFILIATE" boldText="MARKETING" />
      <section className="affiliate-marketing-form-sec">
        <div className="container">
          <div className="row align-items-center">
            <div className="col-lg-6">
              <div className="affiliate-marketing-content">
                <h2><span className="primary-color">Join Our</span> <span className="secondary-color">Affiliate Program</span></h2>
                <h4>Program highlights:</h4>
                <ul className="affiliate-marketing-ul">
                  <li>
                    You’ll earn a 20% commission on the first order of anyone
                    you send our way.
                  </li>
                  <li>
                    From our affiliate dashboard you will be able to keep track
                    of clicks, referred sales and commissions earned.
                  </li>
                  <li>
                    We will give you all the resources you need to succeed
                    including promotional materials and a dedicated affiliate
                    manager.
                  </li>
                  <li>
                    You’ll get commissions paid out monthly via Paypal, check,
                    or account credits.
                  </li>
                </ul>
                <h4>How to get started:</h4>
                <ul className="affiliate-marketing-ul">
                  <li>Sign up using the form on the right.</li>
                  <li>
                    Share your unique affiliate link via email, social media,
                    personal website/blog or text message to drive traffic to
                    our site
                  </li>
                  <li>
                    Get rewarded with a 20% commission on the first order with
                    each referred sale!
                  </li>
                </ul>
                <h4>How to get started:</h4>
                <p>
                  Further Information: To maintain a fair and rewarding program
                  for all affiliates, we strictly prohibit the promotion of our
                  products through coupon sharing websites, and we encourage
                  applicants who are passionate about our products and want to
                  share them with their personal network to join our program.
                </p>
                <p>
                  For questions or further details, please contact us at
                  866-468-7487, or via email at
                  CustomerService@ApricotPower.com.
                </p>
                <p>
                  We reserve the right to review and adjust commission payouts
                  to ensure compliance with program terms and to maintain the
                  program's integrity.
                </p>
              </div>
            </div>
            <div className="col-lg-6">
              <div className="affiliate-marketing-form">
                <div className="affiliate-marketing-form-logo">
                  <img src={logo} alt="Logo" />
                </div>
                <h3>Refer and EarN</h3>

                <div className="formDiv">
                  <div className="row">

                    <div className="col-xl-6 col-lg-12 col-md-6 mb-4">
                      <div class="custom-input">
                        <label for="firstName" class="form-label">
                          First Name
                        </label>
                        <input
                          type="text"
                          class="form-control custom-input-field"
                          id="firstName"
                          placeholder="First Name"
                        />
                      </div>
                    </div>
                    <div className="col-xl-6 col-lg-12 col-md-6 mb-4">
                      <div class="custom-input ">
                        <label for="lastName" class="form-label">
                          Last Name
                        </label>
                        <input
                          type="text"
                          class="form-control custom-input-field"
                          id="lastName"
                          placeholder="Last Name"
                        />
                      </div>
                    </div>

                    <div className="col-md-12 mb-4">
                      <div class="custom-input">
                        <label for="email" class="form-label">
                          Email
                        </label>
                        <input
                          type="text"
                          class="form-control custom-input-field"
                          id="email"
                          placeholder="Email"
                        />
                      </div>
                    </div>

                    <div className="col-xl-6 col-lg-12 col-md-6 mb-4">
                      <div class="custom-input">
                        <label for="choosePassword" class="form-label">
                          Choose Passwrod
                        </label>
                        <input
                          type="password"
                          class="form-control custom-input-field"
                          id="choosePassword"
                          placeholder="Choose Passwrod"
                        />
                      </div>
                    </div>
                    <div className="col-xl-6 col-lg-12 col-md-6 mb-4">
                      <div class="custom-input">
                        <label for="confirmPassword" class="form-label">
                          Confirm Password
                        </label>
                        <input
                          type="text"
                          class="form-control custom-input-field"
                          id="confirmPassword"
                          placeholder="Confirm Password"
                        />
                      </div>
                    </div>

                    <div className="col-xl-6 col-lg-12 col-md-6 mb-4">
                      <div class="custom-input">
                        <label for="phone" class="form-label">
                          Phone
                        </label>
                        <input
                          type="text"
                          class="form-control custom-input-field"
                          id="phone"
                          placeholder="Phone"
                        />
                      </div>
                    </div>
                    <div className="col-xl-6 col-lg-12 col-md-6 mb-4">
                      <div class="custom-input ">
                        <label for="lastName" class="form-label">
                          Payment Preference
                        </label>
                        <select className="form-control custom-input-field">
                            <option value="Option1">Option1</option>
                        </select>
                        {/* <input
                          type="text"
                          class="form-control custom-input-field"
                          id="lastName"
                          placeholder="Payment Preference"
                        /> */}
                      </div>
                    </div>

                    <div className="col-md-12 mb-4">
                      <div class="custom-input ">
                        <label for="sharing-affiliate-link" class="form-label">
                          How are you planning on sharing your affiliate link?
                        </label>
                        <input
                          type="text"
                          class="form-control custom-input-field"
                          id="sharing-affiliate-link"
                        />
                      </div>
                    </div>

                    <div className="affiliate-form-button">
                        <button type="submit" class="button-with-icon">Start Reffering</button>
                    </div>

                    <h2 className="affiliate-form-alreadyAccount">Already have an account? <span className="secondary-color">Login</span></h2>


                  </div>
                </div>


              </div>
            </div>
          </div>
        </div>
      </section>
    </DefaultLayout>
  );
};

export default AffiliateMarketing;
