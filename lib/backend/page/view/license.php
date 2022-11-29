<div class="wrap about-wrap full-width-layout qlwrap">
	<form method="post">
		<?php settings_fields( sanitize_key( $plugin_slug . '-qlwlm-create' ) ); ?>
		<table class="widefat striped">
			<thead>
				<tr>
					<th colspan="2"><b><?php echo __ql_translate( 'License'); ?></b></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><?php echo __ql_translate( 'Market'); ?></th>
					<td>
						<select style="width:350px" name="<?php echo esc_html( $plugin_slug ); ?>[license_market]">
							<option <?php selected( $user_data['license_market'], '' ); ?> value="">QuadLayers</option>
							<option <?php selected( $user_data['license_market'], 'envato' ); ?> value="envato">Envato</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __ql_translate( 'Email'); ?></th>
					<td>
						<input type="email" name="<?php echo esc_html( $plugin_slug ); ?>[license_email]" placeholder="<?php echo __ql_translate( 'Enter your order email.'); ?>" value="<?php echo esc_html( $user_data['license_email'] ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __ql_translate( 'Key'); ?></th>
					<td>
						<input type="password" name="<?php echo esc_html( $plugin_slug ); ?>[license_key]" placeholder="<?php echo __ql_translate( 'Enter your license key.'); ?>" value="<?php echo esc_attr( $user_data['license_key'] ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button( __ql_translate( 'Save'), 'primary' ); ?>	
	</form>	
	<form method="post">		
		<?php settings_fields( sanitize_key( $plugin_slug . '-qlwlm-delete' ) ); ?>
		<table class="widefat striped" cellspacing="0">
			<thead>
				<tr>
					<th colspan="2"><b><?php echo __ql_translate( 'Status'); ?></b></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $activation['license_created'] ) ) : ?>
					<tr>
						<td><?php echo __ql_translate( 'Created'); ?></td>
						<td><?php echo date( get_option( 'date_format' ), strtotime( $activation['license_created'] ) ); ?></td>
					</tr>
					<tr>
						<td><?php echo __ql_translate( 'Limit'); ?></td>
						<td><?php echo $activation['license_limit'] ? esc_attr( $activation['license_limit'] ) : __ql_translate( 'Unlimited'); ?></td>
					</tr>
					<tr>
						<td><?php echo __ql_translate( 'Activations'); ?></td>
						<td><?php echo esc_attr( $activation['activation_count'] ); ?></td>
					</tr>
					<tr>
						<td><?php echo __ql_translate( 'Updates'); ?></td>
						<td><?php echo ( $activation['license_expiration'] != '0000-00-00 00:00:00' && $activation['license_updates'] ) ? sprintf( __ql_translate( 'Expires on %s'), $activation['license_expiration'] ) : __ql_translate( 'Unlimited'); ?></td>
					</tr>
					<tr>
						<td><?php echo __ql_translate( 'Support'); ?></td>
						<td><?php echo ( $activation['license_expiration'] != '0000-00-00 00:00:00' && $activation['license_support'] ) ? sprintf( __ql_translate( 'Expires on %s'), $activation['license_expiration'] ) : __ql_translate( 'Unlimited'); ?></td>
					</tr>
					<tr>
						<td><?php echo __ql_translate( 'Expiration'); ?></td>
						<td><?php echo ( $activation['license_expiration'] != '0000-00-00 00:00:00' ) ? date_i18n( get_option( 'date_format' ), strtotime( $activation['license_expiration'] ) ) : __ql_translate( 'Unlimited'); ?></td>
					</tr>
				<?php endif; ?>
				<tr>
					<td>
						<b><?php echo __ql_translate( 'Notice'); ?></b>
					</td>
					<td>
						<span class="description">
							<?php if ( ! empty( $activation['message'] ) ) : ?>
								<?php echo esc_html( $activation['message'] ); ?>
							<?php endif; ?>
							<?php if ( ! empty( $activation['license_key'] ) ) : ?>
								<?php echo __ql_translate( 'Thanks for register your license!'); ?>
							<?php endif; ?>
							<?php if ( empty( $activation['message'] ) && empty( $activation['license_key'] ) ) : ?>
								<?php echo __ql_translate( 'Before you can receive plugin updates, you must first authenticate your license.'); ?>
							<?php endif; ?>

						</span>
					</td>
				</tr>
			</tbody>
		</table>
			<?php if ( $activation ) : ?>
				<?php submit_button( __ql_translate( 'Delete'), 'secondary' ); ?>		
			<?php endif; ?>
	</form>
</div>
