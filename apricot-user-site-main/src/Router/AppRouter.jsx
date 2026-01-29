import { useContext, useEffect } from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";

// Import your components/pages
import Home from "../screens/Home";
import FuelProducts from "../screens/FuelProducts";
import RetailerLocations from "../screens/RetailerLocations";
import Blogs from "../screens/Blogs";
import Contact from "../screens/Contact";
import Reviews from "../screens/Reviews";
import WholesaleApplication from "../screens/WholesaleApplication";
import AffiliateMarketing from "../screens/AffiliateMarketing";
import ReferAFriend from "../screens/ReferAFriend";
import FAQs from "../screens/FAQs";
import SeedsRecipes from "../screens/SeedsRecipes";
import AdminLogin from "../screens/Auth/Login";
import Register from "../screens/Auth/Register";
import { ProtectedRoutes } from "./ProtectedRoutes";
import Cart from "../screens/Cart";
import Checkout from "../screens/Checkout";
import ProductDetail from "../screens/ProductDetail";
import OrderHistory from "../screens/OrderHistory/OrderHistory";
import Privacy from "../screens/Privacy/index";
import ReturnPolicy from "../screens/ReturnPolicy/index";

import ShiipingPolicy from "../screens/ShippingPolicy/index";
import BasicInfo from "../screens/BasicInfo";
import Aboutus from "../screens/Aboutus";
import EmailPreferences from "../screens/EmailPreferences";
import AccountDetails from "../screens/AccountsDetails";
import CompletePayment from "../screens/CompletePayment/page";
import { GetProfileData } from "../redux/slices/userSlice";
import { useDispatch } from "react-redux";
import CategoryProduct from "../screens/CategoryProduct";
import TagsProduct from "../screens/TagsProduct";

import BlogsDetail from "../screens/Blogs/BlogDetail";

import ScrollToTop from "../components/ScrollToTop";

import SpecialProducts from "../screens/SpecialProducts";
import AutoShip from "../screens/Autoship";
import SearchProduct from "../screens/SearchingProduct";
import VideoLibrary from "../screens/VideoLibrary";
import LifeStyle from "../screens/LifeStyle";
import OrderHistoryDetail from "../screens/OrderHistory/OrderHistoryDetail";
import GuestCheckout from "../screens/Checkout/GuestCheckout";
import SuperFoodMixRecipes from "../screens/SuperFoodMixRecipes";
import BlogsByCategory from "../screens/Blogs/BlogsByCategory";
import Testimonial from "../screens/Testimonial/Testimonial";
import AddFeedBack from "../screens/Testimonial/AddFeedBack";
import LoyaltyLionManager from "../components/LoyaltyLionManager/LoyaltyLionManager";
import { CartContext } from "../Context/CartContext";
import ForgetPassword from "../screens/Auth/ForgetPassword";
import ForgetPassword2 from "../screens/Auth/ForgetPassword2";
// import LoyaltyLionWidget from "../components/LoyaltyLionManager/Widget";

const AppRouter = () => {
  const dispatch = useDispatch();
  const token = localStorage.getItem("login");
  useEffect(() => {
    const fetchData = async () => {
      if (token !== null) {
        dispatch(GetProfileData());
      }
    };

    fetchData();
  }, [token, dispatch]);
  const { cartItems } = useContext(CartContext);
  const user = {
    id: cartItems?.id,
    email: cartItems?.email,
  };

  return (
    <Router basename="/">
      {token && user?.id && user?.email && <LoyaltyLionManager user={user} />}
      {!token && <LoyaltyLionManager user={user} />}

      {/* <LoyaltyLionWidget /> */}
      <ScrollToTop />
      <Routes>
        <Route path="/login" element={<AdminLogin />} />
        <Route path="/register" element={<Register />} />
        <Route path="/" element={<Home />} />
        <Route path="/forget-password" element={<ForgetPassword />} />
        <Route path="/forget-password2" element={<ForgetPassword2 />} />

        <Route path="/store" element={<FuelProducts />} />
        <Route path="/special-product" element={<SpecialProducts />} />
        <Route path="/category/:id" element={<CategoryProduct />} />
        <Route path="/tag/:id" element={<TagsProduct />} />
        <Route path="/search" element={<SearchProduct />} />
        <Route path="/retailer-locations" element={<RetailerLocations />} />
        <Route path="/blogs" element={<Blogs />} />
        <Route path="/blogByCategory/:id/:name" element={<BlogsByCategory />} />
        <Route path="/videos" element={<VideoLibrary />} />
        <Route path="/life-style" element={<LifeStyle />} />
        <Route path="/blogs/:slug" element={<BlogsDetail />} />
        <Route path="/privacy" element={<Privacy />} />
        <Route path="/return_policy" element={<ReturnPolicy />} />
        <Route path="/autoship" element={<AutoShip />} />
        <Route path="/shipping_privacy" element={<ShiipingPolicy />} />
        <Route path="/contact-us" element={<Contact />} />
        <Route path="/reviews" element={<Reviews />} />
        <Route
          path="/wholesale-application"
          element={<WholesaleApplication />}
        />
        <Route path="/affiliate-marketing" element={<AffiliateMarketing />} />
        <Route path="/About" element={<Aboutus />} />
        <Route path="/testimonial" element={<Testimonial />} />
        <Route path="/submit-your-feedback" element={<AddFeedBack />} />
        <Route path="/refer-a-friend" element={<ReferAFriend />} />
        <Route path="/faq" element={<FAQs />} />
        <Route path="/OrderHistory" element={<OrderHistory />} />
        <Route path="/orderHistory/:id" element={<OrderHistoryDetail />} />
        <Route path="/store/customer_info" element={<GuestCheckout />} />
        <Route path="/BasicInfo" element={<BasicInfo />} />
        <Route path="/EmailPreferences" element={<EmailPreferences />} />
        <Route path="/AccountDetails" element={<AccountDetails />} />
        <Route path="/complete-payment" element={<CompletePayment />} />
        {/* <Route path="/seeds-recipes" element={<SeedsRecipes />} /> */}
        <Route path="/seeds-recipes" element={<SeedsRecipes />} />
        <Route path="/item/:id" element={<ProductDetail />} />
        <Route path="/recipes-superfood" element={<SuperFoodMixRecipes />} />
        <Route path="/item/:id" element={<ProductDetail />} />
        <Route path="/cart" element={<Cart />} />
        <Route
          path="/checkout"
          element={<ProtectedRoutes Components={Checkout} />}
        />
      </Routes>
    </Router>
  );
};

export default AppRouter;
