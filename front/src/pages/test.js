// componentDidMount() {
//   // Get the category name from props. This could also come from state.
//   // If no category is passed, it will be null, and your backend will fetch all products.
//   const categoryName = this.props.category || null;

//   // 1. Define the query with a variable ($category)
//   const query = `
//     query GetProductsByCategory($category: String) {
//       getProducts(category: $category) {
//         id
//         name
//         currency_symbol
//         amount
//         attributes {
//           id
//           name
//           value
//         }
//         gallery
//         in_stock
//         category {
//           name
//         }
//       }
//     }
//   `;

//   fetch("http://localhost:8000/graphql", {
//     method: "POST",
//     headers: { "Content-Type": "application/json" },
//     body: JSON.stringify({
//       query: query,
//       // 2. Pass the variables object with the category name
//       variables: {
//         category: categoryName
//       }
//     }),
//   })
//   .then((res) => res.json())
//   .then((result) => {
//     // It's good practice to check if data exists before setting state
//     if (result.data && result.data.getProducts) {
//       this.setState({
//         products: result.data.getProducts,
//       });
//     }
//   })
//   .catch(console.error);
// }