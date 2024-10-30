  import React, { Component } from 'react';
  import './ProductListingPageStyle.css'
  import { Link } from 'react-router-dom';
  import EmptyCart from '../Assets/emptycart.svg'



  class ProductListingPage extends Component {

    constructor(props) {
      super(props);
      this.state = {
        products: [],
      };
    }
    
    componentDidMount() {
  
      fetch('http://shehatacs322.serv00.net:54767/graphql', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
          query: "{ getProducts { id name currency_symbol amount attributes { id name value } gallery in_stock category { name } } }"
        })
      }) 
        .then(res => res.json())
        .then( data =>
          this.setState({
            products: data.data.getProducts
    })
  
  )     
  
  
    }


    handleQuickShop = (index) => {
      const { products } = this.state;
      const storedProducts = JSON.parse(localStorage.getItem("products-stored")) || [];
    
      const product = products[index];
    
      // Create a unique default selection by using the first value of each attribute.
      const defaultSelectedChoices = {};
      [...product.attributes].reverse().forEach(attr => {
        if (attr) {
          // Select the first item as the default value for each attribute.
          defaultSelectedChoices[attr.name] = attr.value;
        }
      }); 

      const newProduct = {
        tag: product.name || '',
        price: product.amount || 0, 
        symbol: product.currency_symbol || '',
        gallery: product.gallery || [],
        quantity: 1,
        attributes: product.attributes || [],
        attributeNames: product.attributes.map(attr => attr.name).filter((value, index, array) => array.indexOf(value) === index) || [],
        "selected-choices": defaultSelectedChoices,
      };
    
      // Check if a similar product already exists in local storage.
      const productIndex = storedProducts.findIndex(
        item =>
          item.tag === newProduct.tag &&
          JSON.stringify(item["selected-choices"]) === JSON.stringify(newProduct["selected-choices"])
      );
    
      // If the product exists, increment quantity; otherwise, add it to the storage.
      if (productIndex !== -1) {
        storedProducts[productIndex].quantity += 1;
      } else {
        storedProducts.push(newProduct);
      }
    
      // Save the updated cart back to local storage.
      localStorage.setItem("products-stored", JSON.stringify(storedProducts));
    };
    
    render() {

      const {activeCategory,toggleProduct,isCartOpen}=this.props

      return (
        
        <div className={`main-listing-page ${isCartOpen ? 'disabled' : ''}`}
            style={{
              opacity: isCartOpen ? 0.5 : 1,
              pointerEvents: isCartOpen ? 'none' : 'auto',
                
              }}>


              <h1 className='active-category'>{activeCategory.toUpperCase()}</h1>

              <div className="product-list">


              {activeCategory==="all"?this.state.products.map((product,index)=>(

                      product.in_stock?
                      <div className='product-card'>
                      <Link key={index} 
                            to={`product/${product.id}`} 
                            onClick={()=>{toggleProduct(product.id)}}  
                            className='link'
                            data-testid={`product-${product.name.toLowerCase().replaceAll(" ","-")}`}>

                          <div  id={product.id} className={product.in_stock ? 'product-item-inStock' : 'product-item-outStock'} >

                          <div className='out-of-stock'>

                                  <h2>Out Of Stock</h2>

                          </div>

                          <img src={product.gallery.split(' ').map(url => url.trim())[0].replace(/[[\]"]/g, '').slice(0, -1)} alt={product.id} className='product-image'/>
                    
                          <p className='product-attributes'> {product['name']}<br/><b>{product.currency_symbol}{product.amount}</b></p>

                          </div>
                          

                      </Link>
                      <button className='quick-shop' onClick={()=>{this.handleQuickShop(index)}}>
                              <img src={EmptyCart} alt='quick-shop'/>
                          </button>
                      </div>
            : <div  id={product.id} data-testid ={`product-${product.id}`} className={product.in_stock ? 'product-item-inStock' : 'product-item-outStock'} >

              <div className='out-of-stock'>

                  <h2>Out Of Stock</h2>

              </div>

              <img src={product.gallery.split(' ').map(url => url.trim())[0].replace(/[[\]"]/g, '').slice(0, -1)} alt={product.id} className='product-image'/>
            
              <p className='product-attributes'> {product['name']}<br/><b>{product.currency_symbol}{product.amount}</b></p>
              
              </div>
          
        ))
          : this.state.products.filter((product)=>(product.category['name']===activeCategory)).map((product,index)=>(

              product.in_stock?
              <div className='product-card'>
              <Link key={index} 
                    to={`product/${product.id}`} 
                    onClick={()=>{toggleProduct(product.id)}}  
                    className='link'
                    data-testid={`product-${product.name.toLowerCase().replaceAll(" ","-")}`}>  
              
                  <div  id={product.id} className={product.in_stock ? 'product-item-inStock' : 'product-item-outStock'} >
              
                          <div className='out-of-stock'>
                                    
                                <h2>Out Of Stock</h2>
                          
                          </div>
            
                  <img src={product.gallery.split(' ').map(url => url.trim())[0].replace(/[[\]"]/g, '').slice(0, -1)} alt={product.id} className='product-image'/>
            
                  <p className='product-attributes'> {product['name']}<br/><b>{product.currency_symbol}{product.amount}</b></p>
                  
                  </div>
                
              </Link>
                <button className='quick-shop'>
                <img src={EmptyCart} alt='quick-shop'/>
            </button>
        </div>

          : <div  id={product.id} data-testid ={`product-${product.name.toLowerCase().replaceAll(" ","-")}`} className={product.in_stock ? 'product-item-inStock' : 'product-item-outStock'} >
              
                <div className='out-of-stock'>
                
                    <h2>Out Of Stock</h2>
              
                </div>
            
            <img src={product.gallery.split(' ').map(url => url.trim())[0].replace(/[[\]"]/g, '').slice(0, -1)} alt={product.id} className='product-image'/>
            
            <p className='product-attributes'> {product['name']}<br/><b>{product.currency_symbol}{product.amount}</b></p>
            
            
          </div>
          ))}
      
        </div>
      
      </div>  
        
      );
    }
  }
  
  export default ProductListingPage;