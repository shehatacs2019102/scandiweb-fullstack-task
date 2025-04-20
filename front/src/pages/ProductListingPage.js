import React, { Component } from "react";
import "./ProductListingPageStyle.css";
import InStockItem from "../components/InStockItem";
import OutStockItem from "../components/OutStockItem";

class ProductListingPage extends Component {
  constructor(props) {
    super(props);
    this.state = {
      products: [],
    };
  }

  componentDidMount() {
    fetch("http://localhost:8000/graphql", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        query:
          "{ getProducts { id name currency_symbol amount attributes { id name value } gallery in_stock category {name} } }",
      }),
    })
      .then((res) => res.json())
      .then((data) =>
        this.setState({
          products: data.data.getProducts,
        })
      );
  }

  handleDisplayProduct = () => {
    const { activeCategory, toggleProduct } = this.props;
    const { products } = this.state;
    let productInCategory = [];
    switch (activeCategory) {
      case "all":
        productInCategory = products;
        break;
      default:
        productInCategory = products.filter((product) => {
          return product.category?.name === activeCategory;
        });
        break;
    }
    return productInCategory.map((item, i) => {
      return item.in_stock ? (
        <InStockItem
          key={item.id}
          toggleProduct={toggleProduct}
          item={item}
          index={i}
          handleQuickShop={this.handleQuickShop}
        />
      ) : (
        <OutStockItem key={item.id} index={i} item={item} />
      );
    });
  };

  handleQuickShop = (id) => {
    const { onCartOpen } = this.props;

    const { products } = this.state;
    const storedProducts =
      JSON.parse(localStorage.getItem("products-stored")) || [];

    // const product = products[index];
    const [product] = products.filter((product) => {
      return product["id"] === id;
    });

    // Create a unique default selection by using the first value of each attribute.
    const defaultSelectedChoices = {};
    [...product.attributes].reverse().forEach((attr) => {
      if (attr) {
        // Select the first item as the default value for each attribute.
        defaultSelectedChoices[attr.name] = attr.value;
      }
    });

    const newProduct = {
      tag: product.name || "",
      price: product.amount || 0,
      symbol: product.currency_symbol || "",
      gallery: product.gallery || [],
      quantity: 1,
      attributes: product.attributes || [],
      attributeNames:
        product.attributes
          .map((attr) => attr.name)
          .filter((value, index, array) => array.indexOf(value) === index) ||
        [],
      "selected-choices": defaultSelectedChoices,
    };

    // Check if a similar product already exists in local storage.
    const productIndex = storedProducts.findIndex(
      (item) =>
        item.tag === newProduct.tag &&
        JSON.stringify(item["selected-choices"]) ===
          JSON.stringify(newProduct["selected-choices"])
    );

    // If the product exists, increment quantity; otherwise, add it to the storage.
    if (productIndex !== -1) {
      storedProducts[productIndex].quantity += 1;
    } else {
      storedProducts.push(newProduct);
    }

    // Save the updated cart back to local storage.
    localStorage.setItem("products-stored", JSON.stringify(storedProducts));

    onCartOpen();
  };

  render() {
    const { activeCategory, isCartOpen } = this.props;

    return (
      <div
        className={`main-listing-page ${isCartOpen ? "disabled" : ""}`}
        style={{
          opacity: isCartOpen ? 0.5 : 1,
          pointerEvents: isCartOpen ? "none" : "auto",
        }}
      >
        <h1 className="active-category">{activeCategory.toUpperCase()}</h1>

        <div className="product-list">{this.handleDisplayProduct()}</div>
      </div>
    );
  }
}

export default ProductListingPage;
