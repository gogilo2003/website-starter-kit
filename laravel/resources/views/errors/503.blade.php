<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon - Young Olive Limited</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a2a3a 0%, #2c3e50 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .container {
            max-width: 1000px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .logo-container {
            margin-bottom: 30px;
            animation: float 3s ease-in-out infinite;
        }

        .logo {
            max-width: 100px;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
        }

        .error-content {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .error-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3498db, #2ecc71);
        }

        .error-code {
            font-size: 60px;
            font-weight: 700;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1;
            margin-bottom: 10px;
            text-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .error-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #fff;
        }

        .subtitle {
            font-size: 20px;
            color: #2ecc71;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .error-message {
            font-size: 14px;
            margin-bottom: 30px;
            color: #e0e0e0;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .progress-container {
            max-width: 500px;
            margin: 30px auto;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            height: 10px;
        }

        .progress-bar {
            height: 100%;
            width: 75%;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            border-radius: 10px;
            animation: progress 2s ease-in-out infinite alternate;
        }

        .status-message {
            font-size: 14px;
            color: #bdc3c7;
            margin-top: 10px;
        }

        .contact-info {
            display: grid;
            /* grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); */
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
        }

        .contact-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            text-align: left;
            transition: transform 0.3s ease, background 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contact-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
        }

        .contact-card h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #3498db;
            display: flex;
            align-items: center;
        }

        .contact-card h3 i {
            margin-right: 10px;
            font-size: 20px;
        }

        .contact-card p {
            margin-bottom: 8px;
            color: #e0e0e0;
            font-size: 15px;
        }

        .email-link {
            color: #2ecc71;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .email-link:hover {
            color: #3498db;
            text-decoration: underline;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }

        .btn-primary {
            background: linear-gradient(90deg, #3498db, #2980b9);
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(52, 152, 219, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        .btn i {
            margin-right: 8px;
        }

        .decoration {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: linear-gradient(45deg, #3498db, transparent);
            opacity: 0.1;
            z-index: -1;
        }

        .decoration-1 {
            top: -50px;
            left: -50px;
            animation: pulse 8s infinite alternate;
        }

        .decoration-2 {
            bottom: -80px;
            right: -50px;
            width: 250px;
            height: 250px;
            background: linear-gradient(45deg, #2ecc71, transparent);
            animation: pulse 10s infinite alternate-reverse;
        }

        .countdown {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 30px 0;
        }

        .countdown-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            min-width: 80px;
        }

        .countdown-value {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .countdown-label {
            font-size: 12px;
            color: #bdc3c7;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: #3498db;
            transform: translateY(-3px);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.1);
            }
        }

        @keyframes progress {
            0% {
                width: 70%;
            }

            100% {
                width: 80%;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .error-code {
                font-size: 40px;
            }

            .error-title {
                font-size: 18px;
            }

            .subtitle {
                font-size: 18px;
            }

            .error-message {
                font-size: 12px;
            }

            .contact-info {
                grid-template-columns: 1fr;
            }

            .error-content {
                padding: 30px 20px;
            }

            .countdown {
                gap: 10px;
            }

            .countdown-item {
                min-width: 70px;
                padding: 10px;
            }

            .countdown-value {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 30px;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 250px;
            }

            .countdown {
                flex-wrap: wrap;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="logo-container">
            <img src="/logo.png" alt="Young Olive Limited" class="logo">
        </div>

        <div class="error-content">
            <h1 class="error-code">503</h1>
            <h2 class="error-title">Our Website is Coming Soon!</h2>
            <p class="subtitle">Under Active Development</p>

            <div class="progress-container">
                <div class="progress-bar"></div>
            </div>
            <p class="status-message">Development Progress: 75% Complete</p>

            <p class="error-message">We're building something amazing for you! Our new website is currently in
                development and will be launching soon. Stay tuned for an enhanced digital experience that reflects our
                commitment to quality and innovation.</p>

            <div class="countdown">
                <div class="countdown-item">
                    <div class="countdown-value" id="days">--</div>
                    <div class="countdown-label">Days</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-value" id="hours">--</div>
                    <div class="countdown-label">Hours</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-value" id="minutes">--</div>
                    <div class="countdown-label">Minutes</div>
                </div>
                <div class="countdown-item">
                    <div class="countdown-value" id="seconds">--</div>
                    <div class="countdown-label">Seconds</div>
                </div>
            </div>

            <div class="action-buttons">
                <a href="mailto:info@youngolive.co.ke" class="btn btn-primary">
                    <i class="fas fa-envelope"></i> Get Notified at Launch
                </a>
                <a href="tel:+254722298105" class="btn btn-secondary">
                    <i class="fas fa-phone"></i> Contact Us Now
                </a>
            </div>

            <div class="contact-info">
                <div class="contact-card">
                    <h3><i class="fas fa-map-marker-alt"></i> Our Location</h3>
                    <p>Yogi Corp Business Centre, 3rd Floor</p>
                    <p>Suite 3B, Factory Street, Off Commercial Street, Industrial area</p>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-map-marker-alt"></i> Postal Address</h3>
                    <p>P.O. Box 4180-00506</p>
                    <p>Nairobi, Kenya</p>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-phone-alt"></i> Call/Text Us</h3>
                    <p>Tel: +254 722 298105</p>
                    <p>Cell: +254 722 720859</p>
                </div>

                <div class="contact-card">
                    <h3><i class="fas fa-envelope"></i> Drop Us an Email</h3>
                    <p>Email: <a href="mailto:info@youngolive.co.ke" class="email-link">info@youngolive.co.ke</a></p>
                    <p>Email: <a href="mailto:sales@youngolive.co.ke" class="email-link">sales@youngolive.co.ke</a></p>
                </div>
            </div>

            <div class="social-links">
                <a href="#" class="social-link">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="#" class="social-link">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
        </div>

        <div class="decoration decoration-1"></div>
        <div class="decoration decoration-2"></div>
    </div>

    <script>
        // Countdown timer (set to 30 days from now as an example)
        const countdownDate = new Date('14-Nov-2025 11:00 am');
        // countdownDate.setDate(countdownDate.getDate() + 30);

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = countdownDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('days').textContent = days;
            document.getElementById('hours').textContent = hours;
            document.getElementById('minutes').textContent = minutes;
            document.getElementById('seconds').textContent = seconds;

            if (distance < 0) {
                clearInterval(countdownTimer);
                document.getElementById('days').textContent = 0;
                document.getElementById('hours').textContent = 0;
                document.getElementById('minutes').textContent = 0;
                document.getElementById('seconds').textContent = 0;
            }
        }

        const countdownTimer = setInterval(updateCountdown, 1000);
        updateCountdown();

        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            const errorCode = document.querySelector('.error-code');

            // Add a subtle animation to the error code
            errorCode.addEventListener('mouseover', function() {
                this.style.transform = 'scale(1.05)';
            });

            errorCode.addEventListener('mouseout', function() {
                this.style.transform = 'scale(1)';
            });

            // Add a floating animation to contact cards on scroll
            const contactCards = document.querySelectorAll('.contact-card');

            function checkScroll() {
                contactCards.forEach(card => {
                    const cardTop = card.getBoundingClientRect().top;
                    const windowHeight = window.innerHeight;

                    if (cardTop < windowHeight * 0.9) {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }
                });
            }

            // Initialize cards with slight offset
            contactCards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            });

            window.addEventListener('scroll', checkScroll);
            checkScroll(); // Check on load
        });
    </script>
</body>

</html>
