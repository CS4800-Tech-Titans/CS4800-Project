use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase {
    public function testSuccessfulLogin() {
        // Simulate a successful login attempt
        $_POST["username"] = "user";
        $_POST["password"] = "password";

        // Include login script
        include 'index.php';

        // Check if the user is redirected to the dashboard
        $this->assertStringContainsString('Location: dashboard.php', xdebug_get_headers());

        // Check if the session variable is set
        $this->assertEquals("user", $_SESSION["username"]);
    }

    public function testFailedLogin() {
        // Simulate a failed login attempt
        $_POST["username"] = "user";
        $_POST["password"] = "wrong_password";

        // Include login script
        include 'index.php';

        // Check if an error message is displayed
        $this->assertStringContainsString("Invalid username or password.", $error_message);
    }
}
