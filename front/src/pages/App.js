import React, { Component } from 'react';
import Header from './components/Header';
import './App.css';
import ProductListingPage from './pages/ProductListingPage';
import ProductDetailsPage from'./pages/ProductDetailsPage';
import { BrowserRouter as Router, Route, Routes,Navigate, json } from 'react-router-dom';

class App extends Component {
  constructor(props) {
    super(props);
    this.state = {
      categories: [],
      activeCategory: 'all',
      isCartOpen: false,
      currentProductId:JSON.parse(localStorage.getItem('CurrentID')) || null,
      
    };
  }


  componentDidMount() {

    

    fetch('http://localhost:8000/graphql', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
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
        console.error('Error fetching categories:', error);
      });
      
  }

  
  handleCategorySelect = (category) => {
    this.setState({ activeCategory: category });
    
  };

  toggleCart = () => {
   
    
      this.setState({ isCartOpen: !this.state.isCartOpen });
    
    
  };
  toggleProduct=(currentProductId)=>{
    this.setState({currentProductId})
    localStorage.setItem('CurrentID', JSON.stringify(currentProductId))
    
  }
  
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
        />

          <Routes>
                <Route path="/" element={<Navigate to="/all" />} />
                <Route path="/:category" exact 
                       element={<ProductListingPage activeCategory={this.state.activeCategory} 
                                                                          toggleProduct={this.toggleProduct} 
                                                                          isCartOpen={this.state.isCartOpen}
                                                                          onCartOpen={this.toggleCart}/>}/>

                <Route path="/:category/product/:id"  exact 
                       element={<ProductDetailsPage currentProductId={this.state.currentProductId}
                                                    isCartOpen={this.state.isCartOpen}
                                                    onCartOpen={this.toggleCart}
                                                      />}/>
          </Routes>
      
        </Router>

      </>

    );

  }
  
}

export default App;
