<?php
include 'db.php';

// Get filter values from the URL
$firma = isset($_GET['firma']) ? $_GET['firma'] : '';
$model = isset($_GET['model']) ? $_GET['model'] : '';
$clasa_energetica = isset($_GET['clasa_energetica']) ? $_GET['clasa_energetica'] : '';
$btu_filter = isset($_GET['btu_filter']) ? $_GET['btu_filter'] : '';
$mp_filter = isset($_GET['mp_filter']) ? $_GET['mp_filter'] : '';
$sortare_pret = isset($_GET['sortare_pret']) ? $_GET['sortare_pret'] : 'asc'; // Default ascending
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'activ'; // Default 'activ' (pentru website)

// Pagination logic
$limit = 12;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Create the query with filters (folosește prepared statements pentru securitate)
$query = "SELECT * FROM products WHERE status = ?"; // Doar produse active implicit

$params = [$status_filter]; // Parametrii pentru prepared statement
$types = "s"; // Tipul parametrului (s = string)

if ($firma) {
    $query .= " AND firma LIKE ?";
    $params[] = "%$firma%";
    $types .= "s";
}

if ($model) {
    $query .= " AND model LIKE ?";
    $params[] = "%$model%";
    $types .= "s";
}

if ($clasa_energetica) {
    $query .= " AND clasa_energetica = ?";
    $params[] = $clasa_energetica;
    $types .= "s";
}

if ($btu_filter) {
    if ($btu_filter == 'below_5000') {
        $query .= " AND btu < 5000";
    } elseif ($btu_filter == 'between_5000_10000') {
        $query .= " AND btu BETWEEN 5000 AND 10000";
    } elseif ($btu_filter == 'above_10000') {
        $query .= " AND btu > 10000";
    }
}

if ($mp_filter) {
    if ($mp_filter == 'below_20') {
        $query .= " AND mp < 20";
    } elseif ($mp_filter == 'between_20_50') {
        $query .= " AND mp BETWEEN 20 AND 50";
    } elseif ($mp_filter == 'above_50') {
        $query .= " AND mp > 50";
    }
}

$query .= " ORDER BY pret $sortare_pret LIMIT $start, $limit";

// Execută interogarea cu prepared statement
$stmt = $conn->prepare($query);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Total rows for pagination
$totalQuery = "SELECT COUNT(*) as total FROM products WHERE status = ?";
$totalParams = [$status_filter];
$totalTypes = "s";

if ($firma) {
    $totalQuery .= " AND firma LIKE ?";
    $totalParams[] = "%$firma%";
    $totalTypes .= "s";
}

if ($model) {
    $totalQuery .= " AND model LIKE ?";
    $totalParams[] = "%$model%";
    $totalTypes .= "s";
}

if ($clasa_energetica) {
    $totalQuery .= " AND clasa_energetica = ?";
    $totalParams[] = $clasa_energetica;
    $totalTypes .= "s";
}

if ($btu_filter) {
    if ($btu_filter == 'below_5000') {
        $totalQuery .= " AND btu < 5000";
    } elseif ($btu_filter == 'between_5000_10000') {
        $totalQuery .= " AND btu BETWEEN 5000 AND 10000";
    } elseif ($btu_filter == 'above_10000') {
        $totalQuery .= " AND btu > 10000";
    }
}

if ($mp_filter) {
    if ($mp_filter == 'below_20') {
        $totalQuery .= " AND mp < 20";
    } elseif ($mp_filter == 'between_20_50') {
        $totalQuery .= " AND mp BETWEEN 20 AND 50";
    } elseif ($mp_filter == 'above_50') {
        $totalQuery .= " AND mp > 50";
    }
}

$totalStmt = $conn->prepare($totalQuery);
if ($totalTypes) {
    $totalStmt->bind_param($totalTypes, ...$totalParams);
}
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Top Climat MD | Vanzare și Instalare Aparate de Aer Condiționat în Moldova</title>

<!-- Meta Google -->
<meta name="google-site-verification" content="Ml6Hk3clr-pcYNTSrKS9GBobNRJyqjZt6Q7_gxFRAG8" />
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Meta Tags Primare -->
<meta name="description" content="Top Climat MD - soluții complete de climatizare în Moldova. Vânzăm și instalăm aparate de aer condiționat de înaltă calitate la prețuri competitive.">
<meta name="author" content="Top Climat MD">
<meta name="keywords" content="aer conditionat chisinau, vanzare aer conditionat moldova, instalare climatizare, service aer conditionat, preturi aparate climatizare, inverter moldova">

