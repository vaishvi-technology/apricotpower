import React, { useContext, useEffect, useState } from "react";
import DefaultLayout from "../../components/DefaultLayout";
import InnerBanner from "../../components/InnerBanner";
import { Button, Card, Col, Container, Form, Row } from "react-bootstrap";
import { base_url } from "../../api";
import { toast } from "react-toastify";
import { useSelector } from "react-redux";
import { CartContext } from "../../Context/CartContext";
import SuccesMessage from "../../components/SuccesMessage/SuccesMessage";

const BasicInfo = () => {
  const { cartItems } = useContext(CartContext);
  const [isSuccess, setIsSuccess] = useState(false);
  const [formData, setFormData] = useState({});
  const token = localStorage.getItem("login");

  const handleChange = (e) => {
    const { name, value } = e.target;

    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    try {
      document.querySelector(".loaderBox").classList.remove("d-none");

      const res = await fetch(`${base_url}/update/basic-information`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formData),
      });
      document.querySelector(".loaderBox").classList.add("d-none");

      const data = await res.json();
      if(data?.status){
        setIsSuccess(true)
      }
 
      if (res.ok) {
        // toast.success(data?.message);;
      } else {
        toast.error(data?.message);
      }
    } catch (err) {
      document.querySelector(".loaderBox").classList.add("d-none");
      toast.error(err?.message);
      console.error("Order failed:", err);
    }
  };
  useEffect(() => {
    setFormData(cartItems);
    document.title = "Basic Info | Apricot Power";
  }, [cartItems]);

  return (
    <DefaultLayout>
      <InnerBanner boldText1="Account Information" />
      <Container className="mt-4 mb-5  basic-info">
        <Card className="p-3 shadow">
          <h4 className="text-theme text-center">
            Edit Basic Account Information
          </h4>
          <Card.Body> 
            <Row className="justify-content-center align-items-center mb-3-responsive">
              <div  className="mb-2-responsive col-2-1024" >
                <Form.Label style={{ fontSize: "15px" }}>*Name:</Form.Label>
              </div>
              <div  className="mb-2-responsive col-3-1024">
                <Form.Control
                  name="first_name"
                  value={formData?.first_name}
                  onChange={handleChange}
                  placeholder="First Name"
                />
              </div>
              {/* <div  className="mb-2-responsive  col-2-1024">
                <Form.Control
                  name="middle_name"
                  value={formData?.middle_name}
                  onChange={handleChange}
                  placeholder="middle Name"
                />
              </div> */}
              <div  className="mb-2-responsive col-3-1024">
                <Form.Control
                  name="last_name"
                  value={formData?.last_name}
                  onChange={handleChange}
                  placeholder="last Name"
                />
              </div>
            </Row>
            <Row className="justify-content-center align-items-center mb-3-responsive">
              <div  className="mb-2-responsive  col-2-1024">
                <Form.Label style={{ fontSize: "15px" }}>Email:</Form.Label>
              </div>
              <div  className="mb-2-responsive col-6-1024">
                <Form.Control
                  name="email"
                  value={formData?.email}
                  onChange={handleChange}
                  placeholder="Email"
                />
              </div>
            </Row>
            <Row className="justify-content-center align-items-center mb-3-responsive">
              <div  className="mb-2-responsive  col-2-1024">
                <Form.Label style={{ fontSize: "15px" }}>
                  New Password:
                </Form.Label>
              </div>
              <div  className="mb-2-responsive col-6-1024">
                <Form.Control
                  name="new_password"
                  type="password"
                  value={formData?.new_password}
                  onChange={handleChange}
                  placeholder="New Password"
                />
              </div>
            </Row>
            <Row className="justify-content-center align-items-center mb-3-responsive">
              <div  className="mb-2-responsive  col-2-1024">
                <Form.Label style={{ fontSize: "15px" }}>
                  Confirm New Password:
                </Form.Label>
              </div>
              <div  className="mb-2-responsive col-6-1024">
                <Form.Control
                  type="password"
                  name="confirm_password"
                  value={formData?.confirm_password}
                  onChange={handleChange}
                  placeholder="Confirm New Password"
                />
              </div>
            </Row>

            <Row
              className={`justify-content-center align-items-center ${
                !isSuccess && "mb-3-responsive"
              }`}
            >
              <div  className="mb-2-responsive  col-2-1024">
                <Form.Label style={{ fontSize: "15px" }}>
                  Existing Password:
                </Form.Label>
              </div>
              <div  className="mb-2-responsive col-6-1024">
                <Form.Control
                  name="existing_password"
                  type="password"
                  value={formData?.existing_password}
                  onChange={handleChange}
                  placeholder="Existing Password"
                />
              </div>
            </Row>
            {isSuccess && (
              <Row className="justify-content-center align-items-center mb-3-responsive">
                <div  className="">
                  <SuccesMessage
                    title="Account Information Updated!"
                    subTitle="Your account details have been successfully updated. Thank you for keeping your information current."
                    setIsSuccess={setIsSuccess}
                  />
                </div>
              </Row>
            )}
            <Row className="justify-content-center">
              <Col xl={2}>
                <Button
                  onClick={() => {}}
                  variant="success"
                  className="mb-2-responsive"
                  style={{
                    backgroundColor: "#F2F2F2",
                    color: "#000",
                    width: "100%",
                  }}
                >
                  Cancel
                </Button>
              </Col>

              <Col xl={2}>
                <Button
                  onClick={() => {
                    handleSubmit();
                  }}
                  variant="success"
                  style={{ width: "100%" }}
                >
                  Save
                </Button>
              </Col>
            </Row>
          </Card.Body>
        </Card>
      </Container>
    </DefaultLayout>
  );
};

export default BasicInfo;
