<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="icon" type="image/png" href="{{asset('image/logo/k-symbol.png')}}"/>
	<title>404 Error</title>

	<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

<style type="text/css">
body{
	margin: 0;
	font-family: 'Roboto', sans-serif;
}
.error-wrap {
    background: linear-gradient(to right, rgb(127, 127, 213), rgb(134, 168, 231), rgb(145, 234, 228));
    width: 100%;
    display: flex;
    height: 100vh;
    align-items: center;
    flex-direction: column;
    justify-content: center;
}
.error-wrap h1 {
   	color: #fff;
    font-size: 50px;
    text-transform: uppercase;
    line-height: 60px;
    margin: 40px 0 25px 0;
}
.error-wrap p {
    margin: 0;
    text-align: center;
    color: #fff;
    font-size: 24px;
    line-height: 34px;
}
.error-wrap img {
    width: 25%;
}

@media screen and (max-width:991px){
	.error-wrap p {
	    font-size: 20px;
	    line-height: 30px;
	}
	.error-wrap h1 {
	    font-size: 40px;
	    line-height: 50px;
	}
	.error-wrap img {
	    width: 30%;
	}
}
@media screen and (max-width:700px){
	.error-wrap img {
	    width: 40%;
	}
}
@media screen and (max-width:480px){
	.error-wrap img {
	    width: 50%;
	}
}
@media screen and (max-width:320px){
	.error-wrap p {
	    font-size: 16px;
	    line-height: 24px;
	}
	.error-wrap h1 {
	    font-size: 30px;
	    line-height: 40px;
	}
	.error-wrap img {
	    width: 60%;
	}
}
</style>

</head>
<body>

<div class="error-wrap">
	<img src="{{ asset('backend/assets/dist/img/404.png') }}" alt="Error">
	<h1>Page not found</h1>
	<p>Sorry, we are not able to find what you <br>where looking for...!</p>
</div>

</body>
</html>