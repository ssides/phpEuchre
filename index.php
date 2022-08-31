require_once __DIR__ . '/vendor/autoload.php';

/**
 * Checks that the JWT assertion is valid (properly signed, for the
 * correct audience) and if so, returns strings for the requesting user's
 * email and a persistent user ID. If not valid, returns null for each field.
 *
 * @param string $assertion The JWT string to assert.
 * @param string $audience The audience of the JWT.
 *
 * @return string[] array containing [$email, $id]
 * @throws Exception on failed validation
 */
function validate_assertion(string $idToken, string $audience) : array
{
    $auth = new Google\Auth\AccessToken();
    $info = $auth->verify($idToken, [
      'certsLocation' => Google\Auth\AccessToken::IAP_CERT_URL,
      'throwException' => true,
    ]);

    if ($audience != $info['aud'] ?? '') {
        throw new Exception(sprintf(
            'Audience %s did not match expected %s', $info['aud'], $audience
        ));
    }

    return [$info['email'], $info['sub']];
}

/**
 * This is an example of a front controller for a flat file PHP site. Using a
 * static list provides security against URL injection by default.
 */
switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
    case '/':
        if (!Google\Auth\Credentials\GCECredentials::onGce()) {
            throw new Exception('You must deploy to appengine to run this sample');
        }
        $metadata = new Google\Cloud\Core\Compute\Metadata();
        $audience = sprintf(
            '/projects/%s/apps/%s',
            $metadata->getNumericProjectId(),
            $metadata->getProjectId()
        );
        $idToken = getallheaders()['X-Goog-Iap-Jwt-Assertion'] ?? '';
        try {
            list($email, $id) = validate_assertion($idToken, $audience);
            printf("<h1>Hello %s</h1>", $email);
        } catch (Exception $e) {
            printf('Failed to validate assertion: %s', $e->getMessage());
        }
        break;
    case '': break; // Nothing to do, we're running our tests
    default:
        http_response_code(404);
        exit('Not Found');
}