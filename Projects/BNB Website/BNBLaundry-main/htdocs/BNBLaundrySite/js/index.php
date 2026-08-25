<?php
include 'db.php';

// Fetch announcements
$announcements = $conn->query("SELECT Title, Content, Date FROM Announcements ORDER BY Date DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BnB Laundry</title>
    <style>
       /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 150px;
            background-color: #f4fbff;
            text-align: center;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 50px;
            background-color: white;
            position: fixed;
            top: 0;
            width: 95%;
            z-index: 1000; 
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #00a8e8;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            padding: 0;
        }
        nav a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        .hero {
            padding: 50px 20px;
        }
        .discount-banner {
            background: #b3e0ff;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
        }
        h1 {
            font-size: 36px;
        }
        .btn-primary {
            background: #00a8e8;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .stats {
            margin-top: 20px;
            font-size: 18px;
        }
        .how-it-works {
            background: #b3e0ff;
            padding: 40px 20px;
            margin-top: 40px;
        }
        .steps {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .step {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 150px;
            font-size: 16px;
            font-weight: bold;
        }
        .announcements {
            background: #fff;
            padding: 20px;
            margin-top: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            overflow-y: auto;
            max-height: 300px;
        }
        .announcement {
            text-align: left;
            margin-bottom: 15px;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .announcement h3 {
            margin: 0;
            color: #007bb5;
        }
        .announcement p {
            margin: 5px 0;
        }
        footer {
            background: #ffffff;
            padding: 10px;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
<header>
    <img src="/BNBLaundrySite/js/images/bnb_logo(1).png" alt="BnB" width="300"> 
    <nav>
        <ul>
            <li><a href='index.php' class='active'>Home</a></li>
            <li><a href='order-tracking.php'>Order Tracking</a></li>
            <li><a href='services.php'>Services</a></li>
            <li><a href='about.php'>About Us</a></li>
            <li><a href='staff-login.php'>Staff Login</a></li>
        </ul>
    </nav>
</header>

<div class='container'>
    <section class='hero'>
        <div class='discount-banner'>20% Discount Text Placeholder</div>
        <h1>Laundry today or <br> Text Placeholder.</h1>
        <p>BnB Laundry service will wash, dry, and fold your laundry at an affordable price. Pickup and drop-off options available!</p>
        <button class='btn-primary'>How it works</button>
        <div class='stats'>
            <span><strong>18k+</strong> Happy Customers</span> &nbsp; | &nbsp;
            <span><strong>10+</strong> Years of Experience</span>
        </div>
    </section>

    <section class='how-it-works'>
        <h2>HOW IT WORKS</h2>
        <h3>Get it done in 4 steps</h3>
        <div class='steps'>
            <div class='step'><h4>STEP 1</h4><p>Pickup</p></div>
            <div class='step'><h4>STEP 2</h4><p>Wash & Dry</p></div>
            <div class='step'><h4>STEP 3</h4><p>Fold</p></div>
            <div class='step'><h4>STEP 4</h4><p>Delivery</p></div>
        </div>
    </section>
            <section class='announcements'>
        <h2>Latest Announcements</h2>
        <?php foreach ($announcements as $announcement): ?>
            <div class='announcement'>
                <h3><?= htmlspecialchars($announcement['Title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($announcement['Content'])) ?></p>
                <small>Posted on <?= date("F j, Y", strtotime($announcement['Date'])) ?></small>
            </div>
        <?php endforeach; ?>
    </section>
</div>

<footer>
    <p>&copy; <?php echo date("Y"); ?> BnB Laundry. All Rights Reserved.</p>
</footer>

<!-- Chatbot Script -->
<script>
(function(){
    if(!window.chatbase || window.chatbase("getState") !== "initialized"){
        window.chatbase = (...arguments) => {
            if(!window.chatbase.q){ window.chatbase.q = [] }
            window.chatbase.q.push(arguments);
        };
        window.chatbase = new Proxy(window.chatbase, {
            get(target, prop){
                if(prop === "q"){ return target.q }
                return(...args) => target(prop, ...args);
            }
        });
    }
    const onLoad = function(){
        const script = document.createElement("script");
        script.src = "https://www.chatbase.co/embed.min.js";
        script.id = "KtPEJaVfs0TWJcL3OJ9u6";
        script.domain = "www.chatbase.co";
        document.body.appendChild(script);
    };
    if(document.readyState === "complete"){ onLoad(); }
    else{ window.addEventListener("load", onLoad); }
})();
</script>
</body>
</html>
