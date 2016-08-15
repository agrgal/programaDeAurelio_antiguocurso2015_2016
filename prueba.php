<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>position demo</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<style>
.positionDiv {
position: absolute;
width: 75px;
height: 75px;
background: red;
}
</style>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
</head>
<body>
<div id="targetElement">
<div class="positionDiv" id="position1" style="background: red;"></div>
<div class="positionDiv" id="position2" style="background: blue;"></div>
<div class="positionDiv" id="position3" style="background: yellow;"></div>
<div class="positionDiv" id="position4" style="background: black;"></div>
</div>
<script>
$( "#position1" ).position({
my: "center",
at: "center",
of: "#targetElement"
});
$( "#position2" ).position({
my: "left top",
at: "left top",
of: "#targetElement"
});
$( "#position3" ).position({
my: "right",
at: "down",
of: "#targetElement"
});
$( document ).mousemove(function( event ) {
$( "#position4" ).position({
my: "left+3 bottom-3",
of: event,
collision: "fit"
});
});
</script>
</body>
</html>
