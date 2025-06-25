<?php
include 'db.php';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$product_id = intval($_GET['id']);

// Fetch product details
$product_query = "SELECT * FROM products WHERE id = $product_id";
$product_result = $conn->query($product_query);
$product = $product_result->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit();
}

// Process form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $quantity = intval($_POST['quantity']);
    $payment_method = $_POST['payment_method'];
    $notes = trim($_POST['notes']);
    
    // Validate inputs
    if (empty($name)) {
        $errors[] = "Numele este obligatoriu";
    }
    
    if (empty($phone)) {
        $errors[] = "Telefonul este obligatoriu";
    } elseif (!preg_match('/^[0-9]{9,15}$/', $phone)) {
        $errors[] = "Telefonul nu este valid";
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Emailul nu este valid";
    }
    
    if (empty($address)) {
        $errors[] = "Adresa este obligatorie";
    }
    
    if ($quantity < 1 || $quantity > 10) {
        $errors[] = "Cantitatea trebuie să fie între 1 și 10";
    }
    
    if (empty($payment_method)) {
        $errors[] = "Metoda de plată este obligatorie";
    }
    
    // If no errors, process order
    if (empty($errors)) {
        $total_price = $quantity * $product['pret'];
        $order_date = date('Y-m-d H:i:s');
        
        $insert_query = "INSERT INTO orders (product_id, name, phone, email, address, quantity, total_price, payment_method, notes, order_date)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("issssiisss", $product_id, $name, $phone, $email, $address, $quantity, $total_price, $payment_method, $notes, $order_date);
        
        if ($stmt->execute()) {
            $success = true;
            
            // Here you could also send an email notification
            // or integrate with a payment gateway
        } else {
            $errors[] = "A apărut o eroare la procesarea comenzii. Vă rugăm să încercați din nou.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cumpără <?php echo htmlspecialchars($product['firma'] . ' ' . $product['model']); ?> | Top Climat</title>
    <meta name="description" content="Comandă <?php echo htmlspecialchars($product['firma'] . ' ' . $product['model']); ?> - condiționare de calitate la prețuri competitive.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .buy-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        .product-summary {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .order-form {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 100px;
        }
        
        .btn-submit {
            background-color: #0066cc;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #0052a3;
        }
        
        .error-message {
            color: #d9534f;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background-color: #f8d7da;
            border-radius: 4px;
        }
        
        .success-message {
            color: #28a745;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background-color: #d4edda;
            border-radius: 4px;
        }
        
        @media (max-width: 768px) {
            .buy-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">
                <a href="index.php" class="logo-text">Top<span>Climat</span></a>
            </div>
            <nav class="nav">
                <a href="index.php"><i class="fas fa-home"></i> Acasă</a>
                <a href="servicii.php"><i class="fas fa-cogs"></i> Servicii</a>
                <a href="tel:+373-61-141-157" class="call-now"><i class="fas fa-phone-alt"></i> Sună acum</a>
            </nav>
        </div>
    </header>

    <main class="buy-container">
    <div class="product-summary">
        <div class="product-card">
            <div class="product-badge">În stoc</div>
            <div class="product-image">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['model']); ?>">
            </div>
            
            <div class="product-content">
                <h1><?php echo htmlspecialchars($product['firma'] . ' ' . $product['model']); ?></h1>
                
                <div class="product-specs">
                    <div class="spec-item">
                        <i class="fas fa-bolt"></i>
                        <span><?php echo htmlspecialchars($product['btu']); ?> BTU</span>
                    </div>
                    <div class="spec-item">
                        <i class="fas fa-vector-square"></i>
                        <span><?php echo htmlspecialchars($product['mp']); ?> mp</span>
                    </div>
                    <div class="spec-item">
                        <i class="fas fa-battery-three-quarters"></i>
                        <span>Clasă <?php echo htmlspecialchars($product['clasa_energetica']); ?></span>
                    </div>
                </div>
                
                <div class="product-pricing">
                    <div class="price"><?php echo htmlspecialchars($product['pret']); ?> MDL</div>
                    <div class="tax-info">+ TVA inclus</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="order-form-container">
        <?php if ($success): ?>
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>Comandă confirmată!</h2>
                <p>Vă mulțumim pentru comandă. Vă vom contacta în cel mai scurt timp pentru confirmare.</p>
                <a href="index.php" class="btn-back">Înapoi la produse</a>
            </div>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="error-card">
                    <div class="error-header">
                        <i class="fas fa-exclamation-circle"></i>
                        <h3>Eroare la completare</h3>
                    </div>
                    <ul class="error-list">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="form-card">
                <h2><i class="fas fa-truck"></i> Date de livrare</h2>
                <form method="POST" action="https://formspree.io/f/mldbolkq">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['firma'] . ' ' . $product['model']); ?>">
                    <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product['pret']); ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nume*</label>
                            <input type="text" id="name" name="name" placeholder="Introduceți numele" required>
                        </div>
                        <div class="form-group">
                            <label for="surname">Prenume*</label>
                            <input type="text" id="surname" name="surname" placeholder="Introduceți prenumele" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Telefon*</label>
                            <input type="tel" id="phone" name="phone" placeholder="06xxxxxxxx" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email (opțional)</label>
                            <input type="email" id="email" name="email" placeholder="exemplu@email.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Adresă completă*</label>
                        <textarea id="address" name="address" placeholder="Strada, număr, localitate, raion" required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="quantity">Cantitate*</label>
                            <select id="quantity" name="quantity" required>
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?> buc</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="payment-methods">
                        <h3><i class="fas fa-credit-card"></i> Metodă de plată</h3>
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="Numerar la livrare" checked>
                                <div class="payment-content">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Numerar la livrare</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="Transfer bancar">
                                <div class="payment-content">
                                    <i class="fas fa-university"></i>
                                    <span>Transfer bancar</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Observații (opțional)</label>
                        <textarea id="notes" name="notes" placeholder="Informații suplimentare despre comandă"></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Finalizează comanda
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
:root {
    --primary-color: #000;
    --primary-hover: #0052a3;
    --success-color: #28a745;
    --error-color: #d9534f;
    --light-gray: #f8f9fa;
    --border-color: #e1e5eb;
    --text-color: #000;
    --text-light: #6c7;
    --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    --radius: 8px;
    --transition: all 0.3s ease;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: var(--text-color);
    background-color: #f5f7fa;
}

