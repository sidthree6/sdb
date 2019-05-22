<?php
if(isset($_POST['syntax']))
{
		$text = $_POST['text'];
		highlight_string($text);
		echo "<br/><br/>";
}
?>
<form action="syntex_highlighter.php" method="post">
<textarea cols="100" rows="20" name="text"></textarea><br/><br/>
<input type="submit" name="syntax" value="Add Syntax" />
</form>