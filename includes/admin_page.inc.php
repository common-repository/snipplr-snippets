<div class='wrap' id="snipplrSnippets">
	<div id="icon-edit" class="icon32">&nbsp;</div>
	<h2>Snipplr Snippets v1.0.1</h2>
	<?php echo $message; ?>
	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<p>Enter a valid API key, you will find this under the 'Settings' page of your <a href="http://snipplr.com/">Snipplr</a> account.</p>
		<h3><label for="APIkey">Snipplr API Key</label></h3>
		<ul id="key">
			<li><input type='text' name='APIkey' value='<?php echo $key; ?>' id='APIkey' size='25' /></li>
		</ul>
		<input class='button-primary' type="submit" name="snip_key_save" value="Save API Key &raquo;" id="snip_key_save" />
		
		<h3>Display Options</h3>
		<ul id="snippetDisplayOptions">
			<li><strong>Snippet Display Settings</strong></li>
			<li><input type='checkbox' name='csshead' value='1' id='csshead' <?php echo $csshead; ?> /> <label for="cssHead">Disable plug-in CSS in site header?</label></li>
			<li><input type='checkbox' name='title' value='1' id='title' <?php echo $title; ?> /> <label for="title">Show snippet title?</label></li>
			<li><input type='checkbox' name='author' value='1' id='author' <?php echo $author; ?> /> <label for="author">Show snippet author?</label></li>
			<li><input type='checkbox' name='comment' value='1' id='comment' <?php echo $comment; ?> /> <label for="comment">Show snippet's comment?</label></li>
		</ul>
		
		<ul id="geshiDisplayOptions">
			<li><strong>GeSHi Display Settings</strong></li>
			<li><input type='checkbox' name='numbers' value='1' id='numbers' <?php echo $numbers; ?> /> <label for="numbers">Show line numbers?</label></li>
			<li><input type='checkbox' name='highlight' value='1' id='highlight' <?php echo $highlight; ?> /> <label for="highlight">Disable snippet syntax highlighting?</label></li>
		</ul>
		<input class='button-primary' type="submit" name="snip_settings_save" value="Save Settings &raquo;" id="snip_settings_save" />
		
		<h3>Uninstall Plug-in</h3>
		<ul id="snip_uninstall">
			<li><p>This button simply removes the options written by the plug-in from the database.</p></li>
		</ul>
		<input class='button-secondary' type="submit" name="snp_uninstall" value="Uninstall &raquo;" />
	</form>
</div><!-- div class='wrap' id="snipplrSnippets" -->