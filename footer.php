	</div><!--  closing "content" -->
	<?php if ( 'home' != $action ) : ?>
	<a href="" id="xmlrpc_validator_log_view_switcher" onclick="xml_rpc_validator.toggle_log( ); return false;">Show Log</a>
	<?php endif; ?>
	<div style="clear: both;"></div>
	<div id="xmlrpc_validator_log" style="display: none">
	<?php echo $xml_rpc_validator_utils->show_log_on_video( ); ?>
	</div>
	
	<footer class="site-footer">
		<div class="footer-content">
			<p>
				Third-party validation tool | Built with WordPress | 
				Maintained by <a href="https://joash.blog" target="_blank" rel="noopener">Joash</a>
			</p>
		</div>
		<?php wp_footer(); ?>
	</footer>
</body>
</html>

