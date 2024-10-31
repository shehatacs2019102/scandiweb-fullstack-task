<?php



namespace App\Controller;
  

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;
use App\Database\Database;

class GraphQL {

    private static $db;
    
    
    public static function init() {
        self::$db = (new Database())->getConnection();
    }

    public static function handle() {
        self::init();

        try {
            
            $productAttributeType = new ObjectType([
                'name' => 'ProductAttribute',
                'fields' => [
                    'id' => Type::nonNull(Type::id()),
                    'name' => Type::nonNull(Type::string()),
                    'value' => Type::string(), 
                ]
            ]);

            $CategoryType = new ObjectType([
                'name' => 'ProductCategory',
                    'fields' => [
                    'name' => Type::nonNull(Type::string())
                    ]
            ]);

            $productType = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => Type::nonNull(Type::id()),
                    'name' => Type::nonNull(Type::string()),
                    'description' => Type::string(),
                    'gallery' => Type::string(),
                    'amount' => Type::float(),
                    'currency_label' => Type::string(),
                    'currency_symbol' => Type::string(),
                    'in_stock' => Type::boolean(),
                    'brand' => Type::string(),
                    'attributes' => [
                        'type' => Type::listOf($productAttributeType),
                        'resolve' => function ($product) {
                            $stmt = self::$db->prepare("
                               SELECT a.id, a.name, av.value 
                               FROM product_attributes pa
                               JOIN attribute_values av ON pa.attribute_value_id = av.id
                               JOIN attributes a ON av.attribute_id = a.id
                               WHERE pa.product_id = :product_id
                            ");
                            $stmt->execute([':product_id' => $product['id']]);
                            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        }
                    ],
                    'category' => [
                        'type' => Type::listOf(Type::string($CategoryType)),
                        'resolve' => function ($product) {
                            $stmt = self::$db->prepare("SELECT name FROM categories WHERE id = :id");
                            $stmt->execute([':id' => $product['category_id']]);
                            return $stmt->fetch(\PDO::FETCH_ASSOC);
                        }
                    ],
                ]
                
            ]);

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'getCategories' => [
                        'type' => Type::listOf(new ObjectType([
                            'name' => 'Category',
                            'fields' => [
                                'id' => Type::nonNull(Type::id()),
                                'name' => Type::nonNull(Type::string()),
                            ],
                        ])),
                        'resolve' => function () {
                            $stmt = self::$db->query("SELECT * FROM categories");
                            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        }
                    ],
                    'getProducts' => [
                        'type' => Type::listOf($productType),
                        'resolve' => function () {
                            $stmt = self::$db->query("
                                SELECT p.id, p.name, p.description, p.in_stock, p.gallery, p.brand, 
                                       pr.amount, pr.currency_label, pr.currency_symbol, 
                                       p.category_id
                                FROM products p
                                LEFT JOIN prices pr ON p.id = pr.product_id
                            ");
                            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        }
                    ],
                    'getProductById' => [
                        'type' => $productType,
                        'args' => [
                            'id' => ['type' => Type::nonNull(Type::id())]
                        ],
                        'resolve' => function ($root, $args) {
                            $stmt = self::$db->prepare("
                                SELECT p.id, p.name, p.description, p.in_stock, 
                                       p.gallery, p.brand, 
                                       COALESCE(pr.amount, 0) as amount, 
                                       COALESCE(pr.currency_label, 'N/A') as currency_label, 
                                       COALESCE(pr.currency_symbol, '-') as currency_symbol,
                                       p.category_id
                                FROM products p
                                LEFT JOIN prices pr ON p.id = pr.product_id
                                WHERE p.id = :id
                            ");
                            $stmt->execute([':id' => $args['id']]);
                            return $stmt->fetch(\PDO::FETCH_ASSOC);
                        }
                    ],
                ],
            ]);
            $orderType = new ObjectType([
                'name' => 'Order',
                'fields' => [
                    'id' => ['type' => Type::nonNull(Type::int())],
                    'items' => Type::nonNull(Type::string()),
                    
                    'total_price' => ['type' => Type::nonNull(Type::float())],
                    // ISO 8601 date format
                ],
            ]);

            

            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'createOrder' => [
                        'type' => $orderType,
                        'args' => [
                            'items' => Type::nonNull(Type::string()),
                            
                            'total_price' => ['type' => Type::nonNull(Type::float())],
                        ],
                        'resolve' => function ($root, $args)   {
                            try {
                                $db = new \PDO("mysql:host=127.0.0.1;dbname=shop", 'root', 'password');
                                $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                                // Prepare and execute the insert statement
                                $stmt = $db->prepare('INSERT INTO orders (items, total_price) VALUES (?, ?)');
                                $stmt->execute([$args['items'], $args['total_price']]);

                                
                                
                                // return $order;
                            } catch (\PDOException $e) {
                                error_log("Database error: " . $e->getMessage());
                                return ['error' => 'Database error'];
                            } catch (\Exception $e) {
                                error_log("General error: " . $e->getMessage());
                                return ['error' => 'An unexpected error occurred'];
                            }
                        },
                    ],
                ],
            ]);
    
            $schema = new Schema(
                (new SchemaConfig())
                    ->setQuery($queryType)
                    ->setMutation($mutationType)
            );

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false || empty($rawInput)) {
                throw new RuntimeException('No GraphQL query was provided in the request body.');
            }

            $input = json_decode($rawInput, true);
            if (!isset($input['query'])) {
                throw new RuntimeException('No GraphQL query found in the request.');
            }

            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

      
            $result = GraphQLBase::executeQuery($schema, $query, null, null, $variableValues);
            $output = $result->toArray();

        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

    
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($output);
    }
}
