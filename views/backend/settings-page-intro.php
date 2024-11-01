<div class="wccpg-intro">
	<a href="https://wpsuperadmins.com/company/contact/?utm_source=wp-admin&utm_campaign=contact-button&utm_medium=conditional-payment-methods" class="button"><span class="dashicons dashicons-editor-help"></span> <?php 
_e( 'Need help? Contact us' );
?></a> - 
	<?php 
?>
		<a href="<?php 
echo WCCPG()->args['buy_url'];
?>" class="button button-primary" target="_blank"><span class="dashicons dashicons-cart"></span> <?php 
echo WCCPG()->args['buy_text'];
?></a>
		<?php 
?>
	<br>
	<?php 
_e( 'You can receive quick help. We have a live chat on our website and your support messages will be responded today' );
?>
</div>
<style>
	h2 {
		text-align: center;
	}
	.wccpg-conditions-list {
		max-width: 670px;
	}
	.wccpg-intro {
		border-top: 1px solid #d2d2d2;
		border-bottom: 1px solid #d2d2d2;
		text-align: center;
		padding: 8px 0;
	}
	.wccpg-intro .dashicons {
		margin-top: 3px;
	}
	#poststuff .inside .wccpg-intro {
		margin-top: 45px;
	} 
</style>