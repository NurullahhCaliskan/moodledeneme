<?php

require_once("$CFG->libdir/formslib.php");

class subscribeform extends moodleform {
    // Form tanımı burada yapılır
    function definition() {
        $mform = $this->_form; // Don't forget the underscore!

        // "ABONE OL" başlığını formun en üstüne ekleyin
        $mform->addElement('html', '<h3 class="form-section-title">ABONE OL</h3>');


        // E-posta adresi için bir alan ekleyin
        $mform->addElement('text', 'email', get_string('email')); // Element adı, etiket
        $mform->setType('email', PARAM_EMAIL); // Bu alanın bir e-posta adresi olduğunu belirtir
        $mform->addRule('email', null, 'required', null, 'client'); // Bu alanın doldurulması gerektiğini belirtir
        $mform->addRule('email', get_string('invalidemail'), 'email', null, 'client'); // Geçerli bir e-posta adresi olup olmadığını kontrol eder



        $this->add_action_buttons(false, get_string('submit'));




    }
}
