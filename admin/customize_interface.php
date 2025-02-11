<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role']!='admin') {
    header("Location: ../log_reg.html");
    exit();
}
$username = $_SESSION['username'];
?>
<html>
<head>
    <title>Customize Interface</title>
    <style>

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            background-color:rgb(0, 0, 0); /* Dim green theme */
            margin: 0;
        }
        
        .button-49,
        .button-49:after {
        width: 150px;
        height: 76px;
        line-height: 78px;
        font-size: 20px;
        font-family: 'Bebas Neue', sans-serif;
        background: linear-gradient(45deg, transparent 5%, #FF013C 5%);
        border: 0;
        color: #fff;
        letter-spacing: 3px;
        box-shadow: 6px 0px 0px #00E6F6;
        outline: transparent;
        position: relative;
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        margin: 10px;
        }

        .button-49:after {
        --slice-0: inset(50% 50% 50% 50%);
        --slice-1: inset(80% -6px 0 0);
        --slice-2: inset(50% -6px 30% 0);
        --slice-3: inset(10% -6px 85% 0);
        --slice-4: inset(40% -6px 43% 0);
        --slice-5: inset(80% -6px 5% 0);
        
        content: 'Customize Interface';
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 3%, #00E6F6 3%, #00E6F6 5%, #FF013C 5%);
        text-shadow: -3px -3px 0px #F8F005, 3px 3px 0px #00E6F6;
        clip-path: var(--slice-0);
        }

        .button-49:hover:after {
        animation: 1s glitch;
        animation-timing-function: steps(2, end);
        }

        h2 {
        color: white;
        font-size: 2em;
        position: relative;
        animation: glitch 1s;
        animation-timing-function: steps(2, end);
     }

     .h2:hover:after {
        animation: 1s glitch;
        animation-timing-function: steps(2, end);
        }

        @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes slideIn {
        from {
            transform: translateY(50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

        @keyframes glitch {
        0% {
            clip-path: var(--slice-1);
            transform: translate(-20px, -10px);
        }
        10% {
            clip-path: var(--slice-3);
            transform: translate(10px, 10px);
        }
        20% {
            clip-path: var(--slice-1);
            transform: translate(-10px, 10px);
        }
        30% {
            clip-path: var(--slice-3);
            transform: translate(0px, 5px);
        }
        40% {
            clip-path: var(--slice-2);
            transform: translate(-5px, 0px);
        }
        50% {
            clip-path: var(--slice-3);
            transform: translate(5px, 0px);
        }
        60% {
            clip-path: var(--slice-4);
            transform: translate(5px, 10px);
        }
        70% {
            clip-path: var(--slice-2);
            transform: translate(-10px, 10px);
        }
        80% {
            clip-path: var(--slice-5);
            transform: translate(20px, -10px);
        }
        90% {
            clip-path: var(--slice-1);
            transform: translate(-10px, 0px);
        }
        100% {
            clip-path: var(--slice-1);
            transform: translate(0);
        }
        }

        @media (min-width: 768px) {
        .button-49,
        .button-49:after {
            width: 200px;
            height: 86px;
            line-height: 88px;
        }
        }
    </style>
</head>
<body>
    <div>
        <centre><h2>What do you want to customize?</h2></centre>
        <a href="customize_home.php"><button class="button-49" role="button">Home Page</button></a>
        <a href="customize_about.php"><button class="button-49" role="button">About Page</button></a>
        <a href="customize_contact.php"><button class="button-49" role="button">Contact Page</button></a>
    </div>
    <div style="margin-top: 20px;">
        <a href="admin6096.php"><button class="button-49" role="button">Back</button></a>
    </div>
</body>
</html>