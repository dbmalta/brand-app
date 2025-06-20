<?php
// index.php - BitKode Marketing Management Platform Landing Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BitKode - AI Marketing Management Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Professional marketing management platform for agencies. Manage clients, campaigns, and branding assets with AI assistance.">
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #3a7bd5;
        }
        .feature-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .cta-section {
            background-color: #f8f9fa;
            padding: 80px 0;
        }
        .btn-primary-custom {
            background: linear-gradient(45deg, #3a7bd5, #00d2ff);
            border: none;
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(58, 123, 213, 0.4);
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">AI Marketing Console</h1>
                    <p class="lead mb-5">The complete marketing management platform for agencies. Streamline client relationships, manage campaigns, and organize branding assets with intelligent AI assistance.</p>
                    <a href="login.php" class="btn btn-primary-custom btn-lg">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2 class="display-5 fw-bold text-dark">Everything You Need to Manage Your Marketing</h2>
                    <p class="lead text-muted">Powerful tools designed for marketing professionals and agencies</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 text-center">
                        <div class="feature-icon">🧍</div>
                        <h4>Client Management</h4>
                        <p class="text-muted">Organize client information, contact details, and branding guidelines in one centralized location.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 text-center">
                        <div class="feature-icon">📣</div>
                        <h4>Campaign Tracking</h4>
                        <p class="text-muted">Create, track, and manage marketing campaigns with objectives, timelines, and status monitoring.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 text-center">
                        <div class="feature-icon">💬</div>
                        <h4>AI Assistant</h4>
                        <p class="text-muted">Get intelligent marketing guidance and support through our integrated AI-powered chatbot.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h3 class="display-6 fw-bold mb-4">Ready to Transform Your Marketing Management?</h3>
                    <p class="lead mb-4">Join marketing professionals who trust BitKode to streamline their client relationships and campaign management.</p>
                    <a href="login.php" class="btn btn-primary-custom btn-lg">Access Your Dashboard</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 BitKode. Professional Marketing Management Platform.</p>
            <p class="mb-0 small text-muted">Learn more at <a href="https://bitkode.com" class="text-light">bitkode.com</a></p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
