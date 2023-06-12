<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>회원가입 페이지</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        .signup-box {
            width: 300px;
            padding: 20px;
            background-color: #f7f7f7;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .signup-box h2 {
            text-align: center;
        }

        .signup-box input[type="text"],
        .signup-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .signup-box input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-link {
            text-align: center;
            margin-top: 10px;
        }

        .success-msg {
            color: green;
            margin-top: 10px;
            text-align: center;
        }

        .error-msg {
            color: red;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="signup-box">
        <h2>회원가입</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="text" name="userBusinessNumber" placeholder="아이디" required>
            <input type="password" name="passWd" placeholder="비밀번호" required>
            <input type="submit" name="signup" value="회원가입">
        </form>
        <div class="login-link">
            <a href="https://7ca7-223-38-81-187.ngrok-free.app/_signin.php">로그인으로 돌아가기</a>
        </div>
        <?php
        // MySQL 연결 설정
        $servername = "localhost";
        $username = "cookUser";
        $password = "1234";
        $dbname = "homPlus";

        // MySQL 연결
        $conn = new mysqli($servername, $username, $password, $dbname);

        // 연결 확인    
        if ($conn->connect_error) {
            die("MySQL 연결 실패: " . $conn->connect_error);
        }

        if (isset($_POST['signup'])) {
            $userBusinessNumber = $_POST['userBusinessNumber'];
            $passWd = $_POST['passWd'];

            // 회원가입 데이터를 삽입하는 쿼리
            $query = "INSERT INTO usertbl (userBusinessNumber, passWd) VALUES ('$userBusinessNumber', '$passWd')";

            if ($conn->query($query) === TRUE) {
                // 회원가입 성공
                echo '<p class="success-msg">회원가입이 성공적으로 완료되었습니다.</p>';
            } else {
                // 회원가입 실패
                echo '<p class="error-msg">회원가입 중 오류가 발생했습니다.</p>';
            }
        }
        ?>
    </div>
</body>
</html>

