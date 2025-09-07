<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Queries\AllProductsQuery;
use App\Controller\Queries\CategoryQuery;
use App\Controller\Queries\ProductByIDQuery;
use App\Controller\Types\AttributeType;
use App\Controller\Types\CategoryType;
use App\Controller\Types\OrderType;
use App\Controller\Types\ProductType;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;


class GraphQL
{
    
    public static function handle(): void
    {
        try {
            $productAttributeType = (new AttributeType())->init();
            $categoryType = (new CategoryType())->init();
            $orderType = (new OrderType())->init();
            $productType = (new ProductType($productAttributeType, $categoryType))->init();

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'getCategories' => (new CategoryQuery($categoryType))->init(),
                    'getProducts' => (new AllProductsQuery($productType))->init(),
                    'getProductById' => (new ProductByIDQuery($productType))->init(),
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
                        'resolve' => function ($root, $args) {
                            try {
                                $host = $_ENV['DB_HOST'];
                                $dbname = $_ENV['DB_NAME'];
                                $user = $_ENV['DB_USER'];
                                $pass = $_ENV['DB_PASS'];
                                $db = new PDO("mysql:host=$host;dbname=$dbname", "$user", "$pass");
                                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                $stmt = $db->prepare('INSERT INTO orders (items, total_price) VALUES (?, ?)');
                                $stmt->execute([$args['items'], $args['total_price']]);

                                $orderId = $db->lastInsertId();
                                if (!$orderId) {
                                    throw new RuntimeException('Failed to retrieve last insert ID');
                                }

                                $stmtproductfetch = $db->prepare('SELECT id, items, total_price FROM orders WHERE id = ?');
                                $stmtproductfetch->execute([$orderId]);
                                return $stmtproductfetch->fetch(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                error_log("Database error: " . $e->getMessage());
                                throw new \Exception('Database error');
                            } catch (\Exception $e) {
                                error_log("General error: " . $e->getMessage());
                                throw new \Exception('An unexpected error occurred');
                            }
                        },
                    ],
                ],
            ]);

            $schemaConfig = new SchemaConfig();
            $schemaConfig->setQuery($queryType);
            $schemaConfig->setMutation($mutationType);
            $schema = new Schema($schemaConfig);

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