<?php require_once 'includes/header.php'; ?>

        <div class="et_pb_section et_pb_with_background et_section_regular contact-banner"
            style="background-image: url(./images/contact-page-banner-1.jpg) !important;">
            <div class="et_pb_row">
                <div
                    class="et_pb_column et_pb_column_4_4 et_pb_column_0  et_pb_css_mix_blend_mode_passthrough et-last-child">
                    <div class="et_pb_module et_pb_text et_pb_text_0  et_pb_text_align_left et_pb_bg_layout_light">
                        <div class="et_pb_text_inner">
                            <h1>Contact Us</h1>
                        </div>
                    </div> <!-- .et_pb_text -->
                </div> <!-- .et_pb_column -->
            </div> <!-- .et_pb_row -->
        </div>

    <div class="et_pb_section et_pb_section_1 et_section_regular">
        <div class="container"> <!-- Bootstrap container for responsive design -->
            <div class="row">
                <!-- Text content column -->
                <div
                    class="col-lg-6 col-md-6 col-12 et_pb_column et_pb_column_1_2 et_pb_column_1 et_pb_css_mix_blend_mode_passthrough">
                    <div class="et_pb_module et_pb_text et_pb_text_1 et_pb_text_align_left et_pb_bg_layout_light">
                        <div class="et_pb_text_inner">
                            <h1>Contact Us</h1>
                        </div>
                    </div> <!-- .et_pb_text -->

                    <div class="et_pb_module et_pb_text et_pb_text_2 et_pb_text_align_left et_pb_bg_layout_light">
                        <div class="et_pb_text_inner">
                            <p style="text-align: left;">Please contact us to know more about the Divine teachings,
                                philosophy and eternal preachings of our six Holy Masters.</p>
                        </div>
                    </div> <!-- .et_pb_text -->

                    <div
                        class="et_pb_module et_pb_text et_pb_text_3 et_pb_text_align_left et_pb_bg_layout_light et_multi_view__hover_selector">
                        <div class="et_pb_text_inner">
                            <h6><strong>Address:</strong></h6>
                            <p><strong>Shri Param Hans Advait Mat&nbsp;Ontario</strong></p>
                            <p><span style="font-size: 16px;">260 Ingleton Blvd, </span></p>
                            <p><span style="font-size: 16px;">Scarborough,&nbsp;</span><span style="font-size: 16px;">ON
                                    M1V 3R1.<br> </span></p>
                            <p><span style="font-size: 16px;">Canada</span></p>
                        </div>
                    </div> <!-- .et_pb_text -->
                </div> <!-- .col -->

                <!-- Contact form column -->
                <div
                    class="col-lg-6 col-md-6 col-12 et_pb_column et_pb_column_1_2 et_pb_column_2 et_pb_css_mix_blend_mode_passthrough et-last-child">
                    <div id="et_pb_contact_form_0"
                        class="et_pb_module et_pb_contact_form_0 et_pb_recaptcha_enabled et_pb_contact_form_container clearfix"
                        data-form_unique_num="0">
                        <div class="et-pb-contact-message"></div>

                        <div class="et_pb_contact">
                            <form id="contactForm" class="et_pb_contact_form clearfix" method="post"
                                action="processform.php">
                                <p class="et_pb_with_border et_pb_contact_field et_pb_contact_field_0 et_pb_text_align_left et_pb_contact_field_last"
                                    data-id="name" data-type="input">
                                    <label for="et_pb_contact_name_0" class="et_pb_contact_form_label">Name</label>
                                    <input type="text" id="et_pb_contact_name_0" class="input contact-border" value=""
                                        name="name" data-required_mark="required" data-field_type="input"
                                        data-original_id="name" placeholder="Name" required>
                                </p>
                                <p class="et_pb_with_border et_pb_contact_field et_pb_contact_field_1 et_pb_text_align_left et_pb_contact_field_last"
                                    data-id="email" data-type="email">
                                    <label for="et_pb_contact_email_0" class="et_pb_contact_form_label">Email
                                        Address</label>
                                    <input type="email" id="et_pb_contact_email_0" class="input contact-border" value=""
                                        name="email" data-required_mark="required" data-field_type="email"
                                        data-original_id="email" placeholder="Email Address" required>
                                <div id="emailError" style="color: red; display: none;">Please enter a valid email
                                    address.</div>
                                </p>
                                <p class="et_pb_with_border et_pb_contact_field et_pb_contact_field_2 et_pb_text_align_left et_pb_contact_field_last"
                                    data-id="phone" data-type="input">
                                    <label for="phoneNumber" class="et_pb_contact_form_label">Phone Number</label>
                                    <input type="tel" id="phoneNumber" class="input contact-border" value=""
                                        name="phone" data-required_mark="required" data-field_type="input"
                                        data-original_id="phone" placeholder="Phone Number" inputmode="numeric"
                                        pattern="\d{10}" title="Please enter exactly 10 digits." required>
                                <div id="phoneError" style="color: red; display: none;">Phone number must be exactly 10
                                    digits and contain only numbers.</div>
                                </p>
                                <p class="et_pb_with_border et_pb_contact_field et_pb_contact_field_3 et_pb_contact_field_last"
                                    data-id="message" data-type="text">
                                    <label for="et_pb_contact_message_0"
                                        class="et_pb_contact_form_label">Message</label>
                                    <textarea name="message" id="et_pb_contact_message_0"
                                        class="et_pb_contact_message input contact-border" data-required_mark="required"
                                        data-field_type="text" data-original_id="message" placeholder="Message"
                                        required></textarea>
                                </p>
                                <input type="hidden" value="et_contact_proccess" name="et_pb_contactform_submit_0">
                                <div class="et_contact_bottom_container">
                                    <button type="submit" name="et_builder_submit_button"
                                        class="et_pb_contact_submit et_pb_button et_pb_custom_button_icon"
                                        data-icon="$">Contact us</button>
                                </div>
                                <input type="hidden" id="_wpnonce-et-pb-contact-form-submitted-0"
                                    name="_wpnonce-et-pb-contact-form-submitted-0" value="32c10bd0d9">
                                <input type="hidden" name="_wp_http_referer" value="/contact-us/">
                            </form>
                        </div> <!-- .et_pb_contact -->
                    </div> <!-- .et_pb_contact_form_container -->
                </div> <!-- .col -->
            </div> <!-- .row -->
        </div> <!-- .container -->
    </div> <!-- .et_pb_section -->

    <script>
        document.getElementById("phoneNumber").addEventListener("input", function () {
            const phoneInput = this;
            const phoneError = document.getElementById("phoneError");
        
            // Allow only numeric input
            phoneInput.value = phoneInput.value.replace(/\D/g, '');
        
            // Check if the phone number is exactly 10 digits
            if (phoneInput.value.length !== 10) {
                phoneError.style.display = 'block';
                phoneInput.classList.add('is-invalid');
            } else {
                phoneError.style.display = 'none';
                phoneInput.classList.remove('is-invalid');
            }
        });
        
        document.getElementById("et_pb_contact_email_0").addEventListener("input", function () {
            const emailInput = this;
            const emailError = document.getElementById("emailError");
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
            // Check if the email is valid
            if (!emailPattern.test(emailInput.value)) {
                emailError.style.display = 'block';
                emailInput.classList.add('is-invalid');
            } else {
                emailError.style.display = 'none';
                emailInput.classList.remove('is-invalid');
            }
        });
        
        // Prevent form submission if phone number or email is invalid
        document.getElementById("contactForm").addEventListener("submit", function (event) {
            const phoneInput = document.getElementById("phoneNumber");
            const emailInput = document.getElementById("et_pb_contact_email_0");
        
            let valid = true;
        
            // Check phone number validity
            if (phoneInput.value.length !== 10) {
                phoneInput.classList.add('is-invalid');
                document.getElementById("phoneError").style.display = 'block';
                valid = false;
            }
        
            // Check email validity
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(emailInput.value)) {
                emailInput.classList.add('is-invalid');
                document.getElementById("emailError").style.display = 'block';
                valid = false;
            }
        
            if (!valid) {
                event.preventDefault(); // Prevent form submission
            }
        });
        </script>

<?php include 'includes/footer.php'; ?>