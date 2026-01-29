/* eslint-disable react-hooks/exhaustive-deps */
/* eslint-disable react/no-unescaped-entities */
/* eslint-disable react/jsx-key */
import { useContext, useEffect, useState } from "react";
import {
  Navbar,
  Nav,
  Container,
  NavDropdown,
  Badge,
  Dropdown,
  Tab,
  Row,
  Col,
  Spinner,
} from "react-bootstrap";

import {
  carticon,
  logo,
  searchicon,
  usericon,
  instagramicon,
  purchaseicon,
  reviewicon,
  refericon,
  birthdayicon,
  fbthumbicon,
  referFriendsicon,
  copyicon,
  logoWhite,
} from "../../../assets/images";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faCopy, faUser } from "@fortawesome/free-solid-svg-icons";
import CustomModal from "../../CustomModal";
import CustomTable from "../../CustomTable";
import { base_url, Referal_url } from "../../../api";
import { toast } from "react-toastify";
import moment from "moment";
import { CartContext } from "../../../Context/CartContext";
import { IPInfoContext } from "ip-info-react";
import ExpressCheckout from "../../ExpressCheckout";
import { useCookies } from "react-cookie";

const iconMap = {
  purchaseicon: purchaseicon,
  birthdayicon: birthdayicon,
  fbthumbicon: fbthumbicon,
  instagramicon: instagramicon,
  reviewicon: reviewicon,
  refericon: refericon,
};
const inventoryHeaders = [
  // {
  //   key: "id",
  //   title: "S.No",
  // },
  {
    key: "date",
    title: "date",
  },
  {
    key: "type",
    title: "Type",
  },
  {
    key: "action",
    title: "Action",
  },
  {
    key: "points",
    title: "Points",
  },
  {
    key: "status",
    title: "Status",
  },
];
const faq = [
  {
    id: 1,
    question: "What is this?",
    answer:
      "This is our way of showing our appreciation. You’ll earn points for activities on our site, like referrals and purchases. You can use them to earn discounts off purchases, so the more you collect the more you save.",
  },
  {
    id: 2,
    question: "Who can join?",
    answer: "Anyone with an account is automatically enrolled.",
  },
  {
    id: 3,
    question: "How do I earn points?",
    answer:
      "You can earn points for all sorts of activities, including referring friends, and making purchases. To see all the ways you can earn points click the Earn Points tab in the menu.",
  },
  {
    id: 4,
    question: "How do I view my point balance?",
    answer: "Your point balance is on every page in the top bar.",
  },
  {
    id: 5,
    question: "How do I redeem my points?",
    answer:
      "Select the tab called Redeem Points. Here you’ll see all the rewards we offer. You can redeem the points you've earned for vouchers which will get you a code to enter in the 'Promo Code' section during checkout (Only one promotion may be used per order).",
  },
  {
    id: 6,
    question: "Is there a limit to the number of points I can earn?",
    answer: "No. Go ahead and earn as many as you can!",
  },
];

