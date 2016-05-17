<?php
	// Requires the variable $trail, which is an array of Category items
?>

<?php if (count($trail) > 0): ?>
<ol class="category-breadcrumb breadcrumb">
	<li><a href="{{ Request::url() }}">Categories</a></li>
	<?php
	for ($i = 0; $i < count($trail); $i++) {
		$id = $trail[$i]["id"];
		$name = $trail[$i]["name"];
		if ($i == count($trail) - 1)
			echo "<li class='active'>$name</li>";
		else
			echo "<li><a href='?id=$id'>$name</a></li>";
	}
	?>
</ol>
<?php endif; ?>
