import React, { Component } from "react";
import { Link } from "react-router-dom";
import '../pages/ProductListingPageStyle.css'
import EmptyCart from "../Assets/emptycart.svg";
export default class InStockItem extends Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  render() {
    const { item, toggleProduct, index, handleQuickShop } = this.props;
    return (
      <div className="product-card">
        <Link
          key={index}
          to={`product/${item.id}`}
          onClick={() => {
            toggleProduct(item.id);
          }}
          className="link"
          data-testid={`product-${item.name
            .toLowerCase()
            .replaceAll(" ", "-")}`}
        >
          <div id={item.id} className={"product-item-inStock"}>
            <img
              src={item.gallery
                .split(" ")
                .map((url) => url.trim())[0]
                .replace(/[[\]"]/g, "")
                .slice(0, -1)}
              alt={item.id}
              className="product-image"
            />

            <p className="product-attributes">
              {" "}
              {item["name"]}
              <br />
              <b>
                {item.currency_symbol}
                {item.amount}
              </b>
            </p>
          </div>
        </Link>
        <button
          className="quick-shop"
          onClick={() => {
            handleQuickShop(item.id);
          }}
        >
          <img src={EmptyCart} alt="quick-shop" />
        </button>
      </div>
    );
  }
}