const Header = () => {
  const [pointsTotal, setpointsTotal] = useState(null);
  useEffect(() => {
    if (window?.loyaltylion?.customer?.pointsTotal) {
      setpointsTotal(window.loyaltylion.customer.pointsTotal);
    }

    const interval = setInterval(() => {
      if (window?.loyaltylion?.customer?.pointsTotal) {
        setpointsTotal(window.loyaltylion.customer.pointsTotal);
        clearInterval(interval);
      }
    }, 500);

    return () => clearInterval(interval);
  }, []);
  const [cookies] = useCookies(["role"]);
  const [pointsModal, setPointsModal] = useState(false);
  const [show, setshow] = useState(false);
  const [points, setPoints] = useState([]);
  const [rewards, setRewards] = useState([]);
  const [search, setSearch] = useState("");
  const [activeTab, setActiveTab] = useState("earnPoints");
  const [purchaseModal, setPurchaseModal] = useState(false);
  const [birthdayDateModal, setBirthdayDateModal] = useState(false);
  const [rewardModal, setRewardModal] = useState(false);
  const [selectReward, setSelectedReward] = useState(false);
  const [categories, setCategories] = useState([]);
  const [selectedItem, setSelectedItem] = useState({});
  const [recentActivityData, setRecentActivityData] = useState([]);
  const [loading, setLoading] = useState(false);
  const [formdata, setFormdata] = useState({});
  const [rewardData, setRewardData] = useState();

  const token = localStorage.getItem("login");
  // const ip = JSON.parse(localStorage.getItem("storefeuinverau_country_code"));

  const navigate = useNavigate();
  const handleLogout = () => {
    localStorage.clear("login");
    navigate("/login");
    window.location.reload();
  };
  const { cartItems, fetchCount } = useContext(CartContext);

  const fetchCategories = () => {
    fetch(`${base_url}/categories/`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then(async (data) => {
        setCategories(data);
      });
  };

  const fetchpoints = async () => {
    await fetch(`${base_url}/earn-points`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => response.json())
      .then(async (data) => {
        setPoints(data);
      });
  };
  const fetchReward = async () => {
    try {
      const response = await fetch(`${base_url}/get/reward`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      });

      if (response.status === 400) {
        // Do NOT update setRewards on 400 error
        console.log("Received 400 Bad Request. Rewards not updated.");
        return; // exit early
      }

      if (!response.ok) {
        // For other non-200 responses, optionally handle error and reset rewards
        setRewards([]);
        console.log("API error: ", response.status);
        if (response?.status == "401") {
          localStorage.removeItem("login");
        }
        return;
      }

      const data = await response.json();
      setRewards(data || []);
    } catch (error) {
      console.log(error);
      setRewards([]);
    }
  };
  // const fetchCount = async () => {
  //   try {
  //     const queryParams = new URLSearchParams();
  //     if (ip.ip) queryParams.append("ip", ip.ip);
  //     const token = localStorage.getItem("login");
  //     const response = await fetch(`${base_url}/ ${queryParams}`, {
  //       method: "GET",
  //       headers: {
  //         Accept: "application/json",
  //         Authorization: `Bearer ${token}`,
  //         "Content-Type": "application/json",
  //       },
  //     });

  //     if (response.status === 400) {
  //       // Do NOT update setRewards on 400 error
  //       console.log("Received 400 Bad Request. Rewards not updated.");
  //       return; // exit early
  //     }

  //     if (!response.ok) {
  //       // For other non-200 responses, optionally handle error and reset rewards
  //       setRewards([]);
  //       console.log("API error: ", response.status);
  //       return;
  //     }

  //     const data = await response.json();
  //     setcartItems(data || []);

  //   } catch (error) {
  //     console.log(error);
  //   }
  // };
  const fetchActivity = async () => {
    try {
      const response = await fetch(`${base_url}/recent/activity`, {
        method: "GET",
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      });

      if (response.status === 400) {
        // Do NOT update setRewards on 400 error
        console.log("Received 400 Bad Request. Rewards not updated.");
        return; // exit early
      }

      if (!response.ok) {
        // For other non-200 responses, optionally handle error and reset rewards
        setRecentActivityData([]);
        console.log("API error: ", response.status);
        return;
      }

      const data = await response.json();
      setRecentActivityData(data || []);
    } catch (error) {
      console.log(error);
      setRewards([]);
    }
  };
  const userInfo = useContext(IPInfoContext);

  useEffect(() => {
    fetchCategories();
    fetchpoints();
    // fetchCount();
    fetchReward();
    fetchActivity();
  }, []);
  useEffect(() => {
    if (userInfo?.ip) {
      fetchCount();
    }
  }, [userInfo.ip]);

  const validateBirthday = () => {
    const { day, month, year } = formdata || {};

    // Check if all fields are present
    if (!day || !month || !year) {
      return toast.error("Please complete your birth date.");
    }

    // Convert values to integers
    const d = parseInt(day, 10);
    const m = parseInt(month, 10) - 1; // JS months are 0-indexed
    const y = parseInt(year, 10);

    // Check if it's a valid date
    const date = new Date(y, m, d);
    if (
      date.getFullYear() !== y ||
      date.getMonth() !== m ||
      date.getDate() !== d
    ) {
      return toast.error("Please enter a valid date.");
    }

    const today = new Date();
    if (date > today) {
      return toast.error("Birth date cannot be in the future.");
    }

    const age = today.getFullYear() - y;
    if (age < 13 || (age === 13 && today < new Date(y + 13, m, d))) {
      return toast.error("You must be at least 13 years old.");
    }

    return true;
  };

  const handleSaveBirthday = async () => {
    setLoading(true);
    try {
      if (validateBirthday() !== true) {
        return; // Prevent submission
      } else {
        document.querySelector(".loaderBox")?.classList.remove("d-none");
        const updateformdata = {
          ...formdata,
          earning_id: selectedItem?.id,
        };
        console.log({ updateformdata });
        const res = await fetch(`${base_url}/add/earn-points`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
          body: JSON.stringify(updateformdata),
        });
        document.querySelector(".loaderBox")?.classList.add("d-none");

        const data = await res.json();
        if (res.ok) {
          // toast.success(data?.message);;
          setFormdata({});

          setSelectedItem({ ...selectedItem, status: 1 });
          await fetchpoints();
        } else {
          setLoading(false);

          console.log(data);
        }
      }
      setLoading(false);
    } catch (err) {
      document.querySelector(".loaderBox")?.classList.add("d-none");

      console.error("Order failed:", err);
      setLoading(false);
    }
  };
  const handleSocialClick = async (item) => {
    window.open(item?.link, "_blank");
    const formdata = {
      earning_id: item?.id,
    };
    try {
      document.querySelector(".loaderBox")?.classList.remove("d-none");

      const res = await fetch(`${base_url}/add/earn-points`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formdata),
      });
      document.querySelector(".loaderBox")?.classList.add("d-none");

      const data = await res.json();
      if (res.ok) {
        // toast.success(data?.message);;
        fetchpoints();
      } else {
        console.log(data);
      }
    } catch (err) {
      document.querySelector(".loaderBox")?.classList.add("d-none");

      console.error("Order failed:", err);
    }
  };
  const handleRewards = async (item) => {
    setSelectedReward(item);
    const formdata = {
      reward_id: item?.id,
    };
    try {
      setLoading(true);

      const res = await fetch(`${base_url}/add/reward`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(formdata),
      });

      const data = await res.json();
      setLoading(false);
      if (res.ok) {
        // toast.success(data?.message);;
        setRewardData(data);
        setRewardModal(true);
        fetchpoints();
      } else {
        console.log(data);
      }
    } catch (err) {
      setLoading(false);

      console.error("Order failed:", err);
    }
  };

  const HandleSearch = () => {
    fetch(`${base_url}/search?search=${search}`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then(async (data) => {
        if (data) {
          navigate(`/search?search=${search}`);
        }
      });
  };
  const [url, seturl] = useState("werewrwerwer");
  const message = encodeURIComponent(
    "Get $10 off your first purchase! Use this link: " + url
  );
  const GetReferal = () => {
    fetch(`${base_url}/refferal/link`, {
      method: "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })
      .then((response) => response.json())
      .then(async (data) => {
        seturl(Referal_url + data?.code);
      });
  };
  useEffect(() => {
    GetReferal();
  }, []);
  const { pathname } = useLocation();

  return (
    <Navbar expand="xl" className="main-header navbar-expand-xl">
      <Container>
        {/* Brand Logo */}
        <Navbar.Brand to="/">
          <Link to="/">
            <img src={logo} alt="Apricot Power Logo" height="40" />
          </Link>
        </Navbar.Brand>
        <Link to="/cart" className="header-link cart-link-mobile">
          <div className="icon-wrapper">
            <img src={carticon} alt="Cart Icon" className={"icon-img"} />
            {cartItems?.cart !== 0 && cartItems.cart && (
              <span className="cart-badge">{cartItems?.cart}</span>
            )}
          </div>
          <span className={`icon-text ${"text-white"}`}>CART</span>
        </Link>

        {/* Toggle for mobile view */}
        <Navbar.Toggle aria-controls="navbar-nav" />

        {/* Navbar Links */}
        <Navbar.Collapse id="navbar-nav">
          <Nav className="me-auto main-navbar-links">
            <NavDropdown title="Products" id="products-dropdown">
              <NavDropdown.Item as={Link} to="/store">
                All Products
              </NavDropdown.Item>
              {(categories || []).map((item, index) => {
                const slug = `${item?.id}-${item?.category_title.replace(
                  /\s+/g,
                  "-"
                )}`;
                return (
                  <NavDropdown.Item
                    key={index}
                    as={Link}
                    to={`/category/${slug}`}
                    state={{ categoryID: item?.id }}
                  >
                    {item?.category_title}
                  </NavDropdown.Item>
                );
              })}
            </NavDropdown>

            <Nav.Link to="/retailer-locations">
              <Link to="/retailer-locations">Retail Locations</Link>
            </Nav.Link>
            <Nav.Link to="/blogs">
              <Link to="/blogs">Blogs</Link>
            </Nav.Link>
            <NavDropdown title="About" id="products-dropdown">
              <NavDropdown.Item as={Link} to="/contact-us">
                Contact Us
              </NavDropdown.Item>
              <NavDropdown.Item as={Link} to="/reviews">
                Reviews
              </NavDropdown.Item>
              <NavDropdown.Item as={Link} to="/testimonial">
                Testimonials
              </NavDropdown.Item>
              <NavDropdown.Item as={Link} to="/wholesale-application">
                Carry Our Products
              </NavDropdown.Item>
              <NavDropdown.Item
                href="https://apricotpower.ositracker.com/myrefer"
                target="_blank"
                rel="noopener noreferrer"
              >
                Affiliate Program
              </NavDropdown.Item>
              <NavDropdown.Item as={Link} to="/refer-a-friend">
                Refer A Friend
              </NavDropdown.Item>
              <NavDropdown.Item as={Link} to="/faq">
                Common Questions
              </NavDropdown.Item>
              <NavDropdown.Item as={Link} to="/seeds-recipes">
                Apricot Seed Recipes
              </NavDropdown.Item>
              <NavDropdown.Item as={Link} to="/recipes-superfood">
                B17 Superfood Recipes
              </NavDropdown.Item>
              <NavDropdown.Item as={Link} to="/life-style">
                Life Style
              </NavDropdown.Item>
            </NavDropdown>
          </Nav>
          {/* Search Bar */}
          {/* <Form className="d-flex">
            <FormControl
              type="search"
              placeholder="Search"
              className="me-2"
              aria-label="Search"
            />
            <Button variant="outline-success">Search</Button>
          </Form> */}

          {!token ? (
            <>
              <div
                className={`header-form  header-formnew ${
                  pathname === "/" ? "black-border" : "white-border"
                } `}
              >
                {/* <div className={`header-form header-formnew`}> */}
                <input
                  // style={{ color: "#ffff", placeholderColor: "#090909" }}
                  type="text"
                  className={`form-control ${
                    pathname === "/" ? "black-placeholder" : "white-placeholder"
                  }`}
                  onKeyDown={(e) => {
                    if (e.key === "Enter") {
                      HandleSearch();
                    }
                  }}
                  placeholder="Search "
                  id="exampleInputEmail1"
                  onChange={(e) => setSearch(e.target.value)}
                  aria-describedby="emailHelp"
                />
                <button
                  className={`header-form-btn text-white`}
                  onClick={() => HandleSearch()}
                >
                  <img
                    src={searchicon}
                    alt="Search Icon"
                    className={`${
                      pathname === "/" ? "searchIcon" : "icon-img"
                    }`}
                  />
                </button>
              </div>
            </>
          ) : (
            <>
              <div
                className={`header-form  ${
                  pathname !== "/" && "white-placeholder"
                }`}
              >
                <input
                  // style={{ color: "#ffff", placeholderColor: "#090909" }}
                  type="text"
                  className={`form-control ${
                    pathname === "/" ? "black-placeholder" : "white-placeholder"
                  }`}
                  onKeyDown={(e) => {
                    if (e.key === "Enter") {
                      HandleSearch();
                    }
                  }}
                  placeholder="Search "
                  id="exampleInputEmail1"
                  onChange={(e) => setSearch(e.target.value)}
                  aria-describedby="emailHelp"
                />
                <button
                  className={`header-form-btn text-white`}
                  onClick={() => HandleSearch()}
                >
                  <img
                    src={searchicon}
                    alt="Search Icon"
                    className={`${
                      pathname === "/" ? "searchIcon" : "icon-img"
                    }`}
                  />
                </button>
              </div>
            </>
          )}
          {/* Icons */}
          <Nav className="ms-3 navbar-icons">
            {!token ? (
              <div
                className={` ${
                  !token ? "user-section2 user-left" : "user-section"
                }`}
              >
                <Link to="/login" className="header-link">
                  <img
                    src={usericon}
                    alt="User Icon"
                    className={pathname !== "/" && "icon-img login-icon" }
                  />
                  <span
                    className={`icon-text text-login ${
                      pathname === "/" ? "text-dark" : "text-white"
                    }`}
                  >
                    LOG IN
                  </span>
                </Link>

                <Link to="/cart" className="header-link cart-link">
                  <div className="icon-wrapper">
                    <img
                      src={carticon}
                      alt="Cart Icon"
                      className={pathname !== "/" && "icon-img"}
                    />
                    {cartItems?.cart !== 0 && cartItems.cart && (
                      <span className="cart-badge">{cartItems?.cart}</span>
                    )}
                  </div>
                  <span
                    className={`icon-text ${
                      pathname === "/" ? "text-dark" : "text-white"
                    }`}
                  >
                    CART
                  </span>
                </Link>
              </div>
            ) : (
              <>
                <div className="user-section ">
                  <div
                    style={{ flexDirection: "column" }}
                    className="d-flex cursor-pointer "
                  >
                    <diV className="d-flex align-items-center">
                      <Link to="/cart" style={{ textDecoration: "none" }}>
                        <img
                          src={carticon}
                          alt="Cart Icon"
                          className={pathname !== "/" && "icon-img text-custom-black"}
                        />
                        <span
                          className={` text-icon-white${
                            pathname === "/" ? "text-dark" : "text-white"
                          }`}
                          style={{ position: "relative", top: "-10px" }}
                        >
                          {cartItems?.cart !== 0 && cartItems?.cart}
                        </span>
                      </Link>
                      <FontAwesomeIcon
                        className={pathname !== "/" && "icon-img text-custom-black"}
                        icon={faUser}
                        style={{ fontSize: "20px", marginLeft: "10px" }}
                      />
                      <span
                        style={{ maxWidth: 100 }}
                        className={pathname !== "/" ? "icon-img text-custom-black" : "user-name"}
                      >
                        {cartItems?.first_name}
                      </span>
                    </diV>

                    <Badge
                      bg="warning"
                      className="mt-1 text-white fw-semibold text-point"
                      style={{ cursor: "pointer" }}
                      onClick={() =>
                        document
                          .getElementsByClassName(
                            "lion-loyalty-widget__title"
                          )[0]
                          .click()
                      }
                    >
                      ⭐ You have{" "}
                      {token &&
                        cartItems?.id &&
                        cartItems?.email &&
                        pointsTotal}{" "}
                      points
                    </Badge>
                  </div>

                  <Dropdown align="end">
                    <Dropdown.Toggle
                      className={`p-0 border-0 shadow-none bg-transparent ${
                        pathname === "/" ? "text-dark" : "text-white"
                      }`}
                      style={{
                        boxShadow: "none",
                        display: "flex",
                        alignItems: "center",
                      }}
                    >
                      <FontAwesomeIcon icon="angle-down" />
                    </Dropdown.Toggle>

                    <Dropdown.Menu>
                      <Dropdown.Item href="/OrderHistory">
                        Order History
                      </Dropdown.Item>
                      <Dropdown.Item href="/BasicInfo">
                        Basic Info
                      </Dropdown.Item>
                      <Dropdown.Item href="/AccountDetails">
                        Account Details
                      </Dropdown.Item>
                      {cookies?.role == 2 && (
                        <Dropdown.Item href="#" onClick={() => setshow(true)}>
                          Quick Checkout
                        </Dropdown.Item>
                      )}
                      <Dropdown.Item href="/EmailPreferences">
                        Email Preferences
                      </Dropdown.Item>
                      <Dropdown.Divider />
                      <Dropdown.Item onClick={handleLogout}>
                        Logout
                      </Dropdown.Item>
                    </Dropdown.Menu>
                  </Dropdown>
                </div>
              </>
            )}
          </Nav>
        </Navbar.Collapse>
      </Container>

      <CustomModal
        show={pointsModal}
        close={() => {
          setPointsModal(false);
        }}
        size="lg"
      >
        <div className="modal-screens bg-white modal-container-fixed ">
          <Tab.Container
            id="left-tabs-example"
            activeKey={activeTab}
            onSelect={(selectedKey) => setActiveTab(selectedKey)}
          >
            <Row>
              <Col sm={3}>
                <div className="modal-sidebar">
                  <div className="sidebar__logo">
                    <Link to="/">
                      <img
                        src={logoWhite}
                        style={{ width: "150px" }}
                        alt="Logo"
                      />
                    </Link>
                  </div>
                  <div className="modal-sidebar-contant">
                    <div className="modal-sidebar-points">
                      <h5>
                        {cartItems?.pointsTotal} <span>Points</span>
                      </h5>
                    </div>
                    <div className="modal-sidebar-list">
                      <Nav variant="pills" className="flex-column ">
                        <Nav.Item>
                          <Nav.Link
                            eventKey="earnPoints"
                            style={{ paddingLeft: "10px" }}
                          >
                            Earn Points
                          </Nav.Link>
                        </Nav.Item>
                        <Nav.Item>
                          <Nav.Link
                            eventKey="getRewards"
                            style={{ paddingLeft: "10px" }}
                          >
                            Get rewards
                          </Nav.Link>
                        </Nav.Item>
                        <Nav.Item>
                          <Nav.Link
                            eventKey="referFriends"
                            style={{ paddingLeft: "10px" }}
                          >
                            refer friends
                          </Nav.Link>
                        </Nav.Item>
                        <Nav.Item>
                          <Nav.Link
                            eventKey="account"
                            style={{ paddingLeft: "10px" }}
                          >
                            account
                          </Nav.Link>
                        </Nav.Item>
                        <Nav.Item>
                          <Nav.Link
                            eventKey="help"
                            style={{ paddingLeft: "10px" }}
                          >
                            help
                          </Nav.Link>
                        </Nav.Item>
                      </Nav>
                    </div>
                  </div>
                </div>
              </Col>
              <Col sm={9}>
                <Tab.Content>
                  <Tab.Pane eventKey="earnPoints">
                    <div className="modal-screens-content-header mb-3">
                      <h4>earn point</h4>
                    </div>
                    <div className="earn-points-content">
                      <div className="row">
                        {points.map((item, index) => (
                          <div className="col-lg-6 mb-3" key={index}>
                            <div className="earn-points-item">
                              <div className="earn-points-item-body">
                                <img src={iconMap[item.icon]} alt={item.type} />
                                <h5>{item.type}</h5>
                              </div>
                              <div className="earn-points-item-footer">
                                <button
                                  onClick={() => {
                                    if (
                                      item?.link !== null &&
                                      item.platform !== null
                                    ) {
                                      handleSocialClick(item);
                                    }
                                    if (item?.type == "Reffer A Friend") {
                                      setActiveTab("referFriends");
                                    }
                                    if (item?.type == "Make A Purchase") {
                                      setPurchaseModal(true);
                                    }
                                    if (item?.type == "Happy Birthday") {
                                      setSelectedItem(item);
                                      setBirthdayDateModal(true);
                                    }
                                  }}
                                  className=""
                                  disabled={item.status == 1 && item.platform}
                                  style={{
                                    background: "transparent",
                                    border: "none",
                                  }}
                                >
                                  {item.points}
                                </button>
                              </div>
                            </div>
                          </div>
                        ))}
                      </div>
                    </div>
                  </Tab.Pane>

                  <Tab.Pane eventKey="getRewards">
                    <div className="modal-screens-content-header mb-3">
                      <h4>Get Rewards</h4>
                    </div>

                    <div className="getRewards-content">
                      <div className="row scrollable-rewards">
                        <div className="col-md-12 mb-3">
                          <div className="get-rewards-head">
                            <p>
                              <span className="font-weight-bold">
                                {cartItems?.total_points}
                              </span>{" "}
                              points can be used now
                            </p>
                            <a href="#">Why not all of them?</a>
                          </div>
                        </div>

                        {(rewards || []).map((item, index) => (
                          <div
                            className="col-lg-6 mb-3"
                            style={{ position: "relative" }}
                            key={index}
                          >
                            <div className="get-reward-items">
                              <h4>{item?.heading}</h4>
                              <p>{item?.require_points} Points</p>
                              <div
                                className="position-absolute start-50 translate-middle-x"
                                style={{ bottom: "20px" }}
                              >
                                {loading && selectReward.id === item?.id ? (
                                  <>
                                    <Spinner size="lg" />
                                  </>
                                ) : (
                                  <button
                                    onClick={() => handleRewards(item)}
                                    className="btn text-nowrap"
                                  >
                                    <span
                                      style={{ backgroundSize: "100% 100%" }}
                                    >
                                      {item?.require_points <=
                                      cartItems?.total_points
                                        ? "  Get Rewards"
                                        : "More Points Needed"}
                                    </span>
                                  </button>
                                )}
                              </div>
                            </div>
                          </div>
                        ))}
                      </div>
                    </div>
                  </Tab.Pane>

                  <Tab.Pane eventKey="referFriends">
                    <div className="modal-screens-content-header mb-3">
                      <h4>Refer Friends</h4>
                    </div>
                    <div className="referFriends-sec">
                      <div className="row mb-3">
                        <div className="col-md-12">
                          <div className="referFriends-content">
                            <img
                              src={referFriendsicon}
                              className="referFriends-icon"
                              alt=""
                            />
                            <h2>
                              Give a friend $10 off their first purchase and
                              earn 1,000 points if they spend over $39
                            </h2>
                          </div>
                          <div className="apricot-social-btns">
                            <a
                              href={`https://wa.me/?text=${message}`}
                              target="_blank"
                              rel="noopener noreferrer"
                              className="apricot-social-btn whatsapp-icon"
                            >
                              WhatsApp
                            </a>

                            <a
                              href={`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(
                                url
                              )}`}
                              target="_blank"
                              rel="noopener noreferrer"
                              className="apricot-social-btn facebook-icon"
                            >
                              Facebook
                            </a>

                            <a
                              href={`sms:?body=${message}`}
                              className="apricot-social-btn email-icon"
                            >
                              Message
                            </a>

                            <a
                              href={`https://twitter.com/intent/tweet?text=${message}`}
                              target="_blank"
                              rel="noopener noreferrer"
                              className="apricot-social-btn x-icon"
                            >
                              X (Twitter)
                            </a>

                            <a
                              href={`mailto:?subject=Join me and get $10!&body=${message}`}
                              className="apricot-social-btn email-icon"
                            >
                              Email
                            </a>
                          </div>

                          <div
                            className="referFriends-copyLink"
                            onClick={() => navigator.clipboard.writeText(url)}
                          >
                            <h4>Or copy your link and share it anywhere</h4>
                            <p>{url}</p>

                            <a
                              className="referFriend-copy-link"
                              href="javascript:;"
                            >
                              <img src={copyicon} alt=""></img>
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </Tab.Pane>

                  <Tab.Pane eventKey="account">
                    <div className="modal-screens-content-header mb-3">
                      <h4>Your Recent Activity</h4>
                    </div>
                    <div className="recent-activity">
                      <div className="row mb-3">
                        <div className="col-md-12">
                          <div className="dashboard-table">
                            <CustomTable headers={inventoryHeaders}>
                              <tbody>
                                {recentActivityData.map((item) => (
                                  <tr>
                                    <td>
                                      {moment(item.date).format("MMM Do YY")}{" "}
                                    </td>
                                    {/* <td>{item.type}</td> */}
                                    <td>{item.action}</td>
                                    <td>{item.points}</td>
                                    <td>
                                      <span
                                        className={`${
                                          item.status ? "bg-green" : "bg-yellow"
                                        } px-2 rounded`}
                                      >
                                        {item.status === 1
                                          ? "Approved"
                                          : "Pending"}
                                      </span>
                                    </td>
                                  </tr>
                                ))}
                              </tbody>
                            </CustomTable>
                          </div>
                        </div>
                      </div>
                    </div>
                  </Tab.Pane>

                  <Tab.Pane eventKey="help">
                    <div className="modal-screens-content-header mb-3">
                      <h4>Help</h4>
                    </div>
                    <div className="modal-screens-help">
                      <div className="row mb-3">
                        <div className="col-md-12">
                          {faq.map((item) => (
                            <div className="modal-screen-help-content">
                              <h5>{item.question}</h5>
                              <p>{item.answer}</p>
                            </div>
                          ))}
                        </div>
                      </div>
                    </div>
                  </Tab.Pane>
                </Tab.Content>
              </Col>
            </Row>
          </Tab.Container>
        </div>
      </CustomModal>
      {/* ============================== Purchase model========================= */}
      <CustomModal
        show={purchaseModal}
        close={() => {
          setPurchaseModal(false);
        }}
        size="lg"
      >
        <h2 className=" text-center fw-bold text-theme">Make A Purchase</h2>

        <p className="text-center p-3">
          Get 5 points for every $1 you spend in our store
        </p>
      </CustomModal>
      {/* ==============================birthday model========================= */}
      <CustomModal
        show={birthdayDateModal}
        close={() => {
          setBirthdayDateModal(false);
        }}
        size="lg"
      >
        <h2 className=" text-center fw-bold text-theme">Happy Birthday</h2>
        {selectedItem?.status == 1 ? (
          <>
            <p className="text-center p-3">
              You will get 250 points on your next birthday
            </p>
          </>
        ) : (
          <>
            <p className="text-center p-3">
              Enter your birthday below and you'll get 250 points on your next
              birthday
            </p>
            <div className="container">
              <div className="d-flex justify-content-center align-items-center p-3">
                <div
                  className="row g-2  justify-content-center align-items-center
            "
                >
                  <div className="col-12 col-sm-4 col-md-3 ">
                    <select
                      className="form-select"
                      onChange={(e) =>
                        setFormdata({ ...formdata, month: e.target.value })
                      }
                    >
                      <option value="">Month</option>
                      <option value="1">January</option>
                      <option value="2">February</option>
                      <option value="3">March</option>
                      <option value="4">April</option>
                      <option value="5">May</option>
                      <option value="6">June</option>
                      <option value="7">July</option>
                      <option value="8">August</option>
                      <option value="9">September</option>
                      <option value="10">October</option>
                      <option value="11">November</option>
                      <option value="12">December</option>
                    </select>
                  </div>
                  <div className="col-6 col-sm-4 col-md-2 ">
                    <input
                      type="text"
                      className="form-control"
                      placeholder="Day"
                      value={formdata?.day}
                      onChange={(e) =>
                        setFormdata({ ...formdata, day: e.target.value })
                      }
                    />
                  </div>
                  <div className="col-6 col-sm-4 col-md-2">
                    <input
                      type="text"
                      className="form-control"
                      placeholder="Year"
                      value={formdata?.year}
                      onChange={(e) =>
                        setFormdata({ ...formdata, year: e.target.value })
                      }
                    />
                  </div>
                </div>
              </div>
            </div>

            <div className="d-flex justify-content-center m-3">
              {loading ? (
                <Spinner size={"lg"} />
              ) : (
                <button
                  className="button-with-icon "
                  onClick={handleSaveBirthday}
                >
                  save
                </button>
              )}
            </div>
          </>
        )}
      </CustomModal>
      {/* ============================== Reward model========================= */}
      <CustomModal
        show={rewardModal}
        close={() => {
          setRewardModal(false);
        }}
        size="md"
      >
        <h2 className=" text-center fw-bold text-theme mt-2">
          {selectReward?.heading}
        </h2>

        <p className="text-center p-3">
          Your New Voucher code is below. Use it at checkout to get your
          discount!
        </p>
        <div className="d-flex  justify-content-center align-items-center mb-3">
          <span className="referal_voucher_code">
            {rewardData?.voucher || "5446545"}
          </span>

          <FontAwesomeIcon
            icon={faCopy}
            style={{ cursor: "pointer", marginLeft: "10px", fontSize: "20px" }}
            onClick={() => {
              navigator.clipboard.writeText(rewardData?.voucher);
              // toast.success("Voucher code copied to clipboard!");
            }}
          />
        </div>
      </CustomModal>
      <ExpressCheckout show={show} setshow={setshow} />
    </Navbar>
  );
};

export default Header;
