<h2><?php echo $params['title']; ?></h2>
<div><?php echo $params['description']; ?></div>
<form id="<?php echo $params['form_id']; ?>" class="<?php echo $params['form_class']; ?>" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
    <label for="first_name">First Name:</label>
    <input type="text" id="first_name" name="first_name" placeholder="First Name" required />

    <label for="last_name">Last Name:</label>
    <input type="text" id="last_name" name="last_name" placeholder="Last Name" required />

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" placeholder="email@email.com" required>

    <label for="message">Message:</label>
    <textarea rows="3" id="message" name="message" placeholder="Type your message here" required></textarea>

    <input id="aveformssubmit" type="submit" value="<?php echo $params['button_text']; ?>" class="aveforms-submit">
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('aveforms_contact_form_nonce'); ?>">
    <input type="hidden" name="action" value="aveforms_contact_form">
    <div id="status" class="status"></div>
</form>