<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="icon" href="/favicon.ico">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="DC-Covers">
    <meta name="msapplication-TileImage" content="link to the image in static folder">
    <meta name="msapplication-TileColor" content="#000">
    <link rel="apple-touch-icon" href="/img/icons/apple-touch-icon-60x60.png">
    <title>Jala Story</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons">
    <link rel="stylesheet" href="/main.css">
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async></script>
    <script>
        var OneSignal = window.OneSignal || [];
        OneSignal.push(["init", {
            appId: "",
            autoRegister: false,
            welcomeNotification: {
                "title": "Thanks!",
                "message": "Have a nice day :)",
            },
            promptOptions: {
                actionMessage: "We'd like to show you notifications for the latest stories and updates.",
                acceptButtonText: "ALLOW",
                cancelButtonText: "NO THANKS"
            }
        }]);
    </script>
</head>
<body>
<noscript>
    <strong>We're sorry but post-bro doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
</noscript>
<div id="app"></div>
<script src="{{ mix('/js/main.js') }}"></script>
</body>
</html>
