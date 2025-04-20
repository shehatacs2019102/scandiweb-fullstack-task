import React, { Component } from "react";
import "./AttributesStyle.css";
export default class ColorAttributes extends Component {
  constructor(props) {
    super(props);
    this.state = {};
  }

  render() {
    const {attributes,header, handleChoiceClick, headerIndex, selectedChoices} = this.props;
    return (
      <>
        <div key={headerIndex} className="attribute-tag">
          {header}
        </div>

        <div className="attributes-variations">
          {attributes
            .filter((attribute) => attribute.name === header)
            .map((attribute, index) => (
              <div
                className={`preference ${
                  selectedChoices[header] === attribute.value
                    ? "active-color"
                    : ""
                }`}
                style={{
                  backgroundColor: attribute.value,
                }}
                key={attribute.value}
                onClick={() => handleChoiceClick(header, attribute.value)}
                data-testid={`product-attribute-${header
                  .toLowerCase()
                  .replaceAll(" ", "-")}-${attribute.value}`}
              >
               
              </div>
            ))}
        </div>
      </>
    );
  }
}
