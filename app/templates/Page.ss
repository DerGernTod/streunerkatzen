<!doctype html>

<html lang="de">

<head>
    <meta charset="utf-8">

    <% base_tag %>

    <title>Streunerkatzen - $Title</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Streunerkatzen">
    <meta name="description" content="$MetaDescription">
    <meta name="keywords" content="$Keywords">
    <meta name="robots" content="index,follow">

    <!-- Open Graph Meta tags (Facebook, Xing, Linked In) -->
    <meta property="og:url" content="$CurrentAbsoluteURL">
    <meta property="og:type" content="website">
    <meta property="og:title" content="$Title">
    <meta property="og:description" content="$MetaDescription">
    <meta property="og:image" content="">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="628">
    <meta property="og:locale" content="de_DE">
    <meta property="og:site_name" content="Streunerkatzen">
    <!-- End Open Graph Meta tags -->

    <!-- Twitter Meta tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="$Title">
    <meta name="twitter:description" content="$MetaDescription">
    <meta name="twitter:image" content="">
    <!-- End Twitter Meta tags -->

    <!-- Google+ Meta tags -->
    <meta itemprop="name" content="$Title">
    <meta itemprop="description" content="$MetaDescription">
    <meta itemprop="image" content="">
    <!-- End Google+ Meta tags -->

    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
</head>

<body>
    <% include Header %>
    <a id="anchor-top" class="hidden"></a>
    <main>
        <div class="content-wrapper">
            $Form
            $Layout
        </div>
        <% include Sidebar %>
    </main>

    <% include BackToTop %>
    <% include Footer %>
</body>

</html>
