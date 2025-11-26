
    </main>

        <footer class="footer-section">

            <div class="container">

                <div class="row">

                    <!-- Left Side: Logos -->

                    <div class="col-lg-4 col-md-12 logos">

                        <div class="logo">

                            <img src="./images/logo-jd-light.png" alt="Jyotidham Logo" />

                        </div>

                        <div class="logo">

                            <img src="./images/logo-round-white.png" alt="logo-round-white" />

                        </div>

                    </div>

    

                    <!-- Right Side: Content and Links -->

                    <div class="col-lg-8 col-md-12 content">

                        <div class="row">

                            <!-- Text Section -->

                            <div class="col-12 text-section">

                                <p>After deep prayer and meditation, a devotee is in touch with his divine

                                    consciousness; there is no greater power than that inward protection.</p>

                            </div>

    

                            <!-- Two Columns -->

                            <div class="col-lg-6 col-md-12 links">

                                <h5>Find Us Here</h5>

                                <p>Shri Param Hans Advait Mat Ontario</p>

                                <p class="address">

                                    <img class="map-pin" src="./images/location.png"

                                        alt="Map Pin" />

                                    236 Ingleton Blvd, Scarborough,<br>

                                    ON M1V 3R1, Canada

                                </p>

                            </div>

                            <div class="col-lg-6 col-md-12 quick-links">

                                <h5>Quick Links</h5>

                                <p><a href="donate.php">Donate</a></p>

                                <p><a href="terms.html">Refund &amp; Privacy Policy</a>

                                </p>

                                <p>We accept</p>

                                <img src="./images/payment-cards-updated.png" alt="Payment Cards">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </footer>

    

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>

        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const togglePassword = document.querySelectorAll('.toggle-password');

                togglePassword.forEach(function (el) {
                    el.addEventListener('click', function () {
                        const target = this.dataset.target;
                        const passwordInput = document.getElementById(target);
                        
                        if (passwordInput.type === 'password') {
                            passwordInput.type = 'text';
                            this.classList.remove('fa-eye');
                            this.classList.add('fa-eye-slash');
                        } else {
                            passwordInput.type = 'password';
                            this.classList.remove('fa-eye-slash');
                            this.classList.add('fa-eye');
                        }
                    });
                });
            });
        </script>
    
