<?php
class LineLogin
{
    private const CLIENT_ID = '2004103118';
    private const CLIENT_SECRET = '030b64009119fc18f8f3a94d194c4bcd';
    private const REDIRECT_URL = 'http://127.0.0.1/my-medic-project-EP2-line-session/callback.php';

    private const AUTH_URL = 'https://access.line.me/oauth2/v2.1/authorize';
    private const PROFILE_URL = 'https://api.line.me/v2/profile';
    private const TOKEN_URL = 'https://api.line.me/oauth2/v2.1/token';
    private const REVOKE_URL = 'https://api.line.me/oauth2/v2.1/revoke';
    private const VERIFYTOKEN_URL = 'https://api.line.me/oauth2/v2.1/verify';

    function getLink()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['state'] = hash('sha256', microtime(TRUE) . rand() . $_SERVER['REMOTE_ADDR']);

        $link = self::AUTH_URL . '?response_type=code&client_id=' . self::CLIENT_ID . '&redirect_uri=' . self::REDIRECT_URL . '&scope=profile%20openid%20email&state=' . $_SESSION['state'];
        return $link;
    }

    function token($code, $state)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SESSION['state'] != $state) {
            return false;
        }

        $header = ['Content-Type: application/x-www-form-urlencoded'];
        $data = [
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => self::REDIRECT_URL,
            "client_id" => self::CLIENT_ID,
            "client_secret" => self::CLIENT_SECRET
        ];

        $response = $this->sendCURL(self::TOKEN_URL, $header, 'POST', $data);
        $token_data = json_decode($response);

        $profile_data = $this->profile($token_data->access_token);

        $this->saveUserDataToMySQL($profile_data);

        return $token_data;
    }

    function profile($access_token)
    {
        $header = ['Authorization: Bearer ' . $access_token];
        $response = $this->sendCURL(self::PROFILE_URL, $header, 'GET');
        $profile_data = json_decode($response);

        if (isset($profile_data->email)) {
            return $profile_data;
        } else {
            $header = ['Authorization: Bearer ' . $access_token];
            $response = $this->sendCURL(self::PROFILE_URL . '/email', $header, 'GET');
            $email_data = json_decode($response);
            $profile_data->email = isset($email_data->email) ? $email_data->email : '';
            return $profile_data;
        }
    }

    function saveUserDataToMySQL($profile_data)
    {
        $line_user_id = $profile_data->userId;
        $display_name = $profile_data->displayName;
        $picture_url = $profile_data->pictureUrl;
        $email = isset($profile_data->email) ? $profile_data->email : '';
        $login_time = date('Y-m-d H:i:s'); // เวลาเข้าสู่ระบบ

        $connection = new mysqli('localhost', 'root', '', 'mdpj_user');

        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        // ตรวจสอบว่าผู้ใช้มีอยู่แล้วหรือไม่
        $sql = "SELECT id FROM users WHERE line_user_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $line_user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // อัพเดทข้อมูลผู้ใช้
            $sql = "UPDATE users SET display_name = ?, picture_url = ?, email = ?, login_time = ? WHERE line_user_id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sssss", $display_name, $picture_url, $email, $login_time, $line_user_id);
        } else {
            // เพิ่มผู้ใช้ใหม่
            $sql = "INSERT INTO users (line_user_id, display_name, picture_url, email, login_time) VALUES (?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sssss", $line_user_id, $display_name, $picture_url, $email, $login_time);
        }

        if ($stmt->execute()) {
            // Do nothing if successful
        } else {
            echo "Error: " . $sql . "<br>" . $connection->error;
        }

        $stmt->close();
        $connection->close();
    }

    private function sendCURL($url, $header, $type, $data = NULL)
    {
        $request = curl_init();

        if ($header != NULL) {
            curl_setopt($request, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);

        if (strtoupper($type) === 'POST') {
            curl_setopt($request, CURLOPT_POST, TRUE);
            curl_setopt($request, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        curl_setopt($request, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($request);

        curl_close($request);
    
        return $response;
    }
}
?>
