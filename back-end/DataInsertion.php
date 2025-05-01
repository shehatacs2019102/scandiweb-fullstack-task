<?php

require_once __DIR__ . '/./vendor/autoload.php';

use Dotenv\Dotenv;
use App\Database\Database;

$dotenv = Dotenv::createImmutable(__DIR__ . "/.");
$dotenv->load();
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];

function checkdataexits ()
{
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("SELECT name FROM categories WHERE id = :id");
    $stmt->execute([':id' => 1]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

if (count(checkdataexits()) < 1) 
{
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $jsonData = file_get_contents('data.json');
        $data = json_decode($jsonData, true);
        $categories = $data['data']['categories'];

        foreach ($categories as $category) 
        {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
            $stmt->execute([':name' => $category['name']]);
        }

        $products = $data['data']['products'];
                
        foreach ($products as $product) 
        {    
            $stmt = $pdo->prepare("INSERT INTO products (id, name, description, in_stock, gallery, category_id, brand) 
                                   VALUES (:id, :name, :description, :in_stock, :gallery, 
                                   (SELECT id FROM categories WHERE name = :category), :brand)");
            $stmt->execute([
                ':id' => $product['id'],
                ':name' => $product['name'],
                ':description' => $product['description'],
                ':in_stock' => $product['inStock'] ? 1 : 0,
                ':gallery' => json_encode($product['gallery']),
                ':category' => $product['category'],
                ':brand' => $product['brand']
            ]);
     
            foreach ($product['attributes'] as $attributeSet) 
            {   
                $stmt = $pdo->prepare("INSERT INTO attributes (name, type) VALUES (:name, :type)
                                       ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
                $stmt->execute([':name' => $attributeSet['name'], ':type' => $attributeSet['type']]);
                $attributeId = $pdo->lastInsertId();
    
                foreach ($attributeSet['items'] as $item) 
                {
                    $stmt = $pdo->prepare("INSERT INTO attribute_values (attribute_id, display_value, value)
                                           VALUES (:attribute_id, :display_value, :value)");
                    $stmt->execute([
                        ':attribute_id' => $attributeId,
                        ':display_value' => $item['displayValue'],
                        ':value' => $item['value']
                    ]);
                    $attributeValueId = $pdo->lastInsertId();
                    $stmt = $pdo->prepare("INSERT INTO product_attributes (product_id, attribute_value_id)
                                           VALUES (:product_id, :attribute_value_id)");
                    $stmt->execute([':product_id' => $product['id'], ':attribute_value_id' => $attributeValueId]);
                }
            }

            foreach ($product['prices'] as $price) 
            {
                $stmt = $pdo->prepare("INSERT INTO prices (product_id, amount, currency_label, currency_symbol) 
                                       VALUES (:product_id, :amount, :currency_label, :currency_symbol)");
                $stmt->execute([
                    ':product_id' => $product['id'],
                    ':amount' => $price['amount'],
                    ':currency_label' => $price['currency']['label'],
                    ':currency_symbol' => $price['currency']['symbol']
                ]);
            }
        }
        echo "Data insertion is done :)!";
    } catch (PDOException $e) {  
      echo "Error: " . $e->getMessage();
    }
}