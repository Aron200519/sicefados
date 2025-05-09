<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FABRICASOFT - SENA</title>

       <style>
           .software-factory-container {
               width: 100%;
               font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
               line-height: 1.6;
               color: #333;
           }

           .icon {
               width: 4rem; /* Iconos más grandes */
               height: 4rem;
               display: inline-block;
           }

           .button-icon {
               width: 2rem;
               height: 2rem;
               display: inline-block;
               vertical-align: middle;
               margin-right: 0.5rem;
           }

           /* Hero Section Mejorada */
           .hero-section {
               position: relative;
               min-height: 100vh;
               background: linear-gradient(rgba(0, 126, 58, 0.9), rgba(0, 126, 58, 0.9)),
                         url('https://images.unsplash.com/photo-1518770660439-4636190af475') center/cover;
               display: flex;
               align-items: center;
               justify-content: center;
           }

           .hero-content {
               position: relative;
               z-index: 2;
               text-align: center;
               padding: 2rem;
               max-width: 900px;
           }

           .hero-icon .icon {
               color: #FFFFFF;
               filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
           }

           .hero-title {
               font-size: 3.5rem;
               font-weight: 800;
               margin: 2rem 0;
               line-height: 1.2;
               color: white;
               text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
           }

           .highlight {
               color: #FFD700; /* Dorado para mejor contraste */
               display: block;
               margin-top: 0.5rem;
           }

           .hero-subtitle {
               font-size: 1.5rem;
               margin-bottom: 3rem;
               font-weight: 400;
               color: white;
               max-width: 600px;
               margin-left: auto;
               margin-right: auto;
           }

           /* Botones Mejorados */
           .cta-button {
               display: inline-flex;
               align-items: center;
               padding: 1rem 2.5rem;
               font-size: 1.2rem;
               font-weight: 600;
               text-decoration: none;
               border-radius: 50px;
               transition: all 0.3s ease;
               margin: 0 1rem;
               text-transform: uppercase;
               letter-spacing: 1px;
           }

           .cta-button.primary {
               background: #FFFFFF;
               color: #007E3A; /* Verde SENA */
               box-shadow: 0 4px 15px rgba(0, 126, 58, 0.3);
           }

           .cta-button.secondary {
               background: transparent;
               color: white;
               border: 3px solid #FFFFFF;
           }

           .cta-button.login {
               background: #1F3A93; /* Azul institucional SENA */
               color: white;
           }

           .cta-button:hover {
               transform: translateY(-5px);
               box-shadow: 0 8px 25px rgba(0, 126, 58, 0.4);
           }

           /* Sección de Servicios */
           .services {
               padding: 6rem 2rem;
               background: #F8F9FA;
           }

           .section-title {
               color: #1F3A93; /* Azul SENA */
               font-size: 2.8rem;
               font-weight: 700;
               text-align: center;
               margin-bottom: 4rem;
               position: relative;
           }

           .section-title::after {
               content: '';
               display: block;
               width: 80px;
               height: 4px;
               background: #007E3A;
               margin: 1rem auto;
           }

           .services-grid {
               display: grid;
               grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
               gap: 3rem;
               max-width: 1200px;
               margin: 0 auto;
           }

           .service-card {
               background: white;
               box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
               padding: 3rem 2rem;
               border-radius: 15px;
               text-align: center;
               transition: all 0.4s ease;
           }

           .service-card:hover {
               transform: translateY(-10px);
               box-shadow: 0 15px 40px rgba(0, 126, 58, 0.15);
           }

           .service-icon .icon {
               color: #1F3A93; /* Azul SENA */
               margin-bottom: 2rem;
           }

           .service-card h3 {
               color: #007E3A; /* Verde SENA */
               font-size: 1.6rem;
               margin-bottom: 1rem;
           }

           /* Sección Por Qué Nosotros */
           .why-choose-us {
               padding: 6rem 2rem;
               background: #FFFFFF;
           }

           .why-grid {
               display: grid;
               grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
               gap: 3rem;
               max-width: 1200px;
               margin: 0 auto;
           }

           .why-card {
               display: flex;
               align-items: center;
               gap: 2rem;
               padding: 2rem;
               background: #F8F9FA;
               border-radius: 15px;
           }

           .why-icon {
               background: #007E3A; /* Verde SENA */
               border-radius: 15px;
               width: 6rem;
               height: 6rem;
               display: flex;
               align-items: center;
               justify-content: center;
               flex-shrink: 0;
           }

           .why-icon .icon {
               color: white;
               width: 3rem;
               height: 3rem;
           }

           .why-content h3 {
               color: #1F3A93; /* Azul SENA */
               font-size: 1.4rem;
               margin-bottom: 0.8rem;
           }

           /* Sección de Contacto */
           .contact-section {
               padding: 6rem 2rem;
               background: linear-gradient(135deg, #007E3A, #1F3A93);
               color: white;
           }

           .contact-content {
               max-width: 800px;
               margin: 0 auto;
               text-align: center;
           }

           .form-group {
               margin-bottom: 2rem;
               text-align: left;
           }

           .form-group label {
               color: white;
               font-size: 1.1rem;
               font-weight: 500;
               display: block;
               margin-bottom: 0.8rem;
           }

           .form-group input,
           .form-group textarea {
               width: 100%;
               padding: 1rem;
               font-size: 1rem;
               border: 2px solid rgba(255, 255, 255, 0.3);
               border-radius: 8px;
               background: rgba(255, 255, 255, 0.1);
               color: white;
               transition: all 0.3s ease;
           }

           .form-group input:focus,
           .form-group textarea:focus {
               border-color: white;
               background: rgba(255, 255, 255, 0.2);
               box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
           }

           @media (max-width: 768px) {
               .hero-title { font-size: 2.5rem; }
               .section-title { font-size: 2.2rem; }
               .cta-button { margin: 1rem 0; width: 100%; justify-content: center; }
               .why-card { flex-direction: column; text-align: center; }
               .why-icon { width: 5rem; height: 5rem; }
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
                            <path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zM10 4h4v2h-4V4zm10 15H4V8h16v11z"/>
                        </svg>
                    </div>
                    <h1 class="hero-title">FÁBRICA DE SOFTWARE<br><span class="highlight">SENA</span></h1>
                    <p class="hero-subtitle">Innovación tecnológica con calidad y compromiso institucional</p>
                    <div>
                        <a href="#contact" class="cta-button primary">
                            <svg class="button-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            Contáctanos
                        </a>
                        <a href="{{ route('fabricasoft.admin.welcome') }}" class="cta-button secondary">
                            <svg class="button-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            Acceso Plataforma
                        </a>
                        @if(Auth::check() && checkRol('fabricasoft.admin'))
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="{{ route('fabricasoftsoft.admin.welcome') }}" class="nav-link @if (Route::is('fabricasoft.admin.*')) active @endif">Administración</a>
                    </li>
                    @endif
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
                                <path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zM10 4h4v2h-4V4zm10 15H4V8h16v11z"/>
                            </svg>
                        </div>
                        <h3>Desarrollo Tecnológico</h3>
                        <p>Creación de software empresarial a medida con estándares internacionales</p>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19.35 10.04A7.49 7.49 0 0 0 12 4a7.49 7.49 0 0 0-7.35 6.04A5.5 5.5 0 0 0 0 15.5C0 18.54 2.46 21 5.5 21h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"/>
                            </svg>
                        </div>
                        <h3>Soluciones en la Nube</h3>
                        <p>Implementación de arquitecturas escalables en AWS, Azure y Google Cloud</p>
                    </div>

                    <div class="service-card">
                        <div class="service-icon">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 2.18l7 3.12v5.7c0 4.83-3.36 9.36-8 10.54-4.64-1.18-8-5.71-8-10.54V6.3l7-3.12z"/>
                            </svg>
                        </div>
                        <h3>Ciberseguridad</h3>
                        <p>Protección avanzada de datos y sistemas con tecnología de punta</p>
                    </div>
                </div>
            </section>

            <!-- Why Choose Us Section -->
            <section class="why-choose-us">
                <h2 class="section-title">Nuestra Ventaja</h2>
                <div class="why-grid">
                    <div class="why-card">
                        <div class="why-icon">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4h-2V2H6v2H4c-1.1 0-2 .9-2 2v3c0 2.21 1.79 4 4 4h1v3h10v-3h1c2.21 0 4-1.79 4-4V6c0-1.1-.9-2-2-2zm-14 5V6h2v5H6zm12 0V6h2v3h-2zM12 22c-1.1 0-2-.9-2-2h4c0 1.1-.9 2-2 2z"/>
                            </svg>
                        </div>
                        <div class="why-content">
                            <h3>Certificación SENA</h3>
                            <p>Proyectos avalados por el Servicio Nacional de Aprendizaje</p>
                        </div>
                    </div>

                    <div class="why-card">
                        <div class="why-icon">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                            </svg>
                        </div>
                        <div class="why-content">
                            <h3>Equipo Especializado</h3>
                            <p>Profesionales certificados en tecnologías emergentes</p>
                        </div>
                    </div>

                    <div class="why-card">
                        <div class="why-icon">
                            <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6l4.5 2.7.75-1.23L14 12.5V7h-3z"/>
                            </svg>
                        </div>
                        <div class="why-content">
                            <h3>Entregas Oportunas</h3>
                            <p>Comprometidos con los plazos establecidos</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
            <section class="contact-section" id="contact">
                <div class="contact-content">
                    <h2 class="section-title">Contáctanos</h2>
                    <p class="hero-subtitle">¡Transformemos juntos tu visión en realidad digital!</p>
                    <form>
                        <div class="form-group">
                            <label for="name">Nombre Completo</label>
                            <input type="text" id="name" name="name" placeholder="Ej: Juan Pérez" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Mensaje</label>
                            <textarea id="message" name="message" placeholder="Describe tu proyecto o consulta..." rows="5" required></textarea>
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
                // Animación suave al hacer scroll
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        document.querySelector(this.getAttribute('href')).scrollIntoView({
                            behavior: 'smooth'
                        });
                    });
                });

                // Animación de las tarjetas al hacer scroll
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = 1;
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                });

                document.querySelectorAll('.service-card, .why-card').forEach((card) => {
                    card.style.opacity = 0;
                    card.style.transform = 'translateY(50px)';
                    card.style.transition = 'all 0.6s ease-out';
                    observer.observe(card);
                });
            });
        </script>
    </body>
</html>
