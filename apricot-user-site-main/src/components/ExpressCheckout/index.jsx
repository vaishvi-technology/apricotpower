/* eslint-disable react-hooks/exhaustive-deps */
/* eslint-disable react/prop-types */
import { useEffect, useState } from "react";
import { Card, Row, Col, Button } from "react-bootstrap";
import ExpressModal from "../CustomModal/ExpressModal";
import CustomInput from "../CustomInput";
import { SelectBox } from "../CustomSelect";
import { getProduct } from "../../api/Services/productServices";
import { addCart, getCart } from "../../api/Services/cartServices";
import CustomButton from "../CustomButton";
import { toast } from "react-toastify";
import { useNavigate } from "react-router-dom";

export default function ExpressCheckout({ show, setshow }) {
  const [products, setProducts] = useState([]);
  const [filteredProducts, setFilteredProducts] = useState([]);
  const [selectedItems, setSelectedItems] = useState([]);
  const [loading, setloading] = useState(false);
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("All");
  const [sortOrder, setSortOrder] = useState("");

  const categoriesoption = [{ id: "All", name: "All" }];
  const sortbyoption = [
    { id: "Price Low to High", name: "Price Low to High" },
    { id: "Price High to Low", name: "Price High to Low" },
  ];
  const [categoryOptions, setCategoryOptions] = useState(categoriesoption);

  const productQuery = async () => {
    try {
      const response = await getProduct();
      setProducts(response);
      setFilteredProducts(response);

      const dynamicCategories = [
        { id: "All", name: "All" },
        ...response.map((cat) => ({
          id: cat.category_title,
          name: cat.category_title,
        })),
      ];
      setCategoryOptions(dynamicCategories);
    } catch (error) {
      console.log(error);
    }
  };

  const cartQuery = async () => {
    try {
      const response = await getCart();
      const cartItems = response?.items || [];

      const initialSelected = cartItems.map((item) => ({
        product_id: item.id,
        checked: true,
        qty: item.qty,
      }));
      setSelectedItems(initialSelected);
    } catch (error) {
      console.log(error);
    }
  };

  useEffect(() => {
    productQuery();
    cartQuery();
  }, []);

  useEffect(() => {
    filterProducts();
  }, [searchTerm, selectedCategory, sortOrder, products]);

  const filterProducts = () => {
    let filtered = [...products];

    if (selectedCategory !== "All") {
      filtered = filtered.filter(
        (cat) => cat.category_title === selectedCategory
      );
    }

    if (searchTerm) {
      filtered = filtered
        .map((cat) => ({
          ...cat,
          products: cat.products.filter((p) =>
            p.product_name.toLowerCase().includes(searchTerm.toLowerCase())
          ),
        }))
        .filter((cat) => cat.products.length > 0);
    }

    if (sortOrder) {
      filtered = filtered.map((cat) => ({
        ...cat,
        products: [...cat.products].sort((a, b) =>
          sortOrder === "Price Low to High"
            ? a.map_price - b.map_price
            : b.map_price - a.map_price
        ),
      }));
    }

    setFilteredProducts(filtered);
  };

  const findProduct = (product_id) =>
    selectedItems.find((item) => item.product_id === product_id);

  const toggleProduct = (product_id) => {
    setSelectedItems((prev) => {
      const found = findProduct(product_id);
      if (found) {
        return prev.map((item) =>
          item.product_id === product_id
            ? { ...item, checked: !item.checked }
            : item
        );
      } else {
        return [...prev, { product_id, checked: true, qty: 1 }];
      }
    });
  };

  const updateQty = (product_id, qty) => {
    if (qty < 1) return;
    setSelectedItems((prev) => {
      const found = findProduct(product_id);
      if (found) {
        return prev.map((item) =>
          item.product_id === product_id ? { ...item, qty } : item
        );
      } else {
        return [...prev, { product_id, checked: false, qty }];
      }
    });
  };

  const incrementQty = (product_id) => {
    const item = findProduct(product_id);
    updateQty(product_id, (item?.qty || 0) + 1
  
  );
  };

  const decrementQty = (product_id) => {
    const item = findProduct(product_id);
    if (item?.qty > 1) {
      updateQty(product_id, item.qty - 1);
    }
  };

  const HighlightText = ({ text, highlight }) => {
    if (!highlight) return <>{text}</>;
    const regex = new RegExp(`(${highlight})`, "gi");
    const parts = text.split(regex);
    return (
      <>
        {parts.map((part, index) =>
          part.toLowerCase() === highlight.toLowerCase() ? (
            <mark
              key={index}
              style={{ backgroundColor: "#f7c13e", fontWeight: "bold" }}
            >
              {part}
            </mark>
          ) : (
            <span key={index}>{part}</span>
          )
        )}
      </>
    );
  };
  const navigate = useNavigate();

  const addToCart = async () => {
    setloading(true);
    try {
      const finaldata = {
        items: selectedItems,
      };
      const response = await addCart(finaldata);
      if (response?.status == 200) {
        // toast.success(response?.message);
        navigate("/cart");
        setloading(false);
        productQuery();
        setshow(false);
      } else {
        setloading(false);
        toast.error(response?.message);
        setshow(false);
      }
    } catch (error) {
      setshow(false);
      setloading(false);
      toast.error(error?.message);
    }
  };
  return (
    <ExpressModal
      show={show}
      heading={"Quick Checkout"}
      close={() => setshow(false)}
      size="xl"
    >
      <div className="container my-4">
        <Row className="g-2 mb-4">
          <Col>
            <SelectBox
              selectClass="mainInput"
              name="categories"
              label="Select Categories"
              option={categoryOptions}
              value={selectedCategory}
              onChange={(e) => setSelectedCategory(e.target.value)}
            />
          </Col>
          <Col>
            <SelectBox
              selectClass="mainInput"
              name="sort"
              label="Sort By"
              option={sortbyoption}
              value={sortOrder}
              onChange={(e) => setSortOrder(e.target.value)}
            />
          </Col>
          <Col>
            <CustomInput
              label="Search Product"
              type="text"
              placeholder="Search anything..."
              labelClass="mainLabel"
              inputClass="mainInput"
              name="search"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </Col>
        </Row>

        <div className="text-end">
          <CustomButton
            onClick={() => addToCart()}
            text={loading ? "Save Changes..." : "Save Changes"}
            variant="secondaryButton"
          />
        </div>

        {filteredProducts?.map((category) => (
          <div key={category.category_title}>
            <h5 className="mb-3">{category?.category_title}</h5>
            {category?.products?.map((product, idx) => {
              const selected = findProduct(product.id);
              return (
                <Card
                  key={product?.id}
                  className="mb-3 p-3 d-flex flex-row align-items-center"
                >
                  {/* <div className="form_check_boxes mt-2">
                    <div className="form-group">
                      <input
                        type="checkbox"
                        id={`store_type${idx}`}
                        checked={selected?.checked || false}
                        onChange={() => toggleProduct(product.id)}
                      />
                      <label htmlFor={`store_type${idx}`} className="mt-2" />
                    </div>
                  </div> */}

                  <img
                    src={product?.image}
                    alt="product"
                    className="me-3"
                    width="60"
                  />

                  <div className="flex-grow-1">
                    <h6 className="mb-1 fw-semibold">
                      <HighlightText
                        text={product?.product_name}
                        highlight={searchTerm}
                      />
                    </h6>

                    <div className="mt-2 d-flex align-items-center gap-2">
                      <label className="me-2">Quantity</label>
                      <Button
                        variant="outline-danger"
                        size="sm"
                        onClick={() => decrementQty(product.id)}
                      >
                        -
                      </Button>
                      <input
                        type="text"
                        min={1}
                        className="form-control d-inline-block text-center me-1"
                        style={{ width: "50px" }}
                        value={selected?.qty || 0}
                        onChange={(e) =>
                          updateQty(product.id, parseInt(e.target.value))
                        }
                      />
                      <Button
                        variant="outline-success"
                        size="sm"
                        onClick={() => incrementQty(product.id)}
                      >
                        +
                      </Button>
                    </div>
                  </div>

                  <div className="fw-bold">${product?.map_price}</div>
                </Card>
              );
            })}
          </div>
        ))}
      </div>
    </ExpressModal>
  );
}
