

					<!--SELECT EDITOR ALL-->
					<?php 
						if(etruel_check_user_role('administrator')){
						$myadsmanager = get_option('wpmyads_manager');
					?>
					<div class="postbox" style="padding:10px;">
						<h3 class="handle"><?php _e( 'Settings','myads' );?></h3>
						<p><?php _e( 'Select manager to manage ads','myads' );?></p>
						<div class="inside" style="margin: 0 -12px -12px -10px;">
							<?php
								$wpemyads_manager = get_users();
							?>
							<select name="wpemyads_manager" id="wpemyads_manager" style="width:100%;">
								<?php foreach ($wpemyads_manager as $key) {?>
										<option <?php selected($myadsmanager,$key->data->ID); ?>  value="<?php echo $key->data->ID; ?>"><?php echo $key->data->display_name; ?></option>
								<?php 
							}
									?>
							 </select>
							 <br>
							 <br>
							 <input type="button" class="button button-primary" id="wpemyads_save_manager" value="<?php _e( 'Save','myads' ); ?>">
							<span class="wpmyads_message_ajax" style="display:none;"></span>
						</div>
					</div>
					<?php } ?>

					<div class="postbox">
						<h3 class="handle"><?php _e( 'Knows my plugins','myads' );?></h3>
						<div class="inside" style="margin: 0 -12px -12px -10px;">
							<div class="wpeplugname" id="wpebanover"><a href="http://wordpress.org/plugins/wpecounter/" target="_Blank" class="wpelinks">WPeCounter</a>
							<div id="wpecounterdesc" class="tsmall" style="display:none;">Visits Post(types) counter. Shown in a sortable column the number of visits on lists of posts, pages, etc. Is extremely lightweight because it works with ajax.</div></div>
							<p></p>
							<div class="wpeplugname" id="wpebanover"><a href="http://wordpress.org/plugins/wpebanover/" target="_Blank" class="wpelinks">WPeBanOver</a>
							<div id="wpebanoverdesc" class="tsmall" style="display:none;">Show a small banner and on mouse event (over, out, click, dblclick) show another big or 2nd banner anywhere in your template, post, page or widget.</div></div>
							<p></p>
							<div class="wpeplugname" id="WPeMatico"><a href="http://wordpress.org/plugins/wpematico/" target="_Blank" class="wpelinks">WPeMatico</a>
							<div id="WPeMaticodesc" class="tsmall" style="display:none;"> WPeMatico is for autoblogging. Drink a coffee meanwhile WPeMatico publish your posts. Post automatically from the RSS/Atom feeds organized into campaigns.</a></div></div>
							<p></p>
							<div class="wpeplugname" id="WPeDPC"><a href="http://wordpress.org/plugins/etruel-del-post-copies/" target="_Blank" class="wpelinks">WP-eDel post copies</a>
							<div id="WPeDPCdesc" class="tsmall" style="display:none;">WPeDPC search for duplicated title name or content in posts in the categories that you selected and let you TRASH all duplicated posts in manual mode or automatic scheduled with WordPress Cron.</a></div></div>
							<p></p>
							<div class="wpeplugname" id="WPeBacklinks"><a href="http://www.netmdp.com/2011/10/wpebacklinks/" target="_Blank" class="wpelinks">WPeBacklinks</a>
							<div id="WPeBacklinksdesc" class="tsmall" style="display:none;">Backlinks.com’s original plugin allow only one key for wordpress site.
							This plugin makes it easier to use different keys to use Backlinks assigned for each page or section of wordpress. If you want to make some money, please register on <a href="http://www.backlinks.com/?aff=52126" class="wpeoverlink" target="_Blank">Backlinks.com here.</a></div></div>
							<p></p>
						</div>
					</div>
<script type="text/javascript">
	<?php 
		$nonce = wp_create_nonce('wpemyads_manager_nonce' );
	?>
	jQuery(document).ready(function($){
		$("#wpemyads_save_manager").click(function() {
			$(".wpmyads_message_ajax").text('Saving Manager....').show(0);
			var wpmyads_manager = $("#wpemyads_manager").val();
			data = {
				'action': 'save_wpmyads_manager',
				_ajax_nonce : "<?php echo $nonce; ?>",
				'wpmyads_manager':wpmyads_manager
			};
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				$(".wpmyads_message_ajax").text('Save Manager!').delay(1000).fadeOut(500);
				//response
			});
		});
	});

</script>