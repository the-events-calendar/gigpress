<?php
/**
 * Add the suggested privacy policy text to the policy postbox.
 *
 * @since 2.3.21
 */
function gigpress_privacy_policy_content() {

	if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
		return false;
	}

	$content = gigpress_default_privacy_policy_content();
	wp_add_privacy_policy_content( __( 'Gigpress', 'gigpress' ), $content );
}

/**
 * Return the default suggested privacy policy content.
 *
 * @param bool $descr Whether to include the descriptions under the section headings. Default false.
 *
 * @since 2.3.21
 *
 * @return string The default policy content.
 */
function gigpress_default_privacy_policy_content() {

	ob_start();
	?>
	<div class="gigpress-privacy">

		<p class="privacy-policy-tutorial"><?php esc_html_e( 'Hello,', 'gigpress' ); ?></p>
		<p class="privacy-policy-tutorial"><?php esc_html_e( 'This information serves as a guide on what sections need to be modified due to usage of GigPress.', 'gigpress' ); ?></p>
		<p class="privacy-policy-tutorial"><?php esc_html_e( 'You should include the information below in the correct sections of you privacy policy.', 'gigpress' ); ?></p>
		<p class="privacy-policy-tutorial"><strong> <?php esc_html_e( 'Disclaimer:', 'gigpress' ); ?></strong> <?php esc_html_e( 'This information is only for guidance and not to be considered as legal advice.', 'gigpress' ); ?></p>

		<h2><?php esc_html_e( 'What personal data we collect and why we collect it', 'gigpress' ); ?></h2>

		<h3><?php esc_html_e( 'Event, Venue, and Organizer Information', 'gigpress' ); ?></h3>

		<p class="privacy-policy-tutorial"><?php esc_html_e( 'Through the usage of The Events Calendar, Events Calendar PRO, The Events Calendar Filter Bar, Eventbrite Tickets, and Community Events plugins, as well as our Event Aggregator Import service (contained within The Events Calendar plugin), information may be collected and stored within your website’s database.', 'gigpress' ); ?></p>
		<p class="privacy-policy-tutorial"><strong><?php esc_html_e( 'Suggested text:', 'gigpress' ); ?></strong></p>
		<p><?php esc_html_e( 'If you create, submit, import, save, or publish Show, Venue, or Artist Information, such information is retained in the local database:', 'gigpress' ); ?></p>

		<ol>
			<li><?php esc_html_e( 'Show information: date, artist name, website, venue, ticket URL, phone', 'gigpress' ); ?></li>
			<li><?php esc_html_e( 'Venue information: name, address, city, country, state, postal code, phone, website', 'gigpress' ); ?></li>
			<li><?php esc_html_e( 'Artist information: name, website', 'gigpress' ); ?></li>
		</ol>

		<h3 class="privacy-policy-tutorial"><?php esc_html_e( 'How Long You Retain this Data', 'gigpress' ); ?></h3>

		<p class="privacy-policy-tutorial"><?php esc_html_e( 'All information (data) is retained in the local database indefinitely, unless otherwise deleted.', 'gigpress' ); ?></p>

		<p class="privacy-policy-tutorial"><?php esc_html_e( 'Certain data may be exported or removed upon users request via the existing Exporter or Eraser. Please note, however, that several “edge cases” exist in which we are unable to perfect the gathering and export of all data for your end users. We suggest running a search in your local database, as well as within the WordPress Dashboard, in order to identify all data collected and stored for your specific user requests.', 'gigpress' ); ?></p>

		<h3 class="privacy-policy-tutorial"><?php esc_html_e( 'Where We Send Your Data', 'gigpress' ); ?></h3>

		<p class="privacy-policy-tutorial"><?php esc_html_e( 'Modern Tribe does not send any of your end users’ data outside of your website by default.', 'gigpress' ); ?></p>

		<p class="privacy-policy-tutorial"><?php esc_html_e( 'If you have extended our plugin(s) to send data to a third-party service such as Google Maps or PayPal, user information may be passed to these external services. These services may be located abroad.', 'gigpress' ); ?></p>

	</div>

	<?php
	$content = ob_get_clean();

	/**
	 * Filters the default content suggested for inclusion in a privacy policy.
	 *
	 * @since 2.3.21
	 *
	 * @param $content string The default policy content.
	 */
	return apply_filters( 'gigpress_default_privacy_policy_content', $content );
}