.buy-container {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 2rem;
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.product-card {
    background: white;
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    position: relative;
    transition: var(--transition);
}

.product-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background-color: var(--success-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    z-index: 1;
}

.product-image {
    padding: 2rem;
    text-align: center;
    border-bottom: 1px solid var(--border-color);
}

.product-image img {
    max-width: 100%;
    height: auto;
    max-height: 250px;
    object-fit: contain;
}

.product-content {
    padding: 1.5rem;
}

.product-content h1 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.product-specs {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.spec-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.spec-item i {
    color: var(--primary-color);
}

.product-pricing {
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.price {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--primary-color);
}

.tax-info {
    font-size: 0.85rem;
    color: var(--text-light);
}

.order-form-container {
    position: relative;
}

.form-card {
    background: white;
    border-radius: var(--radius);
    padding: 2rem;
    box-shadow: var(--shadow);
}

.form-card h2 {
    font-size: 1.25rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary-color);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 0.9rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    font-size: 1rem;
    transition: var(--transition);
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
}

.form-group textarea {
    min-height: 100px;
    resize: vertical;
}

.payment-methods {
    margin: 1.5rem 0;
}

.payment-methods h3 {
    font-size: 1rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.payment-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.payment-option {
    position: relative;
}

.payment-option input {
    position: absolute;
    opacity: 0;
}

.payment-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: var(--radius);
    cursor: pointer;
    transition: var(--transition);
}

.payment-option input:checked + .payment-content {
    border-color: var(--primary-color);
    background-color: rgba(0, 102, 204, 0.05);
}

.payment-content i {
    color: var(--primary-color);
    font-size: 1.25rem;
}

.btn-submit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    background-color: var(--primary-color);
    color: white;
    padding: 1rem;
    border: none;
    border-radius: var(--radius);
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    margin-top: 1rem;
}

.btn-submit:hover {
    background-color: var(--primary-hover);
    transform: translateY(-2px);
}

.btn-submit i {
    font-size: 1rem;
}

.success-card {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.success-icon {
    font-size: 3rem;
    color: var(--success-color);
    margin-bottom: 1rem;
}

.success-card h2 {
    margin-bottom: 0.5rem;
    color: var(--success-color);
}

.success-card p {
    margin-bottom: 1.5rem;
    color: var(--text-light);
}

.btn-back {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background-color: var(--primary-color);
    color: white;
    text-decoration: none;
    border-radius: var(--radius);
    font-weight: 600;
    transition: var(--transition);
}

.btn-back:hover {
    background-color: var(--primary-hover);
}

.error-card {
    padding: 1.5rem;
    background-color: rgba(217, 83, 79, 0.1);
    border-left: 4px solid var(--error-color);
    border-radius: var(--radius);
    margin-bottom: 1.5rem;
}

.error-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    color: var(--error-color);
}

.error-header i {
    font-size: 1.25rem;
}

.error-list {
    padding-left: 1.5rem;
}

.error-list li {
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .buy-container {
        grid-template-columns: 1fr;
        padding: 0 1rem;
    }
    
    .form-row,
    .payment-options {
        grid-template-columns: 1fr;
    }
    
    .product-specs {
        grid-template-columns: 1fr;
    }
}
</style>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-about">
                    <div class="footer-logo">
                        <a href="index.php">Top<span>Climat</span></a>
                    </div>
                    <p>Soluții complete de climatizare pentru locuințe și spații comerciale. Calitate, eficiență și servicii profesionale.</p>
                    <div class="footer-social">
                        <a href="https://www.facebook.com/share/18qtMSFezk/?mibextid=wwXIfr"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/topclimat.md?igsh=bTk4NW81cGFzdHdp&utm_source=qr"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-links">
                    <h3>Linkuri rapide</h3>
                    <ul>
                        <li><a href="index.php"><i class="fas fa-chevron-right"></i> Acasă</a></li>
                        <li><a href="servicii.php"><i class="fas fa-chevron-right"></i> Servicii</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Despre noi</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Contact</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Termeni și condiții</a></li>
                    </ul>
                </div>
                
                <div class="footer-contact">
                    <h3>Contact</h3>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Chișinău, Moldova</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone-alt"></i>
                        <span>+373 61 141 157</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>contact@topclimat.md</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <span>Luni-Vineri: 9:00 - 18:00</span>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 TopClimat. Toate drepturile rezervate.</p>
            </div>
        </div>
    </footer>
</body>
</html>