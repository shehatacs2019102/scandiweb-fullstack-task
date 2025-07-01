import React, { Component } from "react";
import "../pages/ProductListingPageStyle.css";
import { Link } from "react-router-dom";
export default class OutStockItem extends Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  render() {
    const { key, index, item, toggleProduct } = this.props;
    
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
        <div
        id={key}
        data-testid={`product-${item.id}`}
        className={"product-item-outStock"}
      >
        
          <div className="out-of-stock">
            <h2>Out Of Stock</h2>
          </div>

          <img
            src={item.gallery
              .split(" ")
              .map((url) => url.trim())[0]
              .replace(/[[\]"]/g, "")
              .slice(0, -1)}
            alt={key}
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
      </div>
      
    );
  }
}
