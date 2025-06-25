<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
require 'db.php';

// Validate product ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id <= 0) {
    header("HTTP/1.0 404 Not Found");
    die("Invalid product ID");
}

// Fetch product
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $product_id);
if (!$stmt->execute()) {
    die("Database error: " . $stmt->error);
}

$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("HTTP/1.0 404 Not Found");
    die("Product not found");
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Climat - <?php echo htmlspecialchars($product['firma'] . ' ' . $product['model']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($product['Descriere']); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" type="image/icon" href="logo.jpg">
    <style>
        :root {
            --primary: #c0a080; /* Auriu metalizat deschis (Bej auriu) */
            --primary-dark: #b08e60; /* Auriu mai închis */
            --secondary: #d4af37; /* Auriu metalic intens */
            --dark: #121212; /* Negru profund (aproape #000 dar nu chiar) */
            --light: #f8f4e9; /* Alb cremos cu nuanțe de fildeș */
            --gray: #e8e5de; /* Gri cu reflexe de marmură */
            --dark-gray: #6d6d6d; /* Gri elegant */
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.15); /* Umbră mai moale și mai difuză */
            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); /* Tranziție mai smooth */
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light);
            color: #333;
            line-height: 1.6;
        }
        
        /* Header */
        header {
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 15px 0;
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo img {
            height: 40px;
        }
        
        .logo-text {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
            text-decoration: none;
        }
        
        .logo-text span {
            color: var(--secondary);
        }
        
        .nav {
            display: flex;
            gap: 5px;
        }
        
        .nav a {
            color: var(--dark);
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: var(--transition);
            padding: 8px 8px;
            border-radius: 4px;
        }
        
        .nav a:hover {
            color: var(--primary);
            background-color: rgba(192, 160, 128, 0.1);
        }
        
        .nav .call-now {
            background-color: var(--primary);
            color: white;
            border-radius: 30px;
            padding: 8px 16px;
        }
        
        .nav .call-now:hover {
            background-color: var(--primary-dark);
            color: white;
        }
        
        /* Main Product Container */
        .product-detail-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        
        /* Gallery */
        .product-gallery {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .main-product-image {
            width: 100%;
            height: 400px;
            object-fit: contain;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        
        .thumbnail-container {
            display: flex;
            gap: 10px;
        }
        
        .product-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: var(--transition);
        }
        
        .product-thumbnail:hover, 
        .product-thumbnail.active {
            border-color: var(--primary);
        }
        
        /* Product Info */
        .product-detail-info {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        
        .product-detail-title {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        .product-detail-brand {
            font-size: 1rem;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 20px;
            display: block;
        }
        
        .product-detail-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin: 20px 0;
        }
        
        .product-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--dark-gray);
        }
        
        .meta-item i {
            color: var(--primary);
        }
        
        .product-description {
            margin: 25px 0;
            line-height: 1.7;
            color: var(--dark);
        }
        
        .specs-title {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.2rem;
            color: var(--dark);
            position: relative;
            padding-bottom: 8px;
        }
        
        .specs-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: var(--primary);
        }
        
        .specs-list {
            list-style: none;
        }
        
        .specs-list li {
            padding: 12px 0;
            border-bottom: 1px solid var(--gray);
            display: flex;
            justify-content: space-between;
        }
        
        .spec-name {
            font-weight: 500;
            color: var(--dark);
        }
        
        .spec-value {
            color: var(--dark-gray);
            text-align: right;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
            flex: 1;
            justify-content: center;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--gray);
            color: var(--dark-gray);
            flex: 1;
            justify-content: center;
        }
        
        .btn-outline:hover {
            background-color: var(--gray);
        }
        
        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #c59d2b;
        }
        
        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 60px 0 20px;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-logo {
            margin-bottom: 20px;
        }
        
        .footer-logo a {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
        }
        
        .footer-logo a span {
            color: var(--secondary);
        }
        
        .footer-about p {
            margin-bottom: 20px;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .footer-social {
            display: flex;
            gap: 15px;
        }
        
        .footer-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            transition: var(--transition);
        }
        
        .footer-social a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }
        
        .footer-links h3,
        .footer-contact h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .footer-links h3::after,
        .footer-contact h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: var(--primary);
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .contact-item i {
            color: var(--primary);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .product-detail-container {
                grid-template-columns: 1fr;
            }
            
            .header-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .nav {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .main-product-image {
                height: 300px;
            }
        }
        
        @media (max-width: 480px) {
            .product-meta {
                flex-direction: column;
                gap: 10px;
            }
            
            .product-detail-title {
                font-size: 1.5rem;
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

    <!-- Main Product Content -->
    <main class="product-detail-container">
        <!-- Gallery -->
        <div class="product-gallery">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($product['model']); ?>" 
                 class="main-product-image" 
                 id="mainProductImage">
            
            <div class="thumbnail-container">
                <?php
                $images = array_filter([
                    $product['image_url'],
                    $product['image_url_2'] ?? null,
                    $product['image_url_3'] ?? null
                ]);
                
                foreach ($images as $index => $img) {
                    echo '<img src="' . htmlspecialchars($img) . '" 
                          alt="Thumbnail ' . ($index+1) . '" 
                          class="product-thumbnail' . ($index === 0 ? ' active' : '') . '" 
                          onclick="changeProductImage(this, \'' . htmlspecialchars($img) . '\')">';
                }
                ?>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="product-detail-info">
            <h1 class="product-detail-title"><?php echo htmlspecialchars($product['model']); ?></h1>
            <span class="product-detail-brand"><?php echo htmlspecialchars($product['firma']); ?></span>
            
            <div class="product-meta">
                <div class="meta-item">
                    <i class="fas fa-bolt"></i>
                    <span><?php echo htmlspecialchars($product['btu']); ?> BTU</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-vector-square"></i>
                    <span><?php echo htmlspecialchars($product['mp']); ?> mp</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-battery-three-quarters"></i>
                    <span>Clasa <?php echo htmlspecialchars($product['clasa_energetica']); ?></span>
                </div>
            </div>
            
            <div class="product-detail-price"><?php echo number_format($product['pret'], 2); ?> MDL</div>
            
            <?php if (!empty($product['Descriere'])): ?>
            <div class="product-description">
                <p><?php echo nl2br(htmlspecialchars($product['Descriere'])); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($product['detalii_tehnice'])): ?>
            <div class="specs-title">Specificații tehnice:</div>
            <ul class="specs-list">
                <?php
                $specs = array_filter(explode("\n", $product['detalii_tehnice']));
                foreach ($specs as $spec) {
                    $parts = explode(":", $spec, 2);
                    echo '<li>';
                    echo '<span class="spec-name">' . htmlspecialchars(trim($parts[0])) . '</span>';
                    if (count($parts) > 1) {
                        echo '<span class="spec-value">' . htmlspecialchars(trim($parts[1])) . '</span>';
                    }
                    echo '</li>';
                }
                ?>
            </ul>
            <?php endif; ?>
            
            <div class="action-buttons">
                <a href="index.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Înapoi
                </a>
                <a href="buy.php?id=<?php echo $product_id; ?>" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> Comandă acum
                </a>
            </div>
        </div>
    </main>

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

    <script>
        function changeProductImage(thumbnail, imageUrl) {
            // Update main image
            document.getElementById('mainProductImage').src = imageUrl;
            
            // Update active thumbnail
            document.querySelectorAll('.product-thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            thumbnail.classList.add('active');
        }
    </script>
</body>
</html>