<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FABRICASOFT - SENA</title>

        <style>
            :root {
                --sena-green: #009739;
                --sena-dark: #1A3C34;
                --accent: #FFD700;
                --text: #0F172A;
                --light-bg: #F0F4F8;
                --secondary-accent: #00A1D6;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                overflow-x: hidden;
            }

            .software-factory-container {
                width: 100%;
                font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
                color: var(--text);
                line-height: 1.9;
                background: var(--light-bg);
                position: relative;
                overflow: hidden;
            }

            .icon {
                width: 4.5rem;
                height: 4.5rem;
                transition: transform 0.5s ease, filter 0.5s ease;
            }

            .button-icon {
                width: 2.2rem;
                height: 2.2rem;
                margin-right: 1rem;
            }

            /* Animated Background */
            .hero-section {
                position: relative;
                min-height: 100vh;
                background: linear-gradient(145deg, var(--sena-green), var(--sena-dark));
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            .hero-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800"><defs><filter id="glow"><feGaussianBlur stdDeviation="3.5" result="blur"/><feFlood flood-color="%23FFD700" flood-opacity="0.3"/><feComposite in2="blur" operator="in" result="glow"/><feMerge><feMergeNode in="glow"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><g filter="url(#glow)"><circle cx="200" cy="200" r="50" fill="%23FFD700" opacity="0.3"><animate attributeName="cx" values="200;300;200" dur="10s" repeatCount="indefinite"/></circle><circle cx="600" cy="600" r="70" fill="%2300A1D6" opacity="0.3"><animate attributeName="cy" values="600;500;600" dur="12s" repeatCount="indefinite"/></circle><rect x="400" y="400" width="80" height="80" fill="%23FFFFFF" opacity="0.2" transform="rotate(45 440 440)"><animateTransform attributeName="transform" type="rotate" from="45 440 440" to="405 440 440" dur="15s" repeatCount="indefinite"/></rect></g></svg>') center/cover;
                z-index: 1;
                animation: backgroundFlow 20s ease-in-out infinite;
            }

            @keyframes backgroundFlow {
                0%, 100% { transform: scale(1) translate(0, 0); }
                50% { transform: scale(1.1) translate(-20px, -20px); }
            }

            .hero-content {
                text-align: center;
                padding: 5rem 2rem;
                max-width: 1200px;
                z-index: 2;
                animation: zoomIn 1.5s ease-out;
                margin: 0 auto;
            }

            @keyframes zoomIn {
                from { opacity: 0; transform: scale(0.8) translateY(100px); }
                to { opacity: 1; transform: scale(1) translateY(0); }
            }

            .hero-icon .icon {
                color: var(--accent);
                filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.5));
                animation: codePulse 3s ease-in-out infinite;
            }

            @keyframes codePulse {
                0%, 100% { transform: scale(1) rotate(0deg); }
                50% { transform: scale(1.2) rotate(5deg); }
            }

            .hero-title {
                font-size: 6rem;
                font-weight: 900;
                margin: 3rem 0;
                color: #FFFFFF;
                text-shadow: 0 5px 15px rgba(0, 0, 0, 0.8);
                letter-spacing: -3px;
                text-transform: uppercase;
                position: relative;
            }

            .hero-title::after {
                content: '</>';
                position: absolute;
                font-size: 2rem;
                color: var(--accent);
                right: -60px;
                top: 20%;
                animation: blink 1.5s infinite;
            }

            @keyframes blink {
                50% { opacity: 0.3; }
            }

            .highlight {
                color: var(--accent);
                font-size: 7rem;
                font-weight: 900;
                display: block;
                margin-top: 0.8rem;
                animation: glow 2s ease-in-out infinite;
            }

            @keyframes glow {
                0%, 100% { text-shadow: 0 0 10px var(--accent), 0 0 20px var(--accent), 0 0 30px var(--accent); }
                50% { text-shadow: 0 0 20px var(--accent), 0 0 30px var(--accent), 0 0 40px var(--accent); }
            }

            .hero-subtitle {
                font-size: 2.2rem;
                color: rgba(255, 255, 255, 0.95);
                max-width: 900px;
                margin: 0 auto 5rem;
                font-weight: 300;
                letter-spacing: 1px;
                animation: fadeIn 2s ease-out 0.5s both;
                text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            .cta-button {
                display: inline-flex;
                align-items: center;
                padding: 1.8rem 4.5rem;
                font-size: 1.5rem;
                font-weight: 700;
                text-decoration: none;
                border-radius: 15px;
                transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                margin: 1.2rem;
                text-transform: uppercase;
                letter-spacing: 2.5px;
                position: relative;
                overflow: hidden;
                z-index: 2;
            }

            .cta-button::before {
                content: '';
                position: absolute;
                top: 0;
                left: -200%;
                width: 300%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
                transition: left 0.6s ease;
                z-index: -1;
            }

            .cta-button:hover::before {
                left: 200%;
            }

            .cta-button.primary {
                background: var(--accent);
                color: var(--sena-dark);
                box-shadow: 0 10px 30px rgba(0, 151, 57, 0.6);
            }

            .cta-button.secondary {
                background: transparent;
                color: var(--accent);
                border: 4px solid var(--accent);
            }

            .cta-button.login {
                background: var(--sena-dark);
                color: var(--accent);
            }

            .cta-button:hover {
                transform: translateY(-8px) scale(1.15);
                box-shadow: 0 15px 40px rgba(0, 151, 57, 0.7);
            }

            .button-container {
                display: flex;
                justify-content: center;
                gap: 3rem;
                flex-wrap: wrap;
                max-width: 100%;
                padding: 0 1rem;
            }

            .services {
                padding: 12rem 2rem;
                background: linear-gradient(180deg, white, var(--light-bg));
                position: relative;
                z-index: 2;
            }

            .section-title {
                color: var(--sena-dark);
                font-size: 4rem;
                font-weight: 800;
                text-align: center;
                margin-bottom: 7rem;
                text-transform: uppercase;
                letter-spacing: 2px;
                position: relative;
            }

            .section-title::before {
                content: '{';
                position: absolute;
                left: -40px;
                top: 10%;
                font-size: 3rem;
                color: var(--accent);
                animation: rotate 5s infinite linear;
            }

            .section-title::after {
                content: '}';
                position: absolute;
                right: -40px;
                top: 10%;
                font-size: 3rem;
                color: var(--accent);
                animation: rotate 5s infinite linear reverse;
            }

            @keyframes rotate {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            .services-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
                gap: 5rem;
                max-width: 1500px;
                margin: 0 auto;
                padding: 0 1rem;
            }

            .service-card {
                background: white;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
                padding: 4.5rem 3.5rem;
                border-radius: 30px;
                text-align: center;
                transition: all 0.7s ease;
                position: relative;
                border: 3px solid transparent;
                overflow: hidden;
            }

            .service-card::before {
                content: '';
                position: absolute;
                top: -100%;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(45deg, transparent, rgba(0, 151, 57, 0.2), transparent);
                transition: top 0.5s ease;
            }

            .service-card:hover::before {
                top: 0;
            }

            .service-card:hover {
                transform: translateY(-25px) rotate(3deg);
                border-color: var(--accent);
                box-shadow: 0 30px 70px rgba(0, 151, 57, 0.4);
            }

            .service-icon .icon {
                color: var(--sena-green);
                margin-bottom: 3rem;
                transform: scale(1.4);
                transition: transform 0.5s ease;
            }

            .service-card:hover .icon {
                transform: scale(1.6) rotate(10deg);
            }

            .service-card h3 {
                color: var(--sena-dark);
                font-size: 2.2rem;
                margin-bottom: 1.8rem;
                font-weight: 700;
                text-transform: uppercase;
            }

            .service-card p {
                color: var(--text);
                font-size: 1.3rem;
                line-height: 1.7;
            }

            .why-choose-us {
                padding: 12rem 2rem;
                background: white;
                position: relative;
                z-index: 2;
            }

            .why-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
                gap: 5rem;
                max-width: 1500px;
                margin: 0 auto;
                padding: 0 1rem;
            }

            .why-card {
                display: flex;
                align-items: center;
                gap: 3.5rem;
                padding: 3.5rem;
                background: linear-gradient(135deg, var(--light-bg), white);
                border-radius: 30px;
                transition: all 0.6s ease;
                border: 3px solid rgba(0, 151, 57, 0.2);
                position: relative;
                overflow: hidden;
            }

            .why-card::before {
                content: '</>';
                position: absolute;
                top: 10px;
                right: 10px;
                color: var(--accent);
                font-size: 1.5rem;
                opacity: 0.3;
                animation: floatCode 4s ease-in-out infinite;
            }

            @keyframes floatCode {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
            }

            .why-card:hover {
                transform: translateY(-20px) scale(1.05);
                box-shadow: 0 25px 60px rgba(0, 151, 57, 0.3);
                border-color: var(--sena-green);
            }

            .why-icon {
                background: linear-gradient(135deg, var(--sena-green), var(--secondary-accent));
                border-radius: 50%;
                width: 7rem;
                height: 7rem;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                transition: transform 0.6s ease;
            }

            .why-card:hover .why-icon {
                transform: scale(1.2) rotate(20deg);
            }

            .why-icon .icon {
                color: var(--accent);
                width: 3.5rem;
                height: 3.5rem;
            }

            .why-content h3 {
                color: var(--sena-dark);
                font-size: 2rem;
                margin-bottom: 1.5rem;
                font-weight: 700;
                text-transform: uppercase;
            }

            .why-content p {
                color: var(--text);
                font-size: 1.3rem;
                line-height: 1.7;
            }

            .contact-section {
                padding: 12rem 2rem;
                background: linear-gradient(160deg, var(--sena-green), var(--sena-dark));
                color: white;
                position: relative;
                overflow: hidden;
                z-index: 2;
            }

            .contact-section::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 800"><g opacity="0.2"><circle cx="400" cy="400" r="200" fill="%23FFD700"><animate attributeName="r" values="200;220;200" dur="8s" repeatCount="indefinite"/></circle><rect x="300" y="300" width="100" height="100" fill="%2300A1D6" transform="rotate(45 350 350)"><animateTransform attributeName="transform" type="rotate" from="45 350 350" to="405 350 350" dur="10s" repeatCount="indefinite"/></rect></g></svg>') center/cover;
                transform: rotate(45deg);
                animation: backgroundFlow 15s ease-in-out infinite;
            }

            .contact-content {
                max-width: 1100px;
                margin: 0 auto;
                text-align: center;
                position: relative;
                z-index: 2;
            }

            .form-group {
                margin-bottom: 3.5rem;
                text-align: left;
                position: relative;
                max-width: 100%;
            }

            .form-group label {
                color: white;
                font-size: 1.4rem;
                font-weight: 600;
                margin-bottom: 1.5rem;
                display: block;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .form-group input,
            .form-group textarea {
                width: 100%;
                padding: 1.8rem;
                font-size: 1.3rem;
                border: 3px solid rgba(255, 255, 255, 0.6);
                border-radius: 20px;
                background: rgba(255, 255, 255, 0.25);
                color: white;
                transition: all 0.5s ease;
                font-family: inherit;
            }

            .form-group input:focus,
            .form-group textarea:focus {
                border-color: var(--accent);
                background: rgba(255, 255, 255, 0.35);
                box-shadow: 0 0 30px rgba(255, 193, 7, 0.5);
                outline: none;
            }

            .form-group input::placeholder,
            .form-group textarea::placeholder {
                color: rgba(255, 255, 255, 0.7);
                font-style: italic;
            }

            @media (max-width: 768px) {
                .hero-title { font-size: 4rem; }
                .highlight { font-size: 5rem; }
                .hero-subtitle { font-size: 1.6rem; }
                .section-title { font-size: 3rem; }
                .cta-button { padding: 1.5rem 3rem; font-size: 1.3rem; width: 100%; }
                .why-card { flex-direction: column; text-align: center; }
                .why-icon { width: 6rem; height: 6rem; }
                .button-container { flex-direction: column; }
                .service-card h3 { font-size: 1.9rem; }
                .why-content h3 { font-size: 1.7rem; }
            }

            @keyframes float {
                0%, 100% { transform: translateY(0) rotate(0); }
                50% { transform: translateY(-20px) rotate(8deg); }
            }
        </style>
    </head>
    <body>
        <div class="software-factory-container">
            <!-- Hero Section -->
            <section class="hero-section">
                <div class="hero-content">
                    <div class="hero-icon">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h1 class="hero-title">FÁBRICA DE SOFTWARE<br><span class="highlight">SENA</span></h1>
                    <p class="hero-subtitle">Transformamos ideas en soluciones digitales de vanguardia con la excelencia del SENA</p>
                    <div class="button-container">
                        @guest
                            <a href="{{ route('login') }}" class="cta-button secondary">Iniciar Sesión</a>
                        @else
                            <a href="{{ route('fabricasoft.admin.welcome') }}" class="cta-button secondary" style="background:#009739;color:#FFD700;">Panel Administrativo</a>
                        @endguest
                        <a href="#contact" class="cta-button primary">
                            <svg class="button-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            Contáctanos
                        </a>
                    </div>
                </div>
            </section>

            <!-- Services Section -->
            <section class="services">
                <h2 class="section-title">Nuestros Servicios</h2>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                            </svg>
                        </div>
                        <h3>Desarrollo a Medida</h3>
                        <p>Creamos soluciones de software personalizadas que impulsan la innovación y optimizan procesos empresariales con estándares globales.</p>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19.35 10.04A7.49 7.49 0 0 0 12 4a7.49 7.49 0 0 0-7.35 6.04A5.5 5.5 0 0 0 0 15.5C0 18.54 2.46 21 5.5 21h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"/>
                            </svg>
                        </div>
                        <h3>Soluciones Cloud</h3>
                        <p>Implementamos arquitecturas escalables y seguras en AWS, Azure y Google Cloud para potenciar tu transformación digital.</p>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <h3>Ciberseguridad</h3>
                        <p>Protegemos tus activos digitales con soluciones de seguridad avanzadas, garantizando tranquilidad y confianza.</p>
                    </div>
                </div>
            </section>

            <!-- Why Choose Us Section -->
            <section class="why-choose-us">
                <h2 class="section-title">Por Qué Elegirnos</h2>
                <div class="why-grid">
                    <div class="why-card">
                        <div class="why-icon">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <div class="why-content">
                            <h3>Certificación SENA</h3>
                            <p>Nuestros proyectos están respaldados por la excelencia y prestigio del Servicio Nacional de Aprendizaje.</p>
                        </div>
                    </div>

                    <div class="why-card">
                        <div class="why-icon">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                            </svg>
                        </div>
                        <div class="why-content">
                            <h3>Talento de Élite</h3>
                            <p>Contamos con un equipo de profesionales certificados en las tecnologías más innovadoras del mercado.</p>
                        </div>
                    </div>

                    <div class="why-card">
                        <div class="why-icon">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6l4.5 2.7.75-1.23L14 12.5V7h-3z"/>
                            </svg>
                        </div>
                        <div class="why-content">
                            <h3>Compromiso Temporal</h3>
                            <p>Entregamos proyectos puntuales con calidad excepcional, respetando tus plazos y expectativas.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
            <section class="contact-section" id="contact">
                <div class="contact-content">
                    <h2 class="section-title">¡Conecta con Nosotros!</h2>
                    <p class="hero-subtitle">Convierte tu visión en una realidad digital innovadora con nuestro equipo de expertos.</p>
                    <form>
                        <div class="form-group">
                            <label for="name">Nombre Completo</label>
                            <input type="text" id="name" name="name" placeholder="Tu nombre aquí" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Tu Proyecto</label>
                            <textarea id="message" name="message" placeholder="Cuéntanos sobre tu idea o consulta..." rows="8" required></textarea>
                        </div>
                        <button type="submit" class="cta-button login">
                            <svg class="button-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                            Enviar Mensaje
                        </button>
                    </form>
                </div>
            </section>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Smooth scroll
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        document.querySelector(this.getAttribute('href')).scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    });
                });

                // Intersection Observer for animations
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = 1;
                            entry.target.style.transform = 'translateY(0) scale(1) rotate(0deg)';
                            entry.target.style.transition = 'all 1.2s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                        }
                    });
                }, {
                    threshold: 0.4
                });

                // Animate cards
                document.querySelectorAll('.service-card, .why-card').forEach((card) => {
                    card.style.opacity = 0;
                    card.style.transform = 'translateY(100px) scale(0.9) rotate(-3deg)';
                    observer.observe(card);
                });

                // Parallax effect
                const hero = document.querySelector('.hero-section');
                window.addEventListener('scroll', () => {
                    const scrollPosition = window.pageYOffset;
                    hero.style.backgroundPositionY = `${scrollPosition * 0.5}px`;
                });

                // Form validation with animation
                const form = document.querySelector('form');
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const name = form.querySelector('#name').value;
                    const email = form.querySelector('#email').value;
                    const message = form.querySelector('#message').value;

                    if (name && email && message) {
                        const submitButton = form.querySelector('button[type="submit"]');
                        submitButton.style.transform = 'scale(1.2)';
                        submitButton.style.background = '#00A1D6';
                        setTimeout(() => {
                            alert('¡Mensaje enviado con éxito!');
                            form.reset();
                            submitButton.style.transform = 'scale(1)';
                            submitButton.style.background = '';
                        }, 300);
                    } else {
                        const inputs = form.querySelectorAll('input, textarea');
                        inputs.forEach(input => {
                            if (!input.value) {
                                input.style.animation = 'shake 0.3s ease';
                                setTimeout(() => input.style.animation = '', 300);
                            }
                        });
                        alert('Por favor, completa todos los campos.');
                    }
                });

                // Keyframe for shake animation
                const styleSheet = document.createElement('style');
                styleSheet.textContent = `
                    @keyframes shake {
                        0%, 100% { transform: translateX(0); }
                        25% { transform: translateX(-10px); }
                        75% { transform: translateX(10px); }
                    }
                `;
                document.head.appendChild(styleSheet);

                // Icon hover effects
                document.querySelectorAll('.icon').forEach(icon => {
                    icon.addEventListener('mouseenter', () => {
                        icon.style.transform = 'scale(1.4) rotate(15deg)';
                        icon.style.filter = 'drop-shadow(0 8px 15px rgba(0, 0, 0, 0.6))';
                    });
                    icon.addEventListener('mouseleave', () => {
                        icon.style.transform = 'scale(1.2) rotate(0deg)';
                        icon.style.filter = 'drop-shadow(0 5px 10px rgba(0, 0, 0, 0.5))';
                    });
                });

                // Mouse trail effect
                const canvas = document.createElement('canvas');
                canvas.style.position = 'fixed';
                canvas.style.top = '0';
                canvas.style.left = '0';
                canvas.style.width = '100%';
                canvas.style.height = '100%';
                canvas.style.pointerEvents = 'none';
                canvas.style.zIndex = '10000';
                document.body.appendChild(canvas);

                const ctx = canvas.getContext('2d');
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;

                const particles = [];
                const particleCount = 20;

                class Particle {
                    constructor(x, y) {
                        this.x = x;
                        this.y = y;
                        this.size = Math.random() * 5 + 2;
                        this.speedX = Math.random() * 2 - 1;
                        this.speedY = Math.random() * 2 - 1;
                        this.opacity = 1;
                    }

                    update() {
                        this.x += this.speedX;
                        this.y += this.speedY;
                        this.opacity -= 0.02;
                        this.size *= 0.98;
                    }

                    draw() {
                        ctx.beginPath();
                        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                        ctx.fillStyle = `rgba(255, 215, 0, ${this.opacity})`;
                        ctx.fill();
                    }
                }

                document.addEventListener('mousemove', (e) => {
                    for (let i = 0; i < particleCount; i++) {
                        particles.push(new Particle(e.clientX, e.clientY));
                    }
                });

                function animateParticles() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    particles.forEach((particle, index) => {
                        particle.update();
                        particle.draw();
                        if (particle.opacity <= 0 || particle.size <= 0.1) {
                            particles.splice(index, 1);
                        }
                    });
                    requestAnimationFrame(animateParticles);
                }

                animateParticles();

                window.addEventListener('resize', () => {
                    canvas.width = window.innerWidth;
                    canvas.height = window.innerHeight;
                });

                // Typewriter effect for hero subtitle
                const subtitle = document.querySelector('.hero-subtitle');
                const text = subtitle.textContent;
                subtitle.textContent = '';
                let i = 0;

                function typeWriter() {
                    if (i < text.length) {
                        subtitle.textContent += text.charAt(i);
                        i++;
                        setTimeout(typeWriter, 50);
                    }
                }

                setTimeout(typeWriter, 1000);
            });
        </script>
    </body>
</html>
