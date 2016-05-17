@section("footer")

<?php
$utscFooter = file_get_contents("https://www.utsc.utoronto.ca/_includes/application/_footer.html");
echo $utscFooter;
?>
@show