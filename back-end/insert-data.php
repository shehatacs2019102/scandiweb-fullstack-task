<?php

$host = '127.0.0.1';  
$dbname = 'shop';    
$user = 'root';       
$pass = 'password123';          

try {
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $jsonData = file_get_contents('data.json');
    $data = json_decode($jsonData, true);  
    
    $categories = $data['data']['categories'];
    foreach ($categories as $category) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute([':name' => $category['name']]);
    }

   
    $products = $data['data']['products'];
    foreach ($products as $product) {
        
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

        
        foreach ($product['attributes'] as $attributeSet) {
           
            $stmt = $pdo->prepare("INSERT INTO attributes (name, type) VALUES (:name, :type)
                                   ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
            $stmt->execute([':name' => $attributeSet['name'], ':type' => $attributeSet['type']]);
            $attributeId = $pdo->lastInsertId();

            
            foreach ($attributeSet['items'] as $item) {
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

        
        foreach ($product['prices'] as $price) {
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

    echo "Data inserted successfully!";
    
} catch (PDOException $e) {
    
    echo "Error: " . $e->getMessage();
}
