import React, { Component } from "react";
import Header from "./components/Header";
import "./App.css";
import ProductListingPage from "./pages/ProductListingPage";
import ProductDetailsPage from "./pages/ProductDetailsPage";
import {
  BrowserRouter as Router,
  Route,
  Routes,
  Navigate,
  
} from "react-router-dom";

class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      categories: [],
      activeCategory:
        JSON.parse(localStorage.getItem("CategoryActive")) || "all",
      isCartOpen: false,
      currentProductId: JSON.parse(localStorage.getItem("CurrentID")) || null,
      cartCounter: JSON.parse(localStorage.getItem("cart-count")) || 0,
    };
  }

  handleCountChange = () => {
    const cartItems = JSON.parse(localStorage.getItem("products-stored")) || [];
    const cartCount = cartItems.reduce(
      (total, item) => total + item.quantity,
      0
    );
    localStorage.setItem("cart-count", JSON.stringify(cartCount));
    this.setState({
      cartCounter: cartCount,
    });
  };

  componentDidMount() {
    fetch("http://localhost:8000/graphql", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        query: "{ getCategories { name } }",
      }),
    })
      .then((res) => res.json())
      .then((data) =>
        this.setState({
          categories: data.data.getCategories,
        })
      )
      .catch((error) => {
        console.error("Error fetching categories:", error);
      });
  }

  handleCategorySelect = (category) => {
    this.setState({ activeCategory: category });
    localStorage.setItem("CategoryActive", JSON.stringify(category));
  };

  toggleCart = () => {
    this.setState({ isCartOpen: !this.state.isCartOpen });
  };
  toggleProduct = (currentProductId) => {
    this.setState({ currentProductId });
    localStorage.setItem("CurrentID", JSON.stringify(currentProductId));
  };

  render() {
    return (
      <>
        <Router>
          <Header
            categories={this.state.categories}
            activeCategory={this.state.activeCategory}
            onCategorySelect={this.handleCategorySelect}
            onCartOpen={this.toggleCart}
            isCartOpen={this.state.isCartOpen}
            handleCountChange={this.handleCountChange}
            cartCounter={this.state.cartCounter}
          />

          <Routes>
            <Route path="/" element={<Navigate to="/all" />} />
            <Route
              path="/:category"
              exact
              element={
                <ProductListingPage
                  activeCategory={this.state.activeCategory}
                  toggleProduct={this.toggleProduct}
                  isCartOpen={this.state.isCartOpen}
                  onCartOpen={this.toggleCart}
                  handleCountChange={this.handleCountChange}
                />
              }
            />

            <Route
              path="/:category/product/:id"
              exact
              element={
                <ProductDetailsPage
                  currentProductId={this.state.currentProductId}
                  isCartOpen={this.state.isCartOpen}
                  onCartOpen={this.toggleCart}
                  handleCountChange={this.handleCountChange}
                />
              }
            />
          </Routes>
        </Router>
      </>
    );
  }
}

export default App;