<!-- Canonical & Favicon -->
<link rel="canonical" href="https://topclimat.md">
<link rel="icon" href="logo-topclimat.jpg" type="image/jpg">

<!-- Open Graph / Meta Tags pentru Rețele Sociale -->
<meta property="og:title" content="Top Climat MD | Aer Condiționat Profesional în Moldova">
<meta property="og:description" content="Specialiști în vânzarea și instalarea aparatelor de aer condiționat. Soluții personalizate pentru case, birouri și spații comerciale.">
<meta property="og:url" content="https://topclimat.md">
<meta property="og:type" content="website">
<meta property="og:image" content="https://topclimat.md/logo.jpg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="Top Climat MD - Instalare Aer Condiționat Profesional">
<meta property="og:site_name" content="Top Climat MD">
<meta property="og:locale" content="ro_MD">

<!-- Schema.org Markup for Local Business in Moldova -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "Top Climat MD",
  "image": "https://topclimat.md/images/logo-topclimat.jpg",
  "@id": "https://topclimat.md",
  "url": "https://topclimat.md",
  "telephone": "+373-XXX-XXX-XX",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Strada Exemplu 123",
    "addressLocality": "Chișinău",
    "addressRegion": "MD",
    "postalCode": "2020",
    "addressCountry": "MD"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 47.0105,
    "longitude": 28.8638
  },
  "openingHoursSpecification": {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": [
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday"
    ],
    "opens": "08:00",
    "closes": "19:00"
  },
  "sameAs": [
    "https://www.facebook.com/topclimatmd/",
    "https://www.instagram.com/topclimatmd/"
  ],
  "priceRange": "$$",
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "Servicii de Climatizare",
    "itemListElement": [
      {
        "@type": "OfferCatalog",
        "name": "Aparate de Aer Condiționat",
        "itemListElement": [
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Vânzare aparate aer condiționat"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Instalare profesională"
            }
          }
        ]
      },
      {
        "@type": "OfferCatalog",
        "name": "Service și Întreținere",
        "itemListElement": [
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Întreținere periodică"
            }
          },
          {
            "@type": "Offer",
            "itemOffered": {
              "@type": "Service",
              "name": "Reparații și service"
            }
          }
        ]
      }
    ]
  }
}
</script>

