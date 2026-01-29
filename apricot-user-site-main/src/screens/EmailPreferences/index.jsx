import React, { useContext, useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { Button, Card, Col, Container, Form, Row } from "react-bootstrap";
import { base_url } from "../../api";
import { toast } from "react-toastify";
import { CartContext } from "../../Context/CartContext";
import api from "../../api/api";
import SuccesMessage from "../../components/SuccesMessage/SuccesMessage";

const EmailPreferences = () => {
  const [data, setData] = useState({});
  const [isSuccess, setIsSuccess] = useState(false);
  const [formData, setFormData] = useState({});
  const token = localStorage.getItem("login");

  const [newsletterChecked, setNewsletterChecked] = useState(true);
  const [reviewChecked, setReviewChecked] = useState(true);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleNewsletterChange = (e) => {
    setNewsletterChecked(e.target.checked);
    console.log("Newsletter:", e.target.checked);
  };

  const handleReviewChange = (e) => {
    setReviewChecked(e.target.checked);
    console.log("Review Request:", e.target.checked);
  };

  const handleSubmit = async () => {
    try {
      document.querySelector(".loaderBox")?.classList.remove("d-none");
      const payload = {
        ...formData,
        news_letter: newsletterChecked,
        review_product: reviewChecked,
      };

      const res = await fetch(`${base_url}/update/email-preferences`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(payload),
      });

      document.querySelector(".loaderBox")?.classList.add("d-none");
      const data = await res.json();
      if (data?.status) {
        setIsSuccess(true);
      }
      if (res.ok) {
        // toast.success(data?.message);;
      } else toast.error(data?.message);
    } catch (err) {
      document.querySelector(".loaderBox")?.classList.add("d-none");
      toast.error(err?.message);
      console.error("Submit failed:", err);
    }
  };

  useEffect(() => {
    GetUserDetail();
    document.title = "Email Preferences | Apricot Power";
  }, []);
  const GetUserDetail = async () => {
    try {
      const response = await api.get("/auth/user");

      setData(response?.data);

      return response.data;
    } catch (error) {
      console.error("Error fetching user detail:", error);
      return null; // return null if API fails
    }
  };

  return (
    <DefaultLayout>
      <InnerBanner boldText1="Email Preferences" />
      <Container className="my-5">
        <Row className="justify-content-center">
          <Col xs={12} md={10} lg={8}>
            <Card className="p-3 shadow">
              <h4 className="text-theme text-center mb-3">
                Email Preferences for {data?.email}
              </h4>
              <p className="text-center px-2" style={{ fontSize: "16px" }}>
                How would you like to stay in touch? We will never give your
                email address to a third party, and you can unsubscribe at any
                time.{" "}
                <a
                  href="https://www.apricotpower.com/privacy.asp"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="greenColor"
                >
                  (Read our privacy policy)
                </a>
              </p>

              <Card.Body>
                <Form>
                  <Row className="align-items-center mb-3">
                    <Col xs={12} md={3}>
                      <Form.Label>Email:</Form.Label>
                    </Col>
                    <Col xs={12} md={9}>
                      <Form.Control
                        name="email"
                        value={formData.email || ""}
                        onChange={handleChange}
                        placeholder="Email"
                      />
                    </Col>
                  </Row>

                  <Row className="mb-3">
                    <Col xs={12}>
                      <Form.Check
                        type="checkbox"
                        id="newsletter"
                        name="newsletter"
                        checked={newsletterChecked}
                        onChange={handleNewsletterChange}
                        className="mb-3"
                        label={
                          <>
                            <strong>Apricot Power Newsletter:</strong>{" "}
                            <span className="d-block">
                              Get exclusive discounts while learning more about
                              good health! Sent approximately once per week.
                            </span>
                          </>
                        }
                      />

                      <Form.Check
                        type="checkbox"
                        id="reviewRequest"
                        name="reviewRequest"
                        checked={reviewChecked}
                        onChange={handleReviewChange}
                        className="mb-3"
                        label={
                          <>
                            <strong>Review Us and Our Products:</strong>{" "}
                            <span className="d-block">
                              Your feedback is vital to our business. If this
                              box is checked you will receive review invitations
                              after purchases.
                            </span>
                          </>
                        }
                      />
                    </Col>
                  </Row>

                  <Row className="justify-content-center">
                    <Col xs={12} sm={12} md={12}>
                      {isSuccess && (
                        <Row className="justify-content-center align-items-center mb-3">
                          <Col md={6} className="">
                            <SuccesMessage
                              title="Email Preferences Updated!"
                              subTitle="Your email settings are updated. We’ll only send you the emails you want to receive."
                              setIsSuccess={setIsSuccess}
                            />
                          </Col>
                        </Row>
                      )}
                    </Col>
                  </Row>
                  <Row className="justify-content-center">
                    <Col xs={12} sm={6} md={4}>
                      <Button
                        onClick={handleSubmit}
                        variant="success"
                        className="w-100"
                      >
                        Save
                      </Button>
                    </Col>
                  </Row>
                </Form>
              </Card.Body>
            </Card>
          </Col>
        </Row>
      </Container>
    </DefaultLayout>
  );
};

export default EmailPreferences;
