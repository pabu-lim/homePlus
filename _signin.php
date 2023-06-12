<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>로그인 페이지</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        .login-box {
            width: 300px;
            padding: 20px;
            background-color: #f7f7f7;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-box h2 {
            text-align: center;
        }

        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .login-box input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .signup-link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>로그인</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="text" name="userBusinessNumber" placeholder="아이디" required>
            <input type="password" name="passWd" placeholder="비밀번호" required>
            <input type="submit" name="login" value="로그인">
        </form>
        <div class="signup-link">
            <a href="https://7ca7-223-38-81-187.ngrok-free.app/_signup.php">회원가입</a>
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

        if (isset($_POST['login'])) {
            $userBusinessNumber = $_POST['userBusinessNumber'];
            $passWd = $_POST['passWd'];

            // 입력한 아이디와 비밀번호를 사용하여 데이터베이스에서 조회
            $query = "SELECT * FROM usertbl WHERE userBusinessNumber = '$userBusinessNumber' AND passWd = '$passWd'";
            $result = $conn->query($query);

            if ($result->num_rows == 1) {
                // 로그인 성공
                session_start();
                $_SESSION['userBusinessNumber'] = $userBusinessNumber;
                header("Location:https://7ca7-223-38-81-187.ngrok-free.app/_finding.php"); // 조회 페이지로 이동
                exit();
            } else {
                // 로그인 실패
                echo '<p class="error-msg">아이디 또는 비밀번호가 잘못되었습니다.</p>';
            }
        }
        ?>
    </div>
</body>
</html>