<!-- Sitemap & Robots.txt References -->
<link rel="sitemap" type="application/xml" title="Sitemap" href="https://topclimat.md/sitemap.xml">
<meta name="robots" content="index, follow">
    <meta property="og:image" content="https://topclimat.ro/condiționare.jpg" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="shortcut icon" type="image/icon" href="logo.jpg"/>
    <style>
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <div class="logo">
                <a href="index.php" class="logo-text">Top<span >Climat</span></a>
            </div>
            <nav class="nav">
                <a href="index.php"><i class="fas fa-home"></i> Acasă</a>
                <a href="servicii.php"><i class="fas fa-cogs"></i> Servicii</a>
                <a href="tel:+373-61-141-157" class="call-now"><i class="fas fa-phone-alt"></i> Sună acum</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Cele mai bune aparate de aer condiționat</h1>
        <p>Fii gospodar alege cele mai eficiente solutii de climatizare la preț atracriv.</p>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="filter-header">
            <h2>Filtrează produsele</h2>
            <button class="toggle-filters" onclick="toggleFilters()">
                <i class="fas fa-sliders-h"></i> Filtre
            </button>
        </div>
        
        <form class="filter-form" method="GET" action="index.php">
            <div class="filter-group">
                <label for="firma">Producător</label>
                <input type="text" id="firma" name="firma" placeholder="ex: Samsung, LG" value="<?php echo $firma; ?>">
            </div>
            
            <div class="filter-group">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" placeholder="ex: AR12, Dual Cool" value="<?php echo $model; ?>">
            </div>
            
            <div class="filter-group">
                <label for="clasa_energetica">Clasă energetică</label>
                <select id="clasa_energetica" name="clasa_energetica">
                    <option value="">Toate clasele</option>
                    <option value="A+++" <?php echo ($clasa_energetica == 'A+++') ? 'selected' : ''; ?>>A+++</option>
                    <option value="A++" <?php echo ($clasa_energetica == 'A++') ? 'selected' : ''; ?>>A++</option>
                    <option value="A+" <?php echo ($clasa_energetica == 'A+') ? 'selected' : ''; ?>>A+</option>
                    <option value="A" <?php echo ($clasa_energetica == 'A') ? 'selected' : ''; ?>>A</option>
                    <option value="B" <?php echo ($clasa_energetica == 'B') ? 'selected' : ''; ?>>B</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="btu_filter">Putere BTU</label>
                <select id="btu_filter" name="btu_filter">
                    <option value="">Toate</option>
                    <option value="below_5000" <?php echo ($btu_filter == 'below_5000') ? 'selected' : ''; ?>>Sub 5000 BTU</option>
                    <option value="between_5000_10000" <?php echo ($btu_filter == 'between_5000_10000') ? 'selected' : ''; ?>>5000-10000 BTU</option>
                    <option value="above_10000" <?php echo ($btu_filter == 'above_10000') ? 'selected' : ''; ?>>Peste 10000 BTU</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="mp_filter">Suprafață (mp)</label>
                <select id="mp_filter" name="mp_filter">
                    <option value="">Toate</option>
                    <option value="below_20" <?php echo ($mp_filter == 'below_20') ? 'selected' : ''; ?>>Sub 20 mp</option>
                    <option value="between_20_50" <?php echo ($mp_filter == 'between_20_50') ? 'selected' : ''; ?>>20-50 mp</option>
                    <option value="above_50" <?php echo ($mp_filter == 'above_50') ? 'selected' : ''; ?>>Peste 50 mp</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="sortare_pret">Sortează după preț</label>
                <select id="sortare_pret" name="sortare_pret">
                    <option value="">Implicit</option>
                    <option value="asc" <?php echo ($sortare_pret == 'asc') ? 'selected' : ''; ?>>Preț crescător</option>
                    <option value="desc" <?php echo ($sortare_pret == 'desc') ? 'selected' : ''; ?>>Preț descrescător</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="reset" class="btn btn-outline">Resetează</button>
                <button type="submit" class="btn btn-primary">Aplică filtre</button>
            </div>
        </form>
    </section>
        
        <div class="products-grid">
            <?php while ($product = $result->fetch_assoc()) { ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['model']; ?>">
                        <span class="product-badge"><?php echo $product['clasa_energetica']; ?></span>
                    </div>
                    <div class="product-info">
                        <div class="product-brand"><?php echo $product['firma']; ?></div>
                        <h3 class="product-title"><?php echo $product['model']; ?></h3>
                        
                        <div class="product-specs">
                            <div class="spec-item">
                                <i class="fas fa-bolt"></i>
                                <span><?php echo $product['btu']; ?> BTU</span>
                            </div>
                            <div class="spec-item">
                                <i class="fas fa-vector-square"></i>
                                <span><?php echo $product['mp']; ?> mp</span>
                            </div>
                        </div>
                        
                        <div class="product-price"><?php echo $product['pret']; ?> MDL</div>
                        
                        <div class="product-actions">
                            <a href="products.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">Detalii</a>
                            <a href="buy.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary btn-sm" class="btn btn-secondary btn-sm">Comandă</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        
        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <a href="index.php?page=<?php echo $i; ?>&firma=<?php echo $firma; ?>&model=<?php echo $model; ?>&clasa_energetica=<?php echo $clasa_energetica; ?>&btu_filter=<?php echo $btu_filter; ?>&mp_filter=<?php echo $mp_filter; ?>&sortare_pret=<?php echo $sortare_pret; ?>" <?php echo ($i == $current_page) ? 'class="active"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
            <?php } ?>
        </div>
    </section>

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
        // Toggle filters visibility
        function toggleFilters() {
            const form = document.querySelector('.filter-form');
            form.style.display = form.style.display === 'grid' ? 'none' : 'grid';
        }

        // Initialize filters as hidden on mobile
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth < 768) {
                document.querySelector('.filter-form').style.display = 'none';
            } else {
                document.querySelector('.filter-form').style.display = 'grid';
            }
        });

        // Responsive behavior for filters
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                document.querySelector('.filter-form').style.display = 'grid';
            }
        });
    </script>
</body>
</html>