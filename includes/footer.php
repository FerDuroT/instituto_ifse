<footer class="main-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section about">
                <h3>Instituto IFSE</h3>
                <p>Supérate con nosotros</p>
                <div class="contact-info">
                    <span><i class="fas fa-map-marker-alt"></i> Quito, Ecuador</span>
                    <span><i class="fas fa-phone"></i> +593 5555555</span>
                    <span><i class="fas fa-envelope"></i> info@ifse.edu.ec</span>
                </div>
            </div>
            <div class="footer-section links">
                <h3>Enlaces Rápidos</h3>
                <ul>
                    <li><a href="https://www.ministeriodelinterior.gob.ec/seguridad-privada/" target="_blank"> SICOSEP - Ministerio del Interior</a></li>
                    <li><a href="/instituto_ifse/cursos.php">Catálogo de cursos</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/instituto_ifse/pages/miperfil.php">Mi Perfil</a></li>
                        
                    <?php else: ?>
                        <li><a href="/instituto_ifse/auth/login.php">Iniciar Sesión</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="footer-section social">
                <h3>Síguenos</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            Instituto IFSE. Derechos reservados.
        </div>
    </div>
</footer>
<!-- Para iconos de redes sociales: -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />